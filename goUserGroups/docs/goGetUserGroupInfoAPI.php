<?php

 ####################################################
 #### Name: goGetUserGroupInfoAPI.php            ####
 #### Description: API to get specific User Group####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################

        $url = "https://YOUR_URL/goAPI/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = ""; #Username goes here. (required)
        $postfields["goPass"] = ""; #Password goes here. (required)
        $postfields["goAction"] = ""; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["agent_id"] = ""; #Desired Agent ID. (required)
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
                        for($i=0;$i<count($output->user_group);$i++){
                                echo $output->user_group[$i]."</br>";
                                echo $output->group_name[$i]."</br>";
                                echo $output->group_type[$i]."</br>";
                                echo $output->forced_timeclock_login[$i]."</br>";
                        }
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
