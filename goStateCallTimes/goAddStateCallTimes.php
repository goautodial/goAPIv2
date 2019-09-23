<?php
 /**
 * @file 		goAddStateCallTime.php
 * @brief 		API for State Calltimes
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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

    ### POST or GET Variables
	$state_call_time_id = $astDB->escape($_REQUEST['state_call_time_id']);
	$state_call_time_state = $astDB->escape($_REQUEST['state_call_time_state']);
	$state_call_time_name = $astDB->escape($_REQUEST['state_call_time_name']);
	$state_call_time_comments = $astDB->escape($_REQUEST['state_call_time_comments']);
	$sct_default_start = $astDB->escape($_REQUEST['sct_default_start']);
	$sct_default_stop = $astDB->escape($_REQUEST['sct_default_stop']);
	$sct_sunday_start = $astDB->escape($_REQUEST['sct_sunday_start']);
	$sct_sunday_stop = $astDB->escape($_REQUEST['sct_sunday_stop']);
	$sct_monday_start= $astDB->escape($_REQUEST['sct_monday_start']);
	$sct_monday_stop= $astDB->escape($_REQUEST['sct_monday_stop']);
	$sct_tuesday_start = $astDB->escape($_REQUEST['sct_tuesday_start']);
	$sct_tuesday_stop = $astDB->escape($_REQUEST['sct_tuesday_stop']);
	$sct_wednesday_start = $astDB->escape($_REQUEST['sct_wednesday_start']);
	$sct_wednesday_stop = $astDB->escape($_REQUEST['sct_wednesday_stop']);
	$sct_thursday_start = $astDB->escape($_REQUEST['sct_thursday_start']);
	$sct_thursday_stop = $astDB->escape($_REQUEST['sct_thursday_stop']);
	$sct_friday_start = $astDB->escape($_REQUEST['sct_friday_start']);
	$sct_friday_stop = $astDB->escape($_REQUEST['sct_friday_stop']);
	$sct_saturday_start = $astDB->escape($_REQUEST['sct_saturday_start']);
	$sct_saturday_stop = $astDB->escape($_REQUEST['sct_saturday_stop']);
	$user_group = $astDB->escape($_REQUEST['user_group']);

    ### Default values 


    ### ERROR CHECKING 
	if($state_call_time_id == null || strlen($state_call_time_id) < 3) {
		$apiresults = array("result" => "Error: Set a value for State Call Time ID not less than 3 characters.");
	} else {
		if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $state_call_time_name) || $state_call_time_name == null){
			$apiresults = array("result" => "Error: Special characters found in state call time name and must not be empty");
		} else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $state_call_time_id)){
				$apiresults = array("result" => "Error: Special characters found in state call time ID");
			} else {
				if(strlen($state_call_time_state) != 2){
					$apiresults = array("result" => "Error: State Call Time State only accept two characters");
				} else {
					if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $state_call_time_state)){
						$apiresults = array("result" => "Error: Special characters found in state call time state");
					} else {
						if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $state_call_time_comments)){
							$apiresults = array("result" => "Error: Special characters found in state call time comments");
						} else {
							if(!is_numeric($sct_default_start) && $sct_default_start != null){
								$apiresults = array("result" => "Error: sct_default_start must be a number or combination of number");
							} else {
								if(!is_numeric($sct_default_stop) && $sct_default_stop != null){
									$apiresults = array("result" => "Error: sct_default_stop must be a number or combination of number");
								} else {
									if(!is_numeric($sct_sunday_start) && $sct_sunday_start != null){
										$apiresults = array("result" => "Error: sct_sunday_start must be a number or combination of number");
									} else {
										if(!is_numeric($sct_sunday_stop) && $sct_sunday_stop != null){
											$apiresults = array("result" => "Error: sct_sunday_stop must be a number or combination of number");
										} else {
											if(!is_numeric($sct_monday_start) && $sct_monday_start != null){
												$apiresults = array("result" => "Error: sct_monday_start must be a number or combination of number");
											} else {
												if(!is_numeric($sct_monday_stop) && $sct_monday_stop != null){
													$apiresults = array("result" => "Error: sct_monday_stop must be a number or combination of number");
												} else {
													if(!is_numeric($sct_tuesday_start) && $sct_tuesday_start != null){
														$apiresults = array("result" => "Error: sct_tuesday_start must be a number or combination of number");
													} else {
														if(!is_numeric($sct_tuesday_stop) && $sct_tuesday_stop != null){
															$apiresults = array("result" => "Error: sct_tuesday_stop must be a number or combination of number");
														} else {
															if(!is_numeric($sct_wednesday_start) && $sct_wednesday_start != null){
																$apiresults = array("result" => "Error: sct_wednesday_start must be a number or combination of number");
															} else {
																if(!is_numeric($sct_wednesday_stop) && $sct_wednesday_stop != null){
																	$apiresults = array("result" => "Error: sct_wednesday_stop must be a number or combination of number");
																} else {
																	if(!is_numeric($sct_thursday_start) && $sct_thursday_start != null){
																		$apiresults = array("result" => "Error: sct_thursday_start must be a number or combination of number");
																	} else {
																		if(!is_numeric($sct_thursday_stop) && $sct_thursday_stop != null){
																			$apiresults = array("result" => "Error: sct_thursday_stop must be a number or combination of number");
																		} else {
																			if(!is_numeric($sct_friday_start) && $sct_friday_start != null){
																				$apiresults = array("result" => "Error: sct_friday_start must be a number or combination of number");
																			} else {
																				if(!is_numeric($sct_friday_stop) && $sct_friday_stop != null){
																					$apiresults = array("result" => "Error: sct_friday_stop must be a number or combination of number");
																				} else {
																					if(!is_numeric($sct_saturday_start) && $sct_saturday_start != null){
																						$apiresults = array("result" => "Error: sct_saturday_start must be a number or combination of number");
																					} else {
																						if(!is_numeric($sct_saturday_stop) && $sct_saturday_stop != null){
																							$apiresults = array("result" => "Error: sct_saturday_stop must be a number or combination of number");
																						} else {
																							$groupId = go_get_groupid($goUser, $astDB);
																							
																							if (!checkIfTenant($groupId, $goDB)) {
																								//$ul = "";
																							} else {
																								//$ul = "AND user_group='$groupId'";
																								//$addedSQL = "WHERE user_group='$groupId'";
																								$astDB->where('user_group', $groupId);
																							}
																							
																							//$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups WHERE user_group='".mysqli_escape_string($user_group)."' $ul ORDER BY user_group LIMIT 1;";
																							$astDB->where('user_group', $user_group);
																							$astDB->orderBy('user_group', 'desc');
																							$rsltv = $astDB->getOne('vicidial_user_groups', 'user_group,group_name,forced_timeclock_login');
																							$countResult = $astDB->getRowCount();
																							
																							if($countResult > 0) {
																								//$queryCheck = "SELECT state_call_time_id from vicidial_state_call_times where state_call_time_id='".mysqli_escape_string($state_call_time_id)."';";
																								$astDB->where('state_call_time_id', $state_call_time_id);
																								$sqlCheck = $astDB->get('vicidial_state_call_times');
																								$countCheck = $astDB->getRowCount();
																								if($countCheck <= 0) {
																									//$newQuery = "INSERT INTO vicidial_state_call_times (state_call_time_id, state_call_time_state, state_call_time_name, state_call_time_comments, user_group, sct_default_start, sct_default_stop, sct_sunday_start, sct_sunday_stop, sct_monday_start, sct_monday_stop, sct_tuesday_start, sct_tuesday_stop, sct_wednesday_start, sct_wednesday_stop, sct_thursday_start, sct_thursday_stop, sct_friday_start, sct_friday_stop, sct_saturday_start, sct_saturday_stop) VALUES ('".mysqli_real_escape_string($state_call_time_id)."', '".mysqli_real_escape_string($state_call_time_state)."', '".mysqli_real_escape_string($state_call_time_name)."', '".mysqli_real_escape_string($state_call_time_comments)."', '".mysqli_real_escape_string($user_group)."', '".mysqli_real_escape_string($sct_default_start)."', '".mysqli_real_escape_string($sct_default_stop)."', '".mysqli_real_escape_string($sct_sunday_start)."', '".mysqli_real_escape_string($sct_sunday_stop)."', '".mysqli_real_escape_string($sct_monday_start)."', '".mysqli_real_escape_string($sct_monday_stop)."', '".mysqli_real_escape_string($sct_tuesday_start)."', '".mysqli_real_escape_string($sct_tuesday_stop)."', '".mysqli_real_escape_string($sct_wednesday_start)."', '".mysqli_real_escape_string($sct_wednesday_stop)."', '".mysqli_real_escape_string($sct_thursday_start)."', '".mysqli_real_escape_string($sct_thursday_stop)."', '".mysqli_real_escape_string($sct_friday_start)."', '".mysqli_real_escape_string($sct_friday_stop)."', '".mysqli_real_escape_string($sct_saturday_start)."', '".mysqli_real_escape_string($sct_saturday_stop)."');";
																									$insertData = array(
																										'state_call_time_id' => $state_call_time_id,
																										'state_call_time_state' => $state_call_time_state,
																										'state_call_time_name' => $state_call_time_name,
																										'state_call_time_comments' => $state_call_time_comments,
																										'user_group' => $user_group,
																										'sct_default_start' => $sct_default_start,
																										'sct_default_stop' => $sct_default_stop,
																										'sct_sunday_start' => $sct_sunday_start,
																										'sct_sunday_stop' => $sct_sunday_stop,
																										'sct_monday_start' => $sct_monday_start,
																										'sct_monday_stop' => $sct_monday_stop,
																										'sct_tuesday_start' => $sct_tuesday_start,
																										'sct_tuesday_stop' => $sct_tuesday_stop,
																										'sct_wednesday_start' => $sct_wednesday_start,
																										'sct_wednesday_stop' => $sct_wednesday_stop,
																										'sct_thursday_start' => $sct_thursday_start,
																										'sct_thursday_stop' => $sct_thursday_stop,
																										'sct_friday_start' => $sct_friday_start,
																										'sct_friday_stop' => $sct_friday_stop,
																										'sct_saturday_start' => $sct_saturday_start,
																										'sct_saturday_stop' => $sct_saturday_stop
																									);
																									$rsltv = $astDB->insert('vicidial_state_call_times', $insertData);
																									
																									$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Added New State Call Time: $state_call_time_id", $log_group, $newQuery);
																									
																									if($rsltv == false){
																										$apiresults = array("result" => "Error: Add failed, check your details");
																									} else {
																										$apiresults = array("result" => "success");
																									}
																								} else {
																									$apiresults = array("result" => "Error: Add failed, State Call Time already already exist!");
																								}
																							} else {
																								$apiresults = array("result" => "Error: Invalid User Group");
																							}
																						}
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
?>