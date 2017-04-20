<?php
    ####################################################
    #### Name: goGetTotalActiveLeads.php            ####
    #### Description: API to get total active leads ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jeremiah Sebastian V. Samatra  ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
	
    $groupId = go_get_groupid($session_user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_campaigns($groupId);
		if($stringv !== "'ALLCAMPAIGNS'")
			$ul = " and vls.campaign_id IN ($stringv)";
		else
			$ul = "";
    }
	
    $query = "SELECT count(*) as getTotalActiveLeads from vicidial_lists as vls,vicidial_list as vl where vl.list_id=vls.list_id and active='Y' $ul"; 
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
