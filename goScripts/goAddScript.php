<?php
   ####################################################
   #### Name: goAddScript.php                      ####
   #### Description: API to add Script	           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
        $script_id = $_REQUEST['script_id'];
        $script_name = $_REQUEST['script_name'];
        $script_comments = $_REQUEST['script_comments'];
        $script_text = $_REQUEST['script_text'];
        $active = $_REQUEST['active'];
        //$campaign_id = $_REQUEST['campaign_id'];
		$user = $_REQUEST['user'];
        $ip_address = $_REQUEST['hostname'];
        $goUser = $_REQUEST['goUser'];


    ### Default values 
    $defActive = array("Y","N");


    ### ERROR CHECKING 
        if($script_id == null || strlen($script_id) < 3) {
                $apiresults = array("result" => "Error: Set a value for Script ID not less than 3 characters.");
        } else {
				if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$script_name) || $script_name == null){
						$apiresults = array("result" => "Error: Special characters found in script name and must not be empty");
				} else {
						if($script_text == null){
									$apiresults = array("result" => "Error: Set a value for Script Text");
						} else {
								### Check value compare to default values
								if(!in_array($active,$defActive) && $active != null) {
										$apiresults = array("result" => "Error: Default value for active is Y or N only.");
								} else {
				
								
								# getting the usergroup of the agent logged in
								$get_usergroup_query = "select user_group from vicidial_users where user='$user'";
								$init_getquery = mysqli_query($link, $get_usergroup_query);
								
								$usergroup_result = mysqli_fetch_array($init_getquery);
								$var_usergroup = $usergroup_result['user_group'];
								
								$groupId = go_get_groupid($goUser);
								
								if (!checkIfTenant($groupId)) {
										$ul = "";
								} else {
										$ul = "AND user_group='$groupId'";
								}
	
								$queryCheck = "SELECT script_id from vicidial_scripts where script_id='$script_id' $ul;";
								$sqlCheck = mysqli_query($link, $queryCheck);
								$countCheck = mysqli_num_rows($sqlCheck);
								
										if($countCheck <= 0){
										
											$newQuery = "INSERT INTO vicidial_scripts (script_id, script_comments, script_name, active, user_group, script_text) VALUES ('$script_id', '$script_comments', '$script_name', '$active', '$var_usergroup', '$script_text');";
											$rsltv = mysqli_query($link, $newQuery);
						
										if($user == NULL){
											$user_group = $var_usergroup;
										} else {
											$user_group = $groupId;
										}
												/*
												$newQueryTwo = "UPDATE vicidial_campaigns SET campaign_script = '$script_id' WHERE campaign_id = '$campaign_id';";
												$rsltvTwo = mysqli_query($link, $newQueryTwo);
								### Admin logs
																	$SQLdate = date("Y-m-d H:i:s");
																	$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Script $script_id','UPDATE vicidial_campaigns SET campaign_script = $script_id WHERE campaign_id = $campaign_id');";
																	$rsltvLog = mysqli_query($linkgo, $queryLog);*/
							
												if($rsltv == false){
													$apiresults = array("result" => "Error: Add failed, check your details");
												} else {
							
																	  $apiresults = array("result" => "success");
												}
										
										}else {
																  $apiresults = array("result" => "Error: Add failed, Script already already exist!");
						
										}


                                }  
						}
				}
		}

?>
