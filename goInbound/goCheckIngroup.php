<?php
/**
 * @file        goCheckInbound.php
 * @brief       API to check for existing Inbound Group ID
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
  $group_id = $astDB->escape($_REQUEST['group_id']);
  
  $astDB->where("group_id", $group_id);
  $row = $astDB->getValue("vicidial_inbound_groups", "count(*)");
  //$stmtCheck = "SELECT group_id from vicidial_inbound_groups where group_id='$group_id';";
  
  if ($row > 0) {
    $apiresults = array("result" => "GROUP NOT ADDED - there is already a Inbound in the system with this ID\n");
  } else {
    $apiresults = array("result" => "success");
  }
      
?>