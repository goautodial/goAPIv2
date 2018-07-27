<?php
 /**
 * @file        goGetAllLeadRecycling.php
 * @brief 	    API for Getting All Lead Recycling
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author	    Alexander Abenoja  <alex@goautodial.com>
 * @author      Chris Lomuntad  <chris@goautodial.com>
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
	//$log_ip	 									= $astDB->escape($_REQUEST["log_ip"]);
	
	$campaigns 										= allowed_campaigns($log_group, $goDB, $astDB);

    ### ERROR CHECKING
	if ( empty($log_user) || is_null($log_user) ) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} else {
		if ( is_array($campaigns) ) {			
			$astDB->where( "campaign_id", $campaigns, "IN" );
			$astDB->orderBy( "campaign_id", "desc" );
			$rsltv 									= $astDB->get('vicidial_lead_recycle');

			if ($astDB->count > 0) {
				foreach ($rsltv as $fresults) {
					//$data[] 				= array(
					$recycle_id[] 					= $fresults['recycle_id'];
					$campaign_id[] 					= $fresults['campaign_id'];
					$status[] 						= $fresults['status'];
					$attempt_delay[] 				= $fresults['attempt_delay'];
					$attempt_maximum[] 				= $fresults['attempt_maximum'];
					$active[] 						= $fresults['active'];
					//);
				}
				
				$apiresults 						= array(
					"result" 							=> "success", 
					"recycle_id" 						=> $recycle_id,
					"campaign_id"						=> $campaign_id,
					"status"							=> $status,
					"attempt_delay"						=> $attempt_delay,
					"attempt_maximum"					=> $attempt_maximum,
					"active"							=> $active
				);        
			} else {
				$apiresults 						= array(
					"result" 							=> "No data available."
				);
			}
		} else {
			$err_msg 								= error_handle("10108", "status. No campaigns available");
			$apiresults								= array(
				"code" 									=> "10108", 
				"result" 								=> $err_msg
			);		
		}
	}
?>
