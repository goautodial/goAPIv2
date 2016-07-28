<?php

 ####################################################
 #### Name: goAddMOHAPI.php                      ####
 #### Description: API to add new MOH		 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	

	 $url = "https://162.254.144.92/goAPI/goMusicOnHold/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = ""; #Username goes here. (required)
         $postfields["goPass"] = ""; #Password goes here. (required)
         $postfields["goAction"] = "goAddMOH"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json. (required)
         $postfields["moh_id"] = $_POST['moh_id'];// ""; #Desired Music On Hold (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["moh_name"] = $_POST['moh_name']; //""; #Desired name (required)
         $postfields["user_group"] = $_POST['user_group']; //""; #assign to user group
	 $postfields["active"] = $_POST['active']; //""; #Y or N (required) 
	

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
		echo "Added New MOH ID: ".$_POST['moh_id'];	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
