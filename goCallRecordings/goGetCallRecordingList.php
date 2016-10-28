<?php
    #######################################################
    #### Name: goGetCallRecordingList.php              ####
    #### Description: API to get all call recordings   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    $limit = $_REQUEST['limit'];
    $requestDataPhone = $_REQUEST['requestDataPhone'];
	$start_filterdate = mysqli_real_escape_string($link, $_REQUEST['start_filterdate']);
	$end_filterdate = mysqli_real_escape_string($link, $_REQUEST['end_filterdate']);
	$agent_filter = mysqli_real_escape_string($link, $_REQUEST['agent_filter']);
	
    if($limit < 1){ $limit = 20; } else { $limit = 0; }
 
    	$groupId = go_get_groupid($goUser);
    
	if (!checkIfTenant($groupId)) {
        	$ul='';
    	} else { 
		$ul = "WHERE user_group='$groupId'";  
	}

   	// $query = "SELECT lead_id,status,user,list_id,phone_number,CONCAT(first_name,' ',last_name) AS full_name,last_local_call_time FROM vicidial_list LIMIT $limit";
	
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
	$sqlPhone = "AND vl.phone_number LIKE '%$requestDataPhone%'";
	$goLimit = "500";
}else{
		$sqlPhone = "";
}

if($start_filterdate != "" && $end_filterdate != "" && $start_filterdate != $end_filterdate){
		$goLimit = "1000";
		//$filterdate = "AND ('$start_filterdate' <= rl.start_time and '$end_filterdate' >= rl.end_time)";
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
	
	$query = "SELECT CONCAT(vl.first_name,' ',vl.last_name) AS full_name, rl.vicidial_id, vl.last_local_call_time, vl.phone_number, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user, rl.start_time, rl.end_time FROM recording_log AS rl, vicidial_list vl WHERE rl.lead_id = vl.lead_id $sqlPhone $filterdate $filteragent ORDER BY rl.end_time DESC LIMIT $goLimit;";
	
//search via date
//	$query = "SELECT vl.last_local_call_time, vl.phone_number, rl.recording_id, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user, cl.start_time, cl.end_time, cl.uniqueid FROM recording_log AS rl, call_log as cl, vicidial_list vl WHERE rl.vicidial_id = cl.uniqueid AND rl.lead_id = vl.lead_id AND vl.last_local_call_time LIKE '%$searchString%' ORDER BY cl.uniqueid DESC";
   	
	$rsltv = mysqli_query($link, $query);
		
	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataLeadId[] = $fresults['lead_id'];
		$dataUniqueid[] = $fresults['vicidial_id'];
       	$dataStatus[] = $fresults['status'];
		$dataUser[] = $fresults['user'];
		// $dataListId[] = $fresults['list_id'];
		//$dataListId[] = $fresults['uniqueid'];
		$dataPhoneNumber[] = $fresults['phone_number'];
		$dataFullName[] = $fresults['full_name'];
		$dataLastLocalCallTime[] = $fresults['last_local_call_time'];
		$dataStartLastLocalCallTime[] = $fresults['start_time'];
		$dataEndLastLocalCallTime[] = $fresults['end_time'];
		$dataLocation[] = $fresults['location'];
		
	}

	$query1 = "SELECT count(*) AS `cnt` FROM recording_log WHERE lead_id='{$fresults['lead_id']}';";
	$rsltv1 = mysqli_query($link, $query1);
	
	while($fresults1 = mysqli_fetch_array($rsltv1)){
		$dataCount[] = $fresults1['cnt'];	
	}

	//$query3 = "SELECT a.phone_number FROM vicidial_list a, recording_log b WHERE a.lead_id=b.lead_id AND ";

   		$apiresults = array(
			"result" => "success",
			"query" => $query,
			"cnt" => $dataCount,
			"lead_id" => $dataLeadId,
			"uniqueid" => $dataUniqueid,
			"status" => $dataStatus,
			"users" => $dataUser,
			"list_id" => $dataListId,
			"phone_number" => $dataPhoneNumber,
			"full_name" => $dataFullName,
			"last_local_call_time" => $dataLastLocalCallTime,
			"start_last_local_call_time" => $dataStartLastLocalCallTime,
			"end_last_local_call_time" => $dataEndLastLocalCallTime,
			"location" => $dataLocation
		);
	

?>
