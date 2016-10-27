<?php
    ####################################################
    #### Name: goGetOfflineActiveAgents.php         ####
    #### Type: API to get total agents onCall       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jerico James Flores Milo       ####
    ####             Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################
 
    include_once("../goFunctions.php");
    
    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul=' and user_level != 4';
    } else { 
        $stringv = go_getall_allowed_users($groupId);
        $stringv .= "'j'";
        $ul = " and user IN ($stringv) and user_level != 4";
    }
    
    $query_OfflineActiveAgents = "SELECT vicidial_users.user as 'vu_user', vicidial_users.full_name as 'vu_full_name',vicidial_users.user_group as 'vu_user_group', vicidial_users.user_level as 'vu_user_level', vicidial_users.active as 'vu_status' from vicidial_users where vicidial_users.active='Y' AND vicidial_users.user_level !=4 AND vicidial_users.user NOT IN (SELECT vicidial_live_agents.user as 'vla_user' from vicidial_live_agents)";
     
    $rsltvOfflineAgents = mysqli_query($link,$query_OfflineActiveAgents);

    $data = array();
        
    while($resultsOfflineAgents = mysqli_fetch_array($rsltvOfflineAgents, MYSQLI_ASSOC)){               
        array_push($data, $resultsOfflineAgents);            
    }
            
    $apiresults = array("result" => "success", "data" => $data);

?>
