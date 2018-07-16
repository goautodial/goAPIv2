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
	
	$log_user 								= $session_user;
	$log_group 								= go_get_groupid($session_user, $astDB); 
	//$log_ip 								= $astDB->escape($_REQUEST['log_ip']);	
    
	if (empty($log_user) || is_null($log_user)){
		$apiresults 						= array(
			"result" 							=> "Error: Session User Not Defined."
		);
	} else {	
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere('user_group', "---ALL---");
		} else {
			if($log_group !== "ADMIN"){
				$astDB->where('user_group', $log_group);
				$astDB->orWhere('user_group', "---ALL---");
			}
		}
		
		//$campaigns 						= go_getall_allowed_campaigns($log_group, $astDB);
		//$campaignsArr					= explode(' ', $campaigns);
		
		/*if (is_array($campaignsArr)) {
			if (!preg_match("/ALLCAMPAIGNS/",  $campaigns)) {
				$astDB->where('campaign_id', $campaignsArr, 'IN');
			}*/

		$cols 								= array(
			"campaign_id",
			"campaign_name",
			"dial_method",
			"active"
		);
		
		$astDB->orderBy('campaign_id', 'asc');
		$result 							= $astDB->get('vicidial_campaigns', NULL, $cols);

		if ($astDB->count > 0) {
			foreach($result as $fresults){
				$dataCampID[] 				= $fresults['campaign_id'];
				$dataCampName[] 			= $fresults['campaign_name'];// .$fresults['dial_method'].$fresults['active'];
				$dataDialMethod[] 			= $fresults['dial_method'];
				$dataActive[] 				= $fresults['active'];
			}

			$apiresults 					= array(
				"result" 						=> "success", 
				"campaign_id" 					=> $dataCampID, 
				"campaign_name" 				=> $dataCampName, 
				"dial_method" 					=> $dataDialMethod, 
				"active" 						=> $dataActive
			);			
		} else {
			$apiresults 					= array(
				"result" 						=> "success"
			);					
		}
		/*} else {
			$err_msg 					= error_handle("40001");
			$apiresults 				= array(
				"code" 						=> "40001", 
				"result" 					=> $err_msg
			);
		}*/
	}
	
?>
