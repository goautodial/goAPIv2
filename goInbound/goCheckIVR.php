<?php
/**
 * @file        goCheckIVR.php
 * @brief       API to check for existing IVR Menu ID
 * @copyright   Copyright (C) GOautodial Inc.
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
 
    // POST or GET Variables
    $menu_id = $astDB->escape($_REQUEST['menu_id']);
    
    $astDB->where("menu_id", $menu_id);
    $row = $astDB->getValue("vicidial_call_menu", "count(*)");
    //$stmtCheck = "SELECT menu_id from vicidial_call_menu where menu_id='$menu_id';";
    
    if ($row > 0) {
        $apiresults = array("result" => "Error: CALL MENU NOT ADDED - there is already a CALL MENU in the system with this ID");
    }else{
        $apiresults = array("result" => "success");
    }
?>