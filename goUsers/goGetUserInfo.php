<?php
/**
 * @file        goGetUserInfo.php
 * @brief       API to get specific user details 
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim H. Abenoja <alex@goautodial.com>
 * @author      Demian Lizandro Biscocho <demianb@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/
    include_once ("goAPI.php");

    // POST or GET Variables
    $user_id = $astDB->escape($_REQUEST['user_id']);
    $user = $astDB->escape($_REQUEST['user']);
    $filter = "default";
    $filter = $astDB->escape($_REQUEST['filter']);
    
    $typeOf = $astDB->escape($_REQUEST['type']);
    
    $ip_address = $astDB->escape($_REQUEST['log_ip']);
    $log_user = $astDB->escape($_REQUEST['log_user']);
    $log_group = $astDB->escape($_REQUEST['log_group']);
        
    // Check user_id if its null or empty
    if($user_id == null && $user == null) {
		$err_msg = error_handle("40001");
        $apiresults = array("code" => "40001","result" => $err_msg);
    } else {
            
			$groupId = go_get_groupid($goUser, $astDB);
            
        if (!checkIfTenant($groupId)) {
            if($user_id != NULL){
                $ul = "vicidial_users.user_id='$user_id'";
                $vul = "and vu.user_id='$user_id'";               
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
        
        // GLOBAL (array, single line)
        $query_GetUserInfo = "
                            SELECT user_id, user, full_name, email, user_group, active, user_level, 
                            phone_login, phone_pass, voicemail_id, hotkeys_active, vdc_agent_api_access, 
                            agent_choose_ingroups, vicidial_recording_override, vicidial_transfers, closer_default_blended, 
                            agentcall_manual, scheduled_callbacks, agentonly_callbacks, agent_lead_search_override 
                            FROM vicidial_users 
                            WHERE $ul
                            ";
                            
        $fresults = $astDB->rawQuery($query_GetUserInfo);
        //$fresults = $rsltvGetUserInfo;
        $num_users = $astDB->getRowCount();   
		
		if($num_users < 1){
			$err_msg = error_handle("41004", "user. Doesn't exist!");
			$apiresults = array("code" => "40001","result" => $err_msg);
		}
		
        // DASHBOARD (array, multi-lines)
        $query_OnlineAgents = "
                                SELECT count(*) as 'OnlineAgents' 
                                FROM vicidial_live_agents 
                                WHERE vicidial_live_agents.user_level != 4
                                ";
        $rsltvOnlineAgents = $astDB->rawQuery($query_OnlineAgents);
        $countResultOnlineAgents = $astDB->getRowCount();
        
        // USER PROFILE (non-array)
        //count only calls with length_in_sec > 0
        $query_InboundCallsTodayAgent = "
                                        SELECT count(vcl.lead_id) as incallstoday 
                                        FROM vicidial_closer_log as vcl, vicidial_users as vu, call_log as cl 
                                        WHERE vcl.user = vu.user $vul 
                                        AND (call_date $date) 
                                        AND vcl.uniqueid = cl.uniqueid 
                                        AND vcl.length_in_sec > 0
                                        ";                                                
        
        //count only 1 sale per lead_id and length_in_sec > 0
        $query_InboundSalesTodayAgent = "
                                        SELECT count(distinct lead_id) as InboundSales 
                                        FROM vicidial_closer_log as vcl, vicidial_users as vu    
                                        WHERE vcl.user = vu.user 
                                        AND (call_date $date)
                                        AND vcl.status = '$status' $vul
                                        AND length_in_sec > 0                                                 
                                        ";
        
        //count only calls with length_in_sec > 0
        $query_OutboundCallsTodayAgent = "
                                        SELECT count(vl.lead_id) as outcallstoday 
                                        FROM vicidial_log as vl, vicidial_users as vu, call_log as cl 
                                        WHERE vl.user = vu.user $vul 
                                        AND (call_date $date) 
                                        AND vl.uniqueid = cl.uniqueid 
                                        AND vl.length_in_sec > 0
                                        ";
        //count only 1 sale per lead_id and length_in_sec > 0
        $query_OutboundSalesTodayAgent = "
                                        SELECT count(distinct lead_id) as OutboundSales 
                                        FROM vicidial_log as vl, vicidial_users as vu  
                                        WHERE vl.user = vu.user 
                                        AND (call_date $date) 
                                        AND vl.status = '$status' $vul 
                                        AND length_in_sec > 0
                                        ";
        
        $resultsincallstoday = $astDB->rawQuery($query_InboundCallsTodayAgent);
        $resultsinsales = $astDB->rawQuery($query_InboundSalesTodayAgent);
        $resultsoutcallstoday = $astDB->rawQuery($query_OutboundCallsTodayAgent);
        $resultsoutsales = $astDB->rawQuery($query_OutboundSalesTodayAgent);
        
        //$resultsincallstoday = mysqli_fetch_assoc($rsltvInCallsToday);
        //$resultsoutcallstoday = mysqli_fetch_assoc($rsltvOutCallsToday);        
        //$resultsinsales = mysqli_fetch_assoc($rsltvInSalesToday);
        //$resultsoutsales = mysqli_fetch_assoc($rsltvOutSalesToday);        
            
        
        if($user_id != NULL){
            $check_location = go_check_user_location(NULL, $user_id);
            
            if($check_location !== 0){
                $queryUserInfoGo = "
                                SELECT us.avatar, us.gcal, us.calendar_apikey, us.calendar_id, lo.id as location_id, lo.name as location_name, lo.description as location_description
                                FROM users us, locations lo
                                WHERE us.location_id = lo.id AND us.userid='$user_id'
                                ";
            }else{
                $queryUserInfoGo = "
                                SELECT avatar, gcal, calendar_apikey, calendar_id FROM users us WHERE userid='$user_id';
                                ";
            }
                                
            $fresultsUserInfoGo = $goDB->rawQuery($queryUserInfoGo) or die($goDB->getLastError());
            //$fresultsUserInfoGo = mysqli_fetch_array($rsltvUserInfoGo, MYSQLI_ASSOC);
            
            if ($filter == "userInfo") {
                if(!empty($fresultsUserInfoGo)) {
                    $data = array_merge($fresults, $fresultsUserInfoGo);
                } else {
                    $data = $fresults;
                }	
                
                $apiresults = array("result" => "success", "data" => $data);
                
                $userInfo = $astDB->rawQuery("SELECT user FROM vicidial_users WHERE user_id='$user_id';");
                //$userInfo = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $user = $userInfo['user'];
                
                if($log_user == "" || $log_user == NULL) {
                    $log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed info of User $user", $log_group);
                } 
                
            } else {
                if(!empty($fresultsUserInfoGo)) {
                    $data = array_merge($fresults, $resultsinsales, $resultsoutsales, $resultsincallstoday, $resultsoutcallstoday, $fresultsUserInfoGo);
                } else {
                    $data = array_merge($fresults, $resultsinsales, $resultsoutsales, $resultsincallstoday, $resultsoutcallstoday);
                }            
                
                $apiresults = array("result" => "success", "data" => $data);
                
            }     
            
        }
        
        if ($user != NULL && $countResultOnlineAgents > 0 ){
            
            $check_location = go_check_user_location($user, NULL);
            
            if($check_location !== 0){
                $queryUserInfoGo = "
                                SELECT us.avatar, us.gcal, us.calendar_apikey, us.calendar_id, lo.id as location_id, lo.name as location_name, lo.description as location_description 
                                FROM users us, locations lo
                                WHERE us.location_id = lo.id AND us.name='$user'
                                ";
            }else{
                $queryUserInfoGo = "SELECT avatar, gcal, calendar_apikey, calendar_id FROM users WHERE name='$user';";
            }
            
            $fresultsUserInfoGo = $godB->rawQuery($queryUserInfoGo);
            //$fresultsUserInfoGo = mysqli_fetch_array($rsltvUserInfoGo, MYSQLI_ASSOC);         
            
            $query_OnlineAgentsInCalls = "
                                        SELECT vicidial_live_agents.extension as 'vla_extension',vicidial_live_agents.user as 'vla_user',
                                            vicidial_live_agents.conf_exten as 'vla_conf_exten',vicidial_live_agents.status as 'vla_status',
                                            vicidial_live_agents.comments as 'vla_comments',vicidial_live_agents.server_ip as 'vla_server_ip',
                                            vicidial_live_agents.call_server_ip as 'vla_call_server_ip',UNIX_TIMESTAMP(last_call_time) as 'last_call_time',
                                            UNIX_TIMESTAMP(last_call_finish) as last_call_finish,vicidial_live_agents.campaign_id as 'vla_campaign_id',
                                            UNIX_TIMESTAMP(last_state_change) as 'last_state_change',vicidial_live_agents.lead_id as 'vla_lead_id',
                                            vicidial_live_agents.agent_log_id as 'vla_agent_log_id',vicidial_users.full_name as 'vu_full_name',
                                            vicidial_users.user_group as 'vu_user_group',vicidial_users.phone_login as 'vu_phone_login', vicidial_users.phone_pass as 'vu_phone_pass',
                                            vicidial_users.user_id as 'vu_user_id',vicidial_users.user as 'vu_user',vicidial_live_agents.callerid as 'vla_callerid',
                                            vicidial_list.phone_number as 'vl_phone_number',vicidial_agent_log.sub_status as 'vla_pausecode', 
                                            vicidial_campaigns.campaign_name as 'vla_campaign_name' 
                                        FROM vicidial_live_agents,vicidial_users,vicidial_list,vicidial_agent_log,vicidial_campaigns 
                                        WHERE vicidial_live_agents.campaign_id=vicidial_campaigns.campaign_id 
                                        AND vicidial_live_agents.user=vicidial_users.user AND vicidial_list.lead_id = vicidial_live_agents.lead_id 
                                        AND vicidial_live_agents.user_level != 4 AND vicidial_live_agents.agent_log_id=vicidial_agent_log.agent_log_id  
                                        AND vicidial_live_agents.user='$user' 
                                        ORDER BY last_call_time
                                        ";
                                        
            $rsltvOnlineAgentsInCalls = $astDB->rawQuery($query_OnlineAgentsInCalls);
            $countResultOnlineAgentsInCalls = $astDB->getRowCount();
            $rsltvInCalls = $astDB->rawQuery($query_OnlineAgentsInCalls);              
            
            $query_OnlineAgentsNoCalls = "
                                        SELECT vicidial_live_agents.extension as 'vla_extension',vicidial_live_agents.user as 'vla_user',
                                            vicidial_users.full_name as 'vu_full_name',vicidial_users.user_group as 'vu_user_group',
                                            vicidial_users.phone_login as 'vu_phone_login',vicidial_users.phone_pass as 'vu_phone_pass',vicidial_live_agents.conf_exten as 'vla_conf_exten',
                                            vicidial_live_agents.status as 'vla_status',vicidial_live_agents.comments as 'vla_comments',
                                            vicidial_live_agents.server_ip as 'vla_server_ip',vicidial_live_agents.call_server_ip as 'vla_call_server_ip',
                                            UNIX_TIMESTAMP(last_call_time) as 'last_call_time',UNIX_TIMESTAMP(last_call_finish) as last_call_finish,
                                            vicidial_live_agents.campaign_id as 'vla_campaign_id',UNIX_TIMESTAMP(last_state_change) as 'last_state_change',
                                            vicidial_live_agents.lead_id as 'vla_lead_id',vicidial_live_agents.agent_log_id as 'vla_agent_log_id',
                                            vicidial_users.user_id as 'vu_user_id',vicidial_users.user as 'vu_user',vicidial_live_agents.callerid as 'vla_callerid',
                                            vicidial_agent_log.sub_status as 'vla_pausecode', vicidial_campaigns.campaign_name as 'vla_campaign_name' 
                                        FROM vicidial_live_agents,vicidial_users,vicidial_agent_log,vicidial_campaigns 
                                        WHERE vicidial_live_agents.campaign_id=vicidial_campaigns.campaign_id 
                                        AND vicidial_live_agents.user=vicidial_users.user 
                                        AND vicidial_live_agents.lead_id = 0 
                                        AND vicidial_live_agents.user_level != 4 
                                        AND vicidial_live_agents.agent_log_id=vicidial_agent_log.agent_log_id 
                                        AND vicidial_live_agents.user='$user' 
                                        ORDER BY last_call_time
                                        ";          
                                        
            $rsltvNoCalls = $astDB->rawQuery($query_OnlineAgentsNoCalls);                                                                          
        
            if ($countResultOnlineAgentsInCalls > 0) {                                                      
                
                $dataInCalls = array();                
                foreach ($rsltvInCalls as $resultsInCalls){   
                    $callerid = $resultsInCalls['vla_callerid'];
                    array_push($dataInCalls, $resultsInCalls);                                       
                }
                
                $query_CallerIDsFromVAC = "
                                        SELECT callerid as 'vac_callerid',lead_id as 'vac_lead_id',phone_number as 'vac_phone_number' 
                                        FROM vicidial_auto_calls 
                                        WHERE callerid='$callerid' 
                                        LIMIT 1
                                        ";
                                        
                $rsltvCallerIDsFromVAC = $astDB->rawQuery($query_CallerIDsFromVAC);
                
                $dataCallerIDsFromVAC = array();
                foreach ($rsltvCallerIDsFromVAC as $resultsCallerIDsFromVAC) {
                    array_push($dataCallerIDsFromVAC, $resultsCallerIDsFromVAC);
                }
                
                $query_ParkedChannels = "
                                        SELECT channel as 'pc_channel',server_ip as 'pc_server_ip',channel_group as 'pc_channel_group',
                                            extension as 'pc_extension',parked_by as 'pc_parked_by',parked_time as 'pc_parked_time' 
                                        FROM parked_channels 
                                        WHERE channel_group='$callerid'
                                        ";
                                        
                $rsltvParkedChannels = $astDB->rawQuery($query_ParkedChannels);      
                
                $dataParkedChannels = array();
                foreach ($rsltvParkedChannels as $resultsParkedChannels){
                    array_push($dataParkedChannels, $resultsParkedChannels);
                }                             
                
                $data = $dataInCalls;
                
            } else {
            
                $dataNoCalls = array();
                foreach ($rsltvNoCalls as $resultsNoCalls) {
                    array_push($dataNoCalls, $resultsNoCalls);
                }    
                
                $data = $dataNoCalls;
            
            }        
            
            $log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed info of User $user", $log_group);
            
            $apiresults = array("result" => "success", "data" => $data, "parked" => $dataParkedChannels, "callerids" => $dataCallerIDsFromVAC, "dataGo" => $fresultsUserInfoGo);
            
        }
    }
  

?>
