<?php

 ####################################################
 #### Name: goGetPhoneInfoAPI.php                ####
 #### Description: API to get specific Phone	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	

        $url = "https://gadcs.goautodial.com/goAPI/goPhones/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "admin"; #Username goes here. (required)
        $postfields["goPass"] = "kam0teque1234"; #Password goes here. (required)
        $postfields["goAction"] = "goGetPhoneInfo"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["exten_id"] = $_GET['exten_id']; #Desired exten ID. (required)



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
//	 var_dump($data); die();	
//	print_r($data);

	if ($output->result=="success") {
	   # Result was OK!
                        for($i=0;$i<count($output->extension);$i++){
                                echo $output->extension[$i]."</br>";
                                echo $output->protocol[$i]."</br>";
                                echo $output->server_ip[$i]."</br>";
                                echo $output->dialplan_number[$i]."</br>";
                                echo $output->active[$i]."</br>";
                                echo $output->fullname[$i]."</br>";
                                echo $output->messages[$i]." ".$output->old_messages[$i]."</br>";
                                echo $output->user_group[$i]."</br>";
                        }
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
