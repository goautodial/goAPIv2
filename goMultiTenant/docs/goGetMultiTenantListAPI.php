<?php

 ####################################################
 #### Name: goGetMultiTenantListAPI.php          ####
 #### Description: API to get tenant list	 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################

        $url = "https://YOUR_URL/goAPI/goMultiTenant/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = ""; #Username goes here. (required)
        $postfields["goPass"] = ""; #Password goes here. (required)
        $postfields["goAction"] = ""; #action performed by the [[API:Functions]]. (required)
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
			for($i=0;$i<count($output->tenant_id);$i++){
				echo $output->tenant_id[$i]."</br>";
				echo $output->tenant_name[$i]."</br>";
				echo $output->cnt[$i]."</br>";
				echo $output->active[$i]."</br>";
			}
	 } else {
	   # An error occured
	   	echo "The following error occured: ".$results["message"];
	}

?>
