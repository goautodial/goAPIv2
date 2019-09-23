<?php
/**
 * @file        goAddIVR.php
 * @brief       API to add new IVR
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho 
 * @author      Jerico James Milo
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
    $menu_id 									= $astDB->escape($_REQUEST['menu_id']);
    $menu_name 									= $astDB->escape($_REQUEST['menu_name']);
    $user_group 								= $astDB->escape($_REQUEST['user_group']);
    $menu_prompt 								= $astDB->escape($_REQUEST['menu_prompt']);
    $menu_timeout 								= $astDB->escape($_REQUEST['menu_timeout']);
    $menu_timeout_prompt 						= $astDB->escape($_REQUEST['menu_timeout_prompt']);
    $menu_invalid_prompt 						= $astDB->escape($_REQUEST['menu_invalid_prompt']);
    $menu_repeat 								= $astDB->escape($_REQUEST['menu_repeat']);
    $menu_time_check 							= $astDB->escape($_REQUEST['menu_time_check']);
    $call_time_id 								= $astDB->escape($_REQUEST['call_time_id']);
    $track_in_vdac 								= $astDB->escape($_REQUEST['track_in_vdac']);
    $custom_dialplan_entry 						= $astDB->escape($_REQUEST['custom_dialplan_entry']);
    $tracking_group 							= $astDB->escape($_REQUEST['tracking_group']);	
    $items 										= $_REQUEST['items'];
	
    // Default values 
	$defmenu_time_check 						= array('0','1');
	$deftrack_in_vdac 							= array('0','1');

	if (empty($log_user) || is_null($log_user)) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
	} elseif (empty($menu_id) || strlen($menu_id) < 4) {
        $apiresults 							= array(
			"result" 								=> "Error: Set a value for Menu ID not less than 4 characters."
		);
    } elseif (empty($menu_name)) {
        $apiresults 							= array(
			"result" 								=> "Error: Set a value for menu_name."
		);
    } elseif (empty($user_group)) {
        $apiresults 							= array(
			"result" 								=> "Error: Set a value for user_group."
		);
    } elseif (empty($menu_timeout)) {
        $apiresults 							= array(
			"result" 								=> "Error: Set a value for menu_timeout."
		);
    } elseif (empty($menu_repeat)) {
        $apiresults 							= array(
			"result" 								=> "Error: Set a value for menu_repeat."
		);
    } elseif (empty($tracking_group)) {
        $apiresults 							= array(
			"result" 								=> "Error: Set a value for tracking_group."
		);
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_id)){
        $apiresults 							= array(
			"result" 								=> "Error: Special characters found in menu_id"
		);
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_name)){
        $apiresults 							= array(
			"result" 								=> "Error: Special characters found in menu_name"
		);
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_timeout)){
        $apiresults 							= array(
			"result" 								=> "Error: Special characters found in menu_timeout"
		);
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_repeat)){
        $apiresults 							= array(
			"result" 								=> "Error: Special characters found in menu_repeat"
		);
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $tracking_group)){
        $apiresults 							= array(
			"result" 								=> "Error: Special characters found in tracking_group"
		);
    } elseif (empty($user_group)) {
        $apiresults 							= array(
			"result" 								=> "Error: Set a value for user_group."
		);
    } else {
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
		}

        $astDB->getOne("vicidial_user_groups", "user_group");
		
        if ($astDB->count > 0 || $user_group == "---ALL---") {
            $astDB->where("id_table", "vicidial_call_menu");
            $astDB->where("active", 1);
            $astDB->get("vicidial_override_ids");

			if ($astDB->count > 0) {
                $datum 							= array(
					"value" 						=> $menu_id
				);
				
				$astDB->where("id_table", "vicidial_call_menu");
                $astDB->where("active", 1);
                $astDB->update("vicidial_override_ids", $datum);
			}
			
            $astDB->where("menu_id", $menu_id);
			$astDB->getOne("vicidial_call_menu", "menu_id");
			      
			if ($astDB->count > 0) {
				$apiresults 					= array(
					"result" 						=> "Error: Call menu ID already exists."
				);
			} else {
                $data 							= array(
					"menu_id" 						=> $menu_id,
					"menu_name" 					=> $menu_name,
					"user_group" 					=> $user_group,
					"menu_prompt" 					=> $menu_prompt,
					"menu_timeout" 					=> $menu_timeout,
					"menu_timeout_prompt" 			=> $menu_timeout_prompt,
					"menu_invalid_prompt" 			=> $menu_invalid_prompt,
					"menu_repeat" 					=> $menu_repeat,
					"menu_time_check" 				=> $menu_time_check,
					"call_time_id" 					=> "24hours",
					"track_in_vdac" 				=> $track_in_vdac,
					"custom_dialplan_entry" 		=> $custom_dialplan_entry,
					"tracking_group" 				=> $tracking_group
				);				
				
				$astDB->insert("vicidial_call_menu", $data);
				$query							= $astDB->getLastQuery();

				if (!empty($items)) {
					$exploded_items 			= explode("|", $items);
					$filtered_items 			= array_filter($exploded_items);
					
					//query for call menu options
					for ($i=0; $i < count($filtered_items); $i++) {
						$options 				= explode("+", $filtered_items[$i]);
						
						if (!empty($options[2])) {
							$data2 					= array(
								"menu_id" 				=> $menu_id,
								"option_value" 			=> $options[0],
								"option_description"	=> $options[1],
								"option_route"		 	=> $options[2],
								"option_route_value" 	=> $options[3],
								"option_route_value_context" => $options[4]
							);
							
							$astDB->insert("vicidial_call_menu_options", $data2);
							$query2				= $astDB->getLastQuery();
							
						}
					}					
				}
		
				// set default entry in vicidial_callmenu_options by Franco Hora 
				$data3 							= array(
					"menu_id" 						=> $menu_id,
					"option_value" 					=> "TIMEOUT",
					"option_description" 			=> "Hangup",
					"option_route" 					=> "HANGUP",
					"option_route_value" 			=> "vm-goodbye"
				);
				
                $astDB->insert("vicidial_call_menu_options", $data3);
                
                $query3							= $astDB->getLastQuery();
                
                $log_id 						= log_action($goDB, 'ADD', $log_user, $log_ip, "Added New IVR $menu_id", $log_group, $query . $query2 . $query3);
                
                rebuildconfQuery($astDB, $log_group);

				$apiresults 					= array(
					"result" 						=> "success"
				);
			}
		} else {
			$apiresults 						= array(
				"result" 							=> "Error: INVALID USER GROUP"
			);
		}
	}

?>
