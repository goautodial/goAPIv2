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

    include_once( "goAPI.php" );

	$log_user 										= $session_user;
	$log_group 										= go_get_groupid( $session_user, $astDB ); 
	$log_ip 										= $astDB->escape( $_REQUEST['log_ip'] );
    $allowed_campaigns								= allowed_campaigns($log_group, $goDB, $astDB);
    
    // POST or GET Variables
	$campaign_ids 									= $_REQUEST["campaign_id"];
	$action 										= $astDB->escape($_REQUEST["action"]);
	
	
    // Check campaign_id if its null or empty
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($campaign_ids) || is_null($campaign_ids)) {
		$err_msg 									= error_handle("40001");
        $apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
    } elseif ($action == "delete_selected") {
		if ( !array_diff( $campaign_id, $allowed_campaigns ) ) {

		//foreach ($campaign_ids as $campaignid) {
			//$campaign_id = $campaignid;
			
			$astDB->where("campaign_id", $campaign_ids, "IN");
			$astDB->getOne("vicidial_campaigns", "campaign_id");
			
			if ($astDB->count > 0) {					
				$astDB->where("campaign_id", $campaign_ids, "IN");
				$astDB->delete("vicidial_campaigns");					
				$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());
				
				$astDB->where("campaign_id", $campaign_ids, "IN");
				$astDB->delete("vicidial_campaign_statuses");					
				$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Dispositions in Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());
				
				$astDB->where("campaign_id", $campaign_ids, "IN");
				$goDB->delete("vicidial_lead_recycle");					
				$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Lead Recycles in Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery());					
			
				$apiresults 						= array(
					"result" 							=> "success"
				);
			} else {
				$err_msg 							= error_handle("10109");
				$apiresults 						= array(
					"code" 								=> "10109", 
					"result" 							=> "Error: Campaign doesn't exist."
				);
			}
		} else {
			$err_msg 									= error_handle("10001", "Insufficient permision");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);			
		}
	}
	

?>
