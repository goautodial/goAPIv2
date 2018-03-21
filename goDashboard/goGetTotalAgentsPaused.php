<?php
    ####################################################
    #### Name: goGetTotalAgentsPaused.php           ####
    #### Type: API to get total agents onPaused     ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Jerico James Flores Milo       ####
    ####             Demian Lizandro A. Biscocho    ####    
    #### License: AGPLv2                            ####
    ####################################################
    
    $groupId = go_get_groupid($session_user, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
		$stringv = '';
		$ul="user_level != '4'";
    } else { 
        $stringv = go_getall_allowed_users($groupId, $astDB);
		$ul = " and user IN ($stringv) and user_level != '4'";
    }
    
    $query = "SELECT count(*) as getTotalAgentsPaused FROM vicidial_live_agents WHERE status IN ('PAUSED') $ul"; 
    $data = $astDB->rawQuery($query);
    //$data = mysqli_fetch_assoc($rsltv);
    $apiresults = array("result" => "success", "data" => $data);
	
?>
