<?php
    ####################################################
    #### Name: goGetOutSalesPerHour.php             ####
    #### Type: API to get total Out Sales per hour  ####
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
        $ul = " and vcl.campaign_id IN ($stringv) and user_level != 4";
    }

   $NOW = date("Y-m-d");
            $query_date =  date('Y-m-d H:i');
            $status = "SALE";
            $date = "vlog.call_date BETWEEN '$query_date:00' AND '$query_date:59'";
            $query="select count(*) as getOutSalesPerHour
                    FROM vicidial_log as vlog
                    LEFT JOIN vicidial_list as vl 
                    ON vlog.lead_id=vl.lead_Id
                    WHERE vlog.status='$status' and $date $ul";
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success", "query" => $query), $fresults );
?>
