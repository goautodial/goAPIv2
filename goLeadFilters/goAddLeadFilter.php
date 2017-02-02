<?php
   ####################################################
   #### Name: goAddLeadFilter.php                  ####
   #### Description: API to add new Lead Filter    ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("goFunctions.php");
 
    ### POST or GET Variables
        $lead_filter_id = $_REQUEST['lead_filter_id'];
        $lead_filter_name = $_REQUEST['lead_filter_name'];
        $lead_filter_comments = $_REQUEST['lead_filter_comments'];
        $lead_filter_sql = $_REQUEST['lead_filter_sql'];
        $user_group = $_REQUEST['user_group'];


		$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
		$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
		$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

    ### ERROR CHECKING 
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $lead_filter_id) || $lead_filter_id == null || $lead_filter_id < 4){
                $apiresults = array("result" => "Error: Special characters found in lead_filter_id, must not be empty and not less than 3 characters");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $lead_filter_name) || $lead_filter_name == null){
                $apiresults = array("result" => "Error: Special characters found in lead_filter_name and must not be empty");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $lead_filter_comments) || $lead_filter_comments == null){
                $apiresults = array("result" => "Error: Special characters found in lead_filter_comments and must not be empty");
        } else {
        if($lead_filter_sql == null){
                $apiresults = array("result" => "Error: lead_filter_sql must not be empty");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group) || $user_group == null){
                $apiresults = array("result" => "Error: Special characters found in user_group and must not be empty");
        } else {

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                $query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups WHERE user_group='".mysqli_real_escape_string($link, $user_group)."' $ul ORDER BY user_group LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
                $countResult = mysqli_num_rows($rsltv);

                if($countResult > 0) {
	
			$queryCheck = "SELECT lead_filter_id from vicidial_lead_filters where lead_filter_id='".mysqli_real_escape_string($link, $lead_filter_id)."';";
			$sqlCheck = mysqli_query($link, $queryCheck);
			$countCheck = mysqli_num_rows($sqlCheck);
				if($countCheck <= 0){	

					$newQuery = "INSERT INTO vicidial_lead_filters (lead_filter_id, lead_filter_name, lead_filter_comments, lead_filter_sql, user_group) VALUES ('".mysqli_real_escape_string($link, $lead_filter_id)."', '".mysqli_real_escape_string($link, $lead_filter_name)."', '".mysqli_real_escape_string($link, $lead_filter_comments)."', '".mysqli_real_escape_string($link, $lead_filter_sql)."', '".mysqli_real_escape_string($link, $user_group)."');";
					$rsltv = mysqli_query($link, $newQuery);
	      



	### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Voicemail: $voicemail_id','INSERT INTO vicidial_voicemail (voicemail_id,pass,fullname,active,email,user_group) VALUES ($voicemail_id,$pass,$fullname,$active,$email,$user_group)');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
						$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Lead Filter: $lead_filter_id", $log_group, $newQuery);

				    if($rsltv == false){
						$apiresults = array("result" => "Error: Add failed, check your details");
					} else {

                                          $apiresults = array("result" => "success");
					}
				}
				else {
                                          $apiresults = array("result" => "Error: Add failed, Lead Filter already exist!");

				}
                   } else {
                        $apiresults = array("result" => "Error: Invalid User Group");
		   }
                                        }
                                      
}
}
}
}

?>
