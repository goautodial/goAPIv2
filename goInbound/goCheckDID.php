<?php
/**
 * @file        goCheckDID.php
 * @brief       API to check for existing DID Pattern
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
  $did_pattern = $astDB->escape($_REQUEST['did_pattern']);

  $astDB->where("did_pattern", $did_pattern);
  $rowdf = $astDB->getValue("vicidial_inbound_dids", "count(*)");
  //$stmtdf = "SELECT did_pattern from vicidial_inbound_dids where did_pattern='$did_pattern';";
  
  if ($rowdf > 0) {
    $apiresults = array("result" => "<br>DID NOT ADDED - DID already exist.\n");
  } else {
    $apiresults = array("result" => "success");
  }
?>