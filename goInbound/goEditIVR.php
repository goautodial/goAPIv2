<?php
/**
 * @file        goEditIVR.php
 * @brief       API to edit IVR Details 
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho  
 * @author      Jeremiah Sebastian Samatra 
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
    $menu_id 										= $_REQUEST['menu_id'];
	$menu_name 										= $_REQUEST['menu_name'];
	$menu_prompt 									= $_REQUEST['menu_prompt'];
	$menu_timeout 									= $_REQUEST['menu_timeout'];
	$menu_timeout_prompt 							= $_REQUEST['menu_timeout_prompt'];
	$menu_invalid_prompt 							= $_REQUEST['menu_invalid_prompt'];
	$menu_repeat 									= $_REQUEST['menu_repeat'];
	$menu_time_check 								= $_REQUEST['menu_time_check'];
	$call_time_id 									= $_REQUEST['call_time_id'];
	$track_in_vdac 									= $_REQUEST['track_in_vdac'];
	$tracking_group 								= $_REQUEST['tracking_group'];
	$user_group 									= $_REQUEST['user_group'];
	$custom_dialplan_entry 							= $_REQUEST['custom_dialplan_entry'];	
	$items 											= $_REQUEST['items'];
	
	if (!empty($items)) {
		$exploded_items 							= explode("|", $items);
		$filtered_items 							= array_filter($exploded_items);
	}

    // Default values 
    $defActive = array("Y","N");

	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($menu_id) || is_null($menu_id)) {
        $apiresults 								= array(
			"result" 									=> "Error: Set a value for Menu ID."
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_name)) {
		$apiresults 								= array(
			"result" 									=> "Error: Special characters found in menu_name"
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_timeout)) {
		$apiresults 								= array(
			"result" 									=> "Error: Special characters found in menu_timeout"
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_repeat)) {
		$apiresults 								= array(
			"result" 									=> "Error: Special characters found in menu_repeat"
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $call_time_id)) {
		$apiresults 								= array(
			"result" 									=> "Error: Special characters found in call_time_id"
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $tracking_group)) {
		$apiresults 								= array(
			"result" 									=> "Error: Special characters found in tracking_group"
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $custom_dialplan_entry)) {
		$apiresults 								= array(
			"result" 									=> "Error: Special characters found in custom_dialplan_entry"
		);
	} elseif ($menu_time_check < 0 && !is_null($menu_time_check) || $menu_time_check > 1 && !is_null($menu_time_check)) {
		$apiresults 								= array(
			"result" 									=> "Error: menu_time_check Value should be 0 or 1"
		);
	} elseif ($track_in_vdac < 0 && !is_null($track_in_vdac) || $track_in_vdac > 1 && !is_null($track_in_vdac)) {
		$apiresults 								= array(
			"result" 									=> "Error: track_in_vdac Value should be 0 or 1"
		);
	} else {
		if (checkIfTenant($log_group, $goDB)) {
            $astDB->where("user_group", $log_group);
            $astDB->orWhere("user_group", "---ALL---");
		}
		
        $astDB->where("menu_id", $menu_id);
        $rsltv_check 								= $astDB->get("vicidial_call_menu");
        
        if ($astDB->count > 0) {
			$data 									= array(
				"menu_name" 							=> $menu_name,
				"menu_prompt" 							=> $menu_prompt,
				"menu_timeout" 							=> $menu_timeout,
				"menu_timeout_prompt" 					=> $menu_timeout_prompt,
				"menu_invalid_prompt" 					=> $menu_invalid_prompt,
				"menu_repeat" 							=> $menu_repeat,
				"menu_time_check" 						=> $menu_time_check,
				"call_time_id" 							=> $call_time_id,
				"track_in_vdac" 						=> $track_in_vdac,
				"tracking_group" 						=> $tracking_group,
				"user_group" 							=> $user_group,
				"custom_dialplan_entry" 				=> $custom_dialplan_entry
			);
			
			$astDB->where("menu_id", $menu_id);
			$astDB->update("vicidial_call_menu", $data); 
			$qupdate								= $astDB->getLastQuery();								
			
			//$log_id 								= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Call Menu ID $menu_id", $log_group, $astDB->getLastQuery());
	        		
			// query for call menu options
			if (!empty($items)) {
				$return_query 						= "";
				$astDB->where("menu_id", $menu_id);
				$astDB->delete("vicidial_call_menu_options");

				$qdelete							= $astDB->getLastQuery();
				//$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Call Menu ID $menu_id", $log_group, $astDB->getLastQuery());
				
				for ($i=0; $i < count($filtered_items); $i++) {
					$options 						= explode("+", $filtered_items[$i]);
					
					if (!empty($options[2])) {
						$insertData 				= array(
							"menu_id" 					=> $menu_id,
							"option_value" 				=> $options[0],
							"option_description" 		=> $options[1],
							"option_route" 				=> $options[2],
							"option_route_value" 		=> $options[3],
							"option_route_value_context"=> $options[4]
						);
						
						$astDB->insert("vicidial_call_menu_options", $insertData);
						$qinsert					= $astDB->getLastQuery();
					}
				}
			}
			
			$log_id 								= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Call Menu ID $menu_id", $log_group, $qupdate . $qdelete . $qinsert);
			//reload asterisk
			rebuildconfQuery($astDB, $log_ip);
			
			$apiresults 							= array(
				"result" 								=> "success"
			);			
		} else {
			$apiresults 							= array(
				"result" 								=> "Error: IVR doesn't exist."
			);
		}
	}
	
?>
