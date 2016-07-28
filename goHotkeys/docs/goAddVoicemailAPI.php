<?php

 ####################################################
 #### Name: goAddVoicemailAPI.php                ####
 #### Description: API to add new Voicemail	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
         $url = "https://encrypted.goautodial.com/goAPI/goVoicemails/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goAddVoicemail"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["user_group"] = $_GET['user_group'];
	 $postfields["voicemail_id"] = $_GET['voicemail_id']; #Voicemail ID
	 $postfields["pass"] = $_GET['pass']; #password for voicemail
	 $postfields["fullname"] = $_GET['fullname']; #Full name
	 $postfields["email"] = $_GET['email']; #Email
	 $postfields["user_group"] = $_GET['user_group']; #User group
	 $postfields["active"] = $_GET['active']; #Status. Either Y or N only



	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
	 curl_close($ch);
	 $output = json_decode($data);
	

	if ($output->result=="success") {
	   # Result was OK!
		echo "Added New Voicemail ID: ".$_REQUEST['voicemail_id'];	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
