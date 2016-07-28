<?php

 ####################################################
 #### Name: goJamesDeleteCampaign.php            ####
 #### Description: API to delete specific campaign ##
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jerico James Milo		 ####
 #### License: AGPLv2                            ####
 ####################################################
	
	 ### POST or GET Variables
	 $campaign_id = $_REQUEST['campaign_id'];

	 $url = "https://jameshv.goautodial.com/goAPI/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	 $postfields["goUser"] = "goautodial";// "2012107124"; # Admin/Tenant/Non-Tenant username goes here
	 $postfields["goPass"] = "JUs7g0P455W0rD11214"; //"liSB92qd";  # Admin/Tenant/Non-Tenant password goes here
	 $postfields["goAction"] = "goDeleteCampaign"; #action performed by the [[API:Functions]]
	 $postfields["responsetype"] = "json"; #response type by the [[API:Functions]]
	 $postfields["limit"] = "1"; #response type by the [[API:Functions]]
	 $postfields["campaign_id"] = "$campaign_id"; #variable for Campaign ID

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
		echo "Delete Success";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
