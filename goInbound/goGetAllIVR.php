<?php
 /**
 * @file        goGetAllIVR.php
 * @brief       API to get all DID Details
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
    
	//$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	
   if (isset($_REQUEST['limit'])) {
		$limit = $astDB->escape($_REQUEST['limit']);
	} else { $limit = 50; }
    //if($limit < 1){ $limit = 1000; } else { $limit = $limit; }

    
    if (checkIfTenant($log_group, $goDB)) {
        $astDB->where("user_group", $log_group);
        //$ul = "AND user_group='$user_group'";
    }

    $cols = Array("menu_id", "menu_name", "menu_prompt", "menu_timeout");
    $astDB->where("menu_id", "defaultlog", "!=");
    $selectQuery = $astDB->get("vicidial_call_menu", $limit, $cols);
    //$query = "SELECT menu_id,menu_name,menu_prompt,menu_timeout from vicidial_call_menu WHERE menu_id!='defaultlog' $ul order by menu_id LIMIT $limit";
    
	foreach($selectQuery as $fresults){
    	$dataMenuId[] =  $fresults['menu_id'];
    	$dataMenuName[] =  $fresults['menu_name'];
    	$dataMenuPrompt[] =  $fresults['menu_prompt'];
    	$dataMenuTimeout[] =  $fresults['menu_timeout'];
	}

    $apiresults = array( "result" => "success", "menu_id" => $dataMenuId, "menu_name" => $dataMenuName, "menu_prompt" => $dataMenuPrompt, "menu_timeout" => $dataMenuTimeout);
?>
