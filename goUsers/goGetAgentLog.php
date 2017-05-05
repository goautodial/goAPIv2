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
		if($user === null)
        $groupId = go_get_groupid($user);
		$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
		
        if (checkIfTenant($groupId)) {
            $ul = "AND user='$user'";
        } else { 
            $ul = "AND user='$user' AND user_group='$groupId'";
			if($groupId !== "ADMIN")
				$notAdminSQL = "AND user_group != 'ADMIN'";
        }
		
		if($start_date !== ""){
			$start_date .= " 00:00:00";
			$end_date .= " 23:59:59";
			$daterange = "AND (date_format(val.event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
		}else{
			$daterange = "";
		}
		
		$query = "SELECT DISTINCT(val.agent_log_id), val.user, val.event_time, val.status, vl.phone_number, val.campaign_id, val.user_group, vl.list_id, val.lead_id, vl.term_reason FROM vicidial_agent_log val, vicidial_log vl WHERE val.lead_id = vl.lead_id AND val.user = vl.user AND val.user = '$user' $daterange ORDER BY val.event_time DESC LIMIT 10000;";
		$agentlog_query = mysqli_query($link, $query);
			
			while($agentlog_fetch = mysqli_fetch_array($agentlog_query)){
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
			}
		$apiresults = array("result" => "success", "query" => $query, "agent_log_id" => $agent_log_id, "user" => $agent_user, "event_time" => $event_time, "status" => $status, "phone_number" => $phone_number, "campaign_id" => $campaign_id, "user_group" => $user_group, "list_id" => $list_id, "lead_id" => $lead_id, "term_reason" => $term_reason);
		
		$log_id = log_action($linkgo, 'VIEW', $user, $ip_address, "Viewed the agent log of Agent: $user", $groupId);
	}
?>

