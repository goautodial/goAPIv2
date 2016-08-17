<?php

 ####################################################
 #### NamegoEditVoicemail.php                   ####
 #### Description API to edit specific voicemail####
 #### Version 0.9                               ####
 #### Copyright GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by Jeremiah Sebastian Samatra	 ####
 #### License AGPLv2                            ####
 ####################################################
	


	 $url = "https://webrtc.goautodial.com/goAPI/goCalltimes/goAPI.php"; # URL to GoAutoDial API file
	 $postfields["goUser"] = "admin"; #Username goes here. (required)
     $postfields["goPass"] = "Yq48yHo2g0"; #Password goes here. (required)
     $postfields["goAction"] = "goEditCalltime"; #action performed by the [[APIFunctions]]
	 $postfields["responsetype"] = "json"; #json (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
     
    $postfields["call_time_id"]       = '1am-2am'; #Desired uniqueid. (required)
    $postfields["call_time_name"]     = 'TEST EDIT';
    $postfields["call_time_comments"] = 'test comment edit THIS';
    $postfields["user_group"]         = 'AGENTS';
    
    $start_default =  "1200";
    $stop_default =  "1200";

    $start_sunday =  "1200";
    $stop_sunday =  "1200";

    $start_monday = "1200";
    $stop_monday = "1200";

    $start_tuesday =  "1200";
    $stop_tuesday ="1200";

    $start_wednesday =  "1200";
    $stop_wednesday = "1200";

    $start_thursday = "1200";
    $stop_thursday = "1200";

    $start_friday = "1200";
    $stop_friday = "1200";

    $start_saturday = "1200";
    $stop_saturday = "0100";
    
    $postfields["ct_default_start"]   = $start_default;
    $postfields["ct_default_stop"]    = $stop_default;

    $postfields["ct_sunday_start"]    = $start_sunday;
    $postfields["ct_sunday_stop"]     = $stop_sunday;

    $postfields["ct_monday_start"]    = $start_monday;
    $postfields["ct_monday_stop"]     = $stop_monday;

    $postfields["ct_tuesday_start"]   = $start_tuesday;
    $postfields["ct_tuesday_stop"]    = $stop_tuesday;

    $postfields["ct_wednesday_start"] = $start_wednesday;
    $postfields["ct_wednesday_stop"]  = $stop_wednesday;

    $postfields["ct_thursday_start"]  = $start_thursday;
    $postfields["ct_thursday_stop"]   = $stop_thursday;

    $postfields["ct_friday_start"]    = $start_friday;
    $postfields["ct_friday_stop"]     = $stop_friday;

    $postfields["ct_saturday_start"]  = $start_saturday;
    $postfields["ct_saturday_stop"]   = $stop_saturday;


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
	
    var_dump($output);
    
//	print_r($data);

	if ($output->result=="success") {
	   # Result was OK!
		echo "Update Success";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
