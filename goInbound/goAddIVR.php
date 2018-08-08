<?php
/**
 * @file        goAddIVR.php
 * @brief       API to add new IVR
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James Milo  <jerico@goautodial.com>
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
    
	$log_user 									= $session_user;
	$log_group 									= go_get_groupid($session_user, $astDB); 
	$log_ip 									= $astDB->escape($_REQUEST['log_ip']);     

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
    if (!empty($items)) {
        $exploded_items 						= explode("|", $items);
        $filtered_items 						= array_filter($exploded_items);
    }
	
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
		if (checkIfTenant ($log_group, $goDB)) {
			$astDB->where ("user_group", $log_group);
		}

        $countResult 							= $astDB->getValue("vicidial_user_groups", "count(*)");
		//$query 							= "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ulug ORDER BY user_group LIMIT 1;";
		
        if($countResult > 0 || $user_group == "---ALL---") {
            $astDB->where("id_table", "vicidial_call_menu");
            $astDB->where("active", 1);
            $voi_ct 							= $astDB->getValue("vicidial_override_ids");
            //$stmt 							= "SELECT value FROM vicidial_override_ids where id_table='vicidial_call_menu' and active='1';";
			
			if ($voi_ct > 0) {
                $datum 							= Array(
					"value" 						=> $menu_id
				);
				
				$astDB->where("id_table", "vicidial_call_menu");
                $astDB->where("active", 1);
                $astDB->update("vicidial_override_ids", $datum);
                //$stmt 							= "UPDATE vicidial_override_ids SET value='$menu_id' where id_table='vicidial_call_menu' and active='1';";
			}
			
            $astDB->where("menu_id", $menu_id);
            $row 								= $astDB->getValue("vicidial_call_menu", "count(*)");
			//$stmtCheck 						= "SELECT menu_id from vicidial_call_menu where menu_id='$menu_id';";
			      
			if ($row > 0) {
				$apiresults 					= array(
					"result" 						=> "Error: CALL MENU NOT ADDED - there is already a CALL MENU in the system with this ID"
				);
			  //$message 							= "CALL MENU NOT ADDED - there is already a CALL MENU in the system with this ID\n";
			} else {
                $data 							= Array(
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
				
				$query 							= $astDB->insert("vicidial_call_menu", $data);
                $queryAddIVR 					= "INSERT INTO vicidial_call_menu (menu_id, menu_name, user_group, menu_prompt, menu_timeout, menu_timeout_prompt, menu_invalid_prompt, menu_repeat, menu_time_check, call_time_id, track_in_vdac, custom_dialplan_entry, tracking_group) values('$menu_id', '$menu_name', '$user_group', '$menu_prompt', '$menu_timeout', '$menu_timeout_prompt', '$menu_invalid_prompt', '$menu_repeat', '$menu_time_check', '24hours', '$track_in_vdac', '$custom_dialplan_entry', '$tracking_group');";
				//$resultQueryAdd 							= mysqli_query($link, $queryAddIVR);
				
				// set default entry in vicidial_callmenu_options by Franco Hora 
				$data2 							= Array(
					"menu_id" 						=> $menu_id,
					"option_value" 					=> "TIMEOUT",
					"option_description" 			=> "Hangup",
					"option_route" 					=> "HANGUP",
					"option_route_value" 			=> "vm-goodbye"
				);
				
                $query2 						= $astDB->insert("vicidial_call_menu_options", $data2);
                //$queryDef 							= "INSERT INTO vicidial_call_menu_options (menu_id,option_value,option_description,option_route,option_route_value) values('$menu_id','TIMEOUT','Hangup','HANGUP','vm-goodbye');";
				
				//query for call menu options
				for($i=0; $i < count($filtered_items); $i++){
					$options 					= explode("+", $filtered_items[$i]);
					if(!empty($options[0])){
						$data3 					= Array(
							"menu_id" 				=> $menu_id,
							"option_value" 			=> $options[0],
							"option_route" 			=> $option[1],
							"option_description" 	=> $options[2],
							"option_route_value" 	=> $options[3],
							"option_route_value_context" => $options[4]
						);
					}
				}
				
                rebuildconfQuery($astDB, $log_group);
				//$queryUpdateAsterisk 							= "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y';";
				$resultVSC 						= mysqli_query($link, $queryUpdateAsterisk);
				
		// Admin logs
                if ($query) {
                    $log_id 					= log_action($goDB, 'ADD', $log_user, $log_group, "Added New IVR $menu_id", $log_group, $queryAddIVR);
                    $apiresults 				= array(
						"result" 					=> "success", 
						"query" 					=> $queryAddIVR
					);
                } else {
                    $apiresults 				= array(
						"result" 					=> "Error: Query Failed"
					);
                }
			}

		} else {
			$apiresults 						= array(
				"result" 							=> "Error: INVALID USER GROUP"
			);
		}
		
	}

?>
