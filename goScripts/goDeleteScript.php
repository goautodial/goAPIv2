<?php
 /**
 * @file        goDeleteScript.php
 * @brief       API for Deleting Specific Scripts
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
 * @author      Jeremiah Sebastian V. Samatra
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
 
	$log_user 									= $session_user;
	$log_group 									= go_get_groupid($session_user, $astDB);    
	$log_ip 									= $astDB->escape($_REQUEST["log_ip"]);
	
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
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if ($log_group !== "ADMIN"){
					$astDB->where("user_group", $log_group);
					$astDB->orWhere("user_group", "---ALL---");
				}
			}	
			
			if ($astDB->count > 0) {        
				$astDB->where("script_id", $script_id);
				$getScripts 					= $astDB->getOne("vicidial_scripts", "script_id");

				if($getScripts) {
					$astDB->where("script_id", $script_id);
					$astDB->delete("vicidial_scripts");
					$deleteQuery 				= $astDB->getLastQuery();

					$astDB->where("script_id", $script_id);
					$astDB->delete("go_scripts");

					$data_update 				= array(
						"campaign_script" 			=> ""
					);
					
					$astDB->where("campaign_script", $script_id);
					$astDB->update("vicidial_campaigns", $data_update);

					$log_id 					= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted Script ID: $script_id", $log_group, $deleteQuery);

					$apiresults 				= array(
						"result" 					=> "success"
					);
				} else {
					$apiresults 				= array(
						"result" 					=> "Error: Script doesn't exist."
					);
				}
			} else {
				$err_msg 						= error_handle( "10001", "Insufficient permision" );
				$apiresults 					= array(
					"code" 							=> "10001", 
					"result" 						=> $err_msg
				);			
			}
		} else {
			$apiresults 						= array(
				"result" 							=> "Error: Script doesn't exist."
			);		
		}
	}
?>
