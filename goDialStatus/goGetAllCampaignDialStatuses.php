<?php
 /**
 * @file 		goGetAllCampaignDialStatuses.php
 * @brief 		API for Dial Status
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Noel Umandap  <noel@goautodial.com>
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

    //$campaign_id = $astDB->escape($_REQUEST['campaign_id']);
    
    //$query = "SELECT status,status_name
    //        FROM vicidial_campaign_statuses 
    //        WHERE campaign_id='$campaign_id'
    //        ORDER BY status";
	$astDB->where('campaign_id', $campaign_id);
	$astDB->orderBy('status', 'desc');
   	$rsltv = $astDB->get('vicidial_campaign_statuses', null, 'status,status_name');
    
    foreach ($rsltv as $fresults){
		$dataStatus[] = $fresults['status'];
       	$dataStatusName[] = $fresults['status_name'];
   		$apiresults = array(
			"result" => "success",
			"status" => $dataStatus,
			"status_name" => $dataStatusName,
			"test" => $query
		);
	}
?>
