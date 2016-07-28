<?php

 ####################################################
 #### Name: goEditScriptAPI.php                   ####
 #### Description: API to edit specific script####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	


	 $url = "https://encrypted.goautodial.com/goAPI/goScripts/goAPI.php"; # URL to GoAutoDial API file
	 $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goEditScript"; #action performed by the [[API:Functions]]
	 $postfields["responsetype"] = "json"; #json (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	 $postfields["script_id"] = $_GET["script_id"]; #
         $postfields["user_group"] = $_GET['user_group'];
         $postfields["script_name"] = $_GET['script_name'];
         $postfields["script_comments"] = $_GET['script_comments'];
         $postfields["script_text"] = $_GET['script_text'];
         $postfields["active"] = $_GET['active'];


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
