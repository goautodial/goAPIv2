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
    
    include_once("../goFunctions.php");
	
    $user = mysqli_real_escape_string($link, $_POST['user']);
    $groupId = go_get_groupid($user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_users($groupId);
        $ul = " and campaign_id IN ($stringv)";
    }

    $NOW = date("Y-m-d");

    $queryTotalcalls = "select sum(calls_today) as getTotalCalls from vicidial_campaign_stats where calls_today > -1 and update_time BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    
    $queryInboundcalls = "select count(call_date) as getTotalInboundCalls from vicidial_closer_log where call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    
    $queryOutboundcalls = "select count(call_date) as getTotalOutboundCalls from vicidial_log where call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    
    $rsltvTotalcalls = mysqli_query($link, $queryTotalcalls);
    $rsltvIncalls = mysqli_query($link, $queryInboundcalls);
    $rsltvOutcalls = mysqli_query($link, $queryOutboundcalls);
    
    $dataTotalCalls = mysqli_fetch_assoc($rsltvTotalcalls);
    $dataIncalls = mysqli_fetch_assoc($rsltvIncalls);
    $dataOutcalls = mysqli_fetch_assoc($rsltvOutcalls);
    
    $data = array_merge($dataTotalCalls, $dataIncalls, $dataOutcalls);
        
    $apiresults = array("result" => "success", "data" => $data); 
?>
