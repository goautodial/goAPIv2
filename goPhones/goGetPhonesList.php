<?php
    #######################################################
    #### Name: goGetPhonesList.php	               ####
    #### Description: API to get all Phone	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 1000; } else { $limit = $limit; }
	
	$groupId = go_get_groupid($session_user);
	
	if (!checkIfTenant($groupId)) {
		$ul='';
	} else {
		if ($groupId != 'ADMIN') {
			$ul = "WHERE user_group='$groupId'";
		}else{
			$ul = '';
		}
	}
	# generate random phone login
	$x = 0;
	$y = 0;
	$phone_login = '';
	while($x == $y){
		$random_digit = mt_rand(1000000000, 9999999999);
		$check_existing_phonelogins_query = "SELECT phone_login FROM vicidial_users WHERE phone_login = '$random_digit';";
		$check_existing_phonelogins_exec_query = mysqli_query($link, $check_existing_phonelogins_query);
		
		if($check_existing_phonelogins_exec_query == true){
			$y = 1;
			$phone_login = $random_digit;
		}
	}
   	//$query = "SELECT extension,protocol,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group FROM phones $ul ORDER BY extension LIMIT $limit;";
   	//$query = "SELECT p.extension, p.protocol, p.server_ip, p.dialplan_number, p.voicemail_id, p.status, p.active, p.fullname, p.messages, p.old_messages, p.user_group, vu.user_id, vu.user, vu.full_name  FROM phones as p, vicidial_users as vu WHERE  vu.phone_login = p.extension $ul ORDER BY extension LIMIT $limit;";
	$query = "SELECT extension, protocol, server_ip, active, messages, old_messages FROM phones $ul ORDER BY extension LIMIT $limit;";
	$rsltv = mysqli_query($link,$query);

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataExtension[] = $fresults['extension'];
		//$dataUserID[] = $fresults['user_id'];
		//$dataUser[] = $fresults['user'];
		$dataProtocol[] = $fresults['protocol'];// .$fresults['dial_method'].$fresults['active'];
		$dataServerIp[] = $fresults['server_ip'];
		//$dataDialplanNumber[] = $fresults['dialplan_number'];
		//$dataVoicemailId[] = $fresults['voicemail_id'];
		//$dataStatus[] = $fresults['status'];
		$dataActive[] = $fresults['active'];
		//$dataFullname[] = $fresults['full_name'];
		$dataMessages[] = $fresults['messages'];
		$dataOldMessages[] = $fresults['old_messages'];
		//$dataUserGroup[] = $fresults['user_group'];
	}
	
	//$apiresults = array("result" => "success", "extension" => $dataExtension, "userid" => $dataUserID, "user" => $dataUser, "protocol" => $dataProtocol, "server_ip" => $dataServerIp, "dialplan_number" => $dataDialplanNumber, "voicemail_id" => $dataVoicemailId, "status" => $dataStatus, "active" => $dataActive, "fullname" => $dataFullname, "messages" => $dataMessages, "old_messages" => $dataOldMessages, "user_group" => $dataUserGroup);
	$apiresults = array("result" => "success", "extension" => $dataExtension, "protocol" => $dataProtocol, "server_ip" => $dataServerIp, "active" => $dataActive, "messages" => $dataMessages, "old_messages" => $dataOldMessages, "available_phone" => $phone_login);
?>
