<?php

 ####################################################
 #### Name: goEditDIDAPI.php                     ####
 #### Description: API to edit specific DID      ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	

	 $url = "https://jameshv.goautodial.com/goAPI/goInbound/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = ""; #Username goes here. (required)
         $postfields["goPass"] = ""; #Password goes here. (required)
         $postfields["goAction"] = ""; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json. (required)
         $postfields["user"] = ""; #Desired user (required if did_route is AGENT)
         $postfields["user_unavailable_action"] = ""; #Desired user unavailable action (required if did_route is AGENT)
         $postfields["group_id"] = ""; #Desired group ID (required if did_route is IN-GROUP)
         $postfields["phone"] = ""; #Desired phone (required if did_route is PHONE)
         $postfields["server_ip"] = ""; #Desired server ip (required if did_route is PHONE)
         $postfields["menu_id"] = ""; #Desired menu id (required if did_route is IVR)
         $postfields["voicemail_ext"] = ""; #Desired voicemail (required if did_route is VOICEMAIL)
         $postfields["extension"] = ""; #Desired  extension (required if did_route is CUSTOM EXTENSION)
         $postfields["exten_context"] = ""; #Deisred context (required if did_route is CUSTOM EXTENSION)
         $postfields["did_pattern"] = ""; #Desired pattern (required)
         $postfields["did_description"] = ""; #Desired description(required)
         $postfields["did_route"] = ""; #'EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU', or'VMAIL_NO_INST' (required)
         $postfields["record_call"] = ""; #Desired call record (required)
         $postfields["user_group"] = ""; #Assign to user group
         $postfields["did_active"] = ""; #Y or N (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	 $postfields["did_id"] = ""; #Desired ID (required)


	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
	 curl_close($ch);
	 $output = json_decode($data);
	
//	print_r($data);

	if ($output->result=="success") {
	   # Result was OK!
		echo "Update Success";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
