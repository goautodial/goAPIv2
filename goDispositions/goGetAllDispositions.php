<?php
 /**
 * @file 		goGetAllDispositions.php
 * @brief 		API for Dispositions
 * @copyright 	Copyright (c) GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Jeremiah Sebastian Samatra
 * @author     	Chris Lomuntad
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

	$log_user 					= $session_user;
	$log_group 					= go_get_groupid($session_user, $astDB); 
	$customRequest				= $astDB->escape($_REQUEST['custom_request']);
	
	$campaigns 					= go_getall_allowed_campaigns($log_group, $astDB);
	$campaignsArr				= explode(' ', $campaigns);
		
	if (empty($session_user)) {
		$err_msg 				= error_handle("40001");
        $apiresults 			= array("code" => "40001","result" => $err_msg);
	} elseif (!empty($customRequest) && $customRequest === "custom") {
		$query = "(
			SELECT status,status_name FROM vicidial_campaign_statuses
		) UNION (
			SELECT status,status_name FROM vicidial_statuses 
				ORDER BY status
		)";
		$fresults = $astDB->rawQuery($query);
		
		foreach ($fresults as $fresult) {
			$dataStat[] 		= $fresult['status'];			
			$dataStatName[] 	= $fresult['status_name'];
		}	
		
		$apiresults 			= array(
			"result" 				=> "success", 
			"status" 				=> $dataStat, 
			"status_name" 			=> $dataStatName
		);	

	} elseif (!empty($customRequest) && $customRequest === "campaign") {				
		if (is_array($campaignsArr)) {
			if (!preg_match("/ALLCAMPAIGNS/",  $campaigns)) {
				$astDB->where("campaign_id", $campaignsArr, "IN");
			}
			
			$cols 					= array(
				"status", 
				"status_name", 
				"campaign_id"
			);
			
			$astDB->orderBy("campaign_id");
			$result 				= $astDB->get("vicidial_campaign_statuses", NULL, $cols);	
			
			if ($astDB->count > 0) {
				foreach ($result as $fresults) {
					$dataStat[] 		= $fresults["status"];			
					$dataStatName[] 	= $fresults["status_name"];
					$dataCampID[] 		= $fresults["campaign_id"];
				}			
				
				$apiresults 			= array(
					"result" 				=> "success", 
					"campaign_id" 			=> $dataCampID, 
					"status_name" 			=> $dataStatName, 
					"status" 				=> $dataStat
				);			
			}	 		
		} else {
			$err_msg 				= error_handle("10108", "status. No campaigns available");
			$apiresults				= array(
				"code" 					=> "10108", 
				"result" 				=> $err_msg
			);
		}
	} elseif (!empty($customRequest) && $customRequest !== "custom") {
		$err_msg 					= error_handle("41006", "custom_request");
        $apiresults 				= array(
			"code" 						=> "41006",
			"result" 					=> $err_msg
		);
	}
	
	
?>

