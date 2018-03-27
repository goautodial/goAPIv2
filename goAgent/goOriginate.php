<?php
 /**
 * @file 		goOriginate.php
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
if (isset($_GET['goQueryCID'])) { $queryCID = $astDB->escape($_GET['goQueryCID']); }
    else if (isset($_POST['goQueryCID'])) { $queryCID = $astDB->escape($_POST['goQueryCID']); }
if (isset($_GET['goOutboundCID'])) { $outbound_cid = $astDB->escape($_GET['goOutboundCID']); }
    else if (isset($_POST['goOutboundCID'])) { $outbound_cid = $astDB->escape($_POST['goOutboundCID']); }
if (isset($_GET['goExten'])) { $exten = $astDB->escape($_GET['goExten']); }
    else if (isset($_POST['goExten'])) { $exten = $astDB->escape($_POST['goExten']); }
if (isset($_GET['goExtContext'])) { $ext_context = $astDB->escape($_GET['goExtContext']); }
    else if (isset($_POST['goExtContext'])) { $ext_context = $astDB->escape($_POST['goExtContext']); }
if (isset($_GET['goExtPriority'])) { $ext_priority = $astDB->escape($_GET['goExtPriority']); }
    else if (isset($_POST['goExtPriority'])) { $ext_priority = $astDB->escape($_POST['goExtPriority']); }
if (isset($_GET['goUseGroupAlias'])) { $usegroupalias = $astDB->escape($_GET['goUseGroupAlias']); }
    else if (isset($_POST['goUseGroupAlias'])) { $usegroupalias = $astDB->escape($_POST['goUseGroupAlias']); }
if (isset($_GET['goPresetName'])) { $preset_name = $astDB->escape($_GET['goPresetName']); }
    else if (isset($_POST['goPresetName'])) { $preset_name = $astDB->escape($_POST['goPresetName']); }
if (isset($_GET['goAccount'])) { $account = $astDB->escape($_GET['goAccount']); }
    else if (isset($_POST['goAccount'])) { $account = $astDB->escape($_POST['goAccount']); }
if (isset($_GET['goAgentDialedNumber'])) { $agent_dialed_number = $astDB->escape($_GET['goAgentDialedNumber']); }
    else if (isset($_POST['goAgentDialedNumber'])) { $agent_dialed_number = $astDB->escape($_POST['goAgentDialedNumber']); }
if (isset($_GET['goAgentDialedType'])) { $agent_dialed_type = $astDB->escape($_GET['goAgentDialedType']); }
    else if (isset($_POST['goAgentDialedType'])) { $agent_dialed_type = $astDB->escape($_POST['goAgentDialedType']); }
if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
    else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }
if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
    else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }
if (isset($_GET['goAlertCID'])) { $alertCID = $astDB->escape($_GET['goAlertCID']); }
    else if (isset($_POST['goAlertCID'])) { $alertCID = $astDB->escape($_POST['goAlertCID']); }
if (isset($_GET['goCallVariables'])) { $call_variables = $astDB->escape($_GET['goCallVariables']); }
    else if (isset($_POST['goCallVariables'])) { $call_variables = $astDB->escape($_POST['goCallVariables']); }

$user = $agent->user;

if ($is_logged_in) {
	if ( (strlen($exten) < 1) || (strlen($channel) < 1) || (strlen($ext_context) < 1) || ( (strlen($queryCID) < 10) && ($alertCID < 1) ) ) {
        $APIResult = array( "result" => "error", "message" => "Exten or queryCID is NOT valid, Originate command not inserted." );
	} else {
        $notallowed = 0;
		if ( (preg_match('/MANUAL/i', $agent_dialed_type)) && ( (preg_match("/^\d860\d\d\d\d$/i", $exten)) || (preg_match("/^860\d\d\d\d$/i", $exten)) ) ) {
            $APIResult = array( "result" => "error", "message" => "You are not allowed to dial into other agent sessions $exten." );
			$notallowed = 1;
		}

        if ($notallowed < 1) {
            if (strlen($outbound_cid) > 1) {
                $outCID = "\"$queryCID\" <$outbound_cid>";
            } else {
                $outCID = "$queryCID";
            }
            if ( ($usegroupalias > 0) && (strlen($account) > 1) ) {
                $RAWaccount = $account;
                $account = "Account: $account";
                $variable = "Variable: _usegroupalias=1";
                if (strlen($call_variables) > 9)
                    {$variable = "Variable: _usegroupalias=1";}   # |$call_variables
            } else {
                $variable = '';
                $account = "Variable: $call_variables";
            }
            //$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','Callerid: $outCID','$account','$variable','','','');";
            $insertData = array(
                'man_id' => '',
                'uniqueid' => '',
                'entry_date' => $NOW_TIME,
                'status' => 'NEW',
                'response' => 'N',
                'server_ip' => $server_ip,
                'channel' => '',
                'action' => 'Originate',
                'callerid' => $queryCID,
                'cmd_line_b' => "Channel: $channel",
                'cmd_line_c' => "Context: $ext_context",
                'cmd_line_d' => "Exten: $exten",
                'cmd_line_e' => "Priority: $ext_priority",
                'cmd_line_f' => "Callerid: $outCID",
                'cmd_line_g' => $account,
                'cmd_line_h' => $variable,
                'cmd_line_i' => '',
                'cmd_line_j' => '',
                'cmd_line_k' => ''
            );
            $rslt = $astDB->insert('vicidial_manager', $insertData);

            $APIResult = array( "result" => "success", "message" => "Originate command sent for Exten $exten Channel $channel on $server_ip.", "account" => $account, "variable" => $variable );
    
            ### log outbound call in the dial log
            //$stmt = "INSERT INTO vicidial_dial_log SET caller_code='$queryCID',lead_id='$lead_id',server_ip='$server_ip',call_date='$NOW_TIME',extension='$exten',channel='$channel',timeout='0',outbound_cid='$outCID',context='$ext_context';";
            $insertData = array(
                'caller_code' => $queryCID,
                'lead_id' => $lead_id,
                'server_ip' => $server_ip,
                'call_date' => $NOW_TIME,
                'extension' => $exten,
                'channel' => $channel,
                'timeout' => '0',
                'outbound_cid' => $outCID,
                'context' => $ext_context
            );
            $rslt = $astDB->insert('vicidial_dial_log', $insertData);
    
            if ($agent_dialed_number > 0) {
                if (strlen($lead_id) < 1) {$lead_id = '0';}
                $customer_hungup = '';
                if ( ($stage > 0) and (preg_match("/3WAY/", $agent_dialed_type)) ) 
                    {$customer_hungup = 'BEFORE_CALL';}
                //$stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id,preset_name,campaign_id,customer_hungup) values('$user','$NOW_TIME','$agent_dialed_type','$server_ip','$exten','$channel','$lead_id','$outbound_cid','$RAWaccount','$preset_name','$campaign','$customer_hungup')";
                $insertData = array(
                    'user' => $user,
                    'call_date' => $NOW_TIME,
                    'call_type' => $agent_dialed_type,
                    'server_ip' => $server_ip,
                    'phone_number' => $exten,
                    'number_dialed' => $channel,
                    'lead_id' => $lead_id,
                    'callerid' => $outbound_cid,
                    'group_alias_id' => $RAWaccount,
                    'preset_name' => $preset_name,
                    'campaign_id' => $campaign,
                    'customer_hungup' => $customer_hungup
                );
                $rslt = $astDB->insert('user_call_log', $insertData);
    
                if (strlen($preset_name) > 0){
                    //$stmt = "SELECT count(*) from vicidial_xfer_stats where campaign_id='$campaign' and preset_name='$preset_name';";
                    $astDB->where('campaign_id', $campaign);
                    $astDB->where('preset_name', $preset_name);
                    $rslt = $astDB->get('vicidial_xfer_stats');
                    $xfer_count = $astDB->getRowCount();
                    if ($xfer_count > 0) {
                        //$stmt = "UPDATE vicidial_xfer_stats SET xfer_count=(xfer_count+1) where campaign_id='$campaign' and preset_name='$preset_name';";
                        $xfer_count++;
                        $astDB->where('campaign_id', $campaign);
                        $astDB->where('preset_name', $preset_name);
                        $rslt = $astDB->update('vicidial_xfer_stats', array('xfer_count' => $xfer_count));
                    } else {
                        //$stmt = "INSERT INTO vicidial_xfer_stats SET campaign_id='$campaign',preset_name='$preset_name',xfer_count='1';";
                        $rslt = $astDB->insert('vicidial_xfer_stats', array('campaign_id' => $campaign, 'preset_name' => $preset_name, 'xfer_count' => 1));
                    }
                }
            }
        }
    }
} else {
    $APIResult = array( "result" => "error", "message" => "User ID '{$user}' is NOT logged in." );
}
?>