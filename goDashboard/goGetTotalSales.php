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

    include_once ("goAPI.php");
 
	$campaigns 											= allowed_campaigns($log_group, $goDB, $astDB);
	$type												= (!isset($_REQUEST["type"])) ? "all-daily" : $astDB->escape($_REQUEST['type']);
	$NOW 												= date("Y-m-d");
	
	// ERROR CHECKING 
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {
            $astDB->where('user_group', $log_group);
            $allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
            
            if (strtoupper($log_group) !== 'ADMIN') {
                $allowed_campaigns = trim($allowed_camps['allowed_campaigns'], "-");
                if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
                    $campaigns = explode(" ", trim($allowed_campaigns));
                }

                //get inbound groups
                $getIngroups                            = $astDB->where('user_group', $log_group)
                    ->get('vicidial_inbound_groups', NULL, array('group_id'));

                $ingroups                               = array();
                foreach ($getIngroups as $fresults) {
                    $ingroups[]                         = $fresults['group_id'];
                }
            }

			if (is_array($campaigns)) {
				//$status									= array("SALE");
				$default_status = array("SALE");
				$camp_sql = $astDB->where("sale", "Y")
					->where("campaign_id", $campaigns, "IN")
					->get("vicidial_campaign_statuses",NULL, "status");
				$query_camp = $astDB->getLastQuery();

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

					$data 								= $astDB
						->join("vicidial_list vl", "vlog.lead_id = vl.lead_id", "LEFT")
						->where("vlog.status", $status, "IN")
						->where("vlog.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")
						->where("vlog.campaign_id", $campaigns, "IN")
						->getValue("vicidial_log as vlog", "count(*)");
						
					break;
					
					case "out-hourly":

					$data = $astDB
						->join("vicidial_list vl", "vlog.lead_id = vl.lead_id", "LEFT")
						->where("vlog.status", $status, "IN")
						->where("vlog.call_date", array("$datehourly:00:00", "$datehourly:59:59"), "BETWEEN")
						->where("vlog.campaign_id", $campaigns, "IN")
						->getValue("vicidial_log as vlog", "count(*)");
					break;
					
					case "in-daily":
					
                    if (strtoupper($log_group) !== 'ADMIN') {
                        $astDB->where("vcl.campaign_id", $ingroups, "IN");
                    }
					$data 								= $astDB
						->join("vicidial_list vl", "vcl.lead_id = vl.lead_id", "LEFT")
						->where("vcl.status", $status, "IN")
						->where("vcl.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")
						// ->where("vcl.campaign_id", $ingroups, "IN")
						->getValue("vicidial_closer_log vcl", "count(*)");
					break;
					
					case "in-hourly":
					
                    if (strtoupper($log_group) !== 'ADMIN') {
                        $astDB->where("vcl.campaign_id", $ingroups, "IN");
                    }
					$data 								= $astDB
						->join("vicidial_list vl", "vcl.lead_id = vl.lead_id", "LEFT")
						->where("vcl.status", $status, "IN")
						->where("vcl.call_date", array("$datehourly:00:00", "$datehourly:59:59"), "BETWEEN")
						// ->where("vcl.campaign_id", $ingroups, "IN")
						->getValue("vicidial_closer_log  vcl", "count(*)");
					break;			
					
					case "all-daily":
					$outsales = $astDB
						->join("vicidial_list vl", "vlog.lead_id = vl.lead_id", "LEFT")
						->where("vlog.status", $status, "IN")
						->where("vlog.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")
						->where("vlog.campaign_id", $campaigns, "IN")
						->getValue("vicidial_log as vlog", "count(*)");
				
                    if (strtoupper($log_group) !== 'ADMIN') {
                        $astDB->where("vcl.campaign_id", $ingroups, "IN");
                    }
					$insales = $astDB
						->join("vicidial_list vl", "vcl.lead_id = vl.lead_id", "LEFT")
						->where("vcl.status", $status, "IN")
						->where("vcl.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")
						// ->where("vcl.campaign_id", $ingroups, "IN")
						->getValue("vicidial_closer_log  vcl", "count(*)");
                        $test = $astDB->getLastQuery();
					
					$data = $insales + $outsales;
					break;
				}
						
				$apiresults = array(
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
				); 
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}

?>
