<?php
 /**
 * @file 		goGetCallsInQueue.php
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
$user_group = $agent->user_group;

if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goConfExten'])) { $conf_exten = $astDB->escape($_GET['goConfExten']); }
    else if (isset($_POST['goConfExten'])) { $conf_exten = $astDB->escape($_POST['goConfExten']); }
if (isset($_GET['goExtension'])) { $extension = $astDB->escape($_GET['goExtension']); }
    else if (isset($_POST['goExtension'])) { $extension = $astDB->escape($_POST['goExtension']); }
if (isset($_GET['goProtocol'])) { $protocol = $astDB->escape($_GET['goProtocol']); }
    else if (isset($_POST['goProtocol'])) { $protocol = $astDB->escape($_POST['goProtocol']); }

if ($is_logged_in) {
	//$stmt="SELECT view_calls_in_queue,grab_calls_in_queue from vicidial_campaigns where campaign_id='$campaign'";
    $astDB->where('campaign_id', $campaign);
    $rslt = $astDB->getOne('vicidial_campaigns', 'view_calls_in_queue,grab_calls_in_queue');
	$view_calls_in_queue =	$rslt['view_calls_in_queue'];
	$grab_calls_in_queue =	$rslt['grab_calls_in_queue'];

	if (preg_match('/NONE/i', $view_calls_in_queue)) {
		echo "Calls in Queue View Disabled for this campaign\n";
        $APIResult = array( "result" => "error", "message" => "Calls in Queue View is disabled for this campaign" );
	} else {
		$view_calls_in_queue = preg_replace('/ALL/', '99', $view_calls_in_queue);
	
		### grab the status and campaign/in-group information for this agent to display
		$ADsql = '';
		//$stmt="SELECT status,campaign_id,closer_campaigns from vicidial_live_agents where user='$user' and server_ip='$server_ip';";
        $astDB->where('user', $user);
        $astDB->where('server_ip', $server_ip);
        $rslt = $astDB->getOne('vicidial_live_agents', 'status,campaign_id,closer_campaigns');
		$Alogin = $rslt['status'];
		$Acampaign = $rslt['campaign_id'];
		$AccampSQL = $rslt['closer_campaigns'];
		$AccampSQL = preg_replace('/\s-/', '', $AccampSQL);
		$AccampSQL = preg_replace('/\s/', "','", $AccampSQL);
		if (preg_match('/AGENTDIRECT/i', $AccampSQL)) {
			$AccampSQL = preg_replace('/AGENTDIRECT/', '', $AccampSQL);
			$ADsql = "or ( (campaign_id LIKE \"%AGENTDIRECT%\") and (agent_only='$user') )";
		}

		### grab the basic data on calls in the queue for this agent
		$stmt="SELECT lead_id,campaign_id,phone_number,uniqueid,UNIX_TIMESTAMP(call_time) AS call_time,call_type,auto_call_id FROM vicidial_auto_calls WHERE status IN('LIVE') AND ( (campaign_id='$Acampaign') OR (campaign_id IN('$AccampSQL')) $ADsql) ORDER BY queue_priority,call_time;";
        $rslt = $astDB->rawQuery($stmt);
		$calls_count = $astDB->getRowCount();
		$loop_count = 0;
		while ($calls_count > $loop_count) {
			$row = $rslt[$loop_count];
			$CQlead_id[$loop_count] =		$row['lead_id'];
			$CQcampaign_id[$loop_count] =	$row['campaign_id'];
			$CQphone_number[$loop_count] =	$row['phone_number'];
			$CQuniqueid[$loop_count] =		$row['uniqueid'];
			$CQcall_time[$loop_count] =		$row['call_time'];
			$CQcall_type[$loop_count] =		$row['call_type'];
			$CQauto_call_id[$loop_count] =	$row['auto_call_id'];
			$loop_count++;
		}

		### re-order the calls to always make sure the AGENTDIRECT calls are first
		$loop_count = 0;
		$o = 0;
		while ($calls_count > $loop_count) {
			if (preg_match('/AGENTDIRECT/i', $CQcampaign_id[$loop_count])) {
				$OQlead_id[$o] =		$CQlead_id[$loop_count];
				$OQcampaign_id[$o] =	$CQcampaign_id[$loop_count];
				$OQphone_number[$o] =	$CQphone_number[$loop_count];
				$OQuniqueid[$o] =		$CQuniqueid[$loop_count];
				$OQcall_time[$o] =		$CQcall_time[$loop_count];
				$OQcall_type[$o] =		$CQcall_type[$loop_count];
				$OQauto_call_id[$o] =	$CQauto_call_id[$loop_count];
				$o++;
			}
			$loop_count++;
		}
		$loop_count = 0;
		while ($calls_count > $loop_count) {
			if (!preg_match('/AGENTDIRECT/i', $CQcampaign_id[$loop_count])) {
				$OQlead_id[$o] =		$CQlead_id[$loop_count];
				$OQcampaign_id[$o] =	$CQcampaign_id[$loop_count];
				$OQphone_number[$o] =	$CQphone_number[$loop_count];
				$OQuniqueid[$o] =		$CQuniqueid[$loop_count];
				$OQcall_time[$o] =		$CQcall_time[$loop_count];
				$OQcall_type[$o] =		$CQcall_type[$loop_count];
				$OQauto_call_id[$o] =	$CQauto_call_id[$loop_count];
				$o++;
			}
			$loop_count++;
		}

//      echo "<TABLE CELLPADDING=0 CELLSPACING=1 BORDER=0 STYLE=\"width:100%;\">";
//		echo "<TR>";
//		echo "<TD BGCOLOR=\"#CCCCCC\"> &nbsp; </TD>";
//		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; PHONE &nbsp; </font></TD>";
//		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; NAME &nbsp; </font></TD>";
//		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; WAIT &nbsp; </font></TD>";
//		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; AGENT &nbsp; </font></TD>";
//		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"> &nbsp; &nbsp; &nbsp; </font></TD>";
//		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; CALL GROUP &nbsp; </font></TD>";
//		echo "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; TYPE &nbsp; </font></TD>";
//		echo "</TR>";

		### Print call information and gather more info on the calls as they are printed
		$callsInQueue = array();
		$loop_count = 0;
		while ( ($calls_count > $loop_count) and ($view_calls_in_queue > $loop_count) ) {
			$call_time = ($StarTtime - $OQcall_time[$loop_count]);
			$Fminutes_M = ($call_time / 60);
			$Fminutes_M_int = floor($Fminutes_M);
			$Fminutes_M_int = intval("$Fminutes_M_int");
			$Fminutes_S = ($Fminutes_M - $Fminutes_M_int);
			$Fminutes_S = ($Fminutes_S * 60);
			$Fminutes_S = round($Fminutes_S, 0);
			if ($Fminutes_S < 10) {$Fminutes_S = "0$Fminutes_S";}
			$call_time = "$Fminutes_M_int:$Fminutes_S";
			$call_handle_method = '';

			if ($OQcall_type[$loop_count] == 'IN') {
				//$stmt="SELECT group_name,group_color from vicidial_inbound_groups where group_id='$OQcampaign_id[$loop_count]';";
                $astDB->where('group_id', $OQcampaign_id[$loop_count]);
                $rslt = $astDB->getOne('vicidial_inbound_groups', 'group_name,group_color');
				$group_name =			$rslt['group_name'];
				$group_color =			$rslt['group_color'];
			}
			//$stmt="SELECT comments,user,first_name,last_name from vicidial_list where lead_id='$OQlead_id[$loop_count]'";
            $astDB->where('lead_id', $OQlead_id[$loop_count]);
            $rslt = $astDB->getOne('vicidial_list', 'comments,user,first_name,last_name');
			$comments =		$rslt['comments'];
			$agent =		$rslt['user'];
			$first_last_name = "{$rslt['first_name']} {$rslt['last_name']}";
			$caller_name =	$first_last_name;

			//$stmt="SELECT full_name from vicidial_users where user='$agent'";
            $astDB->where('user', $agent);
            $rslt = $astDB->getOne('vicidial_users', 'full_name');
			$agent_name_count = $astDB->getRowCount();
			if ($agent_name_count > 0) {
				$agent_name =		$rslt['full_name'];
			} else {
                $agent_name = '';
            }

			if (strlen($caller_name) < 2)
				{$caller_name =	$comments;}
			if (strlen($caller_name) > 30) {$caller_name = substr("$caller_name", 0, 30);}

			if (preg_match("/0$|2$|4$|6$|8$/i", $loop_count)) {$Qcolor = '#FCFCFC';} 
			else{$Qcolor = '#ECECEC';}

            $call_id = $OQauto_call_id[$loop_count];
			if ( (preg_match('/Y/i', $grab_calls_in_queue)) and ($OQcall_type[$loop_count] == 'IN') ) {
				//echo "<TR $Qcolor>";
				//echo "<TD> <a href=\"#\" onclick=\"callinqueuegrab('$OQauto_call_id[$loop_count]');return false;\"><font style=\"font-size: 11px; font-family: sans-serif;\">TAKE CALL</a> &nbsp; </TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQphone_number[$loop_count] &nbsp; </font></TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $caller_name &nbsp; </font></TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $call_time &nbsp; </font></TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $agent - $agent_name &nbsp; </font></TD>";
				//echo "<TD bgcolor=\"$group_color\"><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; &nbsp; &nbsp; </font></TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQcampaign_id[$loop_count] - $group_name &nbsp; </font></TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQcall_type[$loop_count] &nbsp; </font></TD>";
				//echo "</TR>";
                $callsInQueue[$call_id] = array(
                    'phone' => $OQphone_number[$loop_count],
                    'name' => $caller_name,
                    'wait' => $call_time,
                    'agent' => "{$agent} - {$agent_name}",
                    'call_group' => "{$OQcampaign_id[$loop_count]} - {$group_name}",
                    'type' => $OQcall_type[$loop_count],
                    'cangrab' => 1
                );
			} else {
				//echo "<TR $Qcolor>";
				//echo "<TD> &nbsp; </TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQphone_number[$loop_count] &nbsp; </font></TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $caller_name &nbsp; </font></TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $call_time &nbsp; </font></TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $agent - $agent_name &nbsp; </font></TD>";
				//echo "<TD bgcolor=\"$group_color\"><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; &nbsp; &nbsp; </font></TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQcampaign_id[$loop_count] - $group_name &nbsp; </font></TD>";
				//echo "<TD><font style=\"font-size: 11px; font-family: sans-serif;\"> &nbsp; $OQcall_type[$loop_count] &nbsp; </font></TD>";
				//echo "</TR>";
                $callsInQueue[$call_id] = array(
                    'phone' => $OQphone_number[$loop_count],
                    'name' => $caller_name,
                    'wait' => $call_time,
                    'agent' => "{$agent} - {$agent_name}",
                    'call_group' => "{$OQcampaign_id[$loop_count]} - {$group_name}",
                    'type' => $OQcall_type[$loop_count],
                    'cangrab' => 0
                );
			}
			$loop_count++;
		}
		//echo "</TABLE><BR> &nbsp;\n";
        if (count($callsInQueue) > 0) {
            $APIResult = array( "result" => "success", "data" => $callsInQueue );
        } else {
            $APIResult = array( "result" => "notice", "message" => "No Calls in Queue at the moment" );
        }
	}
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>