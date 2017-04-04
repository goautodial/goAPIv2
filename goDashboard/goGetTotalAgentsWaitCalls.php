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
    
    include_once("../goFunctions.php");
	
    $user = mysqli_real_escape_string($link, $_POST['user']);
    $groupId = go_get_groupid($user);
    
    if (checkIfTenant($groupId)) {
		$ul="user_level != '4'";
    } else { 
        $stringv = go_getall_allowed_users($groupId);
		$ul = " and user IN ($stringv) and user_level != '4'";
    }
    
    $query = "SELECT count(*) as getTotalAgentsWaitCalls FROM vicidial_live_agents WHERE status IN ('READY','CLOSER') $ul"; 
    $rsltv = mysqli_query($link, $query);
    $data = mysqli_fetch_assoc($rsltv);
    $apiresults = array("result" => "success", "data" => $data);
?>
