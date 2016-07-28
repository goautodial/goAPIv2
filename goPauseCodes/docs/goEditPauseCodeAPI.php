<?php

 ####################################################
 #### Name: goEditPauseCodeAPI.php               ####
 #### Description: API to edit specific Pause Code####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
         $url = "https://gadcs.goautodial.com/goAPI/goPauseCodes/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "admin"; #Username goes here. (required)
         $postfields["goPass"] = "kam0teque1234"; #Password goes here. (required)
         $postfields["goAction"] = "goEditPauseCode"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json (required)
         $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["pauseCampID"] = $_GET['pauseCampID'];
         $postfields["pause_code"] = $_GET['pause_code']; #VPause Code
         $postfields["pause_code_name"] = $_GET['pause_code_name']; #pause code name
         $postfields["billable"] = $_GET['billable']; #FNo, YES or HALF


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
		echo "Update Success";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
