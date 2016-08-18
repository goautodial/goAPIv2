<?php
    ####################################################
    #### Name: goGetAgentsMonitoring.php            ####
    #### Type: API to get total agents onCall       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jerico James Flores Milo       ####
    ####             Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include "goFunctions.php";
    
    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul=' and user_level != 4';
    } else { 
        $stringv = go_getall_allowed_users($groupId);
        $stringv .= "'j'";
        $ul = " and user IN ($stringv) and user_level != 4";
    }
    
    $query = "SELECT extension as 'station',vicidial_users.full_name as 'agent_full_name',vicidial_live_agents.user as 'user',vicidial_users.user_group as 'tenant_id',conf_exten as 'sessionid',status,comments,server_ip,call_server_ip,UNIX_TIMESTAMP(last_call_time) as 'last_call_time',UNIX_TIMESTAMP(last_call_finish) as last_call_finish,campaign_id as 'campaign',UNIX_TIMESTAMP(last_state_change) as 'last_state_change',lead_id,agent_log_id,user_id,vicidial_live_agents.callerid as 'callerid' FROM vicidial_live_agents,vicidial_users WHERE vicidial_live_agents.user=vicidial_users.user AND vicidial_live_agents.user_level != 4 ORDER BY status,last_call_time";
    
    $query_phone = "SELECT phone_number from vicidial_list where lead_id='$lead_id' limit 1";
     
    $rsltv = mysqli_query($link,$query);
    $countResult = mysqli_num_rows($rsltv);

    if($countResult > 0) {
        $data = array();
            while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){               
                array_push($data, $fresults);
            }
            $apiresults = array("result" => "success", "data" => $data);
    } 
?>
