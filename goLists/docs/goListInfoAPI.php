<?php

 ####################################################
 #### Name: goListInfoAPI.php                    ####
 #### Description: API to view specific List	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################
	

 	$url = "https://gadcs.goautodial.com/goAPI/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "admin"; #Username goes here. (required)
        $postfields["goPass"] = "kam0teque1234"; #Password goes here. (required)
        $postfields["goAction"] = "goGetListInfo"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["list_id"] = $_GET['list_id']; #Desired list id. (required) 



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
                        for($i=0;$i<count($output->list_id);$i++){
                                echo $output->list_id[$i]."</br>";
                                echo $output->list_name[$i]."</br>";
                                echo $output->active[$i]."</br>";
                                echo $output->list_lastcalldate[$i]."</br>";
                                echo $output->tally[$i]."</br>";
                                echo $output->campaign_id[$i]."</br>";
                        }
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
