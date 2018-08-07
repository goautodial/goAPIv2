<?php
 /**
 * @file 		goDeleteLead.php
 * @brief 		API for Deleting Leads
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Abenoja  <alex@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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
 
	$log_user 									= $session_user;
	$log_group 									= go_get_groupid($session_user, $astDB); 
	$log_ip 									= $astDB->escape($_REQUEST['log_ip']);
	
    // POST or GET Variables
    $lead_id 									= $astDB->escape($_REQUEST['lead_id']);

		
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
	} elseif (empty($lead_id) || is_null($lead_id)) {
		$err_msg 								= error_handle("40001");
		$apiresults 							= array(
			"code" 									=> "40001", 
			"result" 								=> $err_msg
		);
	} else {	
		// check lead_id if it exists
		$astDB->where("lead_id", $lead_id);
		$fresults 								= $astDB->getOne("vicidial_list", "lead_id");
		
		if ($fresults) {                
			// check if customer
			$goDB->where("lead_id", $lead_id);
			$fresultsgo 						= $goDB->getOne("go_customers", "lead_id");
			
			if ($fresultsgo) { 
				$goDB->where("lead_id", $lead_id);
				$goDB->delete('go_customers');
				
				$log_id 						= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Lead ID: $lead_id", $log_group, $goDB->getLastQuery());
			}
				
			$astDB->where('lead_id', $lead_id);
            $astDB->delete('vicidial_list');

			$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Lead ID: $lead_id", $log_group, $astDB->getLastQuery());
			
			$apiresults							= array(
				"result" 							=> "success"
			);
		} else {
			$err_msg 							= error_handle("10010");
			$apiresults 						= array(
				"code" 								=> "10010", 
				"result" 							=> $err_msg
			);
			//$apiresults = array("result" => "Error: Lead ID does not exist.");
		}		
	}
	
?>
