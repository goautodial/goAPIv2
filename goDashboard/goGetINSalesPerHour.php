<?php
    ####################################################
    #### Name: goGetINSalesPerHour.php              ####
    #### Type: API to get total In Sales per hour   ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jerico James Flores Milo       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    
	$groupId = go_get_groupid($session_user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_campaigns($groupId);
		if($stringv !== "'ALLCAMPAIGNS'")
			$ul = "and vcl.campaign_id IN ($stringv)";
		else
			$ul = "";
    }

    $NOW = date("Y-m-d");
	$query_date =  date('Y-m-d H');
	$status = "SALE";
	$date = "vcl.call_date BETWEEN '$query_date:00:00' AND '$query_date:59:59'";
    $query = "select count(*) as getINSalesPerHour
			FROM vicidial_closer_log as vcl
			LEFT JOIN vicidial_list as vl 
			ON vcl.lead_id=vl.lead_Id
			WHERE vcl.status='$status' $ul and $date ";
	//$query = "select concat(round((select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid ),2),'%')/8 as getInSalesPerHour;";
    //$drop_percentage = ( ($line->drops_today / $line->answers_today) * 100); 
    $rsltv = mysqli_query($link,$query)or die("Error: ".mysqli_error($link));
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success", "query" => $query), $fresults );
?>
