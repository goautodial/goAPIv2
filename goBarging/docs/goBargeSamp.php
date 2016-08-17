<?php

 ####################################################
 #### Name: goGetUserInfoAPI.php                 ####
 #### Description: API to edit specific user	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	$user_id = "admin";
	$url = "https://webrtc.goautodial.com/goAPI/goBarging/goAPI.php"; #URL to GoAutoDial API. (required)
        
		$postfields["goUser"] = "admin"; #Username goes here. (required)
        $postfields["goPass"] = "Yq48yHo2g0"; #Password goes here. (required)
        $postfields["goAction"] = "goGetAgentsOnCall"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
		$postfields["user_id"] = $user_id; #Desired User ID (required)

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
	
	print_r($data);

	if ($output->result=="success") {
	   # Result was OK!
                        for($i=0;$i<count($output->userno);$i++){
                                echo $output->userno[$i]."</br>";
                                echo $output->full_name[$i]."</br>";
                                echo $output->user_level[$i]."</br>";
                                echo $output->user_group[$i]."</br>";
                                echo $output->active[$i]."</br>";
                        }
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
