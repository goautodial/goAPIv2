<?php
 /**
 * @file 		goMonitorAgent.php
 * @brief 		API for Barging
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


if (isset($_GET['goSource'])) { $source = $astDB->escape($_GET['goSource']); }
    else if (isset($_POST['goSource'])) { $source = $astDB->escape($_POST['goSource']); }
if (isset($_GET['goFunction'])) { $function = $astDB->escape($_GET['goFunction']); }
    else if (isset($_POST['goFunction'])) { $function = $astDB->escape($_POST['goFunction']); }
if (isset($_GET['goAgent'])) { $agent = $astDB->escape($_GET['goAgent']); }
    else if (isset($_POST['goAgent'])) { $agent = $astDB->escape($_POST['goAgent']); }
if (isset($_GET['goPhoneLogin'])) { $phone_login = $astDB->escape($_GET['goPhoneLogin']); }
    else if (isset($_POST['goPhoneLogin'])) { $phone_login = $astDB->escape($_POST['goPhoneLogin']); }
if (isset($_GET['goSessionID'])) { $session_id = $astDB->escape($_GET['goSessionID']); }
    else if (isset($_POST['goSessionID'])) { $session_id = $astDB->escape($_POST['goSessionID']); }
if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
    else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }
if (isset($_GET['goUserIP'])) { $ip_address = $astDB->escape($_GET['goUserIP']); }
    else if (isset($_POST['goUserIP'])) { $ip_address = $astDB->escape($_POST['goUserIP']); }

$is_logged_in = check_agent_login($astDB, $agent);
$NOW_TIME = date("Y-m-d H:i:s");
$startMS = microtime();
$action = $stage;

if ($is_logged_in) {
	if(strlen($source) < 2) {
		$result = 'ERROR';
		$result_reason = "Invalid Source";
        $apiresults = array( "result" => "error", "message" => "$result_reason - $source" );
	} else {
        $hasError = 0;
        
        $astDB->where('user', $goUser);
        $astDB->where('active', 'Y');
        $rslt = $astDB->getOne('vicidial_users', 'api_list_restrict,api_allowed_functions,user_group');
		$user_group = $rslt['user_group'];
        
		if ( (!preg_match("/ $function /", $rslt['api_allowed_functions'])) && (!preg_match("/ALL_FUNCTIONS/", $rslt['api_allowed_functions'])) ) {
			$apiresults = array( "result" => "error", "message" => "User does NOT have permission to use this function" );
			$hasError = 1;
		}
        
        if ($hasError < 1) {
            //$stmt="SELECT count(*) from vicidial_users where user='$user' and vdc_agent_api_access='1' and user_level > 6 and active='Y';";
            $astDB->where('user', $goUser);
            $astDB->where('vdc_agent_api_access', '1');
            $astDB->where('user_level', '6', '>');
            $astDB->where('active', 'Y');
            $rslt = $astDB->get('vicidial_users');
            $allowed_user = $astDB->getRowCount();
            if ( ($allowed_user < 1) && ($source != 'queuemetrics') ) {
                $apiresults = array( "result" => "error", "message" => "User does NOT have permission to use blind monitoring" );
                $hasError = 1;
            } else {
                //$stmt="SELECT count(*) from vicidial_conferences where conf_exten='$session_id' and server_ip='$server_ip';";
                $astDB->where('conf_exten', $session_id);
                $astDB->where('server_ip', $server_ip);
                $rslt = $astDB->get('vicidial_conferences');
                $session_exists = $astDB->getRowCount();
    
                if ($session_exists < 1) {
                    $apiresults = array( "result" => "error", "message" => "Invalid Session ID", "session_id" => $session_id, "server_ip" => $server_ip, "user" => $goUser );
                    $hasError = 1;
                } else {
                    //$stmt="SELECT count(*) from phones where login='$phone_login';";
                    $astDB->where('login', $phone_login);
                    $rslt = $astDB->get('phones');
                    $phone_exists = $astDB->getRowCount();
    
                    if ( ($phone_exists < 1) && ($source != 'queuemetrics') ) {
                        $apiresults = array( "result" => "error", "message" => "Invalid Phone Login", "phone_login" => $phone_login, "user" => $goUser );
                        $hasError = 1;
                    } else {
                        if ($source == 'queuemetrics') {
                            //$stmt="SELECT active_voicemail_server from system_settings;";
                            $rslt = $astDB->getOne('system_settings', 'active_voicemail_server');
                            $monitor_server_ip =	$rslt['active_voicemail_server'];
                            $dialplan_number =		$phone_login;
                            $outbound_cid =			'';
                            if (strlen($monitor_server_ip)<7)
                                {$monitor_server_ip = $server_ip;}
                        } else {
                            //$stmt="SELECT dialplan_number,server_ip,outbound_cid from phones where login='$phone_login';";
                            $astDB->where('login', $phone_login);
                            $rslt = $astDB->get('phones', null, 'dialplan_number,server_ip,outbound_cid');
                            $row = $rslt[0];
                            $dialplan_number =	$row['dialplan_number'];
                            $monitor_server_ip =$row['server_ip'];
                            $outbound_cid =		$row['outbound_cid'];
                        }
    
                        $S = '*';
                        $D_s_ip = explode('.', $server_ip);
                        if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
                        if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
                        if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
                        if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
                        if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
                        if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
                        if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
                        if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
                        $monitor_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S";
    
                        $PADuser = sprintf("%08s", $goUser);
                        while (strlen($PADuser) > 8) {$PADuser = substr("$PADuser", 0, -1);}
                        $BMquery = "BM$StarTtimE$PADuser";
                        
                        $rslt = $astDB->getOne('system_settings', 'agent_whisper_enabled');
                        $agent_whisper_enabled = $rslt['agent_whisper_enabled'];
    
                        if ( (preg_match('/MONITOR/', $stage)) || (strlen($stage) < 1) ) {
                            $stage = '0';
                            $wAction = 'listened';
                        }
                        if (preg_match('/BARGE/', $stage)) {
                            $stage = '';
                            $wAction = 'barged';
                        }
                        if (preg_match('/HIJACK/', $stage)) {
                            $stage = '';
                            $wAction = 'hijacking';
                        }
                        if (preg_match('/WHISPER/', $stage)) {
                            $wAction = 'whispered';
                            if ($agent_whisper_enabled == '1') {
                                $stage = '47378218';
                            } else {
                                # WHISPER not enabled
                                $stage = '0';
                            }
                        }
    
                        ### insert a new lead in the system with this phone number
                        //$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$monitor_server_ip','','Originate','$BMquery','Channel: Local/$monitor_dialstring$stage$session_id@default','Context: default','Exten: $dialplan_number','Priority: 1','Callerid: \"VC Blind Monitor\" <$outbound_cid>','','','','','');";
                        $insertData = array(
                            'man_id' => '',
                            'uniqueid' => '',
                            'entry_date' => $NOW_TIME,
                            'status' => 'NEW',
                            'response' => 'N',
                            'server_ip' => $monitor_server_ip,
                            'channel' => '',
                            'action' => 'Originate',
                            'callerid' => $BMquery,
                            'cmd_line_b' => "Channel: Local/$monitor_dialstring$stage$session_id@default",
                            'cmd_line_c' => "Context: default",
                            'cmd_line_d' => "Exten: $dialplan_number",
                            'cmd_line_e' => 'Priority: 1',
                            'cmd_line_f' => "Callerid: \"VC Blind Monitor\" <$outbound_cid>",
                            'cmd_line_g' => '',
                            'cmd_line_h' => '',
                            'cmd_line_i' => '',
                            'cmd_line_j' => '',
                            'cmd_line_k' => ''
                        );
                        $rslt = $astDB->insert('vicidial_manager', $insertData);
                        $affected_rows = $astDB->getRowCount();
                        if ($affected_rows > 0) {
                            $man_id = $astDB->getInsertId();
    
                            //$stmt = "INSERT INTO vicidial_dial_log SET caller_code='$BMquery',lead_id='0',server_ip='$monitor_server_ip',call_date='$NOW_TIME',extension='$dialplan_number',channel='Local/$monitor_dialstring$stage$session_id@default',timeout='0',outbound_cid='\"VC Blind Monitor\" <$outbound_cid>',context='default';";
                            $insertData = array(
                                'caller_code' => $BMquery,
                                'lead_id' => '0',
                                'server_ip' => $monitor_server_ip,
                                'call_date' => $NOW_TIME,
                                'extension' => $dialplan_number,
                                'channel' => "Local/$monitor_dialstring$stage$session_id@default",
                                'timeout' => '0',
                                'outbound_cid' => "\"VC Blind Monitor\" <$outbound_cid>",
                                'context' => 'default'
                            );
                            $rslt = $astDB->insert('vicidial_dial_log', $insertData);
    
                            ##### BEGIN log visit to the vicidial_report_log table #####
                            $endMS = microtime();
                            $startMSary = explode(" ", $startMS);
                            $endMSary = explode(" ", $endMS);
                            $runS = ($endMSary[0] - $startMSary[0]);
                            $runM = ($endMSary[1] - $startMSary[1]);
                            $TOTALrun = ($runS + $runM);
                            //$stmt="INSERT INTO vicidial_report_log set event_date=NOW(), user='$user', ip_address='1.1.1.1', report_name='API Blind Monitor', browser='API', referer='realtime_report.php', notes='$user, $monitor_server_ip, $dialplan_number, $session_id, $phone_login', url='REALTIME BLIND MONITOR',run_time='$TOTALrun';";
                            $insertData = array(
                                'event_date' => 'NOW()',
                                'user' => $goUser,
                                'ip_address' => '1.1.1.1',
                                'report_name' => 'API Blind Monitor',
                                'browser' => 'API',
                                'referer' => 'goMonitorAgent.php',
                                'notes' => "$goUser, $monitor_server_ip, $dialplan_number, $session_id, $phone_login",
                                'url' => 'REALTIME BLIND MONITOR',
                                'run_time' => $TOTALrun
                            );
                            $rslt = $astDB->insert('vicidial_report_log', $insertData);
                            ##### END log visit to the vicidial_report_log table #####
    
                            $message = "Blind monitor has been launched";
                            $data = array(
                                'phone_login' => $phone_login,
                                'dialed_session' => "$monitor_dialstring$stage$session_id",
                                'dialplan_number' => $dialplan_number,
                                'session_id' => $session_id,
                                'man_id' => $man_id,
                                'user' => $goUser
                            );
                        }
                    }
                }
            }
            
            if ($hasError < 1) {
				$log_id = log_action($goDB, $action, $goUser, $ip_address, "{$goUser} {$wAction} in to {$agent}'s call", $user_group);
				
                $apiresults = array( "result" => "success", "message" => $message, "data" => $data );
            }
        }
	}
} else {
    $apiresults = array( "result" => "error", "message" => "User '{$agent}' using phone exten '{$phone_login}' is currently NOT logged in." );
}
?>
