<?php
 /**
 * @file 		goAddMOH.php
 * @brief 		API for Adding Music On Hold
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
 * @author		Jeremiah Sebastian Samatra
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

	
	$moh_id 											= $astDB->escape(($_REQUEST['moh_id'] ?? ''));
	$moh_name 											= $astDB->escape(($_REQUEST['moh_name'] ?? ''));
	$user_group 										= $astDB->escape(($_REQUEST['user_group'] ?? ''));
	$active 											= strtoupper((string) $astDB->escape(($_REQUEST['active'] ?? '')));
	$random 											= strtoupper((string) $astDB->escape(($_REQUEST['random'] ?? '')));
	$values 											= $astDB->escape(($_REQUEST['item'] ?? ''));
    $filename                                           = $astDB->escape(($_REQUEST['filename'] ?? ''));

	
    ### Default values 
    $defActive 											= ["Y","N"];
    $defRandom 											= ["Y","N"];

	// Error Checking
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
	} elseif ($moh_id == null || strlen((string) $moh_id) < 3) {
		$apiresults 									= [
			"result" 										=> "Error: Set a value for MOH ID not less than 3 characters."
		];
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', (string) $moh_name) || $moh_name == null) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in moh_name and must not be empty"
		];
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', (string) $moh_id)) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in moh_id"
		];
	} elseif (!in_array($active, (is_array($defActive) ? $defActive : []))) {
		$apiresults 									= [
			"result" 										=> "Error: Default value for active is Y or N only."
		];
	} elseif (!in_array($random, (is_array($defRandom) ? $defRandom : []))) {
		$apiresults 									= [
			"result" 										=> "Error: Default value for random is Y or N only."
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
			# check MOH ID if it exists
			$astDB->where('moh_id', $moh_id);
			$astDB->getOne('vicidial_music_on_hold');
			$countResult 								= $astDB->getRowCount();
			
			if ($countResult > 0) {
				$apiresults 							= [
					"result" 								=> "Error: MOH ID already exists."
				];	
			} else {			
				$insertData 							= [
					'moh_id' 								=> $moh_id,
					'moh_name' 								=> $moh_name,
					'user_group' 							=> $user_group,
					'active' 								=> $active,
					'random' 								=> $random
				];
				
				$rsltv 									= $astDB->insert('vicidial_music_on_hold', $insertData);				
				$log_id 								= log_action($goDB, 'ADD', $log_user, $log_ip, "Added Music On-Hold: $moh_id", $log_group, $astDB->getLastQuery());

				if ($rsltv == false) {
					$apiresults 						= [
						"result" 							=> "Error: Add failed, check your details"
					];
				} else {
					$insertData 						= [
						//'filename' 							=> 'conf',
                                                'filename'                                                      => $filename,
						'rank' 								=> '1',
						'moh_id' 							=> $moh_id
					];
					
					$insertResult 						= $astDB->insert('vicidial_music_on_hold_files', $insertData);
					$log_id 							= log_action($goDB, 'ADD', $log_user, $log_ip, "Added Music On-Hold: $moh_id", $log_group, $astDB->getLastQuery());

					$updateData 						= [
						'rebuild_conf_files' 				=> 'Y',
						'rebuild_music_on_hold' 			=> 'Y',
						'sounds_update' 					=> 'Y'
					];
					
					$astDB->where('generate_vicidial_conf', 'Y');
					$astDB->where('active_asterisk_server', 'Y');
					$astDB->update('servers', $updateData);
					
					$log_id 							= log_action($goDB, 'ADD', $log_user, $log_ip, "Added Music On-Hold: $moh_id", $log_group, $astDB->getLastQuery());
					$apiresults 						= [
						"result" 							=> "success"
					];
				}
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
