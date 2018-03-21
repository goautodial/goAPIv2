<?php
    ####################################################
    #### Name: goGetTotalActiveLeads.php            ####
    #### Description: API to get total active leads ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jeremiah Sebastian V. Samatra  ####
    #### License: AGPLv2                            ####
    ####################################################
    
    $groupId = go_get_groupid($session_user, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_campaigns($groupId, $astDB);
		if($stringv !== "'ALLCAMPAIGNS'")
			$ul = " and vls.campaign_id IN ($stringv)";
		else
			$ul = "";
    }
	
    $query = "SELECT count(*) as getTotalActiveLeads from vicidial_lists as vls,vicidial_list as vl where vl.list_id=vls.list_id and active='Y' $ul"; 
    $fresults = $astDB->rawQuery($query);
    //$fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
