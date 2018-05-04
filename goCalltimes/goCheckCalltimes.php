<?php
/**
 * @file        goCheckCalltimes.php
 * @brief       API to check existing Call Time
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
    
    include_once ("../goFunctions.php");
 
    /// POST or GET Variables
    $call_time_id = $_REQUEST['call_time_id'];

    if(empty($call_time_id)){
      $apiresults = array("result" => "Error: Missing Required Parameter");
    }else{
      $astDB->where("call_time_id", $call_time_id);
      $astDB->getOne("vicidial_call_times", "call_time_id");
      //$queryCheck = "SELECT call_time_id from vicidial_call_times where call_time_id='$call_time_id';";
      $countCheck = $astDB->count;
      
      if($countCheck <= 0){
          $apiresults = array("result" => "success");
      }else{
          $apiresults = array("result" => "Error: Call Time ID already exists!");
      }
    }
    
?>