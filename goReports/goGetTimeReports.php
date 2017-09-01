<?php
    /////////////////////////////////////////////////////////
    /// Name: goGetAgentLog.php 						 ///
    /// Description: API to get custom time report 		 ///
    /// Copyright: GOAutoDial Inc. (c) 2016 			 ///
    /// Written by: Alexander Abenoja 					 ///
    ////////////////////////////////////////////////////////
    include_once ("../goFunctions.php");
    
    $default_date = date("Y-m-d");
	$def_start_date .= $default_date." 00:00:00";
	$def_end_date .= $default_date." 23:59:59";

    // POST or GET Variables
    $user = mysqli_real_escape_string($link, $_REQUEST['user']);
    $user_id = mysqli_real_escape_string($link, $_REQUEST['user_id']);
    $start_date = mysqli_real_escape_string($link, $_REQUEST['fromDate']);
    if(empty($start_date))
    	$start_date = $def_start_date;
	$end_date = mysqli_real_escape_string($link, $_REQUEST['toDate']);
	if(empty($end_date))
		$end_date = $def_end_date;
	$campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
	$groupId = go_get_groupid($session_user);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
	$id = mysqli_real_escape_string($link, $_REQUEST['id']);
	$export = mysqli_real_escape_string($link, $_REQUEST['export']);
	$duration_cmd = mysqli_real_escape_string($link, $_REQUEST['duration_cmd']); //duration if 1, enabled

	if(empty($duration_cmd))
		$duration_cmd = 0;

	$datetime1 = date_create($start_date);
	$datetime2 = date_create($end_date);
	$date_difference = date_diff($datetime1, $datetime2);
	$difference = $date_difference->format("%m");
    
    // Check user_id if its null or empty
    if(empty($session_user)) { 
        $err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
    }elseif($difference > 3){
    	$err_msg = error_handle("41004", "date range. The allowed date range is 3 months or less.");
		$apiresults = array("code" => "41004", "result" => $err_msg);
    }elseif(!is_numeric($id) && !empty($id)){
    	$err_msg = error_handle("41002", "id");
		$apiresults = array("code" => "41002", "result" => $err_msg);
    } else{
        if (checkIfTenant($groupId)) {
            $ul = "";
        } else {
			if($groupId !== "ADMIN")
				$ul = "AND vl.user_group='$groupId'";
			else
				$ul = "";
        }

        if(!empty($user))
        	$user_query = "AND vl.user = '$user'";
		else
			$user_query = "";

		if(!empty($user_id))
        	$userid_query = "AND vu.user_id = '$user_id'";
		else
			$userid_query = "";

		if(!empty($campaign_id))
        	$campaign_query = "AND vl.campaign_id = '$campaign_id'";
        else
        	$campaign_query = "";

		if(!empty($start_date) && !empty($end_date)){
			$daterange1 = " AND (date_format(vl.event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
			$daterange2 = " AND (date_format(vl.event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
			$limit = "LIMIT 10000";
		}else{
			$daterange1 = " AND (date_format(vl.event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
			$daterange2 = " AND (date_format(vl.event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
			$limit = "LIMIT 100";
		}

		$agent_id_query = "";
		$user_id_query = "";
		if(!empty($id)){
			// $check_agent = mysqli_query($link, "SELECT agent_log_id, event_time FROM vicidial_agent_log WHERE agent_log_id = '$id';");
			// $num_check_agent = mysqli_num_rows($check_agent);

			// if($num_check_agent > 0){
			// 	$agent_id_query = "AND agent_log_id > '$id'";
			// 	$fetch_agent_date = mysqli_fetch_array($check_agent);
			// 	$start_date = $fetch_agent_date['event_time'];
			// 	$daterange1 = " (date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
			// 	$daterange2 = " (date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
			// }else{
				$check_user = mysqli_query($link, "SELECT user_log_id, event_date FROM vicidial_user_log WHERE user_log_id = '$id';")or die(mysqli_error($link));
				$num_check_user = mysqli_num_rows($check_user);
				if($num_check_user > 0){
					$user_id_query = "AND user_log_id > '$id'";
					$fetch_user_date = mysqli_fetch_array($check_user);
					$start_date = $fetch_user_date['event_date'];
					$daterange1 = "AND (date_format(vl.event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
					$daterange2 = "AND (date_format(vl.event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
				}
			//}
		}
		
		$userlog_query = "SELECT vl.user_log_id, vl.user, vl.event, vl.event_date, vl.campaign_id, vl.user_group, vu.user_id FROM vicidial_user_log vl, vicidial_users vu WHERE vl.user=vu.user $ul $daterange2 $user_query $userid_query $campaign_query $user_id_query ORDER BY user_log_id ASC,event_date ;";
		$exec_userlog_query = mysqli_query($link, $userlog_query) or die(mysqli_error($link));
		
		$row = 1;
		while($userlog_fetch = mysqli_fetch_array($exec_userlog_query)){
			$log_id = $userlog_fetch['user_log_id'];
			$action = $userlog_fetch['event'];
			$event_time = $userlog_fetch['event_date'];
			$campaign_id = $userlog_fetch['campaign_id'];
			$user_group = $userlog_fetch['user_group'];

			$userlog[] = array("id" => $log_id, "user_id" => $userlog_fetch['user_id'],"user" => $userlog_fetch['user'], "action" => $action, "event_time" => $event_time, "campaign_id" => $campaign_id, "user_group" => $user_group);
			$row++;
		}
	
		// $userlog_query = "(SELECT agent_log_id, user, sub_status, event_time, campaign_id, user_group FROM vicidial_agent_log WHERE $ul $daterange1 $user_query $campaign_query $agent_id_query) UNION (SELECT user_log_id, user, event, event_date, campaign_id, user_group FROM vicidial_user_log WHERE $ul $daterange2 $user_query $campaign_query $user_id_query) ORDER BY event_time ASC, agent_log_id;";
		// //SELECT user_log_id, event, event_date, campaign_id, user_group FROM vicidial_user_log WHERE $ul $daterange ORDER BY event_date DESC LIMIT 10000;
		// $exec_userlog_query = mysqli_query($link, $userlog_query) or die(mysqli_error($link));

		// $lock_date = mysqli_fetch_array($exec_userlog_query);
		// $lock_date_fetch = $lock_date['event_time'];

		// $row = 1;
		// $login_pause = 0;
		// $prev_time = "";
		// //$force = 0;
		// //$prev_time = "";
		// while($userlog_fetch = mysqli_fetch_array($exec_userlog_query)){
		// 	$log_id = $userlog_fetch['agent_log_id'];
		// 	$sub_status = $userlog_fetch['sub_status'];
		// 	$event_time = $userlog_fetch['event_time'];

		// 	$duration = 0;
		// 	$sum = 0;
			
		// 	// if($event_time === $prev_time)
		// 	// 	$login_pause = 1;

		// 	if($lock_date_fetch <= $userlog_fetch['event_time']){
		// 		$get_call_log_query = "SELECT event_time, pause_epoch, pause_sec, wait_epoch, wait_sec, talk_epoch, talk_sec, dispo_epoch, dispo_sec FROM vicidial_agent_log WHERE agent_log_id = '".$log_id."';";
		// 		$get_call_log = mysqli_query($link, $get_call_log_query) or die(mysqli_error($linkgo));
		// 		$num_call_log = mysqli_num_rows($get_call_log);

		// 		if($num_call_log > 0){
		// 			while($fetch_log = mysqli_fetch_array($get_call_log)){
		// 				//$event_timestamp = strtotime($fetch_log['event_time']);
		// 				$pause_timestamp = $fetch_log['pause_epoch'];
		// 					$pause = date('Y-m-d H:i:s', $pause_timestamp);
		// 				$pause_sec = $fetch_log['pause_sec'];
		// 				//	$pause_sec = gmdate('H:i:s', $pause_sec);
		// 				$wait = $fetch_log['wait_epoch'];
		// 					$wait = date('Y-m-d H:i:s', $wait);
		// 				$wait_sec = $fetch_log['wait_sec'];
		// 				$talk_sec = $fetch_log['talk_sec'];
		// 				$dispo_sec = $fetch_log['dispo_sec'];
		// 				//	$wait_sec = gmdate('H:i:s', $wait_sec);
		// 				$dispo = $fetch_log['dispo_epoch'];
		// 				//	$dispo = gmdate('H:i:s', $dispo);
		// 				$talk = $fetch_log['talk_epoch'];
		// 				//	$talk = gmdate('H:i:s', $talk);
		// 			}

		// 			// 	$action = "FORCE_LOGOUT";
		// 			// 	$userlog[] = array("id" => $userlog_fetch['agent_log_id'], "user" => $userlog_fetch['user'], "action" => $action, "event_time" => $pause, "campaign_id" => $userlog_fetch['campaign_id'], "user_group" => $userlog_fetch['user_group']);
		// 			// 	$force = 1;
		// 			// }else 
		// 			if(($pause > 0 && $wait > 0 && $sub_status == NULL) || ($pause_sec > 0)){
		// 				if($userlog_fetch['sub_status'] !== NULL){
		// 					$action = $userlog_fetch['sub_status'];
		// 					$userlog[] = array("id" => $userlog_fetch['agent_log_id'], "user" => $userlog_fetch['user'], "action" => $action, "event_time" => $event_time, "campaign_id" => $userlog_fetch['campaign_id'], "user_group" => $userlog_fetch['user_group']);
		// 					$row = $row + 1;
		// 				}

		// 				if(($event_time !== $pause && $userlog_fetch['sub_status'] !== NULL) || ($userlog_fetch['sub_status'] === NULL && $pause_sec > 3) || ($pause_sec > 4)){
		// 					$action = "PAUSE";

		// 					if($duration_cmd > 0){
		// 						$duration = gmdate('H:i:s', $pause_sec);
								
		// 						$userlog[] = array("id" => $userlog_fetch['agent_log_id'], "user" => $userlog_fetch['user'], "action" => $action, "event_time" => $pause, "duration" => $duration, "campaign_id" => $userlog_fetch['campaign_id'], "user_group" => $userlog_fetch['user_group']);
		// 					}else{
		// 						$userlog[] = array("id" => $userlog_fetch['agent_log_id'], "user" => $userlog_fetch['user'], "action" => $action, "event_time" => $pause, "campaign_id" => $userlog_fetch['campaign_id'], "user_group" => $userlog_fetch['user_group']);
		// 					}
		// 				}
						
		// 				//if($wait_sec > 0 || $dispo_sec > 0 || $talk_sec > 0){
		// 				if($wait_sec > 0 || ($pause != $wait && $pause_sec > 3 && $talk > 0 && $dispo > 0)){
		// 					$row = $row + 1;

		// 					$sum = $wait_sec + $talk_sec + $dispo_sec; 
							
		// 					$action = "RESUME";
		// 					if($duration_cmd > 0){
		// 						$duration = gmdate('H:i:s', $sum);
								 
		// 						$userlog[] = array("id" => $userlog_fetch['agent_log_id'], "user" => $userlog_fetch['user'], "action" => $action, "event_time" => $wait, "duration" => $duration, "campaign_id" => $userlog_fetch['campaign_id'], "user_group" => $userlog_fetch['user_group']);
		// 					}else{
		// 						$userlog[] = array("id" => $userlog_fetch['agent_log_id'], "user" => $userlog_fetch['user'], "action" => $action, "event_time" => $wait, "campaign_id" => $userlog_fetch['campaign_id'], "user_group" => $userlog_fetch['user_group']);
		// 					}
							
		// 				}
						
						
		// 			}else{
		// 				$action = $sub_status;
		// 				$userlog[] = array("id" => $userlog_fetch['agent_log_id'], "user" => $userlog_fetch['user'], "action" => $action, "event_time" => $event_time, "campaign_id" => $userlog_fetch['campaign_id'], "user_group" => $userlog_fetch['user_group']);
		// 			}
		// 			//die(" pause: ".$pause." wait: ".$wait." dispo: ".$dispo." talk: ".$talk." TOTAL: ".$total_pause);
		// 		}else{
		// 			$action = $userlog_fetch['sub_status'];
		// 			$userlog[] = array("id" => $userlog_fetch['agent_log_id'], "user" => $userlog_fetch['user'], "action" => $action, "event_time" => $event_time, "campaign_id" => $userlog_fetch['campaign_id'], "user_group" => $userlog_fetch['user_group']);
		// 		}
		// 	}

		// 	//$userlog_event[] = $userlog_fetch['sub_status'];
		// 	//$userlog_event_date[] = $userlog_fetch['event_time'];
		// 	//$userlog_campaign_id[] = $userlog_fetch['campaign_id'];
		// 	//$userlog_user_group[] = $userlog_fetch['user_group'];
		// 	$row++;
		// 	$login_pause = 0;
		// }

		if(is_numeric($export) && !empty($export) && $export == 1){
			if($userlog != NULL){
				$filename = "Custom_Time_Report_".$start_date."_".$end_date.".csv";
	        	header('Content-type: application/csv');
	        	header('Content-Disposition: attachment; filename='.$filename);

	        	echo $header = "ID,USER,ACTION,EVENT TIME,CAMPAIGN,USERGROUP\n";

	        	$count = 0;
		        for($i=0; $i <= count($userlog); $i++){
		            $count_row = $userlog[$i];
		            echo $count_row["id"].",";
		            echo $count_row["user"].",";
		            echo $count_row["action"].",";
		            echo $count_row["event_time"].",";
		            echo $count_row["campaign_id"].",";
		            echo $count_row["user_group"]."\n";
		        }
		        //echo $row;
			}else{
				$err_msg = error_handle("40001");
				//"query" => $userlog_query, 
				$apiresults = array("result" => "No records retrieved from: ".$start_date." - ".$end_date);
			}
        }else{
			if($userlog == NULL){
				$err_msg = error_handle("40001");
				//"query" => $userlog_query, 
				$apiresults = array("result" => "No records retrieved from: ".$start_date." - ".$end_date);
			}else{
				$apiresults = array("result" => "success", "data" => $userlog);
			}
		}
		

		//$log_id = log_action($linkgo, 'VIEW', $user, $ip_address, "Viewed the agent log of Agent: $user", $groupId);
	}
?>

