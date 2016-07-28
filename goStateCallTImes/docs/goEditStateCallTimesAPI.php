<?php

 ####################################################
 #### Name: goEditStateCallTimeAPI.php                   ####
 #### Description: API to edit specific State Call Times####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	


	 $url = "https://encrypted.goautodial.com/goAPI/goStateCallTImes/goAPI.php"; # URL to GoAutoDial API file
	 $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goEditStateCallTime"; #action performed by the [[API:Functions]]
	 $postfields["responsetype"] = "json"; #json (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	 $postfields["state_call_time_id"] = $_GET["state_call_time_id"]; #
         $postfields["user_group"] = $_GET['user_group'];
         $postfields["sct_saturday_stop"] = $_GET['sct_saturday_stop'];
         $postfields["sct_saturday_start"] = $_GET['sct_saturday_start'];
         $postfields["sct_friday_stop"] = $_GET['sct_friday_stop'];
         $postfields["sct_friday_start"] = $_GET['sct_friday_start'];
         $postfields["sct_thursday_stop"] = $_GET['sct_thursday_stop'];
         $postfields["sct_thursday_start"] = $_GET['sct_thursday_start'];
         $postfields["sct_wednesday_stop"] = $_GET['sct_wednesday_stop'];
         $postfields["sct_wednesday_start"] = $_GET['sct_wednesday_start'];
         $postfields["sct_tuesday_stop"] = $_GET['sct_tuesday_stop'];
         $postfields["sct_tuesday_start"] = $_GET['ct_tuesday_start'];
         $postfields["sct_monday_stop"] = $_GET['sct_monday_stop'];
         $postfields["sct_monday_start,"] = $_GET['sct_monday_start'];
         $postfields["sct_sunday_stop"] = $_GET['sct_sunday_stop'];
         $postfields["sct_sunday_start"] = $_GET['sct_sunday_start'];
         $postfields["sct_default_stop"] = $_GET['sct_default_stop'];
         $postfields["sct_default_start"] = $_GET['sct_default_start'];
         $postfields["user_group"] = $_GET['user_group'];
         $postfields["state_call_time_comments"] = $_GET['state_call_time_comments'];
         $postfields["state_call_time_name"] = $_GET['state_call_time_name'];
         $postfields["state_call_time_state"] = $_GET['state_call_time_state'];
         $postfields["state_call_time_id"] = $_GET['state_call_time_id'];


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
		echo "Update Success";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
