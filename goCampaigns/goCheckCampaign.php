<?php
/**
 * @file    	goCheckCampaign.php
 * @brief     	API to check if campaign already exists
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Alexander Jim Abenoja 
 * @author      Jeremiah Sebastian Samatra
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
	//$log_ip 										= $astDB->escape($_REQUEST['log_ip']);		
	$campaigns 										= allowed_campaigns($log_group, $goDB, $astDB);
	
	// POST or GET Variables
    $campaign_id 									= $astDB->escape($_REQUEST['campaign_id']);
    $status 										= $astDB->escape($_REQUEST['status']);
    
    // Check exisiting status
	if (!isset($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$err_msg 									= error_handle("40001");
        $apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
    } else {
		if (!empty($status)) {
			if (in_array($campaign_id, $campaigns) || $campaign_id == 'ALL') {			
				$astDB->where('status', $status);
				$astDB->get('vicidial_statuses', null, 'status');
				
				if ($astDB->count <= 0) {
					if ($campaign_id == 'ALL') {
						foreach ($campaigns["campaign_id"] as $key => $campaignid) {
							$astDB->where("campaign_id", $campaignid);
							$astDB->where("status", $status);
							$astDB->get("vicidial_campaign_statuses", NULL, "status");
							
							if($astDB->count <= 0) {
								$apiresults 			= array(
									"result" 				=> "success"
								);						
							} else {
								$err_msg 					= error_handle("41004", "status. Campaign Status already exists");
								$apiresults					= array(
									"code" 						=> "41004", 
									"result" 					=> $err_msg
								);
							}							
						}					
					} else {
						$astDB->where("campaign_id", $campaign_id);
						$astDB->where("status", $status);
						$astDB->get("vicidial_campaign_statuses", NULL, "status");
						
						if($astDB->count <= 0) {
							$apiresults 			= array(
								"result" 				=> "success"
							);						
						} else {
							$err_msg 					= error_handle("41004", "status. Campaign Status already exists");
							$apiresults					= array(
								"code" 						=> "41004", 
								"result" 					=> $err_msg
							);
						}					
					}
				} else {
					$err_msg 						= error_handle("41004", "status. Status already exists in the default statuses");
					$apiresults 					= array(
						"code" 							=> "41004", 
						"result" 						=> $err_msg
					);
				}
			} else {		
				$err_msg 							= error_handle("10108", "status. No campaigns available");
				$apiresults							= array(
					"code" 								=> "10108", 
					"result" 							=> $err_msg
				);
			}
		} elseif (!empty($campaign_id) && empty($status)) {
			$astDB->where('campaign_id', $campaign_id);
			$astDB->get('vicidial_campaigns', null, 'campaign_id');

			if ($astDB->count > 0) {
				$apiresults 					= array(
					"result" 						=> "fail", 
					"status" 						=> "Campaign already exist."
				);
			} else {
				$apiresults 					= array(
					"result" 						=> "success"
				);
			}
		}
	}
	
?>
