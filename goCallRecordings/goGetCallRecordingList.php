<?php
    #######################################################
    #### Name: goGetCallRecordingList.php              ####
    #### Description: API to get all call recordings   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    $limit = $_REQUEST['limit'];
    $requestDataPhone = $_REQUEST['requestDataPhone'];   
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

if(!empty($requestDataPhone)) {
	$sqlPhone = "AND vl.phone_number LIKE '%$requestDataPhone%'";
}


//search via phone
//	$query = "SELECT CONCAT(vl.first_name,' ',vl.last_name) AS full_name, vl.last_local_call_time, vl.phone_number, rl.recording_id, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user, cl.start_time, cl.end_time, cl.uniqueid FROM recording_log AS rl, call_log as cl, vicidial_list vl WHERE rl.vicidial_id = cl.uniqueid AND rl.lead_id = vl.lead_id $sql2 ORDER BY cl.uniqueid DESC LIMIT 20;";
	$query = "SELECT CONCAT(vl.first_name,' ',vl.last_name) AS full_name, vl.last_local_call_time, vl.phone_number, rl.recording_id, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user, cl.start_time, cl.end_time, cl.uniqueid FROM recording_log AS rl, call_log as cl, vicidial_list vl WHERE rl.vicidial_id = cl.uniqueid AND rl.lead_id = vl.lead_id $sqlPhone  ORDER BY cl.uniqueid DESC LIMIT 50;";
//	$query = "SELECT CONCAT(vl.first_name,' ',vl.last_name) AS full_name, vl.last_local_call_time, vl.phone_number, rl.recording_id, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user, cl.start_time, cl.end_time, cl.uniqueid FROM recording_log AS rl, call_log as cl, vicidial_list vl WHERE rl.vicidial_id = cl.uniqueid AND rl.lead_id = vl.lead_id AND vl.phone_number='g' ORDER BY cl.uniqueid DESC LIMIT 50";
	
//search via date
//	$query = "SELECT vl.last_local_call_time, vl.phone_number, rl.recording_id, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user, cl.start_time, cl.end_time, cl.uniqueid FROM recording_log AS rl, call_log as cl, vicidial_list vl WHERE rl.vicidial_id = cl.uniqueid AND rl.lead_id = vl.lead_id AND vl.last_local_call_time LIKE '%$searchString%' ORDER BY cl.uniqueid DESC";
   	
	$rsltv = mysqli_query($link, $query);

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataLeadId[] = $fresults['lead_id'];
		$dataUniqueid[] = $fresults['uniqueid'];
       		$dataStatus[] = $fresults['status'];
		$dataUser[] = $fresults['user'];
		// $dataListId[] = $fresults['list_id'];
		$dataListId[] = $fresults['uniqueid'];
		$dataPhoneNumber[] = $fresults['phone_number'];
		$dataFullName[] = $fresults['full_name'];
		// $dataLastLocalCallTime[] = $fresults['last_local_call_time'];
		$dataStartLastLocalCallTime[] = $fresults['start_time'];
		$dataEndLastLocalCallTime[] = $fresults['end_time'];
		$dataLocation[] = $fresults['location'];

	$query1 = "SELECT count(*) AS `cnt` FROM recording_log WHERE lead_id='{$fresults['lead_id']}';";
	$rsltv1 = mysqli_query($link, $query1);
	while($fresults1 = mysqli_fetch_array($rsltv1)){
		$dataCount[] = $fresults1['cnt'];	
	}

	$query3 = "SELECT a.phone_number FROM vicidial_list a, recording_log b WHERE a.lead_id=b.lead_id AND ";

   		$apiresults = array(
			"result" => "success",
			"cnt" => $dataCount,
			"lead_id" => $dataLeadId,
			"uniqueid" => $dataUniqueid,
			"status" => $dataStatus,
			"users" => $dataUser,
			"list_id" => $dataListId,
			"phone_number" => $dataPhoneNumber,
			"full_name" => $dataFullName,
			"start_last_local_call_time" => $dataStartLastLocalCallTime,
			"end_last_local_call_time" => $dataEndLastLocalCallTime,
			"location" => $dataLocation
		);
	}

?>
