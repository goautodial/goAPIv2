<?php

 ####################################################
 #### Name: goGetCallRecordingListAPI.php        ####
 #### Description: API to get call recording list####
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
			for($i=0;$i<count($output->lead_id);$i++){
				echo $output->lead_id[$i]."</br>";
				echo $output->list_id[$i]."</br>";
				echo $output->phone_number[$i]."</br>";
				echo $output->full_name[$i]."</br>";
				echo $output->last_local_call_time[$i]."</br>";
				echo $output->status[$i]."</br>";
				echo $output->users[$i]."</br>";
				echo $output->cnt[$i]."</br>";
			}
	 } else {
	   # An error occured
	   	echo "The following error occured: ".$results["message"];
	}

?>
