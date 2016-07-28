<?php

 ####################################################
 #### Name: goJeremy.php                         ####
 #### Description: API for dashboard php encode  ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################


	 $url = "https://jameshv.goautodial.com/goAPI/goScripts/goAPI.php"; # URL to GoAutoDial API file
	 $postfields["goUser"] = "goautodial";// "2012107124"; # Admin/Tenant/Non-Tenant username goes here
	 $postfields["goPass"] = "JUs7g0P455W0rD11214"; //"liSB92qd";  # Admin/Tenant/Non-Tenant password goes here
	 $postfields["goAction"] = "goGetAllScriptsList"; #action performed by the [[API:Functions]]
	 $postfields["responsetype"] = "json"; #response type by the [[API:Functions]]
	 $postfields["limit"] = "20"; #response type by the [[API:Functions]]

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
		echo '<table border="1" cellspacing="0" cellpadding="2"><tr><th>SCRIPT ID</th><th>SCRIPT NAME</th><th>STATUS</th><th>USER GROUP</th></tr>'; 
			for($i=0;$i<count($output->script_id);$i++){
				echo "<tr><td>".$output->script_id[$i]."</td>";
				echo "<td>".$output->script_name[$i]."</td>";
				echo "<td>".$output->active[$i]."</td>";
				echo "<td>".$output->user_group[$i]."</td>";
				echo "</tr>";
			}
		echo '</table>';
	 } else {
	   # An error occured
	   	echo "The following error occured: ".$results["message"];
	}

?>
