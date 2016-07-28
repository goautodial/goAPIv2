<?php

 ####################################################
 #### Name: goAddDispostionAPI.php                ####
 #### Description: API to add new Disposition 	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
         $url = "https://encrypted.goautodial.com/goAPI/goDispositions/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goAddDisposition"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["status"] = $_GET['status'];
         $postfields["status_name"] = $_GET['status_name'];
         $postfields["selectable"] = $_GET['selectable'];
         $postfields["campaign_id"] = $_GET['campaign_id'];
         $postfields["human_answered"] = $_GET['human_answered'];
         $postfields["sale"] = $_GET['sale'];
         $postfields["dnc"] = $_GET['dnc'];
         $postfields["customer_contact"] = $_GET['customer_contact'];
         $postfields["not_interested"] = $_GET['not_interested'];
         $postfields["unworkable"] = $_GET['unworkable'];
         $postfields["scheduled_callback"] = $_GET['scheduled_callback'];


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
		echo "Added New Campaign Status: ".$_REQUEST['campaign_id'];	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
