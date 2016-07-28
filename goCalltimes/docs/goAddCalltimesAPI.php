<?php

 ####################################################
 #### Name: goAddCallitimesAPI.php               ####
 #### Description: API to add new Calltimes	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
 #### Written by: Warren Ipac Briones     	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
         $url = "https://encrypted.goautodial.com/goAPI/goCalltimes/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goAddCalltime"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["user_group"] = $_GET['user_group'];
         $postfields["ct_saturday_stop"] = $_GET['ct_saturday_stop'];
         $postfields["ct_saturday_start"] = $_GET['ct_saturday_start'];
         $postfields["ct_friday_stop"] = $_GET['ct_friday_stop'];
         $postfields["ct_friday_start"] = $_GET['ct_friday_start'];
         $postfields["ct_thursday_stop"] = $_GET['ct_thursday_stop'];
         $postfields["ct_thursday_start"] = $_GET['ct_thursday_start'];
         $postfields["ct_wednesday_stop"] = $_GET['ct_wednesday_stop'];
         $postfields["ct_wednesday_start"] = $_GET['ct_wednesday_start'];
         $postfields["ct_tuesday_stop"] = $_GET['ct_tuesday_stop'];
         $postfields["ct_tuesday_start"] = $_GET['ct_tuesday_start'];
         $postfields["ct_monday_stop"] = $_GET['ct_monday_stop'];
         $postfields["ct_monday_start,"] = $_GET['ct_monday_start'];
         $postfields["ct_sunday_stop"] = $_GET['ct_sunday_stop'];
         $postfields["ct_sunday_start"] = $_GET['ct_sunday_start'];
         $postfields["ct_default_stop"] = $_GET['ct_default_stop'];
         $postfields["ct_default_start"] = $_GET['ct_default_start'];
         $postfields["user_group"] = $_GET['user_group'];
         $postfields["call_time_comments"] = $_GET['call_time_comments'];
         $postfields["call_time_name"] = $_GET['call_time_name'];
         //$postfields["state_call_time_state"] = $_GET['state_call_time_state'];
         $postfields["call_time_id"] = $_GET['call_time_id'];

	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
	 curl_close($ch); //var_dump($data);
	 $output = json_decode($data);
	
	if ($output->result=="success") {
	   # Result was OK!
		echo "Added New Call Time ID: ".$_REQUEST['call_time_id'];	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
