<?php

 ####################################################
 #### Name:goEditVoicemail.php                   ####
 #### Description: API to edit specific voicemail####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	


	 $url = "https://encrypted.goautodial.com/goAPI/goVoicemails/goAPI.php"; # URL to GoAutoDial API file
	 $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goEditVoicemail"; #action performed by the [[API:Functions]]
	 $postfields["responsetype"] = "json"; #json (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	 $postfields["pass"] = $_GET["pass"]; #
	 $postfields["fullname"] = $_GET["fullname"]; #
	 $postfields["email"] = $_GET["email"]; #
	 $postfields["active"] = $_GET["active"]; #
	 $postfields["delete_vm_after_email"] = $_GET["delete_vm_after_email"]; #
	 $postfields["voicemail_id"] = $_GET["voicemail_id"]; #

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
