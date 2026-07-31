<?php
 /**
 * @file 		goEditMOH.php
 * @brief 		API for Modifying Music On Hold
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
	
    ### POST or GET Variables
	$moh_id 											= $astDB->escape(($_REQUEST['moh_id'] ?? ''));
	$moh_name 											= $astDB->escape(($_REQUEST['moh_name'] ?? ''));
	$user_group 										= $astDB->escape(($_REQUEST['user_group'] ?? ''));
	$active 											= strtoupper((string) $astDB->escape(($_REQUEST['active'] ?? '')));
	$random 											= strtoupper((string) $astDB->escape(($_REQUEST['random'] ?? '')));
	$values 											= $astDB->escape(($_REQUEST['item'] ?? ''));
	$filename 											= $astDB->escape(($_REQUEST['filename'] ?? ''));
	$ranks 												= $astDB->escape(($_REQUEST['rank'] ?? ''));
	
    ### Default values 
    $defActive 											= ["Y","N"];
    $defRandom 											= ["N","Y"];

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
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', (string) $moh_name)) {
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
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper((string) $log_group) !== 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					}
				}					
			}	
	
			$astDB->where('moh_id', $moh_id);
			$rsltvMoh 									= $astDB->getOne('vicidial_music_on_hold');
			
			if ($rsltvMoh) {
				foreach ($rsltvMoh as $fresults) {
					$datamoh_id 						= $fresults['moh_id'];
					$datamoh_name 						= $fresults['moh_name'];
					$dataactive 						= $fresults['active'];
					$datarandom 						= $fresults['random'];
					$datauser_group						= $fresults['user_group'];
				}			

				if ($filename != null) {
					$insertData 						= [
						'filename' 							=> $filename,
						//'rank' 								=> $rank,
                                                'rank'                                                          => '1',
						'moh_id' 							=> $moh_id
					];
					$astDB->where('moh_id', $moh_id);
					$astDB->update('vicidial_music_on_hold_files', $insertData);
					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Music On-Hold: $moh_id", $log_group, $astDB->getLastQuery());
				}
				
				if ($moh_name == null) {$moh_name 		= $datamoh_name;}
				if ($active == null) {$active 			= $dataactive;}
				if ($user_group == null) {$user_group 	= $datauser_group;}
				if ($random == null) {$random 			= $datarandom;}

				$updateData 							= [
					'moh_name' 								=> $moh_name,
					'active' 								=> $active,
					'user_group' 							=> $user_group,
					'random' 								=> $random
				];
				
				$astDB->where('moh_id', $moh_id);
				$rsltv1 								= $astDB->update('vicidial_music_on_hold', $updateData);
				/*$log_id 								= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Music On-Hold: $moh_id", $log_group, astDB->getLastQuery());

				$updateData 							= array(
					'rebuild_conf_files' 					=> 'Y',
					'rebuild_music_on_hold' 				=> 'Y',
					'sounds_update' 						=> 'Y'
				);
				
				$astDB->where('generate_vicidial_conf', 'Y');
				$astDB->where('active_asterisk_server', 'Y');
				$astDB->update('servers', $updateData);
					
				$apiresults 							= array(
					"result" 								=> "success"
				);*/
				
				if (!$rsltv1) {
					$apiresults 						= [
						"result" 							=> "Error: Try updating Moh Again"
					];
				} else {
					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Music On-Hold: $moh_id", $log_group, $astDB->getLastQuery());
					
					$apiresults 						= [
						"result" 							=> "success"
					];
					
					$affected_rows++;
					
					if ($affected_rows) {
						//$newQuery2 = "UPDATE servers SET rebuild_conf_files='Y',rebuild_music_on_hold='Y',sounds_update='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y';";
						$updateData 					= [
							'rebuild_conf_files' 			=> 'Y',
							'rebuild_music_on_hold'			=> 'Y',
							'sounds_update' 				=> 'Y'
						];
						$astDB->where('generate_vicidial_conf', 'Y');
						$astDB->where('active_asterisk_server', 'Y');
						$astDB->update('servers', $updateData);
						$apiresults 					= [
							"result" 						=> "success"
						];
					} else {
						$apiresults 					= [
							"result" 						=> "Error: Try updating Moh Again"
						];
					}
				}
			} else {
				$apiresults 							= [
					"result" 								=> "Error: MOH doesn't exist"
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
