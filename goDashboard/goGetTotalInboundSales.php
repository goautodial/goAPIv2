<?php
    ####################################################
    #### Name: getTotalInboundSales.php             ####
    #### Type: API to get total inbound sales       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Warren Ipac Briones            ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    
    $groupId = go_get_groupid($session_user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else {
		$stringv = go_getall_allowed_users($groupId);
		if($groupId !== "ADMIN")
			$ul = " and vcl.user IN ($stringv)";
		else
			$ul = "";
    }

   $NOW = date("Y-m-d");
            $query_date =  date('Y-m-d');
            $status = "SALE";
            $date = "vcl.call_date BETWEEN '$query_date 00:00:00' AND '$query_date 23:59:59'";
//select sum(calls_today) as calls_today,sum(drops_today) as drops_today,sum(answers_today) as answers_today from vicidial_campaign_stats where calls_today > -1 and update_time BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59'
   //$query = "select count(*) as InboundSales from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid ";
   $query = "select count(*) as InboundSales
        FROM vicidial_closer_log as vcl
        LEFT JOIN vicidial_list as vl 
        ON vcl.lead_id=vl.lead_Id
        WHERE vcl.status='$status' and $date $ul";
	//$drop_percentage = ( ($line->drops_today / $line->answers_today) * 100); 
    $rsltv = mysqli_query($link,$query)or die("Error: ".mysqli_error($link));
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success", "query" => $query ), $fresults );
?>
