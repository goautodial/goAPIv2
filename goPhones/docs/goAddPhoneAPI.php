<?php

 ####################################################
 #### Name: goAddListAPI.php                     ####
 #### Description: API to add new List		 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	


	 $url = "https://jameshv.goautodial.com/goAPI/goPhones/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = ""; #Username goes here. (required)
         $postfields["goPass"] = ""; #Password goes here. (required)
         $postfields["goAction"] = ""; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json. (required)
         $postfields["extension"] = ""; #Deisred extension (required)
         $postfields["server_ip"] = ""; #Desired server_ip (required)
         $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["pass"] = ""; #Desired password (required)
         $postfields["protocol"] = ""; #SIP, Zap, IAX2, or EXTERNAL. (required)
         $postfields["dialplan_number"] = ""; #Desired dialplan number (required)
         $postfields["voicemail_id"] = ""; #Desired voicemail (required)
         $postfields["status"] = ""; #ACTIVE, SUSPENDED, CLOSED, PENDING, or ADMIN (required)
         $postfields["active"] = ""; #Y or N (required)
         $postfields["fullname"] = ""; #Desired full name (required)
         $postfields["messages"] = ""; #Desire message (required)
         $postfields["old_messages"] = ""; #Desired old message (required)
         $postfields["user_group"] = ""; #Assign to user group (required)
         

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
		echo "Added New Phone ID";
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
