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
 
	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB); 
	$type	 										= $astDB->escape($_REQUEST["type"]);
	$campaigns 										= allowed_campaigns($log_group, $goDB, $astDB);
    
    // ERROR CHECKING 
	if ( !isset($log_user) || is_null($log_user) ) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (is_array($campaigns)) {
		if ( !isset($_REQUEST["type"]) ) {	
			$type									= "all";
		}
		
		$status										= "SALE";
		$datetoday									= date("Y-m-d");
		$datehourly	 								= date('Y-m-d H');
		//$datestartday								= date("Y-m-d") . " 00:00:00";
		//$dateendday									= date("Y-m-d") . " 23:59:59";
		
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

?>
