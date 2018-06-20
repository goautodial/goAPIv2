<?php
 /**
 * @file        goGetDIDInfo.php
 * @brief       API to get specific DID Details
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
    
    // POST or GET Variables
    $did_id = $_REQUEST['did_id'];
    
	if(empty($did_id)) { 
		$apiresults = array("result" => "Error: Set a value for DID ID."); 
	} else {
    	$groupId = go_get_groupid($goUser, $astDB);
    
		if (checkIfTenant($groupId, $goDB)) {
            $astDB->where("user_group", $groupId);
		}

        $astDB->where("did_id", $did_id);
        $fresults = $astDB->getOne("vicidial_inbound_dids");
   		//$query = "SELECT * FROM vicidial_inbound_dids $ul order by did_pattern LIMIT 1;";
   		
		if($astDB->count > 0) {
			$apiresults = array( "result" => "success", "data" => $fresults);
		} else {
			$apiresults = array("result" => "Error: DID doesn't exist.");
		}
	}
?>
