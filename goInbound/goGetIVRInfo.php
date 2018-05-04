<?php
/**
 * @file        goGetIVRInfo.php
 * @brief       API to get specific IVR Details
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James Milo  <jericojames@goautodial.com>
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

    include_once ("../goFunctions.php");
    
    // POST or GET Variables
    $menu_id = $_REQUEST['menu_id'];
    
	if(empty($menu_id)) { 
		$apiresults = array("result" => "Error: Set a value for Menu ID."); 
	} else {
    	$groupId = go_get_groupid($goUser, $astDB);
    
		if (checkIfTenant($groupId, $goDB)) {
			$astDB->where("user_group", $groupId);
    	}

    	$astDB->where("menu_id", $menu_id);
    	$fresults = $astDB->where("menu_id", "defaultlog", "!=");
   		//$query = "SELECT *	FROM vicidial_call_menu WHERE menu_id != 'defaultlog' $ul order by menu_id LIMIT 1;";
		
		if($fresults) {
			$apiresults = array( "result" => "success", "data" => $fresults);
		} else {
			$apiresults = array("result" => "Error: IVR Menu doesn't exist.");
		}
	}
?>
