<?php
   ####################################################
   #### Name: goAddPauseCode.php                   ####
   #### Description: API to add new Pause Code     ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include "goFunctions.php";
 
    ### POST or GET Variables
	$camp = $_REQUEST['pauseCampID'];
	$pause_code = $_REQUEST['pause_code'];
	$pause_code_name = $_REQUEST['pause_code_name'];
	$billable = strtoupper($_REQUEST['billable']);
	
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	
    ### Default values 
    $defBill = array('NO','YES','HALF');

    ### ERROR CHECKING 
        if($camp == null || strlen($camp) < 3) {
                $apiresults = array("result" => "Error: Set a value for CAMP ID not less than 3 characters.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_code) || $pause_code == null){
                $apiresults = array("result" => "Error: Special characters found in pause code and must not be empty");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_code_name)){
                $apiresults = array("result" => "Error: Special characters found in pause code name");
        } else {

                if(!in_array($billable,$defBill)) {
                        $apiresults = array("result" => "Error: Default value for billable is No, Yes or half only.");
                } else {

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

			$queryCheck = "SELECT * FROM vicidial_campaigns WHERE campaign_id='$camp'";
			$sqlCheck = mysqli_query($link,$queryCheck);
			$countCheck1 = mysqli_num_rows($sqlCheck);
				if($countCheck1 > 0){	
	
			$queryCheck = "SELECT * FROM vicidial_pause_codes WHERE campaign_id='$camp' AND pause_code = '$pause_code';";
			$sqlCheck = mysqli_query($link,$queryCheck);
			$countCheck = mysqli_num_rows($sqlCheck);
				if($countCheck <= 0){	

					$newQuery = "INSERT INTO vicidial_pause_codes (pause_code,pause_code_name,campaign_id,billable) VALUES ('$pause_code', '$pause_code_name', '$camp', '$billable');";
					$rsltv = mysqli_query($link,$newQuery);
	      



	### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Pause Code: $camp','INSERT INTO vicidial_pause_codes (pause_code,pause_code_name,campaign_id,billable) VALUES ($pause_code,$pause_code_name,$camp,$billable)');";
                                        //$rsltvLog = mysqli_query($linkgo,$queryLog);
						$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Pause Code $pause_code under Campaign $camp", $log_group, $newQuery);

				        if($rsltv == false){
						$apiresults = array("result" => "Error: Add failed, check your details");
					} else {

                                          $apiresults = array("result" => "success");
					}
				} else {
                                          $apiresults = array("result" => "Error: Add failed, Pause Code already exist!");

				}
				} else {
                                          $apiresults = array("result" => "Error: Add failed, Campaign ID does not exist!");

				}
                                        }
                                      

}
}
}

?>
