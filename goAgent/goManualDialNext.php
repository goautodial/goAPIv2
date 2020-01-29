<?php
 /**
 * @file 		goManualDialNext.php
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

$agent = get_settings('user', $astDB, $goUser);

$user = $agent->user;
$user_group = $agent->user_group;
$phone_login = (isset($phone_login)) ? $phone_login : $agent->phone_login;
$phone_pass = (isset($phone_pass)) ? $phone_pass : $agent->phone_pass;

### Check if the agent's phone_login is currently connected
$sipIsLoggedIn = check_sip_login($kamDB, $phone_login, $SIPserver);

if ($sipIsLoggedIn) {
    if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
        else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
    if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
        else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
    if (isset($_GET['goPreview'])) { $preview = $astDB->escape($_GET['goPreview']); }
        else if (isset($_POST['goPreview'])) { $preview = $astDB->escape($_POST['goPreview']); }
    if (isset($_GET['goCallbackID'])) { $callback_id = $astDB->escape($_GET['goCallbackID']); }
        else if (isset($_POST['goCallbackID'])) { $callback_id = $astDB->escape($_POST['goCallbackID']); }
    if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
        else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }
    if (isset($_GET['goPhoneCode'])) { $phone_code = $astDB->escape($_GET['goPhoneCode']); }
        else if (isset($_POST['goPhoneCode'])) { $phone_code = $astDB->escape($_POST['goPhoneCode']); }
    if (isset($_GET['goPhoneNumber'])) { $phone_number = $astDB->escape($_GET['goPhoneNumber']); }
        else if (isset($_POST['goPhoneNumber'])) { $phone_number = $astDB->escape($_POST['goPhoneNumber']); }
    if (isset($_GET['goListID'])) { $list_id = $astDB->escape($_GET['goListID']); }
        else if (isset($_POST['goListID'])) { $list_id = $astDB->escape($_POST['goListID']); }
    if (isset($_GET['goUseGroupAlias'])) { $usegroupalias = $astDB->escape($_GET['goUseGroupAlias']); }
        else if (isset($_POST['goUseGroupAlias'])) { $usegroupalias = $astDB->escape($_POST['goUseGroupAlias']); }
    if (isset($_GET['goAgentDialedType'])) { $agent_dialed_type = $astDB->escape($_GET['goAgentDialedType']); }
        else if (isset($_POST['goAgentDialedType'])) { $agent_dialed_type = $astDB->escape($_POST['goAgentDialedType']); }
    if (isset($_GET['goVendorLeadCode'])) { $vendor_lead_code = $astDB->escape($_GET['goVendorLeadCode']); }
        else if (isset($_POST['goVendorLeadCode'])) { $vendor_lead_code = $astDB->escape($_POST['goVendorLeadCode']); }
    if (isset($_GET['goAgentDialedNumber'])) { $agent_dialed_number = $astDB->escape($_GET['goAgentDialedNumber']); }
        else if (isset($_POST['goAgentDialedNumber'])) { $agent_dialed_number = $astDB->escape($_POST['goAgentDialedNumber']); }
    if (isset($_GET['goDialIngroup'])) { $dial_ingroup = $astDB->escape($_GET['goDialIngroup']); }
        else if (isset($_POST['goDialIngroup'])) { $dial_ingroup = $astDB->escape($_POST['goDialIngroup']); }
    if (isset($_GET['goNoCallDialFlag'])) { $nocall_dial_flag = $astDB->escape($_GET['goNoCallDialFlag']); }
        else if (isset($_POST['goNoCallDialFlag'])) { $nocall_dial_flag = $astDB->escape($_POST['goNoCallDialFlag']); }
    if (isset($_GET['goVTCallbackID'])) { $vtiger_callback_id = $astDB->escape($_GET['goVTCallbackID']); }
        else if (isset($_POST['goVTCallbackID'])) { $vtiger_callback_id = $astDB->escape($_POST['goVTCallbackID']); }
    if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
        else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }
    if (isset($_GET['goAccount'])) { $account = $astDB->escape($_GET['goAccount']); }
        else if (isset($_POST['goAccount'])) { $account = $astDB->escape($_POST['goAccount']); }
    if (isset($_GET['goQMExtension'])) { $qm_extension = $astDB->escape($_GET['goQMExtension']); }
        else if (isset($_POST['goQMExtension'])) { $qm_extension = $astDB->escape($_POST['goQMExtension']); }
    if (isset($_GET['goSIPserver'])) { $qm_extension = $astDB->escape($_GET['goSIPserver']); }
        else if (isset($_POST['goSIPserver'])) { $qm_extension = $astDB->escape($_POST['goSIPserver']); }
    if (isset($_GET['goConfExten'])) { $conf_exten = $astDB->escape($_GET['goConfExten']); }
        else if (isset($_POST['goConfExten'])) { $conf_exten = $astDB->escape($_POST['goConfExten']); }
    if (isset($_GET['goExtContext'])) { $ext_context = $astDB->escape($_GET['goExtContext']); }
        else if (isset($_POST['goExtContext'])) { $ext_context = $astDB->escape($_POST['goExtContext']); }
    if (isset($_GET['goDialTimeout'])) { $dial_timeout = $astDB->escape($_GET['goDialTimeout']); }
        else if (isset($_POST['goDialTimeout'])) { $dial_timeout = $astDB->escape($_POST['goDialTimeout']); }
    if (isset($_GET['goDialPrefix'])) { $dial_prefix = $astDB->escape($_GET['goDialPrefix']); }
        else if (isset($_POST['goDialPrefix'])) { $dial_prefix = $astDB->escape($_POST['goDialPrefix']); }
    if (isset($_GET['goCampaignCID'])) { $campaign_cid = $astDB->escape($_GET['goCampaignCID']); }
        else if (isset($_POST['goCampaignCID'])) { $campaign_cid = $astDB->escape($_POST['goCampaignCID']); }
    if (isset($_GET['goUseInternalDNC'])) { $use_internal_dnc = $astDB->escape($_GET['goUseInternalDNC']); }
        else if (isset($_POST['goUseInternalDNC'])) { $use_internal_dnc = $astDB->escape($_POST['goUseInternalDNC']); }
    if (isset($_GET['goUseCampaignDNC'])) { $use_campaign_dnc = $astDB->escape($_GET['goUseCampaignDNC']); }
        else if (isset($_POST['goUseCampaignDNC'])) { $use_campaign_dnc = $astDB->escape($_POST['goUseCampaignDNC']); }
    if (isset($_GET['goOmitPhoneCode'])) { $omit_phone_code = $astDB->escape($_GET['goOmitPhoneCode']); }
        else if (isset($_POST['goOmitPhoneCode'])) { $omit_phone_code = $astDB->escape($_POST['goOmitPhoneCode']); }
    if (isset($_GET['goManualDialFilter'])) { $manual_dial_filter = $astDB->escape($_GET['goManualDialFilter']); }
        else if (isset($_POST['goManualDialFilter'])) { $manual_dial_filter = $astDB->escape($_POST['goManualDialFilter']); }
    if (isset($_GET['goDialMethod'])) { $dial_method = $astDB->escape($_GET['goDialMethod']); }
        else if (isset($_POST['goDialMethod'])) { $dial_method = $astDB->escape($_POST['goDialMethod']); }
    if (isset($_GET['goManualDialCallTimeCheck'])) { $manual_dial_call_time_check = $astDB->escape($_GET['goManualDialCallTimeCheck']); }
        else if (isset($_POST['goManualDialCallTimeCheck'])) { $manual_dial_call_time_check = $astDB->escape($_POST['goManualDialCallTimeCheck']); }
    if (isset($_GET['goAgentLogID'])) { $agent_log_id = $astDB->escape($_GET['goAgentLogID']); }
        else if (isset($_POST['goAgentLogID'])) { $agent_log_id = $astDB->escape($_POST['goAgentLogID']); }
    
    if (!isset($agent_dialed_number{3})) {
        $agent_dialed_number = $phone_number;
    }

    $system_settings = get_settings('system', $astDB);
    $phone_settings = get_settings('phone', $astDB, $phone_login, $phone_pass);
    //$server_ip = $phone_settings->server_ip;
    //$ext_context = $phone_settings->ext_context;
    
    $campaign_settings = get_settings('campaign', $astDB, $campaign);
    //$dial_timeout = $campaign_settings->dial_timeout;
    //$dial_prefix = $campaign_settings->dial_prefix;
    //$campaign_cid = $campaign_settings->campaign_cid;
    //$use_internal_dnc = $campaign_settings->use_internal_dnc;
    //$use_campaign_dnc = $campaign_settings->use_campaign_dnc;
    //$omit_phone_code = $campaign_settings->omit_phone_code;
    //$manual_dial_filter = $campaign_settings->manual_dial_filter;
    $manual_dial_list_id = $campaign_settings->manual_dial_list_id;
    //$dial_method = $campaign_settings->dial_method;
    //$manual_dial_call_time_check = $campaign_settings->manual_dial_call_time_check;
    
    $errmsg = 0;
    $override_phone = 0;
    
    if (strlen($manual_dial_list_id) > 0) {
        $list_id = $manual_dial_list_id;
    }
    
    //$nocall_dial_flag = 'DISABLED';
    
    $astDB->where('server_ip', $server_ip);
    $query = $astDB->getOne('servers', 'asterisk_version,local_gmt');
    $asterisk_version = $query['asterisk_version'];
    $gmt_recs = count($query['local_gmt']);
    if ($gmt_recs > 0) {
        $DBSERVER_GMT = $query['local_gmt'];
        if (strlen($DBSERVER_GMT)>0)
            {$SERVER_GMT = $DBSERVER_GMT;}
        if ($isdst)
            {$SERVER_GMT++;} 
    } else {
        $SERVER_GMT = date("O");
        $SERVER_GMT = preg_replace("/\+/i", "", $SERVER_GMT);
        $SERVER_GMT = ($SERVER_GMT + 0);
        $SERVER_GMT = ($SERVER_GMT / 100);
    }
    $LOCAL_GMT_OFF = $SERVER_GMT;
    $LOCAL_GMT_OFF_STD = $SERVER_GMT;
    
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
    $qm_extension = "$extension";
    if ( (preg_match('/8300/', $phone_settings->dialplan_number)) and (strlen($phone_settings->dialplan_number)<5) and ($protocol == 'Local') ) {
        $SIP_user = "{$protocol}/{$extension}{$phone_login}";
        $qm_extension = "{$extension}{$agent->phone_login}";
    }
    
    $astDB->where('extension', $SIP_user);
    $astDB->where('server_ip', $server_ip);
    $query = $astDB->getOne('vicidial_conferences', 'conf_exten');
    $conf_exten = $query['conf_exten'];
    
    $astDB->where('extension', $extension);
    $astDB->where('server_ip', $phone_settings->server_ip);
    $astDB->where('program', 'vicidial');
    $query = $astDB->getOne('web_client_sessions', 'session_name');
    $session_name = $query['session_name'];
    
    $astDB->where('user', $user);
    $query = $astDB->getOne('vicidial_live_agents', 'agent_log_id');
    $agent_log_id = $query['agent_log_id'];
    
    $astDB->where('phone_number', $phone_number);
    $astDB->where('lead_id', $lead_id);
    $query = $astDB->getOne('vicidial_list');
    if (count($query)) {
        $list_id = $query['list_id'];
        $phone_code = $query['phone_code'];
        $vendor_lead_code = $query['vendor_lead_code'];
    }

    $MT[0] = '';
    $msg = '';
    $row = '';
    $rowx = '';
    $override_dial_number = '';
    $channel_live = 1;
    $lead_id = preg_replace("/[^0-9]/","",$lead_id);
    $ACcount = '';
    $ACcomments = '';
    $LISTweb_form_address = '';
    $LISTweb_form_address_two = '';
    if ( (strlen($conf_exten)<1) || (strlen($campaign)<1)  || (strlen($ext_context)<1) ) {
        $channel_live = 0;
        $msg .= "HOPPER EMPTY\n";
        $msg .= "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
        $APIResult = array( "result" => "error", "message" => $msg );
    } else {
        ##### grab number of calls today in this campaign and increment
        $eac_phone = '';
        $astDB->where('user', $user);
        $astDB->where('campaign_id', $campaign);
        $query = $astDB->getOne('vicidial_live_agents', 'calls_today,extension');
        $vla_cc_ct = $astDB->getRowCount();
        if ($vla_cc_ct > 0) {
            $calls_today =	$query['calls_today'];
            $eac_phone   =  $query['extension'];
        } else {
            $calls_today = '0';
        }
        $calls_today++;
    
        $script_recording_delay = 0;
        ##### find if script contains recording fields
        $query = $astDB->rawQuery("SELECT * FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$campaign' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";");
        $vs_vc_ct = $astDB->getRowCount();
        if ($vs_vc_ct > 0) {
            $script_recording_delay = $vs_vc_ct;
        }
        
        $mdmdRslt = $goDB->rawQuery("SHOW COLUMNS FROM `go_campaigns` LIKE 'manual_dial_min_digits'");
        if ($goDB->getRowCount() > 0) {
            $goDB->where('campaign_id', $campaign);
            $rslt = $goDB->getOne('go_campaigns', 'manual_dial_min_digits');
            $manual_dial_min_digits = $rslt['manual_dial_min_digits'];
        }
    
        ### check if this is a callback, if it is, skip the grabbing of a new lead and mark the callback as INACTIVE
        if ( (strlen($callback_id)>0) and (strlen($lead_id)>0) ) {
            $affected_rows = 1;
            $CBleadIDset = 1;
            
            $astDB->where('callback_id', $callback_id);
            $query = $astDB->update('vicidial_callbacks', array('status'=>'INACTIVE'));
        }
        ### check if this is a specific lead call, if it is, skip the grabbing of a new lead
        else if (strlen($lead_id)>0) {
            $affected_rows = 1;
            $CBleadIDset = 1;
        
            if (strlen($phone_number) >= $manual_dial_min_digits)
                {$override_dial_number = $phone_number;}
        } else {
            if (strlen($phone_number)>=3) {
                if (preg_match("/ENABLED/", $manual_dial_call_time_check)) {
                    $secX = date("U");
                    $hour = date("H");
                    $min = date("i");
                    $sec = date("s");
                    $mon = date("m");
                    $mday = date("d");
                    $year = date("Y");
                    $isdst = date("I");
                    $Shour = date("H");
                    $Smin = date("i");
                    $Ssec = date("s");
                    $Smon = date("m");
                    $Smday = date("d");
                    $Syear = date("Y");
                    $pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
                    $inSD = $pulldate0;
                    $dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );
        
                    $postalgmt = '';
                    $postal_code = '';
                    $state = '';
                    if (strlen($phone_code)<1)
                        {$phone_code = '1';}
        
                    $local_call_time = '24hours';
                    ##### gather local call time setting from campaign
                    $astDB->where('campaign_id', $campaign);
                    $rslt = $astDB->get('vicidial_campaigns', null, 'local_call_time');
                    $camp_lct_ct = $astDB->getRowCount();
                    if ($camp_lct_ct > 0) {
                        $row = $rslt[0];
                        $local_call_time = $row['local_call_time'];
                    }
        
                    ### get current gmt_offset of the phone_number
                    $USarea = substr($phone_number, 0, 3);
                    $gmt_offset = lookup_gmt($astDB, $phone_code, $USarea, $state, $LOCAL_GMT_OFF_STD, $Shour, $Smin, $Ssec, $Smon, $Smday, $Syear, $postalgmt, $postal_code);
        
                    $dialable = dialable_gmt($astDB, $local_call_time, $gmt_offset, $state);
                    
                    if ($dialable < 1) {
                        ### purge from the dial queue and api
                        $astDB->where('phone_number', $phone_number);
                        $astDB->where('user', $user);
                        $rslt = $astDB->delete('vicidial_manual_dial_queue');
                        $VMDQaffected_rows = $astDB->getRowCount();
        
                        //$stmt = "UPDATE vicidial_live_agents set external_dial='' where user='$user';";
                        $astDB->where('user', $user);
                        $rslt = $astDB->update('vicidial_live_agents', array('external_dial'=>''));
                        $VLAEDaffected_rows = $astDB->getRowCount();
        
                        $message = "OUTSIDE OF LOCAL CALL TIME   $VMDQaffected_rows|$VLAEDaffected_rows";
                        $errmsg++;
                        //$APIResult = array( "result" => "error", "message" => $message );
                        //exit;
                    }
                }
        
                if (preg_match("/DNC/", $manual_dial_filter)) {
                    if (preg_match("/AREACODE/",$use_internal_dnc)) {
                        $phone_number_areacode = substr($phone_number, 0, 3);
                        $phone_number_areacode .= "XXXXXXX";
                        //$stmt="SELECT count(*) from vicidial_dnc where phone_number IN('$phone_number','$phone_number_areacode');";
                        $astDB->where('phone_number', array($phone_number, $phone_number_areacode), 'IN');
                    } else {
                        //$stmt="SELECT count(*) FROM vicidial_dnc where phone_number='$phone_number';";
                        $astDB->where('phone_number', $phone_number);
                    }
                    $rslt = $astDB->get('vicidial_dnc');
                    $dnc_ct = $astDB->getRowCount();
                    
                    if ($dnc_ct > 0) {
                        ### purge from the dial queue and api
                        //$stmt = "DELETE from vicidial_manual_dial_queue where phone_number='$phone_number' and user='$user';";
                        $astDB->where('phone_number', $phone_number);
                        $astDB->where('user', $user);
                        $rslt = $astDB->delete('vicidial_manual_dial_queue');
                        $VMDQaffected_rows = $astDB->getRowCount();
        
                        //$stmt = "UPDATE vicidial_live_agents set external_dial='' where user='$user';";
                        $astDB->where('user', $user);
                        $rslt = $astDB->update('vicidial_live_agents', array('external_dial'=>''));
                        $VLAEDaffected_rows = $astDB->getRowCount();
        
                        $message = "DNC NUMBER";
                        $errmsg++;
                        //$APIResult = array( "result" => "error", "message" => $message );
                        //exit;
                    }
                    if ( (preg_match("/Y/",$use_campaign_dnc)) or (preg_match("/AREACODE/",$use_campaign_dnc)) ) {
                        //$stmt="SELECT use_other_campaign_dnc from vicidial_campaigns where campaign_id='$campaign';";
                        $astDB->where('campaign_id', $campaign);
                        $rslt = $astDB->getOne('vicidial_campaigns', 'use_other_campaign_dnc');
                        $row = $rslt;
                        $use_other_campaign_dnc = $row['use_other_campaign_dnc'];
                        $temp_campaign_id = $campaign;
                        if (strlen($use_other_campaign_dnc) > 0) {$temp_campaign_id = $use_other_campaign_dnc;}
        
                        if (preg_match("/AREACODE/",$use_campaign_dnc)) {
                            $phone_number_areacode = substr($phone_number, 0, 3);
                            $phone_number_areacode .= "XXXXXXX";
                            //$stmt="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$phone_number','$phone_number_areacode') and campaign_id='$temp_campaign_id';";
                            $astDB->where('phone_number', array($phone_nubmer, $phone_number_areacode), 'IN');
                        } else {
                            //$stmt="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$phone_number' and campaign_id='$temp_campaign_id';";
                            $astDB->where('phone_number', $phone_number);
                        }
                        $astDB->where('campaign_id', $temp_campaign_id);
                        $rslt = $astDB->get('vicidial_campaign_dnc');
                        $camp_dnc_ct = $astDB->getRowCount();
                        if ($camp_dnc_ct > 0) {
                            ### purge from the dial queue and api
                            //$stmt = "DELETE from vicidial_manual_dial_queue where phone_number='$phone_number' and user='$user';";
                            $astDB->where('phone_number', $phone_number);
                            $astDB->where('user', $user);
                            $rslt = $astDB->delete('vicidial_manual_dial_queue');
                            $VMDQaffected_rows = $astDB->getRowCount();
        
                            //$stmt = "UPDATE vicidial_live_agents set external_dial='' where user='$user';";
                            $astDB->where('user', $user);
                            $rslt = $astDB->update('vicidial_live_agents', array('external_dial'=>''));
                            $VLAEDaffected_rows = $astDB->getRowCount();
        
                            $message = "DNC NUMBER";
                            $errmsg++;
                            //$APIResult = array( "result" => "error", "message" => $message );
                            //exit;
                        }
                    }
                }
                if (preg_match("/CAMPLISTS/",$manual_dial_filter)) {
                    //$stmt="SELECT list_id,active from vicidial_lists where campaign_id='$campaign'";
                    $astDB->where('campaign_id', $campaign);
                    $rslt = $astDB->get('vicidial_lists', null, 'list_id,active');
                    $lists_to_parse = $astDB->getRowCount();
                    $camp_lists = '';
                    if ($lists_to_parse > 0) {
                        foreach ($rslt as $rowx) {
                            if (preg_match("/Y/", $rowx['active'])) {
                                $active_lists++;
                                $camp_lists .= $rowx['list_id'].",";
                            }
                            if (preg_match("/ALL/",$manual_dial_filter)) {
                                if (preg_match("/N/", $rowx['active'])) {
                                    $inactive_lists++;
                                    $camp_lists .= $rowx['list_id'].",";
                                }
                            } else {
                                if (preg_match("/N/", $rowx['active'])) 
                                    {$inactive_lists++;}
                            }
                        }
                    }
                    $camp_lists = preg_replace("/.$/i","",$camp_lists);
                    $camp_lists = explode(",", $camp_lists);
        
                    //$stmt="SELECT count(*) FROM vicidial_list where phone_number='$phone_number' and list_id IN($camp_lists);";
                    $astDB->where('phone_number', $phone_number);
                    $astDB->where('list_id', $camp_lists, 'IN');
                    $rslt = $astDB->get('vicidial_list');
                    $listCnt = $astDB->getRowCount();
                    
                    if ($listCnt < 1) {
                        ### purge from the dial queue and api
                        //$stmt = "DELETE from vicidial_manual_dial_queue where phone_number='$phone_number' and user='$user';";
                        $astDB->where('phone_number', $phone_number);
                        $astDB->where('user', $user);
                        $VMDQaffected_rows = $astDB->delete('vicidial_manual_dial_queue');
        
                        //$stmt = "UPDATE vicidial_live_agents set external_dial='' where user='$user';";
                        $astDB->where('user', $user);
                        $rslt = $astDB->update('vicidial_live_agents', array('external_dial'=>''));
                        $VLAEDaffected_rows = $astDB->getRowCount();
        
                        $message = "NUMBER NOT IN CAMPLISTS";
                        $errmsg++;
                        //$APIResult = array( "result" => "error", "message" => $message );
                        //exit;
                    }
                }
                if ($stage == 'lookup') {
                    if (strlen($vendor_lead_code) > 0) {
                        //$stmt="SELECT lead_id FROM vicidial_list where vendor_lead_code='$vendor_lead_code' order by modify_date desc LIMIT 1;";
                        $astDB->where('vendor_lead_code', $vendor_lead_code);
                        $astDB->orderBy('modify_date', 'desc');
                        $rslt = $astDB->getOne('vicidial_list', 'lead_id');
                        $man_leadID_ct = $astDB->getRowCount();
                        if ( (count($man_leadID_ct) > 0) and (strlen($phone_number) >= $manual_dial_min_digits) )
                            {$override_phone++;}
                    } else {
                        // Added a script to fetch the tenant id and it's allowed campaigns -- Chris Lomuntad <chris@goautodial.com>
                        $stmt="SELECT TRIM(TRIM(TRAILING '-' FROM allowed_campaigns)) AS allowed_camps FROM vicidial_user_groups AS vug, vicidial_users AS vu WHERE vu.user='$user' AND vug.user_group=vu.user_group;";
                        $rslt = $astDB->rawQuery($stmt);
                        $row = $rslt[0];
                        $allowed_campaigns = str_replace(" ", "','", $row['allowed_camps']);
                        
                        // Get allowed campaigns and list ids for the tenant
                        $campaign_SQL = "";
                        if (!preg_match("/ALL-CAMPAIGNS/", $allowed_campaigns)) {
                            $campaign_SQL = "vc.campaign_id IN ('$allowed_campaigns') AND";
                        }
                        $stmt="SELECT list_id,manual_dial_list_id FROM vicidial_lists AS vl, vicidial_campaigns AS vc WHERE $campaign_SQL vl.campaign_id=vc.campaign_id;";
                        $rslt = $astDB->rawQuery($stmt);
                        $Xct = $astDB->getRowCount();
                        
                        if ($Xct > 0) {
                            //for ($i=0;$i<$Xct;$i++) {
                            //    $Xrow = mysql_fetch_row($rslt);
                            //    $list_ids[$i] = $Xrow[0];
                            //}
                            $i = 0;
                            foreach ($rslt as $Xrow) {
                                $list_ids[$i] = $Xrow['list_id'];
                                if (!in_array($Xrow['manual_dial_list_id'], $list_ids)) {
                                    $i++;
                                    $list_ids[$i] = $Xrow['manual_dial_list_id'];
                                }
                                $i++;
                            }
                            //$list_ids = implode("','",$list_ids);
                            //$list_idSQL = "AND list_id IN ('$list_ids')";
                            $astDB->where('list_id', $list_ids, 'IN');
                        }
                        
                        //$stmt="SELECT lead_id FROM vicidial_list where phone_number='$phone_number' $list_idSQL order by modify_date desc LIMIT 1;";
                        $astDB->where('phone_number', $phone_number);
                        $astDB->orderBy('modify_date', 'desc');
                        $rslt = $astDB->getOne('vicidial_list', 'lead_id');
                        $man_leadID_ct = $astDB->getRowCount();
                    }
                    if ($man_leadID_ct > 0) {
                        $affected_rows = 1;
                        $row = $rslt;
                        $lead_id = $row['lead_id'];
                        $CBleadIDset = 1;
                    } else {
                        ### insert a new lead in the system with this phone number
                        //$stmt = "INSERT INTO vicidial_list SET phone_code='$phone_code',phone_number='$phone_number',list_id='$list_id',status='QUEUE',user='$user',called_since_last_reset='Y',entry_date='$ENTRYdate',last_local_call_time='$NOW_TIME',vendor_lead_code='$vendor_lead_code';";
                        $insertData = array(
                            'phone_code' => $phone_code,
                            'phone_number' => $phone_number,
                            'list_id' => $list_id,
                            'status' => 'QUEUE',
                            'user' => $user,
                            'called_since_last_reset' => 'Y',
                            'entry_date' => $ENTRYdate,
                            'last_local_call_time' => $NOW_TIME,
                            'vendor_lead_code' => $vendor_lead_code
                        );
                        $rslt = $astDB->insert('vicidial_list', $insertData);
                        $affected_rows = $astDB->getRowCount();
                        $lead_id = $astDB->getInsertId();
                        $CBleadIDset = 1;
                    }
                } else {
                    ### insert a new lead in the system with this phone number
                    //$stmt = "INSERT INTO vicidial_list SET phone_code='$phone_code',phone_number='$phone_number',list_id='$list_id',status='QUEUE',user='$user',called_since_last_reset='Y',entry_date='$ENTRYdate',last_local_call_time='$NOW_TIME',vendor_lead_code='$vendor_lead_code';";
                    $insertData = array(
                        'phone_code' => $phone_code,
                        'phone_number' => $phone_number,
                        'list_id' => $list_id,
                        'status' => 'QUEUE',
                        'user' => $user,
                        'called_since_last_reset' => 'Y',
                        'entry_date' => $ENTRYdate,
                        'last_local_call_time' => $NOW_TIME,
                        'vendor_lead_code' => $vendor_lead_code
                    );
                    $rslt = $astDB->insert('vicidial_list', $insertData);
                    $affected_rows = $astDB->getRowCount();
                    $lead_id = $astDB->getInsertId();
                    $CBleadIDset = 1;
                }
            } else {
                ##### gather no hopper dialing settings from campaign
                //$stmt="SELECT no_hopper_dialing,agent_dial_owner_only,local_call_time,dial_statuses,drop_lockout_time,lead_filter_id,lead_order,lead_order_randomize,lead_order_secondary,call_count_limit FROM vicidial_campaigns where campaign_id='$campaign';";
                $astDB->where('campaign_id', $campaign);
                $rslt = $astDB->getOne('vicidial_campaigns', 'no_hopper_dialing,agent_dial_owner_only,local_call_time,dial_statuses,drop_lockout_time,lead_filter_id,lead_order,lead_order_randomize,lead_order_secondary,call_count_limit');
                $camp_nohopper_ct = $astDB->getRowCount();
                if ($camp_nohopper_ct > 0) {
                    $row = $rslt;
                    $no_hopper_dialing =		$row['no_hopper_dialing'];
                    $agent_dial_owner_only =	$row['agent_dial_owner_only'];
                    $local_call_time =			$row['local_call_time'];
                    $dial_statuses =			$row['dial_statuses'];
                    $drop_lockout_time =		$row['drop_lockout_time'];
                    $lead_filter_id =			$row['lead_filter_id'];
                    $lead_order =				$row['lead_order'];
                    $lead_order_randomize =		$row['lead_order_randomize'];
                    $lead_order_secondary =		$row['lead_order_secondary'];
                    $call_count_limit =			$row['call_count_limit'];
                }
                if (preg_match("/N/i", $no_hopper_dialing)) {
                    ### grab the next lead in the hopper for this campaign and reserve it for the user
                    //$stmt = "UPDATE vicidial_hopper SET status='QUEUE', user='$user' WHERE campaign_id='$campaign' AND status='READY' ORDER BY priority DESC,hopper_id LIMIT 1";
                    $astDB->where('campaign_id', $campaign);
                    $astDB->where('status', 'READY');
                    $astDB->orderBy('priority', 'desc');
                    $astDB->orderBy('hopper_id', 'asc');
                    $rslt = $astDB->update('vicidial_hopper', array('status'=>'QUEUE', 'user'=>$user), 1);
                    $affected_rows = $astDB->getRowCount();
                } else {
                    ### figure out what the next lead that should be dialed is
        
                    ##########################################################
                    ### BEGIN find the next lead to dial without looking in the hopper
                    ##########################################################
                #	$DB=1;
                    if (strlen($dial_statuses) > 2) {
                        $g = 0;
                        $p = '13';
                        $GMT_gmt[0] = '';
                        $GMT_hour[0] = '';
                        $GMT_day[0] = '';
                        while ($p > -13) {
                            $pzone = 3600 * $p;
                            $pmin = (gmdate("i", time() + $pzone));
                            $phour = ( (gmdate("G", time() + $pzone)) * 100);
                            $pday = gmdate("w", time() + $pzone);
                            $tz = sprintf("%.2f", $p);	
                            $GMT_gmt[$g] = "$tz";
                            $GMT_day[$g] = "$pday";
                            $GMT_hour[$g] = ($phour + $pmin);
                            $p = ($p - 0.25);
                            $g++;
                        }
    
                        //$stmt="SELECT call_time_id,call_time_name,call_time_comments,ct_default_start,ct_default_stop,ct_sunday_start,ct_sunday_stop,ct_monday_start,ct_monday_stop,ct_tuesday_start,ct_tuesday_stop,ct_wednesday_start,ct_wednesday_stop,ct_thursday_start,ct_thursday_stop,ct_friday_start,ct_friday_stop,ct_saturday_start,ct_saturday_stop,ct_state_call_times FROM vicidial_call_times where call_time_id='$local_call_time';";
                        $astDB->where('call_time_id', $local_call_time);
                        $rowx = $astDB->get('vicidial_call_times', null, 'call_time_id,call_time_name,call_time_comments,ct_default_start,ct_default_stop,ct_sunday_start,ct_sunday_stop,ct_monday_start,ct_monday_stop,ct_tuesday_start,ct_tuesday_stop,ct_wednesday_start,ct_wednesday_stop,ct_thursday_start,ct_thursday_stop,ct_friday_start,ct_friday_stop,ct_saturday_start,ct_saturday_stop,ct_state_call_times');
                        $Gct_default_start =	$rowx['ct_default_start'];
                        $Gct_default_stop =		$rowx['ct_default_stop'];
                        $Gct_sunday_start =		$rowx['ct_sunday_start'];
                        $Gct_sunday_stop =		$rowx['ct_sunday_stop'];
                        $Gct_monday_start =		$rowx['ct_monday_start'];
                        $Gct_monday_stop =		$rowx['ct_monday_stop'];
                        $Gct_tuesday_start =	$rowx['ct_tuesday_start'];
                        $Gct_tuesday_stop =		$rowx['ct_tuesday_stop'];
                        $Gct_wednesday_start =	$rowx['ct_wednesday_start'];
                        $Gct_wednesday_stop =	$rowx['ct_wednesday_stop'];
                        $Gct_thursday_start =	$rowx['ct_thursday_start'];
                        $Gct_thursday_stop =	$rowx['ct_thursday_stop'];
                        $Gct_friday_start =		$rowx['ct_friday_start'];
                        $Gct_friday_stop =		$rowx['ct_friday_stop'];
                        $Gct_saturday_start =	$rowx['ct_saturday_start'];
                        $Gct_saturday_stop =	$rowx['ct_saturday_stop'];
                        $Gct_state_call_times = $rowx['ct_state_call_times'];
    
                        $ct_states = '';
                        $ct_state_gmt_SQL = '';
                        $ct_srs=0;
                        $b=0;
                        if (strlen($Gct_state_call_times) > 2) {
                            $state_rules = explode('|', $Gct_state_call_times);
                            $ct_srs = ((count($state_rules)) - 2);
                        }
                        while($ct_srs >= $b) {
                            if (strlen($state_rules[$b])>1) {
                                //$stmt="SELECT state_call_time_id,state_call_time_state,state_call_time_name,state_call_time_comments,sct_default_start,sct_default_stop,sct_sunday_start,sct_sunday_stop,sct_monday_start,sct_monday_stop,sct_tuesday_start,sct_tuesday_stop,sct_wednesday_start,sct_wednesday_stop,sct_thursday_start,sct_thursday_stop,sct_friday_start,sct_friday_stop,sct_saturday_start,sct_saturday_stop from vicidial_state_call_times where state_call_time_id='$state_rules[$b]';";
                                $astDB->where('state_call_time_id', $state_rules[$b]);
                                $row = $astDB->get('vicidial_state_call_times', null, 'state_call_time_id,state_call_time_state,state_call_time_name,state_call_time_comments,sct_default_start,sct_default_stop,sct_sunday_start,sct_sunday_stop,sct_monday_start,sct_monday_stop,sct_tuesday_start,sct_tuesday_stop,sct_wednesday_start,sct_wednesday_stop,sct_thursday_start,sct_thursday_stop,sct_friday_start,sct_friday_stop,sct_saturday_start,sct_saturday_stop');
                                $Gstate_call_time_id =		$row['state_call_time_id'];
                                $Gstate_call_time_state =	$row['state_call_time_state'];
                                $Gsct_default_start =		$row['sct_default_start'];
                                $Gsct_default_stop =		$row['sct_default_stop'];
                                $Gsct_sunday_start =		$row['sct_sunday_start'];
                                $Gsct_sunday_stop =			$row['sct_sunday_stop'];
                                $Gsct_monday_start =		$row['sct_monday_start'];
                                $Gsct_monday_stop =			$row['sct_monday_stop'];
                                $Gsct_tuesday_start =		$row['sct_tuesday_start'];
                                $Gsct_tuesday_stop =		$row['sct_tuesday_stop'];
                                $Gsct_wednesday_start =		$row['sct_wednesday_start'];
                                $Gsct_wednesday_stop =		$row['sct_wednesday_stop'];
                                $Gsct_thursday_start =		$row['sct_thursday_start'];
                                $Gsct_thursday_stop =		$row['sct_thursday_stop'];
                                $Gsct_friday_start =		$row['sct_friday_start'];
                                $Gsct_friday_stop =			$row['sct_friday_stop'];
                                $Gsct_saturday_start =		$row['sct_saturday_start'];
                                $Gsct_saturday_stop =		$row['sct_saturday_stop'];
        
                                $ct_states .= "'$Gstate_call_time_state',";
    
                                $r=0;
                                $state_gmt='';
                                while($r < $g) {
                                    if ($GMT_day[$r] == 0) {	#### Sunday local time
                                        if (($Gsct_sunday_start == 0) and ($Gsct_sunday_stop == 0)) {
                                            if ( ($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        } else {
                                            if ( ($GMT_hour[$r] >= $Gsct_sunday_start) and ($GMT_hour[$r] < $Gsct_sunday_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        }
                                    }
                                    if ($GMT_day[$r] == 1) {	#### Monday local time
                                        if (($Gsct_monday_start == 0) and ($Gsct_monday_stop == 0)) {
                                            if ( ($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        } else {
                                            if ( ($GMT_hour[$r] >= $Gsct_monday_start) and ($GMT_hour[$r] < $Gsct_monday_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        }
                                    }
                                    if ($GMT_day[$r] == 2) {	#### Tuesday local time
                                        if (($Gsct_tuesday_start == 0) and ($Gsct_tuesday_stop == 0)) {
                                            if ( ($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        } else {
                                            if ( ($GMT_hour[$r] >= $Gsct_tuesday_start) and ($GMT_hour[$r] < $Gsct_tuesday_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        }
                                    }
                                    if ($GMT_day[$r] == 3) {	#### Wednesday local time
                                        if (($Gsct_wednesday_start == 0) and ($Gsct_wednesday_stop == 0)) {
                                            if ( ($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        } else {
                                            if ( ($GMT_hour[$r] >= $Gsct_wednesday_start) and ($GMT_hour[$r] < $Gsct_wednesday_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        }
                                    }
                                    if ($GMT_day[$r] == 4) {	#### Thursday local time
                                        if (($Gsct_thursday_start == 0) and ($Gsct_thursday_stop == 0)) {
                                            if ( ($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        } else {
                                            if ( ($GMT_hour[$r] >= $Gsct_thursday_start) and ($GMT_hour[$r] < $Gsct_thursday_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        }
                                    }
                                    if ($GMT_day[$r] == 5) {	#### Friday local time
                                        if (($Gsct_friday_start == 0) and ($Gsct_friday_stop == 0)) {
                                            if ( ($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        } else {
                                            if ( ($GMT_hour[$r] >= $Gsct_friday_start) and ($GMT_hour[$r] < $Gsct_friday_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        }
                                    }
                                    if ($GMT_day[$r] == 6) {	#### Saturday local time=
                                        if (($Gsct_saturday_start == 0) and ($Gsct_saturday_stop == 0)) {
                                            if ( ($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        } else {
                                            if ( ($GMT_hour[$r] >= $Gsct_saturday_start) and ($GMT_hour[$r] < $Gsct_saturday_stop) )
                                                {$state_gmt .= "'$GMT_gmt[$r]',";}
                                        }
                                    }
                                    $r++;
                                }
                                $state_gmt = "$state_gmt'99'";
                                $ct_state_gmt_SQL .= "or (state='$Gstate_call_time_state' and gmt_offset_now IN($state_gmt)) ";
                            }
    
                            $b++;
                        }
                        if (strlen($ct_states)>2) {
                            $ct_states = preg_replace("/,$/i", '', $ct_states);
                            $ct_statesSQL = "and state NOT IN($ct_states)";
                        } else {
                            $ct_statesSQL = "";
                        }
    
                        $r = 0;
                        $default_gmt = '';
                        while($r < $g) {
                            if ($GMT_day[$r] == 0) {	#### Sunday local time
                                if (($Gct_sunday_start == 0) and ($Gct_sunday_stop == 0)) {
                                    if ( ($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                } else {
                                    if ( ($GMT_hour[$r] >= $Gct_sunday_start) and ($GMT_hour[$r] < $Gct_sunday_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                }
                            }
                            if ($GMT_day[$r] == 1) {	#### Monday local time
                                if (($Gct_monday_start == 0) and ($Gct_monday_stop == 0)) {
                                    if ( ($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                } else {
                                    if ( ($GMT_hour[$r] >= $Gct_monday_start) and ($GMT_hour[$r] < $Gct_monday_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                }
                            }
                            if ($GMT_day[$r] == 2) {	#### Tuesday local time
                                if (($Gct_tuesday_start == 0) and ($Gct_tuesday_stop == 0)) {
                                    if ( ($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                } else {
                                    if ( ($GMT_hour[$r] >= $Gct_tuesday_start) and ($GMT_hour[$r] < $Gct_tuesday_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                }
                            }
                            if ($GMT_day[$r] == 3) {	#### Wednesday local time
                                if (($Gct_wednesday_start == 0) and ($Gct_wednesday_stop == 0)) {
                                    if ( ($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                } else {
                                    if ( ($GMT_hour[$r] >= $Gct_wednesday_start) and ($GMT_hour[$r] < $Gct_wednesday_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                }
                            }
                            if ($GMT_day[$r] == 4) {	#### Thursday local time
                                if (($Gct_thursday_start == 0) and ($Gct_thursday_stop == 0)) {
                                    if ( ($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop) )
                                        {$default_gmt.="'$GMT_gmt[$r]',";}
                                } else {
                                    if ( ($GMT_hour[$r] >= $Gct_thursday_start) and ($GMT_hour[$r] < $Gct_thursday_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                }
                            }
                            if ($GMT_day[$r] == 5) {	#### Friday local time
                                if (($Gct_friday_start == 0) and ($Gct_friday_stop == 0)) {
                                    if ( ($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                } else {
                                    if ( ($GMT_hour[$r] >= $Gct_friday_start) and ($GMT_hour[$r] < $Gct_friday_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                }
                            }
                            if ($GMT_day[$r] == 6) {	#### Saturday local time
                                if (($Gct_saturday_start == 0) and ($Gct_saturday_stop == 0)) {
                                    if ( ($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                } else {
                                    if ( ($GMT_hour[$r] >= $Gct_saturday_start) and ($GMT_hour[$r] < $Gct_saturday_stop) )
                                        {$default_gmt .= "'$GMT_gmt[$r]',";}
                                }
                            }
                            $r++;
                        }
    
                        $default_gmt = "$default_gmt'99'";
                        $all_gmtSQL = "(gmt_offset_now IN($default_gmt) $ct_statesSQL) $ct_state_gmt_SQL";
        
                        $dial_statuses = preg_replace("/ -$/","",$dial_statuses);
                        $Dstatuses = explode(" ", $dial_statuses);
                        $Ds_to_print = (count($Dstatuses) - 0);
                        $Dsql = '';
                        $o = 0;
                        while ($Ds_to_print > $o)  {
                            $o++;
                            $Dsql .= "'$Dstatuses[$o]',";
                        }
                        $Dsql = preg_replace("/,$/","",$Dsql);
                        if (strlen($Dsql) < 2) {$Dsql = "''";}
        
                        $DLTsql='';
                        if ($drop_lockout_time > 0) {
                            $DLseconds = ($drop_lockout_time * 3600);
                            $DLseconds = floor($DLseconds);
                            $DLseconds = intval("$DLseconds");
                            $DLTsql = "and ( ( (status IN('DROP','XDROP')) and (last_local_call_time < CONCAT(DATE_ADD(NOW(), INTERVAL -$DLseconds SECOND),' ',CURTIME()) ) ) or (status NOT IN('DROP','XDROP')) )";
                        }
    
                        $CCLsql='';
                        if ($call_count_limit > 0) {
                            $CCLsql = "and (called_count < $call_count_limit)";
                        }
        
                        //$stmt="SELECT lead_filter_sql FROM vicidial_lead_filters where lead_filter_id='$lead_filter_id';";
                        $astDB->where('lead_filter_id', $lead_filter_id);
                        $rslt = $astDB->get('vicidial_lead_filters', null, 'lead_filter_sql');
                        $filtersql_ct = $astDB->getRowCount();
                        if ($filtersql_ct > 0) {
                            $row = $rslt[0];
                            $fSQL = "and ({$row['lead_filter_sql']})";
                            $fSQL = preg_replace('/\\\\/','',$fSQL);
                        }
    
                        //$stmt="SELECT list_id FROM vicidial_lists where campaign_id='$campaign' and active='Y';";
                        $astDB->where('campaign_id', $campaign);
                        $astDB->where('active', 'Y');
                        $rslt = $astDB->get('vicidial_lists', null, 'list_id');
                        $camplists_ct = $astDB->getRowCount();
                        $k = 0;
                        $camp_lists = '';
                        while ($camplists_ct > $k) {
                            $row = $rslt[$k];
                            $camp_lists .=	"'{$row['list_id']}',";
                            $k++;
                        }
                        $camp_lists = preg_replace("/.$/i", "", $camp_lists);
                        if (strlen($camp_lists) < 4) {$camp_lists = "''";}
    
                        //$stmt="SELECT user_group,territory FROM vicidial_users where user='$user';";
                        $astDB->where('user', $user);
                        $rslt = $astDB->get('vicidial_users', null, 'user_group,territory');
                        $userterr_ct = $astDB->getRowCount();
                        if ($userterr_ct > 0) {
                            $row = $rslt[0];
                            $user_group =	$row['user_group'];
                            $territory =	$row['territory'];
                        }
    
                        $adooSQL = '';
                        if (preg_match("/TERRITORY/i",$agent_dial_owner_only)) {
                            $agent_territories = '';
                            $agent_choose_territories = 0;
                            //$stmt="SELECT agent_choose_territories from vicidial_users where user='$user';";
                            $astDB->where('user', $user);
                            $rslt = $astDB->get('vicidial_users', null, 'agent_choose_territories');
                            $Uterrs_to_parse = $astDB->getRowCount();
                            if ($Uterrs_to_parse > 0) {
                                $row = $rslt[0];
                                $agent_choose_territories = $row['agent_choose_territories'];
                            }
        
                            if ($agent_choose_territories < 1) {
                                //$stmt="SELECT territory from vicidial_user_territories where user='$user';";
                                $astDB->where('user', $user);
                                $rslt = $astDB->get('vicidial_user_territories', null, 'territory');
                                $vuts_to_parse = $astDB->getRowCount();
                                $o = 0;
                                while ($vuts_to_parse > $o) {
                                    $row = $rslt[$o];
                                    $agent_territories .= "'{$row['territory']}',";
                                    $o++;
                                }
                                $agent_territories = preg_replace("/\,$/", '', $agent_territories);
                                $searchownerSQL = " and owner IN($agent_territories)";
                                if ($vuts_to_parse < 1)
                                    {$searchownerSQL = " and lead_id < 0";}
                            } else {
                                //$stmt="SELECT agent_territories from vicidial_live_agents where user='$user';";
                                $astDB->where('user', $user);
                                $rslt = $astDB->get('vicidial_live_agents', null, 'agent_territories');
                                $terrs_to_parse = $astDB->getRowCount();
                                if ($terrs_to_parse > 0) {
                                    $row = $rslt[0];
                                    $agent_territories = $row['agent_territories'];
                                    $agent_territories = preg_replace("/ -$|^ /", '', $agent_territories);
                                    $agent_territories = preg_replace("/ /", "','", $agent_territories);
                                    $searchownerSQL = " and owner IN('$agent_territories')";
                                }
                            }
        
                            $adooSQL = $searchownerSQL;
                        }
                        if (preg_match("/USER/i",$agent_dial_owner_only)) {$adooSQL = "and owner='$user'";}
                        if (preg_match("/USER_GROUP/i",$agent_dial_owner_only)) {$adooSQL = "and owner='$user_group'";}
                        if (preg_match("/_BLANK/",$agent_dial_owner_only)) {
                            $adooSQLa = preg_replace("/^and /", '', $adooSQL);
                            $blankSQL = "and ( ($adooSQLa) or (owner='') or (owner is NULL) )";
                            $adooSQL = $blankSQL;
                        }
    
                        if ($lead_order_randomize == 'Y') {
                            $last_order = "RAND()";
                        } else {
                            $last_order = "lead_id asc";
                            if ($lead_order_secondary == 'LEAD_ASCEND') {$last_order = "lead_id asc";}
                            if ($lead_order_secondary == 'LEAD_DESCEND') {$last_order = "lead_id desc";}
                            if ($lead_order_secondary == 'CALLTIME_ASCEND') {$last_order = "last_local_call_time asc";}
                            if ($lead_order_secondary == 'CALLTIME_DESCEND') {$last_order = "last_local_call_time desc";}
                        }
    
                        $order_stmt = '';
                        if (preg_match("/DOWN/i",$lead_order)) {$order_stmt = 'order by lead_id asc';}
                        if (preg_match("/UP/i",$lead_order)) {$order_stmt = 'order by lead_id desc';}
                        if (preg_match("/UP LAST NAME/i",$lead_order)) {$order_stmt = "order by last_name desc, $last_order";}
                        if (preg_match("/DOWN LAST NAME/i",$lead_order)) {$order_stmt = "order by last_name, $last_order";}
                        if (preg_match("/UP PHONE/i",$lead_order)) {$order_stmt = "order by phone_number desc, $last_order";}
                        if (preg_match("/DOWN PHONE/i",$lead_order)) {$order_stmt = "order by phone_number, $last_order";}
                        if (preg_match("/UP COUNT/i",$lead_order)) {$order_stmt = "order by called_count desc, $last_order";}
                        if (preg_match("/DOWN COUNT/i",$lead_order)) {$order_stmt = "order by called_count, $last_order";}
                        if (preg_match("/UP LAST CALL TIME/i",$lead_order)) {$order_stmt = "order by last_local_call_time desc, $last_order";}
                        if (preg_match("/DOWN LAST CALL TIME/i",$lead_order)) {$order_stmt = "order by last_local_call_time, $last_order";}
                        if (preg_match("/RANDOM/i",$lead_order)) {$order_stmt = "order by RAND()";}
                        if (preg_match("/UP RANK/i",$lead_order)) {$order_stmt = "order by rank desc, $last_order";}
                        if (preg_match("/DOWN RANK/i",$lead_order)) {$order_stmt = "order by rank, $last_order";}
                        if (preg_match("/UP OWNER/i",$lead_order)) {$order_stmt = "order by owner desc, $last_order";}
                        if (preg_match("/DOWN OWNER/i",$lead_order)) {$order_stmt = "order by owner, $last_order";}
                        if (preg_match("/UP TIMEZONE/i",$lead_order)) {$order_stmt = "order by gmt_offset_now desc, $last_order";}
                        if (preg_match("/DOWN TIMEZONE/i",$lead_order)) {$order_stmt = "order by gmt_offset_now, $last_order";}
        
                        $stmt="UPDATE vicidial_list SET user='QUEUE$user' where called_since_last_reset='N' and user NOT LIKE \"QUEUE%\" and status IN($Dsql) and list_id IN($camp_lists) and ($all_gmtSQL) $CCLsql $DLTsql $fSQL $adooSQL $order_stmt LIMIT 1;";
                        $rslt = $astDB->rawQuery($stmt);
                        $affected_rows = $astDB->getRowCount();
        
                    #	$fp = fopen ("./DNNdebug_log.txt", "a");
                    #	fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$agent_dialed_number|$user|M|$MqueryCID||$province|$affected_rows|$stmt|\n");
                    #	fclose($fp);
    
                        if ($affected_rows > 0) {
                            //$stmt="SELECT lead_id,list_id,gmt_offset_now,state,entry_list_id,vendor_lead_code FROM vicidial_list where user='QUEUE$user' order by modify_date desc LIMIT 1;";
                            $QUEUEuser = "QUEUE{$user}";
                            $astDB->where('user', $QUEUEuser);
                            $astDB->orderBy('modify_date', 'desc');
                            $rslt = $astDB->getOne('vicidial_list', 'lead_id,list_id,gmt_offset_now,state,entry_list_id,vendor_lead_code');
                            $leadpick_ct = $astDB->getRowCount();
                            if ($leadpick_ct > 0) {
                                $row = $rslt;
                                $lead_id =			$row['lead_id'];
                                $list_id =			$row['list_id'];
                                $gmt_offset_now =	$row['gmt_offset_now'];
                                $state =			$row['state'];
                                $entry_list_id =	$row['entry_list_id'];
                                $vendor_lead_code = $row['vendor_lead_code'];
        
                                //$stmt = "INSERT INTO vicidial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='QUEUE',list_id='$list_id',gmt_offset_now='$gmt_offset_now',state='$state',alt_dial='MAIN',user='$user',priority='0',source='Q',vendor_lead_code='$vendor_lead_code';";
                                $insertData = array(
                                    'lead_id' => $lead_id,
                                    'campaign_id' => $campaign,
                                    'status' => 'QUEUE',
                                    'list_id' => $list_id,
                                    'gmt_offset_now' => $gmt_offset_now,
                                    'state' => $state,
                                    'alt_dial' => 'MAIN',
                                    'user' => $user,
                                    'priority' => '0',
                                    'source' => 'Q',
                                    'vendor_lead_code' => $vendor_lead_code
                                );
                                $rslt = $astDB->insert('vicidial_hopper', $insertData);
                            }
                        }
                    }
                ##########################################################
                ### END  find the next lead to dial without looking in the hopper
                ##########################################################
            #	$DB=0;
                }
            }
        }
    
        if ($affected_rows > 0 && $errmsg < 1) {
            if (!$CBleadIDset) {
                ##### grab the lead_id of the reserved user in vicidial_hopper
                //$stmt="SELECT lead_id FROM vicidial_hopper where campaign_id='$campaign' and status='QUEUE' and user='$user' LIMIT 1;";
                $astDB->where('campaign_id', $campaign);
                $astDB->where('status', 'QUEUE');
                $astDB->where('user', $user);
                $rslt = $astDB->getOne('vicidial_hopper', 'lead_id');
                $hopper_leadID_ct = $astDB->getRowCount();
                if ($hopper_leadID_ct > 0) {
                    $row = $rslt;
                    $lead_id = $row['lead_id'];
                }
            }
    
            ##### grab the data from vicidial_list for the lead_id
            //$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
            $astDB->where('lead_id', $lead_id);
            $rslt = $astDB->getOne('vicidial_list', 'lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id');
            $list_lead_ct = $astDB->getRowCount();
            if ($list_lead_ct > 0) {
                $row = $rslt;
            #	$lead_id		= trim("$row['lead_id']");
                $dispo			= trim("{$row['status']}");
                $tsr			= trim("{$row['user']}");
                $vendor_id		= trim("{$row['vendor_lead_code']}");
                $source_id		= trim("{$row['source_id']}");
                $list_id		= trim("{$row['list_id']}");
                $gmt_offset_now	= trim("{$row['gmt_offset_now']}");
                $called_since_last_reset = trim("{$row['called_since_last_reset']}");
                $phone_code		= trim("{$row['phone_code']}");
                if ($override_phone < 1)
                    {$phone_number	= trim("{$row['phone_number']}");}
                $title			= trim("{$row['title']}");
                $first_name		= trim("{$row['first_name']}");
                $middle_initial	= trim("{$row['middle_initial']}");
                $last_name		= trim("{$row['last_name']}");
                $address1		= stripcslashes(trim("{$row['address1']}"));
                $address2		= stripcslashes(trim("{$row['address2']}"));
                $address3		= trim("{$row['address3']}");
                $city			= trim("{$row['city']}");
                $state			= trim("{$row['state']}");
                $province		= trim("{$row['province']}");
                $postal_code	= trim("{$row['postal_code']}");
                $country_code	= trim("{$row['country_code']}");
                $gender			= trim("{$row['gender']}");
                $date_of_birth	= trim("{$row['date_of_birth']}");
                $alt_phone		= trim("{$row['alt_phone']}");
                $email			= trim("{$row['email']}");
                $security		= trim("{$row['security_phrase']}");
                $comments		= stripcslashes(trim("{$row['comments']}"));
                $called_count	= trim("{$row['called_count']}");
                $rank			= trim("{$row['rank']}");
                $owner			= trim("{$row['owner']}");
                $entry_list_id	= trim("{$row['entry_list_id']}");
                if ($entry_list_id < 100) {$entry_list_id = $list_id;}
            }
            if ($system_settings->qc_features_active > 0) {
                ##### if list has audited comments, grab the audited comments
                $ACcount = '';
                $ACcomments = '';
                $audit_comments_active = audit_comments_active($astDB, $list_id, $format, $user, $NOW_TIME, $server_ip, $session_name);
                if ($audit_comments_active) {
                    get_audited_comments($astDB, $lead_id, $format, $user, $NOW_TIME, $server_ip, $session_name);
                }
                $ACcomments = strip_tags(htmlentities($ACcomments));
                $ACcomments = preg_replace("/\r/i", '', $ACcomments);
                $ACcomments = preg_replace("/\n/i", '!N', $ACcomments);
            }

            $called_count++;

            if ( (strlen($agent_dialed_type) < 3) or (strlen($agent_dialed_number) < $manual_dial_min_digits) ) {
                if (strlen($agent_dialed_type) < 3)
                    {$agent_dialed_type = 'MAIN';}
                    
                if ($campaign_settings->alt_number_dialing == 'Y')
                    {$agent_dialed_type = 'ALT';}
                    
                if ($phone_number !== '' && strlen($phone_number) > 3) {
                    $agent_dialed_number = $phone_number;
                } else if ($agent_dialed_type == 'ALT' && ((strlen($phone_number) <= 3) or (strlen($phone_number) < $manual_dial_min_digits))) {
                    $agent_dialed_number = ($alt_phone !== '' ? $alt_phone : $address3);
                }
            }
            if ( (strlen($callback_id) > 0) and (strlen($lead_id) > 0) ) {
                if ($agent_dialed_type == 'ALT')
                    {$agent_dialed_number = $alt_phone;}
                if ($agent_dialed_type == 'ADDR3')
                    {$agent_dialed_number = $address3;}
            }

            ##### BEGIN check for postal_code and phone time zones if alert enabled
            $post_phone_time_diff_alert_message = '';
            //$stmt="SELECT post_phone_time_diff_alert,local_call_time,owner_populate FROM vicidial_campaigns where campaign_id='$campaign';";
            $astDB->where('campaign_id', $campaign);
            $rslt = $astDB->get('vicidial_campaigns', null, 'post_phone_time_diff_alert,local_call_time,owner_populate');
            $camp_pptda_ct = $astDB->getRowCount();
            if ($camp_pptda_ct > 0) {
                $row = $rslt[0];
                $post_phone_time_diff_alert =	$row['post_phone_time_diff_alert'];
                $local_call_time =				$row['local_call_time'];
                $owner_populate =				$row['owner_populate'];
            }
            if ( ($post_phone_time_diff_alert == 'ENABLED') or (preg_match("/OUTSIDE_CALLTIME/", $post_phone_time_diff_alert)) ) {
                ### get current gmt_offset of the phone_number
                $postalgmtNOW = '';
                $USarea = substr($agent_dialed_number, 0, 3);
                $PHONEgmt_offset = lookup_gmt($astDB, $phone_code, $USarea, $state, $LOCAL_GMT_OFF_STD, $Shour, $Smin, $Ssec, $Smon, $Smday, $Syear, $postalgmtNOW, $postal_code);
                $PHONEdialable = dialable_gmt($astDB, $local_call_time, $PHONEgmt_offset, $state);
    
                $postalgmtNOW = 'POSTAL';
                $POSTgmt_offset = lookup_gmt($astDB, $phone_code, $USarea, $state, $LOCAL_GMT_OFF_STD, $Shour, $Smin, $Ssec, $Smon, $Smday, $Syear, $postalgmtNOW, $postal_code);
                $POSTdialable = dialable_gmt($astDB, $local_call_time, $POSTgmt_offset, $state);
    
            #	$post_phone_time_diff_alert_message = "$POSTgmt_offset|$POSTdialable   ---   $PHONEgmt_offset|$PHONEdialable|$USarea";
                $post_phone_time_diff_alert_message = '';
    
                if ($PHONEgmt_offset != $POSTgmt_offset) {
                    $post_phone_time_diff_alert_message .= "Phone and Post Code Time Zone Mismatch! ";
    
                    if ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_ONLY') {
                        $post_phone_time_diff_alert_message = '';
                        if ($PHONEdialable < 1)
                            {$post_phone_time_diff_alert_message .= " Phone Area Code Outside Dialable Zone $PHONEgmt_offset ";}
                        if ($POSTdialable < 1)
                            {$post_phone_time_diff_alert_message .= " Postal Code Outside Dialable Zone $POSTgmt_offset";}
                    }
                }
                if ( ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_PHONE') or ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_POSTAL') or ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_BOTH') )
                    {$post_phone_time_diff_alert_message = '';}
    
                if ( ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_PHONE') or ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_BOTH') ) {
                    if ($PHONEdialable < 1)
                        {$post_phone_time_diff_alert_message .= " Phone Area Code Outside Dialable Zone $PHONEgmt_offset ";}
                }
                if ( ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_POSTAL') or ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_BOTH') ) {
                    if ($POSTdialable < 1)
                        {$post_phone_time_diff_alert_message .= " Postal Code Outside Dialable Zone $POSTgmt_offset ";}
                }
            }
            ##### END check for postal_code and phone time zones if alert enabled
    
    
            ##### if lead is a callback, grab the callback comments
            $CBentry_time =		'';
            $CBcallback_time =	'';
            $CBuser =			'';
            $CBcomments =		'';
            $CBstatus =			0;
    
            //$stmt="SELECT count(*) FROM vicidial_statuses where status='$dispo' and scheduled_callback='Y';";
            $astDB->where('status', $dispo);
            $astDB->where('scheduled_callback', 'Y');
            $rslt = $astDB->get('vicidial_statuses');
            $cb_record_ct = $astDB->getRowCount();
            if ($cb_record_ct > 0) {
                $CBstatus = $cb_record_ct;
            }
            if ($CBstatus < 1) {
                //$stmt="SELECT count(*) FROM vicidial_campaign_statuses where status='$dispo' and scheduled_callback='Y';";
                $astDB->where('status', $dispo);
                $astDB->where('scheduled_callback', 'Y');
                $rslt = $astDB->get('vicidial_campaign_statuses');
                $cb_record_ct = $astDB->getRowCount();
                if ($cb_record_ct > 0) {
                    $CBstatus = $cb_record_ct;
                }
            }
            if ( ($CBstatus > 0) or ($dispo == 'CBHOLD') ) {
                //$stmt="SELECT entry_time,callback_time,user,comments FROM vicidial_callbacks where lead_id='$lead_id' order by callback_id desc LIMIT 1;";
                $astDB->where('lead_id', $lead_id);
                $astDB->orderBy('callback_id', 'desc');
                $rslt = $astDB->getOne('vicidial_callbacks', 'entry_time,callback_time,user,comments,callback_id');
                $cb_record_ct = $astDB->getRowCount();
                if ($cb_record_ct > 0) {
                    $row = $rslt;
                    $CBentry_time =		trim("{$row['entry_time']}");
                    $CBcallback_time =	trim("{$row['callback_time']}");
                    $CBuser =			trim("{$row['user']}");
                    $CBcomments =		trim("{$row['comments']}");
                    $CBack_id =		    trim("{$row['callback_id']}");
                    
                    $astDB->where('callback_id', $CBack_id);
                    $astDB->where('user', $user);
                    $astDB->where('status', 'INACTIVE', '!=');
                    $query = $astDB->update('vicidial_callbacks', array('status'=>'INACTIVE'));
                }
                
                $astDB->where('lead_id', $lead_id);
                $astDB->orderBy('entry_time', 'asc');
                $rslt = $astDB->get('vicidial_callbacks', null, 'entry_time,comments,user');
                $CBcommentsALL = '<div class="col-sm-12"><h4 style="font-weight: 600;">CallBack Comments</h4></div>';
                foreach ($rslt as $row) {
                    $getDate = strtotime($row['entry_time']);
                    $thisDate = date('Y-m-d', $getDate);
                    $thisComment = $row['comments'];
                    $thisUser = $row['user'];
                    if (strlen($thisComment) > 0) {
                        $CBcommentsALL .= '<div class="col-sm-12" style="font-size: 14px;">';
                        $CBcommentsALL .= '	<strong>'.$thisDate.':</strong> '.$thisComment.' ~ <strong>'.$thisUser.'</strong>';
                        $CBcommentsALL .= '</div>';
                    }
                }
            }
    
            //$stmt = "SELECT local_gmt FROM servers where active='Y' limit 1;";
            $astDB->where('active', 'Y');
            $rslt = $astDB->getOne('servers', 'local_gmt');
            $server_ct = $astDB->getRowCount();
            if ($server_ct > 0) {
                $row = $rslt;
                $local_gmt =	$row['local_gmt'];
                $isdst = date("I");
                if ($isdst) {$local_gmt++;}
            }
            $LLCT_DATE_offset = ($local_gmt - $gmt_offset_now);
            $LLCT_DATE = date("Y-m-d H:i:s", mktime(date("H")-$LLCT_DATE_offset, date("i"), date("s"), date("m"), date("d"), date("Y")));
    
            if (preg_match('/Y/', $called_since_last_reset)) {
                $called_since_last_reset = preg_replace('/Y/', '', $called_since_last_reset);
                if (strlen($called_since_last_reset) < 1)
                    {$called_since_last_reset = 0;}
                $called_since_last_reset++;
                $called_since_last_reset = "Y$called_since_last_reset";
            } else {
                $called_since_last_reset = 'Y';
            }
            
            $updateData = array(
                'status' => 'INCALL',
                'called_since_last_reset' => $called_since_last_reset,
                'called_count' => $called_count,
                'user' => $user,
                'last_local_call_time' => $LLCT_DATE
            );
            if ( ($owner_populate=='ENABLED') and ( (strlen($owner) < 1) or ($owner=='NULL') ) ) {
                $updateData['owner'] = $user;
                $owner = $user;
            }
            ### flag the lead as called and change it's status to INCALL
            //$stmt = "UPDATE vicidial_list set status='INCALL', called_since_last_reset='$called_since_last_reset', called_count='$called_count',user='$user',last_local_call_time='$LLCT_DATE'$ownerSQL where lead_id='$lead_id';";
            $astDB->where('lead_id', $lead_id);
            $rslt = $astDB->update('vicidial_list', $updateData);
    
            if (!$CBleadIDset) {
                ### delete the lead from the hopper
                //$stmt = "DELETE FROM vicidial_hopper where lead_id='$lead_id';";
                $astDB->where('lead_id', $lead_id);
                $astDB->delete('vicidial_hopper');
            }
    
            //$stmt="UPDATE vicidial_agent_log set lead_id='$lead_id',comments='MANUAL' where agent_log_id='$agent_log_id';";
            $astDB->where('agent_log_id', $agent_log_id);
            //$astDB->where('uniqueid', 'INACTIVE', '!=');
            //$rslt = $astDB->update('vicidial_agent_log', array('lead_id'=>$lead_id, 'comments'=>'MANUAL'));
            $rslt = $astDB->update('vicidial_agent_log', array('comments'=>'MANUAL'));
    
            //$stmt="UPDATE vicidial_lists set list_lastcalldate=NOW() where list_id='$list_id';";
            $astDB->where('list_id', $list_id);
            $rslt = $astDB->update('vicidial_lists', array('list_lastcalldate'=>'NOW()'));
    
            $campaign_cid_override = '';
            $LISTweb_form_address = '';
            $LISTweb_form_address_two = '';
            
            ### check if there is a list_id override
            if (strlen($list_id) > 1) {
                //$stmt = "SELECT campaign_cid_override,web_form_address,web_form_address_two FROM vicidial_lists where list_id='$list_id';";
                $astDB->where('list_id', $list_id);
                $rslt = $astDB->get('vicidial_lists', null, 'campaign_cid_override,web_form_address,web_form_address_two');
                $lio_ct = $astDB->getRowCount();
                if ($lio_ct > 0) {
                    $row = $rslt[0];
                    $campaign_cid_override =	(!is_null($row['campaign_cid_override'])) ? $row['campaign_cid_override'] : '';
                    $LISTweb_form_address =		(!is_null($row['web_form_address'])) ? $row['web_form_address'] : '';
                    $LISTweb_form_address_two =	(!is_null($row['web_form_address_two'])) ? $row['web_form_address_two'] : '';
                }
            }
    
            ### if preview dialing, do not send the call	
            if ( (strlen($preview)<1) or ($preview == 'NO') or (strlen($dial_ingroup) > 1) ) {
                ### prepare variables to place manual call from VICIDiaL
                $CCID_on = 0;
                $CCID = '';
                $local_DEF = 'Local/';
                $local_AMP = '@';
                $Local_out_prefix = '9';
                $Local_dial_timeout = '60';
            #	$Local_persist = '/n';
                $Local_persist = '';
                if ($dial_timeout > 4) {$Local_dial_timeout = $dial_timeout;}
                $Local_dial_timeout = ($Local_dial_timeout * 1000);
                if (strlen($dial_prefix) > 0) {$Local_out_prefix = "$dial_prefix";}
                if (strlen($campaign_cid) > 6) {
                    $CCID = "$campaign_cid";
                    $CCID_on++;
                }
                if (strlen($campaign_cid_override) > 6) {
                    $CCID = "$campaign_cid_override";
                    $CCID_on++;
                }
                
                $dynRslt = $goDB->rawQuery("SHOW COLUMNS FROM `go_campaigns` LIKE 'dynamic_cid'");
                if ($goDB->getRowCount() > 0) {
                    $goDB->where('campaign_id', $campaign);
                    $rslt = $goDB->getOne('go_campaigns', 'dynamic_cid');
                    $dynCID = $rslt['dynamic_cid'];
                    
                    if ($dynCID === 'Y') {
                        $astDB->where('phone_number', $phone_number);
                        $astDB->where('lead_id', $lead_id);
                        $rslt = $astDB->getOne('vicidial_list', 'security_phrase');
                        $dynamic_cid = $rslt['security_phrase'];
                        
                        if (strlen($dynamic_cid) > 6) {
                            $CCID = "$dynamic_cid";
                            $CCID_on++;
                        }
                    }
                }
                
                ### check for custom cid use
                $use_custom_cid = 0;
                $temp_CID = '';
                //$stmt = "SELECT use_custom_cid FROM vicidial_campaigns where campaign_id='$campaign';";
                $astDB->where('campaign_id', $campaign);
                $rslt = $astDB->get('vicidial_campaigns', null, 'use_custom_cid');
                $uccid_ct = $astDB->getRowCount();
                if ($uccid_ct > 0) {
                    $row = $rslt[0];
                    $use_custom_cid = $row['use_custom_cid'];
                    if ($use_custom_cid == 'AREACODE') {
                        $temp_ac = substr("$agent_dialed_number", 0, 3);
                        //$stmt = "SELECT outbound_cid FROM vicidial_campaign_cid_areacodes where campaign_id='$campaign' and areacode='$temp_ac' and active='Y' order by call_count_today limit 1;";
                        $astDB->where('campaign_id', $campaign);
                        $astDB->where('areacode', $temp_ac);
                        $astDB->where('active', 'Y');
                        $astDB->orderBy('call_count_today');
                        $rslt = $astDB->getOne('vicidial_campaign_cid_areacodes', 'outbound_cid,call_count_today');
                        $vcca_ct = $astDB->getRowCount();
                        if ($vcca_ct > 0) {
                            $row = $rslt;
                            $temp_vcca = $row['outbound_cid'];
                            $call_count_today = $row['call_count_today'];

                            //$stmt="UPDATE vicidial_campaign_cid_areacodes set call_count_today=(call_count_today + 1) where campaign_id='$campaign' and areacode='$temp_ac' and outbound_cid='$temp_vcca';";
                            $astDB->where('campaign_id', $campaign);
                            $astDB->where('areacode', $temp_ac);
                            $astDB->where('outbound_cid', $temp_vcca);
                            $astDB->update('vicidial_campaign_cid_areacodes', array('call_count_today'=>($call_count_today + 1)));
                        }
                        $temp_CID = preg_replace("/\D/", '', $temp_vcca);
                    }
                    if ($use_custom_cid == 'Y')
                        {$temp_CID = preg_replace("/\D/", '', $security);}
                    if (strlen($temp_CID) > 6) 
                        {$CCID = "$temp_CID";   $CCID_on++;}
                }
    
                if (preg_match("/x/i", $dial_prefix)) {$Local_out_prefix = '';}
    
                $PADlead_id = sprintf("%010s", $lead_id);
                while (strlen($PADlead_id) > 10) {$PADlead_id = substr("$PADlead_id", 1);}
    
                ### check for extension append in campaign
                $use_eac = 0;
                //$stmt = "SELECT count(*) FROM vicidial_campaigns where extension_appended_cidname='Y' and campaign_id='$campaign';";
                $astDB->where('extension_appended_cidname', 'Y');
                $astDB->where('campaign_id', $campaign);
                $rslt = $astDB->get('vicidial_campaigns');
                $eacid_ct = $astDB->getRowCount();
                if ($eacid_ct > 0) {
                    $use_eac =	$eacid_ct;
                }
    
                # Create unique calleridname to track the call: MmddhhmmssLLLLLLLLLL
                $MqueryCID = "M$CIDdate$PADlead_id";
                $EAC = '';
                if ($use_eac > 0) {
                    $eac_extension = preg_replace("/SIP\/|IAX2\/|Zap\/|DAHDI\/|Local\//", '', $eac_phone);
                    $EAC=" $eac_extension";
                }
    
                ### whether to omit phone_code or not
                if (preg_match('/Y/i', $omit_phone_code)) {
                    $Ndialstring = "$Local_out_prefix$agent_dialed_number";
                } else {
                    $Ndialstring = "$Local_out_prefix$phone_code$agent_dialed_number";
                }
    
                if ( ($usegroupalias > 0) and (strlen($account) > 1) ) {
                    $RAWaccount = $account;
                    $account = "Account: $account";
                    $variable = "Variable: usegroupalias=1";
                } else {
                    $RAWaccount = '';
                    $account = '';
                    $variable = '';
                }
    
                $dial_channel = "{$local_DEF}{$conf_exten}{$local_AMP}{$ext_context}{$Local_persist}";
    
                $preset_name='';
                if (strlen($dial_ingroup) > 1) {
                    ### look for a dial-ingroup cid
                    $dial_ingroup_cid='';
                    //$stmt = "SELECT dial_ingroup_cid FROM vicidial_inbound_groups where group_id='$dial_ingroup';";
                    $astDB->where('group_id', $dial_ingroup);
                    $astDB->get('vicidial_inbound_groups', null, 'dial_ingroup_cid');
                    $digcid_ct = $astDB->getRowCount();
                    if ($digcid_ct > 0) {
                        $dial_ingroup_cid =	$row['dial_ingroup_cid'];
                    }
                    if (strlen($dial_ingroup_cid) > 6) {
                        $CCID = "$dial_ingroup_cid";
                        $CCID_on++;
                    }
    
                    $preset_name = 'DIG';
                    $MqueryCID = "Y$CIDdate$PADlead_id";
                    
                    $loop_ingroup_dial_prefix = '8305888888888888';
                    $dial_wait_seconds = '4';	# 1 digit only
                    if ($nocall_dial_flag == 'ENABLED') {
                        $Ndialstring = "{$loop_ingroup_dial_prefix}{$dial_wait_seconds}" . "999";
                        $preset_name = 'DIG_NODIAL';
                    } else {
                        $Ndialstring = "{$loop_ingroup_dial_prefix}{$dial_wait_seconds}{$Ndialstring}";
                    }
    
    #				$dial_ingroup_dialstring = "90009*$dial_ingroup" . "**$lead_id" . "**$agent_dialed_number" . "*$user" . "*$user" . "**1*$conf_exten";
    #				$dial_channel = "$local_DEF$dial_ingroup_dialstring$local_AMP$ext_context$Local_persist";
    
                    $dial_channel = "{$local_DEF}{$Ndialstring}{$local_AMP}{$ext_context}{$Local_persist}";
    
                    $dial_wait_seconds = '0';	# 1 digit only
                    $dial_ingroup_dialstring = "90009*{$dial_ingroup}" . "**{$lead_id}" . "**{$agent_dialed_number}" . "*{$user}" . "*{$user}" . "**1*{$conf_exten}";
                    $Ndialstring = "{$loop_ingroup_dial_prefix}{$dial_wait_seconds}{$dial_ingroup_dialstring}";
                }
    
                if ($CCID_on) {$CIDstring = "\"$MqueryCID$EAC\" <$CCID>";}
                else {$CIDstring = "$MqueryCID$EAC";}
    
                ### insert the call action into the vicidial_manager table to initiate the call
                #	$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $conf_exten','Context: $ext_context','Channel: $local_DEF$Local_out_prefix$phone_code$phone_number$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
                //$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $Ndialstring','Context: $ext_context','Channel: $dial_channel','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','$account','$variable','','');";
                $insertData = array(
                    'man_id' => '',
                    'uniqueid' => '',
                    'entry_date' => $NOW_TIME,
                    'status' => 'NEW',
                    'response' => 'N',
                    'server_ip' => $server_ip,
                    'channel' => '',
                    'action' => 'Originate',
                    'callerid' => $MqueryCID,
                    'cmd_line_b' => "Exten: $Ndialstring",
                    'cmd_line_c' => "Context: $ext_context",
                    'cmd_line_d' => "Channel: $dial_channel",
                    'cmd_line_e' => 'Priority: 1',
                    'cmd_line_f' => "Callerid: $CIDstring",
                    'cmd_line_g' => "Timeout: $Local_dial_timeout",
                    'cmd_line_h' => $account,
                    'cmd_line_i' => $variable,
                    'cmd_line_j' => '',
                    'cmd_line_k' => ''
                );
                $astDB->insert('vicidial_manager', $insertData);
    
                ### log outbound call in the dial log
                //$stmt = "INSERT INTO vicidial_dial_log SET caller_code='$MqueryCID',lead_id='$lead_id',server_ip='$server_ip',call_date='$NOW_TIME',extension='$Ndialstring',channel='$dial_channel', timeout='$Local_dial_timeout',outbound_cid='$CIDstring',context='$ext_context';";
                $insertData = array(
                    'caller_code' => $MqueryCID,
                    'lead_id' => $lead_id,
                    'server_ip' => $server_ip,
                    'call_date' => $NOW_TIME,
                    'extension' => $Ndialstring,
                    'channel' => $dial_channel,
                    'timeout' => $Local_dial_timeout,
                    'outbound_cid' => $CIDstring,
                    'context' => $ext_context
                );
                $rslt = $astDB->insert('vicidial_dial_log', $insertData);
    
                ### Skip logging and list overrides if dial in-group is used
                if (strlen($dial_ingroup) < 1) {
                    //$stmt = "INSERT INTO vicidial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) values('$server_ip','$campaign','XFER','$lead_id','$MqueryCID','$phone_code','$agent_dialed_number','$NOW_TIME','OUT')";
                    $insertData = array(
                        'server_ip' => $server_ip,
                        'campaign_id' => $campaign,
                        'status' => 'XFER',
                        'lead_id' => $lead_id,
                        'callerid' => $MqueryCID,
                        'phone_code' => $phone_code,
                        'phone_number' => $agent_dialed_number,
                        'call_time' => $NOW_TIME,
                        'call_type' => 'OUT'
                    );
                    $rslt = $astDB->insert('vicidial_auto_calls', $insertData);
                }
    
                ### update the agent status to INCALL in vicidial_live_agents
                //$stmt = "UPDATE vicidial_live_agents set status='INCALL',last_call_time='$NOW_TIME',callerid='$MqueryCID',lead_id='$lead_id',comments='MANUAL',calls_today='$calls_today',external_hangup=0,external_status='',external_pause='',external_dial='',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
                $updateData = array(
                    'status' => 'INCALL',
                    'last_call_time' => $NOW_TIME,
                    'callerid' => $MqueryCID,
                    'lead_id' => $lead_id,
                    'comments' => 'MANUAL',
                    'calls_today' => $calls_today,
                    'external_hangup' => 0,
                    'external_status' => '',
                    'external_pause' => '',
                    'external_dial' => '',
                    'last_state_change' => $NOW_TIME
                );
                $astDB->where('user', $user);
                $astDB->where('server_ip', $server_ip);
                $rslt = $astDB->update('vicidial_live_agents', $updateData);
    
                ### update calls_today count in vicidial_campaign_agents
                //$stmt = "UPDATE vicidial_campaign_agents set calls_today='$calls_today' where user='$user' and campaign_id='$campaign';";
                $astDB->where('user', $user);
                $astDB->where('campaign_id', $campaign);
                $rslt = $astDB->update('vicidial_campaign_agents', array('calls_today'=>$calls_today));
    
                if ($agent_dialed_number > 0) {
                    //$stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id,preset_name) values('$user','$NOW_TIME','$agent_dialed_type','$server_ip','$agent_dialed_number','$Ndialstring','$lead_id','$CCID','$RAWaccount','$preset_name')";
                    $insertData = array(
                        'user' => $user,
                        'call_date' => $NOW_TIME,
                        'call_type' => $agent_dialed_type,
                        'server_ip' => $server_ip,
                        'phone_number' => $agent_dialed_number,
                        'number_dialed' => $Ndialstring,
                        'lead_id' => $lead_id,
                        'callerid' => $CCID,
                        'group_alias_id' => $RAWaccount,
                        'preset_name' => $preset_name
                    );
                    $astDB->insert('user_call_log', $insertData);
                }
    
                ### Skip logging and list overrides if dial in-group is used
                if (strlen($dial_ingroup) < 1) {
                    $val_pause_epoch = 0;
                    $val_pause_sec = 0;
                    //$stmt = "SELECT pause_epoch FROM vicidial_agent_log where agent_log_id='$agent_log_id';";
                    $astDB->where('agent_log_id', $agent_log_id);
                    $rslt = $astDB->get('vicidial_agent_log', null, 'pause_epoch');
                    $vald_ct = $astDB->getRowCount();
                    if ($vald_ct > 0) {
                        $row = $rslt[0];
                        $val_pause_epoch =	$row['pause_epoch'];
                        $val_pause_sec = ($StarTtimE - $val_pause_epoch);
                    }
    
                    //$stmt="UPDATE vicidial_agent_log set pause_sec='$val_pause_sec',wait_epoch='$StarTtimE' where agent_log_id='$agent_log_id';";
                    $astDB->where('agent_log_id', $agent_log_id);
                    $rslt = $astDB->update('vicidial_agent_log', array('pause_sec'=>$val_pause_sec,'wait_epoch'=>$StarTtimE));
    
                    #############################################
                    ##### START QUEUEMETRICS LOGGING LOOKUP #####
                    //$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_pe_phone_append,queuemetrics_socket,queuemetrics_socket_url FROM system_settings;";
                    $rslt = $astDB->get('system_settings', null, 'enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_pe_phone_append,queuemetrics_socket,queuemetrics_socket_url');
                    $qm_conf_ct = $astDB->getRowCount();
                    if ($qm_conf_ct > 0) {
                        $row = $rslt[0];
                        $enable_queuemetrics_logging =	$row['enable_queuemetrics_logging'];
                        $queuemetrics_server_ip	=		$row['queuemetrics_server_ip'];
                        $queuemetrics_dbname =			$row['queuemetrics_dbname'];
                        $queuemetrics_login	=			$row['queuemetrics_login'];
                        $queuemetrics_pass =			$row['queuemetrics_pass'];
                        $queuemetrics_log_id =			$row['queuemetrics_log_id'];
                        $queuemetrics_pe_phone_append = $row['queuemetrics_pe_phone_append'];
                        $queuemetrics_socket =			$row['queuemetrics_socket'];
                        $queuemetrics_socket_url =		$row['queuemetrics_socket_url'];
                    }
                    ##### END QUEUEMETRICS LOGGING LOOKUP #####
                    ###########################################
    
                    if ($enable_queuemetrics_logging > 0) {
                        $data4SQL = '';
                        $data4SS = '';
                        //$stmt="SELECT queuemetrics_phone_environment FROM vicidial_campaigns where campaign_id='$campaign' and queuemetrics_phone_environment!='';";
                        $astDB->where('campaign_id', $campaign);
                        $astDB->where('queuemetrics_phone_environment', '', '!=');
                        $rslt = $astDB->get('vicidial_campaigns', null, 'queuemetrics_phone_environment');
                        $cqpe_ct = $astDB->getRowCount();
                        if ($cqpe_ct > 0) {
                            $pe_append = '';
                            $row = $rslt[0];
                            if ( ($queuemetrics_pe_phone_append > 0) and (strlen($row['queuemetrics_phone_environment']) > 0) )
                                {$pe_append = "-$qm_extension";}
                            $data4SQL = ",data4='{$row['queuemetrics_phone_environment']}{$pe_append}'";
                            $data4SS = "&data4={$row['queuemetrics_phone_environment']}{$pe_append}";
                        }
    
                        $linkB = new MySQLiDB("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass", "$queuemetrics_dbname");
    
                        # UNPAUSEALL
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$user',verb='UNPAUSEALL',serverid='$queuemetrics_log_id' $data4SQL;";
                        $rslt = $linkB->rawQuery($stmt);
                        $affected_rows = $linkB->getRowCount();
    
                        # CALLOUTBOUND (formerly ENTERQUEUE)
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$MqueryCID',queue='$campaign',agent='NONE',verb='CALLOUTBOUND',data2='$agent_dialed_number',serverid='$queuemetrics_log_id' $data4SQL;";
                        $rslt = $linkB->rawQuery($stmt);
                        $affected_rows = $linkB->getRowCount();
    
                        # CONNECT
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$MqueryCID',queue='$campaign',agent='Agent/$user',verb='CONNECT',data1='0',serverid='$queuemetrics_log_id' $data4SQL;";
                        $rslt = $linkB->rawQuery($stmt);
                        $affected_rows = $linkB->getRowCount();
    
                        $linkB->__destruct();
    
                        if ( ($queuemetrics_socket == 'CONNECT_COMPLETE') and (strlen($queuemetrics_socket_url) > 10) ) {
                            $socket_send_data_begin = '?';
                            $socket_send_data = "time_id=$StarTtimE&call_id=$MqueryCID&queue=$campaign&agent=Agent/$user&verb=CONNECT&data1=0$data4SS";
                            if (preg_match("/\?/", $queuemetrics_socket_url))
                                {$socket_send_data_begin = '&';}
                            ### send queue_log data to the queuemetrics_socket_url ###
                            $SCUfile = file("$queuemetrics_socket_url$socket_send_data_begin$socket_send_data");
                        }
                    }
                }
    
                ### Check for List ID override settings
                $VDCL_xferconf_a_number = '';
                $VDCL_xferconf_b_number = '';
                $VDCL_xferconf_c_number = '';
                $VDCL_xferconf_d_number = '';
                $VDCL_xferconf_e_number = '';
                //$stmt = "SELECT xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_campaigns where campaign_id='$campaign';";
                $astDB->where('campaign_id', $campaign);
                $rslt = $astDB->get('vicidial_campaigns', null, 'xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number');
                $VC_preset_ct = $astDB->getRowCount();
                if ($VC_preset_ct > 0) {
                    $row = $rslt[0];
                    $VDCL_xferconf_a_number =	(is_null($row['xferconf_a_number'])) ? '' : $row['xferconf_a_number'];
                    $VDCL_xferconf_b_number =	(is_null($row['xferconf_b_number'])) ? '' : $row['xferconf_b_number'];
                    $VDCL_xferconf_c_number =	(is_null($row['xferconf_c_number'])) ? '' : $row['xferconf_c_number'];
                    $VDCL_xferconf_d_number =	(is_null($row['xferconf_d_number'])) ? '' : $row['xferconf_d_number'];
                    $VDCL_xferconf_e_number =	(is_null($row['xferconf_e_number'])) ? '' : $row['xferconf_e_number'];
                }
    
                ##### check if system is set to generate logfile for transfers
                //$stmt="SELECT enable_agc_xfer_log FROM system_settings;";
                $rslt = $astDB->get('system_settings', null, 'enable_agc_xfer_log');
                $enable_agc_xfer_log_ct = $astDB->getRowCount();
                if ($enable_agc_xfer_log_ct > 0) {
                    $row = $rslt[0];
                    $enable_agc_xfer_log = $row['enable_agc_xfer_log'];
                }
                $WeBRooTWritablE = 0;
                if ( ($WeBRooTWritablE > 0) and ($enable_agc_xfer_log > 0) ) {
                    #	DATETIME|campaign|lead_id|phone_number|user|type
                    #	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|M
                    $fp = fopen ("./xfer_log.txt", "a");
                    fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$agent_dialed_number|$user|M|$MqueryCID||$province\n");
                    fclose($fp);
                }
            }
    
    
            ##### find if script contains recording fields
            //$stmt = "SELECT count(*) AS cnt FROM vicidial_lists WHERE list_id='$list_id' and agent_script_override!='' and agent_script_override IS NOT NULL and agent_script_override!='NONE';";
            $rslt = $astDB->rawQuery("SELECT count(*) AS cnt FROM vicidial_lists WHERE list_id='$list_id' and agent_script_override!='' and agent_script_override IS NOT NULL and agent_script_override!='NONE';");
            $vls_vc_ct = $astDB->getRowCount();
            if ($vls_vc_ct > 0) {
                $row = $rslt[0];
                if ($row['cnt'] > 0) {
                    $script_recording_delay = 0;
                    ##### find if script contains recording fields
                    $stmt = "SELECT count(*) AS cnt FROM vicidial_scripts vs,vicidial_lists vls WHERE list_id='$list_id' and vs.script_id=vls.agent_script_override and script_text LIKE \"%--A--recording_%\";";
                    $rslt = $astDB->rawQuery($stmt);
                    $vs_vc_ct = $astDB->getRowCount();
                    if ($vs_vc_ct > 0) {
                        $row = $rslt[0];
                        $script_recording_delay = $row['cnt'];
                    }
                }
            }
    
            if (strlen($list_id) > 0) {
                //$stmt = "SELECT xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_lists where list_id='$list_id';";
                $astDB->where('list_id', $list_id);
                $rslt = $astDB->get('vicidial_lists', null, 'xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number');
                $VDIG_preset_ct = $astDB->getRowCount();
                if ($VDIG_preset_ct > 0) {
                    $row = $rslt[0];
                    if (strlen($row['xferconf_a_number']) > 0)
                        {$VDCL_xferconf_a_number =	$row['xferconf_a_number'];}
                    if (strlen($row['xferconf_b_number']) > 0)
                        {$VDCL_xferconf_b_number =	$row['xferconf_b_number'];}
                    if (strlen($row['xferconf_c_number']) > 0)
                        {$VDCL_xferconf_c_number =	$row['xferconf_c_number'];}
                    if (strlen($row['xferconf_d_number']) > 0)
                        {$VDCL_xferconf_d_number =	$row['xferconf_d_number'];}
                    if (strlen($row['xferconf_e_number']) > 0)
                        {$VDCL_xferconf_e_number =	$row['xferconf_e_number'];}
                }
                
                $custom_field_names = '|';
                $custom_field_names_SQL = '';
                $custom_field_values = '----------';
                $custom_field_types = '|';
                ### find the names of all custom fields, if any
                $stmt = "SELECT field_label,field_type FROM vicidial_lists_fields where list_id='$entry_list_id' and field_type NOT IN('SCRIPT','DISPLAY') and field_label NOT IN('vendor_lead_code','source_id','list_id','gmt_offset_now','called_since_last_reset','phone_code','phone_number','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','gender','date_of_birth','alt_phone','email','security_phrase','comments','called_count','last_local_call_time','rank','owner');";
                $rslt = $astDB->rawQuery($stmt);
                $cffn_ct = $astDB->getRowCount();
                $d = 0;
                while ($cffn_ct > $d) {
                    $row = $rslt[$d];
                    $custom_field_names .=	"{$row['field_label']}|";
                    $custom_field_names_SQL .=	"{$row['field_label']},";
                    $custom_field_types .=	"{$row['field_type']}|";
                    $custom_field_values .=	"----------";
                    $custom_field[$d] = $row['field_label'];
                    $d++;
                }
                if ($cffn_ct > 0) {
                    $custom_field_names_SQL = preg_replace("/.$/i", "", $custom_field_names_SQL);
                    ### find the values of the named custom fields
                    //$stmt = "SELECT $custom_field_names_SQL FROM custom_$entry_list_id where lead_id='$lead_id' limit 1;";
                    $astDB->where('lead_id', $lead_id);
                    $rslt = $astDB->getOne("custom_{$entry_list_id}", "$custom_field_names_SQL");
                    $cffv_ct = $astDB->getRowCount();
                    if ($cffv_ct > 0) {
                        $row = $rslt;
                        $custom_field_values = '----------';
                        $custom_field_ct = count($custom_field);
                        $d = 0;
                        while ($custom_field_ct > $d) {
                            $idx = $custom_field[$d];
                            $custom_field_values .=	"$row[$idx]----------";
                            $d++;
                        }
                        $custom_field_values = preg_replace("/\n/", " ", $custom_field_values);
                        $custom_field_values = preg_replace("/\r/", "", $custom_field_values);
                    }
                }
            }
    
    
            $comments = preg_replace("/\r/i", '', $comments);
            $comments = preg_replace("/\n/i", '!N!', $comments);
    
            $address1 = preg_replace("/\r/i", '', $address1);
            $address1 = preg_replace("/\n/i", '!N!', $address1);
    
            $address2 = preg_replace("/\r/i", '', $address2);
            $address2 = preg_replace("/\n/i", '!N!', $address2);
            
            $astDB->where('lead_id', $lead_id);
            $astDB->orderBy('notesid', 'desc');
            $CNotes = $astDB->getOne('vicidial_call_notes', 'call_notes');
            $call_notes = (!is_null($CNotes['call_notes'])) ? $CNotes['call_notes'] : '';
            
            $LeaD_InfO = array(
                'MqueryCID' => (isset($MqueryCID)) ? $MqueryCID : "",
                'lead_id' => $lead_id,
                'status' => $dispo,
                'user' => $tsr,
                'vendor_lead_code' => $vendor_id,
                'list_id' => $list_id,
                'gmt_offset_now' => $gmt_offset_now,
                'phone_code' => $phone_code,
                'phone_number' => $phone_number,
                'title' => $title,
                'first_name' => $first_name,
                'middle_initial' => $middle_initial,
                'last_name' => $last_name,
                'address1' => $address1,
                'address2' => $address2,
                'address3' => $address3,
                'city' => $city,
                'state' => $state,
                'province' => $province,
                'postal_code' => $postal_code,
                'country_code' => $country_code,
                'gender' => $gender,
                'date_of_birth' => ($date_of_birth != "0000-00-00") ? "$date_of_birth" : "",
                'alt_phone' => $alt_phone,
                'email' => $email,
                'security_phrase' => $security,
                'comments' => $comments,
                'called_count' => $called_count,
                'CBentry_time' => $CBentry_time,
                'CBcallback_time' => $CBcallback_time,
                'CBuser' => $CBuser,
                'CBcomments' => $CBcomments,
                'agent_dialed_number' => $agent_dialed_number,
                'agent_dialed_type' => $agent_dialed_type,
                'source_id' => $source_id,
                'rank' => $rank,
                'owner' => $owner,
                'Call_Script_ID' => '',
                'script_recording_delay' => $script_recording_delay,
                'xferconf_a_number' => (isset($VDCL_xferconf_a_number)) ? $VDCL_xferconf_a_number : "",
                'xferconf_b_number' => (isset($VDCL_xferconf_b_number)) ? $VDCL_xferconf_b_number : "",
                'xferconf_c_number' => (isset($VDCL_xferconf_c_number)) ? $VDCL_xferconf_c_number : "",
                'xferconf_d_number' => (isset($VDCL_xferconf_d_number)) ? $VDCL_xferconf_d_number : "",
                'xferconf_e_number' => (isset($VDCL_xferconf_e_number)) ? $VDCL_xferconf_e_number : "",
                'entry_list_id' => $entry_list_id,
                'custom_field_names' => $custom_field_names,
                'custom_field_values' => $custom_field_values,
                'custom_field_types' => $custom_field_types,
                'web_form_address' => $LISTweb_form_address,
                'web_form_address_two' => $LISTweb_form_address_two,
                'post_phone_time_diff_alert_message' => $post_phone_time_diff_alert_message,
                'ACcount' => $ACcount,
                'ACcomments' => $ACcomments,
                'call_notes' => $call_notes,
                'CBcommentsALL' => $CBcommentsALL
            );
    
            $APIResult = array( "result" => "success", "data" => $LeaD_InfO );
        } else {
            if ($errmsg < 1) {
                $message = "HOPPER EMPTY";
            }
            $APIResult = array( "result" => "error", "message" => $message );
        }
    }
} else {
    $message = "SIP exten '{$phone_login}' is NOT connected";
    if (strlen($phone_login) < 1) {
        $message = "User '$user' does NOT have any phone extension assigned.";
    }
    $APIResult = array( "result" => "error", "message" => $message );
}
?>
