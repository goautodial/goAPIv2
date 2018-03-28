<?php
/**
 * @file    goCheckUserGroups.php
 * @brief     API to check for existing user group data
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Original Author <alex@goautodial.com>
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
**/
    
    include_once ("../goFunctions.php");
 
    // POST or GET Variables
    $user_group= $_REQUEST['user_group'];
    
    if(!isset($user_group) || is_null($user_group)){
      $apiresults = array("result" => "Error: Missing Required Parameters.");
    }else{
      $astDB->where("user_group", $user_group);
      $astDB->getOne("vicidial_user_groups", "user_group");
      //$query = "SELECT user_group FROM vicidial_user_groups WHERE user_group='$user_group';";
      $countResult = $astDB->count;
      
      if($countResult > 0) {
          $apiresults = array("result" => "Error: User Group already exist.");
      } else {
          $apiresults = array("result" => "success");
      }
    }
?>