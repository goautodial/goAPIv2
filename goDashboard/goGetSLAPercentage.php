<?php
    ##############################################################
    #### Name: goGetSLAPercentage.php            	      ####
    #### Description: API to get total drops Call percentage  ####
    #### Version: 0.9                              	      ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016  	      ####
    #### Written by: Demian Lizandro A. Biscocho              ####
    #### License: AGPLv2                           	      ####
    ##############################################################
    
    include_once("../goFunctions.php");
    
    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_users($groupId);
        $stringv .= "'j'";
        $ul = "and user_group not in ('','NULL','ADMIN')";
    }

    $NOW = date("Y-m-d");
    $queue_seconds = "queue_seconds <= 20";
    
    $query = "SELECT user_group, sum(term_reason in ('ABANDON','AFTERHOURS')) as abandon, sum(queue_seconds <= 20) as callsansweredlessthan20sec, sum(term_reason not in ('ABANDON','AFTERHOURS')) as answered, count(*) as calls_today, (sum($queue_seconds <= 20)/count(*))*100 as SLA, (sum(length_in_sec)/sum(user not in ('NULL','','VDCL')))/60 as AHT from vicidial_closer_log where call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    //$query = "SELECT concat(round((sum($queue_seconds)/count(*))))*100 as SLA from vicidial_closer_log where call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";

    $rsltv = mysqli_query($link, $query);
    $countResult = mysqli_num_rows($rsltv);
    //echo "<pre>";
    //var_dump($rsltv);   
        
    if($countResult > 0) {
        $data = array();
    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){       
        array_push($data, $fresults);
    }
    $apiresults = array("result" => "success", "data" => $data);
    }
?>
