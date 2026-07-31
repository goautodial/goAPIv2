<?php
 /**
 * @file        goGetPauseCodeInfo.php
 * @brief       API to get specific Pause Code
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author     	Noel Umandap
 * @author     	Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho
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
	$campaign_id		 								= $astDB->escape($_REQUEST['pauseCampID']);
	$pause_code 										= $astDB->escape($_REQUEST['pause_code']);

    ### ERROR CHECKING ...
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
	} elseif ($campaign_id == null || strlen((string) $campaign_id) < 3) {
		$apiresults 									= [
			"result" 										=> "Error: Set a value for CAMP ID not less than 3 characters."
		];
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', (string) $pause_code) || $pause_code == null) {
		$apiresults 									= [
			"result" 										=> "Error: Special characters found in pause code and must not be empty"
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
			$tenant										=  (checkIfTenant($log_group, $goDB)) ? 1 : 0;
			
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
			
			$cols 										= [ "pause_code", "pause_code_name", "billable", "campaign_id" ];
			
			$astDB->where('campaign_id', $campaign_id);
			$astDB->where('pause_code', $pause_code);
			$hotkey 									= $astDB->get('vicidial_pause_codes', null, $cols);

			if ($hotkey) {
				foreach ($hotkey as $fresults) {
					$dataCampID[]   					= $fresults['campaign_id'];
					$dataPC[]       					= $fresults['pause_code'];
					$dataPCN[]      					= $fresults['pause_code_name'];
					$dataBill[]     					= $fresults['billable']; 
				}

				$apiresults					 			= [
					"result" 								=> "success", 
					"campaign_id" 							=> $dataCampID, 
					"pause_code" 							=> $dataPC, 
					"pause_code_name" 						=> $dataPCN, 
					"billable" 								=> $dataBill
				];
			} else {
				$apiresults 							= [
					"result" 								=> "Error: Pause Code does not exist."
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
