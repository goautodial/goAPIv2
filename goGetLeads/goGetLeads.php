<?php
 /**
 * @file 		goGetLeads.php
 * @brief 		API for Getting Leads
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author		Warren Ipac Briones
 * @author     	Alexander Abenoja
 * @author     	Chris Lomuntad
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
	$search 											= $astDB->escape($_REQUEST['search']);
	$disposition_filter 								= $astDB->escape($_REQUEST['disposition_filter']);
	$list_filter 										= $astDB->escape($_REQUEST['list_filter']);
	$address_filter 									= $astDB->escape($_REQUEST['address_filter']);
	$city_filter 										= $astDB->escape($_REQUEST['city_filter']);
	$state_filter 										= $astDB->escape($_REQUEST['state_filter']);
	$search_customers 									= $astDB->escape($_REQUEST['search_customers']);
	$goVarLimit 										= $astDB->escape($_REQUEST["goVarLimit"]);
	$start_date										= $astDB->escape($_REQUEST["start_date"]);
	$end_date                                                                           	= $astDB->escape($_REQUEST["end_date"]);
	$limit 												= 1000;	
	$list_ids											= array();

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
	} elseif (empty($campaigns) || is_null($campaigns)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= array(
			"code" 											=> "40001",
			"result" 										=> $err_msg
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
				$listids								= $astDB
				->where("campaign_id", $campaigns, "IN")
				->get("vicidial_lists", NULL, "list_id");
			}
	
			if ($astDB->count > 0){
				foreach ($listids as $listid) {
					$list_ids[]							= $listid["list_id"];
				}
			}
			
			if (!empty($search)) {
				$astDB->where("phone_number", "%$search%", "LIKE");
				$astDB->orWhere("first_name", "$search%", "LIKE");
				$astDB->orWhere("last_name", "$search%", "LIKE");
				$astDB->orWhere("lead_id", "$search%", "LIKE");
			}

			if (!empty($disposition_filter)) {
				//$filterDispo = "AND status = '$disposition_filter'";
				$astDB->where("status", $disposition_filter);
			}

			if (!empty($list_filter)) {
				//$filterList = "AND list_id = '$list_filter'";
				$astDB->where("list_id", $list_filter);
			}

			if (!empty($address_filter)) {
				//$filterAddress = "AND (address1 LIKE '%$address_filter%' OR address2 LIKE '%$address_filter%')";
				$astDB->where("address1", "%$address_filter%", "LIKE");
				$astDB->orWhere("address2", "%$address_filter%", "LIKE");
			}
			
			if (!empty($city_filter)) {
				//$filterCity = "AND city LIKE '%$city_filter%'";
				$astDB->where("city", "%$city_filter%", "LIKE");
			}

			if (!empty($state_filter)) {
				//$filterState = "AND state LIKE '%$state_filter%'";
				$astDB->where("state", "%$state_filter%", "LIKE");
			}

			if (!empty($start_date) && !empty($end_date)) {
				$start_date = date("Y-m-d G:i:s", strtotime($start_date));
				$end_date = date("Y-m-d G:i:s", strtotime($end_date));

				$astDB->where("last_local_call_time", array( date($start_date), date($end_date)), "BETWEEN");
			}

			if ($goVarLimit > 0) {
				$limit 									= $goVarLimit;
			}
			
			$astDB->where("list_id", $list_ids, "IN");
			$fresultsv 									= $astDB->get("vicidial_list", $limit, "*");
			
			// GET CUSTOMER LIST
			$fresultsvgo 								= $goDB->get("go_customers", null, "lead_id");
			$lead_ids_go								= array();
			
			foreach ($fresultsvgo as $fresultsgo) {
				$lead_id_go								= $fresultsgo["lead_id"];
				
				array_push($lead_ids_go, $lead_id_go);
			}
			
			$datago 									= array();
			$data 										= array();
			
			foreach ($fresultsv as $fresults) {
				if (in_array($fresults['lead_id'], $lead_ids_go)) {
					$dataLeadid[] 						= $fresults['lead_id'];
					$dataListid[] 						= $fresults['list_id'];
					$dataFirstName[] 					= $fresults['first_name'];
					$dataMiddleInitial[] 				= $fresults['middle_initial'];
					$dataLastName[] 					= $fresults['last_name'];
					$dataPhoneNumber[] 					= $fresults['phone_number'];
					$dataDispo[] 						= $fresults['status'];
					$dataLastCallTime[] 				= $fresults['last_local_call_time'];
					
					array_push($datago, $fresults);
				} else {
					$dataLeadid2[] 						= $fresults['lead_id'];
					$dataListid2[] 						= $fresults['list_id'];
					$dataFirstName2[] 					= $fresults['first_name'];
					$dataMiddleInitial2[] 				= $fresults['middle_initial'];
					$dataLastName2[] 					= $fresults['last_name'];
					$dataPhoneNumber2[] 				= $fresults['phone_number'];
					$dataDispo2[] 						= $fresults['status'];
					$dataLastCallTime2[] 				= $fresults['last_local_call_time'];
				
					array_push($data, $fresults);
				}
			}
			
			if ($search_customers) {
				$apiresults 							= array(
					"result" 								=> "success", 
					"lead_id" 								=> $dataLeadid, 
					"list_id" 								=> $dataListid, 
					"first_name" 							=> $dataFirstName, 
					"middle_initial" 						=> $dataMiddleInitial, 
					"last_name" 							=> $dataLastName, 
					"phone_number" 							=> $dataPhoneNumber, 
					"status" 								=> $dataDispo, 
					"last_call_time" 						=> $dataLastCallTime, 
					"data" 									=> $datago
				);
			} else {
				$apiresults 							= array(
					"result" 								=> "success", 
					"lead_id" 								=> $dataLeadid2, 
					"list_id" 								=> $dataListid2, 
					"first_name" 							=> $dataFirstName2, 
					"middle_initial" 						=> $dataMiddleInitial2, 
					"last_name" 							=> $dataLastName2, 
					"phone_number" 							=> $dataPhoneNumber2, 
					"status" 								=> $dataDispo2, 
					"last_call_time" 						=> $dataLastCallTime2,
					"data" 									=> $data
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
