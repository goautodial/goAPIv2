<?php
/**
 * @file        goDeleteHotkey.php
 * @brief       API to delete a specific hotkey/s
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <noelumandap@goautodial.com>
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
  $campaign_id = $_REQUEST['campaign_id'];
  $hotkeys = explode(",", $_REQUEST['hotkey']);
  
  $ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
  $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
  $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
  
  $result = array();
  foreach ($hotkeys as $hotkey){
      $astDB->where('campaign_id', $campaign_id);
      $astDB->where('hotkey', $hotkey);
      $queryDelete = $astDB->delete('vicidial_campaign_hotkeys');
      $deleteQuery = $astDB->getLastQuery();
              
      $rsltv = mysqli_query($link, $query);
      
      if($rsltv){
          array_push($result, "ok");
          $log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Hotkey $hotkey from Campaign $campaign_id", $log_group, $deleteQuery);
      }else{
          array_push($result, "error");
      }
  }
  
  if(in_array("error", $result)) {
    $apiresults = array("result" => "Error: Failed to delete campaign hotkey.");
  } else {
    $apiresults = array("result" => "success");
  }
?>