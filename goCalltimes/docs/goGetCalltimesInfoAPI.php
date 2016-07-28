<?php

 ####################################################
 #### Name: goGetCalltimesInfoAPI.php            ####
 #### Description: API to get specific Calltime  ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Warren Ipac Briones            ####
 #### License: AGPLv2                            ####
 ####################################################


 	$url = "https://gadcs.goautodial.com/goAPI/goCalltimes/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "admin"; #Username goes here. (required)
        $postfields["goPass"] = "kam0teque1234"; #Password goes here. (required)
        $postfields["goAction"] = "getCalltimesInfo"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["call_time_id"] = $_GET['call_time_id'];


	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
	 curl_close($ch);
	 $output = json_decode($data);

//var_dump($data);
	if ($output->result=="success") {
	   # Result was OK!
			for($i=0;$i<count($output->call_time_id);$i++){
				echo $output->call_time_id[$i]."</br>";
				echo $output->call_time_name[$i]."</br>";
				echo $output->ct_default_start[$i]."</br>";
				echo $output->ct_default_stop[$i]."</br>";
				echo $output->user_group[$i]."</br>";
         			echo "</br>";
			}
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
