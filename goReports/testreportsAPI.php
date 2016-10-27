<?php
    $url = "https://webrtc.goautodial.com/goAPI/goJamesReports/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 						= "admin"; #Username goes here. (required)
	$postfields["goPass"] 						= "Yq48yHo2g0"; #Password goes here. (required)
	$postfields["goAction"] 					= "goGetReports"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 				= "json"; #json (required)
    $postfields["pageTitle"] 				    = "stats"; #json (required)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);
    
	var_dump(count($output->getReports->data_calls->cdate));
	for($i = 0; $i <= count($output->getReports->data_calls); $i++){
			$cdate = $output->getReports->data_calls->cdate[$i];
			$hour0 = $output->getReports->data_calls->hour0[$i];
			$hour1 = $output->getReports->data_calls->hour1[$i];
			$hour2 = $output->getReports->data_calls->hour2[$i];
			$hour3 = $output->getReports->data_calls->hour3[$i];
			$hour4 = $output->getReports->data_calls->hour4[$i];
			$hour5 = $output->getReports->data_calls->hour5[$i];
			$hour6 = $output->getReports->data_calls->hour6[$i];
			$hour7 = $output->getReports->data_calls->hour7[$i];
			$hour8 = $output->getReports->data_calls->hour8[$i];
			$hour9 = $output->getReports->data_calls->hour9[$i];
			$hour10 = $output->getReports->data_calls->hour10[$i];
			$hour11 = $output->getReports->data_calls->hour11[$i];
			$hour12 = $output->getReports->data_calls->hour12[$i];
			$hour13 = $output->getReports->data_calls->hour13[$i];
			$hour14 = $output->getReports->data_calls->hour14[$i];
			$hour15 = $output->getReports->data_calls->hour15[$i];
			$hour16 = $output->getReports->data_calls->hour16[$i];
			$hour17 = $output->getReports->data_calls->hour17[$i];
			$hour18 = $output->getReports->data_calls->hour18[$i];
			$hour19 = $output->getReports->data_calls->hour19[$i];
			$hour20 = $output->getReports->data_calls->hour20[$i];
			$hour21 = $output->getReports->data_calls->hour21[$i];
			$hour22 = $output->getReports->data_calls->hour22[$i];
			$hour23 = $output->getReports->data_calls->hour23[$i];
		$max = max($hour0, $hour1, $hour2, $hour3, $hour4, $hour5, $hour6, $hour7, $hour8, $hour9, $hour10, $hour11, $hour12, $hour13, $hour14, $hour15, $hour16, $hour17, $hour18, 
			$hour19, $hour20, $hour21, $hour22, $hour23);
		
		if(count($output->getReports->data_calls) >= $i){echo ", ";}
	}
	/*
    echo "<table border='1'>
    ";
        
    for($i=0; $i <= count($output->getReports->TOPsorted_output); $i++){
        echo $output->getReports->TOPsorted_output[$i];
    }
	 echo "</table>";
	 
    echo "
        <tr>
            <th>TOTAL</th>
            <th>AGENTS: ".$output->getReports->TOT_AGENTS."</th>
            <td>".$output->getReports->TOTcalls."</td>
            <td>".$output->getReports->TOTALtime."</td>
            <td>".$output->getReports->TOTwait."</td>
            <td>".$output->getReports->TOTtalk."</td>
            <td>".$output->getReports->TOTdispo."</td>
            <td>".$output->getReports->TOTpause."</td>
            <td>".$output->getReports->TOTdead."</td>
            <td>".$output->getReports->TOTcustomer."</td>
        </tr>
    ";
   
    
    echo "<table border='1'>";
    for($i=0; $i <= count($output->getReports->BOTsorted_output); $i++){
        echo $output->getReports->BOTsorted_output[$i];
    }
    echo "</table>";
    
    echo "<table border='1'>";
    for($i=0; $i <= count($output->getReports->TOPsorted_outputFILE); $i++){
        echo "<tr><td>".$output->getReports->TOPsorted_outputFILE[$i]."</td></tr>";
    }
    echo "</table>";
    */
?>