<?php

 ####################################################
 #### Name: goGetVoiceFileListAPI.php            ####
 #### Description: API to list audiofiles        ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
$url = "https://encrypted.goautodial.com/goAPI/goVoiceFiles/goAPI.php"; #URL to GoAutoDial API. (required)
$postfields["goUser"] = "goautodial"; #Username goes here. (required)
$postfields["goPass"] = "JUs7g0P455W0rD11214";
$postfields["goAction"] = "goGetVoiceFilesList"; #action performed by the [[API:Functions]]
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
var_dump($data);	
//	print_r($data);
	if ($output->result=="success") {
	   # Result was OK!
                        for($i=0;$i<count($output->file_name);++$i){
                                echo $output->file_name[$i]."</br>";
                                echo $output->file_date[$i]."</br>";
                                echo $output->file_size[$i]."</br>";
                        }
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
