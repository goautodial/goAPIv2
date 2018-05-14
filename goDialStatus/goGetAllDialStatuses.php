<?php
 /**
 * @file 		goGetAllDialStatuses.php
 * @brief 		API for Dial Status
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Noel Umandap  <noel@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 * @author     	Alexander Jim Abenoja  <alex@goautodial.com>
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

    
	$hotkeys_only = $astDB->escape($_REQUEST['hotkeys_only']);
	$campaign_id = $astDB->escape($_REQUEST['campaign_id']);
	
	$selectable = '';
	if ($hotkeys_only === "1") {
		$selectable = "WHERE selectable='Y'";
	}
	
	if (strlen($selectable) > 0 && strlen($campaign_id) > 0) {
		$query = "SELECT status,status_name
					FROM vicidial_campaign_statuses
					$selectable
					AND campaign_id='$campaign_id'
					ORDER BY status";
		$rsltv = $astDB->rawQuery($query);
		
		foreach ($rsltv as $fresults){
			$dataStatus[] = $fresults['status'];
			$dataStatusName[] = $fresults['status_name'];
		}
	}
	
    $query = "SELECT status,status_name
				FROM vicidial_statuses
				$selectable
				ORDER BY status";
   	$rsltv = $astDB->rawQuery($query);
    
    foreach ($rsltv as $fresults){
		$dataStatus[] = $fresults['status'];
       	$dataStatusName[] = $fresults['status_name'];
	}
	
	$apiresults = array(
		"result" => "success",
		"status" => $dataStatus,
		"status_name" => $dataStatusName,
		"test" => $query
	);
?>