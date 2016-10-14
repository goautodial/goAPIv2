<?php
   ####################################################
   #### Name: goAddVoicemail.php                   ####
   #### Description: API to add new Voicemail      ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
	$voicemail_id = $_REQUEST['voicemail_id'];
        $pass = $_REQUEST['pass'];
        $fullname = $_REQUEST['fullname'];
        $email = $_REQUEST['email'];
        $user_group = $_REQUEST['user_group'];
        $active = strtoupper($_REQUEST['active']);
        $ip_address = $_REQUEST['hostname'];
        $goUser = $_REQUEST['goUser'];

        $voicemail_id = mysqli_real_escape_string($link, $voicemail_id);
        $pass = mysqli_real_escape_string($link, $pass);
        $fullname = mysqli_real_escape_string($link, $fullname);
        $email = mysqli_real_escape_string($link, $email);
        $user_group = mysqli_real_escape_string($link, $user_group);
        $active = mysqli_real_escape_string($link, $active);
       
    ### Default values 
    $defActive = array("Y","N");


    ### ERROR CHECKING 
        if($voicemail_id == null || strlen($voicemail_id) < 3) {
                $apiresults = array("result" => "Error: Set a value for VOICEMAIL ID not less than 3 characters.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $fullname) || $fullname == null){
                $apiresults = array("result" => "Error: Special characters found in fullname and must not be empty");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_id)){
                $apiresults = array("result" => "Error: Special characters found in voicemail_id");
        } else {

                if(!in_array($active,$defActive)) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $apiresults = array("result" => "Error: Invalid email format.");
                } else {

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

		 $query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups WHERE user_group='".$user_group."' $ul ORDER BY user_group LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
                $countResult = mysqli_num_rows($rsltv);

                if($countResult > 0) {
	
			$queryCheck = "SELECT voicemail_id from vicidial_voicemail where voicemail_id='".$voicemail_id."';";
			$sqlCheck = mysqli_query($link, $queryCheck);
			$countCheck = mysqli_num_rows($sqlCheck);
				if($countCheck <= 0){	

					$newQuery = "INSERT INTO vicidial_voicemail (voicemail_id, pass, fullname, active, email, user_group) VALUES ('".$voicemail_id."', '".$pass."', '".$fullname."', '".$active."', '".$email."', '".$user_group."');";
					$rsltv = mysqli_query($link, $newQuery);
	      



	### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Voicemail: $voicemail_id','INSERT INTO vicidial_voicemail (voicemail_id,pass,fullname,active,email,user_group) VALUES ($voicemail_id,$pass,$fullname,$active,$email,$user_group)');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);

				        if($rsltv == false){
						$apiresults = array("result" => "Error: Add failed, check your details");
					} else {

                                          $apiresults = array("result" => "success");
					}
				}
				else {
                                          $apiresults = array("result" => "Error: Add failed, Voicemail already already exist!");

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
