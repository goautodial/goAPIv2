<?php

 ####################################################
 #### Name: goGetViewRecordingsAPI.php           ####
 #### Description: API to get recording information##
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################

 	$url = "https://YOUR_URL/goAPI/goCallRecordings/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = ""; #Username goes here. (required)
        $postfields["goPass"] = ""; #Password goes here. (required)
        $postfields["goAction"] = ""; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["leadid"] = ""; #Desired lead id. (required) 


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
			for($i=0;$i<count($output->recording_id);$i++){
				echo $i."</br>";
				echo $output->recording_id[$i]."</br>";
				echo $output->start_time[$i]."</br>";
				echo $output->length_in_sec[$i]."</br>";
				echo $output->full_name[$i]."</br>";
				echo $output->location[$i]."</br>";
			}
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
