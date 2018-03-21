<?php
    ####################################################
    #### Name: goGetTotalAgentsWaitCalls.php        ####
    #### Type: API to get total agents onWaitCalls  ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Jerico James Flores Milo       ####
    ####             Demian Lizandro A. Biscocho    ####
    #### License: AGPLv2                            ####
    ####################################################
    
    $groupId = go_get_groupid($session_user, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
		$ul = " and user_level != '4'";
    } else { 
        $stringv = go_getall_allowed_users($groupId, $astDB);
		$ul = " and user IN ($stringv) and user_level != '4'";
    }
    
    $query = "SELECT count(*) as getTotalAgentsWaitCalls FROM vicidial_live_agents WHERE status IN ('READY','CLOSER') $ul"; 
    $data = $astDB->rawQuery($query);
    //$data = mysqli_fetch_assoc($rsltv);
    $apiresults = array("result" => "success", "data" => $data);
?>
