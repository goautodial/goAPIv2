<?php
/**
 * @file 		goGetAllAreacodes.php
 * @brief 		API to get all areacodes
 * @copyright 	Copyright (c) 2019 GOautodial Inc.
 * @author		Thom Bernarth Patacsil 
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
	  
	// Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
			$astDB->where('user_group', $log_group);
			$allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper($log_group) !== 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					} else {
						$allowed_campaigns = $allowed_camps['allowed_campaigns'];
						if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
							$allowed_campaigns = explode(" ", trim($allowed_campaigns));
							$astDB->orWhere('campaign_id', $allowed_campaigns, 'in');
						}
					}
				}					
			}
			
			/*$dataCampID								= "";
			$dataCampName								= "";
			$dataAreacode								= "";
			$dataOutboundCID							= "";
			$dataActive									= "";
			$dataDescription							= "";	
			$dataCallCountToday							= "";*/
			$cols 										= array(
				"vcid.campaign_id",
				"vc.campaign_name",
				"vcid.areacode",
				"vcid.outbound_cid",
				"vcid.active",
				"vcid.cid_description",
				"vcid.call_count_today"
			);
			
			$astDB->orderBy('campaign_id', 'desc');
			$astDB->join('vicidial_campaigns vc', 'vcid.campaign_id=vc.campaign_id', 'LEFT');
			$result	= $astDB->get('vicidial_campaign_cid_areacodes vcid', NULL, $cols);		
			
			if ($astDB->count > 0) {
				foreach ($result as $fresults){
					$dataCampID[] 						= $fresults['campaign_id'];
					$dataCampName[]						= $fresults['campaign_name'];
					$dataAreacode[] 					= $fresults['areacode'];
					$dataOutboundCID[] 					= $fresults['outbound_cid'];
					$dataActive[] 						= $fresults['active'];
					$dataDescription[]					= $fresults['cid_description'];
					$dataCallCountToday					= $fresults['call_count_today'];
				}				
			
				$apiresults 							= array(
					"result" 								=> "success", 
					"campaign_id" 							=> $dataCampID,
					"campaign_name"							=> $dataCampName,
					"areacode" 								=> $dataAreacode, 
					"outbound_cid" 							=> $dataOutboundCID, 
					"active" 								=> $dataActive,
					"description"							=> $dataDescription,
					"call_count_today"						=> $dataCallCountToday
				);
			} else {
				$apiresults                             = array(
					"result"								=> "No data available in table"
				);
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}
	
?>
