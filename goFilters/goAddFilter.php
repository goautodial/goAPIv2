<?php
 /**
 * @file        goAddFilter.php
 * @brief       API for Adding Filters
 * @copyright   Copyright (c) 2018 GOautodial Inc.
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
 	
	$lead_filter_id 									= $astDB->escape($_REQUEST["lead_filter_id"]);
    $lead_filter_name 									= $astDB->escape($_REQUEST["lead_filter_name"]); 
    $lead_filter_comments 								= $astDB->escape($_REQUEST["lead_filter_comments"]);
    $lead_filter_sql 									= $_REQUEST["lead_filter_sql"];
    //$script_text 										= str_replace('\n','',$script_text);
    $user_group 										= $astDB->escape($_REQUEST["user_group"]);

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
	} elseif ( empty($lead_filter_id) || is_null($lead_filter_id) ) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for Filter ID."
		);
	} elseif ( preg_match("/[\"^£$%&*()}{@#~?><>,|=_+¬-]/",$lead_filter_name) && $lead_filter_name != null ) {
		$apiresults 									= array(
			"result" 										=> "Error: Special characters found in filter name"
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
			if ( empty($user_group) ) {
				$user_group								= "---ALL---";
			}    
			// check if script ID exists
			$astDB->where("lead_filter_id", $lead_filter_id);        
			$results 									= $astDB->getOne("vicidial_lead_filters", "lead_filter_id");
					
			if (!$results) {
				$data_filter 							= array(
					"lead_filter_id" 						=> $lead_filter_id,
					"lead_filter_comments" 					=> $lead_filter_comments,
					"lead_filter_name" 						=> $lead_filter_name,
					"user_group" 							=> $user_group, 
					"lead_filter_sql" 						=> $lead_filter_sql
				);
				
				$insertFilter 							= $astDB->insert("vicidial_lead_filters", $data_filter);
					
				if (!$insertFilter) {
					$apiresults 						= array(
						"result" 							=> "Error: Add failed, check your details"
					);
				} else {
					$log_id 							= log_action($goDB, "ADD", $log_user, $log_ip, "Added New Filter: $lead_filter_id", $log_group, $astDB->getLastQuery());
					$apiresults 						= array(
						"result" 							=> "success"
					);
				}
			} else {
				$apiresults	 							= array(
					"result" 								=> "Error: Add failed, Filter already already exist!"
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
