<?php
    ######################################################
    #### Name: goGetAgentLog.php  	####
    #### Description: API to get agent log of user  	####
    #### Copyright: GOAutoDial Inc. (c) 2016  	####
    #### Written by: Alexander Abenoja  	####
    ######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $user = mysqli_real_escape_string($link, $_REQUEST['user']);
    $start_date = mysqli_real_escape_string($link, $_REQUEST['start_date']);
	$end_date = mysqli_real_escape_string($link, $_REQUEST['end_date']);
	
    ### Check user_id if its null or empty
    if($user === "" || $user === NULL) { 
            $apiresults = array("result" => "Error: Incomplete data passed."); 
    } else {
        $groupId = go_get_groupid($session_user);
		$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
		
        if (checkIfTenant($groupId)) {
            $ul = "user='$user'";
        } else {
			if($groupId !== "ADMIN")
				$ul = "user='$user' AND user_group='$groupId'";
			else
				$ul = "user='$user'";
        }
		
		$date = date("Y-m-d");
		if($start_date !== ""){
			$start_date .= " 00:00:00";
			$end_date .= " 23:59:59";
			$daterange = "AND (date_format(call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
			$limit = "LIMIT 10000";
		}else{
			$start_date = "00:00:00";
			$end_date = "23:59:59";
			$daterange = "AND (date_format(call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$date $start_date' AND '$date $end_date')";
			$limit = "LIMIT 100";
		}
		
		//$query = "SELECT DISTINCT(val.agent_log_id), val.user, val.event_time, val.status, val.sub_status, vl.phone_number, val.campaign_id, val.user_group, vl.list_id, val.lead_id, vl.term_reason FROM vicidial_agent_log val, vicidial_log vl WHERE val.uniqueid = vl.uniqueid $ul $daterange ORDER BY val.event_time DESC $limit;";
		$outbound_query = "SELECT uniqueid, lead_id, list_id, campaign_id, call_date, start_epoch, end_epoch, length_in_sec, status, phone_code, phone_number, user, comments, processed, user_group, term_reason, alt_dial FROM vicidial_log WHERE $ul $daterange ORDER BY call_date DESC LIMIT 10000;";
		$exec_outbound_query = mysqli_query($link, $outbound_query);
		
		while($outbound_fetch = mysqli_fetch_array($exec_outbound_query)){
			$agent_user[] = $outbound_fetch['user'];
			$event_time[] = $outbound_fetch['call_date'];
			$status[] = $outbound_fetch['status'];
			$phone_number[] = $outbound_fetch['phone_number'];
			$campaign_id[] = $outbound_fetch['campaign_id'];
			$user_group[] = $outbound_fetch['user_group'];
			$list_id[] = $outbound_fetch['list_id'];
			$lead_id[] = $outbound_fetch['lead_id'];
			$term_reason[] = $outbound_fetch['term_reason'];
		}
		$outbound_array = array("user" => $agent_user, "event_time" => $event_time, "status" => $status, "phone_number" => $phone_number, "campaign_id" => $campaign_id, "user_group" => $user_group, "list_id" => $list_id, "lead_id" => $lead_id, "term_reason" => $term_reason);
		
		$closerlog_query = mysqli_query($link, "SELECT closecallid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed,queue_seconds,user_group,xfercallid,term_reason,uniqueid,agent_only FROM vicidial_closer_log WHERE $ul $daterange ORDER BY call_date DESC LIMIT 10000;");
			
		while($closerlog_fetch = mysqli_fetch_array($closerlog_query)){
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
		
		if($start_date !== ""){
			//$start_date .= " 00:00:00";
			//$end_date .= " 23:59:59";
			$daterange1 = "AND (date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
			$daterange2 = "AND (date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
			$limit = "LIMIT 10000";
		}else{
			$start_date = "00:00:00";
			$end_date = "23:59:59";
			$daterange1 = "AND (date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$date $start_date' AND '$date $end_date')";
			$daterange2 = "AND (date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$date $start_date' AND '$date $end_date')";
			$limit = "LIMIT 100";
		}
		$userlog_query = "(SELECT agent_log_id, user, sub_status, event_time, campaign_id, user_group FROM vicidial_agent_log WHERE $ul and sub_status != 'NULL' $daterange1) UNION (SELECT user_log_id, user, event, event_date, campaign_id, user_group FROM vicidial_user_log WHERE $ul $daterange2) ORDER BY event_time DESC;";
		//SELECT user_log_id, event, event_date, campaign_id, user_group FROM vicidial_user_log WHERE $ul $daterange ORDER BY event_date DESC LIMIT 10000;
		$exec_userlog_query = mysqli_query($link, $userlog_query);
		while($userlog_fetch = mysqli_fetch_array($exec_userlog_query)){
			$userlog_log_id[] = $userlog_fetch['agent_log_id'];
			$userlog_event[] = $userlog_fetch['sub_status'];
			$userlog_event_date[] = $userlog_fetch['event_time'];
			$userlog_campaign_id[] = $userlog_fetch['campaign_id'];
			$userlog_user_group[] = $userlog_fetch['user_group'];
		}
		$userlog_array = array("user_log_id" => $userlog_log_id, "event" => $userlog_event, "event_date" => $userlog_event_date, "campaign_id" => $userlog_campaign_id, "user_group" => $userlog_user_group);
		/*
		while($agentlog_fetch = mysqli_fetch_array($exec_outbound_query)){
			$agent_log_id[] = $agentlog_fetch['agent_log_id'];
			$agent_user[] = $agentlog_fetch['user'];
			$event_time[] = $agentlog_fetch['event_time'];
			$status[] = $agentlog_fetch['status'];
			$phone_number[] = $agentlog_fetch['phone_number'];
			$campaign_id[] = $agentlog_fetch['campaign_id'];
			$user_group[] = $agentlog_fetch['user_group'];
			$list_id[] = $agentlog_fetch['list_id'];
			$lead_id[] = $agentlog_fetch['lead_id'];
			$term_reason[] = $agentlog_fetch['term_reason'];
		}*/
		
		$apiresults = array("result" => "success", "query" => $userlog_query, "outbound" => $outbound_array, "inbound" => $closerlog_array, "userlog" => $userlog_array);
		
		$log_id = log_action($linkgo, 'VIEW', $user, $ip_address, "Viewed the agent log of Agent: $user", $groupId);
	}
?>

