<?php

 ####################################################
 #### Name: goAddVoicemailAPI.php                ####
 #### Description: API to add new Voicemail	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
         $url = "https://encrypted.goautodial.com/goAPI/goPauseCodes/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goAddPauseCode"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["pauseCampID"] = $_GET['pauseCampID'];
	 $postfields["pause_code"] = $_GET['pause_code']; #VPause Code
	 $postfields["pause_code_name"] = $_GET['pause_code_name']; #pause code name
	 $postfields["billable"] = $_GET['billable']; #FNo, YES or HALF



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
		echo "Added New Pause Code: ".$_REQUEST['pauseCampID'];	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
