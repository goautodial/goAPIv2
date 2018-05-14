<?php
/**
 * @file 		goDeleteCampaign.php
 * @brief 		API to delete campaign
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jerico James Milo  <jericojames@goautodial.com>
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
    // POST or GET Variables
	$agent = get_settings('user', $astDB, $goUser);

	$campaign_id = $astDB->escape($_REQUEST['campaign_id']);
	$action = strtolower($astDB->escape($_REQUEST['action']));
    $goUser = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
	$log_user = $_REQUEST['log_user'];
	$log_group = $_REQUEST['log_group'];
	
    // Check campaign_id if its null or empty
	if(empty($campaign_id) || empty($session_user)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg); 
		//$apiresults = array("result" => "Error: Set a value for Campaign ID."); 
	} else {
		$groupId = go_get_groupid($session_user);
		if(!empty($action) && $action == strtolower("delete_selected")){
			$exploded = explode(",",$campaign_id);
			$error_count = 0;
			for($i=0;$i < count($exploded);$i++){
				$astDB->where('campaign_id', $exploded[$i]);
				$deleteResult  = $astDB->delete('vicidial_campaigns');
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Campaign ID: $exploded[$i]", $log_group, $astDB->getLastQuery());

				$astDB->where('campaign_id', $exploded[$i]);
				$deleteResult2  = $astDB->delete('vicidial_campaigns_statuses');
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Dispositions in Campaign ID: $exploded[$i]", $log_group, $astDB->getLastQuery());
				
				$astDB->where('campaign_id', $exploded[$i]);
				$deleteResult3  = $astDB->delete('vicidial_lead_recycle');
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Lead Recycles in Campaign ID: $exploded[$i]", $log_group, $astDB->getLastQuery());

				if (!checkIfTenant($groupId)) {
					$astDB->where('campaign_id', $campaign_id);
				} else { 
					$astDB->where('campaign_id', $campaign_id);
					$astDB->where('user_group', $agent->user_group);  
				}
				$rsltvdel = $astDB->getOne('vicidial_campaigns', 'campaign_id');
				
				if($rsltvdel) {
					$error_count = $error_count + 1;
				}			
			}
				
			if($error_count > 0) {
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010", "result" => $err_msg); 
				//$apiresults = array("result" => "Error: Delete Failed");
			} else {
				$apiresults = array("result" => "success"); 
			}
		}else{
			if (!checkIfTenant($groupId)) {
				$astDB->where('campaign_id', $campaign_id);
			} else { 
				$astDB->where('campaign_id', $campaign_id);
				$astDB->where('user_group', $agent->user_group);  
			}
			$first_check = $astDB->getOne('vicidial_campaigns', 'campaign_id');
			
			if($first_check) {
				$astDB->where('campaign_id', $campaign_id);
				$deleteResult  = $astDB->delete('vicidial_campaigns');
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());
				
				$astDB->where('campaign_id', $campaign_id);
				$deleteResult2  = $astDB->delete('vicidial_campaigns_statuses');
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Dispositions in Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());
				
				$astDB->where('campaign_id', $campaign_id);
				$deleteResult3  = $astDB->delete('vicidial_lead_recycle');
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Lead Recycles in Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());
				
				$apiresults = array("result" => "success");
			} else {
				$err_msg = error_handle("41004", "campaign. Doesn't exist");
				$apiresults = array("code" => "41004", "result" => $err_msg); 
				//$apiresults = array("result" => "Error: Campaign doesn't exist.");
			}
		}
		
	}//end
?>
