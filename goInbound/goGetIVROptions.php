<?php
/**
 * @file        goGetIVROptions.php
 * @brief       API to get defined IVR Options
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Jerico James Milo
 * @author      Alexander Jim Abenoja
 * @author      Jeremiah Sebastian V. Samatra
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
    
	$log_user 											= $session_user;
	$log_group 											= go_get_groupid($session_user, $astDB); 
	$log_ip 											= $astDB->escape($_REQUEST['log_ip']);
	$goUser												= $astDB->escape($_REQUEST['goUser']);
	$goPass												= (isset($_REQUEST['log_pass']) ? $astDB->escape($_REQUEST['log_pass']) : $astDB->escape($_REQUEST['goPass']));	
    $menu_id 											= $astDB->escape($_REQUEST['menu_id']);
    
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
	}  elseif (empty($menu_id) || is_null($menu_id)) {
        $apiresults 									= array(
			"result" 										=> "Error: Set a value for Menu ID."
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
			$tenant										= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
			
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
			
			$astDB->where("menu_id", $menu_id);
			$selectQuery 								= $astDB->get("vicidial_call_menu_options");
			
			if ($astDB->count > 0) {
				foreach($selectQuery as $fresults){
					$id[] 								= $fresults["menu_id"];
					$option_value[] 					= $fresults["option_value"];
					$option_description[] 				= $fresults["option_description"];
					$option_route[] 					= $fresults["option_route"];
					$option_route_value[] 				= $fresults["option_route_value"];
					$option_route_value_context[] 		= $fresults["option_route_value_context"];
				}	
				
				$apiresults 							= array(
					"result" 								=> "success", 
					"menu_id" 								=> $id, 
					"option_value" 							=> $option_value, 
					"option_description" 					=> $option_description, 
					"option_route" 							=> $option_route, 
					"option_route_value" 					=> $option_route_value, 
					"option_route_value_context" 			=> $option_route_value_context
					//"query" 								=> $selectQuery
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
