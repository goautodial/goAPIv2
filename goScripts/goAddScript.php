<?php
   ####################################################
   #### Name: goAddScript.php                      ####
   #### Description: API to add Script	           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once("../goFunctions.php");
 
    ### POST or GET Variables
        $script_id = mysqli_real_escape_string($link, $_REQUEST['script_id']);
        $script_name = mysqli_real_escape_string($link, $_REQUEST['script_name']);
        $script_comments = mysqli_real_escape_string($link, $_REQUEST['script_comments']);
        $script_text = mysqli_real_escape_string($link, $_REQUEST['script_text']);
        $active = mysqli_real_escape_string($link, $_REQUEST['active']);
        //$campaign_id = $_REQUEST['campaign_id'];
		$user = mysqli_real_escape_string($link, $_REQUEST['user']);
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
        $goUser = mysqli_real_escape_string($link, $_REQUEST['goUser']);
		
		$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
		$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

    ### Default values 
    $defActive = array("Y","N");

    ### ERROR CHECKING 
        if($script_id == null) {
                $apiresults = array("result" => "Error: Set a value for Script ID not less than 3 characters.");
        } else {
				if($script_text == null){
							$apiresults = array("result" => "Error: Set a value for Script Text");
				} else {
						
						$groupId = go_get_groupid($goUser);
								
						if (!checkIfTenant($groupId)) {
								$ul = "";
						} else {
								$ul = "AND user_group='$groupId'";
						}
						
						$queryCheck = "SELECT script_id from vicidial_scripts where script_id='$script_id';";
						$sqlCheck = mysqli_query($link, $queryCheck);
						$countCheck = mysqli_num_rows($sqlCheck);
						
						if($countCheck <= 0){
						
								# getting the usergroup of the agent logged in
								$get_usergroup_query = "select user_group from vicidial_users where user='$user'";
								$init_getquery = mysqli_query($link, $get_usergroup_query);
								
								$usergroup_result = mysqli_fetch_array($init_getquery);
								$var_usergroup = $usergroup_result['user_group'];
								
								if($user != NULL){
										$user_group = $var_usergroup;
								}else {
										$user_group = $groupId;
								}
												
									$newQuery = "INSERT INTO vicidial_scripts(script_id, script_comments, script_name, active, user_group, script_text) VALUES('$script_id', '$script_comments', '$script_name', '$active', '$user_group', '$script_text');";
									$rsltv = mysqli_query($link, $newQuery);
						
								//$apiresults = array("usergroup" => $user_group, "query" => $newQuery);
								
								if($rsltv == false){
										$apiresults = array("result" => "Error: Add failed, check your details");
								} else {
										$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added New Script: $script_id", $log_group, $newQuery);
										$apiresults = array("result" => "success");
								}
								
								/*
								$newQueryTwo = "UPDATE vicidial_campaigns SET campaign_script = '$script_id' WHERE campaign_id = '$campaign_id';";
								$rsltvTwo = mysqli_query($link, $newQueryTwo);
				### Admin logs
													$SQLdate = date("Y-m-d H:i:s");
													$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Script $script_id','UPDATE vicidial_campaigns SET campaign_script = $script_id WHERE campaign_id = $campaign_id');";
													$rsltvLog = mysqli_query($linkgo, $queryLog);
								*/
						
						}else {
								$apiresults = array("result" => "Error: Add failed, Script already already exist!");
		
						}		
				}
		}

?>
