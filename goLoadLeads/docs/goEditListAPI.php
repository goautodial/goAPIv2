<?php

 ####################################################
 #### Name: goEditListAPI.php                    ####
 #### Description: API to edit specific List     ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################
	 


	 $url = "https://jameshv.goautodial.com/goAPI/goLists/goAPI.php"; # URL to GoAutoDial API file
	 $postfields["goUser"] = ""; #Username goes here. (required)
	 $postfields["goPass"] = ""; #Password goes here. (required)
	 $postfields["goAction"] = ""; #action performed by the [[API:Functions]]
	 $postfields["responsetype"] = "json"; #json (required)
	 $postfields["limit"] = "1"; #response type by the [[API:Functions]]
         $postfields["list_id"] = ""; #Desired list id. (required) 
         $postfields["list_name"] = ""; #Desired name. (required)
         $postfields["list_description"] = ""; #Desired description. (required)
         $postfields["campaign_id"] = ""; #Assign to campaign. (required)
         $postfields["active"] = ""; #Y or N (required)
         $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	 $postfields["reset_time"] = "";  #Desired reset time (required)
	 $postfields["xferconf_a_number"] = ""; #Desired number (required)
	 $postfields["xferconf_b_number"] = ""; #Desired number (required)
	 $postfields["xferconf_c_number"] = ""; #Desired number (required)
	 $postfields["xferconf_d_number"] = ""; #Desired number (required)
	 $postfields["xferconf_e_number"] = ""; #Desired number (required)
	 $postfields["agent_script_override"] = ""; #Assign to script (required)
	 $postfields["drop_inbound_group_override"] = ""; #Assign inboung group override (required)
	 $postfields["campaign_cid_override"] = ""; #Assign to campaign override (required)
	 $postfields["web_form_address"] = ""; #Desired web form address (required)
	 $postfields["reset_list"] = ""; #Y or N (required)

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
		echo "Update Success";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
