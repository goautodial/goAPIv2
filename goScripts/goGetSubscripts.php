<?php 
 /**
 * @file 		goGetSubscripts.php
 * @brief 		API to get subscripts
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Noel Umandap  <noel@goautodial.com>
 * @author     	Alexander Jim Abenoja  <alex@goautodial.com>
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
	//$log_ip 									= $astDB->escape($_REQUEST["log_ip"]);
	
	if ( empty($log_user) || is_null($log_user) ) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
    } else {
		if ( checkIfTenant($log_group, $goDB) ) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere("user_group", "---ALL---");
		} else {
			if ($log_group !== "ADMIN"){
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			}
		}	
			
		$astDB->where('subscript' , 1);
		$subScripts 							= $astDB->get('vicidial_scripts', null, 'script_id, script_name, script_text');
		
		foreach ($subScripts as $fresults) {
			$script_id[] 						= $fresults['script_id'];
			$script_name[] 						= $fresults['script_name'];
			$script_text[] 						= $fresults['script_text'];
		}

		$apiresults 							= array(
			"result" 								=> "success", 
			"script_id" 							=> $script_id, 
			"script_name" 							=> $script_name, 
			"script_text" 							=> $script_text
		);
	}
	
?>
