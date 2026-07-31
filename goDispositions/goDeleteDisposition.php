<?php
 /**
 * @file 		goDeleteDisposition.php
 * @brief 		API for Dispositions
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Jeremiah Sebastian Samatra
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

    include_once (__DIR__ . "/goAPI.php");
    
	$campaigns 											= allowed_campaigns($log_group, $goDB, $astDB);
	$campaign_id 										= $astDB->escape(($_REQUEST["campaign_id"] ?? ''));	
	$statuses 											= $astDB->escape(($_REQUEST["statuses"] ?? ''));

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
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= [
			"code" 											=> "40001",
			"result" 										=> $err_msg
		];
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
				if (in_array($campaign_id, (is_array($campaigns) ? $campaigns : []))) {
					$astDB->where("campaign_id", $campaign_id);
					$astDB->get("vicidial_campaign_statuses");
					
					if($astDB->count > 0) {
						if  (empty($statuses) || is_null($statuses)) {
							$astDB->where("campaign_id", $campaign_id);
							$astDB->delete("vicidial_campaign_statuses");
							$log_id 					= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted custom statuses from Campaign: $campaign_id", $log_group, $astDB->getLastQuery());
							
							$apiresults 				= [
								"result" 					=> "success"
							];						
						} else {
							// check if custom status/disposition exists
							$astDB->where("status", $statuses);
							$astDB->where("campaign_id", $campaign_id);
							$astDB->get("vicidial_campaign_statuses");
							
							if($astDB->count > 0) {
								$astDB->where("status", $statuses);
								$astDB->where("campaign_id", $campaign_id);
								$astDB->delete("vicidial_campaign_statuses");
								$log_id 				= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted status: $statuses from Campaign: $campaign_id", $log_group, $astDB->getLastQuery());							

								$goDB->rawQuery("SHOW tables LIKE 'go_statuses';");
								
								if ($goDB->count > 0) {
									$goDB->where("campaign_id", $campaign_id);
									$goDB->where("status", $statuses);
									$goDB->delete("go_statuses");
									$log_id 			= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted status: $statuses from Campaign: $campaign_id", $log_group, $goDB->getLastQuery());
								}				
								
								$apiresults 			= [
									"result" 				=> "success"
								];
							} else {
								$apiresults 			= [
									"result" 				=> "Error: Status doesn't exist"
								];
							}
						}									
					} else {
						$apiresults 					= [
							"result" 						=> "Error: Campaign doesn't exist"
						];
					}			
				} else {
					$apiresults 						= [
						"result" 							=> "Error: Current user ".$log_user." doesn't have enough permission to access this feature"
					];
				}
			} else {
				$apiresults 							= [
					"result" 								=> "Error: Current user ".$log_user." doesn't have enough permission to access this feature"
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
