<?php

 ####################################################
 #### Name: goEditUserGroupAPI.php               ####
 #### Description: API to edit user group	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	


	 $url = "https://jameshv.goautodial.com/goAPI/goUserGroups/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = ""; #Username goes here. (required)
         $postfields["goPass"] = ""; #Password goes here. (required)
         $postfields["goAction"] = ""; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json. (required)
	 $postfields["user_group"] = ""; #Desired user group (required)
         $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	 $postfields["group_name"] = ""; #Desired name (required)
	 $postfields["group_level"] = ""; #Desired level (required)
	 $postfields["forced_timeclock_login"] = ""; #Y, N or ADMIN_EXEMPT (required)
	 $postfields["shift_enforcement"] = ""; #OFF, START or ALL only. (required)

	 user_group, group_name, group_level, forced_timeclock_login, shift_enforcement
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
		echo "Update Success";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
