<?php
 /**
 * @file        goGetScriptInfo.php
 * @brief       API for Getting Script Info
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Jeremiah Sebastian V. Samatra
 * @author      Alexander Jim Abenoja
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

	include_once ("goAPI.php");	 
 
	$log_user 									= $session_user;
	$log_group 									= go_get_groupid($session_user, $astDB);    
	$log_ip 									= $astDB->escape($_REQUEST['log_ip']);
	
	$script_id 									= $astDB->escape($_REQUEST["script_id"]); 
	
	if ( empty($log_user) || is_null($log_user) ) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
	} elseif ( empty($script_id) || is_null($script_id) ) {
		$apiresults 							= array(
			"result" 								=> "Error: Set a value for Script ID."
		);
    } else {
		// check if script ID exists
        $astDB->where("script_id", $script_id);        
		$astDB->getOne("vicidial_scripts", "script_id");
		
		if ($astDB->count > 0) {
			// check if script exists with conditions below:
			if ( checkIfTenant($log_group, $goDB) ) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere('user_group', "---ALL---");
			} else {
				if ($log_group !== "ADMIN"){
					$astDB->where('user_group', $log_group);
					$astDB->orWhere('user_group', "---ALL---");
				}
			}
        
			$astDB->where("script_id", $script_id);        
			$script 								= $astDB->get("vicidial_scripts");
			
			if ($script) {
				foreach ($script as $fresults) {
					$apiresults 					= array(
						"result" 						=> "success", 
						"script_id" 					=> $fresults['script_id'], 
						"script_name" 					=> $fresults['script_name'], 
						"script_comments" 				=> $fresults['script_comments'], 
						"active" 						=> $fresults['active'], 
						"user_group" 					=> $fresults['user_group'], 
						"script_text" 					=> $fresults['script_text']
					);
				}
			} else {
				$err_msg 							= error_handle( "10001", "Insufficient permision" );
				$apiresults 						= array(
					"code" 								=> "10001", 
					"result" 							=> $err_msg
				);			
			}				
		} else {
			$apiresults 							= array(
				"result" 								=> "Error: Script doesn't exist"
			);
		}
	}
	
?>
