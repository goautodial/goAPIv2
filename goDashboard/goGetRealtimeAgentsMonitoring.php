<?php
    ####################################################
    #### Name: goGetRealtimeAgentsMonitoring.php    ####
    #### Type: API to get total agents onCall       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jerico James Flores Milo       ####
    ####             Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    $groupId = go_get_groupid($session_user, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
		$stringv = '';
        $ul_online='';
		$ul_calls='';
    } else { 
        $stringv = go_getall_allowed_users($groupId, $astDB);
        $ul_online = " and user IN ($stringv)";
		$ul_calls = " and vicidial_live_agents.user IN ($stringv)";
    }
    
    $query_OnlineAgents = "SELECT count(*) as 'OnlineAgents' from vicidial_live_agents WHERE (user_level < 7 AND user_level != 4) $ul_online";
    //$query_ParkedChannels = "SELECT channel as 'pc_channel' from parked_channels";
    $query_ParkedChannels = "SELECT channel as 'pc_channel',server_ip as 'pc_server_ip',channel_group as 'pc_channel_group',extension as 'pc_extension',parked_by as 'pc_parked_by',UNIX_TIMESTAMP(parked_time) as 'pc_parked_time' from parked_channels";
    $query_CallerIDsFromVAC = "SELECT callerid as 'vac_callerid',lead_id as 'vac_lead_id',phone_number as 'vac_phone_number' from vicidial_auto_calls";
    $query_OnlineAgentsNoCalls = "SELECT vicidial_live_agents.extension as 'vla_extension',vicidial_live_agents.user as 'vla_user',vicidial_users.full_name as 'vu_full_name',vicidial_users.user_group as 'vu_user_group',vicidial_users.phone_login as 'vu_phone_login',vicidial_live_agents.conf_exten as 'vla_conf_exten',vicidial_live_agents.status as 'vla_status',vicidial_live_agents.comments as 'vla_comments',vicidial_live_agents.server_ip as 'vla_server_ip',vicidial_live_agents.call_server_ip as 'vla_call_server_ip',UNIX_TIMESTAMP(last_call_time) as 'last_call_time',UNIX_TIMESTAMP(last_call_finish) as last_call_finish,vicidial_live_agents.campaign_id as 'vla_campaign_id',UNIX_TIMESTAMP(last_state_change) as 'last_state_change',vicidial_live_agents.lead_id as 'vla_lead_id',vicidial_live_agents.agent_log_id as 'vla_agent_log_id',vicidial_users.user_id as 'vu_user_id',vicidial_users.user as 'vu_user',vicidial_live_agents.callerid as 'vla_callerid',vicidial_agent_log.sub_status as 'vla_pausecode', vicidial_campaigns.campaign_name as 'vla_campaign_name' FROM vicidial_live_agents,vicidial_users,vicidial_agent_log,vicidial_campaigns WHERE vicidial_live_agents.campaign_id=vicidial_campaigns.campaign_id AND vicidial_live_agents.user=vicidial_users.user AND vicidial_live_agents.lead_id = 0 AND vicidial_live_agents.user_level != 4 AND vicidial_live_agents.agent_log_id=vicidial_agent_log.agent_log_id $ul_calls ORDER BY last_call_time";
    $query_OnlineAgentsInCalls = "SELECT vicidial_live_agents.extension as 'vla_extension',vicidial_live_agents.user as 'vla_user',vicidial_live_agents.conf_exten as 'vla_conf_exten',vicidial_live_agents.status as 'vla_status',vicidial_live_agents.comments as 'vla_comments',vicidial_live_agents.server_ip as 'vla_server_ip',vicidial_live_agents.call_server_ip as 'vla_call_server_ip',UNIX_TIMESTAMP(last_call_time) as 'last_call_time',UNIX_TIMESTAMP(last_call_finish) as last_call_finish,vicidial_live_agents.campaign_id as 'vla_campaign_id',UNIX_TIMESTAMP(last_state_change) as 'last_state_change',vicidial_live_agents.lead_id as 'vla_lead_id',vicidial_live_agents.agent_log_id as 'vla_agent_log_id',vicidial_users.full_name as 'vu_full_name',vicidial_users.user_group as 'vu_user_group',vicidial_users.phone_login as 'vu_phone_login',vicidial_users.user_id as 'vu_user_id',vicidial_users.user as 'vu_user',vicidial_live_agents.callerid as 'vla_callerid',vicidial_list.phone_number as 'vl_phone_number',vicidial_agent_log.sub_status as 'vla_pausecode', vicidial_campaigns.campaign_name as 'vla_campaign_name' FROM vicidial_live_agents,vicidial_users,vicidial_list,vicidial_agent_log,vicidial_campaigns WHERE vicidial_live_agents.campaign_id=vicidial_campaigns.campaign_id AND vicidial_live_agents.user=vicidial_users.user AND vicidial_list.lead_id = vicidial_live_agents.lead_id AND vicidial_live_agents.user_level != 4 AND vicidial_live_agents.agent_log_id=vicidial_agent_log.agent_log_id $ul_calls ORDER BY last_call_time";
     
    $rsltvInCalls = $astDB->rawQuery($query_OnlineAgentsInCalls);
    $rsltvNoCalls = $astDB->rawQuery($query_OnlineAgentsNoCalls);
    $rsltvParkedChannels = $astDB->rawQuery($query_ParkedChannels);
    $rsltvCallerIDsFromVAC = $astDB->rawQuery($query_CallerIDsFromVAC);
    
    $queryGo = "SELECT userid, avatar FROM users";    
    $rsltvGo = $goDB->get('users', null, 'userid, avatar');
    $countResultGo = $goDB->getRowCount();  
        
    if($countResultGo > 0) {
        $dataGo = array();
        foreach ($rsltvGo as $fresultsGo){
            array_push($dataGo, $fresultsGo);
        }
    }    

    if($query_OnlineAgents != NULL) {
    
        $dataInCalls = array();        
            foreach ($rsltvInCalls as $resultsInCalls){              
                array_push($dataInCalls, $resultsInCalls);
            }
        $dataCallerIDsFromVAC = array();
            foreach ($rsltvCallerIDsFromVAC as $resultsCallerIDsFromVAc){               
                array_push($dataCallerIDsFromVAC, $resultsCallerIDsFromVAc);
            }
        $dataParkedChannels = array();
            foreach ($rsltvParkedChannels as $resultsParkedChannels){               
                array_push($dataParkedChannels, $resultsParkedChannels);
            }                        
        $dataNoCalls = array();        
            foreach ($rsltvNoCalls as $resultsNoCalls){               
                array_push($dataNoCalls, $resultsNoCalls);
            }                

        $data = array_merge($dataInCalls, $dataNoCalls);
        $apiresults = array("result" => "success", "data" => $data, "dataGo" => $dataGo, "parked" => $dataParkedChannels, "callerids" => $dataCallerIDsFromVAC, "query" => $query_OnlineAgents);
    
    }
?>
