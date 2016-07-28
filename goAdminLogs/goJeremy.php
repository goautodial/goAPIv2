<?php

 ####################################################
 #### Name: goGetAdminLogsList.php 		 ####
 #### Description: API to get all adminlogs      ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Alexander Jim H. Abenoja       ####
 #### License: AGPLv2                            ####
 ####################################################


 	$url = "http://webrtc.goautodial.com/goAPI/goAdminLogs/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = "admin"; #Username goes here. (required)
	$postfields["goPass"] = "Yq48yHo2g0"; #Password goes here. (required)
	$postfields["goAction"] = "goGetAdminLogsList"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"] = "json"; #json. (required)

	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
	 curl_close($ch);
	 $output = json_decode($data);

var_dump($output);

	if ($output->result=="success") {
	   # Result was OK!

			for($i=0;$i<count($output->admin_log_id);$i++){
				echo $output->admin_log_id[$i]."</br>";
				echo $output->user[$i]."</br>";
				echo $output->ip_address[$i]."</br>";
				echo $output->event_date[$i]."</br>";
         			echo "</br>";
			}

	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
