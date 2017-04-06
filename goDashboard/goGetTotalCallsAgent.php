<?php
    ##############################################################
    #### Name: goGetTotalCalls.php            	    	      ####
    #### Description: API to get total calls		      ####
    #### Version: 0.9                              	      ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014  	      ####
    #### Written by: Jeremiah Sebastian V. Samatra 	      ####
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

    $query = "select sum(calls_today) as getTotalCalls from vicidial_campaign_stats where calls_today > -1 and update_time BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    $drop_percentage = ( ($line->drops_today / $line->answers_today) * 100); 
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
