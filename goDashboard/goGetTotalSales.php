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
			if (is_array($campaigns)) {
				$status									= "SALE";
				$datetoday								= date("Y-m-d");
				$datehourly	 							= date('Y-m-d H');
				//$datestartday							= date("Y-m-d") . " 00:00:00";
				//$dateendday							= date("Y-m-d") . " 23:59:59";
				
				switch ($type) {
					case "out-daily":

					$data 								= $astDB
						->join("vicidial_list vl", "vlog.lead_id = vl.lead_id", "LEFT")
						->where("vlog.status", $status)
						->where("vlog.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")
						->where("vlog.campaign_id", $campaigns, "IN")
						->getValue("vicidial_log as vlog", "count(*)");
						
					break;
					
					case "out-hourly":

					$data 								= $astDB
						->join("vicidial_list vl", "vlog.lead_id = vl.lead_id", "LEFT")
						->where("vlog.status", $status)
						->where("vlog.call_date", array("$datehourly:00:00", "$datehourly:59:59"), "BETWEEN")
						->where("vlog.campaign_id", $campaigns, "IN")
						->getValue("vicidial_log as vlog", "count(*)");
						
					break;
					
					case "in-daily":
					
					$data 								= $astDB
						->join("vicidial_list vl", "vcl.lead_id = vl.lead_id", "LEFT")
						->where("vcl.status", $status)
						->where("vcl.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")
						->where("vcl.campaign_id", $campaigns, "IN")
						->getValue("vicidial_closer_log  vcl", "count(*)");
							
					break;
					
					case "in-hourly":
					
					$data 								= $astDB
						->join("vicidial_list vl", "vcl.lead_id = vl.lead_id", "LEFT")
						->where("vcl.status", $status)
						->where("vcl.call_date", array("$datehourly:00:00", "$datehourly:59:59"), "BETWEEN")
						->where("vcl.campaign_id", $campaigns, "IN")
						->getValue("vicidial_closer_log  vcl", "count(*)");
							
					break;			
					
					case "all-daily":
					
					$outsales							= $astDB
						->join("vicidial_list vl", "vlog.lead_id = vl.lead_id", "LEFT")
						->where("vlog.status", $status)
						->where("vlog.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")
						->where("vlog.campaign_id", $campaigns, "IN")
						->getValue("vicidial_log as vlog", "count(*)");
				
					$insales 							= $astDB
						->join("vicidial_list vl", "vcl.lead_id = vl.lead_id", "LEFT")
						->where("vcl.status", $status)
						->where("vcl.call_date", array("$datetoday 00:00:00", "$datetoday 23:59:59"), "BETWEEN")
						->where("vcl.campaign_id", $campaigns, "IN")
						->getValue("vicidial_closer_log  vcl", "count(*)");
					
					$data 								= $insales + $outsales;
					
					break;
				}
						
				$apiresults 								= array(
					"result" 									=> "success",
					//"query"									=> $astDB->getLastQuery(),
					"data" 										=> $data			 
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
