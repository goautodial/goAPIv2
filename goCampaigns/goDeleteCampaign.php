<?php
/**
 * @file 		goDeleteCampaign.php
 * @brief 		API to delete campaign
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Alexander Jim H. Abenoja
 * @author		Jerico James Milo
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

    @include_once ("goAPI.php");
    
    // POST or GET Variables
	$campaign_ids = $_REQUEST['campaign_id'];
	$action = $astDB->escape($_REQUEST['action']);
	
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	$ip_address = $astDB->escape($_REQUEST['log_ip']);	
	
    // Check campaign_id if its null or empty
	if (empty($campaign_ids) || empty($session_user)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg); 
		//$apiresults = array("result" => "Error: Set a value for Campaign ID.");
	} elseif ($action == "delete_selected") {
		$error_count = 0;
		foreach ($campaign_ids as $campaignid) {
			$campaign_id = $campaignid;
			
			$astDB->where("campaign_id", $campaign_id);
			$astDB->getOne("vicidial_campaigns");
			
			if ($astDB->count > 0) {					
				$astDB->where("campaign_id", $campaign_id);
				$astDB->delete("vicidial_campaigns");					
				$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());
				
				$astDB->where("campaign_id", $campaign_id);
				$q_deletePhone = $astDB->delete("vicidial_campaigns_statuses");					
				$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Dispositions in Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());
				
				$astDB->where("campaign_id", $campaign_id);
				$qgo_deleteUser = $goDB->delete("vicidial_lead_recycle");					
				$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Lead Recycles in Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());					
			
			} else {
				$error_count = 1;
			}
			
			if ($error_count == 0) { 
				$apiresults = array("result" => "success"); 
			}		
			if ($error_count == 1) {
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010", "result" => $err_msg, "data" => $campaign_ids);
				//$apiresults = array("}result" => "Error: Delete Failed");
			}
		}
	}
	

?>
