<?php

 ####################################################
 #### Name: goEditMOHAPI.php                     ####
 #### Description: API to edit specific MOH      ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################
	


	 $url = "http://162.254.144.92/goAPI/goMusicOnHold/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "goautodial"; #Username goes here. (required)
         $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
         $postfields["goAction"] = "goEditMOH"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json. (required)
         $postfields["moh_id"] = "1000"; #Desired Music On Hold (required)
	 $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["moh_name"] = "Test Only"; #Desired name (required)
         $postfields["random"] = "";  #Desired random (required)
         $postfields["active"] = "Y";  #Y or N (required)
         $postfields["user_group"] = "ADMIN"; #Assigned to user group



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
