<?php
 /**
 * @file 		goCheckMOH.php
 * @brief 		API for Checking Music On Hold
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Alexander Abenoja  <alex@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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

    ### POST or GET Variables
    $moh_id = $astDB->escape($_REQUEST['moh_id']);
    
    //$queryCheck = "SELECT moh_id from vicidial_music_on_hold where moh_id='".$moh_id."';";
    $astDB->where('moh_id', $moh_id);
    $sqlCheck = $astDB->get('vicidial_music_on_hold');
    $countCheck = $astDB->getRowCount();
    
    if($countCheck <= 0) {
        $apiresults = array("result" => "success");
    } else {
        $apiresults = array("result" => "Error: Add failed, Music On Hold already already exist!");
    }
?>