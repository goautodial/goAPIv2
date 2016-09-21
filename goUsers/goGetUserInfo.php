<?php
    #######################################################
    #### Name: goGetUserInfo.php	               ####
    #### Description: API to get specific user	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian Samatra        ####
    ####             Demian Lizandro Biscocho          ####
    #### License: AGPLv2                               ####
    #######################################################
        
    include_once("../goFunctions.php");

    ### POST or GET Variables
    $user_id = $_REQUEST['user_id'];
    $user = $_REQUEST['user'];
        
    ### Check user_id if its null or empty
    if($user_id == null && $user == null) { 
            $apiresults = array("result" => "Error: Set a value for User ID."); 
    } else {
            $groupId = go_get_groupid($goUser);
                        
    if (!checkIfTenant($groupId)) {
        if($user_id != NULL){
                $ul = "vicidial_users.user_id='$user_id'";
                $vul = "vu.user_id='$user_id'";               
        }else if($user != NULL){
                $ul = "vicidial_users.user='$user'";
        }
    } else {
         if($user_id != NULL){
                $ul = "vicidial_users.user_id='$user_id' AND vicidial_users.user_group='$groupId'";
        }else if($user != NULL){
                $ul = "vicidial_users.user='$user' AND vicidial_users.user_group='$groupId'";   
        }
    }
        
        if($user_id != NULL){
                $notAdminSQL = "AND vicidial_live_agents.user_level != '9'";
        }else if($user != NULL){
                $notAdminSQL = "AND vicidial_live_agents.user_level != '9'";
        }
        
        $NOW = date("Y-m-d");
        $query_date =  date('Y-m-d');
        $status = "SALE";
        $date = "BETWEEN '$query_date 00:00:00' AND '$query_date 23:59:59'";
        
        ### DASHBOARD (array, multi-lines)
        $query_OnlineAgents = "SELECT count(*) as 'OnlineAgents' from vicidial_live_agents WHERE vicidial_live_agents.user_level != 4";
        $query_ParkedChannels = "SELECT channel as 'pc_channel',server_ip as 'pc_server_ip',channel_group as 'pc_channel_group',extension as 'pc_extension',parked_by as 'pc_parked_by',parked_time as 'pc_parked_time' from parked_channels limit 1";
        $query_CallerIDsFromVAC = "SELECT callerid as 'vac_callerid',lead_id as 'vac_lead_id,phone_number as 'vac_phone_number' from vicidial_auto_calls limit 1";
        $query_OnlineAgentsNoCalls = "SELECT vicidial_live_agents.extension as 'vla_extension',vicidial_live_agents.user as 'vla_user',vicidial_users.full_name as 'vu_full_name',vicidial_users.user_group as 'vu_user_group',vicidial_users.phone_login as 'vu_phone_login',vicidial_live_agents.conf_exten as 'vla_conf_exten',vicidial_live_agents.status as 'vla_status',vicidial_live_agents.comments as 'vla_comments',vicidial_live_agents.server_ip as 'vla_server_ip',vicidial_live_agents.call_server_ip as 'vla_call_server_ip',UNIX_TIMESTAMP(last_call_time) as 'last_call_time',UNIX_TIMESTAMP(last_call_finish) as last_call_finish,vicidial_live_agents.campaign_id as 'vla_campaign_id',UNIX_TIMESTAMP(last_state_change) as 'last_state_change',vicidial_live_agents.lead_id as 'vla_lead_id',vicidial_live_agents.agent_log_id as 'vla_agent_log_id',vicidial_users.user_id as 'vu_user_id',vicidial_live_agents.callerid as 'vla_callerid' FROM vicidial_live_agents,vicidial_users WHERE vicidial_live_agents.user=vicidial_users.user AND lead_id = 0 AND vicidial_live_agents.user_level != 4 AND $ul $notAdminSQL";
        $query_OnlineAgentsInCalls = "SELECT vicidial_live_agents.extension as 'vla_extension',vicidial_live_agents.user as 'vla_user',vicidial_users.full_name as 'vu_full_name',vicidial_users.user_group as 'vu_user_group',vicidial_users.phone_login as 'vu_phone_login',vicidial_live_agents.conf_exten as 'vla_conf_exten',vicidial_live_agents.status as 'vla_status',vicidial_live_agents.comments as 'vla_comments',vicidial_live_agents.server_ip as 'vla_server_ip',vicidial_live_agents.call_server_ip as 'vla_call_server_ip',UNIX_TIMESTAMP(last_call_time) as 'last_call_time',UNIX_TIMESTAMP(last_call_finish) as last_call_finish,vicidial_live_agents.campaign_id as 'vla_campaign_id',UNIX_TIMESTAMP(last_state_change) as 'last_state_change',vicidial_live_agents.lead_id as 'vla_lead_id',vicidial_live_agents.agent_log_id as 'vla_agent_log_id',vicidial_users.user_id as 'vu_user_id',vicidial_live_agents.callerid as 'vla_callerid',vicidial_list.phone_number as vl_phone_number FROM vicidial_live_agents,vicidial_users,vicidial_list WHERE vicidial_live_agents.user=vicidial_users.user AND vicidial_list.lead_id = vicidial_live_agents.lead_id AND vicidial_live_agents.user_level != 4 AND $ul $notAdminSQL";

        $rsltvInCalls = mysqli_query($link,$query_OnlineAgentsInCalls);
        $rsltvNoCalls = mysqli_query($link,$query_OnlineAgentsNoCalls);
        $rsltvParkedChannels = mysqli_query($link,$query_ParkedChannels);
        $rsltvCallerIDsFromVAC = mysqli_query($link,$query_CallerIDsFromVAC);
        
        ### USER PROFILE (array, multi-lines)
        ### Get phone call details for both inbound and outbound
        $query_InboundCallsAgent = "SELECT vlist.first_name, vlist.last_name, vcl.phone_number, vcl.lead_id,vcl.list_id,campaign_id,call_date,length_in_sec,vcl.status from vicidial_closer_log as vcl, vicidial_users as vu, vicidial_list as vlist where vu.user=vcl.user and vcl.lead_id=vlist.lead_id and $vul limit 100";
        $query_OutboundCallsAgent = "SELECT vlist.first_name, vlist.last_name, vl.phone_number,vl.lead_id,vl.list_id,campaign_id,call_date,length_in_sec,vl.status,vl.called_count from vicidial_log as vl, vicidial_users as vu, vicidial_list as vlist where vu.user=vl.user and vl.lead_id=vlist.lead_id and $vul limit 100";

        $rsltvInCallsAgent = mysqli_query($link,$query_InboundCallsAgent);
        $rsltvOutCallsAgent = mysqli_query($link,$query_OutboundCallsAgent);
        
        ### USER PROFILE (non-array)
        $query_InboundCallsTodayAgent = "SELECT sum(called_count) as incallstoday from vicidial_closer_log as vcl, vicidial_users as vu where vu.user=vcl.user and $vul and vcl.call_date $date";
        $query_InboundSalesTodayAgent = "SELECT count(*) as InboundSales from vicidial_closer_log as vcl, vicidial_agent_log as val, vicidial_users as vu where vcl.uniqueid=val.uniqueid and val.status='$status' and vu.user=vcl.user and $vul and vcl.call_date $date";     
        $query_OutboundCallsTodayAgent = "SELECT sum(called_count) as outcallstoday from vicidial_log as vl, vicidial_users as vu where vu.user=vl.user and $vul and vl.call_date $date";
        $query_OutboundSalesTodayAgent = "SELECT count(*) as OutboundSales from vicidial_log as vl, vicidial_agent_log as val, vicidial_users as vu where vl.uniqueid=val.uniqueid and val.status='$status' and vu.user=vl.user and $vul and vl.call_date $date";

        $rsltvInCallsToday = mysqli_query($link,$query_InboundCallsTodayAgent);
        $rsltvInSalesToday = mysqli_query($link,$query_InboundSalesTodayAgent);
        $rsltvOutCallsToday = mysqli_query($link,$query_OutboundCallsTodayAgent);
        $rsltvOutSalesToday = mysqli_query($link,$query_OutboundSalesTodayAgent);
        
        $resultsincallstoday = mysqli_fetch_assoc($rsltvInCallsToday);
        $resultsoutcallstoday = mysqli_fetch_assoc($rsltvOutCallsToday);        
        $resultsinsales = mysqli_fetch_assoc($rsltvInSalesToday);
        $resultsoutsales = mysqli_fetch_assoc($rsltvOutSalesToday);        
        
        ### GLOBAL (array, single line)
        $query_GetUserInfo = "SELECT user_id, user, full_name, email, user_group, active, user_level, phone_login, phone_pass, voicemail_id, hotkeys_active, vdc_agent_api_access, agent_choose_ingroups, vicidial_recording_override, vicidial_transfers, closer_default_blended, agentcall_manual, scheduled_callbacks, agentonly_callbacks FROM vicidial_users WHERE $ul";
        $rsltvGetUserInfo = mysqli_query($link, $query_GetUserInfo);
        $fresults = mysqli_fetch_assoc($rsltvGetUserInfo);        
        
        //$countrsltvInCalls = mysqli_num_rows($rsltvInCalls);
        //$countrsltvNoCalls = mysqli_num_rows($rsltvNoCalls);

        if ($user != NULL && $query_OnlineAgents != NULL){
        
            $dataInCalls = array();
                while($resultsInCalls = mysqli_fetch_array($rsltvInCalls, MYSQLI_ASSOC)){               
                    array_push($dataInCalls, $resultsInCalls);
                }
                //echo "pre";
                //print_r($dataInCalls);
            $dataNoCalls = array();
                while($resultsNoCalls = mysqli_fetch_array($rsltvNoCalls, MYSQLI_ASSOC)){               
                    array_push($dataNoCalls, $resultsNoCalls);
                }
            $dataParkedChannels = array();
                while($resultsParkedChannels = mysqli_fetch_array($rsltvParkedChannels, MYSQLI_ASSOC)){               
                    array_push($dataParkedChannels, $resultsParkedChannels);
                }
            $dataCallerIDsFromVAC = array();
                while($resultsCallerIDsFromVAC = mysqli_fetch_array($rsltvCallerIDsFromVAC, MYSQLI_ASSOC)){               
                    array_push($dataCallerIDsFromVAC, $resultsCallerIDsFromVAC);
                }
            $data = array_merge($dataInCalls, $dataNoCalls, $dataParkedChannels, $dataCallerIDsFromVAC);  
            
            $apiresults = array("result" => "success", "data" => $data);
            
        } else if ($user_id != NULL){
            $dataInCallsAgent = array();
                while($resultsInCallsAgent = mysqli_fetch_array($rsltvInCallsAgent, MYSQLI_ASSOC)){               
                    array_push($dataInCallsAgent, $resultsInCallsAgent);
                }
            $dataOutCallsAgent = array();
                while($resultsOutCallsAgent = mysqli_fetch_array($rsltvOutCallsAgent, MYSQLI_ASSOC)){               
                    array_push($dataOutCallsAgent, $resultsOutCallsAgent);
                }
            
            $data = array_merge($fresults, $resultsinsales, $resultsoutsales, $resultsincallstoday, $resultsoutcallstoday);
            $apiresults = array("result" => "success", "data" => $data, "agentincalls" => $dataInCallsAgent, "agentoutcalls" => $dataOutCallsAgent);

        }
    }
  

?>
