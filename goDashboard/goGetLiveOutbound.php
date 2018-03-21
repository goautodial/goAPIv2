<?php
    ##############################################################
    #### Name: getTotalCalls.php            	    	      ####
    #### Description: API to get total calls		      ####
    #### Version: 0.9                              	      ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014  	      ####
    #### Written by: Jeremiah Sebastian V. Samatra 	      ####
    #### License: AGPLv2                           	      ####
    ##############################################################
    
    $groupId = go_get_groupid($session_user, $astDB);
    
    if (!checkIfTenant($groupId, $goDB)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_users($groupId, $astDB);
        $ul = " and vu.user IN ($stringv) and vu.user_level != 4";
    }

    $NOW = date("Y-m-d");
 
    $query = "select count(*) AS outbound from vicidial_live_agents as vla,vicidial_users as vu where vla.user=vu.user and status = 'INCALL' and (comments IN ('MANUAL','AUTO') or length(comments) < '1') $ul";
    $fresults = $astDB->rawQuery($query);
    //$fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
