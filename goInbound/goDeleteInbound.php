<?php
/**
 * @file        goDeleteInbound.php
 * @brief       API to delete specific in-groups, call menus and DIDs
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
 * @author      Jeremiah Sebastian V. Samatra  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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

    // POST or GET Variables
    $id 										= $astDB->escape($_REQUEST['inbound_id']);
    $type	 									= $astDB->escape($_REQUEST['type']);
	
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
	} elseif (empty($id) || is_null($id)) {
        $apiresults 							= array(
			"result" 								=> "Error: Inbound ID Not Defined."
		);
    } else {			
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere("user_group", "---ALL---");
		}
		
		switch ($type) {				
            case "ingroup":				
				$astDB->where("group_id", $id);
				$astDB->where("group_id", "AGENTDIRECT", "!=");
				$astDB->getOne("vicidial_inbound_groups");
				
				if ($astDB->count > 0) {
					$astDB->where("group_id", $id);
					$astDB->delete("vicidial_inbound_groups");
					
					$log_id 					= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted ingroup ID: $id", $log_group, $astDB->getLastQuery());
					
					$astDB->where("group_id", $id);
					$astDB->delete("vicidial_inbound_group_agents");
					
					$log_id 					= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted ingroup ID: $id", $log_group, $astDB->getLastQuery());
					
					$astDB->where("campaign_id", $id);
					$astDB->delete("vicidial_campaign_stats");
					
					$log_id 					= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted ingroup ID: $id", $log_group, $astDB->getLastQuery());

					$apiresults 				= array(
						"result" 					=> "success"
					);					
				} else {
					$apiresults 				= array(
						"result" 					=> "Error: Call menu doesn't exist or insufficient rights."
					);
				}					
				
			break;
                
			case "ivr":
				$astDB->where("menu_id", $id);
				$astDB->where("menu_id", "defaultlog", "!=");
				$astDB->getOne("vicidial_call_menu");
				
				if ($astDB->count > 0) {
					$astDB->where("menu_id", $id);
					$astDB->delete("vicidial_call_menu");
					
					$log_id 					= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted call menu ID: $id", $log_group, $astDB->getLastQuery());
					
					$astDB->where("menu_id", $id);
					$astDB->delete("vicidial_call_menu_options");			
					
					$log_id 					= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted call menu ID: $id", $log_group, $astDB->getLastQuery());

					$apiresults 				= array(
						"result" 					=> "success"
					);					
				} else {
					$apiresults 				= array(
						"result" 					=> "Error: Call menu doesn't exist or insufficient rights."
					);
				}
				
			break;
				
			case "did":
				$astDB->where("did_id", $id);
				$astDB->getOne("vicidial_inbound_dids");
				
				if ($astDB->count > 0) {
					$astDB->where("did_id", $id);
					$astDB->delete("vicidial_inbound_dids");
					
					$log_id 					= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted DID ID: $id", $log_group, $astDB->getLastQuery());

					$apiresults 				= array(
						"result" 					=> "success"
					);				
				} else {
					$apiresults 				= array(
						"result" 					=> "Error: Call DID doesn't exist or insufficient rights."
					);
				}
				
			break;
		}
		
		//print_r($apiresults);
		
	}
	
?>
