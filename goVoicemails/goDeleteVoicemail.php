<?php
    #######################################################
    #### Name: goDeleteVoicemail.php	               ####
    #### Description: API to delete specific Voicemail ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
        $voicemail = mysqli_real_escape_string($link, $_REQUEST['voicemail_id']);
        $voicemail_id = mysqli_real_escape_string($link, $_REQUEST['voicemail_id']);
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
		
		$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
		$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    ### Check Voicemail ID if its null or empty
	if($voicemail_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Voicemail ID."); 
	} else {
 
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }


                                $query = "SELECT active_voicemail_server from system_settings";
				$resultIP = mysqli_query($link, $query);
			while($fresults = mysqli_fetch_array($link, $resultIP)){
				$server_ip = mysqli_real_escape_string($fresults['active_voicemail_server']);
				if($server_ip != null){$ip_address = $server_ip; }
				
			}
				
   		$queryOne = "SELECT voicemail_id FROM vicidial_voicemail $ul where voicemail_id='$voicemail';";
   		$rsltvOne = mysqli_query($link, $queryOne);
		$countResult = mysqli_num_rows($rsltvOne);

		if($countResult > 0) {
				$deleteQuery = "DELETE FROM vicidial_voicemail WHERE voicemail_id = '$voicemail';"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);
				//echo $deleteQuery;

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Voicemail ID $dataVoicemailID','DELETE FROM vicidial_voicemails WHERE voicemail_id=$dataVoicemailID;');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Voicemail ID: $voicemail", $log_group, $deleteQuery);


				$apiresults = array("result" => "success");

		} else {
			$apiresults = array("result" => "Error: Voicemail doesn't exist.");
		}
	}//end
?>
