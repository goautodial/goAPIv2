<?php
 /**
 * @file        goAddLeadRecycling.php
 * @brief 	    API for Adding Lead Recycling
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Alexander Jim Abenoja
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

	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB); 
	$log_ip 										= $astDB->escape($_REQUEST['log_ip']);
	$campaigns 										= allowed_campaigns($log_group, $goDB, $astDB);	
	
	// POST or GET Variables
	$campaign_id 									= $astDB->escape($_REQUEST['campaign_id']);
	$status 										= $astDB->escape($_REQUEST['status']);
	$attempt_delay 									= $astDB->escape($_REQUEST['attempt_delay']);
	$attempt_maximum 								= $astDB->escape($_REQUEST['attempt_maximum']);
	$active 										= $astDB->escape(strtoupper($_REQUEST['active']));

	// Default values 
    $defActive 										= array(
		"Y",
		"N"
	);
	
	// ERROR CHECKING 
	if (empty($campaign_id) || empty($session_user) || empty($status)) {
		$err_msg 									= error_handle("40001", "campaign_id, session_user, and status");
		$apiresults 								= array(
			"code" 										=> "40001", 
			"result" 									=> $err_msg
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status)) {
		$apiresults 								= array(
			"result" 									=> "Error: Special characters found in status"
		);
	} elseif (preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_delay) || $attempt_delay < 120 || $attempt_delay > 99999){
		$apiresults									= array(
			"result" 									=> "Error: Attempt Delay Maximum is 5 digits. No special characters allowed. Must be atleast 120 seconds"
		);
	} elseif ($attempt_maximum < 1 || strlen($attempt_maximum) > 3 || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_maximum)) {
		$apiresults 								= array(
			"result" 									=> "Error: Attempt Maximum is 3 digits. No special characters allowed."
		);
	} elseif (!in_array($active,$defActive) && !empty($active)) {
		$apiresults 								= array(
			"result" 									=> "Error: Default value for Active is Y or N only."
		);
	} else {
		if (empty($attempt_delay)) {
			$attempt_delay 							= "1800";
		}
		
		if (empty($attempt_maximum)) {
			$attempt_maximum 						= "2";
		}
		
		if(empty($active)) {
			$active 								= "Y";
		}
		
		if (is_array($campaigns)) {
			$astDB->where('campaign_id', $campaigns, 'IN');
			$astDB->getOne("vicidial_campaign_statuses", "status");
			$countCheck1							= $astDB->getRowCount();
						
			$astDB->getOne("vicidial_statuses", "status");
			$countCheck2							= $astDB->getRowCount();
						
			if ($countCheck1 > 0 || $countCheck2 >0) {
				if ($campaign_id == "ALL") {					
					$astDB->where('campaign_id', $campaigns, 'IN');
					$query 							= $astDB->get('vicidial_campaigns', NULL, 'campaign_id');

					foreach ($query as $row){
						$campaign_id 				= $row['campaign_id'];
						
						$data						= array( 
							"campaign_id"				=> $campaign_id,
							"status"					=> $status,
							"attempt_delay"				=> $attempt_delay,
							"attempt_maximum"			=> $attempt_maximum,
							"active"					=> $active
						);
						
						$astDB->insert("vicidial_lead_recycle", $data);
						$log_id 					= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Lead Recycling under Status: $status in Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());
					}
						
					$apiresults 					= array(
						"result" 						=> "success"
					);						
				} else {
					$data							= array( 
						"campaign_id"					=> $campaign_id,
						"status"						=> $status,
						"attempt_delay"					=> $attempt_delay,
						"attempt_maximum"				=> $attempt_maximum,
						"active"						=> $active
					);
					
					$astDB->insert("vicidial_lead_recycle", $data);
					$log_id 						= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Lead Recycling under Status: $status in Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());
					
					$apiresults 					= array(
						"result" 						=> "success"
					);
				}
			} else {
				$apiresults 						= array(
					"result" 							=> "Error: Campaign ID or Status does not exist."
				);
			}
		}
	}
?>
