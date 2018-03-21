<?php
    ####################################################
    #### Name: goGetTotalAgentsOnline.php           ####
    #### Type: API to get total agents online       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jerico James Flores Milo       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    $groupId = go_get_groupid($session_user, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
		$stringv = '';
        $ul_online='';
		$ul_calls='';
    } else { 
        $stringv = go_getall_allowed_users($groupId, $astDB);
		$ul = " and user IN ($stringv) and user_level != '4'";
    }
    
    $query = "SELECT count(*) as getTotalAgentsOnline FROM vicidial_live_agents $ul"; 
    $data = $astDB->rawQuery($query);
    //$data = mysqli_fetch_assoc($rsltv);
    $apiresults = array("result" => "success", "data" => $data);
?>
