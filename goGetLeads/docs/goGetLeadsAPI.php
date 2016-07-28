<?php

 ####################################################
 #### Name:   goGetLeadsAPI.php                  ####
 #### Description: API to get Leads              ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Warren Ipac Briones            ####
 #### License: AGPLv2                            ####
 ####################################################


 	$url = "https://gadcs.goautodial.com/goAPI/goGetLeads/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "admin"; #Username goes here. (required)
        $postfields["goPass"] = "kam0teque1234"; #Password goes here. (required)
        $postfields["goAction"] = "goGetLeads"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
	$postfields["goVarLimit"] = $_GET['goVarLimit'];
	$postfields["user_id"] = $_GET['user_id'];

	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
		var_dump($data);
	 curl_close($ch);
	 $output = json_decode($data);
/*	if ($output->result=="success") {
	   # Result was OK!
			for($i=0;$i<count($output->list_id);$i++){
				echo $output->list_id[$i]."</br>";
				echo $output->first_name[$i]."</br>";
				echo $output->middle_initial[$i]."</br>";
				echo $output->last_name[$i]."</br>";
				echo $output->email[$i]."</br>";
				echo $output->phone_number[$i]."</br>";
				echo $output->alt_phone[$i]."</br>";
				echo $output->address1[$i]."</br>";
				echo $output->address2[$i]."</br>";
				echo $output->address3[$i]."</br>";
				echo $output->city[$i]."</br>";
				echo $output->state[$i]."</br>";
				echo $output->province[$i]."</br>";
				echo $output->postal_code[$i]."</br>";
				echo $output->country_code[$i]."</br>";
				echo $output->date_of_birth[$i]."</br>";
				echo $output->entry_date[$i]."</br>";
				echo $output->user[$i]."</br>";
				echo $output->gender[$i]."</br>";
				echo $output->comments[$i]."</br>";
         			echo "</br>";
			}
	 } else {
	   # An error occured
	   	echo $output->result;
	}
*/
?>
