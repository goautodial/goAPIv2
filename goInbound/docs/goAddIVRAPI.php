<?php

 ####################################################
 #### Name: goAddListAPI.php                     ####
 #### Description: API to add new List		 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################


	 $url = "https://jameshv.goautodial.com/goAPI/goInbound/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = ""; #Username goes here. (required)
         $postfields["goPass"] = ""; #Password goes here. (required)
         $postfields["goAction"] = ""; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json. (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["menu_name"] = ""; #Desired name (required)
         $postfields["menu_id"] = ""; #Desired menu ID (required)
         $postfields["custom_dialplan_entry"] = ""; #Desired dialplan entry (required)
         $postfields["menu_prompt"] = ""; #Desired prompt (required)
         $postfields["menu_timeout"] = ""; #Desired timeout (Required)
         $postfields["menu_timeout_prompt"] = ""; #Desired time out prompt (required)
         $postfields["menu_invalid_prompt"] = ""; #Desired invali prompt (required)?
         $postfields["menu_repeat"] = ""; #Desired menu repeat (required)
         $postfields["call_time_id"] = ""; #Desired time (required)
         $postfields["tracking_group"] = ""; #Desired tracking group (required)
         $postfields["user_group"] = ""; #Assign to user group (required


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
		echo "Added New IVR Menu ID";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
