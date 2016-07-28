<?php

 ####################################################
 #### Name:		                         ####
 #### Description: API to edit specific campaign ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jerico James Milo		 ####
 #### License: AGPLv2                            ####
 ####################################################
	
        $url = "https://YOUR_URL/goAPI/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = ""; #Username goes here. (required)
        $postfields["goPass"] = ""; #Password goes here. (required)
        $postfields["goAction"] = ""; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["menu_id"] = ""; #Desired menu id. (required)

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
                        for($i=0;$i<count($output->menu_id);$i++){
                                echo $output->menu_id[$i]."</br>";
                                echo $output->menu_name[$i]."</br>";
                                echo $output->menu_prompt[$i]."</br>";
                                echo $output->menu_timeout[$i]."</br>";
                        }
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
