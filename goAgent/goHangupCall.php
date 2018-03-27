<?php
 /**
 * @file 		goHangupCall.php
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
if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goChannel'])) { $channel = $astDB->escape($_GET['goChannel']); }
    else if (isset($_POST['goChannel'])) { $channel = $astDB->escape($_POST['goChannel']); }
if (isset($_GET['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_GET['goAutoDialLevel']); }
    else if (isset($_POST['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_POST['goAutoDialLevel']); }
if (isset($_GET['goCallServerIP'])) { $call_server_ip = $astDB->escape($_GET['goCallServerIP']); }
    else if (isset($_POST['goCallServerIP'])) { $call_server_ip = $astDB->escape($_POST['goCallServerIP']); }
if (isset($_GET['goQueryCID'])) { $queryCID = $astDB->escape($_GET['goQueryCID']); }
    else if (isset($_POST['goQueryCID'])) { $queryCID = $astDB->escape($_POST['goQueryCID']); }
if (isset($_GET['goCallCID'])) { $CallCID = $astDB->escape($_GET['goCallCID']); }
    else if (isset($_POST['goCallCID'])) { $CallCID = $astDB->escape($_POST['goCallCID']); }
if (isset($_GET['goSeconds'])) { $seconds = $astDB->escape($_GET['goSeconds']); }
    else if (isset($_POST['goSeconds'])) { $seconds = $astDB->escape($_POST['goSeconds']); }
if (isset($_GET['goExten'])) { $exten = $astDB->escape($_GET['goExten']); }
    else if (isset($_POST['goExten'])) { $exten = $astDB->escape($_POST['goExten']); }
if (isset($_GET['goNoDeleteVDAC'])) { $nodeletevdac = $astDB->escape($_GET['goNoDeleteVDAC']); }
    else if (isset($_POST['goNoDeleteVDAC'])) { $nodeletevdac = $astDB->escape($_POST['goNoDeleteVDAC']); }
if (isset($_GET['goLogCampaign'])) { $log_campaign = $astDB->escape($_GET['goLogCampaign']); }
    else if (isset($_POST['goLogCampaign'])) { $log_campaign = $astDB->escape($_POST['goLogCampaign']); }
if (isset($_GET['goQMExtension'])) { $qm_extension = $astDB->escape($_GET['goQMExtension']); }
    else if (isset($_POST['goQMExtension'])) { $qm_extension = $astDB->escape($_POST['goQMExtension']); }

$user = $agent->user;

if ($is_logged_in) {
    //$stmt="UPDATE vicidial_live_agents SET external_hangup='0' where user='$user';";
    $astDB->where('user', $user);
    $rslt = $astDB->update('vicidial_live_agents', array( 'external_hangup' => 0 ));

    $channel_live = 1;
    if ( (strlen($channel)<3) or (strlen($queryCID)<15) ) {
        $channel_live = 0;
        //echo "Channel $channel is not valid or queryCID $queryCID is not valid, Hangup command not inserted";
        $APIResult = array( "result" => "error", "message" => "Channel '{$channel}' is NOT valid or queryCID '{$queryCID}' is NOT valid. Hangup command not inserted." );
    } else {
        if (strlen($call_server_ip) < 7) {$call_server_ip = $server_ip;}
        
        if ( ($auto_dial_level > 0) and (strlen($CallCID)>2) and (strlen($exten) > 2) and ($seconds > 0)) {
            //$stmt="SELECT count(*) FROM vicidial_auto_calls where channel='$channel' and callerid='$CalLCID';";
            $astDB->where('channel', $channel);
            $astDB->where('callerid', $CallCID);
            $rslt = $astDB->get('vicidial_auto_calls');
            $auto_calls_ct = $astDB->getRowCount();
            if ($auto_calls_ct == 0) {
                //echo "Call $CalLCID $channel is not live on $call_server_ip, Checking Live Channel...\n";
                $errmsg = "Call {$CallCID} {$channel} is NOT live on {$call_server_ip}, Checking live channel...";
    
                //$stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel' and extension LIKE \"%$exten\";";
                $rslt = $astDB->rawQuery("SELECT count(*) AS cnt FROM live_channels WHERE server_ip = '$call_server_ip' AND channel='$channel' AND extension LIKE \"%$exten\";");
                if ($rslt[0]['cnt'] == 0) {
                    $channel_live = 0;
                    //echo "Channel $channel is not live on $call_server_ip, Hangup command not inserted $rowx[0]\n$stmt\n";
                    $errmsg = "Channel {$channel} is NOT live on {$call_server_ip}, Hangup command NOT inserted.";
                } else {
                    //echo "$stmt\n";
                }
            }
        }
        if ( ($auto_dial_level < 1) and (strlen($stage)>2) and (strlen($channel)>2) and (strlen($exten)>2) ) {
            //$stmt="SELECT count(*) FROM live_channels where server_ip = '$call_server_ip' and channel='$channel' and extension NOT LIKE \"%$exten%\";";
            $rslt = $astDB->rawQuery("SELECT count(*) AS cnt FROM live_channels WHERE server_ip = '$call_server_ip' AND channel='$channel' AND extension NOT LIKE \"%$exten%\";");
            if ($rslt[0]['cnt'] > 0) {
                $channel_live = 0;
                //echo "Channel $channel in use by another agent on $call_server_ip, Hangup command not inserted $rowx[0]\n$stmt\n";
                $errmsg = "Channel {$channel} in use by another agent on {$call_server_ip}, Hangup command not inserted.";
            } else {
                //echo "$stmt\n";
            }
        }
    
        if ($channel_live == 1) {
            if ( (strlen($CallCID) > 15) and ($seconds > 0)) {
                //$stmt="SELECT count(*) FROM vicidial_auto_calls where callerid='$CalLCID';";
                $astDB->where('callerid', $CallCID);
                $rslt = $astDB->get('vicidial_auto_calls');
                $auto_call_ct = $astDB->getRowCount();
                if ($auto_calls_ct > 0) {
                    #############################################
                    ##### START QUEUEMETRICS LOGGING LOOKUP #####
                    //$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_pe_phone_append,queuemetrics_socket,queuemetrics_socket_url FROM system_settings;";
                    $queuemetrics = get_settings('queuemetrics', $astDB);
                    ##### END QUEUEMETRICS LOGGING LOOKUP #####
                    ###########################################
                    if ($queuemetrics->enable_queuemetrics_logging > 0) {
                        $qmDB = new MySQLiDB($queuemetrics->queuemetrics_server_ip, $queuemetrics->queuemetrics_login, $queuemetrics->queuemetrics_pass, $queuemetrics->queuemetrics_dbname);

                        //$stmt="SELECT count(*) from queue_log where call_id='$CalLCID' and verb='CONNECT';";
                        $qmDB->where('call_id', $CallCID);
                        $qmDB->where('verb', 'CONNECT');
                        $rslt = $qmDB->get('queue_log');
                        $VAC_cn_ct = $qmDB->getRowCount();
                        if ($VAC_cn_ct > 0) {
                            $caller_connect	= $VAC_cn_ct;
                        }
                        if ($caller_connect > 0) {
                            $CLqueue_position = '1';
                            ### grab call lead information needed for QM logging
                            //$stmt="SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid,queue_position from vicidial_auto_calls where callerid='$CalLCID' order by call_time limit 1;";
                            $astDB->where('callerid', $CallCID);
                            $astDB->orderBy('call_time');
                            $rslt = $astDB->getOne('vicidial_auto_calls', 'auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid,queue_position');
                            $VAC_qm_ct = $astDB->getRowCount();
                            if ($VAC_qm_ct > 0) {
                                $auto_call_id =			$rslt['auto_call_id'];
                                $CLlead_id =			$rslt['lead_id'];
                                $CLphone_number =		$rslt['phone_number'];
                                $CLstatus =				$rslt['status'];
                                $CLcampaign_id =		$rslt['campaign_id'];
                                $CLphone_code =			$rslt['phone_code'];
                                $CLalt_dial =			$rslt['alt_dial'];
                                $CLstage =				$rslt['stage'];
                                $CLcallerid =			$rslt['callerid'];
                                $CLuniqueid =			$rslt['uniqueid'];
                                $CLqueue_position =		$rslt['queue_position'];
                            }
    
                            $CLstage = preg_replace("/.*-/", '', $CLstage);
                            if (strlen($CLstage) < 1) {$CLstage = 0;}

                            //$stmt="SELECT count(*) from queue_log where call_id='$CalLCID' and verb='COMPLETECALLER' and queue='$CLcampaign_id';";
                            $qmDB->where('call_id', $CallCID);
                            $qmDB->where('verb', 'COMPLETECALLER');
                            $qmDB->where('queue', $CLcampaign_id);
                            $rslt = $qmDB->get('queue_log');
                            $VAC_cc_ct = $qmDB->getRowCount();
                            if ($VAC_cc_ct > 0) {
                                $caller_complete	= $VAC_cc_ct;
                            }
    
                            if ($caller_complete < 1) {
                                $time_id = 0;
                                //$stmt="SELECT time_id from queue_log where call_id='$CalLCID' and verb IN('ENTERQUEUE','CALLOUTBOUND') and queue='$CLcampaign_id';";
                                $qmDB->where('call_id', $CallCID);
                                $qmDB->where('verb', array('ENTERQUEUE', 'CALLOUTBOUND'), 'in');
                                $qmDB->where('queue', $CLcampaign_id);
                                $rslt = $qmDB->get('queue_log', null, 'time_id');
                                $VAC_eq_ct = $qmDB->getRowCount();
                                if ($VAC_eq_ct > 0) {
                                    $time_id	= $VAC_eq_ct;
                                }
                                $StarTtime = date("U");
                                if ($time_id > 100000) 
                                    {$seconds = ($StarTtime - $time_id);}
    
                                $data4SQL = '';
                                $data4SS = '';
                                //$stmt="SELECT queuemetrics_phone_environment FROM vicidial_campaigns where campaign_id='$log_campaign' and queuemetrics_phone_environment!='';";
                                $astDB->where('campaign_id', $log_campaign);
                                $astDB->where('queuemetrics_phone_environment', '', '!=');
                                $rslt = $astDB->get('vicidial_campaigns', null, 'queuemetrics_phone_environment');
                                $cqpe_ct = $astDB->getRowCount();
                                if ($cqpe_ct > 0) {
                                    $row = $rslt[0];
                                    $pe_append = '';
                                    if ( ($queuemetrics->queuemetrics_pe_phone_append > 0) and (strlen($row['queuemetrics_phone_environment']) > 0) )
                                        {$pe_append = "-$qm_extension";}
                                    $data4SQL = ",data4='{$row['queuemeterics_phone_enviromene']}{$pe_append}'";
                                    $data4SS = "&data4={$row['queuemeterics_phone_enviromene']}{$pe_append}";
                                    $data4SQL = array(
                                        'data4' => "{$row['queuemeterics_phone_enviromene']}{$pe_append}"
                                    );
                                }

                                $insertData = array(
                                    'partition' => 'P01',
                                    'time_id' => $StarTtime,
                                    'call_id' => $CallCID,
                                    'queue' => $CLcampaign_id,
                                    'agent' => "Agent/{$user}",
                                    'verb' => 'COMPLETEAGENT',
                                    'data1' => $CLstage,
                                    'data2' => $seconds,
                                    'data3' => $CLqueue_position,
                                    'serverid' => $queuemetrics_log_id
                                );
                                
                                if (is_array($data4SQL)) {
                                    $insertData = array_merge($insertData, $data4SQL);
                                }
                                //$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$CalLCID',queue='$CLcampaign_id',agent='Agent/$user',verb='COMPLETEAGENT',data1='$CLstage',data2='$secondS',data3='$CLqueue_position',serverid='$queuemetrics_log_id' $data4SQL;";
                                $rslt = $qmDB->insert('queue_log', $insertData);
                                $affected_rows = $qmDB->getRowCount();
    
                                if ( ($queuemetrics->queuemetrics_socket == 'CONNECT_COMPLETE') and (strlen($queuemetrics->queuemetrics_socket_url) > 10) ) {
                                    $socket_send_data_begin='?';
                                    $socket_send_data = "time_id=$StarTtime&call_id=$CalLCID&queue=$CLcampaign_id&agent=Agent/$user&verb=COMPLETEAGENT&data1=$CLstage&data2=$secondS&data3=$CLqueue_position$data4SS";
                                    if (preg_match("/\?/",$queuemetrics->queuemetrics_socket_url))
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
    
            //$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Hangup','$queryCID','Channel: $channel','','','','','','','','','');";
            $insertData = array(
                'man_id' => '',
                'uniqueid' => '',
                'entry_date' => $NOW_TIME,
                'status' => 'NEW',
                'response' => 'N',
                'server_ip' => $call_server_ip,
                'channel' => '',
                'action' => 'Hangup',
                'callerid' => $queryCID,
                'cmd_line_b' => "Channel: {$channel}",
                'cmd_line_c' => '',
                'cmd_line_d' => '',
                'cmd_line_e' => '',
                'cmd_line_f' => '',
                'cmd_line_g' => '',
                'cmd_line_h' => '',
                'cmd_line_i' => '',
                'cmd_line_j' => '',
                'cmd_line_k' => ''
            );
            $rslt = $astDB->insert('vicidial_manager', $insertData);
            $errmsg = $astDB->getLastError();
            if (strlen($errmsg) < 1) {
                $APIResult = array( "result" => "success", "message" => "Hangup command sent for Channel {$channel} on {$call_server_ip}" );
            } else {
                $APIResult = array( "result" => "error", "message" => $errmsg );
            }
        } else {
            $APIResult = array( "result" => "error", "message" => $errmsg );
        }
    }
} else {
    $APIResult = array( "result" => "error", "message" => "User ID '{$user}' is NOT logged in." );
}
?>