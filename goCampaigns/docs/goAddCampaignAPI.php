<?php

 ####################################################
 #### Name: goAddListAPI.php                     ####
 #### Description: API to add new List		 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
	 $campaign_id = $_REQUEST['campaign_id']; 
         $url = "http://encrypted.goautodial.com/goAPI/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goAddCampaign"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json (required)
         $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["did_pattern"] = ""; #Desired did pattern (required if campaign type is BLENDED)
         $postfields["group_color"] = ""; #Desired group color (required if campaign type is BLENDED)
         $postfields["call_route"] = ""; #Desired call route (required if campaign type is BLENDED)
         $postfields["survey_type"] = ""; #survey type values is BROADCAST or PRESS1 only (required if campaign type is survey)
         $postfields["number_channels"] = "5"; #number channel values is 1,5,10,15,20, or 30 only (requred)
         $postfields["campaign_type"] = "OUTBOUND"; #Type of campaign, values is OITBOUND, INBOUND, BLENDED or SURVEY only. (required)
         $postfields["campaign_id"] = $campaign_id;//"20317264"; #Desired campaign id (required)
         $postfields["campaign_name"] = ""; #Desired name of campaign

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
		echo "Added New Camapign ID: ".$campaign_id;	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
