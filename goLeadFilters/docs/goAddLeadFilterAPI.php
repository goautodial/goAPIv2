<?php

 ####################################################
 #### Name: goAddLeadFilterAPI.php               ####
 #### Description: API to add new lead filter	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
         $url = "https://encrypted.goautodial.com/goAPI/goLeadFilters/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goAddLeadFilter"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json (required)
         $postfields["filters"] = $_GET['filters']; #Default value
         $postfields["lead_filter_id"] = $_GET['lead_filter_id'];
         $postfields["lead_filter_name"] = $_GET['lead_filter_name'];
         $postfields["lead_filter_comments"] = $_GET['lead_filter_comments'];
         $postfields["lead_filter_sql"] = $_GET['lead_filter_sql'];
         $postfields["user_group"] = $_GET['user_group'];


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
		echo "Added New Filter ID: ".$_REQUEST['lead_filter_id'];	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
