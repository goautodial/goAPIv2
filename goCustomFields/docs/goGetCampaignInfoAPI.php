<?php

 ####################################################
 #### Name:goGetCampaignInfoAPI.php              ####
 #### Description: API to edit specific campaign ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jerico James Milo		 ####
 #### License: AGPLv2                            ####
 ####################################################
	

 	$url = "https://162.254.144.92/goAPI/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "goautodial"; #Username goes here. (required)
        $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
        $postfields["goAction"] = "getCampaignInfo"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["campaign_id"] = "20317264"; #variable for Campaign ID. (required)


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
                        for($i=0;$i<count($output->campaign_id);$i++){
                                echo $output->campaign_id[$i]."</br>";
                                echo $output->campaign_name[$i]."</br>";
				echo $output->dial_method[$i].'</br>';
				echo $output->active[$i].'</br>';
                        }
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
