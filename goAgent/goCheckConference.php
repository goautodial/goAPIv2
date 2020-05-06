<?php
 /**
 * @file 		goCheckConference.php
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
$system_settings = get_settings('system', $astDB);
$phone_settings = get_settings('phone', $astDB, $agent->phone_login, $agent->phone_pass);

if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goClient'])) { $client = $astDB->escape($_GET['goClient']); }
    else if (isset($_POST['goClient'])) { $client = $astDB->escape($_POST['goClient']); }
if (isset($_GET['goConfExten'])) { $conf_exten = $astDB->escape($_GET['goConfExten']); }
    else if (isset($_POST['goConfExten'])) { $conf_exten = $astDB->escape($_POST['goConfExten']); }
if (isset($_GET['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_GET['goAutoDialLevel']); }
    else if (isset($_POST['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_POST['goAutoDialLevel']); }
if (isset($_GET['goCampAgentDisp'])) { $campagentstdisp = $astDB->escape($_GET['goCampAgentDisp']); }
    else if (isset($_POST['goCampAgentDisp'])) { $campagentstdisp = $astDB->escape($_POST['goCampAgentDisp']); }

# default optional vars if not set
if (strlen($ACTION) < 1) {$ACTION = "refresh";}
//if (strlen($client) < 1) {$client = "agc";}
if (strlen($client) < 1) {$client = "vdc";}

$Alogin = 'N';
$RingCalls = 'N';
$DiaLCalls = 'N';

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_TIME = date("Ymd_His");
if (!isset($query_date)) {$query_date = $NOW_DATE;}
$random = (rand(1000000, 9999999) + 10000000);
$error_catched = 0;

$server_ip = $phone_settings->server_ip;
$user = $agent->user;

if( (strlen($session_name) < 12) or (!isset($session_name)) ) {
    $APIResult = array( "result" => "error", "message" => "Session name is invalid or missing" );
} else {
    //$stmt="SELECT count(*) from web_client_sessions where session_name='$session_name' and server_ip='$server_ip';";
    $astDB->where('session_name', $session_name);
    $astDB->where('server_ip', $server_ip);
    $rslt = $astDB->get('web_client_sessions');
    $SNauth = $astDB->getRowCount();
    
    if($SNauth < 1) {
        $APIResult = array( "result" => "error", "message" => "Invalid session_name: $session_name" );
    } else {
        if ($ACTION == 'refresh') {
            $MT[0] = '';
            $channel_live = 1;
            if (strlen($conf_exten) < 1) {
                $channel_live = 0;
                $APIResult = array( "result" => "error", "message" => "Conf Exten is invalid or missing" );
                $error_catched++;
            } else {
                if ($client == 'vdc') {
                    $Acount = 0;
                    $Scount = 0;
                    $AexternalDEAD = 0;
                    $Aagent_log_id = '';
                    $Acallerid = '';
                    $DEADcustomer = 0;
                    $Astatus = '';
                    $Acampaign_id = '';
        
                    ### see if the agent has a record in the vicidial_live_agents table
                    //$stmt="SELECT count(*) from vicidial_live_agents where user='$user' and server_ip='$server_ip';";
                    $astDB->where('user', $user);
                    $astDB->where('server_ip', $server_ip);
                    $rslt = $astDB->get('vicidial_live_agents');
                    $Acount = $astDB->getRowCount();
        
                    ### see if the agent has a record in the vicidial_session_data table
                    //$stmt="SELECT count(*) from vicidial_session_data where user='$user' and server_ip='$server_ip' and session_name='$session_name';";
                    $astDB->where('user', $user);
                    $astDB->where('server_ip', $server_ip);
                    $astDB->where('session_name', $session_name);
                    $rslt = $astDB->get('vicidial_session_data');
                    $Scount = $astDB->getRowCount();
        
                    if ($Acount > 0) {
                        //$stmt="SELECT status,callerid,agent_log_id,campaign_id,lead_id from vicidial_live_agents where user='$user' and server_ip='$server_ip';";
                        $astDB->where('user', $user);
                        $astDB->where('server_ip', $server_ip);
                        $rslt = $astDB->getOne('vicidial_live_agents', 'status,callerid,agent_log_id,campaign_id,lead_id');
                        $Astatus =			$rslt['status'];
                        $Acallerid =		$rslt['callerid'];
                        $Aagent_log_id =	$rslt['agent_log_id'];
                        $Acampaign_id =		$rslt['campaign_id'];
                        $Alead_id =			$rslt['lead_id'];
        
                        $api_manual_dial = 'STANDARD';
                        //$stmt = "SELECT api_manual_dial FROM vicidial_campaigns where campaign_id='$Acampaign_id';";
                        $astDB->where('campaign_id', $Acampaign_id);
                        $rslt = $astDB->get('vicidial_campaigns', null, 'api_manual_dial');
                        $vcc_conf_ct = $astDB->getRowCount();
                        if ($vcc_conf_ct > 0) {
                            $row = $rslt[0];
                            $api_manual_dial =	$row['api_manual_dial'];
                        }
                    }
        
                    ##### BEGIN check on calls in queue, number of active calls in the campaign
                    if ($campagentstdisp == 'YES') {
                        $ADsql = '';
                        ### grab the status of this agent to display
                        //$stmt="SELECT status,campaign_id,closer_campaigns from vicidial_live_agents where user='$user' and server_ip='$server_ip';";
                        $astDB->where('user', $user);
                        $astDB->where('server_ip', $server_ip);
                        $rslt = $astDB->getOne('vicidial_live_agents', 'status,campaign_id,closer_campaigns');
                        $Alogin = $rslt['status'];
                        $Acampaign = $rslt['campaign_id'];
                        $AccampSQL = $rslt['closer_campaigns'];
                        $AccampSQL = preg_replace('/\s\-/', '', $AccampSQL);
                        $AccampSQL = preg_replace('/\s/', "','", $AccampSQL);
                        if (preg_match('/AGENTDIRECT/i', $AccampSQL)) {
                            $AccampSQL = preg_replace('/AGENTDIRECT/i', '', $AccampSQL);
                            $ADsql = "OR ( (campaign_id LIKE \"%AGENTDIRECT%\") AND (agent_only='$user') )";
                        }
        
                        ### grab the number of calls being placed from this server and campaign
                        $rslt = $astDB->rawQuery("SELECT * FROM vicidial_auto_calls WHERE status IN('LIVE') AND ( (campaign_id='$Acampaign') OR (campaign_id IN('$AccampSQL')) $ADsql)");
                        $RingCalls = $astDB->getRowCount();
                        if ($RingCalls > 0) {$RingCalls = "Calls in Queue: $RingCalls";}
                        else {$RingCalls = "Calls in Queue: $RingCalls";}
        
                        ### grab the number of calls being placed from this server and campaign
                        $rslt = $astDB->rawQuery("SELECT * FROM vicidial_auto_calls WHERE status NOT IN('XFER') AND ( (campaign_id='$Acampaign') OR (campaign_id IN('$AccampSQL')) );");
                        $DiaLCalls = $astDB->getRowCount();
                    } else {
                        $Alogin = 'N';
                        $RingCalls = 'N';
                        $DiaLCalls = 'N';
                    }
                    ##### END check on calls in queue, number of active calls in the campaign
        
                    if ($auto_dial_level > 0) {
                        ### update the vicidial_live_agents every second with a new random number so it is shown to be alive
                        //$stmt="UPDATE vicidial_live_agents set random_id='$random' where user='$user' and server_ip='$server_ip';";
                        $astDB->where('user', $user);
                        $astDB->where('server_ip', $server_ip);
                        $rslt = $astDB->update('vicidial_live_agents', array('random_id'=>$random));
                        $errno = $astDB->getLastError();
                        $retry_count = 0;
                        while ( (strlen($errno) > 0) and ($retry_count < 5) ) {
                            $astDB->where('user', $user);
                            $astDB->where('server_ip', $server_ip);
                            $rslt = $astDB->update('vicidial_live_agents', array('random_id'=>$random));
                            $errno = $astDB->getLastError();
                            $retry_count++;
                        }
        
                        ##### BEGIN DEAD logging section #####
                        ### find whether the call the agent is on is hung up
                        //$stmt="SELECT count(*) from vicidial_auto_calls where callerid='$Acallerid';";
                        $astDB->where('callerid', $Acallerid);
                        $rslt = $astDB->get('vicidial_auto_calls');
                        $AcalleridCOUNT = $astDB->getRowCount();
        
                        if ( ($AcalleridCOUNT > 0) and (preg_match("/INCALL/i", $Astatus)) and (preg_match("/^M/", $Acallerid)) ) {
                            $updateNOW_TIME = date("Y-m-d H:i:s");
                            //$stmt="UPDATE vicidial_auto_calls set last_update_time='$updateNOW_TIME' where callerid='$Acallerid';";
                            $astDB->where('callerid', $Acallerid);
                            $rslt = $astDB->update('vicidial_auto_calls', array('last_update_time'=>$updateNOW_TIME));
                        }
        
                        if ( ($AcalleridCOUNT < 1) and (preg_match("/INCALL/i", $Astatus)) and (strlen($Aagent_log_id) > 0) ) {
                            $DEADcustomer++;
                            ### find whether the agent log record has already logged DEAD
                            $rslt = $astDB->rawQuery("SELECT count(*) AS cnt FROM vicidial_agent_log WHERE agent_log_id='$Aagent_log_id' AND ( (dead_epoch IS NOT NULL) OR (dead_epoch > 10000) );");
                            $Aagent_log_idCOUNT = $rslt[0]['cnt'];
                            
                            if ($Aagent_log_idCOUNT < 1) {
                                $NEWdead_epoch = date("U");
                                $deadNOW_TIME = date("Y-m-d H:i:s");
                                //$stmt="UPDATE vicidial_agent_log set dead_epoch='$NEWdead_epoch' where agent_log_id='$Aagent_log_id';";
                                $astDB->where('agent_log_id', $Aagent_log_id);
                                $rslt = $astDB->update('vicidial_agent_log', array('dead_epoch'=>$NEWdead_epoch));
        
                                //$stmt="UPDATE vicidial_live_agents set last_state_change='$deadNOW_TIME' where agent_log_id='$Aagent_log_id';";
                                $astDB->where('agent_log_id', $Aagent_log_id);
                                $rslt = $astDB->update('vicidial_live_agents', array('last_state_change'=>$deadNOW_TIME));
                            }
                        }
                        ##### END DEAD logging section #####
                    } else {
                        ### update the vicidial_live_agents every second with a new random number so it is shown to be alive
                        //$stmt="UPDATE vicidial_live_agents set random_id='$random' where user='$user' and server_ip='$server_ip';";
                        $astDB->where('user', $user);
                        $astDB->where('server_ip', $server_ip);
                        $rslt = $astDB->update('vicidial_live_agents', array('random_id'=>$random));
                        $errno = $astDB->getLastError();
                        $retry_count = 0;
                        while ( (strlen($errno) > 0) and ($retry_count < 5) ) {
                            $astDB->where('user', $user);
                            $astDB->where('server_ip', $server_ip);
                            $rslt = $astDB->update('vicidial_live_agents', array('random_id'=>$random));
                            $errno = $astDB->getLastError();
                            $retry_count++;
                        }
                        ##### BEGIN DEAD logging section #####
                        ### find whether the call the agent is on is hung up
                        //$stmt="SELECT count(*) from vicidial_auto_calls where callerid='$Acallerid';";
                        $astDB->where('callerid', $Acallerid);
                        $rslt = $astDB->get('vicidial_auto_calls');
                        $AcalleridCOUNT = $astDB->getRowCount();
        
                        if ( ($AcalleridCOUNT > 0) and (preg_match("/INCALL/i", $Astatus)) ) {
                            $updateNOW_TIME = date("Y-m-d H:i:s");
                            //$stmt="UPDATE vicidial_auto_calls set last_update_time='$updateNOW_TIME' where callerid='$Acallerid';";
                            $astDB->where('callerid', $Acallerid);
                            $rslt = $astDB->update('vicidial_auto_calls', array('last_update_time'=>$updateNOW_TIME));
                        }
        
                        if ( ($AcalleridCOUNT < 1) and (preg_match("/INCALL/i", $Astatus)) and (strlen($Aagent_log_id) > 0) ) {
                            $DEADcustomer++;
                            ### find whether the agent log record has already logged DEAD
                            $rslt = $astDB->rawQuery("SELECT count(*) AS cnt FROM vicidial_agent_log WHERE agent_log_id='$Aagent_log_id' AND ( (dead_epoch IS NOT NULL) OR (dead_epoch > 10000) );");
                            $Aagent_log_idCOUNT = $rslt[0]['cnt'];
                            
                            if ($Aagent_log_idCOUNT < 1) {
                                $NEWdead_epoch = date("U");
                                $deadNOW_TIME = date("Y-m-d H:i:s");
                                //$stmt="UPDATE vicidial_agent_log set dead_epoch='$NEWdead_epoch' where agent_log_id='$Aagent_log_id';";
                                $astDB->where('agent_log_id', $Aagent_log_id);
                                $rslt = $astDB->update('vicidial_agent_log', array('dead_epoch'=>$NEWdead_epoch));
        
                                //$stmt="UPDATE vicidial_live_agents set last_state_change='$deadNOW_TIME' where agent_log_id='$Aagent_log_id';";
                                $astDB->where('agent_log_id', $Aagent_log_id);
                                $rslt = $astDB->update('vicidial_live_agents', array('last_state_change'=>$NEWdead_epoch));
                            }
                        }
                        ##### END DEAD logging section #####
                    }
        
                    ### grab the API hangup and API dispo fields in vicidial_live_agents
                    //$stmt="SELECT external_hangup,external_status,external_pause,external_dial,external_update_fields,external_update_fields_data,external_timer_action,external_timer_action_message,external_timer_action_seconds,external_dtmf,external_transferconf,external_park,external_timer_action_destination,external_recording from vicidial_live_agents where user='$user' and server_ip='$server_ip';";
                    $astDB->where('user', $user);
                    $astDB->where('server_ip', $server_ip);
                    $rslt = $astDB->getOne('vicidial_live_agents', 'external_hangup,external_status,external_pause,external_dial,external_update_fields,external_update_fields_data,external_timer_action,external_timer_action_message,external_timer_action_seconds,external_dtmf,external_transferconf,external_park,external_timer_action_destination,external_recording');
                    $row = $rslt;
                    $external_hangup =				$row['external_hangup'];
                    $external_status =				$row['external_status'];
                    $external_pause =				$row['external_pause'];
                    $external_dial =				$row['external_dial'];
                    $external_update_fields =		$row['external_update_fields'];
                    $external_update_fields_data =	$row['external_update_fields_data'];
                    $timer_action =					$row['external_timer_action'];
                    $timer_action_message =			$row['external_timer_action_message'];
                    $timer_action_seconds =			$row['external_timer_action_seconds'];
                    $external_dtmf =				$row['external_dtmf'];
                    $external_transferconf =		$row['external_transferconf'];
                    $external_park =				$row['external_park'];
                    $timer_action_destination =		$row['external_timer_action_destination'];
                    $external_recording =			$row['external_recording'];
        
                    $MDQ_count = 0;
                    if ( ($api_manual_dial=='QUEUE') or ($api_manual_dial=='QUEUE_AND_AUTOCALL') ) {
                        //$stmt="SELECT count(*) FROM vicidial_manual_dial_queue where user='$user' and status='READY';";
                        $astDB->where('user', $user);
                        $astDB->where('status', 'READY');
                        $rslt = $astDB->get('vicidial_manual_dial_queue');
                        $mdq_count_record_ct = $astDB->getRowCount();
                        if ($mdq_count_record_ct > 0) {
                            $MDQ_count = $mdq_count_record_ct;
                        }
        
                        if ( ($MDQ_count > 0) and (strlen($external_dial) < 16) and ($Astatus=='PAUSED') and ($Alead_id < 1) ) {
                            //$stmt="SELECT mdq_id,external_dial FROM vicidial_manual_dial_queue where user='$user' and status='READY' order by entry_time limit 1;";
                            $astDB->where('user', $user);
                            $astDB->where('status', 'READY');
                            $astDB->orderBy('entry_time');
                            $rslt = $astDB->getOne('vicidial_manual_dial_queue', 'mdq_id,external_dial');
                            $mdq_record_ct = $astDB->getRowCount();
                            if ($mdq_record_ct > 0) {
                                $row = $rslt;
                                $MDQ_mdq_id =			$row['mdq_id'];
                                $MDQ_external_dial =	$row['external_dial'];
                                $external_dial = $MDQ_external_dial;
        
                                //$stmt="UPDATE vicidial_manual_dial_queue SET status='QUEUE' where mdq_id='$MDQ_mdq_id';";
                                $astDB->where('mdq_id', $MDQ_mdq_id);
                                $rslt = $astDB->update('vicidial_manual_dial_queue', array('status', 'QUEUE'));
                                $UMDQaffected_rows_update = $astDB->getRowCount();
        
                                if ($UMDQaffected_rows_update > 0) {
                                    //$stmt="UPDATE vicidial_live_agents SET external_dial='$MDQ_external_dial' where user='$user' and server_ip='$server_ip';";
                                    $astDB->where('user', $user);
                                    $astDB->where('server_ip', $server_ip);
                                    $rslt = $astDB->update('vicidial_live_agents', array('external_dial'=>$MDQ_external_dial));
                                    $VLAMDQaffected_rows_update = $astDB->getRowCount();
                                }
                            }
                        }
                    }
        
                    if (strlen($external_status) < 1) {$external_status = '::::::::::';}
        
                    $web_epoch = date("U");
                    //$stmt="SELECT UNIX_TIMESTAMP(last_update),UNIX_TIMESTAMP(db_time) from server_updater where server_ip='$server_ip';";
                    $astDB->where('server_ip', $server_ip);
                    $rslt = $astDB->getOne('server_updater', 'UNIX_TIMESTAMP(last_update) AS last_update,UNIX_TIMESTAMP(db_time) AS db_time');
                    $row = $rslt;
                    $server_epoch =	$row['last_update'];
                    $db_epoch =	$row['db_time'];
                    $time_diff = ($server_epoch - $db_epoch);
                    $web_diff = ($db_epoch - $web_epoch);
        
                    ##### check for in-group change details
                    $InGroupChangeDetails = '0|||';
                    $manager_ingroup_set = 0;
                    //$stmt="SELECT count(*) FROM vicidial_live_agents where user='$user' and manager_ingroup_set='SET';";
                    $astDB->where('user', $user);
                    $astDB->where('manager_ingroup_set', 'SET');
                    $rslt = $astDB->get('vicidial_live_agents');
                    $mis_record_ct = $astDB->getRowCount();
                    if ($mis_record_ct > 0) {
                        $manager_ingroup_set = $mis_record_ct;
                    }
                    if ($manager_ingroup_set > 0) {
                        //$stmt="UPDATE vicidial_live_agents SET closer_campaigns=external_ingroups, manager_ingroup_set='Y' where user='$user' and manager_ingroup_set='SET';";
                        $astDB->where('user', $user);
                        $astDB->where('manager_ingroup_set', 'SET');
                        $rslt = $astDB->getOne('vicidial_live_agents', 'external_ingroups');
                        $row = $rslt;
                        $Aexternal_ingroups = $row['external_ingroups'];

                        $astDB->where('user', $user);
                        $astDB->where('manager_ingroup_set', 'SET');
                        $rslt = $astDB->update('vicidial_live_agents', array( 'closer_campaigns' => $Aexternal_ingroups, 'manager_ingroup_set' => 'Y' ));
                        $VLAMISaffected_rows_update = $astDB->getRowCount();
                        if ($VLAMISaffected_rows_update > 0) {
                            $rslt = $astDB->rawQuery("SELECT external_ingroups,external_blended,external_igb_set_user,outbound_autodial,dial_method FROM vicidial_live_agents vla, vicidial_campaigns vc WHERE user='$user' AND manager_ingroup_set='Y' AND vla.campaign_id=vc.campaign_id;");
                            $migs_record_ct = $astDB->getRowCount();
                            if ($migs_record_ct > 0) {
                                $row = $rslt[0];
                                $external_ingroups =		$row['external_ingroups'];
                                $external_blended =			$row['external_blended'];
                                $external_igb_set_user =	$row['external_igb_set_user'];
                                $outbound_autodial =		$row['outbound_autodial'];
                                $dial_method =				$row['dial_method'];
        
                                //$stmt="SELECT full_name FROM vicidial_users where user='$external_igb_set_user';";
                                $astDB->where('user', $external_igb_set_user);
                                $rslt = $astDB->get('vicidial_users', null, 'full_name');
                                $mign_record_ct = $astDB->getRowCount();
                                if ($mign_record_ct > 0) {
                                    $row = $rslt[0];
                                    $external_igb_set_name = $row['full_name'];
                                }
                                
                                $NEWoutbound_autodial = 'N';
                                if ( ($external_blended > 0) and ($dial_method != "INBOUND_MAN") and ($dial_method != "MANUAL") )
                                    {$NEWoutbound_autodial = 'Y';}
        
                                //$stmt="UPDATE vicidial_live_agents SET outbound_autodial='$NEWoutbound_autodial' where user='$user';";
                                $astDB->where('user', $user);
                                $rslt = $astDB->update('vicidial_live_agents', array('outbound_autodial'=>$NEWoutbound_autodial));
                                $VLAMIBaffected_rows_update = $astDB->getRowCount();
        
                                $InGroupChangeDetails = "1|$external_blended|$external_igb_set_user|$external_igb_set_name";
        
                                //$stmt="INSERT INTO vicidial_user_closer_log set user='$user',campaign_id='$Acampaign_id',event_date='$NOW_TIME',blended='$external_blended',closer_campaigns='$external_ingroups',manager_change='$external_igb_set_user';";
                                $insertData = array(
                                    'user' => $user,
                                    'campaign_id' => $Acampaign_id,
                                    'event_date' => $NOW_TIME,
                                    'blended' => $external_blended,
                                    'closer_campaigns' => $external_ingroups,
                                    'manager_change' => $external_igb_set_user
                                );
                                $rslt = $astDB->insert('vicidial_user_closer_log', $insertData);
                            }
                        }
                    }
        
                    ##### grab the shift information the agent
                    //$stmt="SELECT user_group,agent_shift_enforcement_override from vicidial_users where user='$user';";
                    $astDB->where('user', $user);
                    $rslt = $astDB->getOne('vicidial_users', 'user_group,agent_shift_enforcement_override');
                    $row = $rslt;
                    $VU_user_group =						$row['user_group'];
                    $VU_agent_shift_enforcement_override =	$row['agent_shift_enforcement_override'];
        
                    ### Gather timeclock and shift enforcement restriction settings
                    //$stmt="SELECT shift_enforcement,group_shifts from vicidial_user_groups where user_group='$VU_user_group';";
                    $astDB->where('user_group', $VU_user_group);
                    $rslt = $astDB->get('vicidial_user_groups', null, 'shift_enforcement,group_shifts');
                    $row = $rslt[0];
                    $shift_enforcement = $row['shift_enforcement'];
                    $LOGgroup_shiftsSQL = preg_replace('/\s\s/', '', $row['group_shifts']);
                    $LOGgroup_shiftsSQL = preg_replace('/\s/', "','", $LOGgroup_shiftsSQL);
                    //$LOGgroup_shiftsSQL = "shift_id IN('$LOGgroup_shiftsSQL')";
        
                    ### CHECK TO SEE IF AGENT IS WITHIN THEIR SHIFT IF RESTRICTED, IF NOT, OUTPUT ERROR
                    $Ashift_logout = 0;
                    if ( ( (preg_match("/ALL/", $shift_enforcement)) and (!preg_match("/OFF|START/", $VU_agent_shift_enforcement_override)) ) or (preg_match("/ALL/", $VU_agent_shift_enforcement_override)) ) {
                        $shift_ok = 0;
                        if (strlen($LOGgroup_shiftsSQL) < 3) {
                            $Ashift_logout++;
                        } else {
                            $HHMM = date("Hi");
                            $wday = date("w");
        
                            //$stmt="SELECT shift_id,shift_start_time,shift_length,shift_weekdays from vicidial_shifts where $LOGgroup_shiftsSQL order by shift_id";
                            $astDB->where('shift_id', $LOGgroup_shiftsSQL);
                            $astDB->orderBy('shift_id');
                            $rslt = $astDB->get('vicidial_shifts', null, 'shift_id,shift_start_time,shift_length,shift_weekdays');
                            $shifts_to_print = $astDB->getRowCount();

                            if ($shifts_to_print > 0) {
                                foreach ($rslt as $rowx) {
                                    if ($shift_ok < 1) {
                                        $shift_id =			$rowx['shift_id'];
                                        $shift_start_time =	$rowx['shift_start_time'];
                                        $shift_length =		$rowx['shift_length'];
                                        $shift_weekdays =	$rowx['shift_weekdays'];
                
                                        if (preg_match("/$wday/i",$shift_weekdays)) {
                                            $HHshift_length = substr($shift_length, 0, 2);
                                            $MMshift_length = substr($shift_length, 3, 2);
                                            $HHshift_start_time = substr($shift_start_time, 0, 2);
                                            $MMshift_start_time = substr($shift_start_time, 2, 2);
                                            $HHshift_end_time = ($HHshift_length + $HHshift_start_time);
                                            $MMshift_end_time = ($MMshift_length + $MMshift_start_time);
                                            if ($MMshift_end_time > 59) {
                                                $MMshift_end_time = ($MMshift_end_time - 60);
                                                $HHshift_end_time++;
                                            }
                                            if ($HHshift_end_time > 23)
                                                {$HHshift_end_time = ($HHshift_end_time - 24);}
                                            $HHshift_end_time = sprintf("%02s", $HHshift_end_time);	
                                            $MMshift_end_time = sprintf("%02s", $MMshift_end_time);	
                                            $shift_end_time = "$HHshift_end_time$MMshift_end_time";
                
                                            if ( 
                                                ( ($HHMM >= $shift_start_time) and ($HHMM < $shift_end_time) ) or
                                                ( ($HHMM < $shift_start_time) and ($HHMM < $shift_end_time) and ($shift_end_time <= $shift_start_time) ) or
                                                ( ($HHMM >= $shift_start_time) and ($HHMM >= $shift_end_time) and ($shift_end_time <= $shift_start_time) )
                                                )
                                                {$shift_ok++;}
                                        }
                                    } else {
                                        break;
                                    }
                                }
                            }
        
                            if ($shift_ok < 1)
                                {$Ashift_logout++;}
                        }
                    }
        
        
                    if ( ( ($time_diff > 8) or ($time_diff < -8) or ($web_diff > 8) or ($web_diff < -8) ) and (preg_match("/0\$/i", $StarTtime)) ) 
                        {$Alogin = 'TIME_SYNC';}
                    if ( ($Acount < 1) or ($Scount < 1) )
                        {$Alogin = 'DEAD_VLA|'.$Scount.'|'.$Acount;}
                    if ($AexternalDEAD > 0) 
                        {$Alogin = 'DEAD_EXTERNAL';}
                    if ($Ashift_logout > 0)
                        {$Alogin = 'SHIFT_LOGOUT';}
                    if ($external_pause == 'LOGOUT') {
                        $Alogin = 'API_LOGOUT';
                        $external_pause = '';
                    }
        
                    $confOutput = array(
                        'datetime' => $NOW_TIME,
                        'unixtime' => $StarTtime,
                        'logged_in' => $Alogin,
                        'camp_calls' => $RingCalls,
                        'status' => (isset($Astatus)) ? $Astatus : '',
                        'dial_calls' => (isset($DiaLCalls)) ? $DiaLCalls : 'N',
                        'api_hangup' => (isset($external_hangup)) ? $external_hangup : '',
                        'api_status' => $external_status,
                        'api_pause' => (isset($external_pause)) ? $external_pause : '',
                        'api_dial' => (isset($external_dial)) ? $external_dial : '',
                        'dead_call' => $DEADcustomer,
                        'ingroup_change' => $InGroupChangeDetails,
                        'api_fields' => (isset($external_update_fields)) ? $external_update_fields : '',
                        'api_fields_data' => (isset($external_update_fields_data)) ? $external_update_fields_data : '',
                        'api_timer_action' => (isset($timer_action)) ? $timer_action : '',
                        'api_timer_message' => (isset($timer_action_message)) ? $timer_action_message : '',
                        'api_timer_seconds' => (isset($timer_action_seconds)) ? $timer_action_seconds : '',
                        'api_dtmf' => (isset($external_dtmf)) ? $external_dtmf : '',
                        'api_transferconf' => (isset($external_transferconf)) ? $external_transferconf : '',
                        'api_park' => (isset($external_park)) ? $external_park : '',
                        'api_timer_destination' => (isset($timer_action_destination)) ? $timer_action_destination : '',
                        'api_manual_dial_queue' => $MDQ_count,
                        'api_recording' => (isset($external_recording)) ? $external_recording : ''
                    );
        
                    if (strlen($timer_action) > 3) {
                        //$stmt="UPDATE vicidial_live_agents SET external_timer_action='' where user='$user';";
                        $astDB->where('user', $user);
                        $rslt = $astDB->update('vicidial_live_agents', array('external_timer_action'=>''));
                        $VLAETAaffected_rows_update = $astDB->getRowCount();
                    }
                }
                
                $total_conf = 0;
                //$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and extension = '$conf_exten';";
                $astDB->where('server_ip', $server_ip);
                $astDB->where('extension', $conf_exten);
                $rslt = $astDB->get('live_sip_channels', null, 'channel');
                if ($astDB->getRowCount() > 0) {$sip_list = $astDB->getRowCount();}
            #	echo "$sip_list|";
                $loop_count = 0;
                foreach ($rslt as $row) {
                    $loop_count++; $total_conf++;
                    $ChannelA[$total_conf] = "{$row['channel']}";
                }
                //$stmt="SELECT channel FROM live_channels where server_ip = '$server_ip' and extension = '$conf_exten';";
                $astDB->where('server_ip', $server_ip);
                $astDB->where('extension', $conf_exten);
                $rslt = $astDB->get('live_channels', null, 'channel');
                if ($astDB->getRowCount() > 0) {$channels_list = $astDB->getRowCount();}
            #	echo "$channels_list|";
                $loop_count = 0;
                foreach ($rslt as $row) {
                    $loop_count++; $total_conf++;
                    $ChannelA[$total_conf] = "{$row['channel']}";
                }
            }
            
            if ($error_catched < 1) {
                $channels_list = ($channels_list + $sip_list);
            
                $counter = 0;
                $countecho = '';
                while($total_conf > $counter) {
                    $counter++;
                    $countecho = "$countecho$ChannelA[$counter] ~";
                #	echo "$ChannelA[$counter] ~";
                }
                
                $APIResult = array( "result" => "success", "data" => array( "conf_output" => $confOutput, "channels_list" => $channels_list, "count_echo" => $countecho ) );
            }
        }
        
        if ($ACTION == 'register') {
            $MT[0] = '';
            $channel_live = 1;
            if ( (strlen($conf_exten)<1) || (strlen($exten)<1) ) {
                $channel_live = 0;
                $APIResult = array( "result" => "error", "message" => "Conf Exten $conf_exten is not valid or Exten $exten is not valid" );
                $error_catched++;
            } else {
                //$stmt="UPDATE conferences set extension='$exten' where server_ip = '$server_ip' and conf_exten = '$conf_exten';";
                $astDB->where('server_ip', $server_ip);
                $astDB->where('conf_exten', $conf_exten);
                $rslt = $astDB->update('conferences', array('extension' => $exten) );
            }
            
            if ($error_catched < 1) {
                $APIResult = array( "result" => "success", "message" => "Conference $conf_exten has been registered to $exten" );
            }
        }
    }
}
?>