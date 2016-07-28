<?php
    #######################################################
    #### Name: goGetPhonesList.php	               ####
    #### Description: API to get all Phone	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include "goFunctions.php";
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }
 
    	$groupId = go_get_groupid($goUser);
    
	if (!checkIfTenant($groupId)) {
        	$ul='';
    	} else { 
		$ul = "WHERE user_group='$groupId'";  
	}

   	$query = "SELECT extension,protocol,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group FROM phones $ul ORDER BY extension LIMIT $limit;";
   	$rsltv = mysqli_query($link,$query);

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

?>
