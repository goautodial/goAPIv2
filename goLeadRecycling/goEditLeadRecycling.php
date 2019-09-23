<?php
 /**
 * @file       goEditLeadRecycling.php
 * @brief 	    API for Modifying Lead Recycling
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author	    Alexander Abenoja  <alex@goautodial.com>
 * @author     Chris Lomuntad  <chris@goautodial.com>
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
	
	// POST or GET Variables
	$recycle_id 										= $astDB->escape($_REQUEST['recycle_id']);
	$campaign_id 										= $astDB->escape($_REQUEST['campaign_id']);
	$attempt_delay 										= $astDB->escape($_REQUEST['attempt_delay']);
	$attempt_maximum 									= $astDB->escape($_REQUEST['attempt_maximum']);
	$active 											= $astDB->escape(strtoupper($_REQUEST['active']));
	$defActive 											= array('N', 'Y');

    ### ERROR CHECKING
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
	} elseif (empty($recycle_id) || is_null($recycle_id)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= array(
			"code" 											=> "40001",
			"result" 										=> $err_msg
		);
    } elseif (!empty($attempt_delay) && ($attempt_delay > 99999 || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_delay) || $attempt_delay < 120)) {
		$apiresults 									= array(
			"result" 										=> "Error: Maximum is 5 digits. No special characters allowed. Must be atleast 120 seconds"
		);
	} elseif (!empty($attempt_delay) && (strlen($attempt_maximum) > 3 || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_maximum))) {
		$apiresults 									= array(
			"result" 										=> "Error: Special characters found in Attempt Maximum and must not be empty"
		);
	} elseif (!in_array($active, $defActive) && $active != null) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for active is N for No and Y for Yes only."
		);
	} elseif (is_array($campaigns)) {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {		
			if (in_array($campaign_id, $campaigns)) {
				$astDB->where("campaign_id", $campaign_id);
				$astDB->where('recycle_id', $recycle_id);
				//$astDB->getOne("vicidial_lead_recycle", "campaign_id");
				$sqlCheck 								= $astDB->get('vicidial_lead_recycle');
		
				if ($astDB->count > 0) {
					foreach ($sqlCheck as $fresults){
						$dataAttemptDelay 				= $fresults['attempt_delay'];
						$dataAttemptMaximum 			= $fresults['attempt_maximum'];
						$dataCampID 					= $fresults['campaign_id'];
						$dataActive 					= $fresults['active'];			
					}

					if (empty($status)) {
						$status 						= $dataStatus;
					}
					
					if (empty($attempt_delay)) {
						$attempt_delay 					= $dataAttemptDelay;
					}
					
					if (empty($attempt_maximum)) {
						$attempt_maximum 				= $dataAttemptMaximum;
					}
					
					if (empty($campaign_id)) {
						$campaign_id 					= $dataCampID;
					}
					
					if (empty($active)) {
						$active 						= $dataActive;
					}

					$updateData 						= array(
						'attempt_delay' 					=> $attempt_delay,
						'attempt_maximum' 					=> $attempt_maximum,
						'active' 							=> $active
					);
						
					$astDB->where('recycle_id', $recycle_id);
					$astDB->where('campaign_id', $campaign_id);
					
					$rsltv1 							= $astDB->update('vicidial_lead_recycle', $updateData);
					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Lead Recycling: $status", $log_group, $astDB->getLastQuery());					
					
					if ($rsltv1) {
						$apiresults 					= array(
							"result" 						=> "success"
						);
					} else {
						$apiresults 					= array(
							"result" 						=> "Error: Try updating Lead Recycling again"
						);				
					}
				} else {
					$apiresults 						= array(
						"result" 							=> "Error: Lead Recycle ID does not exist!"
					);
				}
			} else {
				$err_msg 								= error_handle("10001", "Insufficient permision");
				$apiresults 							= array(
					"code" 									=> "10001", 
					"result" 								=> $err_msg
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
