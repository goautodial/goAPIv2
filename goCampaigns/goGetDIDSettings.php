<?php
/**
 * @file        goGetDIDSettings.php
 * @brief       API to get DID Settings for a DID
 * @copyright   Copyright (c) 2019 GOautodial Inc.
 * @author      Noel Umandap
 * @author      Alexander Jim Abenoja
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

    $did 												= $astDB->escape($_REQUEST['did']);
    
    // Check campaign_id if its null or empty
	if (empty ($goUser) || is_null ($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty ($goPass) || is_null ($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty ($log_user) || is_null ($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($did) || is_null($did) ) {
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
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					}
				}					
			}
		
			$astDB->where('did_pattern', $did);
			$astDB->join('vicidial_inbound_groups groupSetting', 'didSetting.group_id = groupSetting.group_id ', 'left');
			$result 									= $astDB->get('vicidial_inbound_dids didSetting', null, 'didSetting.did_id,didSetting.did_pattern,didSetting.did_route,didSetting.group_id,didSetting.menu_id,didSetting.user,didSetting.voicemail_ext,groupSetting.group_color');

			if ($astDB->count > 0) {
				foreach($result as $info){
					$data['did_id']         			= $info['did_id'];
					$data['did_pattern']    			= $info['did_pattern'];
					$data['did_route']      			= $info['did_route'];
					$data['group_id']       			= $info['group_id'];
					$data['menu_id']        			= $info['menu_id'];
					$data['user']           			= $info['user'];
					$data['voicemail_ext']  			= $info['voicemail_ext'];
					$data['group_color']    			= $info['group_color'];
				}
				
				$apiresults 							= array(
					"result" 								=> "success", 
					"data" 									=> $data
				);			
			} else {
				$apiresults 							= array(
					"result" 								=> "error"
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
