<?php
 /**
 * @file 		goLoginUser.php
 * @brief 		API for Agent UI
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad <chris@goautodial.com>
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
*/

if (isset($_GET['goUserID'])) { $user_id = $astDB->escape($_GET['goUserID']); }
    else if (isset($_POST['goUserID'])) { $user_id = $astDB->escape($_POST['goUserID']); }
if (isset($_GET['isPBP'])) { $isPBP = $astDB->escape($_GET['isPBP']); }
    else if (isset($_POST['isPBP'])) { $isPBP = $astDB->escape($_POST['isPBP']); }

$getUser = $goUser;
if (isset($user_id) && ($user_id !== "" || $user_id != $goUser)) { $getUser = $user_id; }
$user_settings = get_settings('user', $astDB, $getUser);

$CIDdate = date("ymdHis");
$month_old = mktime(11, 0, 0, date("m"), date("d")-2,  date("Y"));
$past_month_date = date("Y-m-d H:i:s",$month_old);
$user = $user_settings->user;
$VU_user_group = $user_settings->user_group;
$phone_login = (isset($phone_login)) ? $phone_login : $user_settings->phone_login;
$phone_pass = (isset($phone_pass)) ? $phone_pass : $user_settings->phone_pass;

if (isset($_GET['goUseWebRTC'])) { $use_webrtc = $astDB->escape($_GET['goUseWebRTC']); }
    else if (isset($_POST['goUseWebRTC'])) { $use_webrtc = $astDB->escape($_POST['goUseWebRTC']); }
if (isset($_GET['goIngroups'])) { $ingroups = $astDB->escape($_GET['goIngroups']); }
    else if (isset($_POST['goIngroups'])) { $ingroups = $astDB->escape($_POST['goIngroups']); }
if (isset($_GET['goCloserBlended'])) { $closer_blended = $astDB->escape($_GET['goCloserBlended']); }
    else if (isset($_POST['goCloserBlended'])) { $closer_blended = $astDB->escape($_POST['goCloserBlended']); }

$closer_blended = (isset($closer_blended)) ? (int) $closer_blended : 0;

### Check if the agent's phone_login is currently connected
$sipIsLoggedIn = check_sip_login($kamDB, $phone_login, 'kamailio', $use_webrtc);

if ($sipIsLoggedIn || $use_webrtc) {
    if (preg_match("/ADMINPORTAL/", $campaign)) {
        $astDB->where('user_group', array('---ALL---', $VU_user_group), 'in');
        $astDB->where('LENGTH(dial_prefix)', '7', '>=');
        $astDB->where('active', 'Y');
        $astDB->orderBy('campaign_id', 'desc');
        $query = $astDB->getOne('vicidial_campaigns', 'campaign_id');
        $campaign = $query['campaign_id'];
    }
    
    $phone_settings = get_settings('phone', $astDB, $phone_login, $phone_pass);
    $campaign_settings = get_settings('campaign', $astDB, $campaign);
    $system_settings = get_settings('system', $astDB);
    $usergroup = get_settings('usergroup', $astDB, $VU_user_group);
    
    if ($system_settings->pass_hash_enabled == '1' && $bcrypt > 0) {
        $user_settings->pass = $user_settings->pass_hash;
    }
    
    $astDB->where('server_ip', $phone_settings->server_ip);
    $query = $astDB->getOne('servers', 'asterisk_version');
    $asterisk_version = $query['asterisk_version'];
    
    $extension = $phone_settings->extension;
    $protocol = $phone_settings->protocol;
    if ($protocol == 'EXTERNAL') {
        $protocol = 'Local';
        $extension = "{$phone_settings->dialplan_number}@{$phone_settings->ext_context}";
    }
    if (preg_match("/Zap/i", $protocol)) {
        if (preg_match("/^1\.0|^1\.2|^1\.4\.1|^1\.4\.20|^1\.4\.21/i", $asterisk_version)) {
            $do_nothing = 1;
        } else {
            $protocol = 'DAHDI';
        }
    }
    
    $SIP_user = "{$protocol}/{$extension}";
    $SIP_user_DiaL = "{$protocol}/{$extension}";
    $qm_extension = "$extension";
    if ( (preg_match('/8300/', $phone_settings->dialplan_number)) and (strlen($phone_settings->dialplan_number) < 5) and ($protocol == 'Local') ) {
        $SIP_user = "{$protocol}/{$extension}{$user_settings->phone_login}";
        $qm_extension = "{$extension}{$user_settings->phone_login}";
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
    
    $query = $astDB->insert('go_agent_sessions', array('sess_agent_user' => $user ,'sess_agent_phone' => $phone_login ,'sess_agent_status' => 'INUSE'));
    
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
        $enable_sipsak = false;
		if ( ($phone_settings->enable_sipsak_messages > 0) and ($system_settings->allow_sipsak_messages > 0) and (preg_match("/SIP/i",$protocol)) ) {
            $enable_sipsak = true;
            $DS = '-';
            $SIPSAK_prefix = 'LIN-';
            $phone_ip = $phone_settings->phone_ip;
            //echo "<!-- sending login sipsak message: $SIPSAK_prefix$VD_campaign -->\n";
            passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$VD_campaign\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
            $SIqueryCID = "$SIPSAK_prefix$VD_campaign$DS$CIDdate";
		}
        
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
        $insertData = array(
            'user' => $user,
            'server_ip' => $phone_settings->server_ip,
            'conf_exten' => $session_id,
            'extension' => $SIP_user,
            'status' => 'PAUSED',
            'lead_id' => '',
            'campaign_id' => $campaign,
            'closer_campaigns' => '',
            'uniqueid' => '',
            'callerid' => '',
            'channel' => '',
            'random_id' => $random,
            'last_call_time' => $NOW_TIME,
            'last_update_time' => $tsNOW_TIME,
            'last_call_finish' => $NOW_TIME,
            'user_level' => $user_settings->user_level,
            'campaign_weight' => $campaign_weight,
            'calls_today' => $calls_today,
            'last_state_change' => $NOW_TIME,
            'outbound_autodial' => $outbound_autodial,
            'manager_ingroup_set' => 'N',
            'on_hook_ring_time' => $phone_settings->phone_ring_timeout,
            'on_hook_agent' => $phone_settings->on_hook_agent,
            'last_inbound_call_time' => $NOW_TIME,
            'last_inbound_call_finish' => $NOW_TIME,
            'campaign_grade' => $campaign_grade
        );
        $query = $astDB->insert('vicidial_live_agents', $insertData);
        
        $insertData = array(
            'user' => $user,
            'server_ip' => $phone_settings->server_ip,
            'event_time' => $NOW_TIME,
            'campaign_id' => $campaign,
            'pause_epoch' => $StarTtimE,
            'pause_sec' => '0',
            'wait_epoch' => $StarTtimE,
            'user_group' => $user_settings->user_group,
            'sub_status' => 'LOGIN'
        );
        $query = $astDB->insert('vicidial_agent_log', $insertData);
        $agent_log_id = $astDB->getInsertId();
        
        ##### insert an entry on vicidial_user_log
        //$stmt = "INSERT INTO vicidial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) values('$user','LOGOUT','$campaign','$NOW_TIME','$StarTtimE','$user_group')";
        $insertData = array(
            'user' => $user,
            'event' => 'LOGIN',
            'campaign_id' => $campaign,
            'event_date' => $NOW_TIME,
            'event_epoch' => $StarTtimE,
            'user_group' => $user_settings->user_group
        );
        $query = $astDB->insert('vicidial_user_log', $insertData);

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
        
        $closer_choice = (isset($ingroups)) ? " " . str_replace("|", " ", $ingroups) . " -" : "-";
        ////$query = $db->query("UPDATE vicidial_live_agents SET closer_campaigns='$closer_choice' WHERE user='$user' AND server_ip='{$phone_settings->server_ip}';");
        $astDB->where('user', $user);
        $astDB->where('server_ip', $phone_settings->server_ip);
        $query = $astDB->update('vicidial_live_agents', array('closer_campaigns' => $closer_choice));
        
        // Inbound Closers
        if ($closer_blended > 0)
			{$vla_autodial = 'Y';}
		else
			{$vla_autodial = 'N';}
		if (preg_match('/INBOUND_MAN|MANUAL/', $campaign_settings->dial_method))
			{$vla_autodial = 'N';}

		if (preg_match("/MGRLOCK/", $closer_choice)) {
			//$stmt="SELECT closer_campaigns FROM vicidial_users where user='$user' LIMIT 1;";
            $astDB->where('user', $user);
            $query = $astDB->getOne('vicidial_users', 'closer_campaigns');
            $closer_choice = $query['closer_campaigns'];

			//$stmt="UPDATE vicidial_live_agents set closer_campaigns='$closer_choice',last_state_change='$NOW_TIME',outbound_autodial='$vla_autodial' where user='$user' and server_ip='$server_ip';";
            $updateData = array(
                'closer_campaigns' => $closer_choice,
                'last_state_change' => $NOW_TIME,
                'outbound_autodial' => $vla_autodial
            );
            $astDB->where('user', $user);
            $astDB->where('server_ip', $phone_settings->server_ip);
            $query = $astDB->update('vicidial_live_agents', $updateData);
		} else {
			//$stmt="UPDATE vicidial_live_agents set closer_campaigns='$closer_choice',last_state_change='$NOW_TIME',outbound_autodial='$vla_autodial' where user='$user' and server_ip='$server_ip';";
            $updateData = array(
                'closer_campaigns' => $closer_choice,
                'last_state_change' => $NOW_TIME,
                'outbound_autodial' => $vla_autodial
            );
            $astDB->where('user', $user);
            $astDB->where('server_ip', $phone_settings->server_ip);
            $query = $astDB->update('vicidial_live_agents', $updateData);

			//$stmt="UPDATE vicidial_users set closer_campaigns='$closer_choice' where user='$user';";
            $astDB->where('user', $user);
            $query = $astDB->update('vicidial_users', array('closer_campaigns' => $closer_choice));
            $user_settings->closer_campaigns = $closer_choice;
        }

		//$stmt="INSERT INTO vicidial_user_closer_log set user='$user',campaign_id='$campaign',event_date='$NOW_TIME',blended='$closer_blended',closer_campaigns='$closer_choice';";
        $insertData = array(
            'user' => $user,
            'campaign_id' => $campaign,
            'event_date' => $NOW_TIME,
            'blended' => $closer_blended,
            'closer_campaigns' => $closer_choice
        );
        $query = $astDB->insert('vicidial_user_closer_log', $insertData);

		//$stmt="DELETE FROM vicidial_live_inbound_agents where user='$user';";
        //$astDB->where('user', $user);
        //$query = $astDB->delete('vicidial_live_inbound_agents');

		$in_groups_pre = preg_replace("/^ | -$/", '', $closer_choice);
		$in_groups = explode(" ", trim($in_groups_pre));
		$in_groups_ct = count($in_groups);
		$k = 0;
		while ($k < $in_groups_ct) {
			if (strlen($in_groups[$k]) > 1) {
				//$stmt="SELECT group_weight,calls_today,group_grade FROM vicidial_inbound_group_agents where user='$user' and group_id='$in_groups[$k]';";
                $astDB->where('user', $user);
                $astDB->where('group_id', $in_groups[$k]);
                $query = $astDB->get('vicidial_inbound_group_agents', null, 'group_weight,calls_today,group_grade');
				$viga_ct = $astDB->getRowCount();
				if ($viga_ct > 0) {
					$row = $query[0];
					$group_weight =	$row['group_weight'];
					$calls_today =	$row['calls_today'];
					$group_grade =	$row['group_grade'];
				} else {
					$group_weight = 0;
					$calls_today =	0;
					$group_grade =  1;
				}
                
				//$stmt="INSERT INTO vicidial_live_inbound_agents set user='$user',group_id='$in_groups[$k]',group_weight='$group_weight',calls_today='$calls_today',last_call_time='$NOW_TIME',last_call_finish='$NOW_TIME',group_grade='$group_grade';";
                $insertData = array(
                    'user' => $user,
                    'group_id' => $in_groups[$k],
                    'group_weight' => $group_weight,
                    'calls_today' => $calls_today,
                    'last_call_time' => $NOW_TIME,
                    'last_call_finish' => $NOW_TIME,
                    'group_grade' => $group_grade
                );
                $query = $astDB->insert('vicidial_live_inbound_agents', $insertData);
            }
			$k++;
        }
    }
    
    $chkStatus = "SHOW TABLES LIKE 'go_statuses'";
    $statusRslt = mysqli_query($linkgo, $chkStatus);
    $statusExist = mysqli_num_rows($statusRslt);
    $statusTBL = '';
    $statusSQL = '';
    if ($statusExist > 0) {
        $statusTBL = ",`$VARDBgo_database`.go_statuses gs";
        $statusSQL = "AND (vcs.status=gs.status AND vcs.campaign_id=gs.campaign_id) ORDER BY priority,vcs.status";
    }
    
    $chkStatusTable = $goDB->rawQuery("SHOW TABLES LIKE 'go_statuses'");
    $statusTableFound = $goDB->getRowCount();
    
    $VARCBstatusesLIST = '';
    $statuses = array();
    $statuses_colors = array();
    $statuses_priority = array();
    $statuses_ct = 0;
    if ($isPBP !== 'Y') {
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
    }
    
    ##### grab the campaign-specific statuses that can be used for dispositioning by an agent
    $astDB->where('vcs.campaign_id', $campaign);
    $astDB->where('selectable', 'Y');
    if ($statusTableFound > 0) {
        $thisColumns = 'vcs.status,status_name,scheduled_callback,priority,color';
        $astDB->join("`$VARDBgo_database`.go_statuses gs", 'vcs.status=gs.status AND vcs.campaign_id=gs.campaign_id', 'LEFT');
        $astDB->orderBy('priority', 'asc')->orderBy('vcs.status', 'asc');
    } else {
        $thisColumns = 'status,status_name,scheduled_callback';
        $astDB->orderBy('vcs.status');
    }
    $query = $astDB->get('vicidial_campaign_statuses vcs', 500, $thisColumns);
    $statuses_camp_ct = $astDB->getRowCount();
    $thisLastQuery = $astDB->getLastQuery();
    foreach ($query as $row) {
        $status = $row['status'];
        $status_name = $row['status_name'];
        $scheduled_callback = $row['scheduled_callback'];
        $priority = $row['priority'];
        $color = $row['color'];
        $statuses[$status] = "{$status_name}";
        $statuses_priority[$status] = "{$priority}";
        $statuses_colors[$status] = "{$color}";
        if ($scheduled_callback == 'Y')
            {$VARCBstatusesLIST .= " {$status}";}
    }
    ksort($statuses);
    ksort($statuses_priority);
    ksort($statuses_colors);
    $statuses_ct = ($statuses_ct + $statuses_camp_ct);
    $VARCBstatusesLIST .= " ";
    
    $astDB->where('campaign_id', $campaign);
    $astDB->orderBy('pause_code', 'asc');
    $rslt = $astDB->get('vicidial_pause_codes', null, 'pause_code,pause_code_name,billable');
    $pause_codes_ct = $astDB->getRowCount();
    $pause_codes = array();
    foreach ($rslt as $row) {
        $pause = $row['pause_code'];
        $pause_name = str_replace("+", " ", $row['pause_code_name']);
        $billable = $row['billable'];
        $pause_codes[$pause] = "{$pause_name}";
        //if ($billable == 'Y')
        //    {$VARCBstatusesLIST .= " {$status}";}
    }
    ksort($pause_codes);
    
    $VARingroups = "''";
    $VARingroup_handlers = "''";
    $INgrpCT = 0;
    $EMAILgrpCT = 0;
    $PHONEgrpCT = 0;
    if ( ($campaign_settings->campaign_allow_inbound == 'Y') && ($campaign_settings->dial_method != 'MANUAL') ) {
        $closer_campaigns = preg_replace("/^ | -$/", "", $campaign_settings->closer_campaigns);
        $closer_campaigns = explode(" ", $closer_campaigns);
        
        //$stmt="select group_id,group_handling from vicidial_inbound_groups where active = 'Y' and group_id IN($closer_campaigns) order by group_id limit 800;";
        $astDB->where('active', 'Y');
        $astDB->where('group_id', $closer_campaigns, 'IN');
        $astDB->orderBy('group_id', 'asc');
        $rslt = $astDB->get('vicidial_inbound_groups', 800, 'group_id,group_handling');

        $closer_ct = $astDB->getRowCount();
        $VARingroups = '';
        $VARingroup_handlers = '';
        $VARemailgroups = array();
        $VARphonegroups = array();
        while ($INgrpCT < $closer_ct) {
            $row = $rslt[$INgrpCT];
            //$VARingroups[$row['group_id']] = $row['group_handling'];
            $VARingroups .= "'".$row['group_id']."',";
            $VARingroup_handlers .= "'".$row['group_handling']."',";
            if ($row['group_handling']=="EMAIL") { // Make a list of ingroups for email handling groups and one for phones, so there is no overlap
                $VARemailgroups[$row['group_id']] = $row['group_handling'];
                $EMAILgrpCT++;
            } else {
                $VARphonegroups[$row['group_id']] = $row['group_handling'];
                $PHONEgrpCT++;
            }
            ksort($VARemailgroups);
            ksort($VARphonegroups);
            $INgrpCT++;
        }
        $VARingroups = rtrim($VARingroups, ",");
        $VARingroup_handlers = rtrim($VARingroup_handlers, ",");
    }
    
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
        $astDB->orderBy('group_id', 'asc');
        $result = $astDB->get('vicidial_inbound_groups', 800, 'group_id,group_name');
        $xfer_ct = $astDB->getRowCount();
        $XFgrpCT = 0;
        while ($XFgrpCT < $xfer_ct) {
            $row = $result[$XFgrpCT];
            //$VARxferGroups[$row['group_id']] = $row['group_name'];
            $VARxferGroups .= "'".$row['group_id']."',";
            $VARxferGroupsNames .= "'".$row['group_name']."',";
            //ksort($VARxferGroups);
            if ($row['group_id'] == "{$campaign_settings->default_xfer_group}") {$default_xfer_group_name = $row['group_name'];}
            $XFgrpCT++;
        }
        $VARxferGroups = rtrim($VARxferGroups, ",");
        $VARxferGroupsNames = rtrim($VARxferGroupsNames, ",");
    }
    
    if ($campaign_settings->alt_number_dialing == 'Y') {
        $alt_phone_dialing = 1;
    } else {
        $alt_phone_dialing = 0;
        $DefaultALTDial = 0;
    }
    
    $campaign_hotkeys = get_settings('hotkeys', $astDB, $campaign);
    $hotkeys = '';
    $hotkeysInfo = '';
    $hotkeysCnt = 0;
    foreach ($campaign_hotkeys as $row) {
        $hotkeys .= "'{$row->hotkey}': '{$row->status}',";
        $hotkeysInfo .= "'{$row->status}': '{$row->status_name}',";
        $hotkeysCnt++;
    }
    $hotkeys = preg_replace('/,$/', '', $hotkeys);
    $hotkeysInfo = preg_replace('/,$/', '', $hotkeysInfo);
    $HK_statuses_camp = $hotkeysCnt;
    
    if (strlen($usergroup->agent_status_viewable_groups) > 2)
        {$agent_status_view = 1;}
    
    if ($usergroup->agent_status_view_time == 'Y')
        {$agent_status_view_time = 1;}
    
    $goDB->where('campaign_id', $campaign);
    $rslt = $goDB->getOne('go_campaigns', 'custom_fields_launch,custom_fields_list_id,url_tab_first_title,url_tab_first_url,url_tab_second_title,url_tab_second_url,manual_dial_min_digits');
    $custom_fields_launch = 'ONCALL';
    $custom_fields_list_id = '';
    $url_tab_first_title = '';
    $url_tab_first_url = '';
    $url_tab_second_title = '';
    $url_tab_second_url = '';
    $manual_dial_min_digits = '';
    if ($goDB->getRowCount() > 0) {
        $custom_fields_launch = $rslt['custom_fields_launch'];
        $custom_fields_list_id = $rslt['custom_fields_list_id'];
        $url_tab_first_title = $rslt['url_tab_first_title'];
        $url_tab_first_url = $rslt['url_tab_first_url'];
        $url_tab_second_title = $rslt['url_tab_second_title'];
        $url_tab_second_url = $rslt['url_tab_second_url'];
        $manual_dial_min_digits = $rslt['manual_dial_min_digits'];
    }
    
    $default_group_alias_cid = '';
    $default_group_alias = $campaign_settings->default_group_alias;
    if (strlen($default_group_alias) > 1) {
        $astDB->where('group_alias_id', $default_group_alias);
        $rslt = $astDB->get('group_alias', null, 'caller_id_number');
        $VDIG_cidnum_ct = $astDB->getRowCount();
        if ($VDIG_cidnum_ct > 0) {
            $row = $rslt[0];
            $default_group_alias_cid = $row['caller_id_number'];
        }
    }

    $default_web_vars = '';
    $astDB->where('campaign_id', $campinfo['campaign_id']);
    $astDB->where('user', $user_name);
    $rslt = $astDB->get('vicidial_campaign_agents', null, 'group_web_vars');
    $VDIG_cidogwv = $astDB->getRowCount();
    if ($VDIG_cidogwv > 0) {
        $row = $rslt[0];
        $default_web_vars =	$row['group_web_vars'];
    }
    
    $chkLocTable = $goDB->rawQuery("SHOW TABLES LIKE 'locations'");
    $locTableFound = $goDB->getRowCount();
    $location = array();
    if ($locTableFound > 0) {
        $goDB->where('u.name', $getUser);
        $goDB->join('locations l', 'u.location_id=l.id', 'LEFT');
        $locRslt = $goDB->getOne('users u', 'u.location_id, l.name, l.description');
        
        if ($goDB->getRowCount() > 0) {
            $location['id'] = $locRslt['location_id'];
            $location['name'] = $locRslt['name'];
            $location['desc'] = $locRslt['description'];
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
        'closer_blended' => $closer_blended,
        'statuses_count' => $statuses_ct,
        'statuses' => $statuses,
        'statuses_priority' => $statuses_priority,
        'statuses_colors' => $statuses_colors,
        'callback_statuses_list' => $VARCBstatusesLIST,
        'pause_codes_count' => $pause_codes_ct,
        'pause_codes' => (array) $pause_codes,
        'XFgroupCOUNT' => $XFgrpCT,
        'VARxferGroups' => $VARxferGroups,
        'VARxferGroupsNames' => $VARxferGroupsNames,
        'INgroupCOUNT' => $INgrpCT,
        'VARingroups' => $VARingroups,
        'VARingroup_handlers' => $VARingroup_handlers,
        'user_settings' => (array) $user_settings,
        'phone_settings' => (array) $phone_settings,
        'campaign_settings' => (array) $campaign_settings,
        'system_settings' => (array) $system_settings,
        'alt_phone_dialing' => $alt_phone_dialing,
        'DefaultALTDial' => $DefaultALTDial,
        'enable_sipsak' => $enable_sipsak,
        'hotkeys' => $hotkeys,
        'hotkeys_content' => $hotkeysInfo,
        'HK_statuses_camp' => $HK_statuses_camp,
        'agent_status_view' => $agent_status_view,
        'agent_status_view_time' => $agent_status_view_time,
        'custom_fields_launch' => $custom_fields_launch,
        'custom_fields_list_id' => $custom_fields_list_id,
        'url_tab_first_title' => $url_tab_first_title,
        'url_tab_first_url' => $url_tab_first_url,
        'url_tab_second_title' => $url_tab_second_title,
        'url_tab_second_url' => $url_tab_second_url,
        'manual_dial_min_digits' => $manual_dial_min_digits,
        'default_group_alias_cid' => $default_group_alias_cid,
        'LIVE_caller_id_number' => $default_group_alias_cid,
        'default_web_vars' => $default_web_vars,
        'LIVE_web_vars' => $default_web_vars
    );

    $APIResult = array( "result" => "success", "data" => $return, "location" => $location, "lastQuery" => $thisLastQuery );
} else {
    $message = "SIP exten '{$phone_login}' is NOT connected";
    if (strlen($phone_login) < 1) {
        $message = "User '$user' does NOT have any phone extension assigned.";
    }
    $APIResult = array( "result" => "error", "message" => $message );
}
?>
