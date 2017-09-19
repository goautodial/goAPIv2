<?php

 ####################################################
 #### Name: goGetCarrierInfoAPI.php              ####
 #### Description: API to get specific Carrier	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	

        $url = "https://YOUR_URL/goAPI/goCarriers/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = ""; #Username goes here. (required)
        $postfields["goPass"] = ""; #Password goes here. (required)
        $postfields["goAction"] = ""; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["carrier_id"] = ""; #Desired carrier ID. (required)
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
                        for($i=0;$i<count($output->carrier_id);$i++){
                                echo $output->carrier_id[$i]."</br>";
                                echo $output->carrier_name[$i]."</br>";
                                echo $output->server_ip[$i]."</br>";
                                echo $output->protocol[$i]."</br>";
                                echo $output->registration_string[$i]."</br>";
                                echo $output->active[$i]."</br>";
                                echo $output->user_group[$i]."</br>";

                        }
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
