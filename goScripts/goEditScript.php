<?php
 /**
 * @file        goEditScript.php
 * @brief       API for Editing Specific Script
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
 
	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB);    
	$log_ip 										= $astDB->escape($_REQUEST['log_ip']);
	
	$script_id 										= $astDB->escape($_REQUEST["script_id"]); 	
    $script_name 									= $astDB->escape($_REQUEST["script_name"]); 
    $script_comments 								= $astDB->escape($_REQUEST['script_comments']);
    $script_text 									= $astDB->escape($_REQUEST['script_text']);
    $script_text 									= str_replace('\n','',$script_text);
    $user_group 									= $astDB->escape($_REQUEST['user_group']);
    $active 										= $astDB->escape($_REQUEST['active']);
    
    ### Default values
    $defActive 										= array("Y","N");    
    
	if ( empty($log_user) || is_null($log_user) ) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif ( empty($script_id) || is_null($script_id) ) {
		$apiresults 								= array(
			"result" 									=> "Error: Set a value for Script ID."
		);
	} elseif ( preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$script_name) && $script_name != null ) {
		$apiresults 								= array(
			"result" 									=> "Error: Special characters found in script name"
		);
	} elseif ( !in_array($active,$defActive) && $active != null ) {
		$apiresults 								= array(
			"result" 									=> "Error: Default value for active is Y or N only."
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

			$astDB->where('script_id' , $script_id);
			$script								 	= $astDB->get('vicidial_scripts');

			if ($script) {
				foreach($script as $fresults){
					$datascript_id 					= $fresults['script_id'];
					$datascript_name 				= $fresults['script_name'];
					$datascript_comments 			= $fresults['script_comments'];
					$datascript_text 				= $fresults['script_text'];
					$dataactive 					= $fresults['active'];
					$datauser_group 				= $fresults['user_group'];
				}
				
				$data_update 						= array(
					'script_name' 						=> ($script_name == null) ? $datascript_name : $script_name,
					'script_comments' 					=> ($script_comments == null) ? $datascript_comments : $script_comments,
					'script_text' 						=> ($script_text == null) ? ($datascript_text): $script_text,
					'active' 							=> ($active == null) ? $dataactive : $active,
					'user_group' 						=> ($user_group == null) ? $datauser_group : $user_group
				);
				
				$astDB->where('script_id', $script_id);
				$update 							= $astDB->update('vicidial_scripts', $data_update);
			
				if ($update) {
					$apiresults 					= array(
						"result" 						=> "success"
					);

					$log_id 						= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Script ID: $script_id", $log_group, $astDB->getLastQuery());
				} else {
					$apiresults 					= array(
						"result" 						=> "Error: Try updating Script Again"
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
