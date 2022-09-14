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
			->getOne("vicidial_users", "user,user_level,user_group");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		$usergroup										= $fresults["user_group"];
        
        $tenant                                         = ($userlevel < 9 && $usergroup !== "ADMIN") ? 1 : 0;
		
		if ($goapiaccess > 0 && $userlevel > 7) {
            $astDB->where('user_group', $log_group);
            $allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');

            if ($tenant) {
                // $astDB->where("user_group", $usergroup);
                $allowed_campaigns = $allowed_camps['allowed_campaigns'];
                if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
                    $allowed_campaigns = explode(" ", trim($allowed_campaigns));
                    $astDB->where('campaign_id', $allowed_campaigns, 'in');
                }
            } else {
                if (strtoupper($usergroup) != 'ADMIN') {
                    if ($user_level < 9) {
                        // $astDB->where("user_group", $usergroup);
                        $allowed_campaigns = $allowed_camps['allowed_campaigns'];
                        if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
                            $allowed_campaigns = explode(" ", trim($allowed_campaigns));
                            $astDB->where('campaign_id', $allowed_campaigns, 'in');
                        }
                    }
                }
            }
            $SELECTQuery 							= $astDB->get("vicidial_campaigns", NULL, "campaign_id");
            $testLastQuery = $astDB->getLastQuery();
            $array_camp = array();
            foreach($SELECTQuery as $camp_val){
                $array_camp[] 						= $camp_val["campaign_id"];
            }
            
			if (is_array($array_camp)) {
				$listids								= $astDB
				->where("campaign_id", $array_camp, "IN")
				->get("vicidial_lists", NULL, "list_id");
			}
	
			if ($astDB->count > 0){
				foreach ($listids as $listid) {
					$list_ids[]							= $listid["list_id"];
				}
			}
            
            $search_filter = "";
			if (!empty($search)) {
				// $astDB->where("phone_number", "%$search%", "LIKE");
				// $astDB->orWhere("first_name", "%$search%", "LIKE");
				// $astDB->orWhere("last_name", "%$search%", "LIKE");
				// $astDB->orWhere("CONCAT_WS(' ',first_name,last_name)", "$search%", "LIKE");
				// $astDB->orWhere("lead_id", "$search%", "LIKE");
                $search_filter = "AND (phone_number LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%')";
                $search_filter2 = "AND (vl.phone_number LIKE '%$search%' OR vi.first_name LIKE '%$search%' OR vi.last_name LIKE '%$search%')";
			}

            $filterDispo = "";
			if (!empty($disposition_filter)) {
				$filterDispo = "AND status = '$disposition_filter'";
				$filterDispo2 = "AND vl.status = '$disposition_filter'";
				// $astDB->where("status", $disposition_filter);
			}

			if (!empty($list_filter)) {
				$list_ids_filter = "list_id = '$list_filter'";
                $list_ids_filter2 = "AND vl.list_id = '$list_filter'";
				// $astDB->where("list_id", $list_filter);
			}

            $filterAddress = "";
			if (!empty($address_filter)) {
				$filterAddress = "AND (address1 LIKE '%$address_filter%' OR address2 LIKE '%$address_filter%')";
				$filterAddress2 = "AND (vi.address1 LIKE '%$address_filter%' OR vi.address2 LIKE '%$address_filter%')";
				// $astDB->where("address1", "%$address_filter%", "LIKE");
				// $astDB->orWhere("address2", "%$address_filter%", "LIKE");
			}
			
            $filterCity = "";
			if (!empty($city_filter)) {
				$filterCity = "AND city LIKE '%$city_filter%'";
				$filterCity2 = "AND vi.city LIKE '%$city_filter%'";
				// $astDB->where("city", "%$city_filter%", "LIKE");
			}

            $filterState = "";
			if (!empty($state_filter)) {
				$filterState = "AND state LIKE '%$state_filter%'";
				$filterState2 = "AND vi.state LIKE '%$state_filter%'";
				// $astDB->where("state", "%$state_filter%", "LIKE");
			}

            $date_filter = "";
			if (!empty($start_date) && !empty($end_date)) {
				$start_date = date("Y-m-d G:i:s", strtotime($start_date));
				$end_date = date("Y-m-d G:i:s", strtotime($end_date));

				// $astDB->where("last_local_call_time", array( date($start_date), date($end_date)), "BETWEEN");
                $date_filter = "AND last_local_call_time BETWEEN '$start_date' AND '$end_date'";
                $date_filter2 = "AND vl.call_date BETWEEN '$start_date' AND '$end_date'";
			}

			if ($goVarLimit > 0) {
				$limit 									= $goVarLimit;
			}
			
            if (count($list_ids) < 1) {
                $list_ids = array("-1");
            }

            if (empty($list_filter)) {
                // $astDB->where("list_id", $list_ids, "IN");
                $list_ids_string = implode(',', $list_ids);
                $list_ids_filter = "list_id IN ($list_ids_string)";
                $list_ids_filter2 = "AND vl.list_id IN ($list_ids_string)";
            }
            
			// $fresultsv 									= $astDB->get("vicidial_list", $limit, array("lead_id", "list_id", "first_name", "middle_initial", "last_name", "phone_number", "status", "last_local_call_time"));
            // $fresultsv_query                            = "SELECT lead_id, list_id, first_name, middle_initial, last_name, phone_number, status FROM vicidial_list WHERE $list_ids_filter $date_filter $filterDispo $filterCity $filterState $search_filter $filterAddress LIMIT $limit;";
            $fresultsv_query = "(SELECT lead_id, 
                            list_id, 
                            first_name, 
                            middle_initial, 
                            last_name, 
                            phone_number, 
                            status 
                            FROM 
                            vicidial_list 
                            WHERE $list_ids_filter $date_filter $filterDispo $filterCity $filterState $search_filter $filterAddress 
                            LIMIT $limit) 
                            UNION 
                            (SELECT vl.lead_id, 
                            vi.list_id, 
                            vi.first_name, 
                            vi.middle_initial, 
                            vi.last_name, 
                            vl.phone_number, 
                            vl.status 
                            FROM vicidial_log vl, vicidial_list vi 
                            WHERE vl.lead_id=vi.lead_id $list_ids_filter2 $date_filter2 $filterDispo2 $filterCity2 $filterState2 $search_filter2 $filterAddress
                            LIMIT $limit)";
            $fresultsv                                  = $astDB->rawQuery($fresultsv_query);
			
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
					// $dataLastCallTime[] 				= $fresults['last_local_call_time'];
					
					array_push($datago, $fresults);
				} else {
                        $dataLeadid2[] 						= $fresults['lead_id'];
                        $dataListid2[] 						= $fresults['list_id'];
                        $dataFirstName2[] 					= $fresults['first_name'];
                        $dataMiddleInitial2[] 				= $fresults['middle_initial'];
                        $dataLastName2[] 					= $fresults['last_name'];
                        $dataPhoneNumber2[] 				= $fresults['phone_number'];
                        $dataDispo2[] 						= $fresults['status'];
                        // $dataLastCallTime2[] 				= $fresults['last_local_call_time'];
                    
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
