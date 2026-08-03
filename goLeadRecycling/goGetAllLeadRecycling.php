<?php
 /**
 * @file        goGetAllLeadRecycling.php
 * @brief 	    API for Getting All Lead Recycling
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author	    Alexander Abenoja  <alex@goautodial.com>
 * @author      Chris Lomuntad  <chris@goautodial.com>
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


	$campaigns 											= allowed_campaigns($log_group, $goDB, $astDB);

    ### ERROR CHECKING
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
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			if (is_array($campaigns)) {	
				$astDB->where("campaign_id", $campaigns, "IN");
				$astDB->orderBy("campaign_id", "desc");
				$rsltv 									= $astDB->get('vicidial_lead_recycle');

				if ($astDB->count > 0) {
					foreach ($rsltv as $fresults) {
						$recycle_id[] 					= $fresults['recycle_id'];
						$campaign_id[] 					= $fresults['campaign_id'];
						$status[] 						= $fresults['status'];
						$attempt_delay[] 				= $fresults['attempt_delay'];
						$attempt_maximum[] 				= $fresults['attempt_maximum'];
						$active[] 						= $fresults['active'];
					}
					
					$apiresults 						= [
						"result" 							=> "success", 
						"recycle_id" 						=> $recycle_id,
						"campaign_id"						=> $campaign_id,
						"status"							=> $status,
						"attempt_delay"						=> $attempt_delay,
						"attempt_maximum"					=> $attempt_maximum,
						"active"							=> $active
					];        
				} else {
					$apiresults 						= [
						"result" 							=> "No data available."
					];
				}
			} else {
				$err_msg 								= error_handle("10108", "status. No campaigns available");
				$apiresults								= [
					"code" 									=> "10108", 
					"result" 								=> $err_msg
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
