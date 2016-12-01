<?php
    ####################################################
    #### Name: goGetTotalSales.php                  ####
    #### Type: API to get total sales               ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Warren Ipac Briones            ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    
    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_users($groupId);
        $stringv .= "'j'";
        $ul = "and vcl.campaign_id IN ($stringv) and user_level != 4";
    }

    $NOW = date('Y-m-d');    
    $YESTERDAY = date('Y-m-d',strtotime('-1 days'));
    
    $status = "SALE";
    $date = "call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59'";
    $dateY = "call_date BETWEEN '$YESTERDAY 00:00:00' AND '$YESTERDAY 23:59:59'";
    $dateLW = "call_date BETWEEN NOW() - INTERVAL DAYOFWEEK(NOW())+6 DAY AND NOW() - INTERVAL DAYOFWEEK(NOW())-1 DAY";
   
    $query = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid and val.status='$status' and $date $ul) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid and val.status='$status' and $date $ul) as TotalSales";
    $queryY = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid and val.status='$status' and $dateY $ul) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid and val.status='$status' and $dateY $ul) as TotalSalesYesterday";
    $queryLW = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid and val.status='$status' and $dateLW $ul) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid and val.status='$status' and $dateLW $ul) as TotalSalesLastWeek";
    
    //	$query = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid ) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid ) as TotalSales;";
    //$drop_percentage = ( ($line->drops_today / $line->answers_today) * 100); 
    $rsltv = mysqli_query($link,$query);
    $rsltvY =  mysqli_query($link,$queryY);
    $rsltvLW =  mysqli_query($link,$queryLW);
    $fresults = mysqli_fetch_assoc($rsltv);
    $fresultsY = mysqli_fetch_assoc($rsltvY);
    $fresultsLW = mysqli_fetch_assoc($rsltvLW);
    $apiresults = array_merge( array( "result" => "success" ), $fresults, $fresultsY, $fresultsLW);
?>
