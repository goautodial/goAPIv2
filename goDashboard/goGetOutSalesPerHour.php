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
    
    $groupId = go_get_groupid($session_user);

    if (checkIfTenant($groupId)) {
        $ul='';
    } else {
        if($groupId !== "ADMIN")
			$ul = "and vlog.user_group = '$groupId'";
		else
			$ul = "";
    }

   $NOW = date("Y-m-d");
	$query_date =  date('Y-m-d H');
	$status = "SALE";
	$date = "vlog.call_date BETWEEN '$query_date:00:00' AND '$query_date:59:59'";
	$query="select count(*) as getOutSalesPerHour FROM vicidial_log as vlog LEFT JOIN vicidial_list as vl ON vlog.lead_id=vl.lead_id WHERE vlog.status='SALE' $ul and $date";
    $rsltv = mysqli_query($link,$query)or die("Error: ".mysqli_error($link));
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success", "query" => $date), $fresults );
	
	
?>
