<?php
 /**
 * @file 		goPauseCodeSubmit.php
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
$user = $agent->user;

if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goExtension'])) { $extension = $astDB->escape($_GET['goExtension']); }
    else if (isset($_POST['goExtension'])) { $extension = $astDB->escape($_POST['goExtension']); }
if (isset($_GET['goStatus'])) { $status = $astDB->escape($_GET['goStatus']); }
    else if (isset($_POST['goStatus'])) { $status = $astDB->escape($_POST['goStatus']); }
if (isset($_GET['goAgentLogID'])) { $agent_log_id = $astDB->escape($_GET['goAgentLogID']); }
    else if (isset($_POST['goAgentLogID'])) { $agent_log_id = $astDB->escape($_POST['goAgentLogID']); }
if (isset($_GET['goProtocol'])) { $protocol = $astDB->escape($_GET['goProtocol']); }
    else if (isset($_POST['goProtocol'])) { $protocol = $astDB->escape($_POST['goProtocol']); }
if (isset($_GET['goPhoneIP'])) { $phone_ip = $astDB->escape($_GET['goPhoneIP']); }
    else if (isset($_POST['goPhoneIP'])) { $phone_ip = $astDB->escape($_POST['goPhoneIP']); }
if (isset($_GET['goEnableSIPSAKMessages'])) { $enable_sipsak_messages = $astDB->escape($_GET['goEnableSIPSAKMessages']); }
    else if (isset($_POST['goEnableSIPSAKMessages'])) { $enable_sipsak_messages = $astDB->escape($_POST['goEnableSIPSAKMessages']); }
if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
    else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }
if (isset($_GET['goCampaignCID'])) { $campaign_cid = $astDB->escape($_GET['goCampaignCID']); }
    else if (isset($_POST['goCampaignCID'])) { $campaign_cid = $astDB->escape($_POST['goCampaignCID']); }
if (isset($_GET['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_GET['goAutoDialLevel']); }
    else if (isset($_POST['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_POST['goAutoDialLevel']); }

$MT[0] = '';
$errormsg = 0;
$DO_NOT_UPDATE = 0;
$DO_NOT_UPDATE_text = '';

if ($is_logged_in) {
	$row = '';
	$rowx = '';
	if ( (strlen($status) < 1) || (strlen($agent_log_id) < 1) ) {
		$APIResult = array( "result" => "error", "message" => "Either 'agent_log_id' or 'pause_code' submitted is NOT valid." );
	} else {
		### if this is the first pause code entry in a pause session, simply update and log to queue_log
		if ($stage < 1) {
			$stmt="UPDATE vicidial_agent_log set sub_status=\"$status\" where agent_log_id >= '$agent_log_id' and user='$user' and ( (sub_status is NULL) or (sub_status='') )order by agent_log_id limit 2;";
			//$astDB->where('agent_log_id', $agent_log_id, '>=');
			//$astDB->where('user', $user);
			//$astDB->where('sub_status', array(NULL, ''), 'in');
			//$astDB->orderBy('agent_log_id', 'desc');
			//$rslt = $astDB->update('vicidial_agent_log', array('sub_status' => $status), 2);
			$rslt = $astDB->rawQuery($stmt);
			$affected_rows = $astDB->getRowCount();
		} else {
			### this is not the first pause code entry, insert new vicidial_agent_log entry
			$pause_sec = 0;
			//$stmt = "SELECT pause_epoch from vicidial_agent_log where agent_log_id='$agent_log_id';";
			$astDB->where('agent_log_id', $agent_log_id);
			$rslt = $astDB->getOne('vicidial_agent_log', 'pause_epoch');
			$VDpr_ct = $astDB->getRowCount();
			if ($VDpr_ct > 0) {
				$row = $rslt;
				$pause_sec = ($StarTtimE - $row['pause_epoch']);
			}
			//$stmt="UPDATE vicidial_agent_log set pause_sec='$pause_sec' where agent_log_id='$agent_log_id';";
			$astDB->where('agent_log_id', $agent_log_id);
			$rslt = $astDB->update('vicidial_agent_log', array('pause_sec' => $pause_sec));

			$user_group = $agent->user_group;

			//$stmt="INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) values('$user','$server_ip','$NOW_TIME','$campaign','$StarTtimE','0','$StarTtimE','$user_group','$status');";
			$insertData = array(
				'user' => $user,
				'server_ip' => $server_ip,
				'event_time' => $NOW_TIME,
				'campaign_id' => $campaign,
				'pause_epoch' => $StarTtimE,
				'pause_sec' => 0,
				'wait_epoch' => $StarTtimE,
				'user_group' => $user_group,
				'sub_status' => $status
			);
			$rslt = $astDB->insert('vicidial_agent_log', $insertData);
			$affected_rows = $astDB->getRowCount();
			$agent_log_id = $astDB->getInsertId();

			//$stmt="UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id',last_state_change='$NOW_TIME' where user='$user';";
			$astDB->where('user', $user);
			$rslt = $astDB->update('vicidial_live_agents', array('agent_log_id' => $agent_log_id, 'last_state_change' => $NOW_TIME));
			$VLAaffected_rows_update = $astDB->getRowCount();
		}

		### if entry accepted, add a queue_log entry if QM integration is enabled
		if ($affected_rows > 0) {
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
            }
			##### END QUEUEMETRICS LOGGING LOOKUP #####
			###########################################
			if ( ($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (preg_match("/SIP/i", $protocol)) ) {
				$SIPSAK_prefix = 'BK-';
				passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$status\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
			}
			if ($enable_queuemetrics_logging > 0) {
				$pause_call_id = 'NONE';
				if (strlen($campaign_cid) > 12) {$pause_call_id = $campaign_cid;}
				
				$linkB = new MySQLiDB("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass", "$queuemetrics_dbname");

				//$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$pause_call_id',queue='NONE',agent='Agent/$user',verb='PAUSEREASON',serverid='$queuemetrics_log_id',data1='$status';";
				$insertData = array(
					'partition' => 'P01',
					'time_id' => $StarTtimE,
					'call_id' => $pause_call_id,
					'queue' => 'NONE',
					'agent' => "Agent/$user",
					'verb' => 'PAUSEREASON',
					'serverid' => $queuemetrics_log_id,
					'data1' => $status
				);
				$rslt = $linkB->insert('queue_log', $insertData);
				$affected_rows = $linkB->getRowCount();

				$linkB->__destruct();
			}
		}
		$APIResult = array( "result" => "success", "message" => "Pause Code '$status' has been recorded", "agent_log_id" => $agent_log_id );
	}
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>