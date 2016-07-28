<?php

 ####################################################
 #### Name: goGetPauseCodeInfoAPI.php            ####
 #### Description: API to get specific Pause Code        ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################


 	$url = "https://encrypted.goautodial.com/goAPI/goPauseCodes/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "goautodial"; #Username goes here. (required)
        $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
        $postfields["goAction"] = "getPauseCodeInfo"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["pauseCampID"] = $_GET['pauseCampID'];
        $postfields["pause_code"] = $_GET['pause_code'];


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
			for($i=0;$i<count($output->campaign_id);$i++){
				echo $output->campaign_id[$i]."</br>";
				echo $output->pause_code_name[$i]."</br>";
				echo $output->pause_code[$i]."</br>";
				echo $output->billable[$i]."</br>";
         			echo "</br>";
			}
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
