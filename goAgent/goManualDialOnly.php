<?php
 /**
 * @file 		goManualDialOnly.php
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
    if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
        else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }
    if (isset($_GET['goPhoneCode'])) { $phone_code = $astDB->escape($_GET['goPhoneCode']); }
        else if (isset($_POST['goPhoneCode'])) { $phone_code = $astDB->escape($_POST['goPhoneCode']); }
    if (isset($_GET['goPhoneNumber'])) { $phone_number = $astDB->escape($_GET['goPhoneNumber']); }
        else if (isset($_POST['goPhoneNumber'])) { $phone_number = $astDB->escape($_POST['goPhoneNumber']); }
    if (isset($_GET['goConfExten'])) { $conf_exten = $astDB->escape($_GET['goConfExten']); }
        else if (isset($_POST['goConfExten'])) { $conf_exten = $astDB->escape($_POST['goConfExten']); }
    if (isset($_GET['goUseGroupAlias'])) { $usegroupalias = $astDB->escape($_GET['goUseGroupAlias']); }
        else if (isset($_POST['goUseGroupAlias'])) { $usegroupalias = $astDB->escape($_POST['goUseGroupAlias']); }
    if (isset($_GET['goAgentDialedType'])) { $agent_dialed_type = $astDB->escape($_GET['goAgentDialedType']); }
        else if (isset($_POST['goAgentDialedType'])) { $agent_dialed_type = $astDB->escape($_POST['goAgentDialedType']); }
    if (isset($_GET['goAgentDialedNumber'])) { $agent_dialed_number = $astDB->escape($_GET['goAgentDialedNumber']); }
        else if (isset($_POST['goAgentDialedNumber'])) { $agent_dialed_number = $astDB->escape($_POST['goAgentDialedNumber']); }
    if (isset($_GET['goExtContext'])) { $ext_context = $astDB->escape($_GET['goExtContext']); }
        else if (isset($_POST['goExtContext'])) { $ext_context = $astDB->escape($_POST['goExtContext']); }
    if (isset($_GET['goDialTimeout'])) { $dial_timeout = $astDB->escape($_GET['goDialTimeout']); }
        else if (isset($_POST['goDialTimeout'])) { $dial_timeout = $astDB->escape($_POST['goDialTimeout']); }
    if (isset($_GET['goDialPrefix'])) { $dial_prefix = $astDB->escape($_GET['goDialPrefix']); }
        else if (isset($_POST['goDialPrefix'])) { $dial_prefix = $astDB->escape($_POST['goDialPrefix']); }
    if (isset($_GET['goCampaignCID'])) { $campaign_cid = $astDB->escape($_GET['goCampaignCID']); }
        else if (isset($_POST['goCampaignCID'])) { $campaign_cid = $astDB->escape($_POST['goCampaignCID']); }
    if (isset($_GET['goOmitPhoneCode'])) { $omit_phone_code = $astDB->escape($_GET['goOmitPhoneCode']); }
        else if (isset($_POST['goOmitPhoneCode'])) { $omit_phone_code = $astDB->escape($_POST['goOmitPhoneCode']); }
    if (isset($_GET['goAccount'])) { $account = $astDB->escape($_GET['goAccount']); }
        else if (isset($_POST['goAccount'])) { $account = $astDB->escape($_POST['goAccount']); }
    if (isset($_GET['goDialMethod'])) { $dial_method = $astDB->escape($_GET['goDialMethod']); }
        else if (isset($_POST['goDialMethod'])) { $dial_method = $astDB->escape($_POST['goDialMethod']); }
    if (isset($_GET['goAgentLogID'])) { $agent_log_id = $astDB->escape($_GET['goAgentLogID']); }
        else if (isset($_POST['goAgentLogID'])) { $agent_log_id = $astDB->escape($_POST['goAgentLogID']); }
    if (isset($_GET['goSecurity'])) { $security = $astDB->escape($_GET['goSecurity']); }
        else if (isset($_POST['goSecurity'])) { $security = $astDB->escape($_POST['goSecurity']); }
    if (isset($_GET['goQMExtension'])) { $qm_extension = $astDB->escape($_GET['goQMExtension']); }
        else if (isset($_POST['goQMExtension'])) { $qm_extension = $astDB->escape($_POST['goQMExtension']); }
    if (isset($_GET['goOldCID'])) { $old_CID = $astDB->escape($_GET['goOldCID']); }
        else if (isset($_POST['goOldCID'])) { $old_CID = $astDB->escape($_POST['goOldCID']); }


	$MT[0] = '';
	$channel_live = 1;
    $error_catcher = 0;
	$data = array();
	$WeBRooTWritablE = 0;
	if ( (strlen($conf_exten)<1) || (strlen($campaign)<1) || (strlen($ext_context)<1) || (strlen($phone_number)<1) || (strlen($lead_id)<1) ) {
		$channel_live=0;
		$message  = "CALL NOT PLACED: Either conf_exten, campaign or ext_context is NOT valid";
        $APIResult = array( "result" => "error", "message" => $message );
	} else {
		##### clear out last call to same lead if exists #####
		if (strlen($old_CID) > 16) {
			$old_lead_id = substr($old_CID, -10);
			$old_lead_id = ($old_lead_id + 0);
			if ($lead_id == "$old_lead_id") {
				//$stmt="DELETE FROM vicidial_auto_calls where callerid='$old_CID' and lead_id='$old_lead_id';";
                $astDB->where('callerid', $old_CID);
                $astDB->where('lead_id', $old_lead_id);
                $rslt = $astDB->delete('vicidial_auto_calls');
            }
        }

		##### grab number of calls today in this campaign and increment
		//$stmt="SELECT calls_today,extension FROM vicidial_live_agents WHERE user='$user' and campaign_id='$campaign';";
        $astDB->where('user', $user);
        $astDB->where('campaign_id', $campaign);
        $rslt = $astDB->get('vicidial_live_agents', null, 'calls_today,extension');
		$vla_cc_ct = $astDB->getRowCount();
		if ($vla_cc_ct > 0) {
			$row = $rslt[0];
			$calls_today =	$row['calls_today'];
			$eac_phone =	$row['extension'];
		} else {$calls_today = '0';}
		$calls_today++;


		### check for manual dial filter and extension append settings in campaign
		$use_eac = 0;
		$use_custom_cid = 0;
		//$stmt = "SELECT manual_dial_filter,use_internal_dnc,use_campaign_dnc,use_other_campaign_dnc,extension_appended_cidname FROM vicidial_campaigns where campaign_id='$campaign';";
        $astDB->where('campaign_id', $campaign);
        $rslt = $astDB->get('vicidial_campaigns', null, 'manual_dial_filter,use_internal_dnc,use_campaign_dnc,use_other_campaign_dnc,extension_appended_cidname');
		$vcstgs_ct = $astDB->getRowCount();
		if ($vcstgs_ct > 0) {
			$row = $rslt[0];
			$manual_dial_filter =			$row['manual_dial_filter'];
			$use_internal_dnc =				$row['use_internal_dnc'];
			$use_campaign_dnc =				$row['use_campaign_dnc'];
			$use_other_campaign_dnc =		$row['use_other_campaign_dnc'];
			$extension_appended_cidname =	$row['extension_appended_cidname'];
			if ($extension_appended_cidname == 'Y')
				{$use_eac++;}
		}

		### BEGIN check phone filtering for DNC or camplists if enabled ###
		if (preg_match("/DNC/",$manual_dial_filter)) {
			if (preg_match("/AREACODE/",$use_internal_dnc)) {
				$phone_number_areacode = substr($phone_number, 0, 3);
				$phone_number_areacode .= "XXXXXXX";
				//$stmt="SELECT count(*) from vicidial_dnc where phone_number IN('$phone_number','$phone_number_areacode');";
                $astDB->where('phone_number', array($phone_number, $phone_number_areacode), 'in');
			} else {
                //$stmt="SELECT count(*) FROM vicidial_dnc where phone_number='$phone_number';";
                $astDB->where('phone_number', $phone_number);
            }
            $rslt = $astDB->get('vicidial_dnc');
			$dnc_cnt = $astDB->getRowCount();
			if ($dnc_cnt > 0) {
                $APIResult = array( "result" => "error", "message" => "CALL NOT PLACED: DNC Number" );
                $error_catcher++;
			}
			if ( (preg_match("/Y/", $use_campaign_dnc)) or (preg_match("/AREACODE/", $use_campaign_dnc)) ) {
				//$stmt="SELECT use_other_campaign_dnc from vicidial_campaigns where campaign_id='$campaign';";
                $astDB->where('campaign_id', $campaign);
                $rslt = $astDB->getOne('vicidial_campaigns', null, 'use_other_campaign_dnc');
				$use_other_campaign_dnc = $rslt['use_other_campaign_dnc'];
				$temp_campaign_id = $campaign;
				if (strlen($use_other_campaign_dnc) > 0) {$temp_campaign_id = $use_other_campaign_dnc;}

				if (preg_match("/AREACODE/", $use_campaign_dnc)) {
					$phone_number_areacode = substr($phone_number, 0, 3);
					$phone_number_areacode .= "XXXXXXX";
					//$stmt="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$phone_number','$phone_number_areacode') and campaign_id='$temp_campaign_id';";
                    $astDB->where('phone_number', array($phone_number, $phone_number_areacode), 'in');
				} else {
                    //$stmt="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$phone_number' and campaign_id='$temp_campaign_id';";
                    $astDB->where('phone_number', $phone_number);
                }
                $astDB->where('campaign_id', $temp_campaign_id);
                $rslt = $astDB->get('vicidial_campaign_dnc');
				$camp_dnc_cnt = $astDB->getRowCount();
				if ($camp_dnc_cnt > 0) {
                    $APIResult = array( "result" => "error", "message" => "CALL NOT PLACED: DNC Number" );
					$error_catcher++;
				}
            }
        }
		if (preg_match("/CAMPLISTS/", $manual_dial_filter)) {
			//$stmt="SELECT list_id,active from vicidial_lists where campaign_id='$campaign'";
            $astDB->where('campaign_id', $campaign);
            $rslt = $astDB->get('vicidial_lists', null, 'list_id,active');
			$lists_to_parse = $astDB->getRowCount();
			$camp_lists = [];
			$o = 0;
			while ($lists_to_parse > $o) {
				$rowx = $rslt[$o];
				if (preg_match("/Y/", $rowx['active'])) {
                    $active_lists++;
                    //$camp_lists .= "'{$rowx['list_id']}',";
					$camp_lists[] = $rowx['list_id'];
                }
				if (preg_match("/ALL/", $manual_dial_filter)) {
					if (preg_match("/N/", $rowx['active'])) {
                        $inactive_lists++;
                        $camp_lists[] = $rowx['list_id'];
                    }
				} else {
					if (preg_match("/N/", $rowx['active'])) 
						{$inactive_lists++;}
				}
				$o++;
			}
			//$camp_lists = preg_replace("/.$/i","",$camp_lists);

			//$stmt="SELECT count(*) FROM vicidial_list where phone_number='$phone_number' and list_id IN($camp_lists);";
			$astDB->where('phone_number', $phone_number);
			$astDB->where('list_id', $camp_lists, 'in');
			$rslt = $astDB->get('vicidial_list');
			$list_cnt = $astDB->getRowCount();
			
			if ($list_cnt < 1) {
				$APIResult = array( "result" => "error", "message" => "CALL NOT PLACED: Number NOT in CAMPLISTS" );
				$error_catcher++;
			}
		}
		### END check phone filtering for DNC or camplists if enabled ###


		### prepare variables to place manual call from agent dialer
		if ($error_catcher < 1) {
			$CCID_on = 0;
			$CCID = '';
			$LISTweb_form_address = '';
			$LISTweb_form_address_two = '';
			$local_DEF = 'Local/';
			$local_AMP = '@';
			$Local_out_prefix = '9';
			$Local_dial_timeout = '60';
			$Local_persist = '/n';
			if ($dial_timeout > 4) {$Local_dial_timeout = $dial_timeout;}
			$Local_dial_timeout = ($Local_dial_timeout * 1000);
			if (strlen($dial_prefix) > 0) {$Local_out_prefix = "$dial_prefix";}
			if (strlen($campaign_cid) > 6) {
				$CCID = "$campaign_cid";
				$CCID_on++;
			}
			if (preg_match("/x/i", $dial_prefix)) {$Local_out_prefix = '';}
			$campaign_cid_override = '';
			### check if there is a list_id override
			if (strlen($lead_id) > 1) {
				$list_id = '';
				//$stmt = "SELECT list_id,province FROM vicidial_list where lead_id='$lead_id';";
				$astDB->where('lead_id', $lead_id);
				$rslt = $astDB->getOne('vicidial_list', 'list_id,province');
				$lio_ct = $astDB->getRowCount();
				if ($lio_ct > 0) {
					$row = $rslt;
					$list_id =	$row['list_id'];
					$province =	$row['province'];
	
					if (strlen($list_id) > 1) {
						//$stmt = "SELECT campaign_cid_override,web_form_address,web_form_address_two FROM vicidial_lists where list_id='$list_id';";
						$astDB->where('list_id', $list_id);
						$rslt = $astDB->getOne('vicidial_lists', 'campaign_cid_override,web_form_address,web_form_address_two');
						$lio_ct = $astDB->getRowCount();
						if ($lio_ct > 0) {
							$row = $rslt;
							$campaign_cid_override =	$row['campaign_cid_override'];
							$LISTweb_form_address =		$row['web_form_address'];
							$LISTweb_form_address_two =	$row['web_form_address_two'];
						}
					}
				}
			}
			if (strlen($campaign_cid_override) > 6) {
				$CCID = "$campaign_cid_override";
				$CCID_on++;
			}
			### check for custom cid use
			$use_custom_cid = 0;
			//$stmt = "SELECT use_custom_cid FROM vicidial_campaigns where campaign_id='$campaign';";
			$astDB->where('campaign_id', $campaign);
			$rslt = $astDB->get('vicidial_campaigns', null, 'use_custom_cid');
			$uccid_ct = $astDB->getRowCount();
			if ($uccid_ct > 0) {
				$row = $rslt[0];
				$use_custom_cid = $row['use_custom_cid'];
				if ($use_custom_cid == 'AREACODE') {
					$temp_ac = substr("$phone_number", 0, 3);
					//$stmt = "SELECT outbound_cid FROM vicidial_campaign_cid_areacodes where campaign_id='$campaign' and areacode='$temp_ac' and active='Y' order by call_count_today limit 1;";
					$astDB->where('campaign_id', $campaign);
					$astDB->where('areacode', $temp_ac);
					$astDB->where('active', 'Y');
					$astDB->orderBy('call_count_today');
					$rslt = $astDB->getOne('vicidial_campaign_cid_areacodes', 'outbound_cid');
					$vcca_ct = $astDB->getRowCount();
					if ($vcca_ct > 0) {
						$row = $rslt;
						$temp_vcca = $row['outbound_cid'];
	
						//$stmt="UPDATE vicidial_campaign_cid_areacodes set call_count_today=(call_count_today + 1) where campaign_id='$campaign' and areacode='$temp_ac' and outbound_cid='$temp_vcca';";
						$astDB->where('campaign_id', $campaign);
						$astDB->where('areacode', $temp_ac);
						$astDB->where('outbound_cid', $temp_vcca);
						$rslt = $astDB->getOne('vicidial_campaign_cid_areacodes', 'call_count_today');
						$call_count_today = $rslt['call_count_today'];
						
						$updateData = array(
							'call_count_today' => ($call_count_today + 1)
						);
						$astDB->where('campaign_id', $campaign);
						$astDB->where('areacode', $temp_ac);
						$astDB->where('outbound_cid', $temp_vcca);
						$rslt = $astDB->update('vicidial-campaign_cid_areacodes', $updateData);
					}
					$temp_CID = preg_replace("/\D/", '', $temp_vcca);
				}
				if ($use_custom_cid == 'Y')
					{$temp_CID = preg_replace("/\D/", '', $security);}
				if (strlen($temp_CID) > 6) {
					$CCID = "$temp_CID";
					$CCID_on++;
				}
			}
	
			$PADlead_id = sprintf("%010s", $lead_id);
			while (strlen($PADlead_id) > 10) {
				$PADlead_id = substr("$PADlead_id", 1);
			}
	
			# Create unique calleridname to track the call: MmddhhmmssLLLLLLLLLL
			$MqueryCID = "M$CIDdate$PADlead_id";
			$EAC = '';
			if ($use_eac > 0) {
				$eac_extension = preg_replace("/SIP\/|IAX2\/|Zap\/|DAHDI\/|Local\//", '', $eac_phone);
				$EAC = " $eac_extension";
			}
			if ($CCID_on) {$CIDstring = "\"$MqueryCID$EAC\" <$CCID>";}
			else {$CIDstring = "$MqueryCID$EAC";}
	
			if ( ($usegroupalias > 0) and (strlen($account) > 1) ) {
				$RAWaccount = $account;
				$account = "Account: $account";
				$variable = "Variable: usegroupalias=1";
			} else {
				$account = '';
				$variable = '';
			}
	
			### whether to omit phone_code or not
			if (preg_match('/Y/i', $omit_phone_code)) 
				{$Ndialstring = "$Local_out_prefix$phone_number";}
			else
				{$Ndialstring = "$Local_out_prefix$phone_code$phone_number";}
			### insert the call action into the vicidial_manager table to initiate the call
			#	$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $conf_exten','Context: $ext_context','Channel: $local_DEF$Local_out_prefix$phone_code$phone_number$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
			//$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $Ndialstring','Context: $ext_context','Channel: $local_DEF$conf_exten$local_AMP$ext_context$Local_persist','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','$account','$variable','','');";
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
				'cmd_line_d' => "Channel: {$local_DEF}{$conf_exten}{$local_AMP}{$ext_context}{$Local_persist}",
				'cmd_line_e' => 'Priority: 1',
				'cmd_line_f' => "Callerid: $CIDstring",
				'cmd_line_g' => "Timeout: $Local_dial_timeout",
				'cmd_line_h' => $account,
				'cmd_line_i' => $variable,
				'cmd_line_j' => '',
				'cmd_line_k' => ''
			);
			$rslt = $astDB->insert('vicidial_manager', $insertData);
	
			### log outbound call in the dial log
			//$stmt = "INSERT INTO vicidial_dial_log SET caller_code='$MqueryCID',lead_id='$lead_id',server_ip='$server_ip',call_date='$NOW_TIME',extension='$Ndialstring',channel='$local_DEF$conf_exten$local_AMP$ext_context$Local_persist',timeout='$Local_dial_timeout',outbound_cid='$CIDstring',context='$ext_context';";
			$insertData = array(
				'caller_code' => $MqueryCID,
				'lead_id' => $lead_id,
				'server_ip' => $server_ip,
				'call_date' => $NOW_TIME,
				'extension' => $Ndialstring,
				'channel' => "{$local_DEF}{$conf_exten}{$local_AMP}{$ext_context}{$Local_persist}",
				'timeout' => $Local_dial_timeout,
				'outbound_cid' => $CIDstring,
				'context' => $ext_context
			);
			$rslt = $astDB->insert('vicidial_dial_log', $insertData);
	
			//$stmt = "INSERT INTO vicidial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) values('$server_ip','$campaign','XFER','$lead_id','$MqueryCID','$phone_code','$phone_number','$NOW_TIME','OUT')";
			$insertData = array(
				'server_ip' => $server_ip,
				'campaign_id' => $campaign,
				'status' => 'XFER',
				'lead_id' => $lead_id,
				'callerid' => $MqueryCID,
				'phone_code' => $phone_code,
				'phone_number' => $phone_number,
				'call_time' => $NOW_TIME,
				'call_type' => 'OUT'
			);
			$rslt = $astDB->insert('vicidial_auto_calls', $insertData);
	
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
			$errmsg = $astDB->getLastError();
			$retry_count = 0;
			while ( (strlen($errmsg) > 0) and ($retry_count < 9) ) {
				$astDB->where('user', $user);
				$astDB->where('server_ip', $server_ip);
				$rslt = $astDB->update('vicidial_live_agents', $updateData);
				$errmsg = $astDB->getLastError();
				$retry_count++;
			}
	
			//$stmt = "UPDATE vicidial_campaign_agents set calls_today='$calls_today' where user='$user' and campaign_id='$campaign';";
			$updateData = array(
				'calls_today' => $calls_today
			);
			$astDB->where('user', $user);
			$astDB->where('campaign_id', $campaign);
			$rslt = $astDB->update('vicidial_campaign_agents', $updateData);
	
			//echo "$MqueryCID\n";
	
			$val_pause_epoch = 0;
			$val_pause_sec = 0;
			$val_dispo_epoch = 0;
			$val_dispo_sec = 0;
			$val_wait_epoch = 0;
			$val_wait_sec = 0;
			//$stmt = "SELECT dispo_epoch,wait_epoch,pause_epoch FROM vicidial_agent_log where agent_log_id='$agent_log_id';";
			$astDB->where('agent_log_id', $agent_log_id);
			$rslt = $astDB->get('vicidial_agent_log', null, 'dispo_epoch,wait_epoch,pause_epoch');
			$vald_ct = $astDB->getRowCount();
			if ($vald_ct > 0) {
				$row = $rslt[0];
				$val_dispo_epoch =	$row['dispo_epoch'];
				$val_wait_epoch =	$row['wait_epoch'];
				$val_pause_epoch =	$row['pause_epoch'];
				$val_dispo_sec = ($StarTtimE - $val_dispo_epoch);
				$val_wait_sec = ($StarTtimE - $val_wait_epoch);
				$val_pause_sec = ($StarTtimE - $val_pause_epoch);
			}
			if ($val_dispo_epoch > 1000) {
				//$stmt="UPDATE vicidial_agent_log set status='ALTNUM',dispo_sec='$val_dispo_sec' where agent_log_id='$agent_log_id';";
				$updateData = array(
					'status' => 'ALTNUM',
					'dispo_sec' => $val_dispo_sec
				);
				$astDB->where('agent_log_id', $agent_log_id);
				$rslt = $astDB->update('vicidial_agent_log', $updateData);
	
				$user_group = '';
				//$stmt="SELECT user_group FROM vicidial_users where user='$user' LIMIT 1;";
				$astDB->where('user', $user);
				$rslt = $astDB->getOne('vicidial_users', 'user_group');
				$ug_record_ct = $astDB->getRowCount();
				if ($ug_record_ct > 0) {
					$user_group =		trim("{$rslt['user_group']}");
				}
	
				//$stmt="INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) values('$user','$server_ip','$NOW_TIME','$campaign','$StarTtimE','0','$StarTtimE','$user_group','ANDIAL');";
				$insertData = array(
					'user' => $user,
					'server_ip' => $server_ip,
					'event_time' => $NOW_TIME,
					'campaign_id' => $campaign,
					'pause_epoch' => $StarTtimE,
					'pause_sec' => 0,
					'wait_epoch' => $StarTtimE,
					'user_group' => $user_group,
					'sub_status' => 'ANDIAL'
				);
				$rslt = $astDB->insert('vicidial_agent_log', $insertData);
				$affected_rows = $astDB->getRowCount();
				$agent_log_id = $astDB->getInsertId();
	
				//$stmt="UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id',last_state_change='$NOW_TIME' where user='$user';";
				$updateData = array(
					'agent_log_id' => $agent_log_id,
					'last_state_change' => $NOW_TIME
				);
				$astDB->where('user', $user);
				$rslt = $astDB->update('vicidial_live_agents', $updateData);
				$VLAaffected_rows_update = $astDB->getRowCount();
			} else {
				//$stmt="UPDATE vicidial_agent_log set pause_sec='$val_pause_sec',wait_epoch='$StarTtimE' where agent_log_id='$agent_log_id';";
				$updateData = array(
					'pause_sec' => $val_pause_sec,
					'wait_epoch' => $StarTtimE
				);
				$rslt = $astDB->update('vicidial_agent_log', $updateData);
			}
	
			//echo "$agent_log_id\n";
	
	
			if ($agent_dialed_number > 0) {
				//$stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id) values('$user','$NOW_TIME','$agent_dialed_type','$server_ip','$phone_number','$Ndialstring','$lead_id','$CCID','$RAWaccount')";
				$insertData = array(
					'user' => $user,
					'call_date' => $NOW_TIME,
					'call_type' => $agent_dialed_type,
					'server_ip' => $server_ip,
					'phone_number' => $phone_number,
					'number_dialed' => $Ndialstring,
					'lead_id' => $lead_id,
					'callerid' => $CCID,
					'group_alias_id' => $RAWaccount
				);
				$rslt = $astDB->insert('user_call_log', $insertData);
			}
	
	
			#############################################
			##### START QUEUEMETRICS LOGGING LOOKUP #####
			//$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_pe_phone_append,queuemetrics_socket,queuemetrics_socket_url FROM system_settings;";
			$rslt = $astDB->getOne('system_settings', 'enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_pe_phone_append,queuemetrics_socket,queuemetrics_socket_url');
			$qm_conf_ct = $astDB->getRowCount();
			if ($qm_conf_ct > 0) {
				$enable_queuemetrics_logging =	$rslt['enable_queuemetrics_logging'];
				$queuemetrics_server_ip	=		$rslt['queuemetrics_server_ip'];
				$queuemetrics_dbname =			$rslt['queuemetrics_dbname'];
				$queuemetrics_login	=			$rslt['queuemetrics_login'];
				$queuemetrics_pass =			$rslt['queuemetrics_pass'];
				$queuemetrics_log_id =			$rslt['queuemetrics_log_id'];
				$queuemetrics_pe_phone_append = $rslt['queuemetrics_pe_phone_append'];
				$queuemetrics_socket =			$rslt['queuemetrics_socket'];
				$queuemetrics_socket_url =		$rslt['queuemetrics_socket_url'];
			}
			##### END QUEUEMETRICS LOGGING LOOKUP #####
			###########################################
			if ($enable_queuemetrics_logging > 0) {
				$data4SQL = array();
				$data4SS = '';
				//$stmt="SELECT queuemetrics_phone_environment FROM vicidial_campaigns where campaign_id='$campaign' and queuemetrics_phone_environment!='';";
				$astDB->where('campaign_id', $campaign);
				$astDB->where('queuemetrics_phone_environment', '', '!=');
				$rslt = $astDB->getOne('vicidial_campaigns', 'queuemetrics_phone_environment');
				$cqpe_ct = $astDB->getRowCount();
				if ($cqpe_ct > 0) {
					$pe_append = '';
					if ( ($queuemetrics_pe_phone_append > 0) and (strlen($rslt['queuemetrics_phone_environment']) > 0) )
						{$pe_append = "-$qm_extension";}
					//$data4SQL = ",data4='$row[0]$pe_append'";
					$data4SS = "&data4={$rslt['queuemetrics_phone_environment']}{$pe_append}";
					$data4SQL = array(
						'data4' => "{$rslt['queuemetrics_phone_environment']}{$pe_append}"
					);
				}
	
				//$linkB=mysqli_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass", "$queuemetrics_dbname");
				//mysql_select_db("$queuemetrics_dbname", $linkB);
				$linkB = new MySQLiDB("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass", "$queuemetrics_dbname");
	
				# UNPAUSEALL
				//$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$user',verb='UNPAUSEALL',serverid='$queuemetrics_log_id' $data4SQL;";
				$insertData = array(
					'partition' => 'P01',
					'time_id' => $StarTtimE,
					'call_id' => 'NONE',
					'queue' => 'NONE',
					'agent' => "Agent/{$user}",
					'verb' => 'UNPAUSEALL',
					'serverid' => $queuemetrics_log_id
				);
				$unpauseData = array_merge($insertData, $data4SQL);
				$rslt = $linkB->insert('queue_log', $unpauseData);
				$affected_rows = $linkB->getRowCount();
	
				# CALLOUTBOUND (formerly ENTERQUEUE)
				//$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$MqueryCID',queue='$campaign',agent='NONE',verb='CALLOUTBOUND',data2='$phone_number',serverid='$queuemetrics_log_id' $data4SQL;";
				$insertData = array(
					'partition' => 'P01',
					'time_id' => $StarTtimE,
					'call_id' => $MqueryCID,
					'queue' => $campaign,
					'agent' => 'NONE',
					'verb' => 'CALLOUTBOUND',
					'data2' => $phone_number,
					'serverid' => $queuemetrics_log_id
				);
				$calloutboundData = array_merge($insertData, $data4SQL);
				$rslt = $linkB->insert('queue_log', $calloutboundData);
				$affected_rows = $linkB->getRowCount();
	
				# CONNECT
				//$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='$MqueryCID',queue='$campaign',agent='Agent/$user',verb='CONNECT',data1='0',serverid='$queuemetrics_log_id' $data4SQL;";
				$insertData = array(
					'partition' => 'P01',
					'time_id' => $StarTtimE,
					'call_id' => $MqueryCID,
					'queue' => $campaign,
					'agent' => "Agent/{$user}",
					'verb' => 'CONNECT',
					'data1' => 0,
					'serverid' => $queuemetrics_log_id
				);
				$connectData = array_merge($insertData, $data4SQL);
				$rslt = $linkB->insert('queue_log', $connectData);
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
			##### check if system is set to generate logfile for transfers
			//$stmt="SELECT enable_agc_xfer_log FROM system_settings;";
			$rslt = $astDB->getOne('system_settings', 'enable_agc_xfer_log');
			$enable_agc_xfer_log_ct = $astDB->getRowCount();
			if ($enable_agc_xfer_log_ct > 0) {
				$enable_agc_xfer_log = $rslt['enable_agc_xfer_log'];
			}
			if ( ($WeBRooTWritablE > 0) and ($enable_agc_xfer_log > 0) ) {
				#	DATETIME|campaign|lead_id|phone_number|user|type
				#	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|M
				$fp = fopen ("./xfer_log.txt", "a");
				fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$phone_number|$user|M|$MqueryCID||$province\n");
				fclose($fp);
			}
			
			$APIResult = array( "result" => "success", "data" => array( "callerid" => $MqueryCID, "agent_log_id" => $agent_log_id ) );
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
