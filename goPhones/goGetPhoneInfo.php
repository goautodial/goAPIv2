<?php
    #######################################################
    #### Name: goGetMOHInfo.php		               ####
    #### Description: API to get specific MOH	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian Samatra        ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    ### POST or GET Variables
    $exten_id = $_REQUEST['exten_id'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
    
    ### Check moh_id if its null or empty
	if($exten_id == null) { 
		$apiresults = array("result" => "Error: Set a value for EXTEN ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE extension='$exten_id'";
    		} else { 
			$ul = "WHERE extension='$exten_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT extension,protocol,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group FROM phones $ul ORDER BY extension LIMIT 1;";
   		$rsltv = mysqli_query($link,$query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataExtension[] = $fresults['extension'];
                $dataProtocol[] = $fresults['protocol'];// .$fresults['dial_method'].$fresults['active'];
                $dataServerIp[] = $fresults['server_ip'];
                $dataDialplanNumber[] = $fresults['dialplan_number'];
                $dataVoicemailId[] = $fresults['voicemail_id'];
                $dataStatus[] = $fresults['status'];
                $dataActive[] = $fresults['active'];
                $dataFullname[] = $fresults['fullname'];
                $dataMessages[] = $fresults['messages'];
                $dataOldMessages[] = $fresults['old_messages'];
                $dataUserGroup[] = $fresults['user_group'];
                $apiresults = array("result" => "success", "extension" => $dataExtension, "protocol" => $dataProtocol, "server_ip" => $dataServerIp, "dialplan_number" => $dataDialplanNumber, "voicemail_id" => $dataVoicemailId, "status" => $dataStatus, "active" => $dataActive, "fullname" => $dataFullname, "messages" => $dataMessages, "old_messages" => $dataOldMessages, "user_group" => $dataUserGroup);

			}
			
			$log_id = log_action($linkgo, 'VIEW', $log_user, $ip_address, "Viewed the info of Phone: $exten_id", $log_group);
		} else {
			$apiresults = array("result" => "Error: Phone doesn't exist.");
		}
	}
?>
