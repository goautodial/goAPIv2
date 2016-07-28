<?php

 ####################################################
 #### Name: goGetVoiceFileInfoAPI.php            ####
 #### Description: API to view audiofiles        ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian Samatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	
	 ### POST or GET Variables
	 $audiofile = $_REQUEST['audiofile'];

	 $url = "https://jameshv.goautodial.com/goAPI/goVoiceFiles/goAPI.php"; # URL to GoAutoDial API file
	 $postfields["goUser"] = "goautodial";// "2012107124"; # Admin/Tenant/Non-Tenant username goes here
	 $postfields["goPass"] = "JUs7g0P455W0rD11214"; //"liSB92qd";  # Admin/Tenant/Non-Tenant password goes here
	 $postfields["goAction"] = "goGetVoiceFileInfo"; #action performed by the [[API:Functions]]
	 $postfields["responsetype"] = "json"; #response type by the [[API:Functions]]
	 $postfields["audiofile"] = "$audiofile";

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
//var_dump($output);
	if ($output->result=="success") {
	   # Result was OK!
		$count = 1;
		echo '<table border="1" cellspacing="0" cellpadding="2"><tr><th>NO</th><th>FILENAME</th><th>DATE</th><th>SIZE</th></tr>'; 
                        for($i=0;$i<count($output->file_name);++$i){
				
                                echo "<tr><td>".$count++."</td>";
                                echo "<td>".$output->file_name[$i]."</td>";
                                echo "<td>".$output->file_date[$i]."</td>";
                                echo "<td>".$output->file_size[$i]."</td>";
                                //echo "<td>".$output->file_poch[$i]."</td>";
                                echo "</tr>";
				
                        }
                echo '</table>';
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
