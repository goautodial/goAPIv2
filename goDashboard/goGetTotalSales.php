<?php
    ####################################################
    #### Name: goGetTotalSales.php                  ####
    #### Type: API to get total sales               ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Warren Ipac Briones            ####
    #### License: AGPLv2                            ####
    ####################################################
    
    $groupId = go_get_groupid($session_user, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
        $ul_vcl = "";
		$ul_vl = "";
    } else { 
		if($groupId !== "ADMIN"){
			$ul_vcl = "and val.user_group = '$groupId'";
			$ul_vl = "and vl.user_group = '$groupId'";
		}else{
			$ul_vcl = "";
			$ul_vl = "";
		}
    }

    $NOW = date('Y-m-d');    
    $YESTERDAY = date('Y-m-d',strtotime('-1 days'));
    
    $status = "SALE";
    $date = "call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59'";
    $dateY = "call_date BETWEEN '$YESTERDAY 00:00:00' AND '$YESTERDAY 23:59:59'";
    $dateLW = "call_date BETWEEN NOW() - INTERVAL DAYOFWEEK(NOW())+6 DAY AND NOW() - INTERVAL DAYOFWEEK(NOW())-1 DAY";
   
    $query = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid and val.status='$status' $ul_vcl and $date ) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid and val.status='$status' $ul_vl and $date ) as TotalSales";
    $queryY = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid and val.status='$status' and $dateY $ul_vcl) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid and val.status='$status' and $dateY $ul_vl) as TotalSalesYesterday";
    $queryLW = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid and val.status='$status' and $dateLW $ul_vcl) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid and val.status='$status' and $dateLW $ul_vl) as TotalSalesLastWeek";
    
    //	$query = "select (select count(*) from vicidial_closer_log vcl,vicidial_agent_log val where vcl.uniqueid=val.uniqueid ) + (select count(*) from vicidial_log vl,vicidial_agent_log val where vl.uniqueid=val.uniqueid ) as TotalSales;";
    //$drop_percentage = ( ($line->drops_today / $line->answers_today) * 100); 
    $fresults = $astDB->rawQuery($query);
    $fresultsY =  $astDB->rawQuery($queryY);
    $fresultsLW =  $astDB->rawQuery($queryLW);
    //$fresults = mysqli_fetch_assoc($rsltv);
    //$fresultsY = mysqli_fetch_assoc($rsltvY);
    //$fresultsLW = mysqli_fetch_assoc($rsltvLW);
    $apiresults = array_merge( array( "result" => "success", "query" => $query), $fresults, $fresultsY, $fresultsLW);
?>
