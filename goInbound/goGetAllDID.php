<?php
 /**
 * @file        goGetAllDID.php
 * @brief       API to get all DID Details
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
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 1000; } else { $limit = $limit; }

    $groupId = go_get_groupid($goUser, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
        $astDB->where("user_group", $groupId);
        //$ul = "WHERE user_group='$user_group'"; 
    }

    $cols = Array("did_id", "did_pattern", "did_description", "did_active", "did_route");
    $selectQuery = $astDB->get("vicidial_inbound_dids", $limit, $cols);
    //$query = "SELECT did_id,did_pattern,did_description,did_active,did_route from vicidial_inbound_dids $ul order by did_pattern LIMIT $limit";
    
	foreach($selectQuery as $fresults){
    	$dataDidID[] = $fresults['did_id'];
    	$dataDidPattern[] =  $fresults['did_pattern'];
    	$dataDidDescription[] =  $fresults['did_description'];
    	$dataActive[] =  $fresults['did_active'];
    	$dataDidRoute[] =  $fresults['did_route'];
	}

    $apiresults = array( "result" => "success","did_id" => $dataDidID,  "did_pattern" => $dataDidPattern, "did_description" => $dataDidDescription, "active" => $dataActive, "did_route" => $dataDidRoute);
?>
