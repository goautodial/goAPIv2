<?php
    #######################################################
    #### Name: goDeletePauseCode.php	               ####
    #### Description: API to delete specific Pause Code ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include "goFunctions.php";
    
    ### POST or GET Variables
        $camp = $_REQUEST['pauseCampID'];
        $code = $_REQUEST['pause_code'];
        $ip_address = $_REQUEST['hostname'];
    
    ### Check Voicemail ID if its null or empty
	if($camp == null || $code == null) { 
		$apiresults = array("result" => "Error: Set a value for Campaign ID and Pause Code."); 
	} else {
 
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }


   		$queryOne = "SELECT pause_code, campaign_id FROM vicidial_pause_codes WHERE campaign_id='$camp' AND pause_code='$code';";
   		$rsltvOne = mysqli_query($link,$queryOne);
		$countResult = mysqli_num_rows($rsltvOne);

		if($countResult > 0) {
				$deleteQuery = "DELETE FROM vicidial_pause_codes WHERE campaign_id = '$camp';"; 
   				$deleteResult = mysqli_query($link,$deleteQuery);
				$deleteQueryTwo = "DELETE FROM vicidial_pause_codes WHERE campaign_id = '$camp' AND pause_code='$code';"; 
   				$deleteResultTwo = mysqli_query($link,$deleteQueryTwo);
				//echo $deleteQuery;

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Pause Code $code','DELETE FROM vicidial_pause_code WHERE campaign_id=$camp AND pause_code=$code;');";
                                        $rsltvLog = mysqli_query($linkgo,$queryLog);


				$apiresults = array("result" => "success");

		} else {
			$apiresults = array("result" => "Error: Pause Code doesn't exist.");
		}
	}//end
?>
