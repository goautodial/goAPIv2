<?php

 ####################################################
 #### Name: goAddInboundAPI.php                  ####
 #### Description: API to add Ingroup/Inbound    ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################

         $url = "https://encrypted.goautodial.com/goAPI/goInbound/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["responsetype"] = "json"; #json (required)
         $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	


         $postfields["goAction"] = "goAddInbound"; #action performed by the [[API:Functions]]
         $postfields["group_id"] = ""; #Desired group ID (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["group_name"] = ""; #Desired name (required)
         $postfields["group_color"] = ""; #Desired color (required)
	 $postfields["active"] = ""; #Y or N (required)
         $postfields["web_form_address"] = ""; #Desired web form address (required)
         $postfields["voicemail_ext"] = ""; #Desired voicemail (required)
         $postfields["next_agent_call"] = ""; #'fewest_calls_campaign','longest_wait_time','ring_all','random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank',  or 'fewest_calls' (required)
         $postfields["fronter_display"] = ""; #Y or N (required)
         $postfields["ingroup_script"] = ""; #Desired script (required)
         $postfields["get_call_launch"] = ""; #Desired call launch (required)
         $postfields["user_group"] = ""; #Assign user group (required)
//group_id, group_name, group_color,active, web_form_address, voicemail_ext, next_agent_call, fronter_display, ingroup_script, get_call_launch, user_group

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
var_dump($data);	
//	print_r($data);

	if ($output->result=="success") {
	   # Result was OK!
		echo "Added New In-group ID: ";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
