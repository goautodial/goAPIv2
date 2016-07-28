<?php
    ####################################################
    #### Name: getOutSalesPerHour.php               ####
    #### Type: API to get total Out Sales per hour  ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Warren Ipac Briones            ####
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

   $NOW = date("Y-m-d");
            $query_date =  date('Y-m-d');
            $status = "SALE";
            $date = "call_date BETWEEN '$query_date 00:00:00' AND '$query_date 23:59:59'";
		$query="select concat(round((select count(*) as qresult from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid and val.status='$status' and $date $ul),3),'%')/8 as OutSalesPerHour";
	//$query ="select concat(round((select count(*) as qresult from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid ),3),'%')/8 as OutSalesPerHour;";

    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
