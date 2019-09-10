<?php
 /**
 * @file 		goEditStateCallTime.php
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

    ### ERROR CHECKING ...
	if($state_call_time_id == null || strlen($state_call_time_id) < 3) {
		$apiresults = array("result" => "Error: Set a value for State Call Time ID not less than 3 characters.");
	} else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $state_call_time_name) && $state_call_time_name != null){
            $apiresults = array("result" => "Error: Special characters found in state call time name");
        } else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $state_call_time_id) && $state_call_time_id != null){
				$apiresults = array("result" => "Error: Special characters found in state call time ID");
			} else {
				if(strlen($state_call_time_state) != 2 && $state_call_time_state != null){
					$apiresults = array("result" => "Error: State Call Time State only accept two characters");
				} else {
					if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $state_call_time_state) && $state_call_time_state != null){
						$apiresults = array("result" => "Error: Special characters found in state call time state");
					} else {
						if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $state_call_time_comments) && $state_call_time_comments != null){
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
																							
																							//$queryCheck = "SELECT * from vicidial_state_call_times WHERE state_call_time_id='".mysqli_escape_string($state_call_time_id)."'$ul $addedSQL;";
																							$astDB->where('state_call_time_id', $state_call_time_id);
																							$sqlCheck = $astDB->get('vicidial_state_call_times');
																							foreach ($sqlCheck as $fresults){
																								$datastate_call_time_id = $fresults['state_call_time_id'];
																								$datastate_call_time_state = $fresults['state_call_time_state'];				  
																								$datastate_call_time_name = $fresults['state_call_time_name'];				  
																								$datastate_call_time_comments = $fresults['state_call_time_comments'];				  
																								$datauser_group = $fresults['user_group'];				  
																								$datasct_default_start = $fresults['sct_default_start'];				  
																								$datasct_default_stop = $fresults['sct_default_stop'];				  
																								$datasct_sunday_start = $fresults['sct_sunday_start'];				  
																								$datasct_sunday_stop = $fresults['sct_sunday_stop'];				  
																								$datasct_monday_start = $fresults['sct_monday_start'];
																								$datasct_monday_stop = $fresults['sct_monday_stop'];				  
																								$datasct_tuesday_start = $fresults['sct_tuesday_start'];				  
																								$datasct_tuesday_stop = $fresults['sct_tuesday_stop'];				  
																								$datasct_wednesday_start = $fresults['sct_wednesday_start'];				  
																								$datasct_wednesday_stop = $fresults['sct_wednesday_stop'];				  
																								$datasct_thursday_start = $fresults['sct_thursday_start'];				  
																								$datasct_thursday_stop = $fresults['sct_thursday_stop'];				  
																								$datasct_friday_start = $fresults['sct_friday_start'];				  
																								$datasct_friday_stop = $fresults['sct_friday_stop'];				  
																								$datasct_saturday_start = $fresults['sct_saturday_start'];				  
																								$datasct_saturday_stop = $fresults['sct_saturday_stop'];				  
																							}
																							$countVM = $astDB->getRowCount();
																			
																							if($countVM > 0) {
																								if($state_call_time_id == null){ $state_call_time_id = $datastate_call_time_id; }
																								if($state_call_time_state == null){ $state_call_time_state = $datastate_call_time_state;}
																								if($state_call_time_name == null){ $state_call_time_name = $datastate_call_time_name;}
																								if($state_call_time_comments == null){ $state_call_time_comments = $datastate_call_time_comments;}
																								if($user_group == null){ $user_group = $datauser_group; }
																								if($sct_default_start == null){ $sct_default_start =  $datasct_default_start; }
																								if($sct_default_stop == null) {$sct_default_stop =  $datasct_default_stop; }
																								if($sct_sunday_start == null) {$sct_sunday_start = $datasct_sunday_start; }
																								if($sct_sunday_stop == null) {$sct_sunday_stop = $datasct_sunday_stop; }
																								if($sct_monday_start == null) { $sct_monday_start = $datasct_monday_start; }
																								if($sct_monday_stop == null) { $sct_monday_stop = $datasct_monday_stop; }
																								if($sct_tuesday_start == null) { $sct_tuesday_start = $datasct_tuesday_start;}
																								if($sct_tuesday_stop == null) { $sct_tuesday_stop = $datasct_tuesday_stop; }
																								if($sct_wednesday_start == null) {$sct_wednesday_start = $datasct_wednesday_start; }
																								if($sct_wednesday_stop == null) { $sct_wednesday_stop = $datasct_wednesday_stop; }
																								if($sct_thursday_start == null) { $sct_thursday_start = $datasct_thursday_start; }
																								if($sct_thursday_stop == null) { $sct_thursday_stop = $datasct_thursday_stop; }			
																								if($sct_friday_start  == null) {$sct_friday_start = $datasct_friday_start; }
																								if($sct_friday_stop == null) {$sct_friday_stop = $datasct_friday_stop; }
																								if($sct_saturday_start = null) {$sct_saturday_start = $datasct_saturday_start;}
																								if($sct_saturday_stop == null) {$sct_saturday_stop = $datasct_saturday_stop; }
																								if($pass == null) {$pass = $dataVM_pass;}
																								if($active == null) {$active = $dataactive;}
																								if($fullname == null) {$fullname = $datafullname;}
																								if($email == null) {$email = $dataemail;}
																								if($delete_vm_after_email == null) {$delete_vm_after_email = $datadeleteVMemail;}
																								
																								//$queryVM = "UPDATE vicidial_state_call_times SET state_call_time_state='".mysqli_escape_string($state_call_time_state)."',  state_call_time_name='".mysqli_escape_string($state_call_time_name)."',  state_call_time_comments='".mysqli_escape_string($state_call_time_comments)."',  user_group='".mysqli_escape_string($user_group)."',  sct_default_start='".mysqli_escape_string($sct_default_start)."',  sct_default_stop='".mysqli_escape_string($sct_default_stop)."',  sct_sunday_start='".mysqli_escape_string($sct_sunday_start)."',  sct_sunday_stop='".mysqli_escape_string($sct_sunday_stop)."',  sct_monday_start='".mysqli_escape_string($sct_monday_start)."',  sct_monday_stop='".mysqli_escape_string($sct_monday_stop)."',  sct_tuesday_start='".mysqli_escape_string($sct_tuesday_start)."',  sct_tuesday_stop='".mysqli_escape_string($sct_tuesday_stop)."',  sct_wednesday_start='".mysqli_escape_string($sct_wednesday_start)."',  sct_wednesday_stop='".mysqli_escape_string($sct_wednesday_stop)."',  sct_thursday_start='".mysqli_escape_string($sct_thursday_start)."',  sct_thursday_stop='".mysqli_escape_string($sct_thursday_stop)."',  sct_friday_start='".mysqli_escape_string($sct_friday_start)."',  sct_friday_stop='".mysqli_escape_string($sct_friday_stop)."',  sct_saturday_start='".mysqli_escape_string($sct_saturday_start)."',  sct_saturday_stop='".mysqli_escape_string($sct_saturday_stop)."' WHERE state_call_time_id='".mysqli_escape_string($state_call_time_id)."';";
																								$updateData = array(
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
																								$astDB->where('state_call_time_id', $state_call_time_id);
																								$rsltv1 = $astDB->update('vicidial_state_call_times', $updateData);
																								if($astDB->getRowCount() < 1){
																									$apiresults = array("result" => "Error: Try updating State Call Times Again");
																								} else {
																									$apiresults = array("result" => "success");
																									$log_id = log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified State Call Time: $state_call_time_id", $log_group, $astDB->getLastQuery());
																								}
																							} else {
																								$apiresults = array("result" => "Error: State Call Times doesn't exist");
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