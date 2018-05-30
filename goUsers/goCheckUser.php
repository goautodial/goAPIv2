<?php
/**
 * @file    goCheckUser.php
 * @brief     API to check for existing user data
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim H. Abenoja <alex@goautodial.com>
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
    
    @include_once ("goAPI.php");
 
    // POST or GET Variables
      $user = explode(",",$astDB->escape($_REQUEST['user']));
      $phone_login = $astDB->escape($_REQUEST['phone_login']);

      // Phone Login Check optional when not null
      if($phone_login != NULL){
        $col = array("extension");
        $astDB->where("extension", $phone_login);
        $astDB->getOne("phones", null, $col);
        //  $queryPhoneCheck = "SELECT extension FROM phones WHERE extension = '$phone_login';";
        $countCheckResult2 = $astDB->count;      
          if($countCheckResult2 > 0) {
            $apiresults = array("result" => "success");
          }else{
            $apiresults = array("result" => "fail", "phone_login" => "There is no phone that matches your input.");
          }
      }
        
      // User Duplicate Check
      if($user != NULL){
        $col2 = array("user");
        $astDB->where("user", $user);
        $astDB->get("vicidial_users", null, $col2);
        //"SELECT user FROM vicidial_users WHERE user = '$user';";
        $countCheckResult1 = $astDB->count;  
          if($countCheckResult1 > 0) {
            $validate1 = $validate1 + 1;
            $apiresults = array("result" => "user", "user" => "There are 1 or more users with that User ID.");
          }else{
            $apiresults = array("result" => "success");
          }
      }
?>
