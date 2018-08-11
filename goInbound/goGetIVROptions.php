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
    
	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB); 
	$log_ip 										= $astDB->escape($_REQUEST['log_ip']); 
	
    //POST or GET Variables
    $menu_id 										= $astDB->escape($_REQUEST['menu_id']);
    
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($menu_id) || is_null($menu_id)) {
        $apiresults 								= array(
			"result" 									=> "Error: Set a value for Menu ID."
		);
	} else {
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

		
			
	}
?>
