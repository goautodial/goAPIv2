<?php

 ####################################################
 #### Name:   goGetAllLeadFiltersAPI.php         ####
 #### Description: API to get lead filter        ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################


 	$url = "https://encrypted.goautodial.com/goAPI/goVoicemails/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "goautodial"; #Username goes here. (required)
        $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
        $postfields["goAction"] = "getAllVoicemails"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)


	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
	 curl_close($ch);
	 $output = json_decode($data);
//var_dump($data);
	if ($output->result=="success") {
	   # Result was OK!

			for($i=0;$i<count($output->voicemail_id);$i++){
				echo $output->voicemail_id[$i]."</br>";
				echo $output->fullname[$i]."</br>";
				echo $output->active[$i]."</br>";
				echo $output->messages[$i]."</br>";
				echo $output->old_messages[$i]."</br>";
				echo $output->delete_vm_after_email[$i]."</br>";
				echo $output->user_group[$i]."</br>";
         			echo "</br>";
			}

	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
