<?php

 ####################################################
 #### Name:  goGetAllDispositionsAPI.php          ####
 #### Description: API to get All Dispositions        ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################


 	$url = "https://encrypted.goautodial.com/goAPI/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "goautodial"; #Username goes here. (required)
        $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
        $postfields["goAction"] = "getCallPerHour"; #action performed by the [[API:Functions]]. (required)
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


	var_dump($data);
	if ($output->result=="success") {
	   # Result was OK!

			for($i=0;$i<count($output->cdate);$i++){
       				echo $output->cdate[$i];
			        echo $output->Hour9[$i]."<br />";
			        echo $output->Hour10[$i]."<br />";
			        echo $output->Hour11[$i]."<br />";
			        echo $output->Hour12[$i]."<br />";
			        echo $output->Hour13[$i]."<br />";
			        echo $output->Hour14[$i]."<br />";
			        echo $output->Hour15[$i]."<br />";
			        echo $output->Hour16[$i]."<br />";
			        echo $output->Hour17[$i]."<br />";
			        echo $output->Hour18[$i]."<br />";
			        echo $output->Hour19[$i]."<br />";
			        echo $output->Hour20[$i]."<br />";
			        echo $output->Hour21[$i]."<br />";

			}

	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
