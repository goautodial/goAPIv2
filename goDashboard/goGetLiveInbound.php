<?php
    ##############################################################
    #### Name: getTotalCalls.php            	    	      ####
    #### Description: API to get total calls		      ####
    #### Version: 0.9                              	      ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014  	      ####
    #### Written by: Jeremiah Sebastian V. Samatra 	      ####
    #### License: AGPLv2                           	      ####
    ##############################################################
    
    include_once("../goFunctions.php");
    
    $groupId = go_get_groupid($session_user);
    
    if (checkIfTenant($groupId)) {
        $ul='vu.user_level != 4';
    } else {
        $stringv = go_getall_allowed_users($groupId);
        $ul = " and vu.user IN ($stringv) and vu.user_level != 4";
    }

   $NOW = date("Y-m-d");

   $query = "select count(*) AS inbound from vicidial_live_agents as vla,vicidial_users as vu where vla.user=vu.user and status = 'INCALL' and comments = 'INBOUND' $ul";
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
