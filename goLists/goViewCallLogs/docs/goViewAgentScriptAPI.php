<?php

 ####################################################
 #### Name: goViewAgentScriptAPI.php	         ####
 #### Description: API to view scripts   	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jerico James Milo		 ####
 #### License: AGPLv2                            ####
 ####################################################
	

	 $url = "http://69.46.6.35/goAPI/goViewScripts/goAPI.php"; # URL to GoAutoDial API filem (required)
	 $postfields["goUser"] = "admin"; #Username goes here. (required)
	 $postfields["goPass"] = "G02x16"; #Password goes here. (required)
	 $postfields["goAction"] = "goViewAgentScript"; #action performed by the [[API:Functions]] (required0
	 $postfields["responsetype"] = "json"; #response type by the [[API:Functions]] (required)

	 #required fields
         $postfields["lead_id"] = "21372"; #Agent full anme(required)
         $postfields["fullname"] = "Agent 003"; #Agent full anme(required)
         $postfields["first_name"] = "alex"; #Lead first_name (required)
         $postfields["last_name"] = "last_name"; #Lead last_name (required)
         $postfields["middle_initial"] = "middle_initial"; #Lead middle_initial (required)
         $postfields["email"] = "email"; #Lead email (required)
         $postfields["phone_number"] = "092121"; #Lead phone_number (required)
         $postfields["alt_phone"] = "alt_phone"; #Lead alt_phone (required)
         $postfields["address1"] = "address1"; #Lead address1 (required)
         $postfields["address2"] = "address2"; #Lead address2 (required)
         $postfields["address3"] = "address3"; #Lead address3 (required)
         $postfields["city"] = "city"; #Lead city (required)
         $postfields["province"] = "province"; #Lead province (required)
         $postfields["state"] = "state"; #Lead state (required)
         $postfields["postal_code"] = "postal_code"; #Lead postal_code (required)
         $postfields["country_code"] = "country_code"; #Lead country_code(required)


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
		echo $output->gocampaignScript;	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
