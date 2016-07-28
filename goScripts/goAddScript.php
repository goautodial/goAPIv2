<?php
   ####################################################
   #### Name: goAddScript.php                      ####
   #### Description: API to add Script	           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("goFunctions.php");
 
    ### POST or GET Variables
        $script_id = $_REQUEST['script_id'];
        $script_name = $_REQUEST['script_name'];
        $script_comments = $_REQUEST['script_comments'];
        $script_text = $_REQUEST['script_text'];
        $active = $_REQUEST['active'];
        $campaign_id = $_REQUEST['campaign_id'];
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



                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }
	
			$queryCheck = "SELECT script_id from vicidial_scripts where script_id='".mysqli_escape_string($script_id)."';";
			$sqlCheck = mysqli_query($link, $queryCheck);
			$countCheck = mysqli_num_rows($sqlCheck);
				if($countCheck <= 0){		      

					$newQuery = "INSERT INTO vicidial_scripts (script_id, script_comments, script_name, active, script_text) VALUES ('".mysqli_escape_string($script_id)."', '".mysqli_escape_string($script_comments)."', '".mysqli_escape_string($script_name)."', '".mysqli_escape_string($active)."', '".mysqli_escape_string($script_text)."');";
					$rsltv = mysqli_query($link, $newQuery);

				if($user_group == NULL){
					$user_group = "script";
				} else {
					$user_group = $groupId;
				}
					$newQueryOne = "INSERT INTO go_scripts (script_id, account_num) VALUES ('".mysqli_escape_string($script_id)."', '".mysqli_escape_string($user_group)."';";
					$rsltvOne = mysqli_query($link, $newQueryOne);
				if($campaign_id = NULL){
					$campaign_id = "";
				}
					$newQueryTwo = "UPDATE vicidial_campaigns SET campaign_script = '".mysqli_escape_string($script_id)."' WHERE campaign_id = '".mysqli_escape_string($campaign_id)."';";
					$rsltvTwo = mysqli_query($link, $newQueryTwo);
	### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Script $scriptid','UPDATE vicidial_campaigns SET campaign_script = $script_id WHERE campaign_id = $campaign_id');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);

				        if($rsltv == false){
						$apiresults = array("result" => "Error: Add failed, check your details");
					} else {

                                          $apiresults = array("result" => "success");
					}
				}
				else {
                                          $apiresults = array("result" => "Error: Add failed, Script already already exist!");

				}


                                        }
                                      
}
}
}

?>
