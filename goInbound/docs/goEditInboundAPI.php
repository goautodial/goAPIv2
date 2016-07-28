<?php

 ####################################################
 #### Name:		                         ####
 #### Description: API to edit specific campaign ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jerico James Milo		 ####
 #### License: AGPLv2                            ####
 ####################################################


	 $url = "https://jameshv.goautodial.com/goAPI/goInbound/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = ""; #Username goes here. (required)
         $postfields["goPass"] = ""; #Password goes here. (required)
         $postfields["goAction"] = ""; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json. (required)
         $postfields["group_id"] = ""; #Desired group ID (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["group_name"] = ""; #Deisred name (required)
         $postfields["group_color"] = ""; #Desired color (required)
         $postfields["active"] = "";  #Y or N (required)
         $postfields["web_form_address"] = ""; #Desired web form address (required)
         $postfields["next_agent_call"] = ""; #Desired next agent call (required)
         $postfields["fronter_display"] = ""; #Y or N (required)
         $postfields["ingroup_script"] = ""; #Desired script (required)
         $postfields["queue_priority"] = ""; #Desired queue priority (required)



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
