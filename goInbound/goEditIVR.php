<?php
   ####################################################
   #### Name: goEditCampaign.php                   ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
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
		
		$goUser = $_REQUEST['goUser'];
		$ip_address = $_REQUEST['hostname'];
		
		$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
		$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
		
		$items = $_REQUEST['items'];
		$exploded_items = explode("|", $items);
		$filtered_items = array_filter($exploded_items);
   //menu_name, menu_prompt, menu_timeout, menu_timeout_prompt, menu_invalid_prompt, menu_repeat, menu_time_check, call_time_id, track_in_vdac, tracking_group, custom_dialplan_entry, menu_id
    ### Default values 
    $defActive = array("Y","N");

#############################
        if($menu_id == null) {
                $apiresults = array("result" => "Error: Set a value for menu ID.");
        } else {
				if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_name)){
						$apiresults = array("result" => "Error: Special characters found in menu_name");
				} else {
						if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_timeout)){
								$apiresults = array("result" => "Error: Special characters found in menu_timeout");
						} else {
								if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_repeat)){
										$apiresults = array("result" => "Error: Special characters found in menu_repeat");
								} else {
										if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $call_time_id)){
												$apiresults = array("result" => "Error: Special characters found in call_time_id");
										} else {
												if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $tracking_group)){
														$apiresults = array("result" => "Error: Special characters found in tracking_group");
												} else {
														if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $custom_dialplan_entry)){
																$apiresults = array("result" => "Error: Special characters found in custom_dialplan_entry");
														} else {
																if($menu_time_check < 0 && $menu_time_check != null || $menu_time_check > 1 && $menu_time_check != null) {
																		$apiresults = array("result" => "Error: menu_time_check Value should be 0 or 1");
																} else {
																		if($track_in_vdac < 0 && $track_in_vdac != null || $track_in_vdac > 1 && $track_in_vdac != null) {
																				$apiresults = array("result" => "Error: track_in_vdac Value should be 0 or 1");
																		} else {
									/*	 $items = $values;
														foreach (explode("&",$items) as $item)
														{
																list($var,$val) = explode("=",$item,2);
																if (strlen($val) > 0)
																{
						
																		if ($var!="menu_id")
																				$itemSQL .= "$var='".str_replace('+',' ',mysql_real_escape_string($val))."', ";
						
																		if ($var=="menu_id")
																				$callmenu_data="$val";
						
																}
														}
														$itemSQL = rtrim($itemSQL,', ');
														*/
														
														$query = "UPDATE vicidial_call_menu
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
														$resultQuery = mysqli_query($link, $query);
														
														#query for call menu options
														$return_query = "";
														$delete_exoptions = "DELETE FROM vicidial_call_menu_options WHERE menu_id = '$menu_id';";
														$delete_callmenu_entry = mysqli_query($link, $delete_exoptions);
														
														for($i=0; $i < count($filtered_items); $i++){
															$options = explode("+", $filtered_items[$i]);
															if($options[0] !== ''){
																$query_options = "INSERT INTO vicidial_call_menu_options (menu_id,option_value,option_description,option_route,option_route_value, option_route_value_context) values('$menu_id', '$options[0]', '$options[1]', '$options[2]', '$options[3]', '$options[4]');";
																$return_query .= $query_options."+++++";
																$query_callmenu_entry = mysqli_query($link, $query_options);
															}
														}
														
														//reload asterisk
														$queryUpdateAsterisk = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y';";
														$resultVSC = mysqli_query($link, $queryUpdateAsterisk);
								
								if($resultQuery){
											$apiresults = array("result" => "success");
											
											### Admin logs
												//$SQLdate = date("Y-m-d H:i:s");
												//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Call Menu ID $menu_id','UPDATE vicidial_call_menu SET menu_name=$menu_name,  menu_prompt=$menu_prompt,  menu_timeout=$menu_timeout,  menu_timeout_prompt=$menu_timeout_prompt,  menu_invalid_prompt=$menu_invalid_prompt,  menu_repeat=$menu_repeat,  menu_time_check=$menu_time_check,  call_time_id=$call_time_id,  track_in_vdac=$track_in_vdac,  tracking_group=$tracking_group,  custom_dialplan_entry=$custom_dialplan_entry WHERE menu_id=$callmenu_data;')";
												//$rsltvLog = mysqli_query($linkgo, $queryLog);
											$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified Call Menu ID $menu_id", $log_group, $query);
								} else {
											$apiresults = array("result" => "Error: Failed to update");
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

#############################
?>
