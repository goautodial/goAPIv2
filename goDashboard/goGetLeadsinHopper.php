<?php
    ########################################################
    #### Name: goGetLeadsinHopper.php                     ####
    #### Description: API to get total leads in hopper  ####
    #### Version: 0.9                                   ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014       ####
    #### Written by: Jeremiah Sebastian V. Samatra      ####
    #### License: AGPLv2                                ####
    ########################################################
    
    include_once("../goFunctions.php");
    
	$groupId = go_get_groupid($session_user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_campaigns($groupId);
		if($stringv !== "'ALLCAMPAIGNS'")
			$ul = " where campaign_id IN ($stringv)";
		else
			$ul = "";
    }
    $query = "SELECT count(*) as getLeadsinHopper FROM vicidial_hopper $ul"; 
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
