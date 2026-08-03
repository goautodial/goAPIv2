<?php
/**
 * @file        goGetStatisticalReports.php
 * @brief       API for Campaign Statistics
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Alexander Jim Abenoja
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it AND/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

    include_once(__DIR__ . "/goAPI.php");

/** @var MySQLiDB $astDB */
/** @var MySQLiDB $goDB */
/** @var MySQLiDB $kamDB */
/** @var string $goUser */
/** @var string $goPass */
/** @var string $goAction */
/** @var string $goURL */
/** @var string $userResponseType */
/** @var string $session_user */
/** @var string $log_user */
/** @var string|false $log_group */
/** @var string $log_ip */
/** @var string $VARDB_user */
/** @var string $VARDB_pass */
/** @var string $VARDB_database */


    // need function go_sec_convert();
    $fromDate 										= $astDB->escape(($_REQUEST['fromDate'] ?? ''));
    $toDate 										= $astDB->escape(($_REQUEST['toDate'] ?? ''));
    $campaignID										= $astDB->escape(($_REQUEST['campaignID'] ?? ''));
    $request 										= $astDB->escape(($_REQUEST['request'] ?? ''));
    $statewide_sales_report							= $astDB->escape(($_REQUEST['statewide_sales_report'] ?? ''));
    $array_camp = [];
    $array_list = [];
    $array_list_amount = [];
    $ul = '';
    $col_exists = 0;
    $outbound_sql_sales = [];
    $inbound_sql_sales = [];
    //dispo_stats 									= $astDB->escape(($_REQUEST['statuses'] ?? ''));

    if (empty($fromDate)) {
    	$fromDate									= date("Y-m-d")." 00:00:00";
    }

    if (empty($toDate)) {
    	$toDate 									= date("Y-m-d")." 23:59:59";
    }

	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= [
			"result" 									=> "Error: Session User Not Defined."
		];
	} elseif ( empty($campaignID) || is_null($campaignID) ) {
		$err_msg 									= error_handle("40001");
        	$apiresults 							= [
			"code" 										=> "40001",
			"result" 									=> $err_msg
		];
	} elseif (empty($fromDate) && empty($toDate)) {
		$fromDate 									= date("Y-m-d") . " 00:00:00";
		$toDate 									= date("Y-m-d") . " 23:59:59";
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");

		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= is_array($fresults) ? ($fresults["user_level"] ?? 0) : 0;

		// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
		// every time we need to filter out requests
		$tenant										= (checkIfTenant($log_group, $goDB)) ? 1 : 0;

		if ($tenant) {
			$astDB->where("user_group", $log_group);
		} else {
			if (strtoupper((string) $log_group) !== 'ADMIN') {
				if ($userlevel < 8) {
					$astDB->where("user_group", $log_group);
				}
			}
		}

		// check if MariaDB slave server available
		$rslt										= $goDB
			->where('setting', 'slave_db_ip')
			->where('context', 'creamy')
			->getOne('settings', 'value');
		$slaveDBip 									= is_array($rslt) ? ($rslt['value'] ?? '') : '';

		if (!empty($slaveDBip)) {
			$astDB 									= new MySQLiDB($slaveDBip, $VARDB_user, $VARDB_pass, $VARDB_database);

			if (!$astDB) {
				echo "Error: Unable to connect to MariaDB slave server." . PHP_EOL;
				echo "Debugging Error: " . $astDB->getLastError() . PHP_EOL;
				exit;
				//die('MySQL connect ERROR: ' . mysqli_error('mysqli'));
			}
		}

		// SALES PER AGENT
		if ($log_group !== "ADMIN") {
			if ($userlevel < 9) {
				$ul 									= "AND us.user_group = '$log_group'";
			}
		} else {
			$ul 									= "";
		}

		$Qstatus = $astDB
			->where("sale", "Y")
			->get("vicidial_statuses", NULL, "status");

		$sstatusRX 									= "";
		$sstatuses 									= [];
		$a 											= 0;

		if ($astDB->count > 0) {
			foreach ($Qstatus as $rowQS) {
				$goTempStatVal 						= $rowQS['status'];
				$sstatuses[$a] 						= $rowQS['status'];
				$sstatusRX 							.= "{$goTempStatVal}|";
				$a++;
			}
		}

		//if (!empty($sstatuses))
		$sstatuses 									= implode("','",$sstatuses);

		//ALL CAMPAIGNS
		if ("ALL" === strtoupper((string) $campaignID)) {
			$SELECTQuery 							= $astDB->get("vicidial_campaigns", NULL, "campaign_id");

			foreach($SELECTQuery as $camp_val){
				$array_camp[] 						= $camp_val["campaign_id"];
			}

		// Statewide Customization
		$SELECTQueryList 							= $astDB->get("vicidial_lists", null, "list_id");
		$array_list 								= $SELECTQueryList;
		// ./Statewide Customization

		} else {
			$array_camp[] 							= $campaignID;

			// Statewide Customization
			$i = 0;
			foreach($array_camp as $camp) {
				$camp_id 							= $array_camp[$i];
				$astDB->where("campaign_id", $camp_id);
				$SELECTQuery 						= $astDB->get("vicidial_lists", null, "list_id");
				//$query_list = mysqli_query($astDB,"SELECT list_id FROM vicidial_lists WHERE campaign_id = '$camp_id';");
				$array_list 						= $SELECTQuery;
				$i++;
			}
			// ./Statewide Customization

		}

		// Statewide Customization
		foreach($array_list as $list){
			$list_id = is_array($list) ? ($list['list_id'] ?? '') : $list;
			$list_id = preg_replace('/[^0-9]/', '', (string) $list_id);
			if ($list_id === '') {
				continue;
			}
			$custom_list_id = "custom_" . $list_id;
			$tableCheck = $astDB->rawQuery("SHOW TABLES LIKE '{$custom_list_id}'");
			if (!is_array($tableCheck) || count($tableCheck) < 1) {
				continue;
			}
			$query_CF_list = $astDB->rawQuery("DESC {$custom_list_id};");
			if ($query_CF_list) {
				foreach ($query_CF_list as $field_list) {
					$exec_query_CF_list = $field_list["Field"];

					if ($exec_query_CF_list == "Amount") {
						$array_list_amount[] = $custom_list_id;
					}
				}
			}
		}
		// ./Statewide Customization

		$imploded_camp = "'".implode("','", $array_camp)."'";

		$Qstatus2 = $astDB
			->where("sale", "Y")
			->where("campaign_id", $array_camp, "IN")
			->get("vicidial_campaign_statuses", NULL, "status");

		$cstatusRX = "";
		$cstatuses = [];
		$b = 0;

		if ($astDB->count > 0) {
			foreach ($Qstatus2 as $rowQS2) {
				$goTempStatVal = $rowQS2['status'];
				$cstatuses[$b] = $rowQS2['status'];
				$cstatusRX .= "{$goTempStatVal}|";
				$b++;
			}
		}

		//if (!empty($cstatuses)) {
			$cstatuses = implode("','",$cstatuses);
		//}

		if ($sstatuses !== '' && $cstatuses !== '') {
			$statuses = "{$sstatuses}','{$cstatuses}";
			$statusRX = "{$sstatusRX}{$cstatusRX}";
		} else {
			$statuses = ($sstatuses !== '' && $cstatuses === '') ? $sstatuses : $cstatuses;
			$statusRX = ($sstatusRX !== '' && $cstatusRX === '') ? $sstatusRX : $cstatusRX;
		}

		$statusRX = trim($statusRX, "|");

		$TOPsorted_output                               = "";
		$BOTsorted_output                               = "";
		$total_in_sales                                 = 0;
		$total_out_sales                                = 0;
		$total_out_sales_amount							= 0;
		$totagents										= 0;
		$total_in_sales_amount 							= 0;

		if (strtolower((string) $request) === "outbound") {
			// Outbound Sales //
			$outbound_query = "
				SELECT us.full_name AS full_name, us.user AS user,
				SUM(if (vlog.status REGEXP '^(".$statusRX.")$', 1, 0)) AS sale
				FROM vicidial_users as us, vicidial_log as vlog, vicidial_list as vl
				WHERE us.user = vlog.user AND vl.phone_number = vlog.phone_number
				AND vl.lead_id = vlog.lead_id
				#AND vlog.length_in_sec > '0'
				AND vlog.status in ('$statuses') AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '".$fromDate."' AND '".$toDate."'
				AND vlog.campaign_id IN ($imploded_camp) $ul
				GROUP BY us.full_name;
			";
			$outbound_sql = $astDB->rawQuery($outbound_query);

			$totagents = $astDB->count;

			// Statewide Customization
			$outbound_select_query_sales = "";
			$i = 0;
			foreach($array_list_amount as $list_amount){
				if($i > 0) {
					$outbound_select_query_sales .= " UNION ";
				}

				$outbound_select_query_sales = "
					SELECT vlog.list_id, vlog.user, vlog.status, sum(IFNULL(cf.Amount, 0)) AS Amount
					FROM vicidial_log vlog
					LEFT JOIN $list_amount cf on vlog.lead_id = cf.lead_id
					WHERE vlog.lead_id=cf.lead_id
					AND vlog.status IN ('$statuses')
					AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'
					AND vlog.campaign_id IN ($imploded_camp) $ul
					GROUP BY vlog.user
					";
				$i++;
			}

			if($outbound_select_query_sales !== ''){
				$col_exists = 1;
			} else {
				$col_exists = 0;
			}

			if($col_exists){
			$outbound_query_sales = "SELECT t.user, t.status, sum(IFNULL(t.amount, 0)) AS amount
				FROM ( $outbound_select_query_sales ) t
				GROUP BY t.user";

			$outbound_sql_sales = $astDB->rawQuery($outbound_query_sales);
			}
			// ./Statewide Customization

			if ($totagents > 0) {
				$total_sales				= 0;

				foreach($outbound_sql as $row){
				//while ($row = $astDB->rawQuery($outbound_query)) {
					$amount_row = 0;
					// Statewide Customization
					if($col_exists){
					    foreach($outbound_sql_sales as $row_sales){
						if($row_sales['user'] == $row['user']){
							$amount_row = (float) ($row_sales['amount'] ?? 0);
						}
					    }

					    if(empty($amount_row)){
						$amount_row = 0;
					    }

					    $display_amount = "<td nowrap>".$amount_row."</td>";
					    $total_out_sales_amount += (float) $amount_row;
					} else {
					    $total_out_sales_amount = 0;
					    $display_amount = "";
					}
					// ./Statewide Customization

					$TOPsorted_output	 	.= "<tr>";
					$TOPsorted_output 		.= "<td nowrap>".$row['full_name']."</td>";
					$TOPsorted_output 		.= "<td nowrap>".$row['user']."</td>";
					$sale = (int) ($row['sale'] ?? 0);
					$TOPsorted_output		.= "<td nowrap>".$sale."</td>";
					if($statewide_sales_report === 'y'){
						$TOPsorted_output               .= $display_amount;
					}
					$TOPsorted_output 		.= "</tr>";
					$total_out_sales += $sale;
				}
			}
		}
		if (strtolower((string) $request) === "inbound") {
			//GET ALL CLOSER CAMAPIGNS
			$closer_camps = go_getall_closer_campaigns("ALL", $astDB);

			$campaign_inb_query = "vlog.campaign_id IN ($closer_camps)";

			$query = "
				SELECT us.full_name AS full_name, us.user AS user,
				SUM(if (vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale
				FROM vicidial_users as us, vicidial_closer_log as vlog, vicidial_list as vl
				WHERE us.user = vlog.user AND vl.phone_number = vlog.phone_number
				AND vl.lead_id = vlog.lead_id
				#AND vlog.length_in_sec > '0'
				AND vlog.status in ('$statuses') AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'
				AND $campaign_inb_query $ul
				GROUP BY us.full_name
				";

			// Statewide Customization
			$inbound_select_query_sales = "";
			$i = 0;
			foreach($array_list_amount as $list_amount){
					if($i > 0) {
							$inbound_select_query_sales .= " UNION ";
					}

					$inbound_select_query_sales .= "
							SELECT vlog.list_id, vlog.user, vlog.status, sum(IFNULL(cf.Amount, 0)) AS amount
							FROM vicidial_closer_log vlog
							LEFT JOIN $list_amount cf on vlog.lead_id = cf.lead_id
							WHERE vlog.lead_id=cf.lead_id
							AND vlog.status IN ('$statuses')
							AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'
							AND $campaign_inb_query $ul
							GROUP BY vlog.user
							";
					$i++;
			}

			if($inbound_select_query_sales !== ''){
				$col_exists = 1;
			} else {
				$col_exists = 0;
			}

			if($col_exists){
                        $inbound_query_sales = "SELECT t.user, t.status, sum(IFNULL(t.amount, 0)) AS amount
                                FROM ( $inbound_select_query_sales ) t
				GROUP BY t.user";

                        $inbound_sql_sales = $astDB->rawQuery($inbound_query_sales);
			}
                        // ./Statewide Customization

			if ($query) {
				$total_sales				= 0;

				foreach($astDB->rawQuery($query) as $row) {
				//while ($row = $astDB->rawQuery($inbound_query)) {

					// Statewide Customization
					$amount_row = 0;
					if($col_exists){
	                                            foreach($inbound_sql_sales as $row_sales){
                                                if($row_sales['user'] == $row['user']){
                                                        $amount_row = (float) ($row_sales['amount'] ?? 0);
                                                }
                                            }

                                            if(empty($amount_row)){
                                                $amount_row = 0;
                                            }
                                            $total_in_sales_amount += (float) $amount_row;
					    $display_amount = "<td nowrap> ".$amount_row." </td>";
					} else {
						$total_in_sales_amount = 0;
						$display_amount = "";
					}
                                        // ./Statewide Customization

					$BOTsorted_output 		.= "<tr>";
					$BOTsorted_output 		.= "<td nowrap> ".$row['full_name']." </td>";
					$BOTsorted_output 		.= "<td nowrap> ".$row['user']." </td>";
					$sale = (int) ($row['sale'] ?? 0);
					$BOTsorted_output 		.= "<td nowrap> ".$sale." </td>";
					if($statewide_sales_report === 'y'){
					$BOTsorted_output               .= $display_amount;
					}
					$BOTsorted_output 		.= "</tr>";
					$total_in_sales += $sale;
				}
			}
		}

		$apiresults = [
			"result"			=> "success",
			"TOPsorted_output"	=> $TOPsorted_output,
			"BOTsorted_output"	=> $BOTsorted_output,
			"TOToutbound" 		=> $total_out_sales,
			"TOTinbound" 		=> $total_in_sales,
			"TOTAgents"			=> $totagents,
			"TOTOUTamount"		=> $total_out_sales_amount,
			"TOTINamount"		=> $total_in_sales_amount,
			"col_exists"		=> $col_exists,
			"testVar"			=> " "
		];
	}

?>
