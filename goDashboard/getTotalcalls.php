<?php
    ####################################################
    #### Name: getTotalcalls.php                    ####
    #### Type: API to get total calls               ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jin Kevin Dionisioio           ####
    #### License: AGPLv2                            ####
    ####################################################

    include_once ("goFunctions.php");

    $groupId = go_get_groupid($goUser);

    if (!checkIfTenant($groupId)) {
        $ul='';
    } else {
        $stringv = go_getall_allowed_users($groupId);
        $stringv .= "'j'";
        $ul = " and vcl.campaign_id IN ($stringv) and user_level != 4";
    }

    $query_date = date('Y-m-d');

    $query = "SELECT sum(calls_today) as totcalls from vicidial_campaign_stats where calls_today > -1 and update_time between '$query_date 00:00:00' and '$query_date 23:59:59'";

    $drop_percentage = ( ($line->drops_today / $line->answers_today) * 100);
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
