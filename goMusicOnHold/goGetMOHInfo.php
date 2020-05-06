<?php
 /**
 * @file 		goGetMOHInfo.php
 * @brief 		API for Getting Music On Hold Info
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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
	
    ### POST or GET Variables
    $moh_id 											= $astDB->escape($_REQUEST['moh_id']);
    
	// Error Checking
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
	} elseif ($moh_id == null || strlen($moh_id) < 3) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for MOH ID not less than 3 characters."
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
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										= (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
					}
				}					
			}

			$astDB->where('moh_id', $moh_id);
			$rsltv 										= $astDB->get('vicidial_music_on_hold');
			$countResult = $astDB->getRowCount();

			if ($countResult > 0) {
				foreach ($rsltv as $fresults){
					$dataModId[] 						= $fresults['moh_id'];
					$dataMohName[] 						= $fresults['moh_name'];
					$dataActive[] 						= $fresults['active'];
					$dataRandom[] 						= $fresults['random'];
					$dataUserGroup[] 					= $fresults['user_group'];
					
        				$astDB->where('moh_id', $fresults['moh_id']);
				        $rsltvfiles = $astDB->get('vicidial_music_on_hold_files');
				        $countResultFiles = $astDB->getRowCount();

				        if($countResultFiles > 0){
				                foreach($rsltvfiles as $fresultsfiles){
				                        $dataFileName[] = $fresultsfiles['filename'];
				                        $dataRank[] = $fresultsfiles['rank'];
				                }
				        }

				}
			        $apiresults = array(
			                "result" => "success",
			                "moh_id" => $dataModId,
			                "moh_name" => $dataMohName,
			                "active" => $dataActive,
			                "random" => $dataRandom,
			                "user_group" => $dataUserGroup,
			                "filename" => $dataFileName,
			                "rank" => $dataRank
			        );
				
				//$log_id 								= log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed info of Music On-Hold: $moh_id", $log_group);
			} else {
				$apiresults 							= array(
					"result" 								=> "Error: MOH doesn't exist."
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
