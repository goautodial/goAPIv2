<?php
    #######################################################
    #### Name: goDeleteScript.php	               ####
    #### Description: API to delete specific Script ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
        $server_id = mysqli_real_escape_string($link, $_REQUEST['server_id']);
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
		
        $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
        $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    ### Check Voicemail ID if its null or empty
	if($server_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Server ID."); 
	} else {
 
		$groupId = go_get_groupid($goUser);

		if (!checkIfTenant($groupId)) {
				$ul = "";
		} else {
				$ul = "AND user_group='$groupId'";
		}
		
   		$queryOne = "SELECT server_id FROM servers where server_id='$server_id' $ul;";
   		$rsltvOne = mysqli_query($link, $queryOne);
		$countResult = mysqli_num_rows($rsltvOne);

		if($countResult > 0) {
				$deleteQuery = "DELETE FROM servers WHERE server_id= '$server_id';"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);
				
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Server ID: $server_id", $log_group, $deleteQuery);
				
				$apiresults = array("result" => "success");

		} else {
			$apiresults = array("result" => "Error: Server doesn't exist.");
		}
	}//end
?>
