<?php

 ####################################################
 #### Name:  goGetAllDispositionsAPI.php          ####
 #### Description: API to get All Dispositions        ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################


 	$url = "http://webrtc.goautodial.com/goAPI/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "admin"; #Username goes here. (required)
        $postfields["goPass"] = "Yq48yHo2g0"; #Password goes here. (required)
        $postfields["goAction"] = "getAllDispositions"; #action performed by the [[API:Functions]]. (required)
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
	if ($output->result=="success") {
	   # Result was OK!

			for($i=0;$i<count($output->status);$i++){
				echo $output->campaign_id[$i]."</br>";
				echo $output->status_name[$i]."</br>";
				echo $output->campaign_name[$i]."</br>";
				echo $output->status[$i]."</br>";
         			echo "</br>";
			}

	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
