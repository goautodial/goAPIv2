<?php
 /**
 * @file 		goGetLoginInfo.php
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

$SIP_server = (!isset($SIP_server)) ? 'kamailio' : $SIP_server;

$astDB->where('user', $user_id);
$userinfo = $astDB->getOne('vicidial_users', 'user,pass,phone_login,phone_pass,full_name,user_level,hotkeys_active,agent_choose_ingroups,scheduled_callbacks,agentonly_callbacks,agentcall_manual,vicidial_recording,vicidial_transfers,closer_default_blended,user_group,vicidial_recording_override,alter_custphone_override,alert_enabled,agent_shift_enforcement_override,shift_override_flag,allow_alerts,closer_campaigns,agent_choose_territories,custom_one,custom_two,custom_three,custom_four,custom_five,agent_call_log_view_override,agent_choose_blended,agent_lead_search_override,preset_contact_search');
$userExist = $astDB->getRowCount();

$is_logged_in = check_agent_login($astDB, $userinfo['user']);
$data = array( 'is_logged_in' => $is_logged_in );

$forever_stop = 0;
$HKuser_level = 1;
$user_name = $userinfo['user'];
$user_abb = "{$user_name}{$user_name}{$user_name}{$user_name}";
while ( (strlen($user_abb) > 4) and ($forever_stop < 200) )
    {$user_abb = preg_replace("/^\./i","",$user_abb);   $forever_stop++;}

$default_settings = array(
    'LCAe' => array(),
    'LCAc' => array(),
    'LCAt' => array(),
    'LMAe' => array(),
    'session_id' => '',
    'session_name' => '',
    'conf_exten' => '',
    'vtiger_callback_id' => 0,
    'qm_extension' => '',
    'nocall_dial_flag' => '',
    'CallCID' => '',
    'uniqueid' => '',
    'xfername' => '',
    'xferchannel' => '',
    'callchannel' => '',
    'callserverip' => '',
    'lastcustchannel' => '',
    'lastcustserverip' => '',
    'lastxferchannel' => '',
    'custchannellive' => 0,
    'xferchannellive' => 0,
    'customer_server_ip' => '',
    'lead_id' => 0,
    'list_id' => 0,
    'live_customer_call' => 0,
    'live_call_seconds' => 0,
    'XD_live_customer_call' => 0,
    'XD_live_call_seconds' => 0,
    'XDchannel' => '',
    'CheckDEADcall' => 0,
    'CheckDEADcallON' => 0,
    'xfer_in_call' => 0,
    'dialingINprogress' => 0,
    'check_r' => 0,
    'check_s' => '',
    'CloserSelecting' => 0,
    'TerritorySelecting' => 0,
    'WaitingForNextStep' => 0,
    'AgentDispoing' => 0,
    'inOUT' => 'OUT',
    'all_record' => 'NO',
    'all_record_count' => 0,
    'recording_filename' => '',
    'recording_id' => '',
    'VDRP_stage' => 'PAUSED',
    'VDCL_group_id' => '',
    'agent_log_id' => 0,
    'active_group_alias' => '',
    'active_ingroup_dial' => '',
    'agent_dialed_type' => '',
    'agent_dialed_number' => '',
    'cid_choice' => '',
    'prefix_choice' => '',
    'reselect_preview_dial' => 0,
    'reselect_alt_dial' => 0,
    'waiting_on_dispo' => 0,
    'AutoDialReady' => 0,
    'AutoDialWaiting' => 0,
    'pause_code_counter' => 0,
    'lead_dial_number' => '',
    'MDchannel' => '',
    'MDuniqueid' => '',
    'XDuniqueid' => '',
    'tmp_vicidial_id' => '',
    'EAphone_code' => '',
    'EAphone_number' => '',
    'EAalt_phone_notes' => '',
    'EAalt_phone_active' => '',
    'EAalt_phone_count' => '',
    'XDnextCID' => '',
    'XDcheck' => '',
    'uniqueid_status_display' => '',
    'uniqueid_status_prefix' => '',
    'custom_call_id' => '',
    'API_selected_xfergroup' => '',
    'API_selected_callmenu' => '',
    'MD_channel_look' => 0,
    'MD_ring_seconds' => 0,
    'MDnextCID' => '',
    'LastCID' => '',
    'LeadDispo' => '',
    'LeadPrevDispo' => '',
    'AgainHangupChannel' => '',
    'AgainHangupServer' => '',
    'AgainCallSeconds' => '',
    'AgainCallCID' => '',
    'cust_phone_code' => '',
    'cust_phone_number' => '',
    'cust_first_name' => '',
    'cust_middle_initial' => '',
    'cust_last_name' => '',
    'cust_email' => '',
    'called_count' => '',
    'previous_called_count' => '',
    'previous_dispo' => '',
    'CBentry_time' => '',
    'CBcallback_time' => '',
    'CBuser' => '',
    'CBcomments' => '',
    'dialed_number' => '',
    'dialed_label' => '',
    'source_id' => '',
    'call_script_ID' => '',
    'vendor_lead_code' => '',
    'script_recording_delay' => 0,
    'Call_XC_a_Number' => '',
    'Call_XC_b_Number' => '',
    'Call_XC_c_Number' => '',
    'Call_XC_d_Number' => '',
    'Call_XC_e_Number' => '',
    'entry_list_id' => '',
    'post_phone_time_diff_alert_message' => '',
    'timer_action' => '',
    'timer_action_message' => '',
    'timer_action_seconds' => '',
    'timer_action_destination' => '',
    'RedirectXFER' => 0,
    'conf_dialed' => 0,
    'open_dispo_screen' => 0,
    'DialALTPhone' => false,
    'leaving_threeway' => 0,
    'epoch_sec' => 0,
    'agentcallsstatus' => 0,
    'callholdstatus' => 1,
    'campagentstatct' => 0,
    'campagentstatctmax' => 3,
    'APIManualDialQueue' => 0,
    'APIManualDialQueue_last' => 0,
    'update_fields' => 0,
    'update_fields_data' => '',
    'conf_channels_xtra_display' => 0,
    'LCAcount' => 0,
    'LMAcount' => 0,
    'flag_channels' => 0,
    'flag_string' => '',
    'HideMonitorSessions' => 1,
    'volumecontrol_active' => 1,
    'customerparked' => 0,
    'customerparkedcounter' => 0,
    'agentchannel' => '',
    'no_blind_monitors' => 0,
    'blind_monitoring_now' => 0,
    'blind_monitoring_now_trigger' => 0,
    'AgentStatusStatus' => '',
    'AgentStatusCalls' => '',
    'AgentStatusDials' => '',
    'shift_logout_flag' => 0,
    'api_logout_flag' => 0,
    'PauseNotifyCounter' => 0,
    'api_timer_action' => '',
    'api_timer_action_message' => '',
    'api_timer_action_seconds' => 0,
    'api_timer_action_destination' => '',
    'api_dtmf' => '',
    'api_transferconf_function' => '',
    'api_transferconf_group' => '',
    'api_transferconf_number' => '',
    'api_transferconf_consultative' => '',
    'api_transferconf_override' => '',
    'api_transferconf_group_alias' => '',
    'api_transferconf_cid_number' => '',
    'api_parkcustomer' => '',
    'nochannelinsession' => 0,
    'conf_dtmf' => '',
    'conf_silent_prefix' => '5',
    'dtmf_silent_prefix' => '7',
    'CallBackRecipient' => '',
    'CallBackLeadStatus' => '',
    'CallBackDateTime' => '',
    'CallBackrecipient' => '',
    'CallBackComments' => '',
    'DispoQMcsCODE' => '',
    'DispoSelection' => '',
    'DispoSelectStop' => true,
    'customer_3way_hangup_counter' => 0,
    'customer_3way_hangup_counter_trigger' => 0,
    'customer_3way_hangup_dispo_message' => '',
    'currently_in_email' => 0,
    'Dispo3wayMessage' => '',
    'Dispo3wayChannel' => '',
    'Dispo3wayXTRAChannel' => '',
    'Dispo3wayCallServerIP' => '',
    'Dispo3wayCallXFERNumber' => '',
    'Dispo3wayCallCampTail' => '',
    'DispoManualQueueMessage' => '',
    'manual_dial_in_progress' => 0,
    'QUEUEpadding' => 0,
    'focus_blur_enabled' => 0,
    'call_notes_dispo' => '',
    'call_notes' => '',
    'PerCallNotesContent' => '',
    'wrapup_waiting' => 0,
    'default_group_alias_cid' => '',
    'LIVE_caller_id_number' => '',
    'default_web_vars' => '',
    'LIVE_web_vars' => '',
    'did_pattern' => '',
    'did_id' => '',
    'did_extension' => '',
    'did_description' => '',
    'closecallid' => '',
    'xfercallid' => '',
    'view_scripts' => '1',
    'Call_Script_ID' => '',
    'Call_Auto_Launch' => '',
    'useIE' => 0,
    'EMAILgroupCOUNT' => 0,
    'prepopulate_transfer_preset_enabled' => 0,
    'custom_field_names' => '',
    'custom_field_values' => '',
    'custom_field_types' => '',
    'web_form_varsX' => '',
    'external_transferconf_count' => 0,
    'alt_dial_active' => 0,
    'alt_dial_status_display' => 0,
    'in_lead_preview_state' => 0,
    'in_group_dial_display' => 0,
    'NActiveExt' => null,
    'auto_dial_alt_dial' => 0,
    'user_abb' => $user_abb,
    'manual_auto_hotkey' => 0,
    'dispnum' => '',
    'loop_ct' => 0,
    'live_Xfer_HTML' => '',
    'Xfer_Select' => '',
    'DefaultALTDial' => 0,
    'agent_choose_ingroups_DV' => '',
    'agent_choose_ingroups_skip_count' => 0,
    'agent_select_territories_skip_count' => 0,
    'list_webform' => '',
    'list_webform_two' => '',
    'enable_sipsak' => 0,
    'SIP_server' => 'asterisk',
    'logout_stop_timeouts' => 0,
    'no_empty_session_warnings' => 0,
    'trigger_ready' => 0,
    'agent_status_view_active' => 0,
    'agent_status_view' => 0,
    'agent_status_view_time' => 0,
    'dispo_check_all_pause' => 0,
    'xfer_select_agents_active' => 0,
    'CB_count_check' => 60,
    'consult_custom_delay' => 2,
    'consult_custom_wait' => 0,
    'consult_custom_go' => 0,
    'consult_custom_sent' => 0,
    'no_delete_sessions' => 1,
    'HKdispo_display' => 0,
    'HKbutton_allowed' => 1,
    'HKfinish' => 0,
    'hot_keys_active' => 0,
    'active_display' => 1,
    'agc_dial_prefix' => '91',
    'call_variables' => '',
    'even' => 0,
    'LogoutKickAll' => 1,
    'delayed_script_load' => '',
    'LastCallbackCount' => 0,
    'LastCallbackViewed' => 0,
    'HK_statuses_camp' => 0,
    'HKuser_level' => $HKuser_level,
    'quick_transfer_button_orig' => '',
    'local_consult_xfers' => 1,
    'web_form_vars' => '',
    'web_form_vars_two' => '',
    'scheduled_callbacks' => 0,
    'LastCallCID' => '',
    'closer_blended' => 0,
    'threeway_end' => 0,
    'threeway_cid' => '',
    'nextdial_seconds' => 3
);

if ($userExist > 0) {
    $rslt = $astDB->getOne('system_settings', 'pass_hash_enabled');
    if ($rslt['pass_hash_enabled'] == '1') {
        $astDB->where('user', $user_name);
        $rslt = $astDB->getOne('vicidial_users', 'pass_hash');
        $userinfo['pass'] = $rslt['pass_hash'];
    }
    $U_scheduled_callbacks = $userinfo['scheduled_callbacks'];
    unset($userinfo['scheduled_callbacks']);
    
    $userinfo['user_closer_campaigns'] = $userinfo['closer_campaigns'];
    unset($userinfo['closer_campaigns']);
    
    $data = array_merge($data, array( 'user_info' => $userinfo ));
    
    $usergroup = get_settings('usergroup', $astDB, $userinfo['user_group']);
    
    if (isset($userinfo['phone_login']) && isset($userinfo['phone_pass'])) {
        $astDB->where('login', $userinfo['phone_login']);
        if (isset($userinfo['phone_pass'])) {
            //$astDB->where('pass', $userinfo['phone_pass']);
        }
        $astDB->where('active', 'Y');
        $phoneinfo = $astDB->getOne('phones', 'extension,dialplan_number,voicemail_id,phone_ip,computer_ip,server_ip,login,pass,status,active,phone_type,fullname,company,picture,messages,old_messages,protocol,local_gmt,ASTmgrUSERNAME,ASTmgrSECRET,login_user,login_pass,login_campaign,park_on_extension,conf_on_extension,VICIDIAL_park_on_extension,VICIDIAL_park_on_filename,monitor_prefix,recording_exten,voicemail_exten,voicemail_dump_exten,ext_context,dtmf_send_extension,call_out_number_group,client_browser,install_directory,local_web_callerID_URL,VICIDIAL_web_URL,AGI_call_logging_enabled,user_switching_enabled,conferencing_enabled,admin_hangup_enabled,admin_hijack_enabled,admin_monitor_enabled,call_parking_enabled,updater_check_enabled,AFLogging_enabled,QUEUE_ACTION_enabled,CallerID_popup_enabled,voicemail_button_enabled,enable_fast_refresh,fast_refresh_rate,enable_persistant_mysql,auto_dial_next_number,VDstop_rec_after_each_call,DBX_server,DBX_database,DBX_user,DBX_pass,DBX_port,DBY_server,DBY_database,DBY_user,DBY_pass,DBY_port,outbound_cid,enable_sipsak_messages,email,template_id,conf_override,phone_context,phone_ring_timeout,conf_secret,is_webphone,use_external_server_ip,codecs_list,webphone_dialpad,phone_ring_timeout,on_hook_agent,webphone_auto_answer');
        
        if (count($phoneinfo) < 1) {
            $phoneinfo = array(
                'extension' => '',
                'dialplan_number' => '',
                'voicemail_id' => '',
                'phone_ip' => '',
                'computer_ip' => '',
                'server_ip' => '',
                'login' => '',
                'pass' => '',
                'status' => '',
                'active' => '',
                'phone_type' => '',
                'fullname' => '',
                'company' => '',
                'picture' => '',
                'messages' => '',
                'old_messages' => '',
                'protocol' => '',
                'local_gmt' => '',
                'ASTmgrUSERNAME' => '',
                'ASTmgrSECRET' => '',
                'login_user' => '',
                'login_pass' => '',
                'login_campaign' => '',
                'park_on_extension' => '',
                'conf_on_extension' => '',
                'VICIDIAL_park_on_extension' => '',
                'VICIDIAL_park_on_filename' => '',
                'monitor_prefix' => '',
                'recording_exten' => '',
                'voicemail_exten' => '',
                'voicemail_dump_exten' => '',
                'ext_context' => '',
                'dtmf_send_extension' => '',
                'call_out_number_group' => '',
                'client_browser' => '',
                'install_directory' => '',
                'local_web_callerID_URL' => '',
                'VICIDIAL_web_URL' => '',
                'AGI_call_logging_enabled' => '',
                'user_switching_enabled' => '',
                'conferencing_enabled' => '',
                'admin_hangup_enabled' => '',
                'admin_hijack_enabled' => '',
                'admin_monitor_enabled' => '',
                'call_parking_enabled' => '',
                'updater_check_enabled' => '',
                'AFLogging_enabled' => '',
                'QUEUE_ACTION_enabled' => '',
                'CallerID_popup_enabled' => '',
                'voicemail_button_enabled' => '',
                'enable_fast_refresh' => '',
                'fast_refresh_rate' => '',
                'enable_persistant_mysql' => '',
                'auto_dial_next_number' => '',
                'VDstop_rec_after_each_call' => '',
                'outbound_cid' => '',
                'enable_sipsak_messages' => '',
                'email' => '',
                'template_id' => '',
                'conf_override' => '',
                'phone_context' => '',
                'phone_ring_timeout' => '',
                'conf_secret' => '',
                'is_webphone' => '',
                'use_external_server_ip' => '',
                'codecs_list' => '',
                'webphone_dialpad' => '',
                'phone_ring_timeout' => '',
                'on_hook_agent' => '',
                'webphone_auto_answer' => ''
            );
        }
        
        $astDB->where('server_ip', $phoneinfo['server_ip']);
        $query = $astDB->getOne('servers', 'asterisk_version');
        $asterisk_version = $query['asterisk_version'];
        
        $extension = $phoneinfo['extension'];
        $protocol = $phoneinfo['protocol'];
        if ($protocol == 'EXTERNAL') {
            $protocol = 'Local';
            $extension = "{$phoneinfo['dialplan_number']}@{$phoneinfo['ext_context']}";
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
        if ( (preg_match('/8300/', $phoneinfo['dialplan_number'])) and (strlen($phoneinfo['dialplan_number'])<5) and ($protocol == 'Local') ) {
            $SIP_user = "{$protocol}/{$extension}{$userinfo['phone_login']}";
            $qm_extension = "{$extension}{$userinfo['phone_login']}";
        }
        
        $data = array_merge($data, array( 'phone_info' => $phoneinfo ));
    }
    
    $systeminfo = $astDB->getOne('system_settings', 'use_non_latin,vdc_header_date_format,vdc_customer_date_format,vdc_header_phone_format,webroot_writable,timeclock_end_of_day,vtiger_url,enable_vtiger_integration,outbound_autodial_active,enable_second_webform,user_territories_active,static_agent_url,custom_fields_enabled,pllb_grouping_limit,qc_features_active,allow_emails,default_language,vicidial_agent_disable,allow_sipsak_messages,default_local_gmt');
    $data = array_merge($data, array( 'system_info' => $systeminfo ));
    
    if (isset($campaign) && strlen($campaign) > 0) {
        $astDB->where('campaign_id', $campaign);
    } else {
        if ($userinfo['user_level'] >= 7) {
            $astDB->where('user_group', array('---ALL---', $userinfo['user_group']), 'in');
            $astDB->where('LENGTH(dial_prefix)', '7', '>=');
            $astDB->where('active', 'Y');
            $astDB->orderBy('campaign_id', 'desc');
        }
    }

    $nextdial_secondsSQL = '';
    $nextResult = $astDB->rawQuery("SHOW COLUMNS FROM `vicidial_campaigns` LIKE 'nextdial_seconds'");
    if ($astDB->getRowCount() > 0) {
        $nextdial_secondsSQL = ",nextdial_seconds";
    }
    
    $campinfo = $astDB->getOne('vicidial_campaigns', "campaign_id,park_ext,park_file_name,web_form_address,allow_closers,auto_dial_level,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,xferconf_a_dtmf AS Call_XC_a_DTMF,xferconf_a_number AS Call_XC_a_Number,xferconf_b_dtmf AS Call_XC_b_DTMF,xferconf_b_number AS Call_XC_b_Number,alt_number_dialing,scheduled_callbacks,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,agent_pause_codes_active,no_hopper_leads_logins,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,xfer_groups,disable_alter_custphone,display_queue_count,manual_dial_filter,agent_clipboard_copy AS Copy_to_Clipboard,use_campaign_dnc,three_way_call_cid,dial_method,three_way_dial_prefix,web_form_target,vtiger_screen_login,agent_allow_group_alias,default_group_alias,quick_transfer_button,prepopulate_transfer_preset,view_calls_in_queue,view_calls_in_queue_launch,call_requeue_button,pause_after_each_call,no_hopper_dialing,agent_dial_owner_only,agent_display_dialable_leads,web_form_address_two,agent_select_territories,crm_popup_login,crm_login_address,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number AS Call_XC_c_Number,xferconf_d_number AS Call_XC_d_Number,xferconf_e_number AS Call_XC_e_Number,use_custom_cid,scheduled_callbacks_alert,scheduled_callbacks_count,manual_dial_override,blind_monitor_warning,blind_monitor_message,blind_monitor_filename,timer_action_destination,enable_xfer_presets,hide_xfer_number_to_dial,manual_dial_prefix,customer_3way_hangup_logging,customer_3way_hangup_seconds,customer_3way_hangup_action,ivr_park_call,manual_preview_dial,api_manual_dial,manual_dial_call_time_check,my_callback_option,per_call_notes,agent_lead_search,agent_lead_search_method,queuemetrics_phone_environment,auto_pause_precall,auto_pause_precall_code,auto_resume_precall,manual_dial_cid,custom_3way_button_transfer,callback_days_limit,disable_dispo_screen,disable_dispo_status,screen_labels,status_display_fields,pllb_grouping,pllb_grouping_limit,in_group_dial,in_group_dial_select,pause_after_next_call,owner_populate{$nextdial_secondsSQL}");
    
    $astDB->where('user', $user_name);
    $astDB->orderBy('agent_log_id', 'desc');
    $rslt = $astDB->getOne('vicidial_agent_log', 'agent_log_id');
    $agent_log_id = $rslt['agent_log_id'];
    
    $astDB->where('user', $user_name);
    $rslt = $astDB->getOne('vicidial_session_data', 'session_name,conf_exten,server_ip');
    $session_name = $rslt['session_name'];
    $session_id = $rslt['conf_exten'];
    $server_ip = $rslt['server_ip'];

    $VARCBstatusesLIST = '';
    $statuses_ct = 0;
    $statuses = array();
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
    
    if (isset($campaign) && strlen($campaign) > 0) {
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
        //ksort($statuses);
        $statuses_ct = ($statuses_ct + $statuses_camp_ct);
        $testVal = $astDB->getLastQuery();
    }
    $VARCBstatusesLIST .= " ";
    
    $astDB->where('campaign_id', $campinfo['campaign_id']);
    $astDB->orderBy('pause_code', 'asc');
    $rslt = $astDB->get('vicidial_pause_codes', null, 'pause_code,pause_code_name,billable');
    $pause_codes_ct = $astDB->getRowCount();
    foreach ($rslt as $row) {
        $pause = $row['pause_code'];
        $pause_name = str_replace("+", " ", $row['pause_code_name']);
        $billable = $row['billable'];
        $pause_codes[$pause] = "{$pause_name}";
        //if ($billable == 'Y')
        //    {$VARCBstatusesLIST .= " {$status}";}
        ksort($pause_codes);
    }
    
    $VARingroups = array();
    $VARingroup_handlers = array();
    $VARphonegroups = array();
    $VARemailgroups = array();
    $INgrpCT = 0;
    $EMAILgrpCT = 0;
    $PHONEgrpCT = 0;
    if ( ($campinfo['campaign_allow_inbound'] == 'Y') && ($campinfo['dial_method'] != 'MANUAL') ) {
        $closer_campaigns = preg_replace("/^ | -$/", "", $campinfo['closer_campaigns']);
        $closer_campaigns = explode(" ", $closer_campaigns);
        
        //$stmt="select group_id,group_handling from vicidial_inbound_groups where active = 'Y' and group_id IN($closer_campaigns) order by group_id limit 800;";
        $astDB->where('active', 'Y');
        $astDB->where('group_id', $closer_campaigns, 'IN');
        $astDB->orderBy('group_id', 'asc');
        $rslt = $astDB->get('vicidial_inbound_groups', 800, 'group_id,group_handling');

        $closer_ct = $astDB->getRowCount();
        while ($INgrpCT < $closer_ct) {
            $row = $rslt[$INgrpCT];
            $VARingroups[$row['group_id']] = $row['group_id'];
            $VARingroup_handlers[$row['group_handling']] = $row['group_id']; // PHONE OR EMAIL - this is important
            if ($row['group_handling']=="EMAIL") { // Make a list of ingroups for email handling groups and one for phones, so there is no overlap
                $VARemailgroups[$row['group_id']] = $VARingroups[$row['group_id']];
                $EMAILgrpCT++;
            } else {
                $VARphonegroups[$row['group_id']] = $VARingroups[$row['group_id']];
                $PHONEgrpCT++;
            }
            ksort($VARingroups);
            asort($VARingroup_handlers);
            ksort($VARemailgroups);
            ksort($VARphonegroups);
            $INgrpCT++;
        }
    }
    
    $xfer_groups = preg_replace("/^ | -$/", "", $campinfo['xfer_groups']);
    $xfer_groups = explode(" ", $xfer_groups);
    ////$xfer_groups = preg_replace("/ /", "','", $xfer_groups);
    ////$xfer_groups = "'$xfer_groups'";
    $XFgrpCT = 0;
    $VARxferGroups = array();
    $VARxferGroupsNames = array();
    $default_xfer_group_name = '';
    if ($campinfo['allow_closers'] == 'Y') {
        $astDB->where('active', 'Y');
        $astDB->where('group_id', $xfer_groups, 'IN');
        $astDB->orderBy('group_id', 'asc');
        $result = $astDB->get('vicidial_inbound_groups', 800, 'group_id,group_name');
        $xfer_ct = $astDB->getRowCount();
        
        $XFgrpCT = 0;
        while ($XFgrpCT < $xfer_ct) {
            $row = $result[$XFgrpCT];
            $VARxferGroups[$row['group_id']] = $row['group_id'];
            $VARxferGroupsNames[$row['group_name']] = $row['group_id'];
            ksort($VARxferGroups);
            asort($VARxferGroupsNames);
            if ($row['group_id'] == "{$campinfo['default_xfer_group']}") {$default_xfer_group_name = $row['group_name'];}
            $XFgrpCT++;
        }
    }
    
    $campaign_hotkeys = get_settings('hotkeys', $astDB, $campaign);
    $hotkeys = '';
    $hotkeysInfo = '';
    $hotkeysCnt = 0;
    //$hotkeysContent = "<dl class='dl-horizontal'>";
    foreach ($campaign_hotkeys as $row) {
        $hotkeys[$row->hotkey] = "{$row->status}";
        $hotkeysInfo[$row->status] = "{$row->status_name}";
        $hotkeysCnt++;
        //$hotkeysContent .= "<dt class='text-primary'>{$row->hotkey}</dt>";
        //$hotkeysContent .= "<dd>{$row->status} - {$row->status_name}</dd>";
    }
    //$hotkeysContent .= "</dl>";
    $campinfo['hotkeys'] = $hotkeys;
    $campinfo['hotkeys_content'] = $hotkeysInfo;
    $default_settings['HK_statuses_camp'] = $hotkeysCnt;
    
    $quick_transfer_button_enabled = 0;
    $quick_transfer_button_locked = 0;
    if (preg_match("/IN_GROUP|PRESET_1|PRESET_2|PRESET_3|PRESET_4|PRESET_5/", $campinfo['quick_transfer_button']))
        {$quick_transfer_button_enabled = 1;}
    if (preg_match("/LOCKED/", $campinfo['quick_transfer_button']))
        {$quick_transfer_button_locked = 1;}

    $custom_3way_button_transfer_enabled = 0;
    $custom_3way_button_transfer_park = 0;
    $custom_3way_button_transfer_view = 0;
    $custom_3way_button_transfer_contacts = 0;
    if (preg_match("/PRESET_|FIELD_/", $campinfo['custom_3way_button_transfer']))
        {$custom_3way_button_transfer_enabled = 1;}
    if (preg_match("/PARK_/",$campinfo['custom_3way_button_transfer'])) {
        $custom_3way_button_transfer_park = 1;
        $custom_3way_button_transfer_enabled = 1;
    }
    if (preg_match("/VIEW_PRESET/",$campinfo['custom_3way_button_transfer'])) {
        $custom_3way_button_transfer_view = 1;
        $custom_3way_button_transfer_enabled = 1;
    }
    if ( (preg_match("/VIEW_CONTACTS/", $campinfo['custom_3way_button_transfer'])) and ($campinfo['enable_xfer_presets'] == 'CONTACTS') and ($userinfo['preset_contact_search'] != 'DISABLED') ) {
        $custom_3way_button_transfer_contacts = 1;
        $custom_3way_button_transfer_enabled = 1;
    }
    
    $status_display_CALLID = 0;
    $status_display_LEADID = 0;
    $status_display_LISTID = 0;
    if (preg_match("/CALLID/", $campinfo['status_display_fields']))
        {$status_display_CALLID = 1;}
    if (preg_match("/LEADID/", $campinfo['status_display_fields']))
        {$status_display_LEADID = 1;}
    if (preg_match("/LISTID/", $campinfo['status_display_fields']))
        {$status_display_LISTID = 1;}
    
    $AllowManualQueueCalls = 1;
    $AllowManualQueueCallsChoice = 0;
    if ($campinfo['api_manual_dial'] == 'QUEUE') {
        $AllowManualQueueCalls = 0;
        $AllowManualQueueCallsChoice = 1;
    }
    if ($campinfo['manual_preview_dial'] == 'DISABLED')
        {$manual_dial_preview = 0;}
    if ($campinfo['manual_dial_override'] == 'ALLOW_ALL')
        {$userinfo['agentcall_manual'] = 1;}
    if ($campinfo['manual_dial_override'] == 'DISABLE_ALL')
        {$userinfo['agentcall_manual'] = 0;}
    if ($systeminfo['user_territories_active'] < 1)
        {$campinfo['agent_select_territories'] = 0;}
    if (preg_match("/Y/", $campinfo['agent_select_territories']))
        {$campinfo['agent_select_territories'] = 1;}
    else
        {$campinfo['agent_select_territories'] = 0;}

    if (preg_match("/Y/", $campinfo['agent_display_dialable_leads']))
        {$campinfo['agent_display_dialable_leads'] = 1;}
    else
        {$campinfo['agent_display_dialable_leads'] = 0;}

    if (preg_match("/Y/", $campinfo['no_hopper_dialing']))
        {$campinfo['no_hopper_dialing'] = 1;}
    else
        {$campinfo['no_hopper_dialing'] = 0;}

    if ( (preg_match("/Y/", $campinfo['call_requeue_button'])) and ($campinfo['auto_dial_level'] > 0) )
        {$campinfo['call_requeue_button'] = 1;}
    else
        {$campinfo['call_requeue_button'] = 0;}

    if ( (preg_match("/AUTO/", $campinfo['view_calls_in_queue_launch'])) and ($campinfo['auto_dial_level'] > 0) )
        {$campinfo['view_calls_in_queue_launch'] = 1;}
    else
        {$campinfo['view_calls_in_queue_launch'] = 0;}

    if ( (!preg_match("/NONE/", $campinfo['view_calls_in_queue'])) and ($campinfo['auto_dial_level'] > 0) )
        {$campinfo['view_calls_in_queue'] = 1;}
    else
        {$campinfo['view_calls_in_queue'] = 0;}

    if (preg_match("/Y/", $campinfo['pause_after_each_call']))
        {$default_settings['dispo_check_all_pause'] = 1;}
    
    $C_scheduled_callbacks = $campinfo['scheduled_callbacks'];
    unset($campinfo['scheduled_callbacks']);
    
    $cbRslt = $goDB->rawQuery("SHOW COLUMNS FROM `go_campaigns` LIKE 'enable_callback_alert'");
    if ($goDB->getRowCount() > 0) {
        $addedCB_Columns = ",enable_callback_alert,cb_noexpire,cb_sendemail";
    }
    
    $goDB->where('campaign_id', $campinfo['campaign_id']);
    $rslt = $goDB->getOne('go_campaigns', 'custom_fields_launch,custom_fields_list_id,url_tab_first_title,url_tab_first_url,url_tab_second_title,url_tab_second_url');
    $campinfo['custom_fields_launch'] = 'ONCALL';
    $campinfo['custom_fields_list_id'] = '';
    $campinfo['url_tab_first_title'] = '';
    $campinfo['url_tab_first_url'] = '';
    $campinfo['url_tab_second_title'] = '';
    $campinfo['url_tab_second_url'] = '';
    $campinfo['enable_callback_alert'] = 0;
    $campinfo['cb_noexpire'] = 0;
    $campinfo['cb_sendemail'] = 0;
    if ($goDB->getRowCount() > 0) {
        $campinfo['custom_fields_launch'] = $rslt['custom_fields_launch'];
        $campinfo['custom_fields_list_id'] = $rslt['custom_fields_list_id'];
        $campinfo['url_tab_first_title'] = $rslt['url_tab_first_title'];
        $campinfo['url_tab_first_url'] = $rslt['url_tab_first_url'];
        $campinfo['url_tab_second_title'] = $rslt['url_tab_second_title'];
        $campinfo['url_tab_second_url'] = $rslt['url_tab_second_url'];
        $campinfo['enable_callback_alert'] = $rslt['enable_callback_alert'];
        $campinfo['cb_noexpire'] = $rslt['cb_noexpire'];
        $campinfo['cb_sendemail'] = $rslt['cb_sendemail'];
    }
    
    $default_group_alias_cid = '';
    $default_group_alias = $campinfo['default_group_alias'];
    if (strlen($default_group_alias) > 1) {
        $astDB->where('group_alias_id', $default_group_alias);
        $rslt = $astDB->get('group_alias', null, 'caller_id_number');
        $VDIG_cidnum_ct = $astDB->getRowCount();
        if ($VDIG_cidnum_ct > 0) {
            $row = $rslt[0];
            $default_group_alias_cid = $row['caller_id_number'];
        }
    }
    $default_settings['default_group_alias_cid'] = $default_group_alias_cid;
    $default_settings['LIVE_caller_id_number'] = $default_group_alias_cid;

    $default_web_vars = '';
    $astDB->where('campaign_id', $campinfo['campaign_id']);
    $astDB->where('user', $user_name);
    $rslt = $astDB->get('vicidial_campaign_agents', null, 'group_web_vars');
    $VDIG_cidogwv = $astDB->getRowCount();
    if ($VDIG_cidogwv > 0) {
        $row = $rslt[0];
        $default_web_vars =	$row['group_web_vars'];
    }
    $default_settings['default_web_vars'] = $default_web_vars;
    $default_settings['LIVE_web_vars'] = $default_web_vars;
    
    $data = array_merge($data, array( 'camp_info' => $campinfo ));
    
    $default_settings['quick_transfer_button_enabled'] = $quick_transfer_button_enabled;
    $default_settings['quick_transfer_button_locked'] = $quick_transfer_button_locked;
    $default_settings['custom_3way_button_transfer_enabled'] = $custom_3way_button_transfer_enabled;
    $default_settings['custom_3way_button_transfer_park'] = $custom_3way_button_transfer_park;
    $default_settings['custom_3way_button_transfer_view'] = $custom_3way_button_transfer_view;
    $default_settings['custom_3way_button_transfer_contacts'] = $custom_3way_button_transfer_contacts;
    $default_settings['asterisk_version'] = $asterisk_version;
    $default_settings['agent_log_id'] = $agent_log_id;
    //$default_settings['protocol'] = $protocol;
    //$default_settings['extension'] = $extension;
    $default_settings['conf_exten'] = $session_id;
    $default_settings['session_id'] = $session_id;
    $default_settings['session_name'] = $session_name;
    //$default_settings['server_ip'] = $server_ip;
    $default_settings['SIP_user'] = $SIP_user;
    $default_settings['SIP_user_Dial'] = $SIP_user_DiaL;
    $default_settings['SIP_server'] = $SIP_server;
    $default_settings['qm_extension'] = $qm_extension;
    $default_settings['statuses_count'] = $statuses_ct;
    $default_settings['statuses'] = $statuses;
    $default_settings['callback_statuses_list'] = $VARCBstatusesLIST;
    $default_settings['pause_codes_count'] = $pause_codes_ct;
    $default_settings['pause_codes'] = $pause_codes;
    $default_settings['xfer_group_count'] = $XFgrpCT;
    $default_settings['xfer_groups'] = $VARxferGroups;
    $default_settings['xfer_group_names'] = $VARxferGroupsNames;
    $default_settings['inbound_group_count'] = $INgrpCT;
    $default_settings['inbound_groups'] = $VARingroups;
    $default_settings['inbound_group_handlers'] = $VARingroup_handlers;
    //$default_settings['email_group_count'] = $EMAILgrpCT;
    //$default_settings['email_groups'] = $VARemailgroups;
    //$default_settings['phone_group_count'] = $PHONEgrpCT;
    //$default_settings['phone_groups'] = $VARphonegroups;
    $default_settings['webform_session'] = "&session_name={$session_name}";
    $default_settings['status_display_CALLID'] = $status_display_CALLID;
    $default_settings['status_display_LEADID'] = $status_display_LEADID;
    $default_settings['status_display_LISTID'] = $status_display_LISTID;
    $default_settings['AllowManualQueueCalls'] = $AllowManualQueueCalls;
    $default_settings['AllowManualQueueCallsChoice'] = $AllowManualQueueCallsChoice;
    
    if ($campinfo['display_queue_count'] == 'N') {
        $default_settings['callholdstatus'] = 0;
    }
    
    if ($campinfo['alt_number_dialing'] == 'Y') {
        $default_settings['alt_phone_dialing'] = 1;
    } else {
        $default_settings['alt_phone_dialing'] = 0;
        $default_settings['DefaultALTDial'] = 0;
    }
    
    if ($userinfo['phone_login'] == 'nophone' || $phoneinfo['on_hook_agent'] == 'Y') {
        $default_settings['no_empty_session_warnings'] = 1;
    }
    
    if ( ($phoneinfo['enable_sipsak_messages'] > 0) and ($systeminfo['allow_sipsak_messages'] > 0) and (preg_match("/SIP/i", $protocol)) ) {
        $default_settings['enable_sipsak'] = 1;
    }
    
    if (strlen($usergroup->agent_status_viewable_groups) > 2)
        {$default_settings['agent_status_view'] = 1;}
    
    if ($usergroup->agent_status_view_time == 'Y')
        {$default_settings['agent_status_view_time'] = 1;}
    
    if ($C_scheduled_callbacks == 'Y' && $U_scheduled_callbacks == '1') {
        $default_settings['scheduled_callbacks'] = 1;
    }
    
    $data = array_merge($data, array( 'default_settings' => $default_settings ));

    $astDB->where('tld', '', '<>');
    $astDB->join('vicidial_country_iso_tld', 'country=iso3', 'left');
    $astDB->groupBy('country_code,country');
    $rslt = $astDB->get('vicidial_phone_codes', null, 'country_code,country,tld,country_name');
    
    $country_code = [];
    foreach ($rslt as $country) {
        $country_id = "{$country['country']}_{$country['country_code']}";
        $country_code[$country_id]['code'] = htmlentities(addslashes($country['country_code']));
        $country_code[$country_id]['tld'] = htmlentities(addslashes($country['tld']));
        $country_code[$country_id]['name'] = htmlentities(addslashes($country['country_name']));
    }
    $data = array_merge($data, array( 'country_codes' => $country_code ));
    
    $APIResult = array( "result" => "success", "data" => $data, "test" => $testVal );
} else {
    $APIResult = array( "result" => "error", "message" => "User ID '{$user_id}' does NOT exist." );
}
?>