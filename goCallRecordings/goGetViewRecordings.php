<?php
    #######################################################
    #### Name: goGetViewRecordings.php	               ####
    #### Description: API to get specific recordings   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    //$limit = $_REQUEST['limit'];
    //if($limit < 1){ $limit = 20; } else { $limit = $limit; }
    //$leadid = $_REQUEST['leadid'];
    $uniqueid = $_REQUEST['uniqueid'];

        if($uniqueid == null) {
                $apiresults = array("result" => "Error: Empty Unique ID");
        } else {
 
		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
			$ul='';
		} else { 
			$ul = "WHERE user_group='$groupId'";  
		}
		
		
		$query = "
		SELECT
			rl.recording_id,
			rl.length_in_sec,
			rl.filename,
			rl.location,
			rl.lead_id,
			rl.user,
			cl.start_time, 
			cl.uniqueid 
		FROM recording_log AS rl 
		LEFT JOIN call_log as cl 
			ON rl.vicidial_id = cl.uniqueid 
		WHERE
			cl.uniqueid = '".$uniqueid."'";

		$rsltv = mysqli_query($link, $query);
		
		$countResult = mysqli_num_rows($rsltv);
		if($countResult > 0) {
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
				$dataLastLocalCallTime[] = $fresults['start_time'];
				$dataLocation[] = $fresults['location'];
		
			$query1 = "SELECT count(*) AS `cnt` FROM recording_log WHERE lead_id='{$fresults['lead_id']}';";
			$rsltv1 = mysqli_query($link, $query1);
			while($fresults1 = mysqli_fetch_array($rsltv1)){
				$dataCount[] = $fresults1['cnt'];	
			}
		
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
					"last_local_call_time" => $dataLastLocalCallTime,
					"location" => $dataLocation
				);
			}
		}else {
                        $apiresults = array("result" => "Error: Doesn't have Recordings.");
		}

	}
?>
