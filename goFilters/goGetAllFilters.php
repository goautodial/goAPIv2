<?php
 /**
 * @file 		gpGetAllFilters.php
 * @brief 		API to get all scripts
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author      Christopher P. Lomuntad
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
		
			// getting script count
			$astDB->orderBy('lead_filter_id', 'desc');
			$resultGetFilter							= $astDB->getOne('vicidial_lead_filters', 'lead_filter_id');
			
			// condition
			if ($resultGetFilter) {
				if ( preg_match("/^filter/i", $resultGetFilter['lead_filter_id']) ) {
					$get_last_count 					= str_replace("filter", "", $resultGetFilter['lead_filter_id']);
					$last_pl[] 							= intval($get_last_count);
				} else {
					$get_last_count 					= $resultGetFilter['lead_filter_id'];
					$last_pl[] 							= intval($get_last_count);
				}

				// return data
				$filter_num 							= max($last_pl);
				$filter_num 							= $filter_num + 1;
				
				if ($filter_num < 100) {
					if ($filter_num < 10) {
						$filter_num			 			= "00".$filter_num;
					} else {
						$filter_num 					= "0".$filter_num;
					}
				}
				
				if ($log_group != "ADMIN") {
					$filter_num 						= $filter_num;
				}else{
					$filter_num 						= "filter".$filter_num;
				}
			} else {
				// return data
				$filter_num 							= "filter001";
			}
				
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
			
			$filters 									= $astDB->get('vicidial_lead_filters');
			
			if ($astDB->count > 0) {
				foreach ($filters as $filter) {
					$dataFilterID[] 					= $filter['lead_filter_id'];
					$dataFilterName[] 					= $filter['lead_filter_name'];
					$dataFilterComments[] 				= $filter['lead_filter_comments'];
					$dataFilterSQL[]					= $filter['lead_filter_sql'];
					$dataUserGroup[] 					= $filter['user_group'];
				}		
			} 
			
			$apiresults 								= array(
				"result" 									=> "success",
				"filter_id" 								=> $dataFilterID,
				"filter_name" 								=> $dataFilterName,
				"filter_comments" 							=> $dataFilterComments,
				"filter_sql"								=> $dataFilterSQL,
				"user_group" 								=> $dataUserGroup,
				"filter_count" 								=> $filter_num
			);
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}

?>
