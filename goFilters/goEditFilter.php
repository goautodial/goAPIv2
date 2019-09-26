<?php
 /**
 * @file        goEditFilter.php
 * @brief       API for Editing Specific Filter
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
    $lead_filter_comments 								= $astDB->escape($_REQUEST['lead_filter_comments']);
    //$lead_filter_sql 									= $astDB->escape($_REQUEST['lead_filter_sql']);
    $lead_filter_sql 									= $_REQUEST['lead_filter_sql'];
    //$lead_filter_sql 									= str_replace('\n','',$lead_filter_sql);
    $user_group 										= $astDB->escape($_REQUEST['user_group']);
    
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
	} elseif (empty($lead_filter_id) || is_null($lead_filter_id)) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for Filter ID."
		);
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$lead_filter_name) && $lead_filter_name != null) {
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
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
					}
				}					
			}

			$astDB->where('lead_filter_id' , $lead_filter_id);
			$filter								 		= $astDB->get('vicidial_lead_filters');

			if ($filter) {
				foreach ($filter as $fresults) {
					$datafilter_id 						= $fresults['lead_filter_id'];
					$datafilter_name 					= $fresults['lead_filter_name'];
					$datafilter_comments 				= $fresults['lead_filter_comments'];
					$datafilter_sql 					= $fresults['lead_filter_sql'];
					$datauser_group 					= $fresults['user_group'];
				}
				
				$data_update 							= array(
					'lead_filter_name' 						=> ($lead_filter_name == null) ? $datafilter_name : $lead_filter_name,
					'lead_filter_comments' 					=> ($lead_filter_comments == null) ? $datafilter_comments : $lead_filter_comments,
					'lead_filter_sql' 						=> ($lead_filter_sql == null) ? ($datafilter_sql): $lead_filter_sql,
					'user_group' 							=> ($user_group == null) ? $datauser_group : $user_group
				);
				
				$astDB->where('lead_filter_id', $lead_filter_id);
				$update 								= $astDB->update('vicidial_lead_filters', $data_update);
			
				if ($update) {
					$apiresults 						= array(
						"result" 							=> "success"
					);

					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Filter ID: $lead_filter_id", $log_group, $astDB->getLastQuery());
				} else {
					$apiresults 						= array(
						"result" 							=> "Error: Try updating Filter Again"
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
