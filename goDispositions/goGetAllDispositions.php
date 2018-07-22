<?php
 /**
 * @file 		goGetAllDispositions.php
 * @brief 		API for Dispositions
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
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

	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB); 
	$campaigns 										= allowed_campaigns($log_group, $goDB, $astDB);
		
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($campaigns) || is_null($campaigns)) {
		$err_msg 									= error_handle("40001");
        $apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
    } else {
		if (is_array($campaigns)) {
			$campaignsArr				= array();
			foreach ($campaigns["campaign_id"] as $key => $value) {
				array_push($campaignsArr, $value);
			}
			
			$cols 						= array(
				"status", 
				"status_name", 
				"campaign_id"
			);
			
			$astDB->where("campaign_id", $campaignsArr, "IN");
			$astDB->orderBy("campaign_id", "desc");			
			$result 					= $astDB->get("vicidial_campaign_statuses", NULL, $cols);	
			
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
			$err_msg 					= error_handle("10108", "status. No campaigns available");
			$apiresults					= array(
				"code" 						=> "10108", 
				"result" 					=> $err_msg
			);
		}    
    }
	
	
?>

