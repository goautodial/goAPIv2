<?php
/**
 * @file 		goCheckPhoneLogin.php
 * @brief 		API to check if phone already exists
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Jim H. Abenoja <alex@goautodial.com>
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

include_once ("goAPI.php");

// POST or GET Variables
$extension = $_REQUEST['extension'];

if(!empty($extension) && !empty($session_user)){
  $astDB->where("extension", $extension);
  $astDB->getOne("phones", "extension");
  //$query = "SELECT extension FROM phones WHERE extension='$extension';";

  if($astDB->count < 1) {
    $apiresults = array("result" => "success");
  }else{
    $apiresults = array("result" => "Error: Phone already exist.");
  }  
}else{
  $apiresults = array("result" => "Error: Missing parameters.");
}

?>