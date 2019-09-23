<?php
 /**
 * @file        goDeleteFilter.php
 * @brief       API for Deleting Specific Filters
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Christopher P. Lomuntad
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
 
	$filter_id 											= $astDB->escape($_REQUEST["filter_id"]); 
	
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
	} elseif (empty($filter_id) || is_null($filter_id) ) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for Filter ID."
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
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					}
				}					
			}
			
			// check if script ID exists
			$astDB->where("lead_filter_id", $filter_id);        
			$astDB->getOne("vicidial_lead_filters", "lead_filter_id");
			
			if ($astDB->count > 0) {        
				$astDB->where("lead_filter_id", $filter_id);
				$getFilters 							= $astDB->getOne("vicidial_lead_filters", "lead_filter_id");

				if($getFilters) {
					$astDB->where("lead_filter_id", $filter_id);
					$astDB->delete("vicidial_lead_filters");

					$log_id 							= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted Filter ID: $filter_id", $log_group, $astDB->getLastQuery());
					
					$data_update 						= array(
						"lead_filter_id" 					=> ""
					);
					
					$astDB->where("lead_filter_id", $filter_id);
					$astDB->update("vicidial_campaigns", $data_update);

					$log_id 							= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted Filter ID: $filter_id", $log_group, $astDB->getLastQuery());

					$apiresults 						= array(
						"result" 						=> "success"
					);
				} else {
					$apiresults 						= array(
						"result" 							=> "Error: Filter doesn't exist."
					);
				}
			} else {
				$err_msg 								= error_handle( "10001", "Insufficient permision" );
				$apiresults 							= array(
					"code" 									=> "10001", 
					"result" 								=> $err_msg
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
