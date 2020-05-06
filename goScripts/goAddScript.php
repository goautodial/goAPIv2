<?php
 /**
 * @file        goAddScript.php
 * @brief       API for Adding Scripts
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
 	
	$script_id 											= $astDB->escape($_REQUEST["script_id"]); 
	$script_type 										= $astDB->escape($_REQUEST["script_type"]);
    $script_name 										= $astDB->escape($_REQUEST["script_name"]); 
    $script_comments 									= $astDB->escape($_REQUEST["script_comments"]);
    $script_text 										= $astDB->escape($_REQUEST["script_text"]);
    $script_text 										= str_replace('\n','',$script_text);
    $user_group 										= $astDB->escape($_REQUEST["user_group"]);
    $active 											= $astDB->escape($_REQUEST["active"]);
    
    ### Default values
    $defActive 											= array("Y","N");    

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
	} elseif ( empty($script_id) || is_null($script_id) ) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for Script ID."
		);
	} elseif ( preg_match("/[\"^£$%&*()}{@#~?><>,|=_+¬-]/",$script_name) && $script_name != null ) {
		$apiresults 									= array(
			"result" 										=> "Error: Special characters found in script name"
		);
	} elseif ( !in_array($active,$defActive) && $active != null ) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for active is Y or N only."
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
			if ( empty($user_group) ) {
				$user_group								= "---ALL---";
			}    
			// check if script ID exists
			$astDB->where("script_id", $script_id);        
			$results 									= $astDB->getOne("vicidial_scripts", "script_id");
					
			if (!$results) {
				if ($script_type == "default") {
					$subscript 							= 0;
				} else {
					$subscript 							= 1;
				}		
				
				$queryColumn 							= "SHOW COLUMNS FROM `vicidial_scripts` LIKE 'subscript'";
				$resultColumn 							= $astDB->rawQuery($queryColumn);	

				if ($resultColumn) {
					$data_script 						= array(
						"subscript" 						=> $subscript
					);
				}
				
				$data_script 							= array(
					"script_id" 							=> $script_id, 
					"script_comments" 						=> $script_comments, 
					"script_name" 							=> $script_name, 
					"active" 								=> $active, 
					"user_group" 							=> $user_group, 
					"script_text" 							=> $script_text
				);
				
				$insertScript 							= $astDB->insert("vicidial_scripts", $data_script);
					
				if (!$insertScript) {
					$apiresults 						= array(
						"result" 							=> "Error: Add failed, check your details"
					);
				} else {
					$log_id 							= log_action($goDB, "ADD", $log_user, $log_ip, "Added New Script: $script_id", $log_group, $astDB->getLastQuery());
					$apiresults 						= array(
						"result" 							=> "success"
					);
				}
			} else {
				$apiresults	 							= array(
					"result" 								=> "Error: Add failed, Script already already exist!"
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
