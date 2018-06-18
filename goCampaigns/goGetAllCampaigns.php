<?php
/**
 * @file 		goGetAllCampaigns.php
 * @brief 		API to get all campaigns
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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
	include_once ("goAPI.php");
	$agent = get_settings('user', $astDB, $goUser);

	if(!empty($agent)){
		if(!empty($agent->user_group)){
			// Getting Allowed Campigns
			$astDB->where('user_group', $agent->user_group);
			$allowedCamp = $astDB->getOne('vicidial_user_groups', "TRIM(REPLACE(allowed_campaigns,' -','')) AS allowed_campaigns");

			if (checkIfTenant($agent->user_group, $astDB)) {
				//do nothing
			} else {
				if($agent->user_group !== "ADMIN"){
					$astDB->where('user_group', $agent->user_group);
				}
			}
			
			if (isset($user_group) && strlen($user_group) > 0) {
				if (!preg_match("/ALLCAMPAIGNS/",  $allowedCamp['allowed_campaigns'])) {
					$cl = explode(' ', $query['allowed_campaigns']);
    				$astDB->where('campaign_id', $cl, 'in');
				}
			}

			$astDB->orderBy('campaign_id');
			$result = $astDB->get('vicidial_campaigns', null, 'campaign_id,campaign_name,dial_method,active');

			foreach($result as $fresults){
				$dataCampID[] = $fresults['campaign_id'];
				$dataCampName[] = $fresults['campaign_name'];// .$fresults['dial_method'].$fresults['active'];
				$dataDialMethod[] = $fresults['dial_method'];
				$dataActive[] = $fresults['active'];
			}

			$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "campaign_name" => $dataCampName, "dial_method" => $dataDialMethod, "active" => $dataActive);
			
		}else{
			$err_msg = error_handle("40001");
			$apiresults = array("code" => "40001", "result" => $err_msg);
		}
	}else{
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	}
	
	
?>
