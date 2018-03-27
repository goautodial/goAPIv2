<?php
 /**
 * @file 		goGetLeadsInfo.php
 * @brief 		API for Getting Leads Info
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Abenoja  <alex@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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

    ### POST or GET Variables
    $lead_id = $astDB->escape($_REQUEST['lead_id']);
    
    $ip_address = $astDB->escape($_REQUEST['log_ip']);
    $log_user = $session_user;
    $groupId = go_get_groupid($session_user, $astDB);
	$log_group = $groupId;
	
	if($session_user == null){
		$err_msg = error_handle("40001");
        $apiresults = array("code" => "40001","result" => $err_msg); 
	}elseif($lead_id == null) {
		$err_msg = error_handle("40001");
        $apiresults = array("code" => "40001","result" => $err_msg); 
    } else {
		
        if (checkIfTenant($groupId, $goDB)) {
            $ul = "";
        } else {
			if($groupId === "ADMIN")
			$ul = "";
			else
            $ul = "AND user_group='$groupId'";  
        }
        if ($groupId != 'ADMIN') {
            $notAdminSQL = "AND user_group != 'ADMIN'";
        }
		
        //$query = "SELECT * FROM vicidial_list where lead_id='$lead_id'";
		$astDB->where('lead_id', $lead_id);
        $fresults = $astDB->getOne('vicidial_list');
        $list_id = $fresults['list_id'];

        $is_customer = 0;
        if ($astDB->getRowCount() > 0) {
            //$rsltc = mysqli_query($linkgo, "SELECT * FROM go_customers WHERE lead_id='$lead_id' LIMIT 1;");
			$goDB->where('lead_id', $lead_id);
            $fresultsc = $goDB->getOne('go_customers');
            $is_customer = $goDB->getRowCount();
        }
		
        $data = empty($fresultsc) ? $fresults : array_merge($fresults, $fresultsc) ;
		
		if($data !== NULL){
			
			$calls_query = $astDB->rawQuery("SELECT uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed,user_group,term_reason,alt_dial FROM vicidial_log WHERE lead_id = '$lead_id' $ul ORDER BY call_date DESC LIMIT 10000;");
			foreach ($calls_query as $calls_fetch){
				$calls_call_date[] = $calls_fetch['call_date'];
				$calls_length_in_sec[] = gmdate("H:i:s", $calls_fetch['length_in_sec']);
				$calls_status[] = $calls_fetch['status'];
				$calls_user[] = $calls_fetch['user'];
				$calls_campaign_id[] = $calls_fetch['campaign_id'];
				$calls_list_id[] = $calls_fetch['list_id'];
				$calls_term_reason[] = $calls_fetch['term_reason'];
				$calls_phone_number[] = $calls_fetch['phone_number'];
			}
			$calls_array = array("call_date" => $calls_call_date, "length_in_sec" => $calls_length_in_sec, "status" => $calls_status, "user" => $calls_user, "campaign_id" => $calls_campaign_id, "list_id" => $calls_list_id, "term_reason" => $calls_term_reason, "phone_number" => $calls_phone_number);
			
			$closerlog_query = $astDB->rawQuery("SELECT closecallid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed,queue_seconds,user_group,xfercallid,term_reason,uniqueid,agent_only FROM vicidial_closer_log WHERE lead_id = '$lead_id' $ul ORDER BY call_date DESC LIMIT 10000;");
			
			foreach ($closerlog_query as $closerlog_fetch){
				$closerlog_call_date[] = $closerlog_fetch['call_date'];
				$closerlog_length_in_sec[] = gmdate("H:i:s", $closerlog_fetch['length_in_sec']);
				$closerlog_status[] = $closerlog_fetch['status'];
				$closerlog_user[] = $closerlog_fetch['user'];
				$closerlog_campaign_id[] = $closerlog_fetch['campaign_id'];
				$closerlog_list_id[] = $closerlog_fetch['list_id'];
				$closerlog_queue_seconds[] = $closerlog_fetch['queue_seconds'];
				$closerlog_term_reason[] = $closerlog_fetch['term_reason'];
			}
			$closerlog_array = array("call_date" => $closerlog_call_date, "length_in_sec" => $closerlog_length_in_sec, "status" => $closerlog_status, "user" => $closerlog_user, "campaign_id" => $closerlog_campaign_id, "list_id" => $closerlog_list_id, "queue_seconds" => $closerlog_queue_seconds, "term_reason" => $closerlog_term_reason);
			
			$agentlog_query = $astDB->rawQuery("SELECT agent_log_id,user,server_ip,event_time,lead_id,campaign_id,pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec,status,user_group,comments,sub_status FROM vicidial_agent_log WHERE lead_id = '$lead_id' $ul ORDER BY event_time DESC LIMIT 10000;");
			
			foreach ($agentlog_query as $agentlog_fetch){
				$agentlog_event_time[] = $agentlog_fetch['event_time'];
				$agentlog_campaign_id[] = $agentlog_fetch['campaign_id'];
				$agentlog_agent_log_id[] = $agentlog_fetch['agent_log_id'];
				$agentlog_pause_sec[] = $agentlog_fetch['pause_sec'];
				$agentlog_wait_sec[] = $agentlog_fetch['wait_sec'];
				$agentlog_talk_sec[] = $agentlog_fetch['talk_sec'];
				$agentlog_dispo_sec[] = $agentlog_fetch['dispo_sec'];
				$agentlog_status[] = $agentlog_fetch['status'];
				$agentlog_user_group[] = $agentlog_fetch['user_group'];
				$agentlog_sub_status[] = $agentlog_fetch['sub_status'];
			}
			$agentlog_array = array("event_time" => $agentlog_event_time, "campaign_id" => $agentlog_campaign_id, "agent_log_id" => $agentlog_agent_log_id, "pause_sec" => $agentlog_pause_sec, "wait_sec" => $agentlog_wait_sec, "talk_sec" => $agentlog_talk_sec, "dispo_sec" => $agentlog_dispo_sec, "status" => $agentlog_status, "user_group" => $agentlog_user_group, "sub_status" => $agentlog_sub_status);
			
			$record_query = $astDB->rawQuery("SELECT recording_id,channel,server_ip,extension,start_time,start_epoch,end_time,end_epoch,length_in_sec,length_in_min,filename,location,lead_id,user,vicidial_id FROM recording_log WHERE lead_id = '$lead_id' ORDER BY start_time DESC LIMIT 10000;");
			
			foreach ($record_query as $record_fetch){
				$record_start_time[] = $record_fetch['start_time'];
				$record_length_in_sec[] = gmdate("H:i:s", $record_fetch['length_in_sec']);
				$record_recording_id[] = $record_fetch['recording_id'];
				$record_filename[] = $record_fetch['filename'];
				$record_location[] = $record_fetch['location'];
				$record_user[] = $record_fetch['user'];
			}
			$record_array = array("start_time" => $record_start_time, "length_in_sec" => $record_length_in_sec, "recording_id" => $record_recording_id, "filename" => $record_filename, "location" => $record_location, "user" => $record_user);
			
			$list_id = "custom_".$list_id;
			$query_CF_list = $astDB->rawQuery("DESC $list_id;");
			if($query_CF_list){
				foreach ($query_CF_list as $field_list){
					$exec_query_CF_list = $field_list["Field"];

					if($exec_query_CF_list != "lead_id"){
						$list_fields[] = $exec_query_CF_list;
					}
				}
			}
			$fields = implode(",", $list_fields);
			$CF_sql = "SELECT $fields FROM $list_id WHERE lead_id = '$lead_id' LIMIT 10000;";
			$CF_query = $astDB->rawQuery($CF_sql);
			$CF_cnt = $astDB->getRowCount();
			
			if($CF_cnt > 0){
				$CF_fetch = $CF_query;

				 for($x=0;$x < count($list_fields);$x++){
				 	//if($CF_fetch[$x] !== NULL)
				 	$CF_data[$list_fields[$x]] =  str_replace(",", " | ", $CF_fetch[$x]);
				 }
			}

			$apiresults = array("result" => "success", "data" => $data, "is_customer" => $is_customer, "calls" => $calls_array, "closerlog" => $closerlog_array, "agentlog" => $agentlog_array, "record" => $record_array, "custom_fields" => $CF_data);
			
		}else{
			$err_msg = error_handle("41004", "lead_id");
			$apiresults = array("code" => "41004","result" => $err_msg);
		}
		$log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed the lead info of Lead ID: $lead_id", $log_group);
    }
?>
