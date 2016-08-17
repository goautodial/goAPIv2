<?php

 ####################################################
 #### Name:   goGetLeadsAPI.php                  ####
 #### Description: API to get Leads              ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Warren Ipac Briones            ####
 #### License: AGPLv2                            ####
 ####################################################
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
    echo "####################<br/>";
    echo '## THIS IS THE API ##<br/>';
    echo '####################<br/>';
    
 	$url = "https://webrtc.goautodial.com/goAPI/goGetLeads/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = "admin"; #Username goes here. (required)
    $postfields["goPass"] = "Yq48yHo2g0"; #Password goes here. (required)
    $postfields["goAction"] = "goEditLeads"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = "json"; #json. (required)
	$postfields["lead_id"] = "803";
    $postfields["first_name"] = "EDIT";
    $postfields["last_name"] = "TEST";
    $postfields["email"] = "test@email.com";

	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
	 curl_close($ch);
	 $output = json_decode($data);
     
     var_dump($output);
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
