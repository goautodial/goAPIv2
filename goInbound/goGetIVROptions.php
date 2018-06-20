<?php
/**
 * @file        goGetIVROptionsInfo.php
 * @brief       API to get defined IVR Options
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian V. Samatra  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
    
    //POST or GET Variables
    $menu_id = $_REQUEST['menu_id'];
    
	if(empty($menu_id)) { 
		$apiresults = array("result" => "Error: Set a value for Menu ID."); 
	} else {
 
    	$groupId = go_get_groupid($goUser, $astDB);
    
		if (checkIfTenant($groupId, $goDB) {
			$astDB->where("user_group", $groupId);
		}

		$astDB->where("menu_id", $menu_id);
		$selectQuery = $astDB->get("vicidial_call_menu_options");
   		//$query = "SELECT *	FROM vicidial_call_menu_options WHERE $ul;";
		
		foreach($selectQuery as $fresults){
			$id[] = $fresults["menu_id"];
			$option_value[] = $fresults["option_value"];
			$option_description[] = $fresults["option_description"];
			$option_route[] = $fresults["option_route"];
			$option_route_value[] = $fresults["option_route_value"];
			$option_route_value_context[] = $fresults["option_route_value_context"];
		}
		
		$apiresults = array( "result" => "success", "menu_id" => $id, "option_value" => $option_value, "option_description" => $option_description, "option_route" => $option_route, "option_route_value" => $option_route_value, "option_route_value_context" => $option_route_value_context, "query" => $query);	
	}
?>
