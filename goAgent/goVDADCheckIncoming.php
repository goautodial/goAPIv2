<?php
 /**
 * @file 		goVDADCheckIncoming.php
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

$is_logged_in = check_agent_login($astDB, $goUser);

$agent = get_settings('user', $astDB, $goUser);
$campaign_settings = get_settings('campaign', $astDB, $campaign);
$system_settings = get_settings('system', $astDB);
$phone_settings = get_settings('phone', $astDB, $agent->phone_login, $agent->phone_pass);

if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goAgentLogID'])) { $agent_log_id = $astDB->escape($_GET['goAgentLogID']); }
    else if (isset($_POST['goAgentLogID'])) { $agent_log_id = $astDB->escape($_POST['goAgentLogID']); }
if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }

$user = $agent->user;
$server_ip = (strlen($server_ip) > 0) ? $server_ip : $phone_settings->server_ip;

$VDCL_ingroup_recording_override = '';
$VDCL_ingroup_rec_filename = '';
$Ctype = 'A';
$MT[0] = '';
$row = '';
$rowx = '';
$channel_live = 1;
$alt_phone_code = '';
$alt_phone_number = '';
$alt_phone_note = '';
$alt_phone_active = '';
$alt_phone_count = '';
$INclosecallid = '';
$INxfercallid = '';
$dataOutput1 = array();
$dataOutput2 = array();
$dataOutput3 = array();
$dataOutput4 = array();
$LeaD_InfO   = array();
$ACcount = '';
$ACcomments = '';

if ($is_logged_in) {
    ### grab the call and lead info from the vicidial_live_agents table
    //$stmt = "SELECT lead_id,uniqueid,callerid,channel,call_server_ip,comments FROM vicidial_live_agents where server_ip = '$server_ip' and user='$user' and campaign_id='$campaign' and status='QUEUE';";
    $astDB->where('server_ip', $server_ip);
    $astDB->where('user', $user);
    $astDB->where('campaign_id', $campaign);
    $astDB->where('status', 'QUEUE');
    $rslt = $astDB->get('vicidial_live_agents', null, 'lead_id,uniqueid,callerid,channel,call_server_ip,comments');
    $queue_leadID_ct = $astDB->getRowCount();
    
    if ($queue_leadID_ct > 0) {
        $row = $rslt[0];
        $lead_id        = $row['lead_id'];
        $uniqueid	    = $row['uniqueid'];
        $callerid	    = $row['callerid'];
        $channel	    = $row['channel'];
        $call_server_ip	= $row['call_server_ip'];
        $VLAcomments    = $row['comments'];

        if (strlen($call_server_ip) < 7) {$call_server_ip = $server_ip;}
        //echo "1\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . "|\n";
        $dataOutput1 = array(
            'has_call' => 1,
            'lead_id' => $lead_id,
            'uniqueid' => $uniqueid,
            'callerid' => $callerid,
            'channel' => $channel,
            'call_server_ip' => $call_server_ip
        );

        ##### grab number of calls today in this campaign and increment
        //$stmt="SELECT calls_today FROM vicidial_live_agents WHERE user='$user' and campaign_id='$campaign';";
        $astDB->where('user', $user);
        $astDB->where('campaign_id', $campaign);
        $rslt = $astDB->get('vicidial_live_agents', null, 'calls_today');
        $vla_cc_ct = $astDB->getRowCount();
        if ($vla_cc_ct > 0) {
            $calls_today = $vla_cc_ct;
        } else {
            $calls_today = 0;
        }
        $calls_today++;

        ### update the agent status to INCALL in vicidial_live_agents
        //$stmt = "UPDATE vicidial_live_agents set status='INCALL',last_call_time='$NOW_TIME',calls_today='$calls_today',external_hangup=0,external_status='',external_pause='',external_dial='',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
        $updateData = array(
            'status' => 'INCALL',
            'last_call_time' => $NOW_TIME,
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
        $errno = $astDB->getLastError();
        $retry_count = 0;
        while ( (count($errno) > 3) and ($retry_count < 9) ) {
            $astDB->where('user', $user);
            $astDB->where('server_ip', $server_ip);
            $rslt = $astDB->update('vicidial_live_agents', $updateData);
            $errno = $astDB->getLastError();
            $retry_count++;
        }

        //$stmt = "UPDATE vicidial_campaign_agents set calls_today='$calls_today' where user='$user' and campaign_id='$campaign';";
        $astDB->where('user', $user);
        $astDB->where('campain_id', $campaign);
        $rslt = $astDB->update('vicidial_campaign_agents', array( 'calls_today' => $calls_today ));

        ##### grab the data from vicidial_list for the lead_id
        //$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
        $astDB->where('lead_id', $lead_id);
        $rslt = $astDB->getOne('vicidial_list', 'lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id');
        $list_lead_ct = $astDB->getRowCount();
        
        if ($list_lead_ct > 0) {
            $row = $rslt;
        #	$lead_id		= trim("{$row['lead_id']}");
            $dispo			= trim("{$row['status']}");
            $tsr			= trim("{$row['user']}");
            $vendor_id		= trim("{$row['vendor_lead_code']}");
            $source_id		= trim("{$row['source_id']}");
            $list_id		= trim("{$row['list_id']}");
            $gmt_offset_now	= trim("{$row['gmt_offset_now']}");
            $phone_code		= trim("{$row['phone_code']}");
            $phone_number	= trim("{$row['phone_number']}");
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
            $audit_comments_active = audit_comments_active($astDB, $list_id, $format, $user, $NOW_TIME, $server_ip, $session_name);
            if ($audit_comments_active) {
                get_audited_comments($astDB, $lead_id, $format, $user, $NOW_TIME, $server_ip, $session_name);
            }
            $ACcomments = strip_tags(htmlentities($ACcomments));
            $ACcomments = preg_replace("/\r/i", '', $ACcomments);
            $ACcomments = preg_replace("/\n/i", '!N', $ACcomments);
        }

        ##### if lead is a callback, grab the callback comments
        $CBentry_time = '';
        $CBcallback_time = '';
        $CBuser = '';
        $CBcomments = '';
        $CBstatus = 0;

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
            $rslt = $astDB->where('vicidial_campaign_statuses');
            $cb_record_ct = $astDB->getRowCount();
            if ($cb_record_ct > 0) {
                $CBstatus = $cb_record_ct;
            }
        }
        if ( ($CBstatus > 0) or ($dispo == 'CBHOLD') ) {
            //$stmt="SELECT entry_time,callback_time,user,comments FROM vicidial_callbacks where lead_id='$lead_id' order by callback_id desc LIMIT 1;";
            $astDB->where('lead_id', $lead_id);
            $astDB->orderBy('callback_id', 'desc');
            $rslt = $astDB->getOne('vicidial_callbacks', 'entry_time,callback_time,user,comments');
            $cb_record_ct = $astDB->getRowCount();
            if ($cb_record_ct > 0) {
                $row = $rslt;
                $CBentry_time =		trim("{$row['entry_time']}");
                $CBcallback_time =	trim("{$row['callback_time']}");
                $CBuser =			trim("{$row['user']}");
                $CBcomments =		trim("{$row['comments']}");
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
        //$stmt="SELECT owner_populate FROM vicidial_campaigns where campaign_id='$campaign';";
        $astDB->where('campaign_id', $campaign);
        $rslt = $astDB->getOne('vicidial_campaigns', 'owner_populate');
        $camp_op_ct = $astDB->getRowCount();
        if ($camp_op_ct > 0) {
            $row = $rslt;
            $owner_populate = $row['owner_populate'];
        }
        
        $ownerSQL = '';
        if ( ($owner_populate == 'ENABLED') and ( (strlen($owner) < 1) or ($owner == 'NULL') ) ) {
            $ownerArray = array( 'owner' => $user );
            $owner = $user;
        }

        ### update the lead status to INCALL
        $updateData = array(
            'status' => 'INCALL',
            'user' => $user
        );
        if (is_array($ownerArray)) {
            $updateData = array_merge($updateData, $ownerArray);
        }
        //$stmt = "UPDATE vicidial_list set status='INCALL', user='$user' $ownerSQL where lead_id='$lead_id';";
        $astDB->where('lead_id', $lead_id);
        $rslt = $astDB->update('vicidial_list', $updateData);

        ### gather custom_call_id from vicidial_log_extended table
        $custom_call_id = '';
        //$stmt="SELECT custom_call_id FROM vicidial_log_extended where uniqueid='$uniqueid';";
        $astDB->where('uniqueid', $uniqueid);
        $rslt = $astDB->get('vicidial_log_extended', null, 'custom_call_id');
        $vle_record_ct = $astDB->getRowCount();
        if ($vle_record_ct > 0) {
            $row = $rslt[0];
            $custom_call_id = $row['custom_call_id'];
        }

        ### gather user_group and full_name of agent
        $user_group = '';
        //$stmt="SELECT user_group,full_name FROM vicidial_users where user='$user' LIMIT 1;";
        $astDB->where('user', $user);
        $rslt = $astDB->getOne('vicidial_users', 'user_group,full_name');
        $ug_record_ct = $astDB->getRowCount();
        if ($ug_record_ct > 0) {
            $row = $rslt;
            $user_group = trim("{$row['user_group']}");
            $fullname = $row['full_name'];
        }

        //$stmt = "SELECT campaign_id,phone_number,alt_dial,call_type from vicidial_auto_calls where callerid = '$callerid' order by call_time desc limit 1;";
        $astDB->where('callerid', $callerid);
        $astDB->orderBy('call_time', 'desc');
        $rslt = $astDB->get('vicidial_auto_calls', null, 'campaign_id,phone_number,alt_dial,call_type');
        $VDAC_cid_ct = $astDB->getRowCount();
        if ($VDAC_cid_ct > 0) {
            $row = $rslt[0];
            $VDADchannel_group	= $row['campaign_id'];
            $dialed_number		= $row['phone_number'];
            $dialed_label		= $row['alt_dial'];
            $call_type			= $row['call_type'];
            if ( ($dialed_number != $phone_number) and (strlen($dialed_label) < 3) ) {
                if ($dialed_number != $alt_phone) {
                    if ($dialed_number != $address3) {
                        $dialed_label = 'X1';
                        //$stmt = "SELECT alt_phone_count from vicidial_list_alt_phones where lead_id='$lead_id' and phone_number = '$dialed_number' order by alt_phone_count limit 1;";
                        $astDB->where('lead_id', $lead_id);
                        $astDB->where('phone_number', $dialed_number);
                        $astDB->orderBy('alt_phone_count');
                        $rslt = $astDB->getOne('vicidial_list_alt_phones', 'alt_phone_count');
                        $VDAP_cid_ct = $astDB->getRowCount();
                        if ($VDAP_cid_ct > 0) {
                            $row = $rslt;
                            $Xalt_phone_count = $row['alt_phone_count'];

                            //$stmt = "SELECT count(*) from vicidial_list_alt_phones where lead_id='$lead_id';";
                            $astDB->where('lead_id', $lead_id);
                            $rslt = $astDB->get('vicidial_list_alt_phones');
                            $VDAPct_cid_ct = $astDB->getRowCount();
                            if ($VDAPct_cid_ct > 0) {
                                $COUNTalt_phone_count = $VDAPct_cid_ct;

                                if ($COUNTalt_phone_count <= $Xalt_phone_count)
                                    {$dialed_label = 'XLAST';}
                                else
                                    {$dialed_label = "X$Xalt_phone_count";}
                            }
                        }
                    } else {
                        $dialed_label = 'ADDR3';
                    }
                } else {
                    $dialed_label = 'ALT';
                }
            }
        } else {
            $dialed_number = $phone_number;
            $dialed_label = 'MAIN';
            if (preg_match('/^M|^V/',$callerid)) {
                $call_type = 'OUT';
                $VDADchannel_group = $campaign;
            } else {
                $call_type = 'IN';
                //$stmt = "SELECT campaign_id,closecallid,xfercallid from vicidial_closer_log where lead_id = '$lead_id' order by call_date desc limit 1;";
                $astDB->where('lead_id', $lead_id);
                $astDB->orderBy('call_date', 'desc');
                $rslt = $astDB->getOne('vicidial_closer_log', 'campaign_id,closercallid,xfercallid');
                $VDCL_mvac_ct = $astDB->getRowCount();
                if ($VDCL_mvac_ct > 0) {
                    $row = $rslt;
                    $VDADchannel_group = $row['campaign_id'];
                    $INclosecallid = $row['closercallid'];
                    $INxfercallid = $row['xfercallid'];
                }
            }
        }

        if ( ($call_type == 'OUT') or ($call_type == 'OUTBALANCE') ) {
            //$stmt = "UPDATE vicidial_log set user='$user', comments='AUTO', list_id='$list_id', status='INCALL', user_group='$user_group' where lead_id='$lead_id' and uniqueid='$uniqueid';";
            $updateData = array(
                'user' => $user,
                'comments' => 'AUTO',
                'list_id' => $list_id,
                'status' => 'INCALL',
                'user_group' => $user_group
            );
            $astDB->where('lead_id', $lead_id);
            $astDB->where('uniqueid', $uniqueid);
            $rslt = $astDB->update('vicidial_log', $updateData);

            $script_recording_delay = 0;
            ##### grab number of calls today in this campaign and increment
            //$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$campaign' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";";
            $rslt = $astDB->rawQuery("SELECT * FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$campaign' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";");
            $vs_vc_ct = $astDB->getRowCount();
            if ($vs_vc_ct > 0) {
                $script_recording_delay = $vs_vc_ct;
            }

            //$stmt = "SELECT campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_xfer_group,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,timer_action,timer_action_message,timer_action_seconds,timer_action_destination from vicidial_campaigns where campaign_id='$campaign';";
            $astDB->where('campaign_id', $campaign);
            $rslt = $astDB->get('vicidial_campaigns', null, 'campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_xfer_group,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,timer_action,timer_action_message,timer_action_seconds,timer_action_destination');
            $VDIG_cid_ct = $astDB->getRowCount();
            if ($VDIG_cid_ct > 0) {
                $row = $rslt[0];
                $VDCL_campaign_script =			$row['campaign_script'];
                $VDCL_get_call_launch =			$row['get_call_launch'];
                $VDCL_xferconf_a_dtmf =			$row['xferconf_a_dtmf'];
                $VDCL_xferconf_a_number =		$row['xferconf_a_number'];
                $VDCL_xferconf_b_dtmf =			$row['xferconf_b_dtmf'];
                $VDCL_xferconf_b_number =		$row['xferconf_b_number'];
                $VDCL_default_xfer_group =		$row['default_xfer_group'];
                if (strlen($VDCL_default_xfer_group) < 2) {$VDCL_default_xfer_group = 'X';}
                $VDCL_start_call_url =			$row['start_call_url'];
                $VDCL_dispo_call_url =			$row['dispo_call_url'];
                $VDCL_xferconf_c_number =		$row['xferconf_c_number'];
                $VDCL_xferconf_d_number =		$row['xferconf_d_number'];
                $VDCL_xferconf_e_number =		$row['xferconf_e_number'];
                $VDCL_timer_action =			$row['timer_action'];
                $VDCL_timer_action_message =	$row['timer_action_message'];
                $VDCL_timer_action_seconds =	$row['timer_action_seconds'];
                $VDCL_timer_action_destination =$row['timer_action_destination'];
            }

            ### Check for List ID override settings
            if (strlen($list_id)>0) {
                //$stmt = "SELECT xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_lists where list_id='$list_id';";
                $astDB->where('list_id', $list_id);
                $rslt = $astDB->get('vicidial_lists', null, 'xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number');
                $VDIG_xferOR_ct = $astDB->getRowCount();
                if ($VDIG_xferOR_ct > 0) {
                    $row = $rslt[0];
                    if (strlen($row['xferconf_a_number']) > 0)
                        {$VDCL_xferconf_a_number = $row['xferconf_a_number'];}
                    if (strlen($row['xferconf_b_number']) > 0)
                        {$VDCL_xferconf_b_number = $row['xferconf_b_number'];}
                    if (strlen($row['xferconf_c_number']) > 0)
                        {$VDCL_xferconf_c_number = $row['xferconf_c_number'];}
                    if (strlen($row['xferconf_d_number']) > 0)
                        {$VDCL_xferconf_d_number = $row['xferconf_d_number'];}
                    if (strlen($row['xferconf_e_number']) > 0)
                        {$VDCL_xferconf_e_number = $row['xferconf_e_number'];}
                }
            }

            //echo "|||||$VDCL_campaign_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|
            //$VDCL_xferconf_b_number|$VDCL_default_xfer_group|X|X|||||$VDCL_timer_action|$VDCL_timer_action_message|$VDCL_timer_action_seconds|
            //$VDCL_xferconf_c_number|$VDCL_xferconf_d_number|$VDCL_xferconf_e_number||||$VDCL_timer_action_destination||||||\n|\n";
            $dataOutput2 = array(
                'group_web' => '',
                'group_name' => '',
                'group_color' => '',
                'fronter_display' => '',
                'channel_group' => '',
                'campaign_script' => $VDCL_campaign_script,
                'get_call_launch' => $VDCL_get_call_launch,
                'xferconf_a_dtmf' => $VDCL_xferconf_a_dtmf,
                'xferconf_a_number' => $VDCL_xferconf_a_number,
                'xferconf_b_dtmf' => $VDCL_xferconf_b_dtmf,
                'xferconf_b_number' => $VDCL_xferconf_b_number,
                'xferconf_c_number' => $VDCL_xferconf_c_number,
                'xferconf_d_number' => $VDCL_xferconf_d_number,
                'xferconf_e_number' => $VDCL_xferconf_e_number,
                'default_xfer_group' => $VDCL_default_xfer_group,
                'ingroup_recording_override' => 'X',
                'ingroup_rec_filename' => 'X',
                'default_group_alias' => '',
                'caller_id_number' => '',
                'group_web_vars' => '',
                'group_web_two' => '',
                'timer_action' => $VDCL_timer_action,
                'timer_action_message' => $VDCL_timer_action_message,
                'timer_action_seconds' => $VDCL_timer_action_seconds,
                'timer_action_destination' => $VDCL_timer_action_destination,
                'uniqueid_status_display' => '',
                'custom_call_id' => '',
                'uniqueid_status_prefix' => '',
                'did_id' => '',
                'did_extension' => '',
                'did_pattern' => '',
                'did_description' => '',
                'closecallid' => '',
                'xfercallid' => '',
                'fronter_full_name' => ''
            );
            
            if (preg_match('/X/', $dialed_label)) {
                if (preg_match('/LAST/', $dialed_label)) {
                    //$stmt = "SELECT phone_code,phone_number,alt_phone_note,active,alt_phone_count FROM vicidial_list_alt_phones where lead_id='$lead_id' order by alt_phone_count desc limit 1;";
                    $astDB->where('lead_id', $lead_id);
                    $astDB->orderBy('alt_phone_count', 'desc');
                    $limit = 1;
                } else {
                    $Talt_dial = preg_replace("/[^0-9]/","",$dialed_label);
                    //$stmt = "SELECT phone_code,phone_number,alt_phone_note,active,alt_phone_count FROM vicidial_list_alt_phones where lead_id='$lead_id' and alt_phone_count='$Talt_dial';";
                    $astDB->where('lead_id', $lead_id);
                    $astDB->where('alt_phone_count', $Talt_dial);
                    $limit = null;
                }

                $rslt = $astDB->get('vicidial_list_alt_phones', $limit, 'phone_code,phone_number,alt_phone_note,active,alt_phone_count');
                $VLAP_ct = $astDB->getRowCount();
                if ($VLAP_ct > 0) {
                    $row = $rslt[0];
                    $alt_phone_code	=	$row['phone_code'];
                    $alt_phone_number = $row['phone_number'];
                    $alt_phone_note =	$row['alt_phone_note'];
                    $alt_phone_active = $row['active'];
                    $alt_phone_count =	$row['alt_phone_count'];
                }
            }
        } else {
            ### update the vicidial_closer_log user to INCALL
            //$stmt = "UPDATE vicidial_closer_log SET user='$user', comments='AUTO', list_id='$list_id', status='INCALL', user_group='$user_group' WHERE lead_id='$lead_id' ORDER BY closecallid DESC LIMIT 1;";
            $rslt = $astDB->rawQuery("UPDATE vicidial_closer_log SET user='$user', comments='AUTO', list_id='$list_id', status='INCALL', user_group='$user_group' WHERE lead_id='$lead_id' ORDER BY closecallid DESC LIMIT 1;");

            if (strlen($closecallid)<1) {
                //$stmt = "SELECT closecallid,xfercallid from vicidial_closer_log where lead_id='$lead_id' and user='$user' and list_id='$list_id' order by call_date desc limit 1;";
                $astDB->where('lead_id', $lead_id);
                $astDB->where('user', $user);
                $astDB->where('list_id', $list_id);
                $astDB->orderBy('call_date', 'desc');
                $rslt = $astDB->getOne('vicidial_closer_log', 'closercallid,xfercallid');
                $VDCL_mvac_ct = $astDB->getRowCount();
                if ($VDCL_mvac_ct > 0) {
                    $row = $rslt;
                    $INclosecallid =    $row['closercallid'];
                    $INxfercallid =     $row['xfercallid'];
                }
            }

            //$stmt = "SELECT count(*) from vicidial_log where lead_id='$lead_id' and uniqueid='$uniqueid';";
            $astDB->where('lead_id', $lead_id);
            $astDB->where('uniqueid', $uniqueid);
            $rslt = $astDB->get('vicidial_log');
            $VDL_cid_ct = $astDB->getRowCount();
            if ($VDL_cid_ct > 0) {
                $VDCL_front_VDlog = $VDL_cid_ct;
            }

            $script_recording_delay = 0;
            ##### find if script contains recording fields
            //$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_inbound_groups vig WHERE group_id='$VDADchannel_group' and vs.script_id=vig.ingroup_script and script_text LIKE \"%--A--recording_%\";";
            $rslt = $astDB->rawQuery("SELECT count(*) FROM vicidial_scripts vs,vicidial_inbound_groups vig WHERE group_id='$VDADchannel_group' and vs.script_id=vig.ingroup_script and script_text LIKE \"%--A--recording_%\";");
            $vs_vc_ct = $astDB->getRowCount();
            if ($vs_vc_ct > 0) {
                $script_recording_delay = $vs_vc_ct;
            }

            //$stmt = "SELECT group_name,group_color,web_form_address,fronter_display,ingroup_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_xfer_group,ingroup_recording_override,ingroup_rec_filename,default_group_alias,web_form_address_two,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,uniqueid_status_display,uniqueid_status_prefix,timer_action_destination from vicidial_inbound_groups where group_id='$VDADchannel_group';";
            $astDB->where('group_id', $VDADchannel_group);
            $rslt = $astDB->get('vicidial_inbound_groups', null, 'group_name,group_color,web_form_address,fronter_display,ingroup_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_xfer_group,ingroup_recording_override,ingroup_rec_filename,default_group_alias,web_form_address_two,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,uniqueid_status_display,uniqueid_status_prefix,timer_action_destination');
            $VDIG_cid_ct = $astDB->getRowCount();
            if ($VDIG_cid_ct > 0) {
                $row = $rslt[0];
                $VDCL_group_name =					$row['group_name'];
                $VDCL_group_color =					$row['group_color'];
                $VDCL_group_web	=					stripslashes($row['web_form_address']);
                $VDCL_fronter_display =				$row['fronter_display'];
                $VDCL_ingroup_script =				$row['ingroup_script'];
                $VDCL_get_call_launch =				$row['get_call_launch'];
                $VDCL_xferconf_a_dtmf =				$row['xferconf_a_dtmf'];
                $VDCL_xferconf_a_number =			$row['xferconf_a_number'];
                $VDCL_xferconf_b_dtmf =				$row['xferconf_b_dtmf'];
                $VDCL_xferconf_b_number =			$row['xferconf_b_number'];
                $VDCL_default_xfer_group =			$row['default_xfer_group'];
                $VDCL_ingroup_recording_override =	$row['ingroup_recording_override'];
                $VDCL_ingroup_rec_filename =		$row['ingroup_rec_filename'];
                $VDCL_default_group_alias =			$row['default_group_alias'];
                $VDCL_group_web_two =               stripslashes($row['web_form_address_two']);
                $VDCL_timer_action =				$row['timer_action'];
                $VDCL_timer_action_message =		$row['timer_action_message'];
                $VDCL_timer_action_seconds =		$row['timer_action_seconds'];
                $VDCL_start_call_url =				$row['start_call_url'];
                $VDCL_dispo_call_url =				$row['dispo_call_url'];
                $VDCL_xferconf_c_number =			$row['xferconf_c_number'];
                $VDCL_xferconf_d_number =			$row['xferconf_d_number'];
                $VDCL_xferconf_e_number =			$row['xferconf_e_number'];
                $VDCL_uniqueid_status_display =		$row['uniqueid_status_display'];
                $VDCL_uniqueid_status_prefix =		$row['uniqueid_status_prefix'];
                $VDCL_timer_action_destination =	$row['timer_action_destination'];

                //$stmt = "SELECT campaign_script,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_group_alias,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,timer_action_destination from vicidial_campaigns where campaign_id='$campaign';";
                $astDB->where('campaign_id', $campaign);
                $rslt = $astDB->get('vicidial_campaigns', null, 'campaign_script,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_group_alias,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,timer_action_destination');
                $VDIG_cidOR_ct = $astDB->getRowCount();
                if ($VDIG_cidOR_ct > 0) {
                    $row = $rslt[0];
                    if (strlen($VDCL_xferconf_a_dtmf) < 1)
                        {$VDCL_xferconf_a_dtmf = $row['xferconf_a_dtmf'];}
                    if (strlen($VDCL_xferconf_a_number) < 1)
                        {$VDCL_xferconf_a_number = $row['xferconf_a_number'];}
                    if (strlen($VDCL_xferconf_b_dtmf) < 1)
                        {$VDCL_xferconf_b_dtmf = $row['xferconf_b_dtmf'];}
                    if (strlen($VDCL_xferconf_b_number) < 1)
                        {$VDCL_xferconf_b_number = $row['xferconf_b_number'];}
                    if (strlen($VDCL_default_group_alias) < 1)
                        {$VDCL_default_group_alias = $row['default_group_alias'];}
                    if (strlen($VDCL_timer_action) < 1)
                        {$VDCL_timer_action = $row['timer_action'];}
                    if (strlen($VDCL_timer_action_message) < 1)
                        {$VDCL_timer_action_message = $row['timer_action_message'];}
                    if (strlen($VDCL_timer_action_seconds) < 1)
                        {$VDCL_timer_action_seconds = $row['timer_action_seconds'];}
                    if (strlen($VDCL_start_call_url) < 1)
                        {$VDCL_start_call_url = $row['start_call_url'];}
                    if (strlen($VDCL_dispo_call_url) < 1)
                        {$VDCL_dispo_call_url =	$row['dispo_call_url'];}
                    if (strlen($VDCL_xferconf_c_number) < 1)
                        {$VDCL_xferconf_c_number = $row['xferconf_c_number'];}
                    if (strlen($VDCL_xferconf_d_number) < 1)
                        {$VDCL_xferconf_d_number = $row['xferconf_d_number'];}
                    if (strlen($VDCL_xferconf_e_number) < 1)
                        {$VDCL_xferconf_e_number = $row['xferconf_e_number'];}
                    if (strlen($VDCL_timer_action_destination) < 1)
                        {$VDCL_timer_action_destination = $row['timer_action_destination'];}

                    if ( ( (preg_match('/NONE/', $VDCL_ingroup_script)) and (strlen($VDCL_ingroup_script) < 5) ) or (strlen($VDCL_ingroup_script) < 1) ) {
                        $VDCL_ingroup_script = $row['campaign_script'];
                        $script_recording_delay = 0;
                        ##### find if script contains recording fields
                        //$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$campaign' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";";
                        $rslt = $astDB->rawQuery("SELECT count(*) FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$campaign' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";");
                        $vs_vc_ct = $astDB->getRowCount();
                        if ($vs_vc_ct > 0) {
                            $script_recording_delay = $vs_vc_ct;
                        }
                    }
                }

                //$stmt = "SELECT group_web_vars from vicidial_inbound_group_agents where group_id='$VDADchannel_group' and user='$user';";
                $astDB->where('group_id', $VDADchannel_group);
                $astDB->where('user', $user);
                $rslt = $astDB->get('vicidial_inbound_group_agents', null, 'group_web_vars');
                $VDIG_cidgwv_ct = $astDB->getRowCount();
                if ($VDIG_cidgwv_ct > 0) {
                    $row = $rslt[0];
                    $VDCL_group_web_vars = $row['group_web_vars'];
                }

                if (strlen($VDCL_group_web_vars) < 1) {
                    //$stmt = "SELECT group_web_vars from vicidial_campaign_agents where campaign_id='$campaign' and user='$user';";
                    $astDB->where('campaign_id', $campaign);
                    $astDB->where('user', $user);
                    $rslt = $astDB->get('vicidial_campaign_agents', null, 'group_web_vars');
                    $VDIG_cidogwv = $astDB->getRowCount();
                    if ($VDIG_cidogwv > 0) {
                        $row = $rslt[0];
                        $VDCL_group_web_vars = $row['group_web_vars'];
                    }
                }

                ### update the comments in vicidial_live_agents record
                //$stmt = "UPDATE vicidial_live_agents set comments='INBOUND',last_inbound_call_time='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
                $updateData = array(
                    'comments' => 'INBOUND',
                    'last_inbound_call_time' => $NOW_TIME
                );
                $astDB->where('user', $user);
                $astDB->where('server_ip', $server_ip);
                $rslt = $astDB->update('vicidial_live_agents', $updateData);

                $Ctype = 'I';
            } else {
                //$stmt = "SELECT campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_group_alias,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,timer_action_destination from vicidial_campaigns where campaign_id='$VDADchannel_group';";
                $astDB->where('campaign_id', $VDADchannel_group);
                $rslt = $astDB->get('vicidial_campaigns', null, 'campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_group_alias,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,timer_action_destination');
                $VDIG_cid_ct = $astDB->getRowCount();
                if ($VDIG_cid_ct > 0) {
                    $row = $rslt[0];
                    $VDCL_ingroup_script	=		$row['campaign_script'];
                    $VDCL_get_call_launch	=		$row['get_call_launch'];
                    $VDCL_xferconf_a_dtmf	=		$row['xferconf_a_dtmf'];
                    $VDCL_xferconf_a_number	=		$row['xferconf_a_number'];
                    $VDCL_xferconf_b_dtmf	=		$row['xferconf_b_dtmf'];
                    $VDCL_xferconf_b_number	=		$row['xferconf_b_number'];
                    $VDCL_default_group_alias =		$row['default_group_alias'];
                    $VDCL_timer_action = 			$row['timer_action'];
                    $VDCL_timer_action_message = 	$row['timer_action_message'];
                    $VDCL_timer_action_seconds = 	$row['timer_action_seconds'];
                    $VDCL_start_call_url =			$row['start_call_url'];
                    $VDCL_dispo_call_url =			$row['dispo_call_url'];
                    $VDCL_xferconf_c_number =		$row['xferconf_c_number'];
                    $VDCL_xferconf_d_number =		$row['xferconf_d_number'];
                    $VDCL_xferconf_e_number =		$row['xferconf_e_number'];
                    $VDCL_timer_action_destination = $row['timer_action_destination'];
                }

                $script_recording_delay = 0;
                ##### find if script contains recording fields
                //$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$VDADchannel_group' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";";
                $rslt = $astDB->rawQuery("SELECT count(*) FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$VDADchannel_group' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";");
                $vs_vc_ct = $astDB->getRowCount();
                if ($vs_vc_ct > 0) {
                    $script_recording_delay = $vs_vc_ct;
                }

                //$stmt = "SELECT group_web_vars from vicidial_campaign_agents where campaign_id='$VDADchannel_group' and user='$user';";
                $astDB->where('campaign_id', $VDADchannel_group);
                $astDB->where('user', $user);
                $rslt = $astDB->get('vicidial_campaign_agents', null, 'group_web_vars');
                $VDIG_cidogwv = $astDB->getRowCount();
                if ($VDIG_cidogwv > 0) {
                    $row = $rslt[0];
                    $VDCL_group_web_vars = $row['group_web_vars'];
                }
            }

            $VDCL_caller_id_number = '';
            if (strlen($VDCL_default_group_alias) > 1) {
                //$stmt = "SELECT caller_id_number from groups_alias where group_alias_id='$VDCL_default_group_alias';";
                $astDB->where('group_alias_id', $VDCL_default_group_alias);
                $rslt = $astDB->get('groups_alias', null, 'caller_id_number');
                $VDIG_cidnum_ct = $astDB->getRowCount();
                if ($VDIG_cidnum_ct > 0) {
                    $row = $rslt[0];
                    $VDCL_caller_id_number	= $row['caller_id_number'];
                }
            }

            ### Check for List ID override settings
            if (strlen($list_id) > 0) {
                //$stmt = "SELECT xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number,web_form_address,web_form_address_two from vicidial_lists where list_id='$list_id';";
                $astDB->where('list_id', $list_id);
                $rslt = $astDB->get('vicidial_lists', null, 'xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number,web_form_address,web_form_address_two');
                $VDIG_cidOR_ct = $astDB->getRowCount();
                if ($VDIG_cidOR_ct > 0) {
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
                    if (strlen($row['web_form_address']) > 5)
                        {$VDCL_group_web =			$row['web_form_address'];}
                    if (strlen($row['web_form_address_two']) > 5)
                        {$VDCL_group_web_two =		$row['web_form_address_two'];}
                }
            }

            $DID_id = '';
            $DID_extension = '';
            $DID_pattern = '';
            $DID_description = '';

            //$stmt = "SELECT did_id,extension from vicidial_did_log where uniqueid='$uniqueid' and caller_id_number='$phone_number' order by call_date desc limit 1;";
            $astDB->where('uniqueid', $uniqueid);
            $astDB->where('caller_id_number', $phone_number);
            $astDB->orderBy('call_date', 'desc');
            $rslt = $astDB->getOne('vicidial_did_log', 'did_id,extension');
            $VDIDL_ct = $astDB->getRowCount();
            if ($VDIDL_ct > 0) {
                $row = $rslt;
                $DID_id	=			$row['did_id'];
                $DID_extension	=	$row['extension'];

                //$stmt = "SELECT did_pattern,did_description from vicidial_inbound_dids where did_id='$DID_id' limit 1;";
                $astDB->where('did_id', $DID_id);
                $rslt = $astDB->getOne('vicidial_inbound_dids', 'did_pattern,did_description');
                $VDIDL_ct = $astDB->getRowCount();
                if ($VDIDL_ct > 0) {
                    $row = $rslt;
                    $DID_pattern =		$row['did_pattern'];
                    $DID_description =	$row['did_description'];
                }
            }

            ### if web form is set then send on to vicidial.php for override of WEB_FORM address
            if ( (strlen($VDCL_group_web) > 5) or (strlen($VDCL_group_name) > 0) ) {
                //echo "$VDCL_group_web|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|
                //$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|
                //$VDCL_default_xfer_group|$VDCL_ingroup_recording_override|$VDCL_ingroup_rec_filename|$VDCL_default_group_alias|
                //$VDCL_caller_id_number|$VDCL_group_web_vars|$VDCL_group_web_two|$VDCL_timer_action|$VDCL_timer_action_message|
                //$VDCL_timer_action_seconds|$VDCL_xferconf_c_number|$VDCL_xferconf_d_number|$VDCL_xferconf_e_number|
                //$VDCL_uniqueid_status_display|$custom_call_id|$VDCL_uniqueid_status_prefix|$VDCL_timer_action_destination|$DID_id|
                //$DID_extension|$DID_pattern|$DID_description|$INclosecallid|$INxfercallid|\n";
                $dataOutput3 = array(
                    'group_web' => $VDCL_group_web,
                    'group_name' => $VDCL_group_name,
                    'group_color' => $VDCL_group_color,
                    'fronter_display' => $VDCL_fronter_display,
                    'channel_group' => $VDADchannel_group,
                    'ingroup_script' => $VDCL_ingroup_script,
                    'get_call_launch' => $VDCL_get_call_launch,
                    'xferconf_a_dtmf' => $VDCL_xferconf_a_dtmf,
                    'xferconf_a_number' => $VDCL_xferconf_a_number,
                    'xferconf_b_dtmf' => $VDCL_xferconf_b_dtmf,
                    'xferconf_b_number' => $VDCL_xferconf_b_number,
                    'default_xfer_group' => $VDCL_default_xfer_group,
                    'ingroup_recording_override' => $VDCL_ingroup_recording_override,
                    'ingroup_rec_filename' => $VDCL_ingroup_rec_filename,
                    'default_group_alias' => $VDCL_default_group_alias,
                    'caller_id_number' => $VDCL_caller_id_number,
                    'group_web_vars' => $VDCL_group_web_vars,
                    'group_web_two' => $VDCL_group_web_two,
                    'timer_action' => $VDCL_timer_action,
                    'timer_action_message' => $VDCL_timer_action_message,
                    'timer_action_seconds' => $VDCL_timer_action_seconds,
                    'xferconf_c_number' => $VDCL_xferconf_c_number,
                    'xferconf_d_number' => $VDCL_xferconf_d_number,
                    'xferconf_e_number' => $VDCL_xferconf_e_number,
                    'uniqueid_status_display' => $VDCL_uniqueid_status_display,
                    'custom_call_id' => $custom_call_id,
                    'uniqueid_status_prefix' => $VDCL_uniqueid_status_prefix,
                    'timer_action_destination' => $VDCL_timer_action_destination,
                    'did_id' => $DID_id,
                    'did_extension' => $DID_extension,
                    'did_pattern' => $DID_pattern,
                    'did_description' => $DID_description,
                    'closecallid' => $INclosecallid,
                    'xfercallid' => $INxfercallid
                );
            } else {
                //echo "X|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|$VDCL_get_call_launch|
                //$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|
                //$VDCL_ingroup_recording_override|$VDCL_ingroup_rec_filename|$VDCL_default_group_alias|$VDCL_caller_id_number|
                //$VDCL_group_web_vars|$VDCL_group_web_two|$VDCL_timer_action|$VDCL_timer_action_message|$VDCL_timer_action_seconds|
                //$VDCL_xferconf_c_number|$VDCL_xferconf_d_number|$VDCL_xferconf_e_number|$VDCL_uniqueid_status_display|$custom_call_id|
                //$VDCL_uniqueid_status_prefix|$VDCL_timer_action_destination|$DID_id|$DID_extension|$DID_pattern|$DID_description|
                //$INclosecallid|$INxfercallid|\n";
                $dataOutput3 = array(
                    'group_web' => 'X',
                    'group_name' => $VDCL_group_name,
                    'group_color' => $VDCL_group_color,
                    'fronter_display' => $VDCL_fronter_display,
                    'channel_group' => $VDADchannel_group,
                    'ingroup_script' => $VDCL_ingroup_script,
                    'get_call_launch' => $VDCL_get_call_launch,
                    'xferconf_a_dtmf' => $VDCL_xferconf_a_dtmf,
                    'xferconf_a_number' => $VDCL_xferconf_a_number,
                    'xferconf_b_dtmf' => $VDCL_xferconf_b_dtmf,
                    'xferconf_b_number' => $VDCL_xferconf_b_number,
                    'default_xfer_group' => $VDCL_default_xfer_group,
                    'ingroup_recording_override' => $VDCL_ingroup_recording_override,
                    'ingroup_rec_filename' => $VDCL_ingroup_rec_filename,
                    'default_group_alias' => $VDCL_default_group_alias,
                    'caller_id_number' => $VDCL_caller_id_number,
                    'group_web_vars' => $VDCL_group_web_vars,
                    'group_web_two' => $VDCL_group_web_two,
                    'timer_action' => $VDCL_timer_action,
                    'timer_action_message' => $VDCL_timer_action_message,
                    'timer_action_seconds' => $VDCL_timer_action_seconds,
                    'xferconf_c_number' => $VDCL_xferconf_c_number,
                    'xferconf_d_number' => $VDCL_xferconf_d_number,
                    'xferconf_e_number' => $VDCL_xferconf_e_number,
                    'uniqueid_status_display' => $VDCL_uniqueid_status_display,
                    'custom_call_id' => $custom_call_id,
                    'uniqueid_status_prefix' => $VDCL_uniqueid_status_prefix,
                    'timer_action_destination' => $VDCL_timer_action_destination,
                    'did_id' => $DID_id,
                    'did_extension' => $DID_extension,
                    'did_pattern' => $DID_pattern,
                    'did_description' => $DID_description,
                    'closecallid' => $INclosecallid,
                    'xfercallid' => $INxfercallid
                );
            }

            //$stmt = "SELECT full_name from vicidial_users where user='$tsr';";
            $astDB->where('user', $tsr);
            $rslt = $astDB->get('vicidial_users', null, 'full_name');
            $VDU_cid_ct = $astDB->getRowCount();
            if ($VDU_cid_ct > 0) {
                $ros = $rslt[0];
                $fronter_full_name		= $row['full_name'];
                //echo $fronter_full_name . '|' . $tsr . "\n";
                $dataOutput4 = array(
                    'fronter_full_name' => $fronter_full_name
                );
            } else {
                //echo '|' . $tsr . "\n";
                $dataOutput4 = array(
                    'fronter_full_name' => ''
                );
            }
        }

        ##### find if script contains recording fields
        //$stmt="SELECT count(*) FROM vicidial_lists WHERE list_id='$list_id' and agent_script_override!='' and agent_script_override IS NOT NULL and agent_script_override!='NONE';";
        $astDB->where('list_id', $list_id);
        $astDB->where('agent_script_override', '', '!=');
        $astDB->where('agent_script_override', null, 'IS NOT');
        $astDB->where('agent_script_override', 'NONE', '!=');
        $rslt = $astDB->get('vicidial_lists');
        $vls_vc_ct = $astDB->getRowCount();
        if ($vls_vc_ct > 0) {
            $script_recording_delay = 0;
            ##### find if script contains recording fields
            //$stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_lists vls WHERE list_id='$list_id' and vs.script_id=vls.agent_script_override and script_text LIKE \"%--A--recording_%\";";
            $rslt = $astDB->rawQuery("SELECT count(*) FROM vicidial_scripts vs,vicidial_lists vls WHERE list_id='$list_id' and vs.script_id=vls.agent_script_override and script_text LIKE \"%--A--recording_%\";");
            $vs_vc_ct = $astDB->getRowCount();
            if ($vs_vc_ct > 0) {
                $script_recording_delay = $vs_vc_ct;
            }
        }

        $custom_field_names = '|';
        $custom_field_names_SQL = '';
        $custom_field_values = '----------';
        $custom_field_types = '|';
        ### find the names of all custom fields, if any
        //$stmt = "SELECT field_label,field_type FROM vicidial_lists_fields where list_id='$entry_list_id' and field_type NOT IN('SCRIPT','DISPLAY') and field_label NOT IN('vendor_lead_code','source_id','list_id','gmt_offset_now','called_since_last_reset','phone_code','phone_number','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','gender','date_of_birth','alt_phone','email','security_phrase','comments','called_count','last_local_call_time','rank','owner');";
        $astDB->where('list_id', $entry_list_id);
        $astDB->where('field_type', array('SCRIPT','DISPLAY'), 'not in');
        $astDB->where('field_label', array('vendor_lead_code','source_id','list_id','gmt_offset_now','called_since_last_reset','phone_code','phone_number','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','gender','date_of_birth','alt_phone','email','security_phrase','comments','called_count','last_local_call_time','rank','owner'), 'not in');
        $rslt = $astDB->get('vicidial_lists_fields', null, 'field_label,field_type');
        $cffn_ct = $astDB->getRowCount();
        if ($cffn_ct > 0) {
            foreach ($rslt as $row) {
                $custom_field_names .=	"{$row['field_label']}|";
                $custom_field_names_SQL .=	"{$row['field_label']},";
                $custom_field_types .=	"{$row['field_type']}|";
                $custom_field_values .=	"----------";
            }
        }
        if ($cffn_ct > 0) {
            $custom_field_names_SQL = preg_replace("/.$/i", "", $custom_field_names_SQL);
            ### find the values of the named custom fields
            //$stmt = "SELECT $custom_field_names_SQL FROM custom_$entry_list_id where lead_id='$lead_id' limit 1;";
            $astDB->where('lead_id', $lead_id);
            $rslt = $astDB->getOne("custom_{$entry_list_id}", "$custom_field_names_SQL");
            $cffv_ct = $astDB->getRowCount();
            if ($cffv_ct > 0) {
                $custom_field_values = '----------'; 
                foreach ($rslt as $idx => $row) {
                    $custom_field_values .=	"{$row}----------";
                }
                $custom_field_values = preg_replace("/\n/", " ", $custom_field_values);
                $custom_field_values = preg_replace("/\r/", "", $custom_field_values);
            }
        }


        $comments = preg_replace("/\r/i", '', $comments);
        $comments = preg_replace("/\n/i", '!N!', $comments);

        $address1 = preg_replace("/\r/i", '', $address1);
        $address1 = preg_replace("/\n/i", '!N!', $address1);

        $address2 = preg_replace("/\r/i", '', $address2);
        $address2 = preg_replace("/\n/i", '!N!', $address2);

        $areacode = substr($phone_number, 0, 3);
        //$stmt="SELECT country FROM vicidial_phone_codes where country_code='$phone_code' and areacode='$areacode' LIMIT 1;";
        $astDB->where('country_code', $phone_code);
        $astDB->where('areacode', $areacode);
        $rslt = $astDB->getOne('vicidial_phone_codes', 'country');
        $phone_code_ct = $astDB->getRowCount();
        if ($phone_code_ct > 0) {
            $row = $rslt;
            $converted_dial_code = trim("{$row['country']}");
        }
        
        $astDB->where('list_id', $list_id);
        $rslt = $astDB->getOne('vicidial_lists', 'web_form_address,web_form_address_two');
        $row = $rslt;
        $LISTweb_form_address = $row['web_form_address'];
        $LISTweb_form_address_two = $row['web_form_address_two'];
        
        $astDB->where('lead_id', $lead_id);
        $astDB->orderBy('notesid', 'desc');
        $CNotes = $astDB->getOne('vicidial_call_notes', 'call_notes');
        $call_notes = (!is_null($CNotes['call_notes'])) ? $CNotes['call_notes'] : '';

        $LeaD_InfO = array(
            'callerid' => $callerid,
            'lead_id' => $lead_id,
            'dispo' => $dispo,
            'tsr' => $tsr,
            'vendor_id' => $vendor_id,
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
            'date_of_birth' => $date_of_birth,
            'alt_phone' => $alt_phone,
            'email' => $email,
            'security' => $security,
            'comments' => $comments,
            'called_count' => $called_count,
            'CBentry_time' => $CBentry_time,
            'CBcallback_time' => $CBcallback_time,
            'CBuser' => $CBuser,
            'CBcomments' => $CBcomments,
            'dialed_number' => $dialed_number,
            'dialed_label' => $dialed_label,
            'source_id' => $source_id,
            'alt_phone_code' => $alt_phone_code,
            'alt_phone_number' => $alt_phone_number,
            'alt_phone_note' => $alt_phone_note,
            'alt_phone_active' => $alt_phone_active,
            'alt_phone_count' => $alt_phone_count,
            'rank' => $rank,
            'owner' => $owner,
            'script_recording_delay' => $script_recording_delay,
            'entry_list_id' => $entry_list_id,
            'custom_field_names' => $custom_field_names,
            'custom_field_values' => $custom_field_values,
            'custom_field_types' => $custom_field_types,
            'web_form_address' => $LISTweb_form_address,
            'web_form_address_two' => $LISTweb_form_address_two,
            'ACcount' => $ACcount,
            'ACcomments' => $ACcomments,
            'converted_dial_code' => $converted_dial_code,
            'call_notes' => $call_notes,
            'CBcommentsALL' => $CBcommentsALL
        );

        $wait_sec = 0;
        //$StarTtime = date("U");
        //$stmt = "SELECT wait_epoch,wait_sec from vicidial_agent_log where agent_log_id='$agent_log_id';";
        $astDB->where('agent_log_id', $agent_log_id);
        $rslt = $astDB->get('vicidial_agent_log', null, 'wait_epoch,wait_sec');
        $VDpr_ct = $astDB->getRowCount();
        if ($VDpr_ct > 0) {
            $row = $rslt[0];
            $wait_sec = (($StarTtimE - $row['wait_epoch']) + $row['wait_sec']);
        }
        //$stmt="UPDATE vicidial_agent_log set wait_sec='$wait_sec',talk_epoch='$StarTtimE',lead_id='$lead_id' where agent_log_id='$agent_log_id';";
        $astDB->where('agent_log_id', $agent_log_id);
        $rslt = $astDB->update('vicidial_agent_log', array( 'wait_sec' => $wait_sec, 'talk_epoch' => $StarTtimE, 'lead_id' => $lead_id ));

        ### If a scheduled callback, change vicidial_callback record to INACTIVE
        $CBstatus = 0;

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
        if ( ($CBstatus > 0) or (preg_match("/CALLBK|CBHOLD/i", $dispo)) ) {
            //$stmt="UPDATE vicidial_callbacks set status='INACTIVE' where lead_id='$lead_id' and status NOT IN('INACTIVE','DEAD','ARCHIVE');";
            $astDB->where('lead_id', $lead_id);
            $astDB->where('status', array( 'INACTIVE', 'DEAD', 'ARCHIVE' ), 'not in');
            $rslt = $astDB->update('vicidial_callbacks', array( 'status' => 'INACTIVE' ));
        }

        ##### check if system is set to generate logfile for transfers
        //$stmt="SELECT enable_agc_xfer_log FROM system_settings;";
        $rslt = $astDB->getOne('system_settings', 'enable_agc_xfer_log');
        $enable_agc_xfer_log_ct = $astDB->getRowCount();
        if ($enable_agc_xfer_log_ct > 0) {
            $row = $rslt;
            $enable_agc_xfer_log = $row['enable_agc_xfer_log'];
        }

        //if ( ($WeBRooTWritablE > 0) and ($enable_agc_xfer_log > 0) )
        //    {
        //    #	DATETIME|campaign|lead_id|phone_number|user|type
        //    #	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|A
        //    $fp = fopen ("./xfer_log.txt", "a");
        //    fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$phone_number|$user|$Ctype|$callerid|$uniqueid|$province\n");
        //    fclose($fp);
        //    }

        ### Issue Start Call URL if defined
        if (strlen($VDCL_start_call_url) > 7) {
            if (preg_match('/--A--user_custom_/i', $VDCL_start_call_url)) {
                //$stmt = "SELECT custom_one,custom_two,custom_three,custom_four,custom_five from vicidial_users where user='$user';";
                $astDB->where('user', $user);
                $rslt = $astDB->get('vicidial_users', null, 'custom_one,custom_two,custom_three,custom_four,custom_five');
                $VUC_ct = $astDB->getRowCount();
                if ($VUC_ct > 0) {
                    $row = $rslt[0];
                    $user_custom_one	= urlencode(trim($row['custom_one']));
                    $user_custom_two	= urlencode(trim($row['custom_two']));
                    $user_custom_three	= urlencode(trim($row['custom_three']));
                    $user_custom_four	= urlencode(trim($row['custom_four']));
                    $user_custom_five	= urlencode(trim($row['custom_five']));
                }
            }
            $VDCL_start_call_url = preg_replace('/^VAR/', '', $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--lead_id--B--/i', urlencode(trim($lead_id)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--vendor_id--B--/i', urlencode(trim($vendor_id)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--vendor_lead_code--B--/i', urlencode(trim($vendor_id)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--list_id--B--/i', urlencode(trim($list_id)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--gmt_offset_now--B--/i', urlencode(trim($gmt_offset_now)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--phone_code--B--/i', urlencode(trim($phone_code)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--phone_number--B--/i', urlencode(trim($phone_number)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--title--B--/i', urlencode(trim($title)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--first_name--B--/i', urlencode(trim($first_name)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--middle_initial--B--/i', urlencode(trim($middle_initial)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--last_name--B--/i', urlencode(trim($last_name)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--address1--B--/i', urlencode(trim($address1)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--address2--B--/i', urlencode(trim($address2)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--address3--B--/i', urlencode(trim($address3)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--city--B--/i', urlencode(trim($city)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--state--B--/i', urlencode(trim($state)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--province--B--/i', urlencode(trim($province)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--postal_code--B--/i', urlencode(trim($postal_code)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--country_code--B--/i', urlencode(trim($country_code)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--gender--B--/i', urlencode(trim($gender)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--date_of_birth--B--/i', urlencode(trim($date_of_birth)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--alt_phone--B--/i', urlencode(trim($alt_phone)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--email--B--/i', urlencode(trim($email)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--security_phrase--B--/i', urlencode(trim($security_phrase)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--comments--B--/i', urlencode(trim($comments)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--user--B--/i', urlencode(trim($user)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--pass--B--/i', urlencode(trim($pass)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--campaign--B--/i', urlencode(trim($campaign)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--phone_login--B--/i', urlencode(trim($phone_login)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--original_phone_login--B--/i', urlencode(trim($original_phone_login)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--phone_pass--B--/i', urlencode(trim($phone_pass)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--fronter--B--/i', urlencode(trim($fronter)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--closer--B--/i', urlencode(trim($closer)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--group--B--/i', urlencode(trim($VDADchannel_group)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--channel_group--B--/i', urlencode(trim($VDADchannel_group)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--SQLdate--B--/i', urlencode(trim($SQLdate)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--epoch--B--/i', urlencode(trim($epoch)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--uniqueid--B--/i', urlencode(trim($uniqueid)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--customer_zap_channel--B--/i', urlencode(trim($customer_zap_channel)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--customer_server_ip--B--/i', urlencode(trim($customer_server_ip)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--server_ip--B--/i', urlencode(trim($server_ip)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--SIPexten--B--/i', urlencode(trim($SIPexten)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--session_id--B--/i', urlencode(trim($session_id)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--phone--B--/i', urlencode(trim($phone)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--parked_by--B--/i', urlencode(trim($parked_by)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--dispo--B--/i', urlencode(trim($dispo)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--dialed_number--B--/i', urlencode(trim($dialed_number)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--dialed_label--B--/i', urlencode(trim($dialed_label)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--source_id--B--/i', urlencode(trim($source_id)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--rank--B--/i', urlencode(trim($rank)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--owner--B--/i', urlencode(trim($owner)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--camp_script--B--/i', urlencode(trim($camp_script)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--in_script--B--/i', urlencode(trim($in_script)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--fullname--B--/i', urlencode(trim($fullname)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--user_custom_one--B--/i', urlencode(trim($user_custom_one)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--user_custom_two--B--/i', urlencode(trim($user_custom_two)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--user_custom_three--B--/i', urlencode(trim($user_custom_three)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--user_custom_four--B--/i', urlencode(trim($user_custom_four)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--user_custom_five--B--/i', urlencode(trim($user_custom_five)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--talk_time--B--/i', "0", $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--talk_time_min--B--/i', "0", $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--entry_list_id--B--/i', urlencode(trim($entry_list_id)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--did_id--B--/i', urlencode(trim($DID_id)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--did_extension--B--/i', urlencode(trim($DID_extension)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--did_pattern--B--/i', urlencode(trim($DID_pattern)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--did_description--B--/i', urlencode(trim($DID_description)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--closecallid--B--/i', urlencode(trim($INclosecallid)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--xfercallid--B--/i', urlencode(trim($INxfercallid)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--agent_log_id--B--/i', urlencode(trim($agent_log_id)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--call_id--B--/i', urlencode(trim($callerid)), $VDCL_start_call_url);
            $VDCL_start_call_url = preg_replace('/--A--user_group--B--/i', urlencode(trim($user_group)), $VDCL_start_call_url);

            if (strlen($custom_field_names) > 2) {
                $custom_field_names = preg_replace("/^\||\|$/", '', $custom_field_names);
                $custom_field_names = preg_replace("/\|/", ",", $custom_field_names);
                $custom_field_names_ARY = explode(',', $custom_field_names);
                $custom_field_names_ct = count($custom_field_names_ARY);
                $custom_field_names_SQL = $custom_field_names;

                ##### BEGIN grab the data from custom table for the lead_id
                //$stmt="SELECT $custom_field_names_SQL FROM custom_$entry_list_id where lead_id='$lead_id' LIMIT 1;";
                $astDB->where('lead_id', $lead_id);
                $rslt = $astDB->getOne("custom_{$entry_list_id}", "{$custom_field_names_SQL}");
                $list_lead_ct = $astDB->getRowCount();
                if ($list_lead_ct > 0) {
                    foreach ($rslt as $idx => $row) {
                        $form_field_value =		urlencode(trim("{$row}"));
                        $field_name_id =		$idx;
                        $field_name_tag =		"--A--" . $field_name_id . "--B--";
                        $VDCL_start_call_url =  preg_replace("/$field_name_tag/i", "$form_field_value", $VDCL_start_call_url);
                    }
                }
            }

            //$stmt="UPDATE vicidial_log_extended set start_url_processed='Y' where uniqueid='$uniqueid';";
            $astDB->where('uniqueid', $uniqueid);
            $rslt = $astDB->update('vicidial_log_extended', array( 'start_url_processed' => 'Y' ));
            $vle_update = $astDB->getRowCount();

            ### insert a new url log entry
            $SQL_log = "$VDCL_start_call_url";
            $SQL_log = preg_replace('/;/','',$SQL_log);
            $SQL_log = addslashes($SQL_log);
            //$stmt = "INSERT INTO vicidial_url_log SET uniqueid='$uniqueid',url_date='$NOW_TIME',url_type='start',url='$SQL_log',url_response='';";
            $rslt = $astDB->insert('vicidial_url_log', array( 'uniqueid' => $uniqueid, 'url_date' => $NOW_TIME, 'url_type' => 'start', 'url' => $SQL_log, 'url_response' => '' ));
            $affected_rows = $astDB->getRowCount();
            $url_id = $astDB->getInsertId();

            $URLstart_sec = date("U");

            ### grab the call_start_url ###
            //if ($DB > 0) {echo "$VDCL_start_call_url<BR>\n";}
            //$SCUfile = file("$VDCL_start_call_url");
            //if ($DB > 0) {echo "$SCUfile[0]<BR>\n";}

            ### update url log entry
            $URLend_sec = date("U");
            $URLdiff_sec = ($URLend_sec - $URLstart_sec);
            $SCUfile_contents = implode("", $SCUfile);
            $SCUfile_contents = preg_replace('/;/', '', $SCUfile_contents);
            $SCUfile_contents = addslashes($SCUfile_contents);
            //$stmt = "UPDATE vicidial_url_log SET response_sec='$URLdiff_sec',url_response='$SCUfile_contents' where url_log_id='$url_id';";
            $astDB->where('url_log_id', $url_id);
            $rslt = $astDB->update('vicidial_url_log', array( 'response_sec' => $URLdiff_sec, 'url_response' => $SCUfile_contents ));
            $affected_rows = $astDB->getRowCount();

            ##### BEGIN special filtering and response for Vtiger account balance function #####
            # http://vtiger/vicidial/api.php?mode=callxfer&contactwsid=--A--vendor_lead_code--B--&minuteswarning=3
            //$stmt = "SELECT enable_vtiger_integration FROM system_settings;";
            $rslt = $astDB->getOne('system_settings', 'enable_vtiger_integration');
            $ss_conf_ct = $astDB->getRowCount();
            if ($ss_conf_ct > 0) {
                $row = $rslt;
                $enable_vtiger_integration = $row['enable_vtiger_integration'];
            }
            if ( ( ($enable_vtiger_integration > 0) and (preg_match('/callxfer/', $VDCL_start_call_url)) and (preg_match('/contactwsid/', $VDCL_start_call_url)) ) or (preg_match("/minuteswarning/", $VDCL_start_call_url)) ) {
                $SCUoutput = '';
                foreach ($SCUfile as $SCUline) 
                    {$SCUoutput .= "$SCUline";}
                # {"result":true,"durationLimit":3071}
                if ( (strlen($SCUoutput) > 4) or (preg_match("/minuteswarning/", $VDCL_start_call_url)) ) {
                    $minuteswarning = 3; # default to 3
                    if (preg_match("/minuteswarning/",$VDCL_start_call_url)) {
                        $minuteswarningARY = explode('minuteswarning=', $VDCL_start_call_url);
                        $minuteswarning = preg_replace('/&.*/', '', $minuteswarningARY[1]);
                    }
                    ### add this to the Start Call URL for callcard calls to be logged "&minuteswarning=1&callcard=1"
                    if (preg_match("/callcard=/",$VDCL_start_call_url)) {
                        //$stmt="SELECT balance_minutes_start FROM callcard_log where uniqueid='$uniqueid' order by call_time desc LIMIT 1;";
                        $astDB->where('uniqueid', $uniqueid);
                        $astDB->orderBy('call_time', 'desc');
                        $rslt = $astDB->getOne('callcard_log', 'balance_minutes_start');
                        $bms_ct = $astDB->getRowCount();
                        if ($bms_ct > 0) {
                            $row = $rslt;
                            $durationLimit = $row['balance_minutes_start'];

                            //$stmt="UPDATE callcard_log set agent_time='$NOW_TIME',agent='$user' where uniqueid='$uniqueid' order by call_time desc LIMIT 1;";
                            $astDB->where('uniqueid', $uniqueid);
                            $astDB->orderBy('call_time', 'desc');
                            $rslt = $astDB->update('callcard_log', array( 'agent_time' => $NOW_TIME, 'agent' => $user ), 1);
                            $ccl_update = $astDB->getRowCount();
                        }
                    } else {
                        $SCUresponse = explode('durationLimit', $SCUoutput);
                        $durationLimit = preg_replace('/\D/', '', $SCUresponse[1]);
                    }
                    if (strlen($durationLimit) < 1) {$durationLimit = 0;}
                    $durationLimitSECnext = ( ($minuteswarning + 0) * 60);
                    $durationLimitSEC = ( ( ($durationLimit + 0) - $minuteswarning) * 60);  # minutes - 3 for 3-minute-warning
                    if ($durationLimitSEC < 5) {$durationLimitSEC = 5;}
                    if ( ($durationLimitSECnext < 30) or (strlen($durationLimitSECnext)<1) ) {$durationLimitSECnext = 30;}

                    $timer_action_destination = '';
                    if (preg_match("/nextstep=/", $VDCL_start_call_url)) {
                        $nextstepARY = explode('nextstep=', $VDCL_start_call_url);
                        $nextstep = preg_replace("/&.*/", '', $nextstepARY[1]);
                        $nextmessageARY = explode('nextmessage=', $VDCL_start_call_url);
                        $nextmessage = preg_replace("/&.*/", '', $nextmessageARY[1]);
                        $destinationARY = explode('destination=', $VDCL_start_call_url);
                        $destination = preg_replace("/&.*/", '', $destinationARY[1]);
                        $timer_action_destination = "nextstep---$nextstep--$durationLimitSECnext--$destination--$nextmessage--";
                    }

                    //$stmt="UPDATE vicidial_live_agents set external_timer_action='D1_DIAL',external_timer_action_message='$minuteswarning minute warning for customer',external_timer_action_seconds='$durationLimitSEC',external_timer_action_destination='$timer_action_destination' where user='$user';";
                    $updateData = array(
                        'external_timer_action' => 'D1_DIAL',
                        'external_timer_action_message' => "{$minuteswarning} minute warning for customer",
                        'external_timer_action_seconds' => $durationLimitSEC,
                        'external_timer_action_destination' => $timer_action_destination
                    );
                    $astDB->where('user', $user);
                    $rslt = $astDB->update('vicidial_live_agents', $updateData);
                    $vla_update_timer = $astDB->getRowCount();

                    //$fp = fopen ("./call_url_log.txt", "a");
                    //fwrite ($fp, "$VDCL_start_call_url\n$SCUoutput\n$durationLimit|$durationLimitSEC|$vla_update_timer|$minuteswarning|$uniqueid|\n");
                    //fclose($fp);
                }
            }
            ##### END special filtering and response for Vtiger account balance function #####
        }
        
        $outputData = array_merge($dataOutput1, $dataOutput2, $dataOutput3, $dataOutput4, $LeaD_InfO);
        $APIResult = array( "result" => "success", "data" => $outputData );
    } else {
        $APIResult = array( "result" => "error", "message" => "No calls in QUEUE for $user on $server_ip", "data" => array( "has_call" => 0 ));
    #	echo "No calls in QUEUE for $user on $server_ip\n";
    }
} else {
    $APIResult = array( "result" => "error", "message" => "User ID '{$user}' is NOT logged in." );
}
?>