<?php

 ####################################################
 #### Name: goAddUserAPI.php	                 ####
 #### Description: API to add new User		 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	

	 $url = "https://162.254.144.92/goAPI/goUsers/goAPI.php"; # URL to GoAutoDial API filem (required)
	 $postfields["goUser"] = "admin"; #Username goes here. (required)
	 $postfields["goPass"] = "goautodial"; #Password goes here. (required)
	 $postfields["goAction"] = "goAddUser"; #action performed by the [[API:Functions]]
	 $postfields["responsetype"] = "json"; #response type by the [[API:Functions]]
         $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["user"] = "goAPItest"; #Desired value for user (required)
         $postfields["pass"] = "goAPItest"; #Desired value for pass (required)
         $postfields["full_name"] = "API"; #Desired full name (required)
         $postfields["user_group"] = "1"; #Assign to user group (required)
         $postfields["active"] = "N"; #Y or N (required)


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
	

	if ($output->result=="success") {
	   # Result was OK!
		echo "Added New User ID: ";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
