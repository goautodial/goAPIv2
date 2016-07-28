<?php

 ####################################################
 #### Name:		                         ####
 #### Description: API to edit specific campaign ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jerico James Milo		 ####
 #### License: AGPLv2                            ####
 ####################################################
	


	 $url = "http://gadcs.goautodial.com/goAPI/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	 $postfields["goUser"] = "admin"; #Username goes here. (required)
         $postfields["goPass"] = "kam0teque1234"; #Password goes here. (required)
         $postfields["goAction"] = "goEditCampaign"; #action performed by the [[API:Functions]]
	 $postfields["responsetype"] = "json"; #json (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	 $postfields["campaign_id"] = $_GET['campaign_id'];#"20317266"; #Desired Camnpaign (required)
	 $postfields["campaign_name"] = $_GET['campaign_name'];#"Sample"; #Desired name (required)
	 $postfields["active"] = $_GET['active'];#"Y"; #Y or N (required)
	 $postfields["dial_method"] = $_GET['dial_method'];#"RATIO"; #MANUAL,RATIO,ADAPT_HARD_LIMIT,ADAPT_TAPERED,ADAPT_AVERAGE,or INBOUND_MAN (required)

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
		echo "Update Success";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
