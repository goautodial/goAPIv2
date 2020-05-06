<?php
 /**
 * @file 		goUpdateDispo.php
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
if (isset($_GET['goDispoChoice'])) { $dispo_choice = $astDB->escape($_GET['goDispoChoice']); }
    else if (isset($_POST['goDispoChoice'])) { $dispo_choice = $astDB->escape($_POST['goDispoChoice']); }
if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
    else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }
if (isset($_GET['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_GET['goAutoDialLevel']); }
    else if (isset($_POST['goAutoDialLevel'])) { $auto_dial_level = $astDB->escape($_POST['goAutoDialLevel']); }
if (isset($_GET['goAgentLogID'])) { $agent_log_id = $astDB->escape($_GET['goAgentLogID']); }
    else if (isset($_POST['goAgentLogID'])) { $agent_log_id = $astDB->escape($_POST['goAgentLogID']); }
if (isset($_GET['goCallBackDateTime'])) { $CallBackDatETimE = $astDB->escape($_GET['goCallBackDateTime']); }
    else if (isset($_POST['goCallBackDateTime'])) { $CallBackDatETimE = $astDB->escape($_POST['goCallBackDateTime']); }
if (isset($_GET['goListID'])) { $list_id = $astDB->escape($_GET['goListID']); }
    else if (isset($_POST['goListID'])) { $list_id = $astDB->escape($_POST['goListID']); }
if (isset($_GET['goRecipient'])) { $recipient = $astDB->escape($_GET['goRecipient']); }
    else if (isset($_POST['goRecipient'])) { $recipient = $astDB->escape($_POST['goRecipient']); }
if (isset($_GET['goUseInternalDNC'])) { $use_internal_dnc = $astDB->escape($_GET['goUseInternalDNC']); }
    else if (isset($_POST['goUseInternalDNC'])) { $use_internal_dnc = $astDB->escape($_POST['goUseInternalDNC']); }
if (isset($_GET['goUseCampaignDNC'])) { $use_campaign_dnc = $astDB->escape($_GET['goUseCampaignDNC']); }
    else if (isset($_POST['goUseCampaignDNC'])) { $use_campaign_dnc = $astDB->escape($_POST['goUseCampaignDNC']); }
if (isset($_GET['goMDnextCID'])) { $MDnextCID = $astDB->escape($_GET['goMDnextCID']); }
    else if (isset($_POST['goMDnextCID'])) { $MDnextCID = $astDB->escape($_POST['goMDnextCID']); }
if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
    else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }
if (isset($_GET['goCallbackID'])) { $vtiger_callback_id = $astDB->escape($_GET['goCallbackID']); }
    else if (isset($_POST['goCallbackID'])) { $vtiger_callback_id = $astDB->escape($_POST['goCallbackID']); }
if (isset($_GET['goPhoneNumber'])) { $phone_number = $astDB->escape($_GET['goPhoneNumber']); }
    else if (isset($_POST['goPhoneNumber'])) { $phone_number = $astDB->escape($_POST['goPhoneNumber']); }
if (isset($_GET['goPhoneCode'])) { $phone_code = $astDB->escape($_GET['goPhoneCode']); }
    else if (isset($_POST['goPhoneCode'])) { $phone_code = $astDB->escape($_POST['goPhoneCode']); }
if (isset($_GET['goDialMethod'])) { $dial_method = $astDB->escape($_GET['goDialMethod']); }
    else if (isset($_POST['goDialMethod'])) { $dial_method = $astDB->escape($_POST['goDialMethod']); }
if (isset($_GET['goUniqueID'])) { $uniqueid = $astDB->escape($_GET['goUniqueID']); }
    else if (isset($_POST['goUniqueID'])) { $uniqueid = $astDB->escape($_POST['goUniqueID']); }
if (isset($_GET['goCallBackLeadStatus'])) { $CallBackLeadStatus = $astDB->escape($_GET['goCallBackLeadStatus']); }
    else if (isset($_POST['goCallBackLeadStatus'])) { $CallBackLeadStatus = $astDB->escape($_POST['goCallBackLeadStatus']); }
if (isset($_GET['goComments'])) { $comments = $astDB->escape($_GET['goComments']); }
    else if (isset($_POST['goComments'])) { $comments = $astDB->escape($_POST['goComments']); }
if (isset($_GET['goCustomFieldNames'])) { $FORMcustom_field_names = $astDB->escape($_GET['goCustomFieldNames']); }
    else if (isset($_POST['goCustomFieldNames'])) { $FORMcustom_field_names = $astDB->escape($_POST['goCustomFieldNames']); }
if (isset($_GET['goCallNotes'])) { $call_notes = $astDB->escape($_GET['goCallNotes']); }
    else if (isset($_POST['goCallNotes'])) { $call_notes = $astDB->escape($_POST['goCallNotes']); }
if (isset($_GET['goQMDispoCode'])) { $qm_dispo_code = $astDB->escape($_GET['goQMDispoCode']); }
    else if (isset($_POST['goQMDispoCode'])) { $qm_dispo_code = $astDB->escape($_POST['goQMDispoCode']); }
if (isset($_GET['goEmailEnabled'])) { $email_enabled = $astDB->escape($_GET['goEmailEnabled']); }
    else if (isset($_POST['goEmailEnabled'])) { $email_enabled = $astDB->escape($_POST['goEmailEnabled']); }

$MT[0] = '';
$MAN_vl_insert = 0;
$StarTtime = date("U");
$errorcnt = 0;

if ($is_logged_in) {
	if ( (strlen($dispo_choice)<1) || (strlen($lead_id)<1) ) {
		$message = "Dispo Choice or Lead ID is NOT valid";
		$errorcnt++;
	} else {
		if (!isset($recipient)) {
			$recipient = 'ANYONE';
		}
		//$stmt = "SELECT dispo_call_url,queuemetrics_callstatus_override from vicidial_campaigns vc,vicidial_live_agents vla where vla.campaign_id=vc.campaign_id and vla.user='$user';";
		$astDB->where('vla.user', $user);
		$astDB->where('vla.vicidial_live_agents vla', 'vla.campaign_id=vc.campaign_id', 'LEFT');
		$rslt = $astDB->get('vicidial_campaigns vc', null, 'dispo_call_url,queuemetrics_callstatus_override');
		$VC_dcu_ct = $astDB->getRowCount();
		if ($VC_dcu_ct > 0) {
			$row = $rslt[0];
			$dispo_call_url =					$row['dispo_call_url'];
			$queuemetrics_callstatus_override =	$row['queuemetrics_callstatus_override'];
		}
	
		### reset the API fields in vicidial_live_agents record
		//$stmt = "UPDATE vicidial_live_agents set lead_id=0,external_hangup=0,external_status='',external_update_fields='0',external_update_fields_data='',external_timer_action_seconds='-1',external_dtmf='',external_transferconf='',external_park='',external_recording='',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
		$updateData = array(
			'lead_id' => 0,
			'external_hangup' => 0,
			'external_status' => '',
			'external_update_fields' => '0',
			'external_update_fields_data' => '',
			'external_timer_action_seconds' => '-1',
			'external_dtmf' => '',
			'external_transferconf' => '',
			'external_park' => '',
			'external_recording' => '',
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
	
		if ($auto_dial_level < 1) {
			//$stmt = "UPDATE vicidial_live_agents set status='PAUSED',callerid='' where user='$user';";
			$updateData = array(
				'status' => 'PAUSED',
				'callerid' => ''
			);
			$astDB->where('user', $user);
			$rslt = $astDB->update('vicidial_live_agents', $updateData);
		}
		//$stmt="UPDATE vicidial_list set status='$dispo_choice', user='$user' where lead_id='$lead_id';";
		$updateData = array(
			'status' => $dispo_choice,
			'user' => $user
		);
		$astDB->where('lead_id', $lead_id);
		$rslt = $astDB->update('vicidial_list', $updateData);
	
		//Added by Poundteam Incorporated for Audit Comments Package');
		audit_comments($astDB, $lead_id, $list_id, $format, $user, $NOW_TIME, $server_ip, $session_name, $campaign);
	
		// JOEJ - Email feature - may not be necessary if vicidial_email_list doesn't need a status column.
		if ($email_enabled > 0) {
			//$stmt="UPDATE vicidial_email_list set status='$dispo_choice', user='$user' where lead_id='$lead_id' and uniqueid='$uniqueid';";
			$updateData = array(
				'status' => $dispo_choice,
				'user' => $user
			);
			$astDB->where('lead_id', $lead_id);
			$astDB->where('uniqueid', $uniqueid);
			$rslt = $astDB->update('vicidial_email_list', $updateData);
		}
	
		$log_dispo_choice = $dispo_choice;
		if (strlen($CallBackLeadStatus) > 0) {$log_dispo_choice = $CallBackLeadStatus;}
	
		//$stmt = "SELECT count(*) from vicidial_inbound_groups where group_id='$stage';";
		$astDB->where('group_id', $stage);
		$rslt = $astDB->get('vicidial_inbound_groups');
		$row = $astDB->getRowCount();
		if ($row > 0) {
			$call_type = 'IN';
			//$stmt = "UPDATE vicidial_closer_log set status='$log_dispo_choice' where lead_id='$lead_id' and user='$user' order by closecallid desc limit 1;";
			$updateData = array(
				'status' => $log_dispo_choice
			);
			$astDB->where('lead_id', $lead_id);
			$astDB->where('user', $user);
			$astDB->orderBy('closecallid', 'desc');
			$rslt = $astDB->update('vicidial_closer_log', $updateData, 1);
			$VCLaffected_rows = $astDB->getRowCount();
	
			//$stmt = "UPDATE vicidial_live_inbound_agents set last_call_finish=NOW() where group_id='$stage' and user='$user' limit 1;";
			$updateData = array(
				'last_call_finish' => 'NOW()'
			);
			$astDB->where('group_id', $stage);
			$astDB->where('user', $user);
			$rslt = $astDB->update('vicidial_live_inbound_agents', $updateData, 1);
	
			//$stmt = "SELECT dispo_call_url from vicidial_inbound_groups where group_id='$stage';";
			$astDB->where('group_id', $stage);
			$rslt = $astDB->get('vicidial_inbound_groups', null, 'dispo_call_url');
			$row = $rslt[0];
			$dispo_call_url = $row['dispo_call_url'];
		} else {
			$call_type = 'OUT';
			$four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));
	
			if ( ($auto_dial_level < 1) or (preg_match('/^M/', $MDnextCID)) ) {
				//$stmt = "SELECT count(*) from vicidial_log where lead_id='$lead_id' and call_date > \"$four_hours_ago\" and ( (user='$user') or ( (comments='MANUAL') and status IN('AB','ADC','ADCT') ) );";
				$rslt = $astDB->rawQuery("SELECT * from vicidial_log where lead_id='$lead_id' and call_date > \"$four_hours_ago\" and ( (user='$user') or ( (comments='MANUAL') and status IN('AB','ADC','ADCT') ) );");
				$row = $astDB->getRowCount();
				if ($row > 0) {
					//$stmt="UPDATE vicidial_log set status='$log_dispo_choice',user='$user' where lead_id='$lead_id' and call_date > \"$four_hours_ago\" and ( (user='$user') or ( (comments='MANUAL') and status IN('AB','ADC','ADCT') ) ) order by uniqueid desc limit 1;";
					$rslt = $astDB->rawQuery("UPDATE vicidial_log set status='$log_dispo_choice',user='$user' where lead_id='$lead_id' and call_date > \"$four_hours_ago\" and ( (user='$user') or ( (comments='MANUAL') and status IN('AB','ADC','ADCT') ) ) order by uniqueid desc limit 1;");
				} else {
					$VLlist_id = '';
					$VLphone_number = '';
					$VLphone_code = '';
					$user_group='';
					//$stmt = "SELECT user_group FROM vicidial_users where user='$user';";
					$astDB->where('user', $user);
					$rslt = $astDB->getOne('vicidial_users', 'user_group');
					$VUinfo_ct = $astDB->getRowCount();
					if ($VUinfo_ct > 0) {
						$user_group = "{$rslt['user_group']}";
					}
	
					//$stmt = "SELECT list_id,phone_number,phone_code,alt_phone,address3 FROM vicidial_list where lead_id='$lead_id';";
					$astDB->where('lead_id', $lead_id);
					$rslt = $astDB->getOne('vicidial_list', 'list_id,phone_number,phone_code,alt_phone,address3');
					$VLinfo_ct = $astDB->getRowCount();
					if ($VLinfo_ct > 0) {
						$row = $rslt;
						$VLlist_id =		"{$row['list_id']}";
						if (strlen($phone_number) < 6) {
							$VLphone_number =	"{$row['phone_number']}";
							$VLalt =			'MAIN';
							$VLalt_phone =		"{$row['alt_phone']}";
							$VLaddress3 =		"{$row['address3']}";
						} else {
							$VLphone_number =	"$phone_number";
							if ($phone_number == "{$row['phone_number']}")
								{$VLalt =		'MAIN';}
							else {
								if ($phone_number != $VLalt_phone) {
									if ($phone_number != $VLaddress3) {
										$VLalt = 'X1';
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
													{$VLalt = 'XLAST';}
												else
													{$VLalt = "X$Xalt_phone_count";}
											}
										}
									} else {$VLalt = 'ADDR3';}
								} else {$VLalt = 'ALT';}
							}
						}
						if (strlen($phone_code) < 1)
							{$VLphone_code =	"{$row['phone_code']}";}
						else
							{$VLphone_code =	"$phone_code";}
					}
	
					$PADlead_id = sprintf("%010s", $lead_id);
					while (strlen($PADlead_id) > 9) {$PADlead_id = substr("$PADlead_id", 1);}
					$FAKEcall_id = "$StarTtime.$PADlead_id";
					//$stmt = "INSERT INTO vicidial_log set uniqueid='$FAKEcall_id',lead_id='$lead_id',list_id='$VLlist_id',campaign_id='$campaign',call_date='$NOW_TIME',start_epoch='$StarTtime',end_epoch='$StarTtime',length_in_sec='0',status='$log_dispo_choice',phone_code='$VLphone_code',phone_number='$VLphone_number',user='$user',comments='MANUAL',processed='N',user_group='$user_group',term_reason='AGENT',alt_dial='$VLalt';";
					$insertData = array(
						'uniqueid' => $FAKEcall_id,
						'lead_id' => $lead_id,
						'list_id' => $VLlist_id,
						'campaign_id' => $campaign,
						'call_date' => $NOW_TIME,
						'start_epoch' => $StarTtime,
						'end_epoch' => $StarTtime,
						'length_in_sec' => '0',
						'status' => $log_dispo_choice,
						'phone_code' => $VLphone_code,
						'phone_number' => $VLphone_number,
						'user' => $user,
						'comments' => 'MANUAL',
						'processed' => 'N',
						'user_group' => $user_group,
						'term_reason' => 'AGENT',
						'alt_dial' => $VLalt
					);
					$rslt = $astDB->insert('vicidial_log', $insertData);
	
					##### insert log into vicidial_log_extended for manual VICIDiaL call
					//$stmt="INSERT IGNORE INTO vicidial_log_extended SET uniqueid='$FAKEcall_id',server_ip='$server_ip',call_date='$NOW_TIME',lead_id='$lead_id',caller_code='$MDnextCID',custom_call_id='' ON DUPLICATE KEY UPDATE server_ip='$server_ip',call_date='$NOW_TIME',lead_id='$lead_id',caller_code='$MDnextCID';";
					$rslt = $astDB->rawQuery("INSERT IGNORE INTO vicidial_log_extended SET uniqueid='$FAKEcall_id',server_ip='$server_ip',call_date='$NOW_TIME',lead_id='$lead_id',caller_code='$MDnextCID',custom_call_id='' ON DUPLICATE KEY UPDATE server_ip='$server_ip',call_date='$NOW_TIME',lead_id='$lead_id',caller_code='$MDnextCID';");
					$affected_rowsX = $astDB->getRowCount();
	
					$MAN_vl_insert++;
				}
	
				//$stmt="DELETE FROM vicidial_auto_calls where callerid='$MDnextCID';";
				$astDB->where('callerid', $MDnextCID);
				$rslt = $astDB->delete('vicidial_auto_calls');
	
				//$stmt="UPDATE vicidial_live_agents set ring_callerid='' where ring_callerid='$MDnextCID';";
				$updateData = array(
					'ring_callerid' => ''
				);
				$astDB->where('ring_callerid', $MDnextCID);
				$rslt = $astDB->update('vicidial_live_agents', $updateData);
			} else {
				//$stmt="UPDATE vicidial_log set status='$log_dispo_choice' where lead_id='$lead_id' and user='$user' and call_date > \"$four_hours_ago\" order by uniqueid desc limit 1;";
				$updateData = array(
					'status' => $log_dispo_choice
				);
				$astDB->where('lead_id', $lead_id);
				$astDB->where('user', $user);
				$astDB->where('call_date', $four_hours_ago, '>');
				$astDB->orderBy('uniqueid', 'desc');
				$rslt = $astDB->update('vicidial_log', $updateData, 1);
			}
		}
	
		### find all DNC-type statuses in the system
		if ( ($use_internal_dnc=='Y') or ($use_campaign_dnc=='Y') or ($use_internal_dnc=='AREACODE') or ($use_campaign_dnc=='AREACODE') ) {
			$DNC_string_check = '|';
			//$stmt = "SELECT status FROM vicidial_statuses where dnc='Y';";
			$astDB->where('dnc', 'Y');
			$rslt = $astDB->get('vicidial_statuses', null, 'status');
			$dncvs_ct = $astDB->getRowCount();
			$i = 0;
			while ($i < $dncvs_ct) {
				$row = $rslt[$i];
				$DNC_string_check .= "{$row['status']}|";
				$i++;
			}
	
			//$stmt = "SELECT status FROM vicidial_campaign_statuses where dnc='Y';";
			$astDB->where('dnc', 'Y');
			$rslt = $astDB->get('vicidial_campaign_statuses', null, 'status');
			$dncvcs_ct = $astDB->getRowCount();
			$i = 0;
			while ($i < $dncvcs_ct) {
				$row = $rslt[$i];
				$DNC_string_check .= "{$row['status']}|";
				$i++;
			}
	
		#	echo "$DNC_string_check";
		}
	
		$insert_into_dnc = 0;
		if ( ( ($use_internal_dnc == 'Y') or ($use_internal_dnc == 'AREACODE') ) and (preg_match("/\|$log_dispo_choice\|/i", $DNC_string_check) ) ) {
			//$stmt = "SELECT phone_number from vicidial_list where lead_id='$lead_id';";
			$astDB->where('lead_id', $lead_id);
			$rslt = $astDB->getOne('vicidial_list', 'phone_number');
			
			//$stmt="INSERT IGNORE INTO vicidial_dnc (phone_number) values('$row[0]');";
			$rslt = $astDB->rawQuery("INSERT IGNORE INTO vicidial_dnc (phone_number) values('{$rslt['phone_number']}');");
			$insert_into_dnc++;
		}
		if ( ( ($use_campaign_dnc == 'Y') or ($use_campaign_dnc == 'AREACODE') ) and (preg_match("/\|$log_dispo_choice\|/i", $DNC_string_check) ) ) {
			//$stmt="SELECT use_other_campaign_dnc from vicidial_campaigns where campaign_id='$campaign';";
			$astDB->where('campaign_id', $campaign);
			$rslt = $astDB->getOne('vicidial_campaigns', 'use_other_campaign_dnc');
			$use_other_campaign_dnc = $rslt['use_other_campaign_dnc'];
			$temp_campaign_id = $campaign;
			if (strlen($use_other_campaign_dnc) > 0) {$temp_campaign_id = $use_other_campaign_dnc;}
	
			//$stmt = "SELECT phone_number from vicidial_list where lead_id='$lead_id';";
			$astDB->where('lead_id', $lead_id);
			$rslt = $astDB->getOne('vicidial_list', 'phone_number');
	
			//$stmt="INSERT IGNORE INTO vicidial_campaign_dnc (phone_number,campaign_id) values('$row[0]','$temp_campaign_id');";
			$rslt = $astDB->rawQuery("INSERT IGNORE INTO vicidial_campaign_dnc (phone_number,campaign_id) values('{$rslt['phone_number']}','{$temp_campaign_id}');");
			$insert_into_dnc++;
		}
	}
    
	if ($errorcnt < 1) {
		$dispo_sec = 0;
		$StarTtime = date("U");
		//$stmt = "SELECT dispo_epoch,dispo_sec,talk_epoch,wait_epoch,lead_id,comments,agent_log_id from vicidial_agent_log where agent_log_id <='$agent_log_id' and lead_id='$lead_id' order by agent_log_id desc limit 1;";
		$astDB->where('agent_log_id', $agent_log_id, '<=');
		$astDB->where('lead_id', $lead_id);
		$astDB->orderBy('agent_log_id', 'desc');
		$rslt = $astDB->getOne('vicidial_agent_log', 'dispo_epoch,dispo_sec,talk_epoch,wait_epoch,lead_id,comments,agent_log_id');
		$VDpr_ct = $astDB->getRowCount();
		if ($VDpr_ct > 0) {
			$agent_log_id = $rslt['agent_log_id'];
			$updateData = array(
				'status' => $log_dispo_choice,
				'uniqueid' => $uniqueid
			);
			if ( (preg_match("/NULL/i", $rslt['talk_epoch'])) or ($rslt['talk_epoch'] < 1000) ) {
				$rslt['talk_epoch'] = $StarTtime;
				$wait_sec = ($rslt['talk_epoch'] - $rslt['wait_epoch']);
				$talk_epochSQL = array(
					'talk_epoch' => $rslt['talk_epoch'],
					'wait_sec' => $wait_sec
				);
				$updateData = array_merge($updateData, $talk_epochSQL);
			}
			if ( (preg_match("/NULL/i", $rslt['dispo_epoch'])) or ($rslt['dispo_epoch'] < 1000) ) {
				$dispo_epochSQL = array(
					'dispo_epoch' => $StarTtime
				);
				$rslt['dispo_epoch'] = $rslt['talk_epoch'];
				$updateData = array_merge($updateData, $dispo_epochSQL);
			}
			
			$dispo_sec = (($StarTtime - $rslt['dispo_epoch']) + $rslt['dispo_sec']);
			$dispo_secSQL = array(
				'dispo_sec' => $dispo_sec
			);
			$updateData = array_merge($updateData, $dispo_secSQL);
			
			if ( (preg_match('/^M/', $MDnextCID)) and (preg_match('/INBOUND_MAN/', $dial_method)) ) {
				if ( (preg_match("/NULL/i", $rslt['comments'])) or (strlen($rslt['comments']) < 1) ) {
					$commentsSQL = array(
						'comments' => 'MANUAL'
					);
					$updateData = array_merge($updateData, $commentsSQL);
				}
				if ( (preg_match("/NULL/i", $rslt['lead_id'])) or ($rslt['lead_id'] < 1) or (strlen($rslt['lead_id']) < 1) ) {
					$lead_idSQL = array(
						'lead_id' => $lead_id
					);
					$updateData = array_merge($updateData, $lead_idSQL);
				}
			}
		}
		//$stmt="UPDATE vicidial_agent_log set dispo_sec='$dispo_sec',status='$log_dispo_choice',uniqueid='$uniqueid' $dispo_epochSQL $lead_id_commentsSQL where agent_log_id='$agent_log_id';";
		$astDB->where('agent_log_id', $agent_log_id);
		$rslt = $astDB->update('vicidial_agent_log', $updateData);
	
		//$stmt="UPDATE vicidial_campaigns set campaign_calldate='$NOW_TIME' where campaign_id='$campaign';";
		$updateData = array(
			'campaign_calldate' => $NOW_TIME
		);
		$astDB->where('campaign_id', $campaign);
		$rslt = $astDB->update('vicidial_campaigns', $updateData);
		
		$user_group = '';
		//$stmt="SELECT user_group FROM vicidial_users where user='$user' LIMIT 1;";
		$astDB->where('user', $user);
		$rslt = $astDB->getOne('vicidial_users', 'user_group');
		$ug_record_ct = $astDB->getRowCount();
		if ($ug_record_ct > 0) {
			$user_group = trim("{$rslt['user_group']}");
		}
		$CALL_agent_log_id = $agent_log_id;
		
		if ($auto_dial_level < 1) {
			$insertData = array(
				'user' => $user,
				'server_ip' => $server_ip,
				'event_time' => $NOW_TIME,
				'campaign_id' => $campaign,
				'pause_epoch' => $StarTtime,
				'pause_sec' => '0',
				'wait_epoch' => $StarTtime,
				'user_group' => $user_group
			);
			if ($MAN_vl_insert > 0) {
				$MAN_insert_leadIDsql = array(
					'lead_id' => $lead_id
				);
				$insertData = array_merge($insertData, $MAN_insert_leadIDsql);
			}
			//$stmt="INSERT INTO vicidial_agent_log SET user='$user',server_ip='$server_ip',event_time='$NOW_TIME',campaign_id='$campaign',pause_epoch='$StarTtime',pause_sec='0',wait_epoch='$StarTtime',user_group='$user_group'$MAN_insert_leadIDsql;";
			$rslt = $astDB->insert('vicidial_agent_log', $insertData);
			$affected_rows = $astDB->getRowCount();
			$agent_log_id = $astDB->getInsertId();
		
			//$stmt="UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id' where user='$user';";
			$updateData = array(
				'agent_log_id' => $agent_log_id
			);
			$astDB->where('user', $user);
			$rslt = $astDB->update('vicidial_live_agents', $updateData);
			$VLAaffected_rows_update = $astDB->getRowCount();
		}
		
		### CALLBACK ENTRY
		if ( ($dispo_choice == 'CBHOLD') and (strlen($CallBackDatETimE) > 10) ) {
			$comments = urldecode($comments);
			$comments = preg_replace('/"/i', '', $comments);
			$comments = preg_replace("/'/i", '', $comments);
			$comments = preg_replace('/;/i', '', $comments);
			$comments = preg_replace("/\\\\/i", ' ', $comments);
			//$stmt="INSERT INTO vicidial_callbacks (lead_id,list_id,campaign_id,status,entry_time,callback_time,user,recipient,comments,user_group,lead_status) values('$lead_id','$list_id','$campaign','ACTIVE','$NOW_TIME','$CallBackDatETimE','$user','$recipient','$comments','$user_group','$CallBackLeadStatus');";
			$insertData = array(
				'lead_id' => $lead_id,
				'list_id' => $list_id,
				'campaign_id' => $campaign,
				'status' => 'ACTIVE',
				'entry_time' => $NOW_TIME,
				'callback_time' => $CallBackDatETimE,
				'user' => $user,
				'recipient' => $recipient,
				'comments' => $comments,
				'user_group' => $user_group,
				'lead_status' => $CallBackLeadStatus
			);
			$rslt = $astDB->insert('vicidial_callbacks', $insertData);
			
			// Add Callback to events
			$CB30minsEarly = date("Y-m-d H:i:s", strtotime("-30 minutes", strtotime($CallBackDatETimE)));
			$cbtime = date("h:i A", strtotime($CallBackDatETimE));
			$astDB->where('lead_id', $lead_id);
			$rslt = $astDB->getOne('vicidial_list', 'phone_number');
			$insertData = array(
				'user_id' => $agent->user_id,
				'title' => "CALLBACK -- Call ".$rslt['phone_number']." around ".$cbtime,
				'description' => '',
				'all_day' => 0,
				'start_date' => $CB30minsEarly,
				'end_date' => $CallBackDatETimE,
				'url' => '',
				'alarm' => '',
				'notification_sent' => 0,
				'color' => '#03a9f4'
			);
			$rslt = $goDB->insert('events', $insertData);
		}
		
		### BEGIN Call Notes Logging ###
		if (strlen($call_notes) > 1) {
			$VDADchannel_group = $campaign;
			//$stmt = "SELECT campaign_id,closecallid from vicidial_closer_log where uniqueid='$uniqueid' and user='$user' order by call_date desc limit 1;";
			$astDB->where('uniqueid', $uniqueid);
			$astDB->where('user', $user);
			$astDB->orderBy('call_date', 'desc');
			$rslt = $astDB->getOne('vicidial_closer_log', 'campaign_id,closercallid');
			$VDCL_cn_ct = $astDB->getRowCount();
			if ($VDCL_cn_ct > 0) {
				$VDADchannel_group = $rslt['campaign_id'];
				$vicidial_id = $rslt['closercallid'];
			} else {$vicidial_id = $uniqueid;}
		
			# Insert into vicidial_call_notes
			//$stmt="INSERT INTO vicidial_call_notes set lead_id='$lead_id',vicidial_id='$vicidial_id',call_date='$NOW_TIME',call_notes='" . mysqli_real_escape_string($call_notes) . "';";
			
			$call_notes = urldecode($call_notes);
			$call_notes = preg_replace("/\r/i", '', $call_notes);
			$call_notes = preg_replace("/\n/i", '!N!', $call_notes);
			$call_notes = preg_replace("/--AMP--/i", '&', $call_notes);
			$call_notes = preg_replace("/--QUES--/i", '?', $call_notes);
			$call_notes = preg_replace("/--POUND--/i", '#', $call_notes);
			$insertData = array(
				'lead_id' => $lead_id,
				'vicidial_id' => $vicidial_id,
				'call_date' => $NOW_TIME,
				'call_notes' => $call_notes
			);
			$rslt = $astDB->insert('vicidial_call_notes', $insertData);
			$affected_rows = $astDB->getRowCount();
			$notesid = $astDB->getInsertId();
		}
		### END Call Notes Logging ###
		
		//$stmt="SELECT auto_alt_dial_statuses,use_internal_dnc,use_campaign_dnc,api_manual_dial,use_other_campaign_dnc from vicidial_campaigns where campaign_id='$campaign';";
		$astDB->where('campaign_id', $campaign);
		$rslt = $astDB->get('vicidial_campaigns', null, 'auto_alt_dial_statuses,use_internal_dnc,use_campaign_dnc,api_manual_dial,use_other_campaign_dnc');
		$row = $rslt[0];
		$VC_auto_alt_dial_statuses =	$row['auto_alt_dial_statuses'];
		$use_internal_dnc =				$row['use_internal_dnc'];
		$use_campaign_dnc =				$row['use_campaign_dnc'];
		$api_manual_dial =				$row['api_manual_dial'];
		$use_other_campaign_dnc =		$row['use_other_campaign_dnc'];
		
		if ( ($auto_dial_level > 0) and (preg_match("/\s$dispo_choice\s/", $VC_auto_alt_dial_statuses)) ) {
			//$stmt = "SELECT count(*) from vicidial_hopper where lead_id='$lead_id' and status='HOLD';";
			$astDB->where('lead_id', $lead_id);
			$astDB->where('status', 'HOLD');
			$rslt = $astDB->get('vicidial_hopper');
			$row = $astDB->getRowCount();
		
			if ($row > 0) {
				##### Check for alt phone number in DNC list if applicable
				$UD_DNC_campaign = 0;
				$UD_DNC_internal = 0;
				$vh_phone = '';
				//$stmt="SELECT phone_number FROM vicidial_list where lead_id='$lead_id';";
				$astDB->where('lead_id', $lead_id);
				$rslt = $astDB->get('vicidial_list', null, 'phone_number');
				$ud_record_ct = $astDB->getRowCount();
				if ($ud_record_ct > 0) {
					$row = $rslt[0];
					$vh_phone = $row['phone_number'];
				}
		
				if ( (preg_match("/Y/", $use_internal_dnc)) or (preg_match("/AREACODE/", $use_internal_dnc)) ) {
					if (preg_match("/AREACODE/", $use_internal_dnc)) {
						$vhp_phone_areacode = substr($vh_phone, 0, 3);
						$vhp_phone_areacode .= "XXXXXXX";
						//$stmtA="SELECT count(*) from vicidial_dnc where phone_number IN('$vh_phone','$vhp_phone_areacode');";
						$astDB->where('phone_number', array($vh_phone, $vhp_phone_areacode), 'in');
					} else {
						//$stmtA="SELECT count(*) FROM vicidial_dnc where phone_number='$vh_phone';";
						$astDB->where('phone_number', $vh_phone);
					}
					$rslt = $astDB->get('vicidial_dnc');
					$ud_record_ct = $astDB->getRowCount();
					if ($ud_record_ct > 0) {
						$UD_DNC_internal = $ud_record_ct;
					}
				}
		
				if ( (preg_match("/Y/",$use_campaign_dnc)) or (preg_match("/AREACODE/",$use_campaign_dnc)) ) {
					$temp_campaign_id = $campaign;
					if (strlen($use_other_campaign_dnc) > 0) {$temp_campaign_id = $use_other_campaign_dnc;}
					if (preg_match("/AREACODE/", $use_campaign_dnc)) {
						$vhp_phone_areacode = substr($vh_phone, 0, 3);
						$vhp_phone_areacode .= "XXXXXXX";
						//$stmtA="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$vh_phone','$vhp_phone_areacode') and campaign_id='$temp_campaign_id';";
						$astDB->where('phone_number', array($vh_phone, $vhp_phone_areacode), 'in');
					} else {
						//$stmtA="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$vh_phone' and campaign_id='$temp_campaign_id';";
						$astDB->where('phone_number', $vh_phone);
					}
					$astDB->where('campaign_id', $temp_campaign_id);
					$rslt = $astDB->get('vicidial_campaign_dnc');
					$ud_record_ct = $astDB->getRowCount();
					if ($ud_record_ct > 0) {
						$UD_DNC_campaign = $ud_record_ct;
					}
				}
		
				if ( ($UD_DNC_campaign > 0) or ($UD_DNC_internal > 0) ) {
					if ( ( (preg_match("/\sDNCC\s/", $VC_auto_alt_dial_statuses)) and ($UD_DNC_campaign > 0) ) or ( (preg_match("/\sDNCL\s/", $VC_auto_alt_dial_statuses)) and ($UD_DNC_internal > 0) ) ) {
						//$stmt="UPDATE vicidial_hopper set status='DNC' where lead_id='$lead_id' and status='HOLD' limit 1;";
						$astDB->where('lead_id', $lead_id);
						$astDB->where('status', 'HOLD');
						$rslt = $astDB->update('vicidial_hopper', array('status'=>'DNC'), 1);
					}
				} else {
					//$stmt="UPDATE vicidial_hopper set status='READY' where lead_id='$lead_id' and status='HOLD' limit 1;";
					$astDB->where('lead_id', $lead_id);
					$astDB->where('status', 'HOLD');
					$rslt = $astDB->update('vicidial_hopper', array('status'=>'READY'), 1);
				}
			}
		} else {
			//$stmt="DELETE from vicidial_hopper where lead_id='$lead_id' and status='HOLD';";
			$astDB->where('lead_id', $lead_id);
			$astDB->where('status', 'HOLD');
			$rslt = $astDB->delete('vicidial_hopper');
		}
		if ( ($api_manual_dial == 'QUEUE') or ($api_manual_dial == 'QUEUE_AND_AUTOCALL') ) {
			//$stmt="DELETE from vicidial_manual_dial_queue where user='$user' and status='QUEUE';";
			$astDB->where('user', $user);
			$astDB->where('status', 'QUEUE');
			$rslt = $astDB->delete('vicidial_manual_dial_queue');
		}
		
		#############################################
		##### START QUEUEMETRICS LOGGING LOOKUP #####
		//$stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_callstatus,queuemetrics_dispo_pause,queuemetrics_pe_phone_append,queuemetrics_socket,queuemetrics_socket_url FROM system_settings;";
		$rslt = $astDB->getOne('system_settings', 'enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_callstatus,queuemetrics_dispo_pause,queuemetrics_pe_phone_append,queuemetrics_socket,queuemetrics_socket_url');
		$qm_conf_ct = $astDB->getRowCount();
		if ($qm_conf_ct > 0) {
			$enable_queuemetrics_logging =	$rslt['enable_queuemetrics_logging'];
			$queuemetrics_server_ip	=		$rslt['queuemetrics_server_ip'];
			$queuemetrics_dbname =			$rslt['queuemetrics_dbname'];
			$queuemetrics_login	=			$rslt['queuemetrics_login'];
			$queuemetrics_pass =			$rslt['queuemetrics_pass'];
			$queuemetrics_log_id =			$rslt['queuemetrics_log_id'];
			$queuemetrics_callstatus =		$rslt['queuemetrics_callstatus'];
			$queuemetrics_dispo_pause =		$rslt['queuemetrics_dispo_pause'];
			$queuemetrics_pe_phone_append = $rslt['queuemetrics_pe_phone_append'];
			$queuemetrics_socket =			$rslt['queuemetrics_socket'];
			$queuemetrics_socket_url =		$rslt['queuemetrics_socket_url'];
		}
		##### END QUEUEMETRICS LOGGING LOOKUP #####
		###########################################
		if ( ($enable_queuemetrics_logging > 0) and ( ( ($queuemetrics_callstatus > 0) or ($queuemetrics_callstatus_override == 'YES') ) and ($queuemetrics_callstatus_override != 'NO') ) ) {
			$qmDB = new MySQLiDB("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass", "$queuemetrics_dbname");
		
			if (strlen($stage) < 2) 
				{$stage = $campaign;}
			
			$insertData = array(
				'partition' => 'P01',
				'time_id' => $StarTtime,
				'call_id' => $MDnextCID,
				'queue' => $stage,
				'agent' => "Agent/$user",
				'verb' => 'CALLSTATUS',
				'data1' => $log_dispo_choice,
				'serverid' => $queuemetrics_log_id
			);
			if (strlen($qm_dispo_code) > 0) {
				$qm_dispo_codeSQL = array(
					'data3' => $qm_dispo_code
				);
				$insertData = array_merge($insertData, $qm_dispo_codeSQL);
			}
	
			//$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MDnextCID',queue='$stage',agent='Agent/$user',verb='CALLSTATUS',data1='$log_dispo_choice',serverid='$queuemetrics_log_id' $qm_dispo_codeSQL;";
			$rslt = $qmDB->insert('queue_log', $insertData);
			$affected_rows = $qmDB->getRowCount();
		
			### check to make sure a COMPLETE record is present for this call
			$QLcomplete_records = 0;
			//$stmt = "SELECT count(*) FROM queue_log where verb IN('COMPLETEAGENT','COMPLETECALLER') and call_id='$MDnextCID' and agent='Agent/$user' and queue='$stage';";
			$qmDB->where('verb', array('COMPLETEAGENT', 'COMPLETECALLER'), 'in');
			$qmDB->where('call_id', $MDnextCID);
			$qmDB->where('agent', "Agent/$user");
			$qmDB->where('queue', $stage);
			$rslt = $qmDB->get('queue_log');
			$comp_ct = $qmDB->getRowCount();
			if ($comp_ct > 0) {
				$QLcomplete_records =	$comp_ct;
			}
		
			### if there are no complete records, look up information to insert one for this call
			if ($QLcomplete_records < 1) {
				$QLconnect_time = $StarTtime;
				$QLcomplete_time = $StarTtime;
				$QLconnect_one = '';
				$QLconnect_four = '';
				$QLcomplete_position = 1;
		
				//$stmt = "SELECT time_id,data1,data4 FROM queue_log where verb='CONNECT' and call_id='$MDnextCID' and agent='Agent/$user' and queue='$stage' order by time_id desc limit 1;";
				$qmDB->where('verb', 'CONNECT');
				$qmDB->where('call_id', $MDnextCID);
				$qmDB->where('agent', "Agent/$user");
				$qmDB->where('queue', $stage);
				$qmDB->orderBy('time_id', 'desc');
				$rslt = $qmDB->getOne('queue_log', 'time_id,data1,data4');
				$connect_ct = $qmDB->getRowCount();
				if ($connect_ct > 0) {
					$QLconnect_time =	$rslt['time_id'];
					$QLconnect_one =	$rslt['data1'];
					$QLconnect_four =	$rslt['data4'];
				}
		
				//$stmt = "SELECT time_id FROM queue_log where verb='PAUSEREASON' and call_id='$MDnextCID' and agent='Agent/$user' and data1='$queuemetrics_dispo_pause' order by time_id desc limit 1;";
				$qmDB->where('verb', 'PAUSEREASON');
				$qmDB->where('call_id', $MDnextCID);
				$qmDB->where('agent', "Agent/$user");
				$qmDB->where('data1', $queuemetrics_dispo_pause);
				$qmDB->orderBy('time_id', 'desc');
				$rslt = $qmDB->getOne('queue_log', 'time_id');
				$pausereason_ct = $qmDB->getRowCount();
				if ($pausereason_ct > 0) {
					$QLcomplete_time = $rslt['time_id'];
				}
		
				$QLcomplete_length = ($QLcomplete_time - $QLconnect_time);
				if ($QLcomplete_length < 0) {$QLcomplete_length = 0;}
				if ($QLcomplete_length > 86400) {$QLcomplete_length = 1;}
		
				## if inbound, check for initial queue position
				if (preg_match("/^Y/",$MDnextCID)) {
					$four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));
		
					//$stmt = "SELECT queue_position FROM vicidial_closer_log where lead_id='$lead_id' and campaign_id='$stage' and call_date > \"$four_hours_ago\" order by closecallid desc limit 1;";
					$astDB->where('lead_id', $lead_id);
					$astDB->where('campaign_id', $stage);
					$astDB->where('call_date', $four_hours_ago, '>');
					$astDB->orderBy('closecallid', 'desc');
					$rslt = $astDB->getOne('vicidial_closer_log', 'queue_position');
					$vcl_ct = $astDB->getRowCount();
					if ($vcl_ct > 0) {
						$QLcomplete_position = $rslt['queue_position'];
					}
				}
		
				//$stmt = "INSERT INTO queue_log SET partition='P01',time_id='$QLcomplete_time',call_id='$MDnextCID',queue='$stage',agent='Agent/$user',verb='COMPLETEAGENT',data1='$QLconnect_one',data2='$QLcomplete_length',data3='$QLcomplete_position',serverid='$queuemetrics_log_id',data4='$QLconnect_four';";
				$insertData = array(
					'partition' => 'P01',
					'time_id' => $QLcomplete_time,
					'call_id' => $MDnextCID,
					'queue' => $stage,
					'agent' => "Agent/$user",
					'verb' => 'COMPLETEAGENT',
					'data1' => $QLconnect_one,
					'data2' => $QLcomplete_length,
					'data3' => $QLcomplete_position,
					'serverid' => $queuemetrics_log_id,
					'data4' => $QLconnect_four
				);
				$rslt = $qmDB->insert('queue_log', $insertData);
				$affected_rows = $qmDB->getRowCount();
		
				if ( ($queuemetrics_socket == 'CONNECT_COMPLETE') and (strlen($queuemetrics_socket_url) > 10) ) {
					$socket_send_data_begin = '?';
					$socket_send_data = "time_id=$QLcomplete_time&call_id=$MDnextCID&queue=$stage&agent=Agent/$user&verb=COMPLETEAGENT&data1=$QLconnect_one&data2=$QLcomplete_length&data3=$QLcomplete_position&data4=$QLconnect_four";
					if (preg_match("/\?/", $queuemetrics_socket_url))
						{$socket_send_data_begin='&';}
					### send queue_log data to the queuemetrics_socket_url ###
					//$SCUfile = file("$queuemetrics_socket_url$socket_send_data_begin$socket_send_data");
				}
			}
		
			$qmDB->__destruct();
		}
		
		############################################
		### BEGIN Issue Dispo Call URL if defined
		############################################
		if (strlen($dispo_call_url) > 7) {
			$talk_time = 0;
			$talk_time_ms = 0;
			$talk_time_min = 0;
			if ( (preg_match('/--A--user_custom_/i', $dispo_call_url)) or (preg_match('/--A--fullname/i', $dispo_call_url)) or (preg_match('/--A--user_group/i', $dispo_call_url)) ) {
				//$stmt = "SELECT custom_one,custom_two,custom_three,custom_four,custom_five,full_name,user_group from vicidial_users where user='$user';";
				$astDB->where('user', $user);
				$rslt = $astDB->getOne('vicidial_users', 'custom_one,custom_two,custom_three,custom_four,custom_five,full_name,user_group');
				$VUC_ct = $astDB->getRowCount();
				if ($VUC_ct > 0) {
					$user_custom_one =		urlencode(trim($rslt['custom_one']));
					$user_custom_two =		urlencode(trim($rslt['custom_two']));
					$user_custom_three =	urlencode(trim($rslt['custom_three']));
					$user_custom_four =		urlencode(trim($rslt['custom_four']));
					$user_custom_five =		urlencode(trim($rslt['custom_five']));
					$fullname =				urlencode(trim($rslt['full_name']));
					$user_group =			urlencode(trim($rslt['user_group']));
				}
			}
		
			if (preg_match('/--A--talk_time/i', $dispo_call_url)) {
				//$stmt = "SELECT talk_sec,dead_sec from vicidial_agent_log where lead_id='$lead_id' and agent_log_id='$CALL_agent_log_id';";
				$astDB->where('lead_id', $lead_id);
				$astDB->where('agent_log_id', $CALL_agent_log_id);
				$rslt = $astDB->get('vicidial_agent_log', null, 'talk_sec,dead_sec');
				$VAL_talk_ct = $astDB->getRowCount();
				if ($VAL_talk_ct > 0) {
					$row = $rslt[0];
					$talk_sec	=		$row['talk_sec'];
					$dead_sec	=		$row['dead_sec'];
					$talk_time = ($talk_sec - $dead_sec);
					if ($talk_time < 1) {
						$talk_time = 0;
						$talk_time_ms = 0;
					} else {
						$talk_time_ms = ($talk_time * 1000);
						$talk_time_min = ceil($talk_time / 60);
					}
				}
			}
		
			if (preg_match('/--A--dispo_name--B--/i', $dispo_call_url)) {
				### find the full status name for this status
				//$stmt = "SELECT status_name from vicidial_statuses where status='$dispo_choice';";
				$astDB->where('status', $dispo_choice);
				$rslt = $astDB->get('vicidial_statuses', null, 'status_name');
				$vs_name_ct = $astDB->getRowCount();
				if ($vs_name_ct > 0) {
					$row = $rslt[0];
					$status_name = urlencode(trim($row['status_name']));
				} else {
					//$stmt = "SELECT status_name from vicidial_campaign_statuses where status='$dispo_choice' and campaign_id='$campaign';";
					$astDB->where('status', $dispo_choice);
					$astDB->where('campaign_id', $campaign);
					$rslt = $astDB->get('vicidial_campaign_statuses', null, 'status_name');
					$vcs_name_ct = $astDB->getRowCount();
					if ($vcs_name_ct > 0) {
						$row = $rslt[0];
						$status_name = urlencode(trim($row['status_name']));
					}
				}
				if (strlen($status_name) < 1) {$status_name = $dispo_choice;}
			}
			$dispo_name = urlencode(trim($status_name));
		
			if (preg_match('/--A--call_notes/i', $dispo_call_url)) {
				if (strlen($call_notes) > 1)
					{$url_call_notes = urlencode(trim($call_notes));}
				else
					{$url_call_notes = urlencode(" ");}
			}
		
			if (preg_match('/--A--dialed_/i', $dispo_call_url)) {
				$dialed_number =	$phone_number;
				$dialed_label =		'NONE';
		
				if ($call_type == 'OUT') {
					### find the dialed number and label for this call
					//$stmt = "SELECT phone_number,alt_dial from vicidial_log where uniqueid='$uniqueid';";
					$astDB->where('uniqueid', $uniqueid);
					$rslt = $astDB->get('vicidial_log', null, 'phone_number,alt_dial');
					$vl_dialed_ct = $astDB->getRowCount();
					if ($vl_dialed_ct > 0) {
						$row = $rslt[0];
						$dialed_number = $row['phone_number'];
						$dialed_label = $row['alt_dial'];
					}
				}
			}
		
			if (preg_match('/--A--did_/i', $dispo_call_url)) {
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
					$DID_id	=			$rslt['did_id'];
					$DID_extension	=	$rslt['extension'];
		
					//$stmt = "SELECT did_pattern,did_description from vicidial_inbound_dids where did_id='$DID_id' limit 1;";
					$astDB->where('did_id', $DID_id);
					$rslt = $astDB->getOne('vicidial_inbound_dids', 'did_pattern,did_description');
					$VDIDL_ct = $astDB->getRowCount();
					if ($VDIDL_ct > 0) {
						$DID_pattern =		urlencode(trim($rslt['did_pattern']));
						$DID_description =	urlencode(trim($rslt['did_description']));
					}
				}
			}
		
			if ((preg_match('/callid--B--/i', $dispo_call_url)) or (preg_match('/group--B--/i', $dispo_call_url))) {
				$INclosecallid = '';
				$INxfercallid = '';
				$VDADchannel_group = $campaign;
				//$stmt = "SELECT campaign_id,closecallid,xfercallid from vicidial_closer_log where uniqueid='$uniqueid' and user='$user' order by call_date desc limit 1;";
				$astDB->where('uniqueid', $uniqueid);
				$astDB->where('user', $user);
				$astDB->orderBy('call_date', 'desc');
				$rslt = $astDB->getOne('vicidial_closer_log', 'campaign_id,closecallid,xfercallid');
				$VDCL_mvac_ct = $astDB->getRowCount();
				if ($VDCL_mvac_ct > 0) {
					$VDADchannel_group =	$rslt['campaign_id'];
					$INclosecallid =		$rslt['closecallid'];
					$INxfercallid =			$rslt['xfercallid'];
				}
			}
		
			##### grab the data from vicidial_list for the lead_id
			//$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
			$astDB->where('lead_id', $lead_id);
			$rslt = $astDB->getOne('vicidial_list', 'lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id');
			$list_lead_ct = $astDB->getRowCount();
			if ($list_lead_ct > 0) {
				$dispo			= urlencode(trim($rslt['status']));
				$tsr			= urlencode(trim($rslt['user']));
				$vendor_id		= urlencode(trim($rslt['vendor_lead_code']));
				$vendor_lead_code	= urlencode(trim($rslt['vendor_lead_code']));
				$source_id		= urlencode(trim($rslt['source_id']));
				$list_id		= urlencode(trim($rslt['list_id']));
				$gmt_offset_now	= urlencode(trim($rslt['gmt_offset_now']));
				$phone_code		= urlencode(trim($rslt['phone_code']));
				$phone_number	= urlencode(trim($rslt['phone_number']));
				$title			= urlencode(trim($rslt['title']));
				$first_name		= urlencode(trim($rslt['first_name']));
				$middle_initial	= urlencode(trim($rslt['middle_initial']));
				$last_name		= urlencode(trim($rslt['last_name']));
				$address1		= urlencode(trim($rslt['address1']));
				$address2		= urlencode(trim($rslt['address2']));
				$address3		= urlencode(trim($rslt['address3']));
				$city			= urlencode(trim($rslt['city']));
				$state			= urlencode(trim($rslt['state']));
				$province		= urlencode(trim($rslt['province']));
				$postal_code	= urlencode(trim($rslt['postal_code']));
				$country_code	= urlencode(trim($rslt['country_code']));
				$gender			= urlencode(trim($rslt['gender']));
				$date_of_birth	= urlencode(trim($rslt['date_of_birth']));
				$alt_phone		= urlencode(trim($rslt['alt_phone']));
				$email			= urlencode(trim($rslt['email']));
				$security		= urlencode(trim($rslt['security_phrase']));
				$comments		= urlencode(trim($rslt['comments']));
				$called_count	= urlencode(trim($rslt['called_count']));
				$rank			= urlencode(trim($rslt['rank']));
				$owner			= urlencode(trim($rslt['owner']));
				$entry_list_id	= urlencode(trim($rslt['entry_list_id']));
			}
		
			$dispo_call_url = preg_replace('/^VAR/', '', $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--lead_id--B--/i', "$lead_id", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--vendor_id--B--/i', "$vendor_id", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--vendor_lead_code--B--/i', "$vendor_lead_code", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--list_id--B--/i',"$list_id",$dispo_call_url);
			$dispo_call_url = preg_replace('/--A--gmt_offset_now--B--/i', "$gmt_offset_now", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--phone_code--B--/i', "$phone_code", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--phone_number--B--/i', "$phone_number", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--title--B--/i', "$title", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--first_name--B--/i', "$first_name", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--middle_initial--B--/i', "$middle_initial", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--last_name--B--/i', "$last_name", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--address1--B--/i', "$address1", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--address2--B--/i', "$address2", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--address3--B--/i', "$address3", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--city--B--/i', "$city", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--state--B--/i', "$state", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--province--B--/i', "$province", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--postal_code--B--/i', "$postal_code", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--country_code--B--/i', "$country_code", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--gender--B--/i', "$gender", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--date_of_birth--B--/i', "$date_of_birth", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--alt_phone--B--/i', "$alt_phone", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--email--B--/i', "$email", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--security_phrase--B--/i', "$security_phrase", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--comments--B--/i', "$comments", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--user--B--/i', "$user", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--pass--B--/i', "$pass", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--campaign--B--/i', "$campaign", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--phone_login--B--/i', "$phone_login", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--original_phone_login--B--/i', "$original_phone_login", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--phone_pass--B--/i', "$phone_pass", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--fronter--B--/i', "$fronter", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--closer--B--/i', "$user", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--group--B--/i', "$VDADchannel_group", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--channel_group--B--/i', "$VDADchannel_group", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--SQLdate--B--/i', "$SQLdate", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--epoch--B--/i', "$epoch", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--uniqueid--B--/i', "$uniqueid", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--customer_zap_channel--B--/i', "$customer_zap_channel", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--customer_server_ip--B--/i', "$customer_server_ip", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--server_ip--B--/i', "$server_ip", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--SIPexten--B--/i', "$SIPexten", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--session_id--B--/i', "$session_id", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--phone--B--/i', "$phone", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--parked_by--B--/i', "$parked_by", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--dispo--B--/i', "$dispo", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--dispo_name--B--/i', "$dispo_name", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--dialed_number--B--/i', "$dialed_number", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--dialed_label--B--/i', "$dialed_label", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--source_id--B--/i', "$source_id", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--rank--B--/i', "$rank", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--owner--B--/i', "$owner", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--camp_script--B--/i', "$camp_script", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--in_script--B--/i', "$in_script", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--fullname--B--/i', "$fullname", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--user_custom_one--B--/i', "$user_custom_one", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--user_custom_two--B--/i', "$user_custom_two", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--user_custom_three--B--/i', "$user_custom_three", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--user_custom_four--B--/i', "$user_custom_four", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--user_custom_five--B--/i', "$user_custom_five", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--talk_time--B--/i', "$talk_time", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--talk_time_ms--B--/i', "$talk_time_ms", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--talk_time_min--B--/i', "$talk_time_min", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--agent_log_id--B--/i', "$CALL_agent_log_id", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--entry_list_id--B--/i', "$entry_list_id", $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--did_id--B--/i', urlencode(trim($DID_id)), $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--did_extension--B--/i', urlencode(trim($DID_extension)), $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--did_pattern--B--/i', urlencode(trim($DID_pattern)), $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--did_description--B--/i', urlencode(trim($DID_description)), $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--closecallid--B--/i', urlencode(trim($INclosecallid)), $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--xfercallid--B--/i', urlencode(trim($INxfercallid)), $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--call_id--B--/i', urlencode(trim($MDnextCID)), $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--user_group--B--/i', urlencode(trim($user_group)), $dispo_call_url);
			$dispo_call_url = preg_replace('/--A--call_notes--B--/i', "$url_call_notes", $dispo_call_url);
		
			if (strlen($FORMcustom_field_names) > 2) {
				$custom_field_names = preg_replace("/^\||\|$/", '', $FORMcustom_field_names);
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
					$o = 0;
					while ($custom_field_names_ct > $o) {
						$field_name_id =		$custom_field_names_ARY[$o];
						$form_field_value =		urlencode(trim("{$rslt[$field_name_id]}"));
						$field_name_tag =		"--A--" . $field_name_id . "--B--";
						$dispo_call_url = preg_replace("/$field_name_tag/i", "$form_field_value", $dispo_call_url);
						$o++;
					}
				}
			}
		
			//$stmt="UPDATE vicidial_log_extended set dispo_url_processed='Y' where uniqueid='$uniqueid';";
			$updateData = array(
				'dispo_url_processed' => 'Y'
			);
			$astDB->where('uniqueid', $uniqueid);
			$rslt = $astDB->update('vicidial_log_extended', $updateData);
			$vle_update = $astDB->getRowCount();
		
			### insert a new url log entry
			$SQL_log = "$dispo_call_url";
			$SQL_log = preg_replace('/;/', '', $SQL_log);
			$SQL_log = addslashes($SQL_log);
			//$stmt = "INSERT INTO vicidial_url_log SET uniqueid='$uniqueid',url_date='$NOW_TIME',url_type='dispo',url='$SQL_log',url_response='';";
			$insertData = array(
				'uniqueid' => $uniqueid,
				'url_date' => $NOW_TIME,
				'url_type' => 'dispo',
				'url' => $SQL_log,
				'url_response' => ''
			);
			$rslt = $astDB->insert('vicidial_url_log', $insertData);
			$affected_rows = $astDB->getRowCount();
			$url_id = $astDB->getInsertId();
		
			$URLstart_sec = date("U");
		
			### send dispo_call_url ###
			$SCUfile = file("$dispo_call_url");
		
			### update url log entry
			$URLend_sec = date("U");
			$URLdiff_sec = ($URLend_sec - $URLstart_sec);
			$SCUfile_contents = implode("", $SCUfile);
			$SCUfile_contents = preg_replace('/;/','',$SCUfile_contents);
			$SCUfile_contents = addslashes($SCUfile_contents);
			//$stmt = "UPDATE vicidial_url_log SET response_sec='$URLdiff_sec',url_response='$SCUfile_contents' where url_log_id='$url_id';";
			$updateData = array(
				'response_sec' => $URLdiff_sec,
				'url_response' => $SCUfile_contents
			);
			$astDB->where('url_log_id', $url_id);
			$rslt = $astDB->update('vicidial_url_log', $updateData);
			$affected_rows = $astDB->getRowCount();
		
			//$stmt = "SELECT enable_vtiger_integration FROM system_settings;";
			$rslt = $astDB->getOne('system_settings', 'enable_vtiger_integration');
			$ss_conf_ct = $astDB->getRowCount();
			if ($ss_conf_ct > 0) {
				$enable_vtiger_integration = $rslt['enable_vtiger_integration'];
			}
			if ( ($enable_vtiger_integration > 0) and (preg_match('/mode=callend/', $dispo_call_url)) and (preg_match('/contactwsid/', $dispo_call_url)) ) {
				$SCUoutput = '';
				//foreach ($SCUfile as $SCUline) 
					//{$SCUoutput .= "$SCUline";}
				//$fp = fopen ("./call_url_log.txt", "a");
				//fwrite ($fp, "$dispo_call_url\n$SCUoutput\n");
				//fclose($fp);
			}
		
			### add this to the Dispo URL for callcard calls to be logged "&callcard=--A--talk_time_min--B--"
			if (preg_match("/callcard/", $dispo_call_url)) {
				//$stmt="SELECT balance_minutes_start,card_id FROM callcard_log where uniqueid='$uniqueid' order by call_time desc LIMIT 1;";
				$astDB->where('uniqueid', $uniqueid);
				$astDB->orderBy('call_time', 'desc');
				$rslt = $astDB->getOne('callcard_log', 'balance_minutes_start,card_id');
				$bms_ct = $astDB->getRowCount();
				//$fp = fopen ("./call_url_log.txt", "a");
				//fwrite ($fp, "$dispo_call_url\n$stmt|$bms_ct\n");
				//fclose($fp);
		
				if ($bms_ct > 0) {
					$balance_minutes_start =	$rslt['balance_minutes_start'];
					$card_id =					$rslt['card_id'];
	
					$current_minutes = ($balance_minutes_start - $talk_time_min);
	
					//$stmt="UPDATE callcard_log set agent_talk_sec='$talk_time',agent_talk_min='$talk_time_min',dispo_time='$NOW_TIME',agent_dispo='$dispo' where uniqueid='$uniqueid' order by call_time desc LIMIT 1;";
					$updateData = array(
						'agent_talk_sec' => $talk_time,
						'agent_talk_min' => $talk_time_min,
						'dispo_time' => $NOW_TIME,
						'agent_dispo' => $dispo
					);
					$astDB->where('uniqueid', $uniqueid);
					$rslt = $astDB->update('callcard_log', $updateData, 1);
					$ccl_update = $astDB->getRowCount();
		
					//$stmt="UPDATE callcard_accounts set balance_minutes='$current_minutes' where card_id='$card_id';";
					$astDB->where('card_id', $card_id);
					$rslt = $astDB->update('callcard_accounts', array('balance_minutes'=>$current_minutes));
					$cca_update = $astDB->getRowCount();
		
					//$stmt="UPDATE callcard_accounts_details set balance_minutes='$current_minutes' where card_id='$card_id';";
					$astDB->where('card_id', $card_id);
					$rslt = $astDB->update('callcard_accounts_details', array('balance_minutes'=>$current_minutes));
					$ccad_update = $astDB->getRowCount();
				}
			}
		}
		############################################
		### END Issue Dispo Call URL if defined
		############################################
		
		
		##### check if system is set to generate logfile for dispos
		//$stmt="SELECT enable_agc_dispo_log FROM system_settings;";
		$rslt = $astDB->getOne('system_settings', 'enable_agc_dispo_log');
		$enable_agc_dispo_log_ct = $astDB->getRowCount();
		if ($enable_agc_dispo_log_ct > 0) {
			$enable_agc_dispo_log = $rslt['enable_agc_dispo_log'];
		}
		
		if ( ($WeBRooTWritablE > 0) and ($enable_agc_dispo_log > 0) ) {
			$talk_time = 0;
			//$stmt = "SELECT talk_sec,dead_sec from vicidial_agent_log where lead_id='$lead_id' and agent_log_id='$CALL_agent_log_id';";
			$astDB->where('lead_id', $lead_id);
			$astDB->where('agent_log_id', $CALL_agent_log_id);
			$rslt = $astDB->get('vicidial_agent_log', null, 'talk_sec,dead_sec');
			$VAL_talk_ct = $astDB->getRowCount();
			if ($VAL_talk_ct > 0) {
				$talk_sec	=		$rslt['talk_sec'];
				$dead_sec	=		$rslt['dead_sec'];
				$talk_time = ($talk_sec - $dead_sec);
				if ($talk_time < 1) {
					$talk_time = 0;
				}
			}
		
			#	DATETIME|campaign|lead_id|phone_number|user|type|Call_ID||province|talk_sec|
			#	2010-02-19 11:11:11|TESTCAMP|65432|3125551212|1234|D|Y09876543210987654||note|123|
			//$fp = fopen ("./xfer_log.txt", "a");
			//fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$phone_number|$user|D|$MDnextCID||$province|$talk_sec|\n");
			//fclose($fp);
		}
		
		# debug testing sleep
		# sleep(5);
		
		$APIResult = array( "result" => "success", "message" => "Lead {$lead_id} has been changed to {$dispo_choice} status", "data" => array( "lead_id" => $lead_id, "dispo_choice" => $dispo_choice, "agent_log_id" => $agent_log_id ) );
	} else {
		$APIResult = array( "result" => "error", "message" => $message );
	}
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>
