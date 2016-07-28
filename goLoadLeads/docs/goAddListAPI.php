<?php

 ####################################################
 #### Name: goAddListAPI.php                     ####
 #### Description: API to add new List		 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	

	$url = "https://YOUR_URL/goAPI/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = ""; #Username goes here. (required)
        $postfields["goPass"] = ""; #Password goes here. (required)
        $postfields["goAction"] = ""; #action performed by the [[API:Functions]]
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["list_id"] = ""; #Desired list id. (required) 
        $postfields["list_name"] = ""; #Desired name. (required)
        $postfields["list_description"] = ""; #Desired description. (required)
        $postfields["campaign_id"] = ""; #Assign to campaign. (required)
        $postfields["active"] = ""; #Y or N (required)
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
	
//	print_r($data);

	if ($output->result=="success") {
	   # Result was OK!
		echo "Added New List ID ";
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
