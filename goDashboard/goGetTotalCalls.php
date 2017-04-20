<?php
    ########################################################
    #### Name: goGetTotalCalls.php                      ####
    #### Description: API to get total calls            ####
    #### Version: 0.9                                   ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016       ####
    #### Written by: Jeremiah Sebastian V. Samatra      ####
    ####             Demian Lizandro A. Biscocho        ####
    #### License: AGPLv2                                ####
    ########################################################
    
    require_once("../goFunctions.php");
	
    $groupId = go_get_groupid($session_user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_campaigns($groupId);
		if($stringv !== "'ALLCAMPAIGNS'")
			$ul = " AND campaign_id IN ($stringv)";
		else
			$ul = "";
    }

    $NOW = date("Y-m-d");

    $queryTotalcalls = "select sum(calls_today) as getTotalCalls from vicidial_campaign_stats where calls_today > -1 and update_time BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    
    $queryInboundcalls = "select count(call_date) as getTotalInboundCalls from vicidial_closer_log where call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    
    $queryOutboundcalls = "select count(call_date) as getTotalOutboundCalls from vicidial_log where call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    
    $rsltvTotalcalls = mysqli_query($link, $queryTotalcalls)or die("Error: ".mysqli_error($link));
    $rsltvIncalls = mysqli_query($link, $queryInboundcalls)or die("Error: ".mysqli_error($link));
    $rsltvOutcalls = mysqli_query($link, $queryOutboundcalls)or die("Error: ".mysqli_error($link));
	
	$dataTotalCalls = mysqli_fetch_array($rsltvTotalcalls,MYSQLI_ASSOC);
	$dataIncalls = mysqli_fetch_array($rsltvIncalls,MYSQLI_ASSOC);
	$dataOutcalls = mysqli_fetch_array($rsltvOutcalls,MYSQLI_ASSOC);
	
    $data = array("getTotalCalls" => $dataTotalCalls['getTotalCalls'], "getTotalInboundCalls" => $dataIncalls['getTotalInboundCalls'], "getTotalOutboundCalls" => $dataOutcalls['getTotalOutboundCalls']);
	
    $apiresults = array("result" => "success", "data" => $data, "query" => $queryOutboundcalls ); 
?>
