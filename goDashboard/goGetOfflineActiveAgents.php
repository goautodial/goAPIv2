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
    
    $groupId = go_get_groupid($session_user, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
        $ul=' AND vicidial_users.user_level != 4';
    } else { 
        $stringv = go_getall_allowed_users($groupId, $astDB);
        $ul = " AND vicidial_users.user IN ($stringv) AND vicidial_users.user_level != 4";
    }
    
    $query_OfflineActiveAgents = "SELECT vicidial_users.user_id as 'vu_user_id', vicidial_users.user as 'vu_user', vicidial_users.full_name as 'vu_full_name',vicidial_users.user_group as 'vu_user_group', vicidial_users.user_level as 'vu_user_level', vicidial_users.active as 'vu_status' from vicidial_users where vicidial_users.active='Y' AND vicidial_users.user NOT IN (SELECT vicidial_live_agents.user as 'vla_user' from vicidial_live_agents) $ul";     
    
    //$queryGo = "SELECT userid, avatar FROM users";
    $rsltvGo = $goDB->get('users', null, 'userid, avatar');
    $countResultGo = $goDB->getRowCount();
        
    if($countResultGo > 0) {
        $dataGo = array();
        foreach ($rsltvGo as $fresultsGo){
            array_push($dataGo, $fresultsGo);
        }
    }
    
    $rsltvOfflineAgents = $astDB->rawQuery($query_OfflineActiveAgents);
    $data = array();        
    foreach ($rsltvOfflineAgents as $resultsOfflineAgents){               
        array_push($data, $resultsOfflineAgents);            
    }
    
    //$dataM = array_merge($data, $dataGo);        
    $apiresults = array("result" => "success", "data" => $data, "dataGo" => $dataGo);

?>
