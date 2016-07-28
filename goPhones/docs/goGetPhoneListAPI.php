<?php

 ####################################################
 #### Name: goGetPhoneListAPI.php                ####
 #### Description: API to get Phone list	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################

        $url = "https://gadcs.goautodial.com/goAPI/goPhones/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "goautodial"; #Username goes here. (required)
        $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
        $postfields["goAction"] = "goGetPhoneList"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)

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
			for($i=0;$i<count($output->extension);$i++){
				echo $output->extension[$i]."</br>";
				echo $output->protocol[$i]."</br>";
				echo $output->server_ip[$i]."</br>";
				echo $output->dialplan_number[$i]."</br>";
				echo $output->active[$i]."</br>";
				echo $output->fullname[$i]."</br>";
				echo $output->messages[$i]."</br>";
				echo $output->old_messages[$i]."</br>";
				echo $output->user_group[$i]."<brd>";
			}
	 } else {
	   # An error occured
	   	echo "The following error occured: ".$results["message"];
	}

?>
