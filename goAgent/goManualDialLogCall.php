<?php
 /**
 * @file 		goManualDialLogCall.php
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
if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
    else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }
if (isset($_GET['goUniqueID'])) { $uniqueid = $astDB->escape($_GET['goUniqueID']); }
    else if (isset($_POST['goUniqueID'])) { $uniqueid = $astDB->escape($_POST['goUniqueID']); }
if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
    else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }
if (isset($_GET['goListID'])) { $list_id = $astDB->escape($_GET['goListID']); }
    else if (isset($_POST['goListID'])) { $list_id = $astDB->escape($_POST['goListID']); }
if (isset($_GET['goLengthInSec'])) { $length_in_sec = $astDB->escape($_GET['goLengthInSec']); }
    else if (isset($_POST['goLengthInSec'])) { $length_in_sec = $astDB->escape($_POST['goLengthInSec']); }
if (isset($_GET['goPhoneCode'])) { $phone_code = $astDB->escape($_GET['goPhoneCode']); }
    else if (isset($_POST['goPhoneCode'])) { $phone_code = $astDB->escape($_POST['goPhoneCode']); }
if (isset($_GET['goPhoneNumber'])) { $phone_number = $astDB->escape($_GET['goPhoneNumber']); }
    else if (isset($_POST['goPhoneNumber'])) { $phone_number = $astDB->escape($_POST['goPhoneNumber']); }
if (isset($_GET['goExten'])) { $exten = $astDB->escape($_GET['goExten']); }
    else if (isset($_POST['goExten'])) { $exten = $astDB->escape($_POST['goExten']); }
if (isset($_GET['goExtension'])) { $extension = $astDB->escape($_GET['goExtension']); }
    else if (isset($_POST['goExtension'])) { $extension = $astDB->escape($_POST['goExtension']); }
if (isset($_GET['goChannel'])) { $channel = $astDB->escape($_GET['goChannel']); }
    else if (isset($_POST['goChannel'])) { $channel = $astDB->escape($_POST['goChannel']); }
if (isset($_GET['goStartEpoch'])) { $start_epoch = $astDB->escape($_GET['goStartEpoch']); }
    else if (isset($_POST['goStartEpoch'])) { $start_epoch = $astDB->escape($_POST['goStartEpoch']); }
if (isset($_GET['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_GET['goAutoDialLevel']); }
    else if (isset($_POST['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_POST['goAutoDialLevel']); }
if (isset($_GET['goStopRecAfterEachCall'])) { $VDstop_rec_after_each_call = $astDB->escape($_GET['goStopRecAfterEachCall']); }
    else if (isset($_POST['goStopRecAfterEachCall'])) { $VDstop_rec_after_each_call = $astDB->escape($_POST['goStopRecAfterEachCall']); }
if (isset($_GET['goConfSilentPrefix'])) { $conf_silent_prefix = $astDB->escape($_GET['goConfSilentPrefix']); }
    else if (isset($_POST['goConfSilentPrefix'])) { $conf_silent_prefix = $astDB->escape($_POST['goConfSilentPrefix']); }
if (isset($_GET['goProtocol'])) { $protocol = $astDB->escape($_GET['goProtocol']); }
    else if (isset($_POST['goProtocol'])) { $protocol = $astDB->escape($_POST['goProtocol']); }
if (isset($_GET['goExtContext'])) { $ext_context = $astDB->escape($_GET['goExtContext']); }
    else if (isset($_POST['goExtContext'])) { $ext_context = $astDB->escape($_POST['goExtContext']); }
if (isset($_GET['goConfExten'])) { $conf_exten = $astDB->escape($_GET['goConfExten']); }
    else if (isset($_POST['goConfExten'])) { $conf_exten = $astDB->escape($_POST['goConfExten']); }
if (isset($_GET['goUserABB'])) { $user_abb = $astDB->escape($_GET['goUserABB']); }
    else if (isset($_POST['goUserABB'])) { $user_abb = $astDB->escape($_POST['goUserABB']); }
if (isset($_GET['goMDnextCID'])) { $MDnextCID = $astDB->escape($_GET['goMDnextCID']); }
    else if (isset($_POST['goMDnextCID'])) { $MDnextCID = $astDB->escape($_POST['goMDnextCID']); }
if (isset($_GET['goInOut'])) { $inOUT = $astDB->escape($_GET['goInOut']); }
    else if (isset($_POST['goInOut'])) { $inOUT = $astDB->escape($_POST['goInOut']); }
if (isset($_GET['goALTDial'])) { $alt_dial = $astDB->escape($_GET['goALTDial']); }
    else if (isset($_POST['goALTDial'])) { $alt_dial = $astDB->escape($_POST['goALTDial']); }
if (isset($_GET['goAgentChannel'])) { $agentchannel = $astDB->escape($_GET['goAgentChannel']); }
    else if (isset($_POST['goAgentChannel'])) { $agentchannel = $astDB->escape($_POST['goAgentChannel']); }
if (isset($_GET['goConfDialed'])) { $conf_dialed = $astDB->escape($_GET['goConfDialed']); }
    else if (isset($_POST['goConfDialed'])) { $conf_dialed = $astDB->escape($_POST['goConfDialed']); }
if (isset($_GET['goLeavingThreeway'])) { $leaving_threeway = $astDB->escape($_GET['goLeavingThreeway']); }
    else if (isset($_POST['goLeavingThreeway'])) { $leaving_threeway = $astDB->escape($_POST['goLeavingThreeway']); }
if (isset($_GET['goHangupAllNonReserved'])) { $hangup_all_non_reserved = $astDB->escape($_GET['goHangupAllNonReserved']); }
    else if (isset($_POST['goHangupAllNonReserved'])) { $hangup_all_non_reserved = $astDB->escape($_POST['goHangupAllNonReserved']); }
if (isset($_GET['goBlindTransfer'])) { $blind_transfer = $astDB->escape($_GET['goBlindTransfer']); }
    else if (isset($_POST['goBlindTransfer'])) { $blind_transfer = $astDB->escape($_POST['goBlindTransfer']); }
if (isset($_GET['goDialMethod'])) { $dial_method = $astDB->escape($_GET['goDialMethod']); }
    else if (isset($_POST['goDialMethod'])) { $dial_method = $astDB->escape($_POST['goDialMethod']); }
if (isset($_GET['goNoDeleteVDAC'])) { $nodeletevdac = $astDB->escape($_GET['goNoDeleteVDAC']); }
    else if (isset($_POST['goNoDeleteVDAC'])) { $nodeletevdac = $astDB->escape($_POST['goNoDeleteVDAC']); }
if (isset($_GET['goALTNumStatus'])) { $alt_num_status = $astDB->escape($_GET['goALTNumStatus']); }
    else if (isset($_POST['goALTNumStatus'])) { $alt_num_status = $astDB->escape($_POST['goALTNumStatus']); }
if (isset($_GET['goQMExtension'])) { $qm_extension = $astDB->escape($_GET['goQMExtension']); }
    else if (isset($_POST['goQMExtension'])) { $qm_extension = $astDB->escape($_POST['goQMExtension']); }

$user = $agent->user;
$server_ip = (strlen($server_ip) > 0) ? $server_ip : $phone_settings->server_ip;

$VDCL_ingroup_recording_override = '';
$VDCL_ingroup_rec_filename = '';
$Ctype = 'A';
$MT[0] = '';
$row = '';
$rowx = '';
$vidSQL = '';
$VDterm_reason = '';
$testOutput = '';

if ($is_logged_in) {
    if ($stage == "start") {
        if ( (strlen($uniqueid) < 1) || (strlen($lead_id) < 1) || (strlen($list_id) < 1) || (strlen($phone_number) < 1) || (strlen($campaign) < 1) ) {
            $APIResult = array( "result" => "error", "message" => "Log NOT Entered. Either one of the required parameters are missing." );
        } else {
            $user_group = $agent->user_group;
    
            ##### insert log into vicidial_log_extended for manual VICIDiaL call
            $stmt="INSERT IGNORE INTO vicidial_log_extended SET uniqueid='$uniqueid',server_ip='$server_ip',call_date='$NOW_TIME',lead_id='$lead_id',caller_code='$MDnextCID',custom_call_id='' ON DUPLICATE KEY UPDATE server_ip='$server_ip',call_date='$NOW_TIME',lead_id='$lead_id',caller_code='$MDnextCID';";
            $rslt = $astDB->rawQuery($stmt);
            $affected_rowsX = $astDB->getRowCount();
    
            $manualVLexists = 0;
            $beginUNIQUEID = preg_replace("/\..*/", "", $uniqueid);
            //$stmt="SELECT count(*) from vicidial_log where lead_id='$lead_id' and user='$user' and phone_number='$phone_number' and uniqueid LIKE \"$beginUNIQUEID%\";";
            $astDB->where('lead_id', $lead_id);
            $astDB->where('user', $user);
            $astDB->where('phone_number', $phone_number);
            $astDB->where('uniqueid', "$beginUNIQUEID%", 'like');
            $rslt = $astDB->get('vicidial_log');
            $VL_exists_ct = $astDB->getRowCount();
            if ($VL_exists_ct > 0) {
                $row            = $rslt[0];
                $manualVLexists = $VL_exists_ct;
            }
    
            $manualVLexistsDUP = 0;
            if ($manualVLexists < 1) {
                ##### insert log into vicidial_log for manual VICIDiaL call
                //$stmt="INSERT INTO vicidial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed,user_group,alt_dial) values('$uniqueid','$lead_id','$list_id','$campaign','$NOW_TIME','$StarTtimE','INCALL','$phone_code','$phone_number','$user','MANUAL','N','$user_group','$alt_dial');";
                $insertData = array(
                    'uniqueid' => $uniqueid,
                    'lead_id' => $lead_id,
                    'list_id' => $list_id,
                    'campaign_id' => $campaign,
                    'call_date' => $NOW_TIME,
                    'start_epoch' => $StarTtimE,
                    'status' => 'INCALL',
                    'phone_code' => $phone_code,
                    'phone_number' => $phone_number,
                    'user' => $user,
                    'comments' => 'MANUAL',
                    'processed' => 'N',
                    'user_group' => $user_group,
                    'alt_dial' => $alt_dial
                );
                $astDB->insert('vicidial_log', $insertData);
                $DUPerrno = $astDB->getLastError();
                if (strlen($DUPerrno) > 0)
                    {$manualVLexistsDUP = 1;}
                $affected_rows = $astDB->getRowCount();
            }
            if ( ($manualVLexists > 0) or ($manualVLexistsDUP > 0) ) {
                ##### insert log into vicidial_log for manual VICIDiaL call
                //$stmt="UPDATE vicidial_log SET list_id='$list_id',comments='MANUAL',user_group='$user_group',alt_dial='$alt_dial' where lead_id='$lead_id' and user='$user' and phone_number='$phone_number' and uniqueid LIKE \"$beginUNIQUEID%\";";
                $updateData = array(
                    'list_id' => $list_id,
                    'comments' => 'MANUAL',
                    'user_group' => $user_group,
                    'alt_dial' => $alt_dial
                );
                $astDB->where('lead_id', $lead_id);
                $astDB->where('user', $user);
                $astDB->where('phone_number', $phone_number);
                $astDB->where('uniqueid', "$beginUNIQUEID%", 'like');
                $rslt = $astDB->update('vicidial_log', $updateData);
                $affected_rows = $astDB->getRowCount();
            }
    
            if ($affected_rows > 0) {
                $message = "VICIDiaL_LOG Inserted: $uniqueid|$channel|$NOW_TIME\n$StarTtimE";
            } else {
                $message = "LOG NOT ENTERED";
            }
    
            //$stmt = "UPDATE vicidial_auto_calls SET uniqueid='$uniqueid' where lead_id='$lead_id';";
            $updateData = array(
                'uniqueid' => $uniqueid
            );
            $astDB->where('lead_id', $lead_id);
            $rslt = $astDB->update('vicidial_auto_calls', $updateData);

            $APIResult = array( "result" => "success", "message" => $message );
		}
	}

    if ($stage == "end") {
        $status_dispo = 'DISPO';
        $log_no_enter = 0;
        $message = '';
        if ($alt_num_status > 0)
            {$status_dispo = 'ALTNUM';}
        ##### get call type from vicidial_live_agents table
        $VLA_inOUT = 'NONE';
        //$stmt="SELECT comments FROM vicidial_live_agents where user='$user' order by last_update_time desc limit 1;";
        $astDB->where('user', $user);
        $astDB->orderBy('last_update_time', 'desc');
        $rslt = $astDB->getOne('vicidial_live_agents', 'comments');
        $VLA_inOUT_ct = $astDB->getRowCount();
        if ($VLA_inOUT_ct > 0) {
            $row = $rslt;
            $VLA_inOUT = $row['comments'];
        }
    
        if ( (strlen($uniqueid) < 1) && ($VLA_inOUT == 'INBOUND') ) {
            $uniqueid = '6666.1';
        }
        if ( (strlen($uniqueid) < 1) || (strlen($lead_id) < 1) ) {
            $message = "LOG NOT ENTERED: uniqueid or lead_id is NOT value.";
            $log_no_enter = 1;
        } else {
            $term_reason = 'NONE';
            if ($start_epoch < 1000) {
                if ($VLA_inOUT == 'INBOUND') {
                    $four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));
    
                    ##### look for the start epoch in the vicidial_closer_log table
                    //$stmt="SELECT start_epoch,term_reason,closecallid,campaign_id,status FROM vicidial_closer_log where phone_number='$phone_number' and lead_id='$lead_id' and user='$user' and call_date > \"$four_hours_ago\" order by closecallid desc limit 1;";
                    $astDB->where('phone_number', $phone_number);
                    $astDB->where('lead_id', $lead_id);
                    $astDB->where('user', $user);
                    $astDB->where('call_date', $four_hours_ago, '>');
                    $astDB->orderBy('closecallid', 'desc');
                    $VDIDselect = "VDCL_LID $lead_id $phone_number $user $four_hours_ago";
                    $rslt = $astDB->getOne('vicidial_closer_log', 'start_epoch,term_reason,closecallid AS vicidial_id,campaign_id,status');
                } else {
                    ##### look for the start epoch in the vicidial_log table
                    //$stmt="SELECT start_epoch,term_reason,uniqueid,campaign_id,status FROM vicidial_log where uniqueid='$uniqueid' and lead_id='$lead_id' order by call_date desc limit 1;";
                    $astDB->where('uniqueid', $uniqueid);
                    $astDB->where('lead_id', $lead_id);
                    $astDB->orderBy('call_date', 'desc');
                    $VDIDselect = "VDL_UIDLID $uniqueid $lead_id";
                    $rslt = $astDB->getOne('vicidial_log', 'start_epoch,term_reason,uniqueid AS vicidial_id,campaign_id,status');
                }
                $VM_mancall_ct = $astDB->getRowCount();
                if ($VM_mancall_ct > 0) {
                    $row = $rslt;
                    $start_epoch =		$row['start_epoch'];
                    $VDterm_reason =	$row['term_reason'];
                    $VDvicidial_id =	$row['vicidial_id'];
                    $VDcampaign_id =	$row['campaign_id'];
                    $VDstatus =			$row['status'];
                    $length_in_sec = ($StarTtimE - $start_epoch);
                } else {
                    $length_in_sec = 0;
                }
    
                if ( ($length_in_sec < 1) && ($VLA_inOUT == 'INBOUND') ) {
                    ##### start epoch in the vicidial_log table, couldn't find one in vicidial_closer_log
                    //$stmt="SELECT start_epoch,term_reason,campaign_id,status FROM vicidial_log where uniqueid='$uniqueid' and lead_id='$lead_id' order by call_date desc limit 1;";
                    $astDB->where('uniqueid', $uniqueid);
                    $astDB->where('lead_id', $lead_id);
                    $astDB->orderBy('call_date', 'desc');
                    $rslt = $astDB->getOne('vicidial_log', 'start_epoch,term_reason,campaign_id,status');
                    $VM_mancall_ct = $astDB->getRowCount();
                    if ($VM_mancall_ct > 0) {
                        $row = $rslt;
                        $start_epoch =		$row['start_epoch'];
                        $VDterm_reason =	$row['term_reason'];
                        $VDcampaign_id =	$row['campaign_id'];
                        $VDstatus =			$row['status'];
                        $length_in_sec = ($StarTtimE - $start_epoch);
                    } else {
                        $length_in_sec = 0;
                    }
                }
            } else {
                $length_in_sec = ($StarTtimE - $start_epoch);
            }
            
            if (strlen($VDcampaign_id) < 1) {$VDcampaign_id = $campaign;}
    
            $four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));
    
            if ($VLA_inOUT == 'INBOUND') {
                $vcl_statusSQL = '';
                $updateData = array(
                    'end_epoch' => $StarTtimE,
                    'length_in_sec' => $length_in_sec
                );
                if ($VDstatus == 'INCALL') {
                    //$vcl_statusSQL = ",status='$status_dispo'";
                    $updateData['status'] = $status_dispo;
                }
                //$stmt = "UPDATE vicidial_closer_log set end_epoch='$StarTtimE', length_in_sec='$length_in_sec' $vcl_statusSQL where lead_id='$lead_id' and user='$user' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
                $astDB->where('lead_id', $lead_id);
                $astDB->where('user', $user);
                $astDB->where('call_date', $four_hours_ago, '>');
                $astDB->orderBy('call_date', 'desc');
                $rslt = $astDB->update('vicidial_closer_log', $updateData, 1);
                $affected_rows = $astDB->getRowCount();
                if ($affected_rows > 0) {
                    $message .= "$uniqueid\n$channel\n";
    
                #	$fp = fopen ("./vicidial_debug.txt", "a");
                #	fwrite ($fp, "$NOW_TIME|INBND_LOG_4|$VDstatus|$uniqueid|$lead_id|$user|$inOUT|$length_in_sec|$VDterm_reason|$VDvicidial_id|$start_epoch|$stmt|\n");
                #	fclose($fp);
                } else {
                #   $fp = fopen ("./vicidial_debug.txt", "a");
                #   fwrite ($fp, "$NOW_TIME|INBND_LOG_2|$uniqueid|$lead_id|$user|$inOUT|$length_in_sec|$VDterm_reason|$VDvicidial_id|$start_epoch|\n");
                #   fclose($fp);
                }
            }
    
            #############################################
            ##### START QUEUEMETRICS LOGGING LOOKUP #####
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
                
                if ($enable_queuemetrics_logging > 0) {
                    $linkB = new MySQLiDB("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass", "$queuemetrics_dbname");
                }
            }
            ##### END QUEUEMETRICS LOGGING LOOKUP #####
            ###########################################
    
            if ($auto_dial_level > 0) {
                ### check to see if campaign has alt_dial enabled
                //$stmt="SELECT auto_alt_dial,use_internal_dnc,use_campaign_dnc,use_other_campaign_dnc FROM vicidial_campaigns where campaign_id='$campaign';";
                $astDB->where('campaign_id', $campaign);
                $rslt = $astDB->get('vicidial_campaigns', null, 'auto_alt_dial,use_internal_dnc,use_campaign_dnc,use_other_campaign_dnc');
                $VAC_mancall_ct = $astDB->getRowCount();
                if ($VAC_mancall_ct > 0) {
                    $row = $rslt[0];
                    $auto_alt_dial =			$row['auto_alt_dial'];
                    $use_internal_dnc =			$row['use_internal_dnc'];
                    $use_campaign_dnc =			$row['use_campaign_dnc'];
                    $use_other_campaign_dnc =	$row['use_other_campaign_dnc'];
                } else {
                    $auto_alt_dial = 'NONE';
                }
                if (preg_match("/(ALT_ONLY|ADDR3_ONLY|ALT_AND_ADDR3|ALT_AND_EXTENDED|ALT_AND_ADDR3_AND_EXTENDED|EXTENDED_ONLY)/i", $auto_alt_dial)) {
                    ### check to see if lead should be alt_dialed
                    if (strlen($alt_dial) < 2) {
                        $alt_dial = 'NONE';
                    }
    
                    ### check if inbound call, if so find a recent outbound call to pull alt_dial value from
                    if ($VLA_inOUT == 'INBOUND') {
                        $one_hour_ago = date("Y-m-d H:i:s", mktime(date("H")-1,date("i"),date("s"),date("m"),date("d"),date("Y")));
                        ##### find a recent outbound call associated with this inbound call
                        //$stmt="SELECT alt_dial FROM vicidial_log where lead_id='$lead_id' and status IN('DROP','XDROP') and call_date > \"$one_hour_ago\" order by call_date desc limit 1;";
                        $astDB->where('lead_id', $lead_id);
                        $astDB->where('status', array('DROP', 'XDROP'), 'in');
                        $astDB->where('call_date', $one_hour_ago);
                        $astDB->orderBy('call_date', 'desc');
                        $rslt = $astDB->getOne('vicidial_log', 'alt_dial');
                        $VL_alt_ct = $astDB->getRowCount();
                        if ($VL_alt_ct > 0) {
                            $row = $rslt;
                            $alt_dial = $row['alt_dial'];
                        }
                    }
    
                    if ( (preg_match("/(NONE|MAIN)/i", $alt_dial)) && (preg_match("/(ALT_ONLY|ALT_AND_ADDR3|ALT_AND_EXTENDED)/i", $auto_alt_dial)) ) {
                        $alt_dial_skip = 0;
                        //$stmt="SELECT alt_phone,gmt_offset_now,state,vendor_lead_code FROM vicidial_list where lead_id='$lead_id';";
                        $astDB->where('lead_id', $lead_id);
                        $rslt = $astDB->get('vicidial_list', null, 'alt_phone,gmt_offset_now,state,vendor_lead_code');
                        $VAC_mancall_ct = $astDB->getRowCount();
                        if ($VAC_mancall_ct > 0) {
                            $row = $rslt[0];
                            $alt_phone =		$row['alt_phone'];
                            $alt_phone = preg_replace("/[^0-9]/i","",$alt_phone);
                            $gmt_offset_now =	$row['gmt_offset_now'];
                            $state =			$row['state'];
                            $vendor_lead_code =	$row['vendor_lead_code'];
                        } else {
                            $alt_phone = '';
                        }
                        if (strlen($alt_phone)>5) {
                            if ( (preg_match("/Y/", $use_internal_dnc)) || (preg_match("/AREACODE/", $use_internal_dnc)) ) {
                                if (preg_match("/AREACODE/", $use_internal_dnc)) {
                                    $alt_phone_areacode = substr($alt_phone, 0, 3);
                                    $alt_phone_areacode .= "XXXXXXX";
                                    //$stmtA="SELECT count(*) from vicidial_dnc where phone_number IN('$alt_phone','$alt_phone_areacode');";
                                    $astDB->where('phone_number', array("$alt_phone", "$alt_phone_areacode"), 'in');
                                } else {
                                    //$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$alt_phone';";
                                    $astDB->where('phone_number', $alt_phone);
                                }
                                $rslt = $astDB->get('vicidial_dnc');
                                $VLAP_dnc_ct = $astDB->getRowCount();
                                if ($VLAP_dnc_ct > 0) {
                                    $VD_alt_dnc_count = $VLAP_dnc_ct;
                                }
                            } else {
                                $VD_alt_dnc_count = 0;
                            }
                            if ( (preg_match("/Y/", $use_campaign_dnc)) || (preg_match("/AREACODE/", $use_campaign_dnc)) ) {
                                $temp_campaign_id = $campaign;
                                if (strlen($use_other_campaign_dnc) > 0) {$temp_campaign_id = $use_other_campaign_dnc;}
                                if (preg_match("/AREACODE/", $use_campaign_dnc)) {
                                    $alt_phone_areacode = substr($alt_phone, 0, 3);
                                    $alt_phone_areacode .= "XXXXXXX";
                                    //$stmtA="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$alt_phone','$alt_phone_areacode') and campaign_id='$temp_campaign_id';";
                                    $astDB->where('phone_number', array("$alt_phone", "$alt_phone_areacode"), 'in');
                                } else {
                                    //$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$alt_phone' and campaign_id='$temp_campaign_id';";
                                    $astDB->where('phone_number', $alt_phone);
                                }
                                $astDB->where('campaign_id', $temp_campaign_id);
                                $rslt = $astDB->get('vicidial_campaign_dnc');
                                $VLAP_cdnc_ct = $astDB->getRowCount();
                                if ($VLAP_cdnc_ct > 0) {
                                    $VD_alt_dnc_count = ($VD_alt_dnc_count + $VLAP_cdnc_ct);
                                }
                            }
                            if ($VD_alt_dnc_count < 1) {
                                ### insert record into vicidial_hopper for alt_phone call attempt
                                //$stmt = "INSERT INTO vicidial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='HOLD',list_id='$list_id',gmt_offset_now='$gmt_offset_now',state='$state',alt_dial='ALT',user='',priority='25',source='A',vendor_lead_code='$vendor_lead_code';";
                                $insertData = array(
                                    'lead_id' => $lead_id,
                                    'campaign_id' => $campaign,
                                    'status' => 'HOLD',
                                    'list_id' => $list_id,
                                    'gmt_offset_now' => $gmt_offset_now,
                                    'state' => $state,
                                    'alt_dial' => 'ALT',
                                    'user' => '',
                                    'priority' => '25',
                                    'source' => 'A',
                                    'vendor_lead_code' => $vendor_lead_code
                                );
                                $rslt = $astDB->insert('vicidial_hopper', $insertData);
                            } else {
                                $alt_dial_skip = 1;
                            }
                        } else {
                            $alt_dial_skip = 1;
                        }
                        if ($alt_dial_skip > 0) {
                            $alt_dial = 'ALT';
                        }
                    }
    
                    if ( ( (preg_match("/(ALT)/i", $alt_dial)) && (preg_match("/ALT_AND_ADDR3/i", $auto_alt_dial)) ) || ( (preg_match("/(NONE|MAIN)/i", $alt_dial)) && (preg_match("/ADDR3_ONLY/i", $auto_alt_dial)) ) ) {
                        $addr3_dial_skip = 0;
                        //$stmt="SELECT address3,gmt_offset_now,state,vendor_lead_code FROM vicidial_list where lead_id='$lead_id';";
                        $astDB->where('lead_id', $lead_id);
                        $rslt = $astDB->get('vicidial_list', null, 'address3,gmt_offset_now,state,vendor_lead_code');
                        $VAC_mancall_ct = $astDB->getRowCount();
                        if ($VAC_mancall_ct > 0) {
                            $row = $rslt[0];
                            $address3 =			$row['address3'];
                            $address3 = preg_replace("/[^0-9]/i","",$address3);
                            $gmt_offset_now =	$row['gmt_offset_now'];
                            $state =			$row['state'];
                            $vendor_lead_code = $row['vendor_lead_code'];
                        } else {
                            $address3 = '';
                        }
                        if (strlen($address3) > 5) {
                            if ( (preg_match("/Y/", $use_internal_dnc)) || (preg_match("/AREACODE/", $use_internal_dnc)) ) {
                                if (preg_match("/AREACODE/", $use_internal_dnc)) {
                                    $addr3_phone_areacode = substr($address3, 0, 3);
                                    $addr3_phone_areacode .= "XXXXXXX";
                                    //$stmtA="SELECT count(*) from vicidial_dnc where phone_number IN('$address3','$addr3_phone_areacode');";
                                    $astDB->where('phone_number', array("$address3", "$addr3_phone_areacode"), 'in');
                                } else {
                                    //$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$address3';";
                                    $astDB->where('phone_number', $address3);
                                }
                                $rslt = $astDB->get('vicidial_dnc');
                                $VLAP_dnc_ct = $astDB->getRowCount();
                                if ($VLAP_dnc_ct > 0) {
                                    $VD_alt_dnc_count = $VLAP_dnc_ct;
                                }
                            } else {
                                $VD_alt_dnc_count = 0;
                            }
                            if ( (preg_match("/Y/", $use_campaign_dnc)) || (preg_match("/AREACODE/", $use_campaign_dnc)) ) {
                                $temp_campaign_id = $campaign;
                                if (strlen($use_other_campaign_dnc) > 0) {$temp_campaign_id = $use_other_campaign_dnc;}
                                if (preg_match("/AREACODE/", $use_campaign_dnc)) {
                                    $addr3_phone_areacode = substr($address3, 0, 3);
                                    $addr3_phone_areacode .= "XXXXXXX";
                                    //$stmtA="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$address3','$addr3_phone_areacode') and campaign_id='$temp_campaign_id';";
                                    $astDB->where('phone_number', array("$address3", "$addr3_phone_areacode"), 'in');
                                } else {
                                    //$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$address3' and campaign_id='$temp_campaign_id';";
                                    $astDB->where('phone_number', $address3);
                                }
                                $astDB->where('campaign_id', $temp_campaign_id);
                                $rslt = $astDB->get('vicidial_campaign_dnc');
                                $VLAP_cdnc_ct = $astDB->getRowCount();
                                if ($VLAP_cdnc_ct > 0) {
                                    $VD_alt_dnc_count = ($VD_alt_dnc_count + $VLAP_cdnc_ct);
                                }
                            }
                            if ($VD_alt_dnc_count < 1) {
                                ### insert record into vicidial_hopper for address3 call attempt
                                //$stmt = "INSERT INTO vicidial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='HOLD',list_id='$list_id',gmt_offset_now='$gmt_offset_now',state='$state',alt_dial='ADDR3',user='',priority='20',source='A',vendor_lead_code='$vendor_lead_code';";
                                $insertData = array(
                                    'lead_id' => $lead_id,
                                    'campaign_id' => $campaign,
                                    'status' => 'HOLD',
                                    'list_id' => $list_id,
                                    'gmt_offset_now' => $gmt_offset_now,
                                    'state' => $state,
                                    'alt_dial' => 'ADDR3',
                                    'user' => '',
                                    'priority' => '20',
                                    'source' => 'A',
                                    'vendor_lead_code' => $vendor_lead_code
                                );
                                $rslt = $astDB->insert('vicidial_hopper', $insertData);
                            } else {
                                $addr3_dial_skip = 1;
                            }
                        } else {
                            $addr3_dial_skip = 1;
                        }
                        if ($addr3_dial_skip > 0) {
                            $alt_dial = 'ADDR3';
                        }
                    }
    
                    if ( ( ( (preg_match("/(NONE|MAIN)/i", $alt_dial)) && (preg_match("/EXTENDED_ONLY/i", $auto_alt_dial)) ) || ( (preg_match("/(ALT)/i", $alt_dial)) && (preg_match("/(ALT_AND_EXTENDED)/i", $auto_alt_dial)) ) || ( (preg_match("/(ADDR3)/i", $alt_dial)) && (preg_match("/(ADDR3_AND_EXTENDED|ALT_AND_ADDR3_AND_EXTENDED)/i", $auto_alt_dial)) ) || ( (preg_match("/(X)/i", $alt_dial)) && (preg_match("/EXTENDED/i", $auto_alt_dial)) ) )  && (!preg_match("/LAST/i", $alt_dial)) ) {
                        if (preg_match("/(ADDR3)/i", $alt_dial)) {
                            $Xlast = 0;
                        } else {
                            $Xlast = preg_replace("/[^0-9]/", "", $alt_dial);
                        }
                        if (strlen($Xlast) < 1) {
                            $Xlast = 0;
                        }
                        $VD_altdialx = '';
    
                        //$stmt="SELECT gmt_offset_now,state,list_id,entry_list_id,vendor_lead_code FROM vicidial_list where lead_id='$lead_id';";
                        $astDB->where('lead_id', $lead_id);
                        $rslt = $astDB->get('vicidial_list', null, 'gmt_offset_now,state,list_id,entry_list_id,vendor_lead_code');
                        $VL_deailts_ct = $astDB->getRowCount();
                        if ($VL_deailts_ct > 0) {
                            $row = $rslt[0];
                            $EA_gmt_offset_now =	$row['gmt_offset_now'];
                            $EA_state =				$row['state'];
                            $EA_list_id =			$row['list_id'];
                            $EA_entry_list_id =		$row['entry_list_id'];
                            $EA_vendor_lead_code =	$row['vendor_lead_code'];
                        }
                        $alt_dial_phones_count = 0;
                        //$stmt="SELECT count(*) FROM vicidial_list_alt_phones where lead_id='$lead_id';";
                        $astDB->where('lead_id', $lead_id);
                        $rslt = $astDB->get('vicidial_list_alt_phones');
                        $VLAP_ct = $astDB->getRowCount();
                        if ($VLAP_ct > 0) {
                            $alt_dial_phones_count = $VLAP_ct;
                        }
                        while ( ($alt_dial_phones_count > 0) && ($alt_dial_phones_count > $Xlast) ) {
                            $Xlast++;
                            //$stmt="SELECT alt_phone_id,phone_number,active FROM vicidial_list_alt_phones where lead_id='$lead_id' and alt_phone_count='$Xlast';";
                            $astDB->where('lead_id', $lead_id);
                            $astDB->where('alt_phone_count', $Xlast);
                            $rslt = $astDB->get('vicidial_list_alt_phones', null, 'alt_phone_id,phone_number,active');
                            $VLAP_detail_ct = $astDB->getRowCount();
                            if ($VLAP_detail_ct > 0) {
                                $row = $rslt[0];
                                $VD_altdial_id =		$row['alt_phone_id'];
                                $VD_altdial_phone =		$row['phone_number'];
                                $VD_altdial_active =	$row['active'];
                            } else {
                                $Xlast = 9999999999;
                            }
    
                            if (preg_match("/Y/", $VD_altdial_active)) {
                                if ( (preg_match("/Y/", $use_internal_dnc)) || (preg_match("/AREACODE/", $use_internal_dnc)) ) {
                                    if (preg_match("/AREACODE/", $use_internal_dnc)) {
                                        $vdap_phone_areacode = substr($VD_altdial_phone, 0, 3);
                                        $vdap_phone_areacode .= "XXXXXXX";
                                        //$stmtA="SELECT count(*) from vicidial_dnc where phone_number IN('$VD_altdial_phone','$vdap_phone_areacode');";
                                        $astDB->where('phone_number', array("$VD_altdial_phone", "$vdap_phone_areacode"), 'in');
                                    } else {
                                        //$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$VD_altdial_phone';";
                                        $astDB->where('phone_number', $VD_altdial_phone);
                                    }
                                    $rslt = $astDB->get('vicidial_dnc');
                                    $VLAP_dnc_ct = $astDB->getRowCount();
                                    if ($VLAP_dnc_ct > 0) {
                                        $VD_alt_dnc_count = $VLAP_dnc_ct;
                                    }
                                } else {
                                    $VD_alt_dnc_count = 0;
                                }
                                if ( (preg_match("/Y/", $use_campaign_dnc)) || (preg_match("/AREACODE/", $use_campaign_dnc)) ) {
                                    $temp_campaign_id = $campaign;
                                    if (strlen($use_other_campaign_dnc) > 0) {$temp_campaign_id = $use_other_campaign_dnc;}
                                    if (preg_match("/AREACODE/", $use_campaign_dnc)) {
                                        $vdap_phone_areacode = substr($VD_altdial_phone, 0, 3);
                                        $vdap_phone_areacode .= "XXXXXXX";
                                        //$stmtA="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$VD_altdial_phone','$vdap_phone_areacode') and campaign_id='$temp_campaign_id';";
                                        $astDB->where('phone_number', array("$VD_altdial_phone", "$vdap_phone_areacode"), 'in');
                                    } else {
                                        //$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$VD_altdial_phone' and campaign_id='$temp_campaign_id';";
                                        $astDB->where('phone_number', $VD_altdial_phone);
                                    }
                                    $astDB->where('campaign_id', $temp_campaign_id);
                                    $rslt = $astDB->get('vicidial_campaign_dnc');
                                    $VLAP_cdnc_ct = $astDB->getRowCount();
                                    if ($VLAP_cdnc_ct > 0) {
                                        $row=mysqli_fetch_row($rslt);
                                        $VD_alt_dnc_count = ($VD_alt_dnc_count + $VLAP_cdnc_ct);
                                    }
                                }
                                if ($VD_alt_dnc_count < 1) {
                                    if ($alt_dial_phones_count == $Xlast) {
                                        $Xlast = 'LAST';
                                    }
                                    //$stmt = "INSERT INTO vicidial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='HOLD',list_id='$EA_list_id',gmt_offset_now='$EA_gmt_offset_now',state='$EA_state',alt_dial='X$Xlast',user='',priority='15',source='A',vendor_lead_code='$EA_vendor_lead_code';";
                                    $insertData = array(
                                        'lead_id' => $lead_id,
                                        'campaign_id' => $campaign,
                                        'status' => 'HOLD',
                                        'list_id' => $EA_list_id,
                                        'gmt_offset_now' => $EA_gmt_offset_now,
                                        'state' => $EA_state,
                                        'alt_dial' => "X$Xlast",
                                        'user' => '',
                                        'priority' => '15',
                                        'source' => 'A',
                                        'vendor_lead_code' => $EA_vendor_lead_code
                                    );
                                    $rslt = $astDB->insert('vicidial_hopper', $insertData);
                                    $Xlast=9999999999;
                                }
                            }
                        }
                    }
                }
    
                if ($enable_queuemetrics_logging > 0) {
                    ### grab call lead information needed for QM logging
                    //$stmt="SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid from vicidial_auto_calls where lead_id='$lead_id' order by call_time limit 1;";
                    $linkB->where('lead_id', $lead_id);
                    $linkB->orderBy('call_time', 'desc');
                    $rslt = $linkB->getOne('vicidial_auto_calls', 'auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid');
                    $VAC_qm_ct = $linkB->getRowCount();
                    if ($VAC_qm_ct > 0) {
                        $row = $rslt;
                        $auto_call_id	= $row['auto_call_id'];
                        $CLlead_id		= $row['lead_id'];
                        $CLphone_number	= $row['phone_number'];
                        $CLstatus		= $row['status'];
                        $CLcampaign_id	= $row['campaign_id'];
                        $CLphone_code	= $row['phone_code'];
                        $CLalt_dial		= $row['alt_dial'];
                        $CLstage		= $row['stage'];
                        $CLcallerid		= $row['callerid'];
                        $CLuniqueid		= $row['uniqueid'];
                    }
    
                    $CLstage = preg_replace("/.*-/", '', $CLstage);
                    if (strlen($CLstage) < 1) {$CLstage = 0;}
    
                    //$stmt="SELECT count(*) from queue_log where call_id='$MDnextCID' and verb='COMPLETECALLER' and queue='$VDcampaign_id';";
                    $linkB->where('call_id', $MDnextCID);
                    $linkB->where('verb', 'COMPLETECALLER');
                    $linkB->where('queue', $VDcampaign_id);
                    $rslt = $linkB->get('queue_log');
                    $VAC_cc_ct = $linkB->getRowCount();
                    if ($VAC_cc_ct > 0) {
                        $caller_complete = $VAC_cc_ct;
                    }
    
                    if ($caller_complete < 1) {
                        $term_reason = 'AGENT';
                    } else {
                        $term_reason = 'CALLER';
                    }
    
                }
    
                if ($nodeletevdac < 1) {
                    ### delete call record from  vicidial_auto_calls
                    //$stmt = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and campaign_id='$VDcampaign_id' and uniqueid='$uniqueid';";
                    $astDB->where('lead_id', $lead_id);
                    $astDB->where('campaign_id', $VDcampaign_id);
                    $astDB->where('uniqueid', $uniqueid);
                    $rslt = $astDB->delete('vicidial_auto_calls');
                }
    
                $updateData = array(
                    'status' => 'PAUSED',
                    'uniqueid' => 0,
                    'callerid' => '',
                    'channel' => '',
                    'call_server_ip' => '',
                    'last_call_finish' => $NOW_TIME,
                    'comments' => '',
                    'last_state_change' => $NOW_TIME
                );
                if ($VLA_inOUT == 'INBOUND') {
                    //$licf_SQL = ",last_inbound_call_finish='$NOW_TIME'";
                    $updateData['last_inbound_call_finish'] = $NOW_TIME;
                }
                //$stmt = "UPDATE vicidial_live_agents set status='PAUSED',uniqueid=0,callerid='',channel='',call_server_ip='',last_call_finish='$NOW_TIME',comments='',last_state_change='$NOW_TIME' $licf_SQL where user='$user' and server_ip='$server_ip';";
                $astDB->where('user', $user);
                $astDB->where('server_ip', $server_ip);
                $rslt = $astDB->update('vicidial_live_agents', $updateData);
                $error = $astDB->getLastError();
                $retry_count = 0;
                while ( (strlen($error) > 0) and ($retry_count < 9) ) {
                    $astDB->where('user', $user);
                    $astDB->where('server_ip', $server_ip);
                    $rslt = $astDB->update('vicidial_live_agents', $updateData);
                    $error = $astDB->getLastError();
                    $retry_count++;
                }
    
                $affected_rows = $astDB->getRowCount();
                if ($affected_rows > 0) {
                    if ($enable_queuemetrics_logging > 0) {
                        $data4SQL = '';
                        //$stmt="SELECT queuemetrics_phone_environment FROM vicidial_campaigns where campaign_id='$campaign' and queuemetrics_phone_environment!='';";
                        $astDB->where('campaign_id', $campaign);
                        $astDB->where('queuemetrics_phone_environment', '', '!=');
                        $rslt = $astDB->get('vicidial_campaigns', null, 'queuemetrics_phone_environment');
                        $cqpe_ct = $astDB->getRowCount();
                        
                        $insertData = array(
                            'partition' => 'P01',
                            'time_id' => $StarTtimE,
                            'call_id' => 'NONE',
                            'queue' => 'NONE',
                            'agent' => "Agent/$user",
                            'verb' => 'PAUSEALL',
                            'serverid' => $queuemetrics_log_id
                        );
                        if ($cqpe_ct > 0) {
                            $row = $rslt[0];
                            $pe_append = '';
                            if ( ($queuemetrics_pe_phone_append > 0) && (strlen($row['queuemetrics_phone_environment'])>0) ) {
                                $pe_append = "-$qm_extension";
                            }
                            //$data4SQL = ",data4='$row[0]$pe_append'";
                            $data4SQL = "{$row['queuemetrics_phone_environment']}{$pe_append}";
                            $insertData['data4'] = "{$row['queuemetrics_phone_environment']}{$pe_append}";
                        }
    
                        //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$user',verb='PAUSEALL',serverid='$queuemetrics_log_id' $data4SQL;";
                        $rslt = $linkB->insert('queue_log', $insertData);
                        $affected_rows = $linkB->getRowCount();
                    }
                }
            } else {
                if ($enable_queuemetrics_logging > 0) {
                    $CLqueue_position = 1;
                    ### check to see if lead should be alt_dialed
                    //$stmt="SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid,queue_position from vicidial_auto_calls where lead_id='$lead_id' order by call_time desc limit 1;";
                    $astDB->where('lead_id', $lead_id);
                    $astDB->orderBy('call_time', 'desc');
                    $rslt = $astDB->getOne('vicidial_auto_calls', 'auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid,queue_position');
                    $VAC_qm_ct = $astDB->getRowCount();
                    if ($VAC_qm_ct > 0) {
                        $row = $rslt;
                        $auto_call_id = 		$row['auto_call_id'];
                        $CLlead_id = 			$row['lead_id'];
                        $CLphone_number =		$row['phone_number'];
                        $CLstatus = 			$row['status'];
                        $CLcampaign_id = 		$row['campaign_id'];
                        $CLphone_code = 		$row['phone_code'];
                        $CLalt_dial =			$row['alt_dial'];
                        $CLstage =				$row['stage'];
                        $CLcallerid =			$row['callerid'];
                        $CLuniqueid =			$row['uniqueid'];
                        $CLqueue_position =		$row['queue_position'];
                    }
    
                    $CLstage = preg_replace("/XFER|CLOSER|-/", '', $CLstage);
                    if ($CLstage < 0.25) {$CLstage = 0;}
    
                    $data4SQL = '';
                    $data4SS = '';
                    //$stmt="SELECT queuemetrics_phone_environment FROM vicidial_campaigns where campaign_id='$campaign' and queuemetrics_phone_environment!='';";
                    $astDB->where('campaign_id', $campaign);
                    $astDB->where('queuemetrics_phone_environment', '', '!=');
                    $rslt = $astDB->get('vicidial_campaigns', null, 'queuemetrics_phone_environment');
                    $cqpe_ct = $astDB->getRowCount();
                    
                    $insertData = array(
                        'partition' => 'P01',
                        'time_id' => $StarTtimE,
                        'call_id' => $MDnextCID,
                        'queue' => $VDcampaign_id,
                        'agent' => "Agent/$user",
                        'verb' => 'COMPLETEAGENT',
                        'data1' => $CLstage,
                        'data2' => $length_in_sec,
                        'data3' => $CLqueue_position,
                        'serverid' => $queuemetrics_log_id
                    );
                    if ($cqpe_ct > 0) {
                        $row = $rslt[0];
                        $pe_append = '';
                        if ( ($queuemetrics_pe_phone_append > 0) && (strlen($row['queuemetrics_phone_environment']) > 0) ) {
                            $pe_append = "-$qm_extension";
                        }
                        //$data4SQL = ",data4='$row[0]$pe_append'";
                        //$data4SS = "&data4=$row[0]$pe_append";
                        $data4SQL = "{$row['queuemetrics_phone_environment']}{$pe_append}";
                        $insertData['data4'] = "{$row['queuemetrics_phone_environment']}{$pe_append}";
                    }
    
                    //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$MDnextCID',queue='$VDcampaign_id',agent='Agent/$user',verb='COMPLETEAGENT',data1='$CLstage',data2='$length_in_sec',data3='$CLqueue_position',serverid='$queuemetrics_log_id' $data4SQL;";
                    $rslt = $linkB->insert('queue_log', $insertData);
                    $affected_rows = $linkB->getRowCount();
    
                    if ( ($queuemetrics_socket == 'CONNECT_COMPLETE') && (strlen($queuemetrics_socket_url) > 10) ) {
                        $socket_send_data_begin = '?';
                        $socket_send_data = "time_id=$StarTtimE&call_id=$MDnextCID&queue=$VDcampaign_id&agent=Agent/$user&verb=COMPLETEAGENT&data1=$CLstage&data2=$length_in_sec&data3=$CLqueue_position$data4SS";
                        if (preg_match("/\?/",$queuemetrics_socket_url))
                            {$socket_send_data_begin = '&';}
                        ### send queue_log data to the queuemetrics_socket_url ###
                        //if ($DB > 0) {echo "$queuemetrics_socket_url$socket_send_data_begin$socket_send_data<BR>\n";}
                        //$SCUfile = file("$queuemetrics_socket_url$socket_send_data_begin$socket_send_data");
                        //if ($DB > 0) {echo "$SCUfile[0]<BR>\n";}
                    }
                }
    
                if ($nodeletevdac < 1) {
                #	$stmt = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and campaign_id='$campaign' and uniqueid='$uniqueid';";
                    //$stmt = "DELETE from vicidial_auto_calls where lead_id='$lead_id' and campaign_id='$VDcampaign_id' and callerid LIKE \"M%\";";
                    $astDB->where('lead_id', $lead_id);
                    $astDB->where('campaign_id', $VDcampaign_id);
                    $astDB->where('callerid', 'M%', 'like');
                    $rslt = $astDB->delete('vicidial_auto_calls');
                }
    
                //$stmt = "UPDATE vicidial_live_agents set status='PAUSED',uniqueid=0,callerid='',channel='',call_server_ip='',last_call_finish='$NOW_TIME',comments='',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
                $updateData = array(
                    'status' => 'PAUSED',
                    'uniqueid' => 0,
                    'callerid' => '',
                    'channel' => '',
                    'call_server_ip' => '',
                    'last_call_finish' => $NOW_TIME,
                    'comments' => '',
                    'last_state_change' => $NOW_TIME
                );
                $astDB->where('user', $user);
                $astDB->where('server_ip', $server_ip);
                $rslt = $astDB->update('vicidial_live_agents', $updateData);
                $error = $astDB->getLastError();
                $retry_count = 0;
                while ( (strlen($error) > 0) and ($retry_count < 9) ) {
                    $astDB->where('user', $user);
                    $astDB->where('server_ip', $server_ip);
                    $rslt = $astDB->update('vicidial_live_agents', $updateData);
                    $error = $astDB->getLastError();
                    $retry_count++;
                }
    
                $affected_rows = $astDB->getRowCount();
                if ($affected_rows > 0) {
                    if ($enable_queuemetrics_logging > 0) {
                        //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$user',verb='PAUSEALL',serverid='$queuemetrics_log_id' $data4SQL;";
                        $insertData = array(
                            'partition' => 'P01',
                            'time_id' => $StarTtimE,
                            'call_id' => 'NONE',
                            'queue' => 'NONE',
                            'agent' => "Agent/$user",
                            'verb' => 'PAUSEALL',
                            'serverid' => $queuemetrics_log_id,
                            'data4' => $data4SQL
                        );
                        $rslt = $linkB->insert('queue_log', $insertData);
                        $affected_rows = $linkB->getRowCount();
                    }
                }
            }
    
            if ( ($VLA_inOUT == 'AUTO') or ($VLA_inOUT == 'MANUAL') ) {
                $SQLterm = "term_reason='$term_reason',";
    
                if ( (preg_match("/NONE/", $term_reason)) || (preg_match("/NONE/", $VDterm_reason)) || (strlen($VDterm_reason) < 1) ) {
                    ### check to see if lead should be alt_dialed
                    //$stmt="SELECT term_reason,uniqueid,status from vicidial_log where uniqueid='$uniqueid' and lead_id='$lead_id' order by call_date desc limit 1;";
                    $astDB->where('uniqueid', $uniqueid);
                    $astDB->where('lead_id', $lead_id);
                    $astDB->orderBy('call_date', 'desc');
                    $rslt = $astDB->getOne('vicidial_log', 'term_reason,uniqueid,status');
                    $VAC_qm_ct = $astDB->getRowCount();
                    if ($VAC_qm_ct > 0) {
                        $row = $rslt;
                        $VDterm_reason =	$row['term_reason'];
                        $VDvicidial_id =	$row['uniqueid'];
                        $VDstatus =			$row['status'];
                        $VDIDselect =		"VDL_UIDLID $uniqueid $lead_id";
                    }
                    if (preg_match("/CALLER/", $VDterm_reason)) {
                        $SQLterm = "";
                    } else {
                        $SQLterm = "term_reason='AGENT',";
                    }
                }
    
                ##### insert log into vicidial_log_extended for manual VICIDiaL call
                $stmt="INSERT IGNORE INTO vicidial_log_extended SET uniqueid='$uniqueid',server_ip='$server_ip',call_date='$NOW_TIME',lead_id='$lead_id',caller_code='$MDnextCID',custom_call_id='' ON DUPLICATE KEY UPDATE server_ip='$server_ip',call_date='$NOW_TIME',lead_id='$lead_id',caller_code='$MDnextCID';";
                $rslt = $astDB->rawQuery($stmt);
                $affected_rowsX = $astDB->getRowCount();
    
                ### check to see if the vicidial_log record exists, if not, insert it
                $manualVLexists = 0;
                $beginUNIQUEID = preg_replace("/\..*/", "", $uniqueid);
                //$stmt="SELECT status from vicidial_log where lead_id='$lead_id' and user='$user' and phone_number='$phone_number' and uniqueid LIKE \"$beginUNIQUEID%\";";
                $astDB->where('lead_id', $lead_id);
                $astDB->where('user', $user);
                $astDB->where('phone_number', $phone_number);
                $astDB->where('uniqueid', "$beginUNIQUEID%", 'like');
                $rslt = $astDB->get('vicidial_log', null, 'status');
                $manualVLexists = $astDB->getRowCount();
                if ($manualVLexists > 0) {
                    $row = $rslt[0];
                    $VDstatus = $row['status'];
                }
    
                if ($manualVLexists < 1) {
                    ##### insert log into vicidial_log for manual VICIDiaL call
                    //$stmt="INSERT INTO vicidial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed,user_group,alt_dial) values('$uniqueid','$lead_id','$list_id','$campaign','$NOW_TIME','$StarTtimE','DONEM','$phone_code','$phone_number','$user','MANUAL','N','$user_group','$alt_dial');";
                    $insertData = array(
                        'uniqueid' => $uniqueid,
                        'lead_id' => $lead_id,
                        'list_id' => $list_id,
                        'campaign_id' =>$campaign,
                        'call_date' => $NOW_TIME,
                        'start_epoch' => $StarTtimE,
                        'status' => 'DONEM',
                        'phone_code' => $phone_code,
                        'phone_number' => $phone_number,
                        'user' => $user,
                        'comments' => 'MANUAL',
                        'processed' => 'N',
                        'user_group' => $user_group,
                        'alt_dial' => $alt_dial
                    );
                    $rslt = $astDB->insert('vicidial_log', $insertData);
                    $affected_rows = $astDB->getRowCount();
    
                    if ($affected_rows > 0) {
                        $message .= "VICIDiaL_LOG Inserted: $uniqueid|$channel|$NOW_TIME\n$StarTtimE\n";
                    } else {
                        $message .= "LOG NOT ENTERED\n";
                    }
                } else {
                    //$stmt="UPDATE vicidial_log SET uniqueid='$uniqueid' where lead_id='$lead_id' and user='$user' and phone_number='$phone_number' and uniqueid LIKE \"$beginUNIQUEID%\";";
                    $astDB->where('lead_id', $lead_id);
                    $astDB->where('user', $user);
                    $astDB->where('phone_number', $phone_number);
                    $astDB->where('uniqueid', "$beginUNIQUEID%", 'like');
                    $rslt = $astDB->update('vicidial_log', array('uniqueid' => $uniqueid));
                    $affected_rows = $astDB->getRowCount();
                }
    
                ##### update the duration and end time in the vicidial_log table
                $updateData = array(
                    'end_epoch' => $StarTtimE,
                    'length_in_sec' => $length_in_sec
                );
                if ($VDstatus == 'INCALL') {
                    //$vl_statusSQL = ",status='$status_dispo'";
                    $updateData['status'] = $status_dispo;
                }
                //$stmt="UPDATE vicidial_log set $SQLterm end_epoch='$StarTtimE', length_in_sec='$length_in_sec' $vl_statusSQL where uniqueid='$uniqueid' and lead_id='$lead_id' and user='$user' order by call_date desc limit 1;";
                $astDB->where('uniqueid', $uniqueid);
                $astDB->where('lead_id', $lead_id);
                $astDB->where('user', $user);
                $astDB->orderBy('call_date', 'desc');
                $rslt = $astDB->update('vicidial_log', $updateData, 1);
                $affected_rows = $astDB->getRowCount();
    
                if ($affected_rows > 0) {
                    $message .= "$uniqueid\n$channel\n";
                } else {
                    $message .= "LOG NOT ENTERED\n\n";
                }
            } else {
                $SQLterm = "term_reason='$term_reason'";
                $QL_term = '';
    
                if ( (preg_match("/NONE/", $term_reason)) || (preg_match("/NONE/", $VDterm_reason)) || (strlen($VDterm_reason) < 1) ) {
                    ### find out who hung up the call
                    //$stmt="SELECT term_reason,closecallid,queue_position from vicidial_closer_log where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
                    $astDB->where('lead_id', $lead_id);
                    $astDB->where('call_date', $four_hours_ago, '>');
                    $astDB->orderBy('call_date', 'desc');
                    $rslt = $astDB->getOne('vicidial_closer_log', 'term_reason,closecallid,queue_position');
                    $VAC_qm_ct = $astDB->getRowCount();
                    if ($VAC_qm_ct > 0) {
                        $row = $rslt;
                        $VDterm_reason =		$row['term_reason'];
                        $VDvicidial_id =		$row['closecallid'];
                        $VDqueue_position =		$row['queue_position'];
                        $VDIDselect = "VDCL_LID4HOUR $lead_id $four_hours_ago";
                    }
                    if (preg_match("/CALLER/", $VDterm_reason)) {
                        $SQLterm = "";
                    } else {
                        $SQLterm = "term_reason='AGENT'";
                        $QL_term = 'COMPLETEAGENT';
                    }
                }
    
                if (strlen($SQLterm) > 0) {
                    ##### update the duration and end time in the vicidial_log table
                    $stmt="UPDATE vicidial_closer_log set $SQLterm where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
                    $rslt = $astDB->rawQuery($stmt);
                    $affected_rows = $astDB->getRowCount();
                }
    
                if ($enable_queuemetrics_logging > 0) {
                    if ( (strlen($QL_term) > 0) and ($leaving_threeway > 0) )
                        {
                        //$stmt="SELECT count(*) from queue_log where call_id='$MDnextCID' and verb='COMPLETEAGENT' and queue='$VDcampaign_id';";
                        $linkB->where('call_id', $MDnextCID);
                        $linkB->where('verb', 'COMPLETEAGENT');
                        $linkB->where('queue', $VDcampaign_id);
                        $rslt = $linkB->get('queue_log');
                        $VAC_cc_ct = $linkB->getRowCount();
                        if ($VAC_cc_ct > 0) {
                            $agent_complete	= $VAC_cc_ct;
                        }
                        if ($agent_complete < 1) {
                            if (strlen($VDqueue_position) < 1) {
                                ### find out who hung up the call
                                $stmt="SELECT queue_position from vicidial_closer_log where lead_id='$lead_id' and call_date > \"$four_hours_ago\" order by call_date desc limit 1;";
                                $astDB->where('lead_id', $lead_id);
                                $astDB->where('call_date', $four_hours_ago, '>');
                                $astDB->orderBy('call_date', 'desc');
                                $rslt = $astDB->getOne('vicidial_closer_log', 'queue_position');
                                $VAC_qm_ct = $astDB->getRowCount();
                                if ($VAC_qm_ct > 0) {
                                    $row = $rslt;
                                    $VDqueue_position = $row['queue_position'];
                                }
                            }
                            if (strlen($VDqueue_position) < 1) {
                                $VDqueue_position = 1;
                            }
    
                            $data4SQL = '';
                            $data4SS = '';
                            //$stmt="SELECT queuemetrics_phone_environment FROM vicidial_campaigns where campaign_id='$campaign' and queuemetrics_phone_environment!='';";
                            $astDB->where('campaign_id', $campaign);
                            $astDB->where('queuemetrics_phone_environment', '', '!=');
                            $rslt = $astDB->get('vicidial_campaigns', null, 'queuemetrics_phone_environment');
                            $cqpe_ct = $astDB->getRowCount();
                            if ($cqpe_ct > 0) {
                                $row = $rslt[0];
                                $pe_append = '';
                                if ( ($queuemetrics_pe_phone_append > 0) and (strlen($row['queuemetrics_phone_environment'])>0) )
                                    {$pe_append = "-$qm_extension";}
                                $data4SQL = "{$row['queuemetrics_phone_environment']}{$pe_append}";
                                //$data4SS = "&data4=$row[0]$pe_append";
                            }
                            $insertData = array(
                                'partition' => 'P01',
                                'time_id' => $StarTtimE,
                                'call_id' => $MDnextCID,
                                'queue' => $VDcampaign_id,
                                'agent' => "Agent/$user",
                                'verb' => 'COMPLETEAGENT',
                                'data1' => $CLstage,
                                'data2' => $length_in_sec,
                                'data3' => $VDqueue_position,
                                'serverid' => $queuemetrics_log_id,
                                'data4' => $data4SQL
                            );
                            //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$MDnextCID',queue='$VDcampaign_id',agent='Agent/$user',verb='COMPLETEAGENT',data1='$CLstage',data2='$length_in_sec',data3='$VDqueue_position',serverid='$queuemetrics_log_id' $data4SQL;";
                            $rslt = $linkB->insert('queue_log', $insertData);
                            $affected_rows = $linkB->getRowCount();
    
                            if ( ($queuemetrics_socket == 'CONNECT_COMPLETE') and (strlen($queuemetrics_socket_url) > 10) ) {
                                $socket_send_data_begin = '?';
                                $socket_send_data = "time_id=$StarTtimE&call_id=$MDnextCID&queue=$VDcampaign_id&agent=Agent/$user&verb=COMPLETEAGENT&data1=$CLstage&data2=$length_in_sec&data3=$VDqueue_position$data4SS";
                                if (preg_match("/\?/",$queuemetrics_socket_url))
                                    {$socket_send_data_begin='&';}
                                ### send queue_log data to the queuemetrics_socket_url ###
                                //if ($DB > 0) {echo "$queuemetrics_socket_url$socket_send_data_begin$socket_send_data<BR>\n";}
                                //$SCUfile = file("$queuemetrics_socket_url$socket_send_data_begin$socket_send_data");
                                //if ($DB > 0) {echo "$SCUfile[0]<BR>\n";}
                            }
                        }
                    }
                }
            }
        }
    
        $message .= $VDstop_rec_after_each_call . '|' . $extension . '|' . $conf_silent_prefix . '|' . $conf_exten . '|' . $user_abb . "|\n";
    
        ##### if VICIDiaL call and hangup_after_each_call activated, find all recording 
        ##### channels and hang them up while entering info into recording_log and 
        ##### returning filename/recordingID
        if ($VDstop_rec_after_each_call == 1) {
            $local_DEF = 'Local/';
            $local_AMP = '@';
            $total_rec = 0;
            $total_hangup = 0;
            $loop_count = 0;
            //$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and extension = '$conf_exten' order by channel desc;";
            $astDB->where('server_ip', $server_ip);
            $astDB->where('extension', $conf_exten);
            $astDB->orderBy('channel', 'desc');
            $rslt = $astDB->get('live_sip_channels', null, 'channel');
            $rec_list = $astDB->getRowCount();
            while ($rec_list > $loop_count) {
                $row = $rslt[$loop_count];
                if (preg_match("/Local\/$conf_silent_prefix$conf_exten\@/i", $row['channel'])) {
                    $rec_channels[$total_rec] = "{$row['channel']}";
                    $total_rec++;
                } else {
            #		if (preg_match("/$agentchannel/i",$row[0]))
                    if ( ($agentchannel == "{$row['channel']}") or (preg_match('/ASTblind/', $row['channel'])) ) {
                        $donothing = 1;
                    } else {
                        $hangup_channels[$total_hangup] = "{$row['channel']}";
                        $total_hangup++;
                    }
                }
                //if ($format=='debug') {echo "\n<!-- $row[0] -->";}
                $loop_count++; 
            }
    
            $loop_count = 0;
            //$stmt="SELECT channel FROM live_channels where server_ip = '$server_ip' and extension = '$conf_exten' order by channel desc;";
            $astDB->where('server_ip', $server_ip);
            $astDB->where('extension', $conf_exten);
            $astDB->orderBy('channel', 'desc');
            $rslt = $astDB->get('live_channels', null, 'channel');
            $rec_list = $astDB->getRowCount();
            while ($rec_list > $loop_count) {
                $row = $rslt[$loop_count];
                if (preg_match("/Local\/$conf_silent_prefix$conf_exten\@/i", $row['channel'])) {
                    $rec_channels[$total_rec] = "{$row['channel']}";
                    $total_rec++;
                } else {
            #		if (preg_match("/$agentchannel/i",$row[0]))
                    if ( ($agentchannel == "{$row['channel']}") || (preg_match('/ASTblind/', $row['channel'])) ) {
                        $donothing = 1;
                    } else {
                        $hangup_channels[$total_hangup] = "{$row['channel']}";
                        $total_hangup++;
                    }
                }
                //if ($format=='debug') {echo "\n<!-- $row[0] -->";}
                $loop_count++; 
            }
    
    
            ### if a conference call or 3way call was attempted, then hangup all channels except for the agentchannel
            if ( ( ($conf_dialed > 0) || ($hangup_all_non_reserved > 0) ) && ($leaving_threeway < 1) && ($blind_transfer < 1) ) {
                $loop_count = 0;
                while($loop_count < $total_hangup) {
                    if (strlen($hangup_channels[$loop_count]) > 5) {
                        $variable = "Variable: ctuserserverconfleadphone=$loop_count$US$user$US$server_ip$US$conf_exten$US$lead_id$US$phone_number";
    
                        //$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','CH12346$StarTtimE$loop_count','Channel: $hangup_channels[$loop_count]','$variable','','','','','','','','');";
                        $insertData = array(
                            'man_id' => '',
                            'uniqueid' => '',
                            'entry_date' => $NOW_TIME,
                            'status' => 'NEW',
                            'response' => 'N',
                            'server_ip' => $server_ip,
                            'channel' => '',
                            'action' => 'Hangup',
                            'callerid' => "CH12346$StarTtimE$loop_count",
                            'cmd_line_b' => "Channel: $hangup_channels[$loop_count]",
                            'cmd_line_c' => "$variable",
                            'cmd_line_d' => "",
                            'cmd_line_e' => '',
                            'cmd_line_f' => "",
                            'cmd_line_g' => "",
                            'cmd_line_h' => '',
                            'cmd_line_i' => '',
                            'cmd_line_j' => '',
                            'cmd_line_k' => ''
                        );
                        $rslt = $astDB->insert('vicidial_manager', $insertData);
                    }
                    $loop_count++;
                }
            }
    
            $total_recFN = 0;
            $loop_count = 0;
            $filename = $MT;		# not necessary : and cmd_line_f LIKE \"%_$user_abb\"
            //$stmt="SELECT cmd_line_f FROM vicidial_manager where server_ip='$server_ip' and action='Originate' and cmd_line_b = 'Channel: $local_DEF$conf_silent_prefix$conf_exten$local_AMP$ext_context' order by entry_date desc limit $total_rec;";
            $astDB->where('server_ip', $server_ip);
            $astDB->where('action', 'Originate');
            $astDB->where('cmd_line_b', "Channel: $local_DEF$conf_silent_prefix$conf_exten$local_AMP$ext_context");
            $astDB->orderBy('entry_date', 'desc');
            $rslt = $astDB->get('vicidial_manager', $total_rec, 'cmd_line_f');
            $recFN_list = $astDB->getRowCount();
            while ($recFN_list > $loop_count) {
                $row = $rslt[$loop_count];
                $filename[$total_recFN] = preg_replace("/Callerid: /i", "", $row['cmd_line_f']);
                //if ($format=='debug') {echo "\n<!-- $row[0] -->";}
                $total_recFN++;
                $loop_count++; 
            }
    
            $loop_count = 0;
            while($loop_count < $total_rec) {
                if (strlen($rec_channels[$loop_count]) > 5) {
                    //$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','RH12345$StarTtimE$loop_count','Channel: $rec_channels[$loop_count]','','','','','','','','','');";
                    $insertData = array(
                        'man_id' => '',
                        'uniqueid' => '',
                        'entry_date' => $NOW_TIME,
                        'status' => 'NEW',
                        'response' => 'N',
                        'server_ip' => $server_ip,
                        'channel' => '',
                        'action' => 'Hangup',
                        'callerid' => "RH12345$StarTtimE$loop_count",
                        'cmd_line_b' => "Channel: $rec_channels[$loop_count]",
                        'cmd_line_c' => "",
                        'cmd_line_d' => "",
                        'cmd_line_e' => '',
                        'cmd_line_f' => "",
                        'cmd_line_g' => "",
                        'cmd_line_h' => '',
                        'cmd_line_i' => '',
                        'cmd_line_j' => '',
                        'cmd_line_k' => ''
                    );
                    $rslt = $astDB->insert('vicidial_manager', $insertData);
    
                    $message .= "REC_STOP|$rec_channels[$loop_count]|$filename[$loop_count]|";
                    if (strlen($filename[$loop_count]) > 2) {
                        //$stmt="SELECT recording_id,start_epoch,vicidial_id,lead_id FROM recording_log where filename='$filename[$loop_count]'";
                        $astDB->where('filename', $filename[$loop_count]);
                        $rslt = $astDB->get('recording_log', null, 'recording_id,start_epoch,vicidial_id,lead_id');
                        $fn_count = $astDB->getRowCount();
                        if ($fn_count) {
                            $row = $rslt[0];
                            $recording_id = $row['recording_id'];
                            $start_time =	$row['start_epoch'];
                            $vicidial_id =	$row['vicidial_id'];
                            $RClead_id =	$row['lead_id'];
    
                            if ( (strlen($RClead_id) < 1) || ($RClead_id < 1) || ($RClead_id == 'NULL') )
                                {$lidSQL = ",lead_id='$lead_id'";}
                            if (strlen($vicidial_id) < 1) 
                                {$vidSQL = ",vicidial_id='$VDvicidial_id'";}
                            else {
                                if ( (preg_match('/\./', $vicidial_id)) && ($VLA_inOUT == 'INBOUND') ) {
                                    if (!preg_match('/\./',$VDvicidial_id))
                                        {$vidSQL = ",vicidial_id='$VDvicidial_id'";}
    
                                    //if ($WeBRooTWritablE > 0) {
                                    //    $fp = fopen ("./vicidial_debug.txt", "a");
                                    //    fwrite ($fp, "$NOW_TIME|INBND_LOG_3|$uniqueid|$lead_id|$user|$inOUT|$VLA_inOUT|$length_in_sec|$VDterm_reason|$VDvicidial_id|$vicidial_id|$start_epoch|$recording_id|\n");
                                    //    fclose($fp);
                                    //}
                                }
                            }
                            $length_in_sec = ($StarTtimE - $start_time);
                            $length_in_min = ($length_in_sec / 60);
                            $length_in_min = sprintf("%8.2f", $length_in_min);
    
                            $stmt="UPDATE recording_log set end_time='$NOW_TIME',end_epoch='$StarTtimE',length_in_sec=$length_in_sec,length_in_min='$length_in_min' $vidSQL $lidSQL where filename='$filename[$loop_count]' and end_epoch is NULL;";
                            $rslt = $astDB->rawQuery($stmt);
    
                            $message .= "$recording_id|$length_in_min|";
    
                #			$fp = fopen ("./recording_debug_$NOW_DATE$txt", "a");
                #			fwrite ($fp, "$NOW_TIME|RECORD_LOG|$filename[$loop_count]|$uniqueid|$lead_id|$user|$inOUT|$VLA_inOUT|$length_in_sec|$VDterm_reason|$VDvicidial_id|$VDvicidial_id|$vicidial_id|$start_epoch|$recording_id|$VDIDselect|\n");
                #			fclose($fp);
                        } else {
                            $message .= "||";
                        }
                    } else {
                        $message .= "||";
                    }
                    $message .= "\n";
                }
                $loop_count++;
            }
        }
    
        if ($log_no_enter > 0) {
            //$fp = fopen ("./vicidial_debug.txt", "a");
            //fwrite ($fp, "$NOW_TIME|DIAL_LOG_1N|$uniqueid|$lead_id|$user|$inOUT|$VLA_inOUT|$start_epoch|$phone_number|$MDnextCID|$agentchannel|$loop_count|$total_rec|$total_hangup|$VDstop_rec_after_each_call\n");
            //fclose($fp);
        }
    
        if ($log_no_enter < 1) {
            $talk_sec = 0;
            //$StarTtimE = date("U");
            $updateData = array(
                'talk_sec' => $talk_sec,
                'dispo_epoch' => $StarTtimE,
                'uniqueid' => $uniqueid
            );
            //$stmt = "SELECT talk_epoch,talk_sec,wait_sec,wait_epoch,lead_id,comments,dead_epoch from vicidial_agent_log where agent_log_id='$agent_log_id';";
            $astDB->where('agent_log_id', $agent_log_id);
            $rslt = $astDB->get('vicidial_agent_log', null, 'talk_epoch,talk_sec,wait_sec,wait_epoch,lead_id,comments,dead_epoch');
            $VDpr_ct = $astDB->getRowCount();
            if ($VDpr_ct > 0) {
                $row = $rslt[0];
                if ( (preg_match("/NULL/i", $row['talk_epoch'])) || ($row['talk_epoch'] < 1000) ) {
                    //$talk_epochSQL=",talk_epoch='$StarTtimE'";
                    $talk_epochSQL = array(
                        'talk_epoch' => $StarTtimE
                    );
                    $row['talk_epoch'] = $row['wait_epoch'];
                    $updateData = array_merge($updateData, $talk_epochSQL);
                }
                if ( (!preg_match("/NULL/i", $row['dead_epoch'])) && ($row['dead_epoch'] > 1000) ) {
                    $dead_sec = ($StarTtimE - $row['dead_epoch']);
                    if ($dead_sec < 0) {$dead_sec = 0;}
                    //$dead_secSQL=",dead_sec='$dead_sec'";
                    $dead_secSQL = array(
                        'dead_sec' => $dead_sec
                    );
                    $updateData = array_merge($updateData, $dead_secSQL);
                }
                $talk_sec = (($StarTtimE - $row['talk_epoch']) + $row['talk_sec']);
                $updateData['talk_sec'] = $talk_sec;
                if ( ( ($auto_dial_level < 1) || (preg_match('/^M/', $MDnextCID)) ) && (preg_match('/INBOUND_MAN/', $dial_method)) ) {
                    if ( (preg_match("/NULL/i", $row['comments'])) or (strlen($row['comments']) < 1) ) {
                        //$lead_id_commentsSQL .= ",comments='MANUAL'";
                        $lead_id_commentsSQL = array(
                            'comments' => 'MANUAL'
                        );
                    }
                    if ( (preg_match("/NULL/i", $row['lead_id'])) || ($row['lead_id'] < 1) || (strlen($row['lead_id']) < 1) ) {
                        //$lead_id_commentsSQL .= ",lead_id='$lead_id'";
                        $lead_id_commentsSQL = array(
                            'lead_id' => $lead_id
                        );
                    }
                    $updateData = array_merge($updateData, $lead_id_commentsSQL);
                }
            }
            //$stmt="UPDATE vicidial_agent_log set talk_sec='$talk_sec',dispo_epoch='$StarTtimE',uniqueid='$uniqueid' $talk_epochSQL $dead_secSQL $lead_id_commentsSQL where agent_log_id='$agent_log_id';";
            $astDB->where('agent_log_id', $agent_log_id);
            $rslt = $astDB->update('vicidial_agent_log', $updateData);
            $testOutput = $astDB->getLastQuery();
        
            ### update vicidial_carrier_log to match uniqueIDs
            $beginUNIQUEID = preg_replace("/\..*/", "", $uniqueid);
            //$stmt="UPDATE vicidial_carrier_log set uniqueid='$uniqueid' where lead_id='$lead_id' and uniqueid LIKE \"$beginUNIQUEID%\";";
            $astDB->where('lead_id', $lead_id);
            $astDB->where('uniqueid', "$beginUNIQUEID%", 'like');
            $rslt = $astDB->update('vicidial_carrier_log', array('uniqueid' => $uniqueid));
        
            ### if queuemetrics_dispo_pause dispo tag is enabled, log it here
            if (strlen($queuemetrics_dispo_pause) > 0) {
                //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$MDnextCID',queue='NONE',agent='Agent/$user',verb='PAUSEREASON',serverid='$queuemetrics_log_id',data1='$queuemetrics_dispo_pause';";
                $insertData = array(
                    'partition' => 'P01',
                    'time_id' => $StarTtimE,
                    'call_id' => $MDnextCID,
                    'queue' => 'NONE',
                    'agent' => "Agent/$user",
                    'verb' => 'PAUSEREASON',
                    'serverid' => $queuemetrics_log_id,
                    'data1' => $queuemetrics_dispo_pause
                );
                $rslt = $linkB->insert('queue_log', $insertData);
                $affected_rows = $linkB->getRowCount();
            }
        }
        $APIResult = array( "result" => "success", "message" => $message, "test" => $testOutput );
	}
} else {
    $APIResult = array( "result" => "error", "message" => "User ID '{$user}' is NOT logged in." );
}
?>