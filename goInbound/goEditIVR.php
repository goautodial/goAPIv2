<?php
/**
 * @file        goEditInbound.php
 * @brief       API to edit IVR Details 
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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
    
    include_once ("../goFunctions.php");
 
    // POST or GET Variables
	$menu_name = $_REQUEST['menu_name'];
	$menu_prompt = $_REQUEST['menu_prompt'];
	$menu_timeout = $_REQUEST['menu_timeout'];
	$menu_timeout_prompt = $_REQUEST['menu_timeout_prompt'];
	$menu_invalid_prompt = $_REQUEST['menu_invalid_prompt'];
	$menu_repeat = $_REQUEST['menu_repeat'];
	$menu_time_check = $_REQUEST['menu_time_check'];
	$call_time_id = $_REQUEST['call_time_id'];
	$track_in_vdac = $_REQUEST['track_in_vdac'];
	$tracking_group = $_REQUEST['tracking_group'];
	$user_group = $_REQUEST['user_group'];
	$custom_dialplan_entry = $_REQUEST['custom_dialplan_entry'];
	$menu_id = $_REQUEST['menu_id'];
	
	$ip_address = $_REQUEST['hostname'];
	
	$log_user = $goUser;
	$log_group = go_get_groupid($goUser, $astDB);
	
	$items = $_REQUEST['items'];
	if(!empty($items)){
		$exploded_items = explode("|", $items);
		$filtered_items = array_filter($exploded_items);
	}

    // Default values 
    $defActive = array("Y","N");

    if(empty($menu_id)) {
        $apiresults = array("result" => "Error: Set a value for menu ID.");
    } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_name)){
		$apiresults = array("result" => "Error: Special characters found in menu_name");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_timeout)){
		$apiresults = array("result" => "Error: Special characters found in menu_timeout");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_repeat)){
		$apiresults = array("result" => "Error: Special characters found in menu_repeat");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $call_time_id)){
		$apiresults = array("result" => "Error: Special characters found in call_time_id");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $tracking_group)){
		$apiresults = array("result" => "Error: Special characters found in tracking_group");
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $custom_dialplan_entry)){
		$apiresults = array("result" => "Error: Special characters found in custom_dialplan_entry");
	} elseif($menu_time_check < 0 && !is_null($menu_time_check) || $menu_time_check > 1 && !is_null($menu_time_check)) {
		$apiresults = array("result" => "Error: menu_time_check Value should be 0 or 1");
	} elseif($track_in_vdac < 0 && !is_null($track_in_vdac) || $track_in_vdac > 1 && !is_null($track_in_vdac)) {
		$apiresults = array("result" => "Error: track_in_vdac Value should be 0 or 1");
	} else {
		$data = Array(
				"menu_name" => $menu_name,
				"menu_prompt" => $menu_prompt,
				"menu_timeout" => $menu_timeout,
				"menu_timeout_prompt" => $menu_timeout_prompt,
				"menu_invalid_prompt" => $menu_invalid_prompt,
				"menu_repeat" => $menu_repeat,
				"menu_time_check" => $menu_time_check,
				"call_time_id" => $call_time_id,
				"track_in_vdac" => $track_in_vdac,
				"tracking_group" => $tracking_group,
				"user_group" => $user_group,
				"custom_dialplan_entry" => $custom_dialplan_entry
			);
		$astDB->where("menu_id", $menu_id);
		$astDB->update("vicidial_call_menu", $data);
		/*$query = "UPDATE vicidial_call_menu
				SET menu_name = '$menu_name',
				menu_prompt = '$menu_prompt',
				menu_timeout = '$menu_timeout',
				menu_timeout_prompt = '$menu_timeout_prompt',
				menu_invalid_prompt = '$menu_invalid_prompt',
				menu_repeat = '$menu_repeat',
				menu_time_check = '$menu_time_check',
				call_time_id = '$call_time_id',
				track_in_vdac = '$track_in_vdac',
				tracking_group = '$tracking_group',
				user_group = '$user_group',
				custom_dialplan_entry = '$custom_dialplan_entry' 
				WHERE menu_id='$menu_id';";
		*/
		
		// query for call menu options
		if(!empty($items)){
			$return_query = "";
			$astDB->where("menu_id", $menu_id);
			$astDB->delete("vicidial_call_menu_options");
			//$delete_exoptions = "DELETE FROM vicidial_call_menu_options WHERE menu_id = '$menu_id';";
			
			for($i=0; $i < count($filtered_items); $i++){
				$options = explode("+", $filtered_items[$i]);
				if($options[0] !== ''){
					$insertData = Array(
									"menu_id" => $menu_id,
									"option_value" => $option[0],
									"option_description" => $option[1],
									"option_route" => $option[2],
									"option_route_value" => $option[3],
									"option_route_value_context" => $option[4]
								);
					$astDB->insert("vicidial_call_menu_options", $insertData);
					$query_options = "INSERT INTO vicidial_call_menu_options (menu_id,option_value,option_description,option_route,option_route_value, option_route_value_context) values('$menu_id', '$options[0]', '$options[1]', '$options[2]', '$options[3]', '$options[4]');";
					$return_query .= $query_options."____";
				}
			}
		}
		
		//reload asterisk
		rebuildconfQuery($astDB, $ip_address);
		
		if($resultQuery){
			$apiresults = array("result" => "success");
			$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified Call Menu ID $menu_id", $log_group, $query);
		} else {
			$apiresults = array("result" => "Error: Failed to update");
		}
	}
?>
