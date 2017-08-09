<?php
    /////////////////////////////////////////////////
    // Name: goGetReports.php                     ///
    // Description: API for reports               ///
    // Version: 4.0                               ///
    // Copyright: GOAutoDial Ltd. (c) 2011-2016   ///
    // Written by: Alexander Jim H. Abenoja       ///
    // License: AGPLv2                            ///
    /////////////////////////////////////////////////
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
ini_set('memory_limit', '2048M');

    include_once("../goFunctions.php");
	include_once("goReportsFunctions.php");	

	$fromDate = date("Y-m-d")." 00:00:00";
	$toDate = date("Y-m-d")." 23:59:59";
	// need function go_sec_convert();
    $pageTitle = strtolower(mysqli_real_escape_string($link, $_REQUEST['pageTitle']));
    $fromDate = mysqli_real_escape_string($link, $_REQUEST['fromDate']);
    $toDate = mysqli_real_escape_string($link, $_REQUEST['toDate']);
    $campaignID = mysqli_real_escape_string($link, $_REQUEST['campaignID']);
    $request = mysqli_real_escape_string($link, $_REQUEST['request']);
    $userID = mysqli_real_escape_string($link, $_REQUEST['userID']);
    $userGroup = mysqli_real_escape_string($link, $_REQUEST['userGroup']);
	$dispo_stats = mysqli_real_escape_string($link, $_REQUEST['statuses']);
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$log_ip = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
	
	$userGroup = go_get_groupid($session_user);
	
	$defPage = array("stats", "agent_detail", "agent_pdetail", "dispo", "call_export_report", "sales_agent", "sales_tracker", "inbound_report");

	//if(empty($session_user) || empty($pageTitle)){
	if(empty($session_user)){
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	}elseif(empty($fromDate) && empty($toDate)){
		$fromDate = date("Y-m-d")." 00:00:00";
		$toDate = date("Y-m-d")." 23:59:59";
		//die($fromDate." - ".$toDate);
	}elseif($pageTitle == "sales_tracker" && empty($request)){
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	}elseif($pageTitle == "sales_agent" && empty($request)){
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	}
	// elseif(!in_array($pageTitle, $defPage)){
	// 	$err_msg = error_handle("10004");
	// 	$apiresults = array("code" => "10004", "result" => $err_msg);
	// }
	elseif($pageTitle == "call_export_report"){
		$campaigns = mysqli_real_escape_string($link, $_REQUEST['campaigns']);
		$inbounds = mysqli_real_escape_string($link, $_REQUEST['inbounds']);
		$lists = mysqli_real_escape_string($link, $_REQUEST['lists']);
		$custom_fields = mysqli_real_escape_string($link, $_REQUEST['custom_fields']);
		$per_call_notes = mysqli_real_escape_string($link, $_REQUEST['per_call_notes']);
		$rec_location = mysqli_real_escape_string($link, $_REQUEST['rec_location']);
		
		$goReportsReturn = go_export_reports($fromDate, $toDate, $campaigns, $inbounds, $lists, $dispo_stats, $custom_fields, $per_call_notes, $rec_location, $userGroup, $link);
		
		$apiresults = array("result" => "success", "getReports" => $goReportsReturn);
	}else{
		$goReportsReturn = go_get_reports($pageTitle, $fromDate, $toDate, $campaignID, $request, $userID, $userGroup,$link, $dispo_stats, $linkgo);
		$apiresults = array("result" => "success", "getReports" => $goReportsReturn);
		//var_dump($goReportsReturn);
	}
	//2017-04-06 00:00:00    2017-04-06 07:41:00
	
   
	function go_export_reports($fromDate, $toDate, $campaigns, $inbounds, $lists, $dispo_stats, $custom_fields, $per_call_notes, $rec_location,$userGroup, $link){
		//$date_diff = go_get_date_diff($fromDate, $toDate);
        //$date_array = implode("','",go_get_dates($fromDate, $toDate));
		
		if($campaigns != "")
			$campaigns = explode(",",$campaigns);
		if($inbounds != "")
		    $inbounds = explode(",",$inbounds);
		if($lists != "")	
		    $lists = explode(",",$lists);
		if($dispo_stats != "")	
		    $dispo_stats = explode(",",$dispo_stats);
		
		$campaign_SQL = "";
		$group_SQL = "";
		$list_SQL = "";
		$status_SQL = "";
		
		$campaign_ct = count($campaigns);
		$group_ct = count($inbounds);
		$list_ct = count($lists);
		$status_ct = count($dispo_stats);
		
		if($campaigns != ""){
			$i=0;
			while($i < $campaign_ct){
				$campaign_SQL .= "'$campaigns[$i]',";
				$i++;
			}
			
			$campaign_SQL = preg_replace("/,$/i",'',$campaign_SQL);
			$campaign_SQL = "and vl.campaign_id IN($campaign_SQL)";
			$RUNcampaign=$i;
			
		}else{
			$RUNcampaign=0;
		}
		
		if($inbounds != ""){
			$i=0;
			while($i < $group_ct){
				if (strlen($inbounds[$i]) > 0) {
				  $group_SQL .= "'$inbounds[$i]',";
				}
				$i++;
			}
			
			$group_SQL = preg_replace("/,$/i",'',$group_SQL);
			if($group_ct > 0){
				$group_SQL = "and vcl.campaign_id IN($group_SQL)";
			}
			
			$RUNgroup=$i;
		}else{
			$RUNgroup=0;
		}
		
		if($lists != ""){
			$list_SQL = "";
			
			$i=0;
			while($i < $list_ct){
				$list_SQL .= "'$lists[$i]',";
				$i++;
			}
			if (in_array("ALL", $lists)){
				$list_SQL = "";
				$i=0;
				while($i < $campaign_ct){
					$camp_id = $campaigns[$i];
					$query_list = mysqli_query($link,"SELECT list_id FROM vicidial_lists WHERE campaign_id = '$camp_id';");
					while($fetch_list = mysqli_fetch_array($query_list)){
						$array_list[] = $fetch_list["list_id"];
					}
					$i++;
				}
				
			}
			else{
				$list_SQL = preg_replace("/,$/i",'',$list_SQL);
				$list_SQL = "and vi.list_id IN($list_SQL)";
				$i=0;
				while($i < $list_ct){
					$array_list[] = $lists[$i];
					$i++;
				}
			}
		}
		
		if($dispo_stats != ""){
			$i=0;
			while($i < $status_ct){
				$status_SQL .= "'$dispo_stats[$i]',";
				$i++;
			}
			if ( (in_array("ALL", $dispo_stats) ) or ($status_ct < 1) ){
				$status_SQL = "";
			}
			else{
				$status_SQL = preg_replace("/,$/i",'',$status_SQL);
				$status_SQL = "and vl.status IN ($status_SQL)";
			}
		}
		
		//$user_group_SQL = "AND (CASE WHEN vl.user!='VDAD' THEN vl.user_group = '$userGroup' ELSE 1=1 END)";
		if($userGroup !== "ADMIN"){
			$stringv = go_getall_allowed_users($userGroup);
			$user_group_SQL = "AND vl.user IN ($stringv)";
		}else{
			$user_group_SQL = "";
		}
		
		$export_fields_SQL = "";
		
		if ($RUNcampaign > 0 && $RUNgroup < 1){
			$query = "SELECT vl.call_date,vl.phone_number,vl.status,vl.user,vu.full_name,vl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vl.length_in_sec,vl.user_group,vl.alt_dial,vi.rank,vi.owner,vi.lead_id,vl.uniqueid,vi.entry_list_id $export_fields_SQL FROM vicidial_users vu, vicidial_log vl,vicidial_list vi WHERE (date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate') and vu.user=vl.user and vi.lead_id=vl.lead_id $list_SQL $campaign_SQL $user_group_SQL $status_SQL group by vl.call_date order by vl.call_date ";
		}
		
		if ($RUNgroup > 0 && $RUNcampaign < 1){
		 	//if($rec_location == "Y")
            //    $rec_location_where = "and re.lead_id=vcl.lead_id and vcl.closecallid = re.vicidial_id";
//                       $query = "SELECT vl.call_date,vl.phone_number,vl.status,vl.user,vu.full_name,vl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vl.length_in_sec,vl.user_group,vl.alt_dial,vi.rank,vi.owner,vi.lead_id,vl.uniqueid,vi.entry_list_id $export_fields_SQL $rec_location_fields FROM vicidial_users vu, vicidial_log vl,vicidial_list vi $rec_location_from WHERE (date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate') and vu.user=vl.user and vi.lead_id=vl.lead_id $rec_location_where $list_SQL $group_SQL $user_group_SQL $status_SQL order by vl.call_date ";
                        //$query = "SELECT vl.call_date,vl.phone_number,vl.status,vl.user,vu.full_name,vl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vl.length_in_sec,vl.user_group,vcl.queue_seconds,vi.rank,vi.owner,vi.lead_id,vcl.closecallid,vi.entry_list_id,vl.uniqueid $export_fields_SQL $rec_location_fields FROM vicidial_users vu, vicidial_log vl, vicidial_closer_log vcl,vicidial_list vi $rec_location_from where (date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate') and vu.user=vl.user and vi.lead_id=vl.lead_id AND vl.lead_id = vcl.lead_id $rec_location_where $list_SQL $group_SQL $user_group_SQL $status_SQL order by vl.call_date ";
            $query = "SELECT vcl.call_date,vcl.phone_number,vcl.status,vcl.user,vu.full_name,vcl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vcl.length_in_sec,vcl.user_group,vcl.queue_seconds,vi.rank,vi.owner,vi.lead_id, vcl.closecallid, vcl.uniqueid, vi.entry_list_id $export_fields_SQL FROM vicidial_users vu, vicidial_closer_log vcl,vicidial_list vi where (date_format(vcl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate') and vu.user=vcl.user and vi.lead_id=vcl.lead_id AND vcl.lead_id = vcl.lead_id $list_SQL $group_SQL $user_group_SQL $status_SQL order by vcl.call_date";
		}
		
		if ($RUNcampaign > 0 && $RUNgroup > 0){
            //if($rec_location == "Y")
            //$rec_location_where = "AND ((re.lead_id=vl.lead_id and vl.uniqueid = re.vicidial_id) OR (re.lead_id=vcl.lead_id and vcl.closecallid = re.vicidial_id))";
            
            $query = "SELECT vl.call_date,vl.phone_number,vl.status,vl.user,vu.full_name,vl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vl.length_in_sec,vl.user_group,vcl.queue_seconds,vi.rank,vi.owner,vi.lead_id,vl.uniqueid,vl.alt_dial, vcl.closecallid,vi.entry_list_id $export_fields_SQL FROM vicidial_users vu,vicidial_closer_log vcl, vicidial_log vl, vicidial_list vi where (date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate') and vu.user=vl.user and vi.lead_id=vl.lead_id AND vl.lead_id=vcl.lead_id $list_SQL $group_SQL $campaign_SQL $user_group_SQL $status_SQL order by vl.call_date ";
        }


		//$query = "SELECT * FROM vicidial_list LIMIT 100;";
		$result = mysqli_query($link, $query) or die(mysqli_error($link));
		
		$num_column = mysqli_num_fields($result);
		
		//$filename = $date_diff.".csv";
		
		//OUTPUT DATA HEADER//
		while ($fieldinfo=mysqli_fetch_field($result))
		{
			$csv_header[] = $fieldinfo->name;
		}
		if($per_call_notes == "Y"){
			array_push($csv_header, "call_notes");
		}

		//if($rec_location == "Y"){
		//	array_push($csv_header, "recording_location");
		//}
		if($custom_fields == "Y")	{
		    for($i = 0 ; $i < count($array_list); $i++){
				$list_id = $array_list[$i];
				$query_CF_list = mysqli_query($link, "SELECT * FROM custom_$list_id;");
				if($query_CF_list){
					while ($field_list=mysqli_fetch_field($query_CF_list)){
						$exec_query_CF_list = $field_list->name;
						if($exec_query_CF_list != "lead_id"){
							$active_list_fields[] = $exec_query_CF_list;
							array_push($csv_header, $exec_query_CF_list);
						}
					}
					$active_list_fields[] = "|";
				}
			}
		}

		if($rec_location == "Y"){
			array_push($csv_header, "recording_location");
		}
		
		//OUTPUT DATA ROW//
		while($row = mysqli_fetch_row($result)) {
			$lead_id = $row[34];
			$uniqueid = $row[35];
			if($per_call_notes == "Y"){
				$query_callnotes = mysqli_query($link, "SELECT call_notes from vicidial_call_notes where lead_id='$lead_id' LIMIT 1;");
				$notes_ct = mysqli_num_rows($query_callnotes);
				if ($notes_ct > 0){
					$fetch_callnotes = mysqli_fetch_array($query_callnotes);
					$notes_data =	$fetch_callnotes["call_notes"];
					$notes_data = rawurldecode($notes_data);
				}else{
					$notes_data = "";
				}
				array_push($row,$notes_data);
			}

			if($rec_location == "Y"){
				// if(($RUNcampaign > 0 && $RUNgroup < 1) || ($RUNcampaign > 0 && $RUNgroup > 0)){
				// 	$id_SQL = " AND vicidial_id = '$uniqueid'";
				// }else{
				// 	$id_SQL = " AND vicidial_id = '$uniqueid'";
				// }
				
				$query_recordings = mysqli_query($link, "SELECT location from recording_log where lead_id='$lead_id' AND vicidial_id = '$uniqueid' LIMIT 1;");
				$rec_ct = mysqli_num_rows($query_recordings);
				if ($rec_ct > 0){
					$fetch_recording = mysqli_fetch_array($query_recordings);
					$rec_data =	$fetch_recording["location"];
					//$rec_data = rawurldecode($rec_data);
				}else{
					$rec_data = "";
				}
				array_push($row,$rec_data);

				// $rec_location_fields = ", re.location as recording_location";
				// $rec_location_from = ", recording_log re";
				// $rec_location_where = "and re.lead_id=vl.lead_id and vl.uniqueid = re.vicidial_id";
			}
			//if($rec_location == "Y"){
			//	$query_rec_location = mysqli_query($link, "SELECT recording_id as recording_location from recording_log AND (date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate') where lead_id='$lead_id' LIMIT 1;");
			//	$rec_location_ct = mysqli_num_rows($query_rec_location);
			//	if ($rec_location_ct > 0){
			//		$fetch_rec_location = mysqli_fetch_array($query_rec_location);
			//		$rec_location_data =	$fetch_rec_location["recording_location"];
			//	}else{
			//		$rec_location_data = "";
			//	}
			//	array_push($row,$rec_location_data);
			//}

                        if(!empty($row[28])) {
                                $row[28] = preg_replace('/[ ,]+/', '-', trim($row[28]));
                        }
                        if(!empty($row[15])) {
                                $row[15] = preg_replace('/[ ,]+/', '-', trim($row[15]));
                        }
                        if(!empty($row[16])) {
                                $row[16] = preg_replace('/[ ,]+/', '-', trim($row[16]));
                        }
                        if(!empty($row[17])) {
                                $row[17] = preg_replace('/[ ,]+/', '-', trim($row[17]));
                        }


			if($custom_fields == "Y")	{
			    for($i = 0 ; $i < count($array_list); $i++){
				    $list_id = "custom_".$array_list[$i];
					
					$active_list_fields = implode(",",$active_list_fields);
					$active_list_fields = trim($active_list_fields, "|,");
					$active_list_fields = explode("|,", $active_list_fields);
					$fields = implode(",",$active_list_fields);
					$fields_array = explode(",", $fields);
					//$queries[] = "SELECT $fields FROM $list_id WHERE lead_id ='$lead_id' LIMIT 1;";
					$query_CF = mysqli_query($link, "SELECT $fields FROM $list_id WHERE lead_id ='$lead_id' LIMIT 1;");
					if($query_CF){
						$fetch_CF = mysqli_fetch_array($query_CF);
						//while($fetch_CF = mysqli_fetch_array($query_CF)){
						for($x=0;$x < count($fields_array);$x++){
							$fetch_row[] =  str_replace(",", " | ", $fetch_CF[$fields_array[$x]]);
						}
					}else{
					//	for($x=0;$x < count($fields_array);$x++){
					//		$fetch_row[] =  '';
					//	}
					}
					
					
					for($a=0;$a < count($fetch_row);$a++){
						array_push($row, $fetch_row[$a]);
					}
					
					unset($fetch_row);
					unset($fetch_CF);
					/*
					$x = 0;
					while($x < count($active_list_fields)){
						$field = $active_list_fields[$x];
						$field = rtrim($field, ",");
						
						$query_CF = mysqli_query($link, "SELECT $field FROM $list_id WHERE lead_id ='$lead_id';");
						$list_data = mysqli_fetch_assoc($query_CF);
						
						$getColumn = mysqli_query($link, "DESC $list_id;");
						$y = 0;
						while($getcolumn_row = mysqli_fetch_array($getColumn)){
							if($field == $getcolumn_row[$y]){
								array_push($row, $list_data);
							}else{
								array_push($row, " ");
							}
							$y++;
						}
						
						$x++;
					}
					*/
					/*for($x=0;$x < count($active_list_fields);$x++){
						$list_field = $active_list_fields[$x];
						$query_CF = mysqli_query($link, "SELECT $list_field FROM $list_id WHERE lead_id ='$lead_id';");
						$list_data = mysqli_fetch_array($query_CF);
						$list_row_data = $list_data[$list_field];
						array_push($row, $list_row_data);
					}*/
					//array(",", $active_list_fields);
					
			//		//$list_fields = implode(",",$active_list_fields);
			//	    //$query_CF = mysqli_query($link, "SELECT * FROM $list_id WHERE lead_id ='$lead_id';");
			//	    //while($list_data = mysqli_fetch_array($query_CF)){
			//	    //
			//	    //	   array_push($row, $list_data);
			//	    //}
			    }
			}
			$csv_row[] = $row;
		}
		
		$campFilter = (strlen($campaigns) > 0) ? "Campaign(s): $campaigns" : "";
		$inbFilter  = (strlen($inbounds) > 0) ? "Inbound Groups(s): $inbounds" : "";
		$listFilter = (strlen($lists) > 0) ? "List(s): $lists" : "";
		$log_id = log_action($linkgo, 'DOWNLOAD', $log_user, $ip_address, "Exported Call Reports starting from $fromDate to $toDate using the following filters, $campFilter $inbFilter $listFilter", $log_group);
		
		$return = array("query" => $query, "header" => $csv_header, "rows" => $csv_row, "return_this" => $query);
		
		return $return;
	}
	
	function go_get_reports($pageTitle, $fromDate, $toDate, $campaignID, $request, $userID, $userGroup, $link, $dispo_stats, $linkgo){
		
		if (!empty($campaignID) || $pageTitle == 'call_export_report'){
		  	//$return['groupId'] = $goReportsClass->go_getUsergroup($userID);
            //$return['groupId'] = go_getUsergroup($userID,$link);
			$date1=new DateTime($fromDate);
			$date2=new DateTime($toDate);
			$interval = date_diff($date1,$date2);
			$date_diff = $interval->format('%d');
            $date_array = implode("','",go_get_dates($fromDate, $toDate));
//			 $mysqli_query($link, cache_on();
			$file_download = 1;
            
            //Initialise Values
			if ($pageTitle!='inbound_report') {
//				$campaignID = '';
				$query = mysqli_query($link, "select campaign_name from vicidial_campaigns where campaign_id='$campaignID'") or die(mysqli_error($link));
				$num_query = mysqli_num_rows($query);
				if($num_query > 0){
					$err_msg = error_handle("41004", "campaignID. Doesn't exist");
					$apiresults = array("code" => "41006", "result" => $err_msg); 
				}
			} else {
				$query = mysqli_query($link, "select group_name as campaign_name from vicidial_inbound_groups where uniqueid_status_prefix='".$userGroup."'") or die(mysqli_error($link));
			}
            
				$resultu = mysqli_fetch_array($query);
				$return['campaign_name'] = $resultu['campaign_name'];
				$ul = "and campaign_id='$campaignID'";
				
				if (!isset($request) || $request=='') {
					$return['request'] = 'daily';
				} else {
					$return['request'] = $request;
				}
				
				$query = mysqli_query($link, "SELECT status FROM vicidial_statuses WHERE sale='Y'") or die(mysqli_error($link));
				$sstatusRX = "";
				$sstatuses = array();
				
				$a = 0;
				while($Qstatus = mysqli_fetch_array($query)){
					$goTempStatVal = $Qstatus['status'];
					$sstatuses[$a] = $Qstatus['status'];
					$sstatusRX	.= "{$goTempStatVal}|";
					$a++;
				}
				
				if(!empty($sstatuses))
				$sstatuses = implode("','",$sstatuses);
				
				$query2 = mysqli_query($link, "SELECT status FROM vicidial_campaign_statuses WHERE sale='Y' AND campaign_id='$campaignID'") or die(mysqli_error($link));
				
				$cstatusRX = "";
				$cstatuses = array();
				
				$b = 0;
				while($Qstatus = mysqli_fetch_array($query2)){
					$goTempStatVal = $Qstatus['status'];
					$cstatuses[$b] = $Qstatus['status'];
					$cstatusRX	.= "{$goTempStatVal}|";
					$b++;
				}
				
				if(!empty($cstatuses))
				$cstatuses = implode("','",$cstatuses);
				
				
				if (strlen($sstatuses) > 0 && strlen($cstatuses) > 0)
				{
				   $statuses = "{$sstatuses}','{$cstatuses}";
				   $statusRX = "{$sstatusRX}{$cstatusRX}";
				} else {
				   $statuses = (strlen($sstatuses) > 0 && strlen($cstatuses) < 1) ? $sstatuses : $cstatuses;
				   $statusRX = (strlen($sstatusRX) > 0 && strlen($cstatusRX) < 1) ? $sstatusRX : $cstatusRX;
				}
				$statusRX = trim($statusRX, "|");
				//End initialize
            
            //Start Report
			
				// Agent Statistics
				if ($pageTitle=='stats'){
					
					if ($return['request']=='daily') {
						$stringv = go_getall_closer_campaigns($campaignID, $link);
						$closerCampaigns = " and campaign_id IN ('$stringv') ";
						$vcloserCampaigns = " and vclog.campaign_id IN ('$stringv') ";
						$call_time = go_get_calltimes($campaignID, $link);
						
						if($userGroup !== "ADMIN")
						$ul = "AND user_group = '$userGroup'";
						else
						$ul = "";
						
						if (strlen($stringv) > 0 && $stringv != '') {
							$MunionSQL = "UNION select date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 00, 1, 0)) as 'Hour0',sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23' from vicidial_closer_log where length_in_sec>'0' $ul and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns group by cdate";
							$TunionSQL = "UNION ALL select phone_number from vicidial_closer_log vcl where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns $ul";
							$DunionSQL = "UNION select status,count(*) as ccount from vicidial_closer_log where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns $ul group by status";
						}
						
						// Total Calls Made
						//$query = mysqli_query($link, "select * from vicidial_log where campaign_id='$campaignID' and length_in_sec>'0' and call_date between '$fromDate 00:00:00' and '$toDate 23:59:59'");
						$query_total_calls_made = "select cdate, sum(Hour0) as 'Hour0', sum(Hour1) as 'Hour1', sum(Hour2) as 'Hour2', sum(Hour3) as 'Hour3', sum(Hour4) as 'Hour4', sum(Hour5) as 'Hour5', sum(Hour6) as 'Hour6', sum(Hour7) as 'Hour7', sum(Hour8) as 'Hour8', sum(Hour9) as 'Hour9', sum(Hour10) as 'Hour10', sum(Hour11) as 'Hour11', sum(Hour12) as 'Hour12', sum(Hour13) as 'Hour13', sum(Hour14) as 'Hour14', sum(Hour15) as 'Hour15', sum(Hour16) as 'Hour16', sum(Hour17) as 'Hour17', sum(Hour18) as 'Hour18', sum(Hour19) as 'Hour19', sum(Hour20) as 'Hour20', sum(Hour21) as 'Hour21', sum(Hour22) as 'Hour22', sum(Hour23) as 'Hour23' from (select date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 00, 1, 0)) as 'Hour0',sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23' from vicidial_log where length_in_sec>'0' $ul and campaign_id = '$campaignID' and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' group by cdate $MunionSQL) t group by cdate;";
						$query = mysqli_query($link, $query_total_calls_made);
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
							$cdate[] = $row['cdate'];
							$hour0[] = $row['Hour0'];
							$hour1[] = $row['Hour1'];
							$hour2[] = $row['Hour2'];
							$hour3[] = $row['Hour3'];
							$hour4[] = $row['Hour4'];
							$hour5[] = $row['Hour5'];
							$hour6[] = $row['Hour6'];
							$hour7[] = $row['Hour7'];
							$hour8[] = $row['Hour8'];
							$hour9[] = $row['Hour9'];
							$hour10[] = $row['Hour10'];
							$hour11[] = $row['Hour11'];
							$hour12[] = $row['Hour12'];
							$hour13[] = $row['Hour13'];
							$hour14[] = $row['Hour14'];
							$hour15[] = $row['Hour15'];
							$hour16[] = $row['Hour16'];
							$hour17[] = $row['Hour17'];
							$hour18[] = $row['Hour18'];
							$hour19[] = $row['Hour19'];
							$hour20[] = $row['Hour20'];
							$hour21[] = $row['Hour21'];
							$hour22[] = $row['Hour22'];
							$hour23[] = $row['Hour23'];
						}
						$data_calls = array("cdate" => $cdate, "hour0" => $hour0, "hour1" => $hour1, "hour2" => $hour2, "hour3" => $hour3, "hour4" => $hour4, "hour5" => $hour5, "hour6" => $hour6, "hour7" => $hour7, "hour8" => $hour8, "hour9" => $hour9, "hour10" => $hour10, "hour11" => $hour11, "hour12" => $hour12, "hour13" => $hour13, "hour14" => $hour14, "hour15" => $hour15, "hour16" => $hour16, "hour17" => $hour17, "hour18" => $hour18, "hour19" => $hour19, "hour20" => $hour20, "hour21" => $hour21, "hour22" => $hour22, "hour23" => $hour23);
						
						$query = mysqli_query($link, "select phone_number from vicidial_log vl where length_in_sec>'0' and campaign_id = '$campaignID' and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $ul $TunionSQL");
						$total_calls = mysqli_num_rows($query);
						
						// Total Number of Leads
						$query = mysqli_query($link, "select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id");
						$total_leads = mysqli_num_rows($query);
						
						// Total Number of New Leads
						$query = mysqli_query($link, "select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id and vl.status='NEW'");
						$total_new = mysqli_num_rows($query);
						
						// Total Agents Logged In
						$query = mysqli_query($link, "select date_format(event_time, '%Y-%m-%d') as cdate,user as cuser from vicidial_agent_log where campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' group by cuser");
						$total_agents = mysqli_num_rows($query);
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
							$cdate[] = $row['cdate'];
							$cuser[] = $row['cuser'];
						}
						$data_agents = array("cdate" => $cdate, "cuser" => $cuser);
						
						// Disposition of Calls
						$query = mysqli_query($link, "select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul and campaign_id = '$campaignID' group by status $DunionSQL) t group by status;");
						$total_status = mysqli_num_rows($query);
						
						$query = mysqli_query($link, "select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $ul and campaign_id = '$campaignID' group by status $DunionSQL) t group by status;");
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
							$status[] = $row['status'];
							$ccount[] = $row['ccount'];
							
							#getting status name
							$var_status = $row['status'];
							
								# in default statuses
								$query_default_statusname = mysqli_query($link, "SELECT status_name FROM vicidial_statuses WHERE status = '$var_status' LIMIT 1;");
								if($query_default_statusname){
									$fetch_statusname = mysqli_fetch_array($query_default_statusname);
								}
								
								if(!isset($fetch_statusname) || $fetch_statusname == NULL){
								# in custom statuses
								$query_custom_statusname = mysqli_query($link, "SELECT status_name FROM vicidial_campaign_statuses WHERE status = '$var_status' LIMIT 1;");
									$fetch_statusname = mysqli_fetch_array($query_custom_statusname);
								}
							
							$status_name[] = $fetch_statusname['status_name'];
							//$status_name[] = $query_statusname;
						}
						$data_status = array("status" => $status, "status_name" => $status_name, "ccount" => $ccount, "query" => $query_total_calls_made);
					}
					
					if ($return['request']=='weekly') {
						$stringv = go_getall_closer_campaigns($campaignID, $link);
						$closerCampaigns = " and campaign_id IN ('$stringv') ";
						$vcloserCampaigns = " and vclog.campaign_id IN ('$stringv') ";
	
						if (strlen($stringv) > 0 && $stringv != '') {
							$MunionSQL = "UNION select week(DATE_FORMAT( call_date, '%Y-%m-%d' )) as weekno, sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 0, 1, 0))  as 'Day0', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0))  as 'Day1', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0))  as 'Day2', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0))  as 'Day3', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0))  as 'Day4', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0))  as 'Day5', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0))  as 'Day6' from vicidial_closer_log where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns group by weekno";
							$TunionSQL = "UNION ALL select phone_number from vicidial_closer_log vcl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns";
							$DunionSQL = "UNION select status,count(*) as ccount from vicidial_closer_log vcl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns group by status";
						}
						
						// Total Calls Made
						//$query = mysqli_query($link, "select * from vicidial_log where campaign_id='$campaignID' and length_in_sec>'0' and call_date between '$fromDate 00:00:00' and '$toDate 23:59:59'");
						$query = mysqli_query($link, "select weekno, sum(Day0) as 'Day0', sum(Day1) as 'Day1', sum(Day2) as 'Day2', sum(Day3) as 'Day3', sum(Day4) as 'Day4', sum(Day5) as 'Day5', sum(Day6) as 'Day6' from (select week(DATE_FORMAT( call_date, '%Y-%m-%d' )) as weekno, sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 0, 1, 0))  as 'Day0', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0))  as 'Day1', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0))  as 'Day2', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0))  as 'Day3', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0))  as 'Day4', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0))  as 'Day5', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0))  as 'Day6' from vicidial_log where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' )) between week('$fromDate') and week('$toDate') $ul group by weekno $MunionSQL) t group by weekno;");
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
							$weekno[] = "Week ".$row['weekno'];
							$day0[] = $row['Day0'];
							$day1[] = $row['Day1'];
							$day2[] = $row['Day2'];
							$day3[] = $row['Day3'];
							$day4[] = $row['Day4'];
							$day5[] = $row['Day5'];
							$day6[] = $row['Day6'];
						}
						$data_calls = array("weekno" => $weekno, "Day0" => $day0, "Day1" => $day1, "Day2" => $day2, "Day3" => $day3, "Day4" => $day4, "Day5" => $day5, "Day6" => $day6);
						
						$query = mysqli_query($link, "select phone_number from vicidial_log vl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' )) between week('$fromDate') and week('$toDate') $ul $TunionSQL");
						$total_calls = mysqli_num_rows($query);
						
						// Total Number of Leads
						$query = mysqli_query($link, "select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id");
						$total_leads = mysqli_num_rows($query);
						
						// Total Number of New Leads
						$query = mysqli_query($link, "select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id and vl.list_id='NEW'");
						$total_new = mysqli_num_rows($query);
						
						// Total Agents Logged In
						$query = mysqli_query($link, "select date_format(event_time, '%Y-%m-%d') as cdate,user as cuser from vicidial_agent_log where campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' group by cuser");
						$total_agents = mysqli_num_rows($query);
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
							$cdate[] = $row['cdate'];
							$cuser[] = $row['cuser'];
						}
						$data_agents = array("cdate" => $cdate, "cuser" => $cuser);
						
						// Disposition of Calls
						$query = mysqli_query($link, "select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' )) between week('$fromDate') and week('$toDate') $ul group by status $DunionSQL) t group by status;");
						$total_status = mysqli_num_rows($query);
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
							$status[] = $row['status'];
							$ccount[] = $row['ccount'];
							
							#getting status name
							$var_status = $row['status'];
							
								# in default statuses
								$query_default_statusname = mysqli_query($link, "SELECT status_name FROM vicidial_statuses WHERE status = '$var_status' LIMIT 1;");
								if($query_default_statusname){
									$fetch_statusname = mysqli_fetch_array($query_default_statusname);
								}
								
								if(!isset($fetch_statusname) || $fetch_statusname == NULL){
								# in custom statuses
								$query_custom_statusname = mysqli_query($link, "SELECT status_name FROM vicidial_campaign_statuses WHERE status = '$var_status' LIMIT 1;");
									$fetch_statusname = mysqli_fetch_array($query_custom_statusname);
								}
							
							$status_name[] = $fetch_statusname['status_name'];
							//$status_name[] = $query_statusname;
						}
						$data_status = array("status" => $status, "status_name" => $status_name, "ccount" => $ccount);
					}
					
					if ($return['request']=='monthly') {
						$stringv = go_getall_closer_campaigns($campaignID, $link);
						$closerCampaigns = " and campaign_id IN ('$stringv') ";
						$vcloserCampaigns = " and vclog.campaign_id IN ('$stringv') ";
	
						if (strlen($stringv) > 0 && $stringv != '')
						{
							$MunionSQL = "UNION select MONTHNAME(DATE_FORMAT( call_date, '%Y-%m-%d' )) as monthname, sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0)) as 'Month1', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0)) as 'Month2', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0)) as 'Month3', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0)) as 'Month4', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0)) as 'Month5', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0)) as 'Month6', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 7, 1, 0)) as 'Month7', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 8, 1, 0)) as 'Month8', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 9, 1, 0)) as 'Month9', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 10, 1, 0)) as 'Month10', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 11, 1, 0)) as 'Month11', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 12, 1, 0)) as 'Month12' from vicidial_closer_log where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns group by monthname";
							
							$TunionSQL = "UNION ALL select phone_number from vicidial_closer_log vcl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns";
							$DunionSQL = "UNION select status,count(*) as ccount from vicidial_closer_log vcl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns group by status";
						}
	
						// Total Calls Made
						$query = mysqli_query($link, "select monthname, sum(Month1) as 'Month1', sum(Month2) as 'Month2', sum(Month3) as 'Month3', sum(Month4) as 'Month4', sum(Month5) as 'Month5', sum(Month6) as 'Month6', sum(Month7) as 'Month7', sum(Month8) as 'Month8', sum(Month9) as 'Month9', sum(Month10) as 'Month10', sum(Month11) as 'Month11', sum(Month12) as 'Month12' from (select MONTHNAME(DATE_FORMAT( call_date, '%Y-%m-%d' )) as monthname, sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0)) as 'Month1', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0)) as 'Month2', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0)) as 'Month3', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0)) as 'Month4', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0)) as 'Month5', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0)) as 'Month6', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 7, 1, 0)) as 'Month7', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 8, 1, 0)) as 'Month8', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 9, 1, 0)) as 'Month9', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 10, 1, 0)) as 'Month10', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 11, 1, 0)) as 'Month11', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 12, 1, 0)) as 'Month12' from vicidial_log where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul group by monthname $MunionSQL) t group by monthname;");
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
							$monthname[] = $row['monthname'];
							$month0[] = $row['Month1'];
							$month1[] = $row['Month2'];
							$month2[] = $row['Month3'];
							$month3[] = $row['Month4'];
							$month4[] = $row['Month5'];
							$month5[] = $row['Month6'];
							$month6[] = $row['Month7'];
							$month7[] = $row['Month8'];
							$month8[] = $row['Month9'];
							$month9[] = $row['Month10'];
							$month10[] = $row['Month11'];
							$month11[] = $row['Month12'];
						}
						$data_calls = array("monthname" => $monthname, "Month1" => $month0, "Month2" => $month1, "Month3" => $month2, "Month4" => $month3, "Month5" => $month4, "Month6" => $month5, "Month7" => $month6, "Month8" => $month7, "Month9" => $month8, "Month10" => $month9, "Month11" => $month10, "Month12" => $month11);
						
						$query = mysqli_query($link, "select phone_number from vicidial_log vl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul $TunionSQL");
						$total_calls = mysqli_num_rows($query);
						
						// Total Number of Leads
						$query = mysqli_query($link, "select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id");
						$total_leads = mysqli_num_rows($query);
						
						// Total Number of New Leads
						$query = mysqli_query($link, "select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id and vl.list_id='NEW'");
						$total_new = mysqli_fetch_array($query, MYSQLI_ASSOC);
						
						// Total Agents Logged In
						$query = mysqli_query($link, "select date_format(event_time, '%Y-%m-%d') as cdate,user as cuser from vicidial_agent_log where campaign_id='$campaignID' and MONTH(event_time) between MONTH('$fromDate') and MONTH('$toDate') group by cuser");
						$total_agents = mysqli_num_rows($query);
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
							$cdate[] = $row['cdate'];
							$cuser[] = $row['cuser'];
						}
						$data_agents = array("cdate" => $cdate, "cuser" => $cuser);
						
						// Disposition of Calls
						$query = mysqli_query($link, "select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul group by status $DunionSQL) t group by status;");
						$total_status = mysqli_num_rows($query);
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
							$status[] = $row['status'];
							$ccount[] = $row['ccount'];
							
							#getting status name
							$var_status = $row['status'];
							
								# in default statuses
								$query_default_statusname = mysqli_query($link, "SELECT status_name FROM vicidial_statuses WHERE status = '$var_status' LIMIT 1;");
								if($query_default_statusname){
									$fetch_statusname = mysqli_fetch_array($query_default_statusname);
								}
								
								if(!isset($fetch_statusname) || $fetch_statusname == NULL){
								# in custom statuses
								$query_custom_statusname = mysqli_query($link, "SELECT status_name FROM vicidial_campaign_statuses WHERE status = '$var_status' LIMIT 1;");
									$fetch_statusname = mysqli_fetch_array($query_custom_statusname);
								}
							
							$status_name[] = $fetch_statusname['status_name'];
							//$status_name[] = $query_statusname;
						}
						$data_status = array("status" => $status, "status_name" => $status_name, "ccount" => $ccount);
					}
					
					$apiresults = array("call_time" => $call_time, "data_calls" => $data_calls, "data_status" => $data_status, "data_agents" => $data_agents, "total_calls" => $total_calls, "total_leads" => $total_leads, "total_new" => $total_new, "total_status" => $total_status);
					//print_r($query_total_calls_made);
					return $apiresults;
				}
				
				// Agent Time Detail
				if ($pageTitle=="agent_detail") {

					if($userGroup !== "ADMIN")
						$ul = "AND user_group = '$userGroup'";
					else
						$ul = "";

					// BEGIN gather user IDs and names for matching up later
					$query = mysqli_query($link, "SELECT full_name,user FROM vicidial_users ORDER BY user LIMIT 100000");
					$user_ct = mysqli_num_rows($query);
					
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
						$ULname[] = $row['full_name'];
						$ULuser[] = $row['user'];
					}
					
					// END gather user IDs and names for matching up later
				
					// BEGIN gather timeclock records per agent
					$query = mysqli_query($link, "SELECT user,SUM(login_sec) AS login_sec FROM vicidial_timeclock_log WHERE event IN('LOGIN','START') AND date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' GROUP BY user LIMIT 10000000");
					$timeclock_ct = mysqli_num_rows($query);
					
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
						$TCuser[] = $row['user'];
						$TCtime[] = $row['login_sec'];
					}
					
					// END gather timeclock records per agent
					
					// BEGIN gather pause code information by user IDs
					$sub_statuses='-';
					$sub_statusesTXT='';
					$sub_statusesHEAD='';
					$sub_statusesHTML='';
					$sub_statusesFILE='';
					$sub_statusesTOP= array();
					$sub_statusesARY=$MT;
					
					$PCusers='-';
					$PCusersARY=$MT;
					$PCuser_namesARY=$MT;
					
					
					//$query = mysqli_query($link, "SELECT user,SUM(pause_sec) AS pause_sec,sub_status FROM vicidial_agent_log WHERE date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND pause_sec > 0 AND pause_sec < 65000 $ul and campaign_id='$campaignID' GROUP BY user,sub_status ORDER BY user,sub_status DESC LIMIT 10000000");
					$query = mysqli_query($link, "SELECT user,SUM(pause_sec) AS pause_sec,sub_status FROM vicidial_agent_log WHERE date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND pause_sec > 0 $ul and campaign_id='$campaignID' GROUP BY user,sub_status ORDER BY user,sub_status DESC LIMIT 10000000");
					$pause_sec_ct = mysqli_num_rows($query);
			
					$i=0;$a=1;
					$sub_status_count=0;
					$user_count=0;
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
						$PCuser[$i] = $row['user'];
						$PCpause_sec[$i] = $row['pause_sec'];
						$sub_status[$i] = $row['sub_status'];
						
						if (!preg_match("/-$sub_status[$i]-/", $sub_statuses)){
							$sub_statusesFILE .= ",$sub_status[$i]";
							$sub_statuses .= "$sub_status[$i]-";
							$sub_statusesARY[$sub_status_count] = $sub_status[$i];
							$sub_statusesTOP[$i] = "$sub_status[$i]";
							$sub_status_count++;
						}
						if (!preg_match("/-$PCuser[$i]-/", $PCusers)){
							$PCusersARY[$user_count] = $PCuser[$i];
							$user_count++;
						}
						
						$i++;
					}
					
					
					/*foreach ($query->result() as $i => $row)
						{
						$PCuser[$i] =		$row->user;
						$PCpause_sec[$i] =	$row->pause_sec;
						$sub_status[$i] =	$row->sub_status;
				
						if (!eregi("-$sub_status[$i]-", $sub_statuses))
							{
							$sub_statusesFILE .= ",$sub_status[$i]";
							$sub_statuses .= "$sub_status[$i]-";
							$sub_statusesARY[$sub_status_count] = $sub_status[$i];
							$sub_statusesTOP .= "<td><div align=\"center\" class=\"style4\" nowrap><strong> &nbsp;$sub_status[$i]&nbsp; </strong></div></td>";
							$sub_status_count++;
							}
						if (!eregi("-$PCuser[$i]-", $PCusers))
							{
							$PCusersARY[$user_count] = $PCuser[$i];
							$user_count++;
							}
				
						$i++;
						} */
					// END gather pause code information by user IDs
					
					//# BEGIN Gather all agent time records and parse through them in PHP to save on DB load
					$query = mysqli_query($link, "SELECT user,wait_sec,talk_sec,dispo_sec,pause_sec,lead_id,status,dead_sec FROM vicidial_agent_log WHERE date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' $ul and campaign_id='$campaignID' LIMIT 10000000");
					$agent_time_ct = mysqli_num_rows($query);
					$j=0;
					$k=0;
					$uc=0;
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
						$user =			$row['user'];
						$wait =			$row['wait_sec'];
						$talk =			$row['talk_sec'];
						$dispo =		$row['dispo_sec'];
						$pause =		$row['pause_sec'];
						$lead =			$row['lead_id'];
						$status =		$row['status'];
						$dead =			$row['dead_sec'];
						
						// if ($wait > 65000) {$wait=0;}
						// if ($talk > 65000) {$talk=0;}
						// if ($dispo > 65000) {$dispo=0;}
						// if ($pause > 65000) {$pause=0;}
						// if ($dead > 65000) {$dead=0;}
						
						$customer =		($talk - $dead);
						if ($customer < 1){$customer=0;}
						
						$TOTwait =	($TOTwait + $wait);
						$TOTtalk =	($TOTtalk + $talk);
						$TOTdispo =	($TOTdispo + $dispo);
						$TOTpause =	($TOTpause + $pause);
						$TOTdead =	($TOTdead + $dead);
						$TOTcustomer =	($TOTcustomer + $customer);
						$TOTALtime = ($TOTALtime + $pause + $dispo + $talk + $wait);
						
						if ( ($lead > 0) and ((!preg_match("/NULL/",$status)) and (strlen($status) > 0)) ) {$TOTcalls++;}
						
						$user_found=0;
						if ($uc < 1){
							$Suser[$uc] = $user;
							$uc++;
						}
						
						$m=0;
						while ( ($m < $uc) and ($m < 50000) ){
							if ($user == $Suser[$m]){
								$user_found++;
								$Swait[$m] =	($Swait[$m] + $wait);
								$Stalk[$m] =	($Stalk[$m] + $talk);
								$Sdispo[$m] =	($Sdispo[$m] + $dispo);
								$Spause[$m] =	($Spause[$m] + $pause);
								$Sdead[$m] =	($Sdead[$m] + $dead);
								$Scustomer[$m] =	($Scustomer[$m] + $customer);
								if ( ($lead > 0) and ((!preg_match("/NULL/",$status)) and (strlen($status) > 0)) ) {$Scalls[$m]++;}
								}
							$m++;
						}
						if ($user_found < 1){
							$Scalls[$uc] =	0;
							$Suser[$uc] =	$user;
							$Swait[$uc] =	$wait;
							$Stalk[$uc] =	$talk;
							$Sdispo[$uc] =	$dispo;
							$Spause[$uc] =	$pause;
							$Sdead[$uc] =	$dead;
							$Scustomer[$uc] =	$customer;
							if ($lead > 0) {$Scalls[$uc]++;}
							$uc++;
						}
					}
					/*
					foreach ($query->result() as $i => $row)
						{
						$user =			$row->user;
						$wait =			$row->wait_sec;
						$talk =			$row->talk_sec;
						$dispo =		$row->dispo_sec;
						$pause =		$row->pause_sec;
						$lead =			$row->lead_id;
						$status =		$row->status;
						$dead =			$row->dead_sec;
						if ($wait > 65000) {$wait=0;}
						if ($talk > 65000) {$talk=0;}
						if ($dispo > 65000) {$dispo=0;}
						if ($pause > 65000) {$pause=0;}
						if ($dead > 65000) {$dead=0;}
						$customer =		($talk - $dead);
						if ($customer < 1)
							{$customer=0;}
						$TOTwait =	($TOTwait + $wait);
						$TOTtalk =	($TOTtalk + $talk);
						$TOTdispo =	($TOTdispo + $dispo);
						$TOTpause =	($TOTpause + $pause);
						$TOTdead =	($TOTdead + $dead);
						$TOTcustomer =	($TOTcustomer + $customer);
						$TOTALtime = ($TOTALtime + $pause + $dispo + $talk + $wait);
						if ( ($lead > 0) and ((!eregi("NULL",$status)) and (strlen($status) > 0)) ) {$TOTcalls++;}
						
						$user_found=0;
						if ($uc < 1) 
							{
							$Suser[$uc] = $user;
							$uc++;
							}
						$m=0;
						while ( ($m < $uc) and ($m < 50000) )
							{
							if ($user == "$Suser[$m]")
								{
								$user_found++;
				
								$Swait[$m] =	($Swait[$m] + $wait);
								$Stalk[$m] =	($Stalk[$m] + $talk);
								$Sdispo[$m] =	($Sdispo[$m] + $dispo);
								$Spause[$m] =	($Spause[$m] + $pause);
								$Sdead[$m] =	($Sdead[$m] + $dead);
								$Scustomer[$m] =	($Scustomer[$m] + $customer);
								if ( ($lead > 0) and ((!eregi("NULL",$status)) and (strlen($status) > 0)) ) {$Scalls[$m]++;}
								}
							$m++;
							}
						if ($user_found < 1)
							{
							$Scalls[$uc] =	0;
							$Suser[$uc] =	$user;
							$Swait[$uc] =	$wait;
							$Stalk[$uc] =	$talk;
							$Sdispo[$uc] =	$dispo;
							$Spause[$uc] =	$pause;
							$Sdead[$uc] =	$dead;
							$Scustomer[$uc] =	$customer;
							if ($lead > 0) {$Scalls[$uc]++;}
							$uc++;
							}
		
						} */
						
					//if ($DB) {echo "{$this->lang->line("go_done_gathering")} $i {$this->lang->line("go_records_analyzing")}<BR>\n";}
					//# END Gather all agent time records and parse through them in PHP to save on DB load
				
					//////////////////////////////////////
					//# END gathering information from the database section
					//////////////////////////////////////
				
					//# BEGIN print the output to screen or put into file output variable
					/*
					if ($file_download > 0)
						{
						$file_output  = "CAMPAIGN,$campaignID - ".$resultu['campaign_name']."\n";
						$file_output .= "DATE RANGE,$fromDate TO $toDate\n\n";
						$file_output .= "USER,ID,CALLS,TIME CLOCK,AGENT TIME,WAIT,TALK,DISPO,PAUSE,WRAPUP,CUSTOMER,$sub_statusesFILE\n";
						}
					*/
					//# END print the output to screen or put into file output variable
				
					//////////////////////////////////////
					//# BEGIN formatting data for output section
					//////////////////////////////////////
				
					//# BEGIN loop through each user formatting data for output
					$AUTOLOGOUTflag=0;
					$m=0;
					$rowId=1;
					while ( ($m < $uc) and ($m < 50000) ){
						$SstatusesHTML='';
						$SstatusesFILE='';
						$Stime[$m] = ($Swait[$m] + $Stalk[$m] + $Sdispo[$m] + $Spause[$m]);
						$RAWuser = $Suser[$m];
						$RAWcalls = $Scalls[$m];
						$RAWtimeSEC = $Stime[$m];
				
						$Swait[$m] = 	gmdate('H:i:s', $Swait[$m]); 
						$Stalk[$m] = 	gmdate('H:i:s', $Stalk[$m]); 
						$Sdispo[$m] = 	gmdate('H:i:s', $Sdispo[$m]); 
						$Spause[$m] = 	gmdate('H:i:s', $Spause[$m]); 
						$Sdead[$m] = 	gmdate('H:i:s', $Sdead[$m]); 
						$Scustomer[$m] = 	gmdate('H:i:s', $Scustomer[$m]); 
						$Stime[$m] = 	gmdate('H:i:s', $Stime[$m]); 
				
						$RAWtime = $Stime[$m];
						$RAWwait = $Swait[$m];
						$RAWtalk = $Stalk[$m];
						$RAWdispo = $Sdispo[$m];
						$RAWpause = $Spause[$m];
						$RAWdead = $Sdead[$m];
						$RAWcustomer = $Scustomer[$m];
				
						$n=0;
						$user_name_found=0;
						while ($n < $user_ct)
							{
							//if (strtolower($Suser[$m]) == strtolower("$ULuser[$n]"))
							if ($Suser[$m] == $ULuser[$n])
								{
								$user_name_found++;
								$RAWname = $ULname[$n];
								$Sname[$m] = $ULname[$n];
								}
							$n++;
							}
						if ($user_name_found < 1)
							{
							$RAWname =		"NOT IN SYSTEM";
							$Sname[$m] =	$RAWname;
							}
				
						$n=0;
						$punches_found=0;
						while ($n < $punches_to_print)
							{
							if ($Suser[$m] == $TCuser[$n])
								{
								$punches_found++;
								$RAWtimeTCsec =		$TCtime[$n];
								$TOTtimeTC =		($TOTtimeTC + $TCtime[$n]);
								$StimeTC[$m]=		gmdate('H:i:s', $TCtime[$n]); 
								$RAWtimeTC =		$StimeTC[$m];
								$StimeTC[$m] =		sprintf("%10s", $StimeTC[$m]);
								}
							$n++;
							}
						if ($punches_found < 1)
							{
							$RAWtimeTCsec =		"0";
							$StimeTC[$m]=		"0:00"; 
							$RAWtimeTC =		$StimeTC[$m];
							$StimeTC[$m] =		sprintf("%10s", $StimeTC[$m]);
							}
				
						// Check if the user had an AUTOLOGOUT timeclock event during the time period
						$TCuserAUTOLOGOUT = ' ';
						$query = mysqli_query($link, "SELECT COUNT(*) as cnt FROM vicidial_timeclock_log WHERE event='AUTOLOGOUT' AND user='$Suser[$m]' AND date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'");
						$timeclock_ct = mysqli_num_rows($query);
						
						if ($autologout_results > 0){
							$row = mysqli_fetch_array($query);
							//$row=$query->row();
							
							if ($row['cnt'] > 0){
								$TCuserAUTOLOGOUT =	'*';
								$AUTOLOGOUTflag++;
							}
						}
				
						// BEGIN loop through each status //
						$n=0;
						while ($n < $sub_status_count){
							$Sstatus=$sub_statusesARY[$n];
							$SstatusTXT='';
							
							// BEGIN loop through each stat line //
							$i=0;
							$status_found=0;
							
							while ( ($i < $pause_sec_ct) and ($status_found < 1) ){
								if ( ($Suser[$m] == $PCuser[$i]) and ($Sstatus == $sub_status[$i]) )
									{
									$USERcodePAUSE_MS =		gmdate('H:i:s', $PCpause_sec[$i]);
									if (strlen($USERcodePAUSE_MS)<1) {$USERcodePAUSE_MS='0';}
									$pfUSERcodePAUSE_MS =	sprintf("%10s", $USERcodePAUSE_MS);
		
									$SstatusesFILE .= ",$USERcodePAUSE_MS";
									//$sub_statusesTOP[$m]
									$Sstatuses[$m] .= "$USERcodePAUSE_MS";
									$status_found++;
									}
								$i++;
							}
							
							if ($status_found < 1){
								$SstatusesFILE .= ",0:00";
								//$Sstatuses[$m] .= " 0:00";
							}
							// END loop through each stat line //
							
							$n++;
							if(!empty($Sstatuses[$m]))
							$Sstatuses[$m] .= ",";

						}

						// END loop through each status //
						
						/*
						if ($file_download > 0)
							{
							if (strlen($RAWtime)<1) {$RAWtime='0';}
							if (strlen($RAWwait)<1) {$RAWwait='0';}
							if (strlen($RAWtalk)<1) {$RAWtalk='0';}
							if (strlen($RAWdispo)<1) {$RAWdispo='0';}
							if (strlen($RAWpause)<1) {$RAWpause='0';}
							if (strlen($RAWdead)<1) {$RAWdead='0';}
							if (strlen($RAWcustomer)<1) {$RAWcustomer='0';}
							$fileToutput = "$RAWname,$RAWuser,$RAWcalls,$RAWtimeTC,$RAWtime,$RAWwait,$RAWtalk,$RAWdispo,$RAWpause,$RAWdead,$RAWcustomer,$SstatusesFILE\n";
							}
						$Scalls[$m] = ($Scalls[$m] > 0) ? $Scalls[$m] : 0;
						
						if ($x==0) {
							$bgcolor = "#E0F8E0";
							$x=1;
						} else {
							$bgcolor = "#EFFBEF";
							$x=0;
						}
						*/
					//			<td> $StimeTC[$m]$TCuserAUTOLOGOUT </td>
						if(is_null($Scalls[$m])){
							$Scalls[$m] = 0;
						}
						$Toutput = array("rowID" => $rowId, "name" => $Sname[$m], "user" => $Suser[$m], "number_of_calls" => $Scalls[$m], "agent_time" => $Stime[$m], "wait_time" => $Swait[$m], "talk_time" => $Stalk[$m], "dispo_time" => $Sdispo[$m], "pause_time" => $Spause[$m], "wrap_up" => $Sdead[$m], "customer_time" => $Scustomer[$m]);
				
						/*$Boutput = "<tr>
								<td> $Sname[$m] </td>
								$Sstatuses[$m]
								</tr>";*/
						$Sstatuses[$m] = rtrim( $Sstatuses[$m], ",");
						$Boutput = array("rowID" => $rowId, "name" => $Sname[$m], "statuses" => $Sstatuses[$m]);

						$TOPsorted_output[$m] = $Toutput;
						$BOTsorted_output[$m] = $Boutput;
						//$TOPsorted_outputFILE[$m] = $fileToutput;
				
						if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage))
							if ($file_download > 0)
								{$file_output .= "$fileToutput";}
				
						if ($TOPsortMAX < $TOPsortTALLY[$m]) {$TOPsortMAX = $TOPsortTALLY[$m];}
				
					//		echo "$Suser[$m]|$Sname[$m]|$Swait[$m]|$Stalk[$m]|$Sdispo[$m]|$Spause[$m]|$Scalls[$m]\n";
						$m++;
						$rowId++;
					}
					//# END loop through each user formatting data for output
				
				
					$TOT_AGENTS = 'AGENTS: '.$m;
					// 	// BEGIN sort through output to display properly //
					if ( ($TOT_AGENTS > 0) and (preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) )
						{
						if (preg_match("/ID/",$stage))
							{sort($TOPsort, SORT_NUMERIC);}
						if (preg_match("/TIME|LEADS|TCLOCK/",$stage))
							{rsort($TOPsort, SORT_NUMERIC);}
						if (preg_match("/NAME/",$stage))
							{rsort($TOPsort, SORT_STRING);}
				
						$m=0;
						while ($m < $k)
							{
							$sort_split = explode("-----",$TOPsort[$m]);
							$i = $sort_split[1];
							$sort_order[$m] = "$i";
							//if ($file_download > 0)
							//	{$file_output .= "$TOPsorted_outputFILE[$i]";}
							$m++;
							}
						}
					// END sort through output to display properly //
				
					//////////////////////////////////////
					//# END formatting data for output section
					//////////////////////////////////////
				
				
				
				
					//////////////////////////////////////
					//# BEGIN last line totals output section
					//////////////////////////////////////
					$SUMstatusesHTML='';
					//$SUMstatusesFILE='';
					$TOTtotPAUSE=0;
					$n=0;
					while ($n < $sub_status_count)
						{
						$Scalls=0;
						$Sstatus=$sub_statusesARY[$n];
						$SUMstatusTXT='';
						// BEGIN loop through each stat line //
						$i=0; $status_found=0;
						while ($i < $pause_sec_ct){
							if ($Sstatus=="$sub_status[$i]")
								{
								$Scalls = ($Scalls + $PCpause_sec[$i]);
								$status_found++;
								}
							$i++;
						}
						// END loop through each stat line //
						if ($status_found < 1){
							$SUMstatuses[$n] = "00:00:00";
						}
						else{
							$TOTtotPAUSE = ($TOTtotPAUSE + $Scalls);
				
							$USERsumstatPAUSE_MS =		gmdate('H:i:s', $Scalls); 
							$pfUSERsumstatPAUSE_MS = 	sprintf("%11s", $USERsumstatPAUSE_MS);
		
							//$SUMstatusesFILE .= ",$USERsumstatPAUSE_MS";
							$SUMstatuses[$n] = $USERsumstatPAUSE_MS;
						}
						$n++;
					}
					// END loop through each status //
				
					// call function to calculate and print dialable leads
					$TOTwait = gmdate('H:i:s', $TOTwait);
					$TOTtalk = gmdate('H:i:s', $TOTtalk);
					$TOTdispo = gmdate('H:i:s', $TOTdispo);
					$TOTpause = gmdate('H:i:s', $TOTpause);
					$TOTdead = gmdate('H:i:s', $TOTdead);
					$TOTcustomer = gmdate('H:i:s', $TOTcustomer);
					$TOTALtime = gmdate('H:i:s', $TOTALtime);
					$TOTtimeTC = gmdate('H:i:s', $TOTtimeTC);
					
		
					// if ($file_download > 0)
					// 	{
					// 	$file_output .= "TOTAL: $TOT_AGENTS,$TOTcalls,$TOTtimeTC,$TOTALtime,$TOTwait,$TOTtalk,$TOTdispo,$TOTpause,$TOTdead,$TOTcustomer,$SUMstatusesFILE\n";
					// 	}
					//////////////////////////////////////
					//# END formatting data for output section
					//////////////////////////////////////
					
					// $return['TOPsorted_output']		= $TOPsorted_output;
					// $return['BOTsorted_output']		= $BOTsorted_output;
					// $return['TOPsorted_outputFILE']	= $TOPsorted_outputFILE;
					// $return['TOTwait']				= $TOTwait;
					// $return['TOTtalk']				= $TOTtalk;
					// $return['TOTdispo']				= $TOTdispo;
					// $return['TOTpause']				= $TOTpause;
					// $return['TOTdead']				= $TOTdead;
					// $return['TOTcustomer']			= $TOTcustomer;
					// $return['TOTALtime']			= $TOTALtime;
					// $return['TOTtimeTC']			= $TOTtimeTC;
					// $return['sub_statusesTOP']		= $sub_statusesTOP;
					// $return['SUMstatuses']			= $SUMstatuses;
					// $return['TOT_AGENTS']			= $TOT_AGENTS;
					// $return['TOTcalls']				= $TOTcalls;
					// $return['file_output']			= $file_output;

					$apiresults = array("result" => "success", "TOPsorted_output" => $TOPsorted_output, "sub_statusesTOP" => $sub_statusesTOP, "BOTsorted_output" => $BOTsorted_output, "SUMstatuses" => $SUMstatuses, "TOTwait" => $TOTwait, "TOTtalk" => $TOTtalk, "TOTdispo" => $TOTdispo, "TOTpause" => $TOTpause, "TOTdead" => $TOTdead, "TOTcustomer" => $TOTcustomer, "TOTALtime" => $TOTALtime, "TOTtimeTC" => $TOTtimeTC, "TOT_AGENTS" => $TOT_AGENTS, "TOTcalls" => $TOTcalls);
					
					return $apiresults;
					
				}
				
				// Agent Performance Detail
				if ($pageTitle == "agent_pdetail") {
					$statusesFILE='';
					$statuses='-';
					$statusesARY[0]='';
					$j=0;
					$users='-';
					$usersARY[0]='';
					$user_namesARY[0]='';
					$k=0;
					//if (inner_checkIfTenant($userGroup, $linkgo))
					if($userGroup !== "ADMIN")
						$userGroupSQL = "and vicidial_users.user_group='$userGroup'";
					if($date_diff <= 0){
						$filters = "and pause_sec<65000 and wait_sec<65000 and talk_sec<65000 and dispo_sec<65000 ";
					}
					
					$perfdetails_sql = "select count(*) as calls,sum(talk_sec) as talk,full_name,vicidial_users.user as user,sum(pause_sec) as pause_sec,sum(wait_sec) as wait_sec,sum(dispo_sec) as dispo_sec,status,sum(dead_sec) as dead_sec from vicidial_users,vicidial_agent_log where date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' and vicidial_users.user=vicidial_agent_log.user $userGroupSQL and campaign_id='$campaignID' group by user,full_name,status order by full_name,user,status desc limit 500000";
					$query = mysqli_query($link, $perfdetails_sql);
					
					$rows_to_print = mysqli_num_rows($query);
					
					/* foreach($query->result() as $i => $row)
						{
						$calls[$i] =		$row->calls;
						$talk_sec[$i] =		$row->talk;
						$full_name[$i] =	$row->full_name;
						$user[$i] =		$row->user;
						$pause_sec[$i] =	$row->pause_sec;
						$wait_sec[$i] =		$row->wait_sec;
						$dispo_sec[$i] =	$row->dispo_sec;
						$status[$i] =		$row->status;
						$dead_sec[$i] =		$row->dead_sec;
						$customer_sec[$i] =	($talk_sec[$i] - $dead_sec[$i]);
						if ($customer_sec[$i] < 1)
							{$customer_sec[$i]=0;}
						if ( (!eregi("-$status[$i]-", $statuses)) and (strlen($status[$i])>0) )
							{
							$statusesFILE .= ",$status[$i]";
							$statuses .= "$status[$i]-";
							$SUMstatuses .= "$status[$i] ";
							$statusesARY[$j] = $status[$i];
							$SstatusesTOP .= "<td nowrap><div align=\"center\" class=\"style4\"><strong>&nbsp; $status[$i] &nbsp;</strong></div></td>";
							$j++;
							}
						if (!eregi("-$user[$i]-", $users))
							{
							$users .= "$user[$i]-";
							$usersARY[$k] = $user[$i];
							$user_namesARY[$k] = $full_name[$i];
							$k++;
							}
					
						$i++;
						} */
					$i=0;
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
						$calls[$i] =		$row['calls'];
						$talk_sec[$i] =		$row['talk'];
						$full_name[$i] =	$row['full_name'];
						$user[$i] =		$row['user'];
						$pause_sec[$i] =	$row['pause_sec'];
						$wait_sec[$i] =		$row['wait_sec'];
						$dispo_sec[$i] =	$row['dispo_sec'];
						$status[$i] =		$row['status'];
						$dead_sec[$i] =		$row['dead_sec'];
						$customer_sec[$i] =	($talk_sec[$i] - $dead_sec[$i]);
						
						if ($customer_sec[$i] < 1)
							{$customer_sec[$i]=0;}
						if ( (!preg_match("/-$status[$i]-/", $statuses)) and (strlen($status[$i])>0) )
							{
							$statusesFILE .= ",$status[$i]";
							$statuses .= "$status[$i]-";
							$SUMstatuses .= "$status[$i] ";
							$statusesARY[$j] = $status[$i];
								
							## getting status name
								$var_status = $status[$i];
							
								# in default statuses
								$query_default_statusname = mysqli_query($link, "SELECT status_name FROM vicidial_statuses WHERE status = '$var_status' LIMIT 1;");
								if($query_default_statusname){
									$fetch_statusname = mysqli_fetch_array($query_default_statusname);
								}
								
								if(!isset($fetch_statusname) || $fetch_statusname == NULL){
								# in custom statuses
								$query_custom_statusname = mysqli_query($link, "SELECT status_name FROM vicidial_campaign_statuses WHERE status = '$var_status' LIMIT 1;");
									$fetch_statusname = mysqli_fetch_array($query_custom_statusname);
								}
							
							$legend[] = $status[$i]." = ".$fetch_statusname['status_name'];
							
							## end of getting status name
							
							$SstatusesTOP .= "<th> $status[$i] </th>";
							$j++;
							}
						if (!preg_match("/-$user[$i]-/", $users))
							{
							$users .= "$user[$i]-";
							$usersARY[$k] = $user[$i];
							$user_namesARY[$k] = $full_name[$i];
							$k++;
							}
					$i++;
					}
					
					
					if ($file_download > 0)
						{
						$file_output  = "CAMPAIGN,$campaignID - ".$resultu->campaign_name."\n";
						$file_output .= "DATE RANGE,$fromDate TO $toDate\n\n";
						$file_output .= "USER NAME,ID,CALLS,AGENT TIME,PAUSE,PAUSE AVG,WAIT,WAIT AVG,TALK,TALK AVG,DISPO,DISPO AVG,WRAPUP,WRAPUP AVG,CUSTOMER,CUST AVG $statusesFILE\n";
						}
					
					// BEGIN loop through each user //
					$m=0;
					while ($m < $k)
						{
						$Suser=$usersARY[$m];
						$Sfull_name=$user_namesARY[$m];
						$Stime=0;
						$Scalls=0;
						$Stalk_sec=0;
						$Spause_sec=0;
						$Swait_sec=0;
						$Sdispo_sec=0;
						$Sdead_sec=0;
						$Scustomer_sec=0;
						$SstatusesHTML='';
						$SstatusesFILE='';
					
						// BEGIN loop through each status //
						$n=0;
						while ($n < $j)
							{
							$Sstatus=$statusesARY[$n];
							$SstatusTXT='';
							// BEGIN loop through each stat line //
							$i=0; $status_found=0;
							while ($i < $rows_to_print)
								{
								if ( ($Suser=="$user[$i]") and ($Sstatus=="$status[$i]") )
									{
									$Scalls =		($Scalls + $calls[$i]);
									$Stalk_sec =	($Stalk_sec + $talk_sec[$i]);
									$Spause_sec =	($Spause_sec + $pause_sec[$i]);
									$Swait_sec =	($Swait_sec + $wait_sec[$i]);
									$Sdispo_sec =	($Sdispo_sec + $dispo_sec[$i]);
									$Sdead_sec =	($Sdead_sec + $dead_sec[$i]);
									$Scustomer_sec =	($Scustomer_sec + $customer_sec[$i]);
									$SstatusesFILE .= ",$calls[$i]";
									$SstatusesMID[$m] .= "<td> $calls[$i] </td>";
									$status_found++;
									}
								$i++;
								}
							if ($status_found < 1)
								{
								$SstatusesFILE .= ",0";
								$SstatusesMID[$m] .= "<td> 0 </td>";
								}
							// END loop through each stat line //
							$n++;
							}
						// END loop through each status //
						$Stime = ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
						$TOTcalls=($TOTcalls + $Scalls);
						$TOTtime=($TOTtime + $Stime);
						$TOTtotTALK=($TOTtotTALK + $Stalk_sec);
						$TOTtotWAIT=($TOTtotWAIT + $Swait_sec);
						$TOTtotPAUSE=($TOTtotPAUSE + $Spause_sec);
						$TOTtotDISPO=($TOTtotDISPO + $Sdispo_sec);
						$TOTtotDEAD=($TOTtotDEAD + $Sdead_sec);
						$TOTtotCUSTOMER=($TOTtotCUSTOMER + $Scustomer_sec);
						$Stime = ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
						if ( ($Scalls > 0) and ($Stalk_sec > 0) ) {$Stalk_avg = ($Stalk_sec/$Scalls);}
							else {$Stalk_avg=0;}
						if ( ($Scalls > 0) and ($Spause_sec > 0) ) {$Spause_avg = ($Spause_sec/$Scalls);}
							else {$Spause_avg=0;}
						if ( ($Scalls > 0) and ($Swait_sec > 0) ) {$Swait_avg = ($Swait_sec/$Scalls);}
							else {$Swait_avg=0;}
						if ( ($Scalls > 0) and ($Sdispo_sec > 0) ) {$Sdispo_avg = ($Sdispo_sec/$Scalls);}
							else {$Sdispo_avg=0;}
						if ( ($Scalls > 0) and ($Sdead_sec > 0) ) {$Sdead_avg = ($Sdead_sec/$Scalls);}
							else {$Sdead_avg=0;}
						if ( ($Scalls > 0) and ($Scustomer_sec > 0) ) {$Scustomer_avg = ($Scustomer_sec/$Scalls);}
							else {$Scustomer_avg=0;}
					
						$RAWuser = $Suser;
						$RAWcalls = $Scalls;
					
						$pfUSERtime_MS =		gmdate('H:i:s', $Stime); 
						$pfUSERtotTALK_MS =		gmdate('H:i:s', $Stalk_sec); 
						$pfUSERavgTALK_MS =		gmdate('H:i:s', $Stalk_avg);
						$pfUSERtotPAUSE_MS =	gmdate('H:i:s', $Spause_sec);
						$pfUSERavgPAUSE_MS =	gmdate('H:i:s', $Spause_avg);
						$pfUSERtotWAIT_MS =		gmdate('H:i:s', $Swait_sec); 
						$pfUSERavgWAIT_MS =		gmdate('H:i:s', $Swait_avg); 
						$pfUSERtotDISPO_MS =	gmdate('H:i:s', $Sdispo_sec); 
						$pfUSERavgDISPO_MS =	gmdate('H:i:s', $Sdispo_avg); 
						$pfUSERtotDEAD_MS =		gmdate('H:i:s', $Sdead_sec); 
						$pfUSERavgDEAD_MS =		gmdate('H:i:s', $Sdead_avg); 
						$pfUSERtotCUSTOMER_MS =	gmdate('H:i:s', $Scustomer_sec); 
						$pfUSERavgCUSTOMER_MS =	gmdate('H:i:s', $Scustomer_avg); 
					
						$PAUSEtotal[$m] = $pfUSERtotPAUSE_MS;
					
						if ($file_download > 0) {
							$fileToutput = "$Sfull_name,=\"$Suser\",$Scalls,$pfUSERtime_MS,$pfUSERtotPAUSE_MS,$pfUSERavgPAUSE_MS,$pfUSERtotWAIT_MS,$pfUSERavgWAIT_MS,$pfUSERtotTALK_MS,$pfUSERavgTALK_MS,$pfUSERtotDISPO_MS,$pfUSERavgDISPO_MS,$pfUSERtotDEAD_MS,$pfUSERavgDEAD_MS,$pfUSERtotCUSTOMER_MS,$pfUSERavgCUSTOMER_MS$SstatusesFILE\n";
						}
						
						if ($x==0) {
							$bgcolor = "#E0F8E0";
							$x=1;
						} else {
							$bgcolor = "#EFFBEF";
							$x=0;
						}
						
						$Toutput = "<tr>
								<td> $Sfull_name </td>
								<td> $Suser </td>
								<td> $Scalls </td>
								<td> $pfUSERtime_MS </td>
								<td> $pfUSERtotPAUSE_MS </td>
								<td> $pfUSERavgPAUSE_MS </td>
								<td> $pfUSERtotWAIT_MS </td>
								<td> $pfUSERavgWAIT_MS </td>
								<td> $pfUSERtotTALK_MS </td>
								<td> $pfUSERavgTALK_MS </td>
								<td> $pfUSERtotDISPO_MS </td>
								<td> $pfUSERavgDISPO_MS </td>
								<td> $pfUSERtotDEAD_MS </td>
								<td> $pfUSERavgDEAD_MS </td>
								<td> $pfUSERtotCUSTOMER_MS </td>
								<td> $pfUSERavgCUSTOMER_MS </td>
								</tr>";
					
						$Moutput = "<tr>
								<td> $Sfull_name </td>
								$SstatusesMID[$m]
								</tr>";
					
						$TOPsorted_output[$m] = $Toutput;
						$MIDsorted_output[$m] = $Moutput;
						$TOPsorted_outputFILE[$m] = $fileToutput;
					
						if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage))
							if ($file_download > 0)
								{$file_output .= "$fileToutput";}
					
						$m++;
						}
					// END loop through each user //
					
					// BEGIN sort through output to display properly //
					if (preg_match("/ID|TIME|LEADS/",$stage))
						{
						if (preg_match("/ID/",$stage))
							{sort($TOPsort, SORT_NUMERIC);}
						if (preg_match("/TIME|LEADS/",$stage))
							{rsort($TOPsort, SORT_NUMERIC);}
					
						$m=0;
						while ($m < $k)
							{
							$sort_split = explode("-----",$TOPsort[$m]);
							$i = $sort_split[1];
							$sort_order[$m] = "$i";
							if ($file_download > 0)
								{$file_output .= "$TOPsorted_outputFILE[$i]";}
							$m++;
							}
						}
					// END sort through output to display properly //
					
					
					
					//## LAST LINE FORMATTING ////##
					// BEGIN loop through each status //
					$SUMstatusesHTML='';
					$SUMstatusesFILE='';
					$n=0;
					while ($n < $j)
						{
						$Scalls=0;
						$Sstatus=$statusesARY[$n];
						$SUMstatusTXT='';
						// BEGIN loop through each stat line //
						$i=0; $status_found=0;
						while ($i < $rows_to_print)
							{
							if ($Sstatus=="$status[$i]")
								{
								$Scalls =		($Scalls + $calls[$i]);
								$status_found++;
								}
							$i++;
							}
						// END loop through each stat line //
						if ($status_found < 1)
							{
							$SUMstatusesFILE .= ",0";
							$SstatusesSUM .= "<th> 0 </th>";
							}
						else
							{
							$SUMstatusesFILE .= ",$Scalls";
							$SstatusesSUM .= "<th> $Scalls </th>";
							}
						$n++;
						}
					// END loop through each status //
					$TOT_AGENTS = '<th nowrap>AGENTS: '.$m.'</th>';
					
					if ($TOTtotTALK < 1) {$TOTavgTALK = '0';}
					else {$TOTavgTALK = ($TOTtotTALK / $TOTcalls);}
					if ($TOTtotDISPO < 1) {$TOTavgDISPO = '0';}
					else {$TOTavgDISPO = ($TOTtotDISPO / $TOTcalls);}
					if ($TOTtotDEAD < 1) {$TOTavgDEAD = '0';}
					else {$TOTavgDEAD = ($TOTtotDEAD / $TOTcalls);}
					if ($TOTtotPAUSE < 1) {$TOTavgPAUSE = '0';}
					else {$TOTavgPAUSE = ($TOTtotPAUSE / $TOTcalls);}
					if ($TOTtotWAIT < 1) {$TOTavgWAIT = '0';}
					else {$TOTavgWAIT = ($TOTtotWAIT / $TOTcalls);}
					if ($TOTtotCUSTOMER < 1) {$TOTavgCUSTOMER = '0';}
					else {$TOTavgCUSTOMER = ($TOTtotCUSTOMER / $TOTcalls);}
					
					$TOTcalls = '<th nowrap>'.$TOTcalls.'</th>';
					$TOTtime_MS = '<th nowrap>'.gmdate('H:i:s', $TOTtime).'</th>'; 
					$TOTtotTALK_MS = '<th nowrap>'.gmdate('H:i:s', $TOTtotTALK).'</th>'; 
					$TOTtotDISPO_MS = '<th nowrap>'.gmdate('H:i:s', $TOTtotDISPO).'</th>'; 
					$TOTtotDEAD_MS = '<th nowrap>'.gmdate('H:i:s', $TOTtotDEAD).'</th>'; 
					$TOTtotPAUSE_MS = '<th nowrap>'.gmdate('H:i:s', $TOTtotPAUSE).'</th>'; 
					$TOTtotWAIT_MS = '<th nowrap>'.gmdate('H:i:s', $TOTtotWAIT).'</th>'; 
					$TOTtotCUSTOMER_MS = '<th nowrap>'.gmdate('H:i:s', $TOTtotCUSTOMER).'</th>'; 
					$TOTavgTALK_MS = '<th nowrap>'.gmdate('H:i:s', $TOTavgTALK).'</th>'; 
					$TOTavgDISPO_MS = '<th nowrap>'.gmdate('H:i:s', $TOTavgDISPO).'</th>'; 
					$TOTavgDEAD_MS = '<th nowrap>'.gmdate('H:i:s', $TOTavgDEAD).'</th>'; 
					$TOTavgPAUSE_MS = '<th nowrap>'.gmdate('H:i:s', $TOTavgPAUSE).'</th>'; 
					$TOTavgWAIT_MS = '<th nowrap>'.gmdate('H:i:s', $TOTavgWAIT).'</th>'; 
					$TOTavgCUSTOMER_MS = '<th nowrap>'.gmdate('H:i:s', $TOTavgCUSTOMER).'</th>'; 
					
					if ($file_download > 0)
						{
						$file_output .= "TOTAL AGENTS: $TOT_AGENTS,$TOTcalls,$TOTtime_MS,$TOTtotPAUSE_MS,$TOTavgPAUSE_MS,$TOTtotWAIT_MS,$TOTavgWAIT_MS,$TOTtotTALK_MS,$TOTavgTALK_MS,$TOTtotDISPO_MS,$TOTavgDISPO_MS,$TOTtotDEAD_MS,$TOTavgDEAD_MS,$TOTtotCUSTOMER_MS,$TOTavgCUSTOMER_MS$SUMstatusesFILE\n";
						}
					
					$sub_statuses='-';
					$sub_statusesTXT='';
					$sub_statusesFILE='';
					$sub_statusesHEAD='';
					$sub_statusesHTML='';
					$sub_statusesARY=$MT;
					$j=0;
					$PCusers='-';
					$PCusersARY=$MT;
					$PCuser_namesARY=$MT;
					$k=0;
					$query = mysqli_query($link, "select full_name,vicidial_users.user as user,sum(pause_sec) as pause_sec,sub_status,sum(wait_sec + talk_sec + dispo_sec) as non_pause_sec from vicidial_users,vicidial_agent_log where date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' and vicidial_users.user=vicidial_agent_log.user $userGroupSQL and campaign_id='$campaignID' and pause_sec<65000 group by user,full_name,sub_status order by full_name,user,sub_status desc limit 100000");
					$subs_to_print = mysqli_num_rows($query);
				   
					/* foreach ($query->result() as $i => $row)
						{
						$PCfull_name[$i] =	$row->full_name;
						$PCuser[$i] =		$row->user;
						$PCpause_sec[$i] =	$row->pause_sec;
						$sub_status[$i] =	$row->sub_status;
						$PCnon_pause_sec[$i] =	$row->non_pause_sec;
					
						if (!eregi("-$sub_status[$i]-", $sub_statuses))
							{
							$sub_statuses .= "$sub_status[$i]-";
							$sub_statusesFILE .= ",$sub_status[$i]";
							$sub_statusesARY[$j] = $sub_status[$i];
							$SstatusesBOT .= "<td nowrap><div align=\"center\" class=\"style4\"><strong>&nbsp; $sub_status[$i] &nbsp;</strong></div></td>";
							$j++;
							}
						if (!eregi("-$PCuser[$i]-", $PCusers))
							{
							$PCusers .= "$PCuser[$i]-";
							$PCusersARY[$k] = $PCuser[$i];
							$PCuser_namesARY[$k] = $PCfull_name[$i];
							$k++;
							}
					
						$i++;
						}*/
					
					$i=0;             
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
					
						$PCfull_name[$i] =	$row['full_name'];
						$PCuser[$i] =		$row['user'];
						$PCpause_sec[$i] =	$row['pause_sec'];
						$sub_status[$i] =	$row['sub_status'];
						$PCnon_pause_sec[$i] =	$row['non_pause_sec'];
						
						if (!preg_match("/-$sub_status[$i]-/", $sub_statuses))
							{
							$sub_statuses .= "$sub_status[$i]-";
							$sub_statusesFILE .= ",$sub_status[$i]";
							$sub_statusesARY[$j] = $sub_status[$i];
							$SstatusesBOT .= "<th> $sub_status[$i] </th>";
							$j++;
							}
						if (!preg_match("/-$PCuser[$i]-/", $PCusers))
							{
							$PCusers .= "$PCuser[$i]-";
							$PCusersARY[$k] = $PCuser[$i];
							$PCuser_namesARY[$k] = $PCfull_name[$i];
							$k++;
							}
					$i++;
					}
	
					
					if ($file_download > 0) {
						$file_output .= "\n\nUSER NAME,ID,TOTAL,NONPAUSE,PAUSE,$sub_statusesFILE\n";
					}
					
					// BEGIN loop through each user //
					$m=0;
					$Suser_ct = count($usersARY);
					$TOTtotNONPAUSE = 0;
					$TOTtotTOTAL = 0;
					
					while ($m < $k)
						{
						$d=0;
						while ($d < $Suser_ct)
							{
							if ($usersARY[$d] === "$PCusersARY[$m]")
								{$pcPAUSEtotal = $PAUSEtotal[$d];}
							$d++;
							}
						$Suser=$PCusersARY[$m];
						$Sfull_name=$PCuser_namesARY[$m];
						$Spause_sec=0;
						$Snon_pause_sec=0;
						$Stotal_sec=0;
						$SstatusesHTML='';
						$Ssub_statusesFILE='';
					
						// BEGIN loop through each status //
						$n=0;
						while ($n < $j)
							{
							$Sstatus=$sub_statusesARY[$n];
							$SstatusTXT='';
							// BEGIN loop through each stat line //
							$i=0; $status_found=0;
							while ($i < $subs_to_print)
								{
								if ( ($Suser=="$PCuser[$i]") and ($Sstatus=="$sub_status[$i]") )
									{
									$Spause_sec =	($Spause_sec + $PCpause_sec[$i]);
									$Snon_pause_sec =	($Snon_pause_sec + $PCnon_pause_sec[$i]);
									$Stotal_sec =	($Stotal_sec + $PCnon_pause_sec[$i] + $PCpause_sec[$i]);
					
									$USERcodePAUSE_MS =	gmdate('H:i:s', $PCpause_sec[$i]); 
									$pfUSERcodePAUSE_MS =	sprintf("%6s", $USERcodePAUSE_MS);
					
									$Ssub_statusesFILE .= ",$USERcodePAUSE_MS";
									$SstatusesBOTR[$m] .= "<td> $USERcodePAUSE_MS </td>";
									$status_found++;
									}
								$i++;
								}
							if ($status_found < 1)
								{
								$Ssub_statusesFILE .= ",0";
								$SstatusesBOTR[$m] .= "<td> 0:00 </td>";
								}
							// END loop through each stat line //
							$n++;
							}
						// END loop through each status //
						$TOTtotPAUSE=($TOTtotPAUSE + $Spause_sec);
					
						$TOTtotNONPAUSE = ($TOTtotNONPAUSE + $Snon_pause_sec);
						$TOTtotTOTAL = ($TOTtotTOTAL + $Stotal_sec);
					
						$pfUSERtotPAUSE_MS =		gmdate('H:i:s', $Spause_sec); 
						$pfUSERtotNONPAUSE_MS =		gmdate('H:i:s', $Snon_pause_sec); 
						$pfUSERtotTOTAL_MS =		gmdate('H:i:s', $Stotal_sec); 
					
						if ($file_download > 0) {
							$fileToutput = "$Sfull_name,=\"$Suser\",$pfUSERtotTOTAL_MS,$pfUSERtotNONPAUSE_MS,$pfUSERtotPAUSE_MS,$Ssub_statusesFILE\n";
						}
						
						if ($x==1) {
							$bgcolor = "#E0F8E0";
							$x=0;
						} else {
							$bgcolor = "#EFFBEF";
							$x=1;
						}
						
						$Boutput = "<tr>
								<td> $Sfull_name </td>
								<td> $Suser </td>
								<td> $pfUSERtotTOTAL_MS </td>
								<td> $pfUSERtotNONPAUSE_MS </td>
								<td> $pfUSERtotPAUSE_MS </td>
								</tr>";
					
						$BOTsorted_output[$m] = $Boutput;
					
						if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage))
							if ($file_download > 0)
								{$file_output .= "$fileToutput";}
					
						$m++;
						}
					// END loop through each user //
					
					// BEGIN sort through output to display properly //
					if (preg_match("/ID|TIME|LEADS/",$stage))
						{
						$n=0;
						while ($n <= $m)
							{
							$i = $sort_order[$m];
							if ($file_download > 0)
								{$file_output .= "$TOPsorted_outputFILE[$i]";}
							$m--;
							}
						}
					// END sort through output to display properly //
					
					//## LAST LINE FORMATTING ////##
					// BEGIN loop through each status //
					$SUMstatusesHTML='';
					$SUMsub_statusesFILE='';
					$TOTtotPAUSE=0;
					$n=0;
					while ($n < $j)
						{
						$Scalls=0;
						$Sstatus=$sub_statusesARY[$n];
						$SUMstatusTXT='';
						// BEGIN loop through each stat line //
						$i=0; $status_found=0;
						while ($i < $subs_to_print)
							{
							if ($Sstatus=="$sub_status[$i]")
								{
								$Scalls =		($Scalls + $PCpause_sec[$i]);
								$status_found++;
								}
							$i++;
							}
						// END loop through each stat line //
						if ($status_found < 1)
							{
							$SUMsub_statusesFILE .= ",0";
							$SstatusesBSUM .= "<th nowrap> 0:00 </th>";
							}
						else
							{
							$TOTtotPAUSE = ($TOTtotPAUSE + $Scalls);
					
							$USERsumstatPAUSE_MS =		gmdate('H:i:s', $Scalls); 
					
							$SUMsub_statusesFILE .= ",$USERsumstatPAUSE_MS";
							$SstatusesBSUM .= "<th nowrap> $USERsumstatPAUSE_MS </th>";
							}
						$n++;
						}
					// END loop through each status //
						$TOT_AGENTS = '<th nowrap>AGENTS: '.$m.'</th>';
					
						$TOTtotPAUSEB_MS = '<th nowrap>'.gmdate('H:i:s', $TOTtotPAUSE).'</th>'; 
						$TOTtotNONPAUSE_MS = '<th nowrap>'.gmdate('H:i:s', $TOTtotNONPAUSE).'</th>'; 
						$TOTtotTOTAL_MS = '<th nowrap>'.gmdate('H:i:s', $TOTtotTOTAL).'</th>'; 
					
						if ($file_download > 0) {
							$file_output .= "TOTAL AGENTS: $TOT_AGENTS,$TOTtotTOTAL_MS,$TOTtotNONPAUSE_MS,$TOTtotPAUSE_MS,$SUMsub_statusesFILE\n";
						}
						
					$return['TOPsorted_output']		= $TOPsorted_output;
					$return['BOTsorted_output']		= $BOTsorted_output;
					$return['TOPsorted_outputFILE']	= $TOPsorted_outputFILE;
					$return['TOTwait']				= $TOTwait;
					$return['TOTtalk']				= $TOTtalk;
					$return['TOTdispo']				= $TOTdispo;
					$return['TOTpause']				= $TOTpause;
					$return['TOTdead']				= $TOTdead;
					$return['TOTcustomer']			= $TOTcustomer;
					$return['TOTALtime']			= $TOTALtime;
					$return['TOTtimeTC']			= $TOTtimeTC;
					$return['sub_statusesTOP']		= $sub_statusesTOP;
					$return['SUMstatuses']			= $SUMstatuses;
					$return['TOT_AGENTS']			= $TOT_AGENTS;
					$return['TOTcalls']				= $TOTcalls;
					$return['TOTtime_MS']			= $TOTtime_MS; 
					$return['TOTtotTALK_MS']		= $TOTtotTALK_MS; 
					$return['TOTtotDISPO_MS']		= $TOTtotDISPO_MS; 
					$return['TOTtotDEAD_MS']		= $TOTtotDEAD_MS; 
					$return['TOTtotPAUSE_MS']		= $TOTtotPAUSE_MS; 
					$return['TOTtotWAIT_MS']		= $TOTtotWAIT_MS; 
					$return['TOTtotCUSTOMER_MS']	= $TOTtotCUSTOMER_MS; 
					$return['TOTavgTALK_MS']		= $TOTavgTALK_MS; 
					$return['TOTavgDISPO_MS']		= $TOTavgDISPO_MS; 
					$return['TOTavgDEAD_MS']		= $TOTavgDEAD_MS; 
					$return['TOTavgPAUSE_MS']		= $TOTavgPAUSE_MS; 
					$return['TOTavgWAIT_MS']		= $TOTavgWAIT_MS; 
					$return['TOTavgCUSTOMER_MS']	= $TOTavgCUSTOMER_MS; 
					$return['TOTtotTOTAL_MS']		= $TOTtotTOTAL_MS;
					$return['TOTtotNONPAUSE_MS']	= $TOTtotNONPAUSE_MS; 
					$return['TOTtotPAUSEB_MS']		= $TOTtotPAUSEB_MS; 
					$return['MIDsorted_output']		= $MIDsorted_output; 
					$return['SstatusesTOP']			= $SstatusesTOP; 
					$return['SstatusesSUM']			= $SstatusesSUM;
					$return['SstatusesBOT']			= $SstatusesBOT; 
					$return['SstatusesBOTR']		= $SstatusesBOTR;
					$return['SstatusesBSUM']		= $SstatusesBSUM;
					$return['file_output']			= $file_output;
					
					$apiresults = array("result" => "success",
					"TOPsorted_output" => $TOPsorted_output,
					"BOTsorted_output" => $BOTsorted_output,
					"TOPsorted_outputFILE"	=> $TOPsorted_outputFILE,
					"TOTwait" => $TOTwait,
					"TOTtalk" => $TOTtalk,
					"TOTdispo" => $TOTdispo,
					"TOTpause" => $TOTpause,
					"TOTdead" => $TOTdead,
					"TOTcustomer" => $TOTcustomer,
					"TOTALtime"	=> $TOTALtime,
					"TOTtimeTC" => $TOTtimeTC,
					"sub_statusesTOP" => $sub_statusesTOP,
					"SUMstatuses" => $SUMstatuses,
					"TOT_AGENTS" => $TOT_AGENTS,
					"TOTcalls" => $TOTcalls,
					"TOTtime_MS" => $TOTtime_MS, 
					"TOTtotTALK_MS"	=> $TOTtotTALK_MS, 
					"TOTtotDISPO_MS" => $TOTtotDISPO_MS, 
					"TOTtotDEAD_MS"	=> $TOTtotDEAD_MS, 
					"TOTtotPAUSE_MS" => $TOTtotPAUSE_MS, 
					"TOTtotWAIT_MS"	=> $TOTtotWAIT_MS, 
					"TOTtotCUSTOMER_MS" => $TOTtotCUSTOMER_MS, 
					"TOTavgTALK_MS"	=> $TOTavgTALK_MS, 
					"TOTavgDISPO_MS" => $TOTavgDISPO_MS, 
					"TOTavgDEAD_MS"	=> $TOTavgDEAD_MS, 
					"TOTavgPAUSE_MS" => $TOTavgPAUSE_MS, 
					"TOTavgWAIT_MS" => $TOTavgWAIT_MS, 
					"TOTavgCUSTOMER_MS"	=> $TOTavgCUSTOMER_MS, 
					"TOTtotTOTAL_MS" => $TOTtotTOTAL_MS,
					"TOTtotNONPAUSE_MS"	=> $TOTtotNONPAUSE_MS, 
					"TOTtotPAUSEB_MS" => $TOTtotPAUSEB_MS, 
					"MIDsorted_output"	=> $MIDsorted_output, 
					"SstatusesTOP" => $SstatusesTOP, 
					"SstatusesSUM" => $SstatusesSUM,
					"SstatusesBOT" => $SstatusesBOT, 
					"SstatusesBOTR"	=> $SstatusesBOTR,
					"SstatusesBSUM"	=> $SstatusesBSUM,
					"Legend" => $legend,
					"query" => $perfdetails_sql
					//$return['file_output']			= $file_output;
					);
					
					return $apiresults;
				}
				
				//Dial Statuses Summary
				if ($pageTitle=="dispo") {
					$list_ids[0] = "ALL";
					//$total_all=($list_ids[0] == "{$this->lang->line("go_all")}") ? ''.$this->lang->line("go_all_list_ids").' '.$campaignID : ''.$this->lang->line("go_list_ids").': '.implode(',',$list_ids);
					$total_all=($list_ids[0] == "ALL") ? 'ALL List IDs under '.$campaignID : 'List ID(s): '.implode(',',$list_ids);
					
					if (isset($list_ids) && $list_ids[0] == "ALL") {
						$query = mysqli_query($link, "SELECT list_id FROM vicidial_lists WHERE campaign_id='$campaignID' ORDER BY list_id");
		
						/*foreach ($query->result() as $i => $row) {
							$list_ids[$i]=$row->list_id;
						}
						$i++;
						$list_ids[$i] = "{$userGroup}0";
						*/
						$i=0;
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
							$list_ids[$i]=$row['list_id'];
							$i++;
						}
						//$list_ids[$i] = "{$userGroup}0";
					}
					$list = "'".implode("','",$list_ids)."'";
					// grab names of global statuses and statuses in the selected campaign
					$query = mysqli_query($link, "SELECT status,status_name from vicidial_statuses order by status");
					//$statuses_to_print = $query->num_rows();
					$statuses_to_print = mysqli_num_rows($query);
					
					/*foreach ($query->result() as $o => $row) 
						{
						$statuses_list[$row->status] = $row->status_name;
						}
						*/
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
						$statuses_list[$row['status']] = $row['status_name'];
					}
			
					$query_list = "SELECT status, status_name FROM vicidial_campaign_statuses WHERE campaign_id = '$campaignID'; ";
					$query = mysqli_query($link, $query_list);
					//$Cstatuses_to_print = $query->num_rows();
					//$Cstatuses_to_print = mysqli_num_rows($query);
		
					/*foreach ($query->result() as $o => $row) 
						{
						$statuses_list[$row->status] = $row->status_name;
						}*/
					
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
						$query_name = mysqli_query($link, "SELECT status, status_name FROM vicidial_campaign_statuses WHERE campaign_id = '$campaignID' and list_id");
						$statuses_list[$row['status']] = $row['status_name'];
					}
					# end grab status names
					
					$leads_in_list = 0;
					$leads_in_list_N = 0;
					$leads_in_list_Y = 0;
					
					$queryx = "SELECT status, if(called_count >= 10, 10, called_count) as called_count, count(*) as count from vicidial_list where list_id IN(".$list.") and status NOT IN('DC','DNCC','XDROP') group by status, if(called_count >= 10, 10, called_count) order by status,called_count";
					$query = mysqli_query($link, $queryx);
					$status_called_to_print = mysqli_num_rows($query);
					
					$sts=0;
					$first_row=1;
					$all_called_first=1000;
					$all_called_last=0;
					
					/* foreach ($query->result() as $o => $row) 
						{
						$leads_in_list = ($leads_in_list + $row->count);
						$count_statuses[$o]			= $row->status;
						$count_called[$o]			= $row->called_count;
						$count_count[$o]			= $row->count;
						$all_called_count[$row->called_count] = ($all_called_count[$row->called_count] + $row->count);
			
						if ( (strlen($status[$sts]) < 1) or ($status[$sts] != $row->status) )
							{
							if ($first_row) {$first_row=0;}
							else {$sts++;}
							$status[$sts] = $row->status;
							$status_called_first[$sts] = $row->called_count;
							if ($status_called_first[$sts] < $all_called_first) {$all_called_first = $status_called_first[$sts];}
							}
						$leads_in_sts[$sts] = ($leads_in_sts[$sts] + $row->count);
						$status_called_last[$sts] = $row->called_count;
						if ($status_called_last[$sts] > $all_called_last) {$all_called_last = $status_called_last[$sts];}
						} */
		 
					$o=0;
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
						$leads_in_list = ($leads_in_list + $row['count']);
						$count_statuses[$o]			= $row['status'];
						$count_called[$o]			= $row['called_count'];
						$count_count[$o]			= $row['count'];
						$all_called_count[$row['called_count']] = ($all_called_count[$row['called_count']] + $row['count']);
						
						if ( (strlen($status[$sts]) < 1) or ($status[$sts] != $row['status']) )
							{
							if ($first_row) {$first_row=0;}
							else {$sts++;}
							$status[$sts] = $row['status'];
							$status_called_first[$sts] = $row['called_count'];
							if ($status_called_first[$sts] < $all_called_first) {$all_called_first = $status_called_first[$sts];}
							}
						$leads_in_sts[$sts] = ($leads_in_sts[$sts] + $row['count']);
						$status_called_last[$sts] = $row['called_count'];
						if ($status_called_last[$sts] > $all_called_last) {$all_called_last = $status_called_last[$sts];}
						$o++;
					}
						
			
					$TOPsorted_output = "<center>\n";
					$TOPsorted_output .= "
					<TABLE class='table table-striped table-bordered table-hover' id='dispo'>\n";
					$TOPsorted_output .= "
						<thead>
						<tr>
						<th>STATUS</th>
						<th>Status Name</th>";
					$first = $all_called_first;
					while ($first <= $all_called_last)
						{
						if ($first >= 10) {$Fplus="+";}
						else {$Fplus='';}
						$TOPsorted_output .= "<th> $first$Fplus </th>";
						$first++;
						}
					$TOPsorted_output .= "<th nowrap> SUB-TOTAL </th>
					
					</tr></thead><tbody>\n";
			
					$sts=0;
					$statuses_called_to_print = count($status);
					while ($statuses_called_to_print > $sts) 
						{
						$Pstatus = $status[$sts];
						
							$TOPsorted_output .= "<tr>
								<td nowrap> ".$Pstatus." </td>
								<td nowrap> ".$statuses_list[$Pstatus]." </td>";
				
							$first = $all_called_first;
							while ($first <= $all_called_last)
								{
									
								$called_printed=0;
								$o=0;
								while ($status_called_to_print > $o) 
									{
									if ( ($count_statuses[$o] == "$Pstatus") and ($count_called[$o] == "$first") )
										{
										$called_printed++;
										$TOPsorted_output .= "<td nowrap> ".$count_count[$o]." </td>";
										}
				
									$o++;
									}
								if (!$called_printed) 
									{$TOPsorted_output .= "<td nowrap> 0 </td>";}
								$first++;
								}
							$TOPsorted_output .= "<td nowrap> ".$leads_in_sts[$sts]." </td></tr>\n\n";
							$sts++;
						}
			
					$TOPsorted_output .= "
					</tbody>
					<tfoot><tr class='warning'>
					<th nowrap colspan='2'> Total For <i>".$total_all."</i> </th>";
					$first = $all_called_first;
					while ($first <= $all_called_last)
						{
						/*if (eregi("1$|3$|5$|7$|9$", $first)) {$AB='style="background-color:#FFF;border-top:#D0D0D0 dashed 1px;"';} 
						else{$AB='style="background-color:#FFF;border-top:#D0D0D0 dashed 1px;"';}*/
						if ($all_called_count[$first]) {
							$TOPsorted_output .= "
							<th> $all_called_count[$first] </th>";
						} else {
							$TOPsorted_output .= "
							<td> 0 </td>";
						}
						$first++;
						}
					$TOPsorted_output .= "<th>$leads_in_list</th></tr>\n";
					
					$TOPsorted_output .= "
					</tfoot></table>";
					
					//<br /><small style='color:red;'>NO Selected Campaign</small></center>\n";
					
					$return['TOPsorted_output']		= $TOPsorted_output;
					$return['SUMstatuses']			= $sts;
					
					$apiresults = array("result" => "success", "SUMstatuses" => $sts, "TOPsorted_output" => $TOPsorted_output, "query_list" => $query_list, "queryx" => $queryx);
					
					return $apiresults;
				}
				
				// SALES PER AGENT
				if ($pageTitle == "sales_agent") {
					if($userGroup !== "ADMIN")
					$ul = "AND us.user_group = '$userGroup'";
					else
					$ul = "";
					//$list_ids = "{$this->lang->line("go_all")}";
					//$list_id_query=(isset($list_ids) && $list_ids != "{$this->lang->line("go_all")}") ? "and vlog.list_id IN ('".implode("','",$list_ids)."')" : "";
					if($request == "outbound"){
						// Outbound Sales //
						
						$outbound_query = "SELECT us.full_name AS full_name, us.user AS user, SUM(IF(vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale FROM vicidial_users as us, vicidial_log as vlog, vicidial_list as vl WHERE us.user = vlog.user and vl.phone_number = vlog.phone_number and vl.lead_id = vlog.lead_id and vlog.length_in_sec > '0' and vlog.status in ('$statuses') and date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' and vlog.campaign_id='$campaignID' $ul group by us.full_name";
						$query = mysqli_query($link, $outbound_query) or die(mysqli_error($link));
						
						$TOPsorted_output = "";
						$total_out_sales = "";
						
						/*
						$file_output  = "{$this->lang->line("go_campaign")},$campaignID - ".$resultu->campaign_name."\n";
						$file_output .= "{$this->lang->line("go_date_range_caps")},$fromDate {$this->lang->line("go_to_caps")} $toDate\n\n";
						$file_output .= "{$this->lang->line("go_outbound_sales_an_aid_sc")}";*/
						if ($query) {
							$total_sales=0;
							
							/*
							foreach($query->result() as $row) {
							
								if ($x==1) {
									$bgcolor = "#E0F8E0";
									$x=0;
								} else {
									$bgcolor = "#EFFBEF";
									$x=1;
								}
							*/
							
							while($row = mysqli_fetch_array($query)) {
								//$file_output .= $row['full_name'].",".$row['user'].",".$row['sale']."\n";
								$TOPsorted_output .= "<tr>";
								$TOPsorted_output .= "<td nowrap>".$row['full_name']."</td>";
								$TOPsorted_output .= "<td nowrap>".$row['user']."</td>";
								$TOPsorted_output .= "<td nowrap>".$row['sale']."</td>";
								$TOPsorted_output .= "</tr>";
								$total_out_sales = $total_out_sales+$row['sale'];
								
							}
						}
						/*
						if ($total_out_sales < 1) {
							$file_output .= "{$this->lang->line("go_no_records_found")}";
						} else {
							$file_output .= "{$this->lang->line("go_total")},,$total_out_sales\n\n";
						}*/
					}
					
					if($request == "inbound"){
						// Inbound Sales //
						$inbound_query = "SELECT closer_campaigns FROM vicidial_campaigns WHERE campaign_id='".$campaignID."' ORDER BY campaign_id";
						$query = mysqli_query($link, $inbound_query) or die(mysqli_error($link));
						$row = mysqli_fetch_array($query);
						$closer_camp_array=explode(" ",$row['closer_campaigns']);
						$num=count($closer_camp_array);
					
						$x=0;
						while($x<$num) {
							if ($closer_camp_array[$x]!="-") {
									$closer_campaigns[$x]=$closer_camp_array[$x];
							}
							$x++;
						}
						$campaign_inb_query="vlog.campaign_id IN ('".implode("','",$closer_campaigns)."')";
						
						$query = mysqli_query($link, "SELECT us.full_name AS full_name, us.user AS user, SUM(IF(vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale FROM vicidial_users as us, vicidial_closer_log as vlog, vicidial_list as vl WHERE us.user=vlog.user and vl.phone_number=vlog.phone_number and vl.lead_id=vlog.lead_id and vlog.length_in_sec>'0' and vlog.status in ('$statuses') and date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' and $campaign_inb_query $ul group by us.full_name");
						
						$BOTsorted_output = "";
						$total_in_sales = "";
						
						//$file_output .= "{$this->lang->line("go_inbound_sales_an_ai_sc")}";
						if ($query) {
							$total_sales=0;
							
							//foreach($query->result() as $row) {
							while($row = mysqli_fetch_array($query)){
							
								//$file_output .= $row->full_name.",".$row->user.",".$row->sale."\n";
								$BOTsorted_output .= "<tr>";
								$BOTsorted_output .= "<td nowrap> ".$row['full_name']." </td>";
								$BOTsorted_output .= "<td nowrap> ".$row['user']." </td>";
								$BOTsorted_output .= "<td nowrap> ".$row['sale']." </td>";
								$BOTsorted_output .= "</tr>";
								$total_in_sales = $total_in_sales + $row['sale'];
							}
						}
						/*
						if ($total_in_sales < 1) {
							$file_output .= "{$this->lang->line("go_no_records_found")}";
						} else {
							$file_output .= "{$this->lang->line("go_total")},,$total_in_sales";
						}*/
						
						//$return['TOPsorted_output']		= $TOPsorted_output;
						//$return['BOTsorted_output']		= $BOTsorted_output;
						//$return['TOToutbound']			= $total_out_sales;
						//$return['TOTinbound']			= $total_in_sales;
						//$return['file_output']			= $file_output;
					}
					
					$apiresults = array("TOPsorted_output" => $TOPsorted_output, "BOTsorted_output" => $BOTsorted_output, "TOToutbound" => $total_out_sales, "TOTinbound" => $total_in_sales, "query" => $outbound_query);
					
					return $apiresults;
				}
				
				// SALES TRACKER
				if ($pageTitle == "sales_tracker") {
					//$list_ids = "{$this->lang->line("go_all")}";
					//$list_id_query=(isset($list_ids) && $list_ids != "{$this->lang->line("go_all")}") ? "and vlo.list_id IN ('".implode("','",$list_ids)."')" : "";
					if($userGroup !== "ADMIN")
					$ul = "AND us.user_group = '$userGroup'";
					else
					$ul = "";
					if ($request == 'outbound') {
						$outbound_query = "select distinct(vl.phone_number) as phone_number, vl.lead_id as lead_id, vlo.call_date as call_date,us.full_name as agent, vl.first_name as first_name,vl.last_name as last_name,vl.address1 as address,vl.city as city,vl.state as state, vl.postal_code as postal,vl.email as email,vl.alt_phone as alt_phone,vl.comments as comments,vl.lead_id from vicidial_log as vlo, vicidial_list as vl, vicidial_users as us where us.user=vlo.user and vl.phone_number=vlo.phone_number and vl.lead_id=vlo.lead_id and vlo.length_in_sec > '0' and vlo.status in ('$statuses') and date_format(vlo.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' and vlo.campaign_id='$campaignID' $ul order by vlo.call_date ASC limit 2000";
						$query = mysqli_query($link, $outbound_query);
						$outbound_result = "";
						$sale_num_value = 1;
						while($row = mysqli_fetch_array($query)){
							$sale_num[] = $sale_num_value;
							$outbound_result = $row['phone_number'];
							$call_date[] = $row['call_date'];
							$agent[] = $row['agent'];
							$lead_id[] = $row['lead_id'];
							$phone_number[] = $row['phone_number'];
							$first_name[] = $row['first_name'];
							$last_name[] = $row['last_name'];
							$address[] = $row['address'];
							$city[] = $row['city'];
							$state[] = $row['state'];
							$postal[] = $row['postal'];
							$email[] = $row['email'];
							$alt_phone[] = $row['alt_phone'];
							$comments[] = $row['comments'];
							$sale_num_value++;
						}
						/*
						if ($file_download > 0) {
							$file_output  = "{$this->lang->line("go_campaign")},$campaignID - ".$resultu->campaign_name."\n";
							$file_output .= "{$this->lang->line("go_date_range")},$fromDate {$this->lang->line("go_to_caps")} $toDate\n\n";
							$file_output .= "{$this->lang->line("go_outbound_sales_cdt_a_pn_f_l_a_c_s_p_e_an_c")}";
						
							foreach ($TOPsorted_output as $row) {
								$file_output .=$row->call_date.",".$row->agent.",".$row->phone_number.",".$row->first_name.",".$row->last_name.",".$row->address.",".$row->city.",".$row->state.",".$row->postal.",".$row->email.",".$row->alt_phone.",".$row->comments."\n";
							}
						}*/
					}
				
					if ($request == 'inbound') {
						$query = mysqli_query($link, "SELECT closer_campaigns FROM vicidial_campaigns WHERE campaign_id='$campaignID' ORDER BY campaign_id");
						$row = mysqli_fetch_array($query);
						$closer_camp_array = explode(" ",$row['closer_campaigns']);
						$num = count($closer_camp_array);
					
						$x=0;
						while($x<$num) {
							if ($closer_camp_array[$x]!="-") {
								$closer_campaigns[$x]=$closer_camp_array[$x];
							}
							$x++;
						}
						
						$campaign_inb_query="vlo.campaign_id IN ('".implode("','",$closer_campaigns)."')";
					
						$query = mysqli_query($link, "select distinct(vl.phone_number) as phone_number, vl.lead_id as lead_id, vlo.call_date as call_date,us.full_name as agent, 	vl.first_name as first_name,vl.last_name as last_name,vl.address1 as address,vl.city as city,vl.state as state, vl.postal_code as postal,vl.email as email,vl.alt_phone as alt_phone,vl.comments as comments,vl.lead_id from vicidial_closer_log as vlo, vicidial_list as vl, vicidial_users as us where us.user=vl.user and vl.phone_number=vlo.phone_number and vl.lead_id=vlo.lead_id and vlo.length_in_sec > '0' and date_format(vlo.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' and $campaign_inb_query and vlo.status in ('$statuses') $ul order by vlo.call_date ASC limit 2000");
						$inbound_result = "";
						$sale_num_value = 1;
						while($row = mysqli_fetch_array($query)){
							$sale_num[] = $sale_num_value;
							$inbound_result = $row['phone_number'];
							$call_date[] = $row['call_date'];
							$agent[] = $row['agent'];
							$lead_id[] = $row['lead_id'];
							$phone_number[] = $row['phone_number'];
							$first_name[] = $row['first_name'];
							$last_name[] = $row['last_name'];
							$address[] = $row['address'];
							$city[] = $row['city'];
							$state[] = $row['state'];
							$postal[] = $row['postal'];
							$email[] = $row['email'];
							$alt_phone[] = $row['alt_phone'];
							$comments[] = $row['comments'];
							$sale_num_value++;
						}
						/*
						if ($file_download > 0) {
							$file_output  = "{$this->lang->line("go_campaign")},$campaignID - ".$resultu->campaign_name."\n";
							$file_output .= "{$this->lang->line("go_date_range_caps")},$fromDate {$this->lang->line("go_to_caps")} $toDate\n\n";
							$file_output .= "{$this->lang->line("go_outbound_sales_cdt_a_pn_f_l_a_c_s_p_e_an_c")}";
							
							foreach ($TOPsorted_output as $row) {
								$file_output .=$row->call_date.",".$row->agent.",".$row->phone_number.",".$row->first_name.",".$row->last_name.",".$row->address.",".$row->city.",".$row->state.",".$row->postal.",".$row->email.",".$row->alt_phone.",".$row->comments."\n";
							}
						}*/
					}
					
					//$return['TOPsorted_output']		= $TOPsorted_output;
					//$return['file_output']			= $file_output;
					$apiresults = array("outbound_result" => $outbound_result, "inbound_result" => $inbound_result, "sale_num" => $sale_num, "call_date" => $call_date, "agent" => $agent, "phone_number" => $phone_number, "lead_id" => $lead_id, "first_name" => $first_name, "last_name" => $last_name, "address" => $address, "city" => $city, "state" => $state, "postal" => $postal, "email" => $email, "alt_phone" => $alt_phone, "comments" => $comments,"query" => $outbound_query);
					
					return $apiresults;
				}
				
				// INBOUND CALL REPORT
				if ($pageTitle == "inbound_report") {
					
					if($dispo_stats != NULL){
						$ul = " AND status = '$dispo_stats' ";
					}else{
						$ul = "";
					}
					
					$inbound_report_query = "SELECT * FROM vicidial_closer_log WHERE campaign_id = '$campaignID' $ul AND date_format(call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'";
					$query = mysqli_query($link, $inbound_report_query);
					$TOPsorted_output = "";
					$number = 1;
					while($row = mysqli_fetch_array($query)){
						$TOPsorted_output[] .= '<tr>';
						$TOPsorted_output[] .= '<td nowrap>'.$number.'</td>';
						
						$date = strtotime($row['call_date']);
						$date = date("Y-m-d", $date);
						$TOPsorted_output[] .= '<td nowrap>'.$date.'</td>';
						
						$TOPsorted_output[] .= '<td nowrap>'.$row['user'].'</td>';
						$TOPsorted_output[] .= '<td nowrap>'.$row['phone_number'].'</td>';
						
						//$time = strtotime($row['call_date']);
						$time = $row['end_epoch'] + $row['start_epoch'];
						$time = date("h:i:s", $time);
						$TOPsorted_output[] .= '<td nowrap>'.$time.'</td>';
						
						$TOPsorted_output[] .= '<td nowrap style="padding-left:40px;">'.$row['length_in_sec'].'</td>';
						
						$TOPsorted_output[] .= '<td nowrap>'.$row['status'].'</td>';
						$TOPsorted_output[] .= '</tr>';
						$number++;
					}
					/*
					if ($file_download > 0) {
						$file_output  = "{$this->lang->line("go_inbound_camp")},$campaignID - ".$resultu->campaign_name."\n";
						$file_output .= "{$this->lang->line("go_date_range_caps")},$fromDate {$this->lang->line("go_to_caps")} $toDate\n\n";
						$file_output .= "{$this->lang->line("go_date_aid_pn_t_cd_d")}";
						
						foreach ($TOPsorted_output as $row) {
							list($ldate, $ltime) = split(' ',$row->call_date);
							$phone_number = ($row->phone_number != "") ? $row->phone_number : "{$this->lang->line("go_not_registered")}";
							
							$file_output .= "$ldate,".$row->user.",$phone_number,$ltime,".$row->length_in_sec.",".$row->status."\n";
						}
					}
					
					$return['TOPsorted_output']		= $TOPsorted_output;
					$return['file_output']			= $file_output;
					*/
					
					
					$apiresults = array("TOPsorted_output" => $TOPsorted_output, "query" => $inbound_report_query);
					return $apiresults;
				}
				
				/* CALL EXPORT REPORT 
				if ($pageTitle == "call_export_report") {
					//$return['allowed_campaigns']	= $this->go_getall_allowed_campaigns();
					//$groupId = go_get_groupid($userID);
					$groupId = $userGroup;
					if (!checkIfTenant($groupId)) {
					  $ul = '';
					  $user_group_SQL = '';
					} else {
					  $ul = "WHERE user_group='$userGroup'";
					  $user_group_SQL = "and (CASE WHEN vl.user!='VDAD' THEN vl.user_group = '$userGroup' ELSE 1=1 END)";
					}
					
					$query = mysqli_query($link, "SELECT campaign_id FROM vicidial_campaigns $ul");
					while($row = mysqli_fetch_array($query)){
						$allowed_campaigns[] = $row['campaign_id'];
					}
					
					$return['allowed_campaigns']	= implode(",",$allowed_campaigns);
					$return['inbound_groups']		= get_inbound_groups($serID, $link, $userGroup);
					
					$filterSQL = ($this->commonhelper->checkIfTenant($groupId)) ? "WHERE campaign_id IN ('".implode("','",$allowed_campaigns)."')" : "";
					$query = mysqli_query($link, "SELECT list_id FROM vicidial_lists $filterSQL");
					$return['lists_to_print']		= $query->result();
	
					$query = mysqli_query($link, "select status,status_name from vicidial_statuses union select status,status_name from vicidial_campaign_statuses $filterSQL");
					$return['statuses_to_print'] = $query->result();
					
					$query = mysqli_query($link, "select custom_fields_enabled from system_settings");
					$custom_fields_enabled = $query->row();
					
					if (strlen($campaignID) > 4) {
						//$query = mysqli_query($link, "");
						list($header_row, $rec_fields, $custom_fields, $call_notes, $export_fields) = explode(",",$request);
						list($campaign, $group, $list_id, $status) = split(",", $campaignID);
						$campaign = explode("+",eregi_replace("\+$",'',$campaign));
						$group = explode("+",eregi_replace("\+$",'',$group));
						$list_id = explode("+",eregi_replace("\+$",'',$list_id));
						$status = explode("+",eregi_replace("\+$",'',$status));
						
						$campaign_ct = count($campaign);
						$group_ct = count($group);
						$user_group_ct = count($group);
						$list_ct = count($list_id);
						$status_ct = count($status);
						$campaign_string='|';
						$group_string='|';
						$user_group_string='|';
						$list_string='|';
						$status_string='|';
						$outbound_calls=0;
						$export_rows='';
					
						$i=0;
						while($i < $campaign_ct)
							{
							   if (strlen($campaign[$i]) > 0) {
								  $campaign_string .= "$campaign[$i]|";
								  $campaign_SQL .= "'$campaign[$i]',";
							   }
							$i++;
							}
						if ( (ereg("--{$this->lang->line("go_none")}--",$campaign_string) ) or (strlen($campaign_SQL) < 1) )
							{
							//$campaign_SQL = "campaign_id IN('')";
							$campaign_SQL = "";
							$RUNcampaign=1;
							}
						else
							{
							$campaign_SQL = eregi_replace(",$",'',$campaign_SQL);
							$campaign_SQL = "and vl.campaign_id IN($campaign_SQL)";
							$RUNcampaign++;
							}
					
						$i=0;
						while($i < $group_ct)
							{
							   if (strlen($group[$i]) > 0) {
								  $group_string .= "$group[$i]|";
								  $group_SQL .= "'$group[$i]',";
							   }
							$i++;
							}
						if ( (ereg("--{$this->lang->line("go_none")}--",$group_string) ) or ($group_ct < 1) )
							{
							//$group_SQL = "campaign_id IN('')";
							$group_SQL = "";
							$RUNgroup=0;
							}
						else
							{
							$group_SQL = eregi_replace(",$",'',$group_SQL);
							if($group_SQL!=NULL){
													$group_SQL = "and vl.campaign_id IN($group_SQL)";
							}
							else {
							$group_SQL = "and vl.campaign_id IN('$group_SQL')";
							}
							$RUNgroup++;
							}
							
						//$user_group_SQL = "and vl.user_group = '".$return['groupId']."'";
						//$user_group_SQL = '';
						
						$i=0;
						while($i < $list_ct)
							{
							$list_string .= "$list_id[$i]|";
							$list_SQL .= "'$list_id[$i]',";
							$i++;
							}
						if ( (ereg("--{$this->lang->line("go_all")}--",$list_string) ) or ($list_ct < 1) )
							{
							$list_SQL = "";
							}
						else
							{
							$list_SQL = eregi_replace(",$",'',$list_SQL);
							$list_SQL = "and vi.list_id IN($list_SQL)";
							}
					
						$i=0;
						while($i < $status_ct)
							{
							$status_string .= "$status[$i]|";
							$status_SQL .= "'$status[$i]',";
							$i++;
							}
						if ( (ereg("--{$this->lang->line("go_all")}--",$status_string) ) or ($status_ct < 1) )
							{
							$status_SQL = "";
							}
						else
							{
							$status_SQL = eregi_replace(",$",'',$status_SQL);
							$status_SQL = "and vl.status IN ($status_SQL)";
							}
						
						if ($export_fields == "{$this->lang->line("go_extended")}")
							{
							$export_fields_SQL = ",entry_date,vi.called_count,last_local_call_time,modify_date,called_since_last_reset";
							$EFheader = ",entry_date,called_count,last_local_call_time,modify_date,called_since_last_reset";
							}
		
						$k=1;
						if ($RUNcampaign > 0)
							{
							$query = mysqli_query($link, "SELECT vl.call_date,vl.phone_number,vl.status,vl.user,vu.full_name,vl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.phone_number,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vl.length_in_sec,vl.user_group,vl.alt_dial,vi.rank,vi.owner,vi.lead_id,vl.uniqueid,vi.entry_list_id$export_fields_SQL from vicidial_users vu,vicidial_log vl,vicidial_list vi where date_format(vl.call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and (CASE WHEN vl.user!='VDAD' THEN vu.user=vl.user ELSE vl.user='VDAD' END) and vi.lead_id=vl.lead_id $list_SQL $campaign_SQL $user_group_SQL $status_SQL group by vl.call_date order by vl.call_date limit 100000");
							$outbound_to_print = $query->num_rows();
							if ($outbound_to_print < 1)
								{
								$err_nooutbcalls = "{$this->lang->line("go_no_outbound_calls")}";
					// 			exit;
								}
							else
								{
								foreach ($query->result_array() as $row)
									{
									$row['comments'] = preg_replace("/\n|\r/",'!N',$row['comments']);
					
									$export_status[$k] =		$row['status'];
									$export_list_id[$k] =		$row['list_id'];
									$export_lead_id[$k] =		$row['lead_id'];
									$export_uniqueid[$k] =		$row['uniqueid'];
									$export_vicidial_id[$k] =	$row['uniqueid'];
									$export_entry_list_id[$k] =	$row['entry_list_id'];
									$export_fieldsDATA='';
									if ($export_fields == "{$this->lang->line("go_extended")}")
										{$export_fieldsDATA = $row['entry_date'].",".$row['called_count'].",".$row['last_local_call_time'].",".$row['modify_date'].",".$row['called_since_last_reset'].",";}
									$export_rows[$k] = $row['call_date'].",".$row['phone_number'].",".$row['status'].",".$row['user'].",\"".$row['full_name']."\",".$row['campaign_id'].",\"".$row['vendor_lead_code']."\",".$row['source_id'].",".$row['list_id'].",".$row['gmt_offset_now'].",\"".$row['phone_code']."\",\"".$row['phone_number']."\",\"".$row['title']."\",\"".$row['first_name']."\",\"".$row['middle_initial']."\",\"".$row['last_name']."\",\"".$row['address1']."\",\"".$row['address2']."\",\"".$row['address3']."\",\"".$row['city']."\",\"".$row['state']."\",\"".$row['province']."\",\"".$row['postal_code']."\",\"".$row['country_code']."\",\"".$row['gender']."\",\"".$row['date_of_birth']."\",\"".$row['alt_phone']."\",\"".$row['email']."\",\"".$row['security_phrase']."\",\"".$row['comments']."\",".$row['length_in_sec'].",\"".$row['user_group']."\",\"".$row['alt_dial']."\",\"".$row['rank']."\",\"".$row['owner']."\",".$row['lead_id'].",$export_fieldsDATA";
									$k++;
									$outbound_calls++;
									}
								}
							}
							
						if ($header_row=="{$this->lang->line("go_yes")}")
							{
							$RFheader = '';
							$NFheader = '';
							$CFheader = '';
							$EXheader = '';
							if ($rec_fields=="{$this->lang->line("go_id")}")
								{$RFheader = ",recording_id";}
							if ($rec_fields=="{$this->lang->line("go_filename")}")
								{$RFheader = ",recording_filename";}
							if ($rec_fields=="{$this->lang->line("go_location")}")
								{$RFheader = ",recording_location";}
							if ($rec_fields=="{$this->lang->line("go_all")}")
								{$RFheader = ",recording_id,recording_filename,recording_location";}
							if ($export_fields=="{$this->lang->line("go_extended")}")
								{$EXheader = ",uniqueid,caller_code,server_ip,hangup_cause,dialstatus,channel,dial_time,answered_time,cpd_result";}
							if ($call_notes=="{$this->lang->line("go_yes")}")
								{$NFheader = ",call_notes";}
							//if ( ($custom_fields_enabled > 0) and ($custom_fields=='YES') )
							//	{$CFheader = ",custom_fields";}
							if ( ($custom_fields_enabled > 0) and ($custom_fields=="{$this->lang->line("go_yes")}") )
							   {
								  $x = 1;
								  while ($k > $x) {
								 $CF_list_id = $export_list_id[$x];
								 if ($export_entry_list_id[$x] > 99)
									 {$CF_list_id = $export_entry_list_id[$x];}
								 $stmt="SHOW TABLES LIKE \"custom_$CF_list_id\";";
								 $query=mysqli_query($link, $stmt);
								 $tablecount_to_print = $query->num_rows();
								 if ($tablecount_to_print > 0) 
									{
									$stmt = "describe custom_$CF_list_id;";
									$query=mysqli_query($link, $stmt);
									foreach ($query->result() as $row)
										   {
										   if ($row->Field != "lead_id" && !in_array($row->Field,explode(",",$CFheader))) {
											  $CFheader .= ",".$row->Field;
											  $CFdata[$row->Field] = '';
										   }
										   }
									}
								 $x++;
								  }
							   }
				
							$export_rows[0] = "call_date,phone_number,status,user,full_name,campaign_id,vendor_lead_code,source_id,list_id,gmt_offset_now,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,length_in_sec,user_group,alt_dial,rank,owner,lead_id$EFheader,list_name,list_description,status_name$RFheader$EXheader$NFheader$CFheader";
							}
							
						  if ($RUNgroup > 0)
							{
							$query = mysqli_query($link, "SELECT vl.call_date,vl.phone_number,vl.status,vl.user,vu.full_name,vl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.phone_number,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vl.length_in_sec,vl.user_group,vl.queue_seconds,vi.rank,vi.owner,vi.lead_id,vl.closecallid,vi.entry_list_id,vl.uniqueid$export_fields_SQL from vicidial_users vu,vicidial_closer_log vl,vicidial_list vi where date_format(vl.call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and vu.user=vl.user and vi.lead_id=vl.lead_id $list_SQL $group_SQL $user_group_SQL $status_SQL order by vl.call_date limit 100000");
							$inbound_to_print = $query->num_rows();
							if ( ($inbound_to_print < 1) and ($outbound_calls < 1) )
								{
								$err_noinbcalls = "{$this->lang->line("go_no_outbound_calls")}";
					// 			exit;
								}
							else
								{
								foreach ($query->result_array() as $row)
									{
									$row['comments'] = preg_replace("/\n|\r/",'!N',$row['comments']);
					
									$export_status[$k] =		$row['status'];
									$export_list_id[$k] =		$row['list_id'];
									$export_lead_id[$k] =		$row['lead_id'];
									$export_vicidial_id[$k] =	$row['closecallid'];
									$export_entry_list_id[$k] =	$row['entry_list_id'];
									$export_uniqueid[$k] =		$row['uniqueid'];
									$export_fieldsDATA='';
									if ($export_fields == "{$this->lang->line("go_extended")}")
										{$export_fieldsDATA = $row['entry_date'].",".$row['called_count'].",".$row['last_local_call_time'].",".$row['modify_date'].",".$row['called_since_last_reset'].",";}
									$export_rows[$k] = $row['call_date'].",\"".$row['phone_number']."\",\"".$row['status']."\",\"".$row['user']."\",\"".$row['full_name']."\",".$row['campaign_id'].",\"".$row['vendor_lead_code']."\",\"".$row['source_id']."\",".$row['list_id'].",".$row['gmt_offset_now'].",\"".$row['phone_code']."\",\"".$row['phone_number']."\",\"".$row['title']."\",\"".$row['first_name']."\",\"".$row['middle_initial']."\",\"".$row['last_name']."\",\"".$row['address1']."\",\"".$row['address2']."\",\"".$row['address3']."\",\"".$row['city']."\",\"".$row['state']."\",\"".$row['province']."\",\"".$row['postal_code']."\",\"".$row['country_code']."\",\"".$row['gender']."\",\"".$row['date_of_birth']."\",\"".$row['alt_phone']."\",\"".$row['email']."\",\"".$row['security_phrase']."\",\"".$row['comments']."\",".$row['length_in_sec'].",\"".$row['user_group']."\",".$row['queue_seconds'].",\"".$row['rank']."\",\"".$row['owner']."\",".$row['lead_id'].",$export_fieldsDATA";
									$k++;
									}
								}
							}
							
						$i=0;
						while ($k > $i)
							{
							$custom_data='';
							$ex_list_name='';
							$ex_list_description='';
							$query = mysqli_query($link, "SELECT list_name,list_description FROM vicidial_lists where list_id='$export_list_id[$i]'");
							$ex_list_ct = $query->num_rows();
							if ($ex_list_ct > 0)
								{
								$row = $query->row();
								$ex_list_name =			$row->list_name;
								$ex_list_description =	$row->list_description;
								}
				
							$ex_status_name='';
							$query = mysqli_query($link, "SELECT status_name FROM vicidial_statuses where status='$export_status[$i]'");
							$ex_list_ct = $query->num_rows();
							if ($ex_list_ct > 0)
								{
								$row = $query->row();
								$ex_status_name =			$row->status_name;
								}
							else
								{
								$query = mysqli_query($link, "SELECT status_name FROM vicidial_campaign_statuses where status='$export_status[$i]'");
								$ex_list_ct = $query->num_rows();
								if ($ex_list_ct > 0)
									{
									$row = $query->row();
									$ex_status_name =			$row->status_name;
									}
								}
				
							$rec_data='';
							if ( (($rec_fields=="{$this->lang->line("go_id")}") or ($rec_fields=="{$this->lang->line("go_filename")}") or ($rec_fields=="{$this->lang->line("go_location")}") or ($rec_fields=="{$this->lang->line("go_all")}")) && $i > 0 )
								{
								$rec_id='';
								$rec_filename='';
								$rec_location='';
								$query = mysqli_query($link, "SELECT recording_id,filename,location from recording_log where vicidial_id='$export_vicidial_id[$i]' order by recording_id desc LIMIT 10");
								$recordings_ct = $query->num_rows();
								$u=0;
								while ($recordings_ct > $u)
									{
									$row = $query->row();
									$rec_id .=			$row->recording_id;
									$rec_filename .=	$row->filename;
									$rec_location .=	$row->location;
				
									$u++;
									}
								//$rec_id = preg_replace("/.$/",'',$rec_id);
								//$rec_filename = preg_replace("/.$/",'',$rec_filename);
								//$rec_location = preg_replace("/.$/",'',$rec_location);
								if ($rec_fields=="{$this->lang->line("go_id")}")
									{$rec_data = ",$rec_id";}
								if ($rec_fields=="{$this->lang->line("go_filename")}")
									{$rec_data = ",$rec_filename";}
								if ($rec_fields=="{$this->lang->line("go_location")}")
									{$rec_data = ",$rec_location";}
								if ($rec_fields=="{$this->lang->line("go_all")}")
									{$rec_data = ",$rec_id,\"$rec_filename\",\"$rec_location\"";}
								}
				
							$extended_data_a='';
							$extended_data_b='';
							$extended_data_c='';
							if ($export_fields=="{$this->lang->line("go_extended")}")
								{
								$extended_data = ",$export_uniqueid[$i]";
								if (strlen($export_uniqueid[$i]) > 0)
									{
									$uniqueidTEST = $export_uniqueid[$i];
									$uniqueidTEST = preg_replace('/\..*$/','',$uniqueidTEST);
									$query = mysqli_query($link, "SELECT caller_code,server_ip from vicidial_log_extended where uniqueid LIKE \"$uniqueidTEST%\" and lead_id='$export_lead_id[$i]' LIMIT 1");
									$vle_ct = $query->num_rows();
									if ($vle_ct > 0)
										{
										$row=$query->row();
										$extended_data_a =	",".$row->caller_code.",".$row->server_ip;
										$export_call_id[$i] = $row->caller_code;
										}
				
									$query = mysqli_query($link, "SELECT hangup_cause,dialstatus,channel,dial_time,answered_time from vicidial_carrier_log where uniqueid LIKE \"$uniqueidTEST%\" and lead_id='$export_lead_id[$i]' LIMIT 1");
									$vcarl_ct = $query->num_rows();
									if ($vcarl_ct > 0)
										{
										$row=$query->row();
										$extended_data_b =	",\"".$row->hangup_cause."\",\"".$row->dialstatus."\",\"".$row->channel."\",\"".$row->dial_time."\",\"".$row->answered_time."\"";
										}
				
									$query = mysqli_query($link, "SELECT result from vicidial_cpd_log where callerid='$export_call_id[$i]' LIMIT 1");
									$vcpdl_ct = $query->num_rows();
									if ($vcpdl_ct > 0)
										{
										$row=$query->row();
										$extended_data_c =	",\"".$row->result."\"";
										}
				
									}
								if (strlen($extended_data_a)<1)
									{$extended_data_a =	",,";}
								if (strlen($extended_data_b)<1)
									{$extended_data_b =	",,,,,";}
								if (strlen($extended_data_c)<1)
									{$extended_data_c =	",";}
								$extended_data .= "$extended_data_a$extended_data_b$extended_data_c";
								}
				
							$notes_data='';
							if ($call_notes=="{$this->lang->line("go_yes")}")
								{
								if (strlen($export_vicidial_id[$i]) > 0)
									{
									$query = mysqli_query($link, "SELECT call_notes from vicidial_call_notes where vicidial_id='$export_vicidial_id[$i]' LIMIT 1");
									$notes_ct = $query->num_rows();
									if ($notes_ct > 0)
										{
										$row=$query->row;
										$notes_data =	$row->call_notes;
										}
									$notes_data = preg_replace("/\r\n/",' ',$notes_data);
									$notes_data = preg_replace("/\n/",' ',$notes_data);
									}
								$notes_data =	",\"$notes_data\"";
								}
				
							if ( ($custom_fields_enabled > 0) and ($custom_fields=="{$this->lang->line("go_yes")}") )
								{
								$CF_list_id = $export_list_id[$i];
								if ($export_entry_list_id[$i] > 99)
									{$CF_list_id = $export_entry_list_id[$i];}
								$query = mysqli_query($link, "SHOW TABLES LIKE \"custom_$CF_list_id\"");
								$tablecount_to_print = $query->num_rows();
								if ($tablecount_to_print > 0) 
									{
									$query = mysqli_query($link, "describe custom_$CF_list_id");
									$columns_ct = $query->num_rows();
									$u=0;
									foreach ($query->result() as $row)
										{
										//$row=$query->row();
										$column[$u] =	$row->Field;
										$u++;
										}
									if ($columns_ct > 1)
										{
										$query = mysqli_query($link, "SELECT * from custom_$CF_list_id where lead_id='$export_lead_id[$i]' limit 1");
										$customfield_ct = $query->num_rows();
										if ($customfield_ct > 0)
											{
											$row=$query->row_array();
											$t=1;
											while ($columns_ct > $t) 
												{
												//$custom_data .= ",\"".$row[$column[$t]]."\"";
													$CFdata[$column[$t]] = $row[$column[$t]];
												$t++;
												}
											}
										}
										$custom_data = ",\"".implode('","',$CFdata)."\"";
									$custom_data = preg_replace("/\r\n/",'!N',$custom_data);
									$custom_data = preg_replace("/\n/",'!N',$custom_data);
										
										$CFdata = array_fill_keys(array_keys($CFdata), '');
									}
								}
	
							if ($i < 1)
							   $file_output .= $export_rows[$i]."\n";
							else
							   $file_output .= $export_rows[$i]."\"$ex_list_name\",\"$ex_list_description\",\"$ex_status_name\"$rec_data$extended_data$notes_data$custom_data\n";
							$i++;
							}
					
					}
					
					$return['custom_fields_enabled']= $custom_fields_enabled;
					$return['file_output']			= $file_output;
				}
				
				/* Dashboard 
				if ($pageTitle=="dashboard") {
				$sub_total = array();
				//list($statuses, $statuses_name, $system_statuses, $campaign_statuses, $statuses_code) = go_get_statuses($campaignID, $link);
				$TOPsorted_output = "";
				
				// and (val.sub_status NOT LIKE 'LOGIN%' OR val.sub_status IS NULL) 
				$query = mysqli_query($link, "select us.user,us.full_name,val.status,count(*) as calls from vicidial_users as us,vicidial_agent_log as val where date_format(val.event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and us.user=val.user and val.status and val.campaign_id='$campaignID' group by us.user,us.full_name,val.status order by us.full_name,us.user,val.status desc limit 500000");
					
					$calls = "";
					$user = "";
					$fullname = "";
					$status = "";
				while($row = mysqli_fetch_array($query)){
					$calls .= $row['calls'];
					$user .= $row['user'];
					$fullname .= $row['full_name'];
					$status .= $row['status'];
				}
				
				$query = mysqli_query($link, "select val.status from vicidial_agent_log as val, vicidial_log as vl where val.status<>'' and date_format(val.event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and val.campaign_id='$campaignID' and val.uniqueid=vl.uniqueid group by val.status limit 500000");
				
				while($row = mysqli_fetch_array($query)){
					$Dstatus[$row['status']] = $row['status'];
					$TOPsorted_output .= "<td nowrap>".$row['status']."</td>";
				}
				
				/*
				foreach ($query->result() as $i => $row)
				{
					$Dstatus[$row->status] = $row->status;
					$TOPsorted_output .= "<td nowrap style=\"text-transform:uppercase;\"><div align=\"center\" class=\"style4\">&nbsp;".$row->status."</td>";
				}-=-
				
				//$TOPsorted_output .= "<td nowrap>{$this->lang->line("go_sub_total_caps")}&nbsp;</strong></td></tr>";
	
				if (count($agent) > 0) {
					$query = mysqli_query($link, "select lower(us.user) as user,us.full_name from vicidial_users as us, vicidial_agent_log as val where date_format(val.event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and lower(us.user)=lower(val.user) and val.campaign_id='$campaignID' group by us.user limit 500000");
					
					while($user_info = mysqli_fetch_array($query)) {
	
						$TOPsorted_output .= "<tr><td nowrap> ".$user_info['full_name']." </td>";
						
						$t = 0;
						$call_cnt = "";
						for($i = 0; $i < count($Dstatus); $i++) {
							if($agent[$user_info['user']][$Dstatus[$i]] > 0){
								$call_cnt = $agent[$user_info['user']][$Dstatus[$i]] : 0;
							}else{
								$call_cnt = 0;
							}
							$call_cnt = ($agent[$user_info['user']][$Dstatus[$i]] > 0) ? $agent[$user_info['user']][$Dstatus[$i]] : 0;
							$TOPsorted_output .= "<td nowrap> ".$call_cnt." </td>";
							$sub_total[$user_info['user']] = $sub_total[$user_info['user']] + $agent[$user_info['user']][$Dstatus[$i]];
						}
	
						$TOPsorted_output .= "<td nowrap> ".$sub_total[$user_info['user']]." </td></tr>";
						$total_all = $total_all + $sub_total[$user_info['user']];
					}
				}
				
	// 			$query = mysqli_query($link, "select val.status from vicidial_agent_log as val, vicidial_log as vl where val.status<>'' and date_format(val.event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and val.campaign_id='$campaignID' and val.uniqueid=vl.uniqueid group by val.status limit 500000");
	// 			foreach ($query->result() as $row)
	// 			{
	// 				if ($c == 1) {
	// 					$bgcolor = "#EFFBEF";
	// 					$c = 0;
	// 				} else {
	// 					$bgcolor = "#E0F8E0";
	// 					$c = 1;
	// 				}
	// 				
	// 				$TOPsorted_output .= "<tr><td nowrap style=\"border-top:#D0D0D0 dashed 1px;text-transform:uppercase;\"><div align=\"center\" class=\"style4\">&nbsp;".$statuses_name[$row->status]." (".$row->status.")</td>";
	// 				
	// 				foreach ($agent as $o => $user)
	// 				{
	// 					$TOPsorted_output .= "<td nowrap style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style4\">&nbsp;".$user[$row->status]."</td>";
	// 					$sub_total[$o][$row->status] = $sub_total[$o][$row->status] + $user[$row->status];
	// 				}
	// 			}

	//			$TOPsorted_output .= "<tr><td nowrap style=\"border-top:#D0D0D0 dashed 1px;\" colspan=\"".(1+$t)."\"><div align=\"right\" class=\"style3\"><strong>&nbsp;TOTAL:&nbsp;</strong></td><td style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style3\"><strong>&nbsp;$total_all&nbsp;</strong></td></tr>";
				
				if (count($system_statuses) > 0)
				{
					$statuses_codes = implode("','", $system_statuses);
				}
				
				if (count($campaign_statuses) > 0)
				{
					$statuses_codes .= implode("','", $campaign_statuses);
				}
				
				// TOTAL CALLS ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = mysqli_query($link, "SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND status<>''");
				$total_calls = mysqli_num_rows($query);
				
				// TOTAL CONTACTS ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = mysqli_query($link, "SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND status IN ('$statuses_codes')");
				$total_contacts = mysqli_num_rows($query);
			
				// TOTAL NON-CONTACTS ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = mysqli_query($link, "SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND status NOT IN ('$statuses_codes')");
				$total_noncontacts = mysqli_num_rows($query);
	
				// TOTAL SALES ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = mysqli_query($link, "SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND status IN ('$statuses','XFER')");
				$total_sales = mysqli_num_rows($query);
			
				// TOTAL XFER ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = mysqli_query($link, "SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND status='XFER'");
				$total_xfer = mysqli_num_rows($query);
			
				// TOTAL NOT INTERESTED ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = mysqli_query($link, "SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND status='NI'");
				$total_notinterested = mysqli_num_rows($query);
			
				// TOTAL CALLBACKS ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = mysqli_query($link, "SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND status='CALLBK'");
				$total_callbacks = mysqli_num_rows($query);
				
				$query = mysqli_query($link, "select sum(talk_sec) talk_sec,sum(pause_sec) pause_sec,sum(wait_sec) wait_sec,sum(dispo_sec) dispo_sec,sum(dead_sec) dead_sec from vicidial_users,vicidial_agent_log where date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' and vicidial_users.user=vicidial_agent_log.user and $dac_agents_query2 talk_sec<36000 and wait_sec<36000 and talk_sec<36000 and dispo_sec<36000 and campaign_id='$campaignID' limit 500000");
				$total_hours= mysqli_fetch_array($query);
				
				$total_talk_hours = $total_hours['talk_sec'];
				$total_pause_hours = $total_hours['pause_sec'];
				$total_wait_hours = $total_hours['wait_sec'];
				$total_dispo_hours = $total_hours['dispo_sec'];
				$total_dead_hours = $total_hours['dead_sec'];
				$total_login_hours = ($total_hours['talk_sec'] + $total_hours['pause_sec'] + $total_hours['wait_sec'] + $total_hours['dispo_sec'] + $total_hours['dead_sec']);
				
				
				$inbound_campaigns = get_group_id($userID, $link);
				foreach ($inbound_campaigns as $i => $item)
				{
					$inb_camp[$i] = $item->group_id;
				}
	
				if (count($inb_camp)>0)
					$inbCamp = implode("','",$inb_camp);
				
				$total_dialer_calls=0;
// 				$total_dialer_calls_output[]='';
				$isGraph = false;
				$c=0;
				for($i = 0; $i < count($statuses_code); $i++) {
					$code = $statuses_code[$i];
					$query = mysqli_query($link, "select count(*) as cnt from vicidial_log where campaign_id='$campaignID' and length_in_sec>'0' and status='$code' and date_format(call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");
					$fetch_query = mysqli_fetch_array($query);
					$row_out[$code]=$fetch_query['cnt'];
					
					$query = mysqli_query($link, "select count(*) as cnt from vicidial_closer_log where campaign_id IN ('$inbCamp') and length_in_sec>'0' and status='$code' and date_format(call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");
					$fetch_query = mysqli_fetch_array($query);
					$row_in[$code]=$fetch_query['cnt'];
	//				var_dump("select * from vicidial_log where campaign_id='$campaignID' and length_in_sec>'0' and status='$code' and date_format(call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");
					$subtotal[$code]=$row_out[$code]+$row_in[$code];
			
					if ($subtotal[$code]>0) {
						
						if (!$isGraph){
							$total_dialer_calls_output .= '<tr>
								<td> '.$code.' </td>
								<td> '.$statuses_name[$code].' </td>
								<td> '.$subtotal[$code].' </td>
							</tr>';
						} else {
							$total_dialer_calls_output[$code] = $subtotal[$code];
						}
					}
			
					$total_dialer_calls=$total_dialer_calls+$subtotal[$code];
				}
// 				$total_dialer_calls_output = json_encode($total_dialer_calls_output);
				
				// Graph
				for ($i =0; $i < count($statuses); $i++) {
					$status = $statuses[$i];
					$query = mysqli_query($link, "SELECT count(*) as cnt FROM vicidial_agent_log WHERE campaign_id='$campaignID' and status='$status' and date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)");
					$fetch_query = mysqli_fetch_array($query);
					$SUMstatuses[$status]=$fetch_query['cnt'];
				}
	
				for($x=0;$x<count($statuses);$x++)
				{
					$SstatusesARY[$x] = $statuses_name[$statuses[$x]]." (".$statuses[$x].")";
				}
				
				/*
				$return['TOPsorted_output']	= $TOPsorted_output;
				$return['SstatusesTOP']		= $SstatusesARY;
				$return['SUMstatuses']		= $SUMstatuses;
				$return['total_calls']		= $total_calls;
				$return['total_contacts']	= $total_contacts;
				$return['total_noncontacts']= $total_noncontacts;
				$return['total_sales']		= $total_sales;
				$return['total_xfer']		= $total_xfer;
				$return['total_notinterested']= $total_notinterested;
				$return['total_callbacks']	= $total_callbacks;
				$return['total_talk_hours']	= $total_talk_hours;
				$return['total_pause_hours']= $total_pause_hours;
				$return['total_wait_hours']	= $total_wait_hours;
				$return['total_dispo_hours']= $total_dispo_hours;
				$return['total_dead_hours']	= $total_dead_hours;
				$return['total_login_hours']= $total_login_hours;
				$return['total_dialer_calls_output']= $total_dialer_calls_output;
				$return['total_dialer_calls']= $total_dialer_calls;-=-
				
				$apiresults = array("TOPsorted_output" => $TOPsorted_output, "SstatusesTOP" => $SstatusesARY, "SUMstatuses" => $SUMstatuses, "total_calls" => $total_calls, "total_contacts" => $total_contacts, "total_noncontacts" => $total_noncontacts, "total_sales" => $total_sales, "total_xfer" => $total_xfer, "total_notinterested" => $total_notinterested, "total_callbacks" => $total_callbacks, "total_talk_hours" => $total_talk_hours, "total_pause_hours" => $total_pause_hours, "total_wait_hours" => $total_wait_hours, "total_dispo_hours" => $total_dispo_hours, "total_dead_hours" => $total_dead_hours, "total_login_hours" => $total_login_hours, "total_dialer_calls_output" => $total_dialer_calls_output, "total_dialer_calls" => $total_dialer_calls);
				return $apiresults;
			}
			*/
		}

		//$query = mysqli_query($link, "select status,status_name from vicidial_statuses union select status,status_name from vicidial_campaign_statuses");
		//$return['statuses'] = $query->result();
		
		//return $return;
	}
?>
