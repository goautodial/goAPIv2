<?php
    ##############################################################
    #### Name: goGetRingingCalls.php            	      ####
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

    $query = "select count(*) AS getRingingCalls from vicidial_auto_calls where status NOT IN('XFER') $ul and call_type RLIKE 'OUT' ";
    $rsltv = mysqli_query($link, $query)or die("Error: ".mysqli_error($link));
    $data = mysqli_fetch_assoc($rsltv);
    $apiresults = array("result" => "success", "data" => $data, "query" => $query);
?>
