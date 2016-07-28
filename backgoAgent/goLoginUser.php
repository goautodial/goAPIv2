<?php
####################################################
#### Name: goLoginPhone.php                     ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$agent = get_settings('user', $astDB, $goUser);

$CIDdate = date("ymdHis");
$month_old = mktime(11, 0, 0, date("m"), date("d")-2,  date("Y"));
$past_month_date = date("Y-m-d H:i:s",$month_old);
$user = $agent->user;
$VU_user_group = $agent->user_group;
$phone_login = (isset($phone_login)) ? $phone_login : $agent->phone_login;
$phone_pass = (isset($phone_pass)) ? $phone_pass : $agent->phone_pass;

### Check if the agent's phone_login is currently connected
$sipIsLoggedIn = check_sip_login($phone_login);

if ($sipIsLoggedIn) {
    $phone_settings = get_settings('phone', $astDB, $phone_login, $phone_pass);
    $campaign_settings = get_settings('campaign', $astDB, $campaign);
    $system_settings = get_settings('system', $astDB);
    
    $astDB->where('server_ip', $phone_settings->server_ip);
    $query = $astDB->getOne('servers', 'asterisk_version');
    $asterisk_version = $query['asterisk_version'];
    
    $extension = $phone_settings->extension;
    if ($phone_settings->protocol == 'EXTERNAL') {
        $protocol = 'Local';
        $extension = "{$phone_settings->dialplan_number}@{$phone_settings->ext_context}";
    }
    if (preg_match("/Zap/i",$phone_settings->protocol)) {
        if (preg_match("/^1\.0|^1\.2|^1\.4\.1|^1\.4\.20|^1\.4\.21/i", $asterisk_version)) {
            $do_nothing = 1;
        } else {
            $protocol = 'DAHDI';
        }
    }
    
    $SIP_user = "{$protocol}/{$extension}";
    $SIP_user_DiaL = "{$protocol}/{$extension}";
    $qm_extension = "$extension";
    if ( (preg_match('/8300/',$phone_settings->dialplan_number)) and (strlen($phone_settings->dialplan_number)<5) and ($protocol == 'Local') ) {
        $SIP_user = "{$protocol}/{$extension}{$agent->phone_login}";
        $qm_extension = "{$extension}{$agent->phone_login}";
    }
    
    $session_ext = preg_replace("/[^a-z0-9]/i", "", $extension);
    if (strlen($session_ext) > 10) {$session_ext = substr($session_ext, 0, 10);}
    $session_rand = (rand(1,9999999) + 10000000);
    $session_name = "$StarTtimE$US$session_ext$session_rand";
    
    $astDB->where('start_time', $past_month_date, '<');
    $astDB->where('extension', $extension);
    $astDB->where('server_ip', $phone_settings->server_ip);
    $astDB->where('program', 'vicidial');
    $query = $astDB->delete('web_client_sessions');
    
    $query = $astDB->insert('web_client_sessions', array('extension' => $extension, 'server_ip' => $phone_settings->server_ip, 'program' => 'vicidial', 'start_time' => $NOW_TIME, 'session_name' => $session_name));
    
    $astDB->where('campaign_id', $campaign);
    $query = $astDB->getOne('vicidial_hopper', 'count(*) AS cnt');
    $campaign_leads_to_call = $query['cnt'];
    if ( ( ($campaign_settings->campaign_allow_inbound == 'Y') and ($campaign_settings->dial_method != 'MANUAL') ) || ($campaign_leads_to_call > 0) || (preg_match('/Y/',$campaign_settings->no_hopper_leads_logins)) ) {
        ##### check to see if the user has a conf extension already, this happens if they previously exited uncleanly
        //$query = $db->query("SELECT conf_exten FROM vicidial_conferences WHERE extension='$SIP_user' AND server_ip = '{$phone_settings->server_ip}' LIMIT 1;");
        $astDB->where('extension', $SIP_user);
        $astDB->where('server_ip', $phone_settings->server_ip);
        $query = $astDB->getOne('vicidial_conferences', 'conf_exten');
        $prev_login_ct = $astDB->getRowCount();
        
        $i=0;
        while ($i < $prev_login_ct) {
            $session_id = $query['conf_exten'];
            $i++;
        }
        
        if ($prev_login_ct > 0) {
            //var_dump("USING PREVIOUS MEETME ROOM - $session_id - $NOW_TIME - $SIP_user");
        } else {
            ##### grab the next available vicidial_conference room and reserve it
            $astDB->where('server_ip', $phone_settings->server_ip);
            $astDB->where('extension', '');
            $astDB->orWhere('extension', null);
            $query = $astDB->get('vicidial_conferences');
            if ($astDB->getRowCount() > 0) {
                $query = $astDB->rawQuery("UPDATE vicidial_conferences SET extension='$SIP_user', leave_3way='0' WHERE server_ip='{$phone_settings->server_ip}' AND ((extension='') OR (extension=null)) LIMIT 1");
    
                $astDB->where('server_ip', $phone_settings->server_ip);
                $astDB->where('extension', $SIP_user);
                $astDB->orWhere('extension', $user);
                $query = $astDB->getOne('vicidial_conferences', 'conf_exten');
                $session_id = $query['conf_exten'];
            }
            
            //var_dump("USING NEW MEETME ROOM - $session_id - $NOW_TIME - $SIP_user");
        }
        
        ##### clearing records from vicidial_live_agents and vicidial_live_inbound_agents
        $astDB->where('user', $user);
        $query = $astDB->delete('vicidial_live_agents');
        $astDB->where('user', $user);
        $query = $astDB->delete('vicidial_live_inbound_agents');
                    
        ##### insert a NEW record to the vicidial_manager table to be processed
        $SIqueryCID = "S{$CIDdate}{$session_id}";
        $TEMP_SIP_user_DiaL = $SIP_user_DiaL;
        if ($phone_settings->on_hook_agent == 'Y')
            {$TEMP_SIP_user_DiaL = 'Local/8300@default';}
        $agent_login_data = "||$NOW_TIME|NEW|N|{$phone_settings->server_ip}||Originate|$SIqueryCID|Channel: $TEMP_SIP_user_DiaL|Context: {$phone_settings->ext_context}|Exten: $session_id|Priority: 1|Callerid: $SIqueryCID|||||";
        $insertData = array(
            'man_id' => '',
            'uniqueid' => '',
            'entry_date' => $NOW_TIME,
            'status' => 'NEW',
            'response' => 'N',
            'server_ip' => $phone_settings->server_ip,
            'channel' => '',
            'action' => 'Originate',
            'callerid' => $SIqueryCID,
            'cmd_line_b' => "Channel: $TEMP_SIP_user_DiaL",
            'cmd_line_c' => "Context: {$phone_settings->ext_context}",
            'cmd_line_d' => "Exten: $session_id",
            'cmd_line_e' => 'Priority: 1',
            'cmd_line_f' => "Callerid: \"$SIqueryCID\" <{$campaign_settings->campaign_cid}>",
            'cmd_line_g' => '',
            'cmd_line_h' => '',
            'cmd_line_i' => '',
            'cmd_line_j' => '',
            'cmd_line_k' => ''
        );
        $query = $astDB->insert('vicidial_manager', $insertData);
        
        $WebPhonEurl = '';
        $astDB->where('user', $user);
        $query = $astDB->delete('vicidial_session_data');
        
        $query = $astDB->insert('vicidial_session_data', array('session_name' => $session_name, 'user' => $user, 'campaign_id' => $campaign, 'server_ip' => $phone_settings->server_ip, 'conf_exten' => $session_id, 'extension' => $extension, 'login_time' => $NOW_TIME, 'webphone_url' => $WebPhonEurl, 'agent_login_call' => $agent_login_data));
        
        $astDB->where('user', $user);
        $astDB->where('campaign_id', $campaign);
        $query = $astDB->getOne('vicidial_campaign_agents', 'campaign_weight,calls_today,campaign_grade');
        
        if ($astDB->getRowCount() > 0) {
            $campaign_weight = $query['campaign_weight'];
            $calls_today = $query['calls_today'];
            $campaign_grade = $query['campaign_grade'];
        } else {
            $campaign_weight = '0';
            $calls_today = '0';
            $campaign_grade = '1';
            
            $insertData = array(
                'user' => $user,
                'campaign_id' => $campaign,
                'campaign_rank' => '0',
                'campaign_weight' => '0',
                'calls_today' => $calls_today,
                'campaign_grade' => $campaign_grade
            );
            $query = $astDB->insert('vicidial_campaign_agents', $insertData);
        }
        
        if ($campaign_settings->auto_dial_level > 0) {
            $outbound_autodial = 'Y';
        } else {
            $outbound_autodial = 'N';
        }
        
        $random = (rand(1000000, 9999999) + 10000000);
        $query = $astDB->rawQuery("INSERT INTO vicidial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,closer_campaigns,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,user_level,campaign_weight,calls_today,last_state_change,outbound_autodial,manager_ingroup_set,on_hook_ring_time,on_hook_agent,last_inbound_call_time,last_inbound_call_finish,campaign_grade) values('$user','{$phone_settings->server_ip}','$session_id','$SIP_user','PAUSED','','$campaign','','','','','$random','$NOW_TIME','$tsNOW_TIME','$NOW_TIME','{$user_settings->user_level}', '$campaign_weight', '$calls_today','$NOW_TIME','$outbound_autodial','N','{$phone_settings->phone_ring_timeout}','{$phone_settings->on_hook_agent}','$NOW_TIME','$NOW_TIME','$campaign_grade')");
                
        $query = $astDB->rawQuery("INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) values('$user','{$phone_settings->server_ip}','$NOW_TIME','$campaign','$StarTtimE','0','$StarTtimE','{$user_settings->user_group}','LOGIN')");
        $agent_log_id = $astDB->getInsertId();
        
        ////$query = $db->query("UPDATE vicidial_campaigns SET campaign_logindate='$NOW_TIME' WHERE campaign_id='$campaign';");
        $astDB->where('campaign_id', $campaign);
        $query = $astDB->update('vicidial_campaigns', array('campaign_logindate' => $NOW_TIME));
        
        ////$query = $db->query("UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id' where user='$user';");
        $astDB->where('user', $user);
        $query = $astDB->update('vicidial_live_agents', array('agent_log_id' => $agent_log_id));
        
        ////$query = $db->query("UPDATE vicidial_users SET shift_override_flag='0' where user='$user' and shift_override_flag='1';");
        $astDB->where('user', $user);
        $astDB->where('shift_override_flag', '1');
        $query = $astDB->update('vicidial_users', array('shift_override_flag' => '0'));
        
        $closer_campaigns = (strlen($_POST['goIngroups'])) ? " " . implode(" ", $_POST['goIngroups']) . " -" : "";
        ////$query = $db->query("UPDATE vicidial_live_agents SET closer_campaigns='$closer_campaigns' WHERE user='$user' AND server_ip='{$phone_settings->server_ip}';");
        $astDB->where('user', $user);
        $astDB->where('server_ip', $phone_settings->server_ip);
        $query = $astDB->update('vicidial_live_agents', array('closer_campaigns' => $closer_campaigns));
    }
    
    $VARCBstatusesLIST = '';
    ##### grab the statuses that can be used for dispositioning by an agent
    $astDB->where('selectable', 'Y');
    $astDB->orderBy('status');
    $query = $astDB->get('vicidial_statuses', 500, 'status,status_name,scheduled_callback');
    $statuses_ct = $astDB->getRowCount();
    foreach ($query as $row) {
        $status = $row['status'];
        $status_name = $row['status_name'];
        $scheduled_callback = $row['scheduled_callback'];
        $statuses[$status] = "{$status_name}";
        if ($scheduled_callback == 'Y')
            {$VARCBstatusesLIST .= " {$status}";}
    }
    
    ##### grab the campaign-specific statuses that can be used for dispositioning by an agent
    $astDB->where('selectable', 'Y');
    $astDB->where('campaign_id', $campaign);
    $astDB->orderBy('status');
    $query = $astDB->get('vicidial_campaign_statuses', 500, 'status,status_name,scheduled_callback');
    $statuses_camp_ct = $astDB->getRowCount();
    foreach ($query as $row) {
        $status = $row['status'];
        $status_name = $row['status_name'];
        $scheduled_callback = $row['scheduled_callback'];
        $statuses[$status] = "{$status_name}";
        if ($scheduled_callback == 'Y')
            {$VARCBstatusesLIST .= " {$status}";}
    }
    ksort($statuses);
    $statuses_ct = ($statuses_ct + $statuses_camp_ct);
    $VARCBstatusesLIST .= " ";
    
    $xfer_groups = preg_replace("/^ | -$/", "", $campaign_settings->xfer_groups);
    $xfer_groups = explode(" ", $xfer_groups);
    ////$xfer_groups = preg_replace("/ /", "','", $xfer_groups);
    ////$xfer_groups = "'$xfer_groups'";
    $XFgrpCT = 0;
    $VARxferGroups = "''";
    $VARxferGroupsNames = '';
    $default_xfer_group_name = '';
    if ($campaign_settings->allow_closers == 'Y') {
        $VARxferGroups = '';
        $astDB->where('active', 'Y');
        $astDB->where('group_id', $xfer_groups, 'IN');
        $astDB->orderBy('group_id');
        $result = $astDB->get('vicidial_inbound_groups', 800, 'group_id,group_name');
        $xfer_ct = $astDB->getRowCount();
        $XFgrpCT = 0;
        while ($XFgrpCT < $xfer_ct) {
            $row = $result[$XFgrpCT];
            $VARxferGroups[$row['group_id']] = $row['group_name'];
            ksort($VARxferGroups);
            if ($row['group_id'] == "{$campaign_settings->default_xfer_group}") {$default_xfer_group_name = $row['group_name'];}
            $XFgrpCT++;
        }
    }
    
    $return = array(
        'user' => $user,
        'agent_log_id' => $agent_log_id,
        'start_time' => $StarTtimE,
        'now_time' => $NOW_TIME,
        'file_time' => $FILE_TIME,
        'login_date' => $loginDATE,
        'protocol' => $protocol,
        'extension' => $extension,
        'conf_exten' => $session_id,
        'session_id' => $session_id,
        'session_name' => $session_name,
        'server_ip' => $phone_settings->server_ip,
        'asterisk_version' => $asterisk_version,
        'SIP' => $SIP_user,
        'qm_extension' => $qm_extension,
        'statuses_count' => $statuses_ct,
        'statuses' => $statuses,
        'callback_statuses_list' => $VARCBstatusesLIST,
        'xfer_group_count' => $XFgrpCT,
        'xfer_groups' => $VARxferGroups,
        'user_settings' => (array) $agent,
        'phone_settings' => (array) $phone_settings,
        'campaign_settings' => (array) $campaign_settings,
        'system_settings' => (array) $system_settings
    );

    $APIResult = array( "result" => "success", "data" => $return );
} else {
    $APIResult = array( "result" => "error", "message" => "SIP exten '{$phone_login}' is NOT connected" );
}
?>