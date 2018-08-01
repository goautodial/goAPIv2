<?php
 /**
 * @file 		goGetAllScripts.php
 * @brief 		API to get all scripts
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho 
 * @author     	Alexander Jim Abenoja
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
	
	if ( empty($log_user) || is_null($log_user) ) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
	} else {
		if ( checkIfTenant($log_group, $goDB) ) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere('user_group', "---ALL---");
		} else {
			if ($log_group !== "ADMIN"){
				$astDB->where('user_group', $log_group);
				$astDB->orWhere('user_group', "---ALL---");
			}
		}
		
		// getting script count
		$astDB->orderBy('script_id', 'desc');
		$resultGetScript						= $astDB->getOne('vicidial_scripts', 'script_id');
		
		// condition
		if ($resultGetScript) {
			if ( preg_match("/^script/i", $resultGetScript['script_id']) ) {
				$get_last_count 				= str_replace("script", "", $resultGetScript['script_id']);
				$last_pl[] 						= intval($get_last_count);
			} else {
				$get_last_count 				= $resultGetScript['script_id'];
				$last_pl[] 						= intval($get_last_count);
			}

			// return data
			$script_num 						= max($last_pl);
			$script_num 						= $script_num + 1;
			
			if ($script_num < 100) {
				if ($script_num < 10) {
					$script_num = "00".$script_num;
				} else {
					$script_num = "0".$script_num;
				}
			}
			
			if ($log_group != "ADMIN") {
				$script_num 					= $script_num;
			}else{
				$script_num 					= "script".$script_num;
			}
		} else {
			// return data
			$script_num 						= "script001";
		}
			
		if ( checkIfTenant($log_group, $goDB) ) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere('user_group', "---ALL---");
		} else {
			if ($log_group !== "ADMIN"){
				$astDB->where('user_group', $log_group);
				$astDB->orWhere('user_group', "---ALL---");
			}
		}
		
		$scripts 								= $astDB->get('vicidial_scripts');
		
		if ($astDB->count > 0) {
			foreach ($scripts as $script) {
				$dataScriptID[] 				= $script['script_id'];
				$dataScriptName[] 				= $script['script_name'];
				$dataActive[] 					= $script['active'];
				$dataUserGroup[] 				= $script['user_group'];
			}		
		} 
		
		$apiresults 							= array(
			"result" 								=> "success",
			"script_id" 							=> $dataScriptID,
			"script_name" 							=> $dataScriptName,
			"active" 								=> $dataActive,
			"user_group" 							=> $dataUserGroup,
			"script_count" 							=> $script_num
		);
	}

?>
