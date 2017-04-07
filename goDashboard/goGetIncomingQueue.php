<?php
    ##############################################################
    #### Name: goGetIncomingQueue.php       	    	      ####
    #### Description: API to get total calls		      ####
    #### Version: 0.9                              	      ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016  	      ####
    #### Written by: Jeremiah Sebastian V. Samatra 	      ####
    ####             Demian Lizandro A. Biscocho              ####
    #### License: AGPLv2                           	      ####
    ##############################################################
    
    include_once("../goFunctions.php");
    
	$groupId = go_get_groupid($session_user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_campaigns($groupId);
		if($stringv !== "'ALLCAMPAIGNS'")
			$ul = " and campaign_id IN ($stringv)";
		else
			$ul = "";
    }

    $NOW = date("Y-m-d");

    $query = "select count(*) AS getIncomingQueue from vicidial_auto_calls where status NOT IN('XFER') and call_type = 'IN' $ul";
    $rsltv = mysqli_query($link, $query)or die("Error: ".mysqli_error($link));
    $data = mysqli_fetch_assoc($rsltv);
    $apiresults = array("result" => "success", "data" => $data);
?>
