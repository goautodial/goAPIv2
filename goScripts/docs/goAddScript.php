<?php

 ####################################################
 #### Name:  goAddStateCallTimeAPI.php           ####
 #### Description: API to add new Voicemail	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
         $url = "https://encrypted.goautodial.com/goAPI/goScripts/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goAddScript"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["script_id"] = $_GET["script_id"]; #
         $postfields["user_group"] = $_GET['user_group'];
         $postfields["script_name"] = $_GET['script_name'];
         $postfields["script_comments"] = $_GET['script_comments'];
         $postfields["script_text"] = $_GET['script_text'];
         $postfields["active"] = $_GET['active'];
         $postfields["campaign_id"] = $_GET['campaign_id'];


	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
	 curl_close($ch);
	 $output = json_decode($data);

	if ($output->result=="success") {
	   # Result was OK!
		echo "Added New Script ID: ".$_REQUEST['script_id'];	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
