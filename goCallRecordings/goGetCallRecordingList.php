<?php
    #######################################################
    #### Name: goGetCallRecordingList.php              ####
    #### Description: API to get all call recordings   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $limit = mysqli_real_escape_string($link, $_REQUEST['limit']);
    $requestDataPhone = mysqli_real_escape_string($link, $_REQUEST['requestDataPhone']);
	$start_filterdate = mysqli_real_escape_string($link, $_REQUEST['start_filterdate']);
	$end_filterdate = mysqli_real_escape_string($link, $_REQUEST['end_filterdate']);
	$agent_filter = mysqli_real_escape_string($link, $_REQUEST['agent_filter']);
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$log_ip = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
	
    if($limit < 1){ $limit = 20; } else { $limit = 0; }
 
    $groupId = go_get_groupid($session_user);
	
	if (checkIfTenant($groupId)) {
		$ul="AND vl.user_group = '$groupId'";
	} else {
		if($groupId !== "ADMIN"){
			$stringv = go_getall_allowed_users($groupId);
			$ul = "AND rl.user IN ($stringv)";
		}else{
			$ul = "";
		}
	}
	
/*	$query = "
		SELECT
			rl.recording_id,
			rl.length_in_sec,
			rl.filename,
			rl.location,
			rl.lead_id,
			rl.user,
			cl.start_time,
			cl.end_time, 
			cl.uniqueid 
		FROM recording_log AS rl 
		LEFT JOIN call_log as cl 
			ON rl.vicidial_id = cl.uniqueid 
		ORDER BY cl.uniqueid DESC 
		LIMIT ".$limit; */

$goLimit = "25";
		
if(!empty($requestDataPhone)) {
	$sqlPhone = "AND vl.phone_number LIKE '$requestDataPhone%'";
	$goLimit = "500";
}else{
	$sqlPhone = "";
}

if($start_filterdate != "" && $end_filterdate != "" && $start_filterdate != $end_filterdate){
	$goLimit = "1000";
	$filterdate = "AND date_format(rl.end_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_filterdate' AND '$end_filterdate'";
}else{
	$filterdate = "";
}

if(!empty($agent_filter)){
	$goLimit = "1000";
	$filteragent = "AND rl.user = '$agent_filter'";
}else{
	$filteragent = "";
}

//search via phone
//	$query = "SELECT CONCAT(vl.first_name,' ',vl.last_name) AS full_name, vl.last_local_call_time, vl.phone_number, rl.recording_id, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user, cl.start_time, cl.end_time, cl.uniqueid FROM recording_log AS rl, call_log as cl, vicidial_list vl WHERE rl.vicidial_id = cl.uniqueid AND rl.lead_id = vl.lead_id $sql2 ORDER BY cl.uniqueid DESC LIMIT 20;";
//	$query = "SELECT CONCAT(vl.first_name,' ',vl.last_name) AS full_name, vl.last_local_call_time, vl.phone_number, rl.recording_id, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user, cl.start_time, cl.end_time, cl.uniqueid FROM recording_log AS rl, call_log as cl, vicidial_list vl WHERE rl.vicidial_id = cl.uniqueid AND rl.lead_id = vl.lead_id $sqlPhone $filterdate $filteragent ORDER BY rl.end_time DESC LIMIT $goLimit;";
	
	$query = "SELECT CONCAT(vl.first_name,' ',vl.last_name) AS full_name, rl.vicidial_id, vl.last_local_call_time, vl.phone_number, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user, rl.start_time, rl.end_time, rl.recording_id, rl.b64encoded FROM recording_log rl, vicidial_list vl WHERE rl.lead_id = vl.lead_id $sqlPhone $filterdate $filteragent $ul ORDER BY rl.start_time DESC LIMIT $goLimit;";
	
//search via date
//	$query = "SELECT vl.last_local_call_time, vl.phone_number, rl.recording_id, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user, cl.start_time, cl.end_time, cl.uniqueid FROM recording_log AS rl, call_log as cl, vicidial_list vl WHERE rl.vicidial_id = cl.uniqueid AND rl.lead_id = vl.lead_id AND vl.last_local_call_time LIKE '%$searchString%' ORDER BY cl.uniqueid DESC";
   	
	$rsltv = mysqli_query($link, $query);
		
	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$location = $fresults['location'];
		if (strlen($location)>2) {
			$URLserver_ip = $location;
			$URLserver_ip = preg_replace('/http:\/\//i', '', $URLserver_ip);
			$URLserver_ip = preg_replace('/https:\/\//i', '', $URLserver_ip);
			$URLserver_ip = preg_replace('/\/.*/i', '', $URLserver_ip);
			$stmt="SELECT count(*) FROM servers WHERE server_ip='$URLserver_ip';";
			$rsltx=mysqli_query($link, $stmt);
			$rowx=mysqli_fetch_row($rsltx);
			
			if ($rowx[0] > 0) {
				$stmt="SELECT recording_web_link,alt_server_ip,external_server_ip FROM servers WHERE server_ip='$URLserver_ip';";
				$rsltx=mysqli_query($link, $stmt);
				$rowx=mysqli_fetch_row($rsltx);
				
				if (preg_match("/ALT_IP/i",$rowx[0])) {
					$location = preg_replace("/$URLserver_ip/i", "$rowx[1]", $location);
				}
				if (preg_match("/EXTERNAL_IP/i",$rowx[0])) {
					$location = preg_replace("/$URLserver_ip/i", "$rowx[2]", $location);
				}
			}
		}
		
		$dataLeadId[] = $fresults['lead_id'];
		$dataUniqueid[] = $fresults['vicidial_id'];
        $dataStatus[] = $fresults['status'];
		$dataUser[] = $fresults['user'];
		$dataPhoneNumber[] = $fresults['phone_number'];
		$dataFullName[] = $fresults['full_name'];
		$dataLastLocalCallTime[] = $fresults['last_local_call_time'];
		$dataStartLastLocalCallTime[] = $fresults['start_time'];
		$dataEndLastLocalCallTime[] = $fresults['end_time'];
		$dataLocation[] = $location;
		$dataRecordingID[] = $fresults['recording_id'];
		$dataB64encoded[] = $fresults['b64encoded'];
		
	}

	$query1 = "SELECT count(*) AS `cnt` FROM recording_log WHERE lead_id='{$fresults['lead_id']}';";
	$rsltv1 = mysqli_query($link, $query1);
	
	while($fresults1 = mysqli_fetch_array($rsltv1)){
		$dataCount[] = $fresults1['cnt'];	
	}

	$log_id = log_action($linkgo, 'VIEW', $log_user, $log_ip, "View the Call Recording List", $log_group);

	$apiresults = array(
		"result" => "success",
		"query" => $query,
		"cnt" => $dataCount,
		"lead_id" => $dataLeadId,
		"uniqueid" => $dataUniqueid,
		"status" => $dataStatus,
		"users" => $dataUser,
		"phone_number" => $dataPhoneNumber,
		"full_name" => $dataFullName,
		"last_local_call_time" => $dataLastLocalCallTime,
		"start_last_local_call_time" => $dataStartLastLocalCallTime,
		"end_last_local_call_time" => $dataEndLastLocalCallTime,
		"location" => $dataLocation,
		"recording_id" => $dataRecordingID,
		"b64encoded" => $dataB64encoded,
		"query" => $query
	);
	

?>
