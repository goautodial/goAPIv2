<?php

 ####################################################
 #### Name: goDeleteDispositionAPI.php           ####
 #### Description: API to delete specific Status ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	

 	$url = "https://encrypted.goautodial.com/goAPI/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "goautodial"; #Username goes here. (required)
        $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
        $postfields["goAction"] = "goDeleteDisposition"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["campaign_id"] = $_GET['campaign_id']; #Desired Lead Filter id. (required)
        $postfields["statuses"] = $_GET['statuses']; #Desired Lead Filter id. (required)
        $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value

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
		echo "Delete Success ".$_GET['campaign_id'];	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
