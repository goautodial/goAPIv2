<?php
 /**
 * @file 		goManualDialLookCall.php
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
if (isset($_GET['goConfExten'])) { $conf_exten = $astDB->escape($_GET['goConfExten']); }
    else if (isset($_POST['goConfExten'])) { $conf_exten = $astDB->escape($_POST['goConfExten']); }
if (isset($_GET['goMDnextCID'])) { $MDnextCID = $astDB->escape($_GET['goMDnextCID']); }
    else if (isset($_POST['goMDnextCID'])) { $MDnextCID = $astDB->escape($_POST['goMDnextCID']); }
if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
    else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }
if (isset($_GET['goAgentLogID'])) { $agent_log_id = $astDB->escape($_GET['goAgentLogID']); }
    else if (isset($_POST['goAgentLogID'])) { $agent_log_id = $astDB->escape($_POST['goAgentLogID']); }
if (isset($_GET['goDialSeconds'])) { $Dial_Seconds = $astDB->escape($_GET['goDialSeconds']); }
    else if (isset($_POST['goDialSeconds'])) { $Dial_Seconds = $astDB->escape($_POST['goDialSeconds']); }
if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
    else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }

$user = $agent->user;

if ($is_logged_in) {
	$MT[0] = '';
	$call_good = 0;
    
	if (strlen($MDnextCID) < 18) {
		//echo "NO\n";
		//echo "MDnextCID $MDnextCID is not valid\n";
        $APIResult = array( "result" => "error", "lookCID" => "NO", "message" => "MDnextCID {$MDnextCID} is NOT valid." );
	} else {
		##### look for the channel in the UPDATED vicidial_manager record of the call initiation
		//$stmt="SELECT uniqueid,channel FROM vicidial_manager where callerid='$MDnextCID' and server_ip='$server_ip' and status IN('UPDATED','DEAD') LIMIT 1;";
        $astDB->where('callerid', $MDnextCID);
        $astDB->where('server_ip', $server_ip);
        $astDB->where('status', array( 'UPDATED', 'DEAD' ), 'in');
        $rslt = $astDB->getOne('vicidial_manager', 'uniqueid,channel');
		$VM_mancall_ct = $astDB->getRowCount();
		if ($VM_mancall_ct > 0) {
            $row = $rslt;
			$uniqueid = $row['uniqueid'];
			$channel = $row['channel'];
			$call_output = array(
                'uniqueid' => $uniqueid,
                'channel' => $channel,
                'MDalert' => ''
            );
			$call_good++;
		} else {
			### after 10 seconds, start checking for call termination in the carrier log
			if ( ($Dial_Seconds > 0) and (preg_match("/0$/", $Dial_Seconds)) ) {
				//$stmt="SELECT uniqueid,channel,end_epoch FROM call_log where caller_code='$MDnextCID' and server_ip='$server_ip' order by start_time desc LIMIT 1;";
                $astDB->where('caller_code', $MDnextCID);
                $astDB->where('server_ip', $server_ip);
                $astDB->orderBy('start_time', 'desc');
                $rslt = $astDB->getOne('call_log', 'uniqueid,channel,end_epoch');
				$VM_mancallX_ct = $astDB->getRowCount();
				if ($VM_mancallX_ct > 0) {
                    $row = $rslt;
					$uniqueid =		$row['uniqueid'];
					$channel =		$row['channel'];
					$end_epoch =	$row['end_epoch'];

					### Check carrier log for error
					//$stmt="SELECT dialstatus,hangup_cause,sip_hangup_cause,sip_hangup_reason FROM vicidial_carrier_log where uniqueid='$uniqueid' and
                    //server_ip='$server_ip' and channel='$channel' and dialstatus IN('BUSY','CHANUNAVAIL','CONGESTION') LIMIT 1;";
                    $astDB->where('uniqueid', $uniqueid);
                    $astDB->where('server_ip', $server_ip);
                    $astDB->where('channel', $channel);
                    $astDB->where('dialstatus', array( 'BUSY', 'CHANUNAVAIL', 'CONGESTION' ), 'in');
                    $rslt = $astDB->getOne('vicidial_carrier_log', 'dialstatus,hangup_cause,sip_hangup_cause,sip_hangup_reason');
					$CL_mancall_ct = $astDB->getRowCount();
					if ($CL_mancall_ct > 0) {
                        $ros = $rslt;
						$dialstatus =			$row['dialstatus'];
						$hangup_cause =			$row['hangup_cause'];
						$sip_hangup_cause =		$row['sip_hangup_cause'];
						$sip_hangup_reason =	$row['sip_hangup_reason'];

						$channel = $dialstatus;
						$hangup_cause_msg = "Cause: " . $hangup_cause . " - " . hangup_cause_description($hangup_cause);
						$sip_hangup_cause_msg = '';
						if (strlen($sip_hangup_cause) > 1) {
							$sip_hangup_cause_msg = "SIP: " . $sip_hangup_cause . " - ";
							if (strlen($sip_hangup_reason) < 2)
								{$sip_hangup_cause_msg .= sip_hangup_cause_description($sip_hangup_cause);}
							else
								{$sip_hangup_cause_msg .= $sip_hangup_reason;}
						}

						//$call_output = "$uniqueid\n$channel\nERROR\n" . $hangup_cause_msg . "\n<br>" . $sip_hangup_cause_msg;
                        $call_output = array(
                            'uniqueid' => $uniqueid,
                            'channel' => $channel,
                            'MDalert' => 'ERROR',
                            'MDerrorDesc' => $hangup_cause_msg,
                            'MDerrorDescSIP' => $sip_hangup_cause_msg
                        );
						$call_good++;

						### Delete call record
						//$stmt="DELETE from vicidial_auto_calls where callerid='$MDnextCID';";
                        $astDB->where('callerid', $MDnextCID);
                        $rslt = $astDB->delete('vicidial_auto_calls');

						//$stmt="UPDATE vicidial_live_agents set ring_callerid='' where ring_callerid='$MDnextCID';";
                        $astDB->where('ring_callerid', $MDnextCID);
                        $rslt = $astDB->update('vicidial_live_agents', array( 'ring_callerid' => '' ));
                    }
                }
            }
        }

		if ($call_good > 0) {
			if ($stage != "YES") {
				$wait_sec = 0;
				$dead_epochSQL = '';
				//$stmt = "SELECT wait_epoch,wait_sec,dead_epoch from vicidial_agent_log where agent_log_id='$agent_log_id';";
                $astDB->where('agent_log_id', $agent_log_id);
                $rslt = $astDB->get('vicidial_agent_log', 'wait_epoch,wait_sec,dead_epoch');
				$VDpr_ct = $astDB->getRowCount();
				if ($VDpr_ct > 0) {
                    $row = $rslt[0];
					$wait_sec = (($StarTtimE - $row['wait_epoch']) + $row['wait_sec']);
					$now_dead_epoch = $row['dead_epoch'];
					if ( ($now_dead_epoch > 1000) and ($now_dead_epoch < $StarTtimE) )
						{$dead_epochSQL = array( 'dead_epoch' => $StarTtimE );}
				}
				//$stmt="UPDATE vicidial_agent_log set wait_sec='$wait_sec',talk_epoch='$StarTtimE',lead_id='$lead_id' $dead_epochSQL where agent_log_id='$agent_log_id';";
                $updateData = array(
                    'wait_sec' => $wait_sec,
                    'talk_epoch' => $StarTtimE,
                    'lead_id' => $lead_id,
                );
                
                if (is_array($dead_epochSQL)) {
                    $updateData = array_merge( $updateData, $dead_epochSQL );
                }
                $astDB->where('agent_log_id', $agent_log_id);
                $rslt = $astDB->update('vicidial_agent_log', $updateData);

				//$stmt="UPDATE vicidial_auto_calls set uniqueid='$uniqueid',channel='$channel' where callerid='$MDnextCID';";
                $astDB->where('callerid', $MDnextCID);
                $rslt = $astDB->update('vicidial_auto_calls', array( 'uniqueid' => $uniqueid, 'channel' => $channel ));
            }
            
			//$stmt="UPDATE call_log set uniqueid='$uniqueid',channel='$channel' where caller_code='$MDnextCID';";
            $astDB->where('caller_code', $MDnextCID);
            $rslt = $astDB->update('call_log', array( 'uniqueid' => $uniqueid, 'channel' => $channel ));

			//echo "$call_output";
            $APIResult = array( "result" => "success", "lookCID" => "", "data" => $call_output );
		} else {
            //echo "NO\n$DiaL_SecondS\n";
            $APIResult = array( "result" => "error", "lookCID" => "NO", "message" => $Dial_Seconds );
        }
	}
} else {
    $APIResult = array( "result" => "error", "message" => "User ID '{$user}' is NOT logged in." );
}
?>
