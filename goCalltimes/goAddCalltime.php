<?php
/**
 * @file        goAddCalltime.php
 * @brief       API to add Call Time
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja 
 * @author      Warren Ipac Briones
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
**/
	include_once ("goAPI.php");
	
	$call_time_id 										= $astDB->escape($_REQUEST['call_time_id']);
	$call_time_name 									= $astDB->escape($_REQUEST['call_time_name']);
	$call_time_comments 								= $astDB->escape($_REQUEST['call_time_comments']);
	$ct_default_start 									= $astDB->escape($_REQUEST['ct_default_start']);
	$ct_default_stop 									= $astDB->escape($_REQUEST['ct_default_stop']);
	$ct_sunday_start 									= $astDB->escape($_REQUEST['ct_sunday_start']);
	$ct_sunday_stop 									= $astDB->escape($_REQUEST['ct_sunday_stop']);
	$ct_monday_start									= $astDB->escape($_REQUEST['ct_monday_start']);
	$ct_monday_stop										= $astDB->escape($_REQUEST['ct_monday_stop']);
	$ct_tuesday_start 									= $astDB->escape($_REQUEST['ct_tuesday_start']);
	$ct_tuesday_stop 									= $astDB->escape($_REQUEST['ct_tuesday_stop']);
	$ct_wednesday_start 								= $astDB->escape($_REQUEST['ct_wednesday_start']);
	$ct_wednesday_stop 									= $astDB->escape($_REQUEST['ct_wednesday_stop']);
	$ct_thursday_start 									= $astDB->escape($_REQUEST['ct_thursday_start']);
	$ct_thursday_stop 									= $astDB->escape($_REQUEST['ct_thursday_stop']);
	$ct_friday_start 									= $astDB->escape($_REQUEST['ct_friday_start']);
	$ct_friday_stop 									= $astDB->escape($_REQUEST['ct_friday_stop']);
	$ct_saturday_start 									= $astDB->escape($_REQUEST['ct_saturday_start']);
	$ct_saturday_stop 									= $astDB->escape($_REQUEST['ct_saturday_stop']);
	$default_audio 										= $astDB->escape($_REQUEST['default_audio']);
	$sunday_audio 										= $astDB->escape($_REQUEST['sunday_audio']);
	$monday_audio 										= $astDB->escape($_REQUEST['monday_audio']);
	$tuesday_audio 										= $astDB->escape($_REQUEST['tuesday_audio']);
	$wednesday_audio 									= $astDB->escape($_REQUEST['wednesday_audio']);
	$thursday_audio 									= $astDB->escape($_REQUEST['thursday_audio']);
	$friday_audio 										= $astDB->escape($_REQUEST['friday_audio']);
	$saturday_audio 									= $astDB->escape($_REQUEST['saturday_audio']);
		
	if ($astDB->escape($_REQUEST['user_group']) == "ALL") {
		$user_group 									= "---ALL---";
	} else {
		$user_group 									= $astDB->escape($_REQUEST['user_group']);
	}
		
    // ERROR CHECKING 
	if (empty ($goUser) || is_null ($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty ($goPass) || is_null ($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty ($log_user) || is_null ($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
    } elseif ($call_time_id == null || strlen($call_time_id) < 3) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for Call Time ID not less than 3 characters."
		);
	} elseif (preg_match('/[\'^Â£$%&*()}{@#~?><>,|=_+Â-]/',$call_time_name) || $call_time_name == null) {
		$apiresults 									= array(
			"result" 										=> "Error: Special characters found in call time name and must not be empty"
		);
	} elseif (preg_match('/[\'^Â£$%&*()}{@#~?><>,|=_+Â]/',$call_time_id)) {
		$apiresults 									= array(
			"result" 										=> "Error: Special characters found in call time ID"
		);
	} elseif (preg_match('/[\'^Â£$%&*()}{@#~?><>,|=_+Â-]/',$call_time_comments)) {
		$apiresults 									= array(
			"result" 										=> "Error: Special characters found in call time comments"
		);
	} elseif (!is_numeric($ct_default_start) && $ct_default_start != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_default_start must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_default_stop) && $ct_default_stop != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_default_stop must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_sunday_start) && $ct_sunday_start != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_sunday_start must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_sunday_stop) && $ct_sunday_stop != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_sunday_stop must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_monday_start) && $ct_monday_start != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_monday_start must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_monday_stop) && $ct_monday_stop != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_monday_stop must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_tuesday_start) && $ct_tuesday_start != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_tuesday_start must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_tuesday_stop) && $ct_tuesday_stop != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_tuesday_stop must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_wednesday_start) && $ct_wednesday_start != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_wednesday_start must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_wednesday_stop) && $ct_wednesday_stop != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_wednesday_stop must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_thursday_start) && $ct_thursday_start != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_thursday_start must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_thursday_stop) && $ct_thursday_stop != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_thursday_stop must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_friday_start) && $ct_friday_start != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_friday_start must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_friday_stop) && $ct_friday_stop != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_friday_stop must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_saturday_start) && $ct_saturday_start != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_saturday_start must be a number or combination of number"
		);
	} elseif (!is_numeric($ct_saturday_stop) && $ct_saturday_stop != null) {
		$apiresults 									= array(
			"result" 										=> "Error: ct_saturday_stop must be a number or combination of number"
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
			$tenant										= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
			
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
		
			$astDB->getOne("vicidial_user_groups", "user_group,group_name,forced_timeclock_login");
			$countResult 								= $astDB->count;
			
			if ($user_group == "---ALL---") { // temporary
				$countResult 							= 1;
			}
			
			if ($countResult > 0) {
				$astDB->where("call_time_id", $call_time_id);
				$astDB->getOne("vicidial_call_times");
				//$queryCheck = "SELECT call_time_id from vicidial_call_times where call_time_id='$call_time_id';";
				
				if ($astDB->count < 1) {
					$data 								= array(
						"call_time_id" 						=> $call_time_id,
						"call_time_name" 					=> $call_time_name,
						"call_time_comments" 				=> $call_time_comments,
						"user_group" 						=> $user_group,
						"ct_default_start" 					=> $ct_default_start,
						"ct_default_stop" 					=> $ct_default_stop,
						"ct_sunday_start" 					=> $ct_sunday_start,
						"ct_sunday_stop" 					=> $ct_sunday_stop,
						"ct_monday_start" 					=> $ct_monday_start,
						"ct_monday_stop" 					=> $ct_monday_stop,
						"ct_tuesday_start" 					=> $ct_tuesday_start,
						"ct_tuesday_stop" 					=> $ct_tuesday_stop,
						"ct_wednesday_start" 				=> $ct_wednesday_start,
						"ct_wednesday_stop" 				=> $ct_wednesday_stop,
						"ct_thursday_start" 				=> $ct_thursday_start,
						"ct_thursday_stop" 					=> $ct_thursday_stop,
						"ct_friday_start" 					=> $ct_friday_start,
						"ct_friday_stop" 					=> $ct_friday_stop,
						"ct_saturday_start" 				=> $ct_saturday_start,
						"ct_saturday_stop" 					=> $ct_saturday_stop,
						"default_afterhours_filename_override" => $default_audio,
						"sunday_afterhours_filename_override" => $sunday_audio,
						"monday_afterhours_filename_override" => $monday_audio,
						"tuesday_afterhours_filename_override" => $tuesday_audio,
						"wednesday_afterhours_filename_override" => $wednesday_audio,
						"thursday_afterhours_filename_override" => $thursday_audio,
						"friday_afterhours_filename_override" => $friday_audio,
						"saturday_afterhours_filename_override" => $saturday_audio
					);

					$insertQuery 						= $astDB->insert("vicidial_call_times", $data);
					$log_id 							= log_action($goDB, 'ADD', $log_user, $log_ip, "Added New Call Time $call_time_id", $log_group, $astDB->getLastQuery());

					if (!$insertQuery) {
						$apiresults 					= array(
							"result" 						=> "Error: Add failed, check your details"
						);
					} else {
						$apiresults 					= array(
							"result" 						=> "success"
						);
					}
				} else {
					$apiresults 						= array(
						"result" 							=> "Error: Add failed, State Call Time already already exist!"
					);
				}
			} else {
				$apiresults 							= array(
					"result" 								=> "Error: Invalid User Group"
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
