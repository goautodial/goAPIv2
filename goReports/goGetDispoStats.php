<?php
/**
 * @file        goGetDispoStats.php
 * @brief       API reports for disposition statuses
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author      Alexander Jim Abenoja 
 * @author		Demian Lizandro A. Biscocho
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
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
    include_once("goAPI.php");
	
    $fromDate 										= $astDB->escape($_REQUEST['fromDate']);
    $toDate 										= $astDB->escape($_REQUEST['toDate']);
    $campaignID 									= $astDB->escape($_REQUEST['campaignID']);
	
    if (empty($fromDate)) {
    	$fromDate 									= date("Y-m-d")." 00:00:00";
    }
    
    if (empty($toDate)) {
    	$toDate 									= date("Y-m-d")." 23:59:59";
    }
		
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif ( empty($campaignID) || is_null($campaignID) ) {
		$err_msg 									= error_handle("40001");
        	$apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
	} elseif (empty($fromDate) && empty($toDate)) {
		$fromDate 									= date("Y-m-d") . " 00:00:00";
		$toDate 									= date("Y-m-d") . " 23:59:59";
		//die($fromDate." - ".$toDate);									=> $err_msg
	} else {
		$fresults 									= $astDB
			->where("user", $goUser)
			->getOne("vicidial_users", "user,user_level,user_group");
		
		$goapiaccess 								= $astDB->getRowCount();
		$userlevel 									= $fresults["user_level"];
		$usergroup 									= $fresults["user_group"];
        
		// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
		// every time we need to filter out requests
		//$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
        $tenant                                     = ($userlevel < 9 && $usergroup !== "ADMIN") ? 1 : 0;
			
		// check if MariaDB slave server available
		$rslt										= $goDB
			->where('setting', 'slave_db_ip')
			->where('context', 'creamy')
			->getOne('settings', 'value');
		$slaveDBip 									= $rslt['value'];
		
		if (!empty($slaveDBip)) {
			$astDB 									= new MySQLiDB($slaveDBip, $VARDB_user, $VARDB_pass, $VARDB_database);

			if (!$astDB) {
				echo "Error: Unable to connect to MariaDB slave server." . PHP_EOL;
				echo "Debugging Error: " . $astDB->getLastError() . PHP_EOL;
				exit;
				//die('MySQL connect ERROR: ' . mysqli_error('mysqli'));
			}			
		}
		
		if ($tenant) {
			$astDB->where("user_group", $usergroup);
		} else {
			if (strtoupper($usergroup) != 'ADMIN') {
				if ($user_level < 9) {
					$astDB->where("user_group", $usergroup);
				}
			}
		}
		
		//ALL CAMPAIGNS
		if ("ALL" === strtoupper($campaignID)) {
			$SELECTQuery 							= $astDB->get("vicidial_campaigns", NULL, "campaign_id");

			foreach($SELECTQuery as $camp_val){
				$array_camp[] 						= $camp_val["campaign_id"];
			}
		} else {
			$array_camp[] 							= $campaignID;
		}

		//$imploded_camp = "'".implode("','", $array_camp)."'";

		$total_all 									= ($campaignID == "ALL") ? "ALL List IDs under ALL Campaigns" : "ALL List IDs under $campaignID";

		//if (isset($list_ids) && $list_ids[0] == "ALL") {
			/*$query 							= "
				SELECT list_id FROM vicidial_lists 
				WHERE campaign_id = '$campaignID' 
				ORDER BY list_id
			";*/

		$qlistid 									= $astDB
			->where("campaign_id", $array_camp, "IN")
			->orderBy("list_id")
			->get("vicidial_lists", NULL, "list_id");
		
        $list_ids = array();
		if ($astDB->count > 0) {
			foreach ($qlistid as $row) {
				$list_ids[]							= $row['list_id'];
			}
		}
		//}
        if ($tenant) {
            $list = (count($list_ids) > 0) ? "'".implode("','",$list_ids)."'" : "'-1'";
        } else {
            $list = "'".implode("','",$list_ids)."'";
        }
		
		$qsstatuses 								= $astDB
			->orderBy("status")
			->get("vicidial_statuses", NULL, array("status", "status_name"));
		
		if ($astDB->count > 0) {
			foreach ($qsstatuses as $row) {
				$statuses_list[$row['status']] 		= $row['status_name'];
			}
		}
		
		$qcstatuses = $astDB
			->where("campaign_id", $array_camp, "IN")
			->get("vicidial_campaign_statuses", NULL, array("status", "status_name"));
		
		if ($astDB->count > 0) {
			foreach ($qcstatuses as $row) {
				$statuses_list[$row['status']] 		= $row['status_name'];
			}
		}
		
		$leads_in_list 								= 0;
		$leads_in_list_N 							= 0;
		$leads_in_list_Y 							= 0;
		
		/*	
		$cols = array(
			"status", 
			"if (called_count >= 10, 10, called_count) as called_count", 
			"count(*) as count"
		);
		$queryx	= $astDB
			->where("list_id", $list_ids, "IN")
			->where("status", array("DC", "DNCC", "XDROP"), "NOT IN")
			->groupBy("status, if (called_count >= 10, 10, called_count)")
			->orderBy("status, called_count")
			->get("vicidial_list", NULL, $cols);
		var_dump($astDB->getLastError());
		*/	
		$query = "SELECT status, if (called_count >= 10, 10, called_count) as called_count, count(*) as count FROM vicidial_list 
				WHERE list_id IN(".$list.") AND status NOT IN('DC','DNCC','XDROP') 
				GROUP BY status, if (called_count >= 10, 10, called_count) 
				ORDER BY status,called_count
			";
		$queryx	= $astDB->rawQuery($query);	
		$sts						= 0;
		$first_row					= 1;
		$all_called_first				= 1000;
		$all_called_last				= 0;
		//$all_called_count 				= 0;				
		$o						= 0;
		
		if ($astDB->count >0) {
			foreach ($queryx as $row) {
				$leads_in_list 			= ($leads_in_list + $row['count']);
				$count_statuses[$o]		= $row['status'];
				$count_called[$o]		= $row['called_count'];
				$count_count[$o]		= $row['count'];
				
				$all_called_count[$row['called_count']] = ($all_called_count[$row['called_count']] + $row['count']);					
				
				if ( (strlen($status[$sts]) < 1) or ($status[$sts] != $row['status']) ) {
					if ($first_row) {
						$first_row = 0;
					} else{
						$sts++;
					}
					
					$status[$sts] = $row['status'];
					$status_called_first[$sts] 	= $row['called_count'];
					
					if ($status_called_first[$sts] < $all_called_first) {
						$all_called_first 	= $status_called_first[$sts];
					}
				}
				
				$leads_in_sts[$sts] 			= ($leads_in_sts[$sts] + $row['count']);
				$status_called_last[$sts] 		= $row['called_count'];
				
				if ($status_called_last[$sts] > $all_called_last) {
					$all_called_last 		= $status_called_last[$sts];
				}
				$o++;			
			}
		}
		//var_dump($queryx);
		$TOPsorted_output = "<center>\n";
		$TOPsorted_output .= "<TABLE class='table table-striped table-bordered table-hover' id='dispo'>\n";
		$TOPsorted_output .= "
			<thead>
			<tr>
			<th>STATUS</th>
			<th>Status Name</th>
		";
			
		$first = $all_called_first;
		
		while ($first <= $all_called_last) {
			if ($first >= 10) {
				$Fplus						="+";
			} else {
				$Fplus						= "";
			}
			
			$TOPsorted_output 				.= "<th> $first$Fplus </th>";
			$first++;
		}
		
		$TOPsorted_output 					.= "
			<th nowrap> SUB-TOTAL </th>
				</tr></thead><tbody>\n
			";

		$sts						= 0;
		$statuses_called_to_print 			= count($status);
		$statuses_count_called_to_print			= count($count_count);

		while ($statuses_called_to_print > $sts) {
			$Pstatus = $status[$sts];					
			$TOPsorted_output .= "
				<tr>
					<td nowrap> ".$Pstatus." </td>
					<td nowrap> ".$statuses_list[$Pstatus]." </td>
				";

			$first = $all_called_first;
			
			while ($first <= $all_called_last) {							
				$called_printed	= 0;
				$o = 0;
				
				while ($statuses_count_called_to_print > $o) {
					if ( ($count_statuses[$o] == "$Pstatus") AND ($count_called[$o] == "$first") ) {
						$called_printed++;
						$TOPsorted_output .= "<td nowrap> ".$count_count[$o]." </td>";
					}

					$o++;
				}
				
				if (!$called_printed) {
					$TOPsorted_output .= "<td nowrap> 0 </td>";
				}
				
				$first++;
			}
			
			$TOPsorted_output .= "<td nowrap> ".$leads_in_sts[$sts]." </td></tr>\n\n";
			$sts++;
		}

		$TOPsorted_output .= "
			</tbody>
				<tfoot><tr class='warning'>
				<th nowrap colspan='2'> Total For <i>".$total_all."</i> </th>
			";
			
		$first = $all_called_first;
		
		while ($first <= $all_called_last) {
			//if ($all_called_count[$first] == $first) {
				$all_called_count_data = $all_called_count[$first];
				$TOPsorted_output 			.= "
					<th> $all_called_count_data </th>
				";
			//} else {
			//	$TOPsorted_output 			.= "
			//		<th> 0 </th>
			//	";
			//}
			
			$first++;
		}
		
		$TOPsorted_output .= "<th>$leads_in_list</th></tr>\n";				
		$TOPsorted_output .= "</tfoot></table>";
	
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------	
			
		$queryforBot = "
			SELECT DISTINCT gmt_offset_now FROM vicidial_list 
			WHERE list_id IN (".$list.");
		";
		
		$numBot = $astDB->rawQuery($queryforBot);
		
		$BOTsorted_output 					= "<TABLE class='table table-striped table-bordered table-hover' id='dispo_bot'>";
		$BOTsorted_output 					.= "
			<tr><thead>
				<th>Timezone</th>
				<th>Called</th>
				<th>Not Called</th>	
				<thead></tr><tbody>
			";
			
		if ($astDB->count > 0) {
			foreach ($numBot as $rowBot) {
				
				$timezone_now 				= $rowBot['gmt_offset_now'];
				$CALLEDsql 					= "
					SELECT count(gmt_offset_now) as Clead_count FROM vicidial_list 
					WHERE list_id IN (".$list.") 
					AND status != 'NEW' 
					AND status NOT IN ('DC','DNCC','XDROP')
					AND gmt_offset_now = '".$timezone_now."'
				";
				// something in this loop doesn't work, comment mo one by one	
				$queryCALLED 				= $astDB->rawQuery($CALLEDsql);
				foreach($queryCALLED as $rowC)
				$called_leadCount 			= $rowC['Clead_count'];
				
				$NOTCALLEDsql 				= "
					SELECT count(gmt_offset_now) as NClead_count FROM vicidial_list 
					WHERE list_id IN (".$list.") 
					AND status = 'NEW'
					AND status NOT IN ('DC','DNCC','XDROP')
					AND gmt_offset_now = '$timezone_now'
				";
				
				$queryNOTCALLED 			= $NOTCALLEDsql;
				$fetchCalled 				= $astDB->rawQuery($queryNOTCALLED);
				foreach($fetchCalled as $rowNC)
				$notcalled_leadCount 		= $rowNC['NClead_count'];

				$BOTsorted_output 			.= "<tr>";
				$BOTsorted_output 			.= "<td>".$timezone_now."</td><td>".$called_leadCount."</td><td>".$notcalled_leadCount."</td>";
				$BOTsorted_output 			.= "</tr>";
				
			}
		} else {
			$BOTsorted_output 				.= "<tr><td colspan='3'><center>No available Leads</center></td></tr>";
		}	
		
		$BOTsorted_output 					.= "</tbody></TABLE>";

		
		$apiresults 						= array(
			"result" 							=> "success", 
			"SUMstatuses" 						=> $sts, 
			"TOPsorted_output" 					=> $TOPsorted_output, 
			"BOTsorted_output" 					=> $BOTsorted_output,
			"query"							=> $NOTCALLEDsql
		);
		return $apiresults;
	}

?>
