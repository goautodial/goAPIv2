<?php
 /**
 * @file 		goGetTotalSales.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Warren Ipac Briones  <warren@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
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

    include_once (__DIR__ . "/goAPI.php");

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


	$campaigns 											= array_values(array_filter((array) allowed_campaigns($log_group, $goDB, $astDB), static fn($campaign) => (string) $campaign !== ''));
	$type												= (isset($_REQUEST["type"])) ? $astDB->escape($_REQUEST['type']) : "all-daily";
	$NOW 												= date("Y-m-d");

	// ERROR CHECKING
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= [
			"result" 										=> "Error: goAPI User Not Defined."
		];
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= [
			"result" 										=> "Error: goAPI Password Not Defined."
		];
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= [
			"result" 										=> "Error: Session User Not Defined."
		];
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");

		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= (int) ($fresults["user_level"] ?? 0);

		if ($goapiaccess > 0 && $userlevel > 7) {
            $astDB->where('user_group', $log_group);
            $allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');

            if (strtoupper((string) $log_group) !== 'ADMIN') {
                					$allowed_campaigns = trim((string) ($allowed_camps['allowed_campaigns'] ?? ''), "-");
                					if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
                						$campaigns = array_values(array_filter(explode(" ", trim($allowed_campaigns)), static fn($campaign) => (string) $campaign !== ''));
                					}

                //get inbound groups
                $getIngroups                            = $astDB->where('user_group', $log_group)
                    ->get('vicidial_inbound_groups', NULL, ['group_id']);

                $ingroups                               = [];
                foreach ($getIngroups as $fresults) {
                    $ingroups[]                         = $fresults['group_id'];
                }
            }

			if (is_array($campaigns)) {
				//$status									= array("SALE");
				$default_status = ["SALE"];
				$camp_status = [];
				$camp_sql = [];
				if ($campaigns !== []) {
					$camp_sql = $astDB->where("sale", "Y")
						->where("campaign_id", $campaigns, "IN")
						->get("vicidial_campaign_statuses",NULL, "status");
					$query_camp = $astDB->getLastQuery();
				}

				foreach($camp_sql as $data){$camp_status[] = $data['status'];}

				if(!empty($camp_sql)){
					$status = array_merge($default_status, $camp_status);
				} else {
					$status = $default_status;
				}

				$datetoday = date("Y-m-d");
				$datehourly = date('Y-m-d H');
				//$datestartday							= date("Y-m-d") . " 00:00:00";
				//$dateendday							= date("Y-m-d") . " 23:59:59";
				$alex = 1;
				switch ($type) {
					case "out-daily":

					if (strtoupper((string) $log_group) !== 'ADMIN' && $campaigns === []) {
						$data = 0;
						break;
					}
					$data 								= $astDB
						->join("vicidial_list vl", "vlog.lead_id = vl.lead_id", "LEFT")
						->where("vlog.status", $status, "IN")
						->where("vlog.call_date", ["$datetoday 00:00:00", "$datetoday 23:59:59"], "BETWEEN")
						->where("vlog.campaign_id", $campaigns, "IN")
						->getValue("vicidial_log as vlog", "count(*)");

					break;

					case "out-hourly":

					if (strtoupper((string) $log_group) !== 'ADMIN' && $campaigns === []) {
						$data = 0;
						break;
					}
					$data = $astDB
						->join("vicidial_list vl", "vlog.lead_id = vl.lead_id", "LEFT")
						->where("vlog.status", $status, "IN")
						->where("vlog.call_date", ["$datehourly:00:00", "$datehourly:59:59"], "BETWEEN")
						->where("vlog.campaign_id", $campaigns, "IN")
						->getValue("vicidial_log as vlog", "count(*)");
					break;

					case "in-daily":

	                    if (strtoupper((string) $log_group) !== 'ADMIN') {
	                        if ($ingroups === []) {
	                            $data = 0;
	                            break;
	                        }
	                        $astDB->where("vcl.campaign_id", $ingroups, "IN");
	                    }
						$data 								= $astDB
						->join("vicidial_list vl", "vcl.lead_id = vl.lead_id", "LEFT")
						->where("vcl.status", $status, "IN")
						->where("vcl.call_date", ["$datetoday 00:00:00", "$datetoday 23:59:59"], "BETWEEN")
						// ->where("vcl.campaign_id", $ingroups, "IN")
						->getValue("vicidial_closer_log vcl", "count(*)");
					break;

					case "in-hourly":

	                    if (strtoupper((string) $log_group) !== 'ADMIN') {
	                        if ($ingroups === []) {
	                            $data = 0;
	                            break;
	                        }
	                        $astDB->where("vcl.campaign_id", $ingroups, "IN");
	                    }
						$data 								= $astDB
						->join("vicidial_list vl", "vcl.lead_id = vl.lead_id", "LEFT")
						->where("vcl.status", $status, "IN")
						->where("vcl.call_date", ["$datehourly:00:00", "$datehourly:59:59"], "BETWEEN")
						// ->where("vcl.campaign_id", $ingroups, "IN")
						->getValue("vicidial_closer_log  vcl", "count(*)");
					break;

					case "all-daily":
					if (strtoupper((string) $log_group) !== 'ADMIN' && $campaigns === []) {
						$outsales = 0;
					} else {
						$outsales = $astDB
							->join("vicidial_list vl", "vlog.lead_id = vl.lead_id", "LEFT")
							->where("vlog.status", $status, "IN")
							->where("vlog.call_date", ["$datetoday 00:00:00", "$datetoday 23:59:59"], "BETWEEN")
							->where("vlog.campaign_id", $campaigns, "IN")
							->getValue("vicidial_log as vlog", "count(*)");
					}

	                    if (strtoupper((string) $log_group) !== 'ADMIN') {
	                        if ($ingroups === []) {
	                            $insales = 0;
	                        } else {
	                            $astDB->where("vcl.campaign_id", $ingroups, "IN");
	                        }
	                    }
						if (!isset($insales)) {
							$insales = $astDB
								->join("vicidial_list vl", "vcl.lead_id = vl.lead_id", "LEFT")
								->where("vcl.status", $status, "IN")
								->where("vcl.call_date", ["$datetoday 00:00:00", "$datetoday 23:59:59"], "BETWEEN")
								// ->where("vcl.campaign_id", $ingroups, "IN")
								->getValue("vicidial_closer_log  vcl", "count(*)");
	                        $test = $astDB->getLastQuery();
						}

					$data = $insales + $outsales;
					break;
				}

				$apiresults = [
					"result" => "success",
					//"query"	=> $astDB->getLastQuery(),
					"data" => $data,
					//"status" => $status,
					//"camp_status" => $camp_status,
					//"camp_sql" => $camp_sql,
					//"query_camp" => $query_camp,
					//"type" => $type,
					//"alex" => $alex
					//"camp" => "'".implode("','",$campaigns)."'"
				];
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= [
				"code" 										=> "10001",
				"result" 									=> $err_msg
			];
		}
	}

?>
