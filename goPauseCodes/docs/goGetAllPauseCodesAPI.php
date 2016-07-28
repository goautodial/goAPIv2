<?php

 ####################################################
 #### Name:   goGetAllPauseCodesAPI.php          ####
 #### Description: API to get Pause Code         ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################


 	$url = "https://gadcs.goautodial.com/goAPI/goPauseCodes/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "admin"; #Username goes here. (required)
        $postfields["goPass"] = "kam0teque1234"; #Password goes here. (required)
        $postfields["goAction"] = "getAllPauseCodes"; #action performed by the [[API:Functions]]. (required)
        $postfields["pauseCampID"] = $_GET['pauseCampID'];
        $postfields["pause_code"] = $_GET['pause_code'];
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
//	var_dump($data);
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
