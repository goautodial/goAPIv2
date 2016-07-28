<?php

 ####################################################
 #### Name: goAddDIDAPI.php                      ####
 #### Description: API to add new DID		 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
	 $url = "http://webrtc.goautodial.com/goAPI/goInbound/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "admin"; #Username goes here. (required)
         $postfields["goPass"] = "Yq48yHo2g0"; #Password goes here. (required)
         $postfields["goAction"] = "goAddDID"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json. (required)
         $postfields["user"] = "agent010"; #Desired user (required if did_route is AGENT)
         $postfields["user_unavailable_action"] = "PHONE"; #Desired user unavailable action (required if did_route is AGENT)
         $postfields["group_id"] = ""; #Desired group ID (required if did_route is IN-GROUP)
         $postfields["phone"] = ""; #Desired phone (required if did_route is PHONE)
         $postfields["server_ip"] = ""; #Desired server ip (required if did_route is PHONE)
         $postfields["menu_id"] = ""; #Desired menu id (required if did_route is IVR)
         $postfields["voicemail_ext"] = ""; #Desired voicemail (required if did_route is VOICEMAIL)
         $postfields["extension"] = ""; #Desired  extension (required if did_route is CUSTOM EXTENSION)
         $postfields["exten_context"] = ""; #Deisred context (required if did_route is CUSTOM EXTENSION)
         $postfields["did_pattern"] = "1234561234"; #Desired pattern (required)
         $postfields["did_description"] = "TEST2"; #Desired description(required)
         $postfields["did_route"] = "AGENT"; #'EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU', or'VMAIL_NO_INST' (required)
         $postfields["record_call"] = ""; #Desired call record (required)
         $postfields["user_group"] = "DOVAkhiinGroup"; #Assign to user group
         $postfields["did_active"] = "Y"; #Y or N (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value



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
	
   var_dump($data);
    
//	print_r($data);

	if ($output->result=="success") {
	   # Result was OK!
		echo "Added New DID ID: ";
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
