<?php
 /**
 * @file 		goGetCallDetail.php
 * @brief 		API to get call details
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Jim Abenoja  <alex@goautodial.com>
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
    $default_date = date("Y-m-d");
	$def_start_date .= $default_date." 00:00:00";
	$def_end_date .= $default_date." 23:59:59";

    // POST or GET Variables
    $user = mysqli_real_escape_string($link, $_REQUEST['user']);
    $start_date = mysqli_real_escape_string($link, $_REQUEST['fromDate']);
    // if(empty($start_date))
    // 	$start_date = $def_start_date;
	$end_date = mysqli_real_escape_string($link, $_REQUEST['toDate']);
	// if(empty($end_date))
	// 	$end_date = $def_end_date;
	$campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
	$list_id = mysqli_real_escape_string($link, $_REQUEST['list_id']);
	$groupId = go_get_groupid($session_user);
	//$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
	$id = mysqli_real_escape_string($link, $_REQUEST['id']);
	$export = mysqli_real_escape_string($link, $_REQUEST['export']);

	$limit = mysqli_real_escape_string($link, $_REQUEST['limit']);
	$sortOrder = mysqli_real_escape_string($link, $_REQUEST['sortOrder']);
	$sortBy = mysqli_real_escape_string($link, $_REQUEST['sortBy']);
	

	if(empty($limit))
    	$limit = 1000;

	if(empty($sortBy)){
		$sortBy = "AfterDispo";
	}else{
		switch($sortBy){
			case "callId";
				$sortBy = "vl.uniqueid";
			break;
			case "leadId";
				$sortBy = "vi.lead_id";
			break;
			case "Phone_code";
				$sortBy = "vl.phone_code";
			break;
			case "Last_name";
				$sortBy = "vi.last_name";
			break;
			case "Phone_number";
				$sortBy = "vl.phone_number";
			break;
			case "CallDuration";
				$sortBy = "vl.length_in_sec";
			break;
			case "agentName";
				$sortBy = "vl.user";
			break;
			case "agentId";
				$sortBy = "vu.user_id";
			break;
			case "CampaignId";
				$sortBy = "vl.campaign_id";
			break;
			case "CampaignName";
				$sortBy = "vc.campaign_name";
			break;
			case "TransactionDate";
				$sortBy = "vl.call_date";
			break;
			case "ResultCode";
				$sortBy = "vl.status";
			break;
			case "isConversion";
				$sortBy = "isConversion";
			break;
			case "call_notes";
				$sortBy = "vl.uniqueid";
			break;
		}
	}

	if(empty($sortOrder)){
		$sortOrder = "";
	}
	
	$custom_fields = "Y";
	$rec_location = "N";
	$per_call_notes = "Y";

	$datetime1 = date_create($start_date);
	$datetime2 = date_create($end_date);
	$date_difference = date_diff($datetime1, $datetime2);
	$difference = $date_difference->format("%m");
    
    // Check user_id if its null or empty
    if(empty($session_user) || (empty($start_date) && empty($id))) { 
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
				$ul = "user_group='$groupId' AND";
			else
				$ul = "";
        }


  //       if(!empty($id))
  //       	$id = explode(",",$id);
		// else
		// 	$id = array("ALL");

        if(!empty($user))
        	$user = explode(",",$user);
		else
			$user = array("ALL");
		
		if($campaigns != "")
			$campaigns = explode(",",$campaign_id);
		else
			$campaigns = array("ALL");

		if($lists != "")	
		    $lists = explode(",",$list_id);
		else
			$lists = array("ALL");
		
		if($dispo_stats != "")	
		    $dispo_stats = explode(",",$dispo_stats);
		else
			$dispo_stats = array("ALL");

		$id_SQL = "";
		$campaign_SQL = "";
		$list_SQL = "";
		$status_SQL = "";
		
		$id_ct = count($id);
		$user_ct = count($user);
		$campaign_ct = count($campaigns);
		$list_ct = count($lists);
		$status_ct = count($dispo_stats);
		
		if($user != ""){
			if (in_array("ALL", $user)){
				$user_SQL = "";
			}else{
				$i=0;
				while($i < $user_ct){
					$user_SQL .= "'$user[$i]',";
					$i++;
				}
				
				$user_SQL = preg_replace("/,$/i",'',$user_SQL);
				$user_SQL = "and vu.user IN($user_SQL)";
				$RUNcampaign=$i;
			}
		}

		if($id != ""){
			// if (in_array("ALL", $id)){
			// 	$id_SQL = "";
			// }else{
				// $i=0;
				// while($i < $id_ct){
				// 	$id_SQL .= "'$id[$i]',";
				// 	$i++;
				// }
				
				//$id_SQL = preg_replace("/,$/i",'',$id_SQL);

				$get_end = "SELECT vl.end_epoch, (val.dispo_sec + val.dispo_epoch) AS time_end FROM vicidial_log vl, vicidial_agent_log val WHERE vl.uniqueid = val.uniqueid AND vl.uniqueid = '$id';";
				$query_end = mysqli_query($link, $get_end) or die(mysqli_error($link));
				$fetch_end = mysqli_fetch_array($query_end);
				$end_epoch = $fetch_end['end_epoch'];
				$time_end = $fetch_end['time_end'];

				if(!empty($start_date) || !empty($end_date)){
					if(empty($start_date))
				    	$start_date = $def_start_date;
					if(empty($end_date))
						$end_date = $def_end_date;

					$id_SQL = "(date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";

					if(!empty($time_end))
						$id_SQL .= " and (val.dispo_sec + val.dispo_epoch) > '$time_end'";

				}else{
					if(!empty($time_end))
						$id_SQL = "(val.dispo_sec + val.dispo_epoch) > '$time_end'";
					else{
						if(empty($start_date))
			                $start_date = $def_start_date;
	                    if(empty($end_date))
	                           $end_date = $def_end_date;
	                    $id_SQL = "(date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
					}
				}
					
					
				$RUNcampaign=1;
			//}
		}else{
			if(empty($start_date))
		    	$start_date = $def_start_date;
			if(empty($end_date))
				$end_date = $def_end_date;

			$id_SQL = "(date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
		}

		if($campaigns != ""){
			if (in_array("ALL", $campaigns)){
				$campaign_SQL = "";
				
				$query_campaign = mysqli_query($link,"SELECT campaign_id FROM vicidial_campaigns;");
				while($fetch_campaign = mysqli_fetch_array($query_campaign)){
					$array_campaign[] = $fetch_campaign["campaign_id"];
				}

				//$imploded_campaigns = implode("','", $array_campaign);
				//$campaign_SQL = "and vl.campaign_id IN('$imploded_campaigns')";
				$RUNcampaign=1;
			}else{
				$i=0;
				while($i < $campaign_ct){
					$campaign_SQL .= "'$campaigns[$i]',";
					$i++;
				}
				
				$campaign_SQL = preg_replace("/,$/i",'',$campaign_SQL);
				$campaign_SQL = "and vl.campaign_id IN($campaign_SQL)";
				$RUNcampaign=$i;
			}
		}else{
			$RUNcampaign=0;
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
				
				if(isset($array_campaign) && !empty($array_campaign)){
					$i=0;
					while($i < count($array_campaign)){
						$camp_id = $array_campaign[$i];
						$query_list = mysqli_query($link,"SELECT list_id FROM vicidial_lists WHERE active='Y' AND campaign_id = '$camp_id';");
						while($fetch_list = mysqli_fetch_array($query_list)){
							$array_list[] = $fetch_list["list_id"];
						}
						$i++;
					}
				}else{
					$i=0;
					while($i < $campaign_ct){
						$camp_id = $campaigns[$i];
						$query_list = mysqli_query($link,"SELECT list_id FROM vicidial_lists WHERE active='Y' AND campaign_id = '$camp_id';");
						while($fetch_list = mysqli_fetch_array($query_list)){
							$array_list[] = $fetch_list["list_id"];
						}
						$i++;
					}
				}

			}else{
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
		
		if($rec_location == "Y"){
			$rec_location_fields = ", asteriskV4.re.location as recording_location";
			$rec_location_from = ", recording_log re";
			$rec_location_where = "and re.lead_id=vl.lead_id and vl.uniqueid = re.vicidial_id";
		}else{
			$rec_location_fields = "";
			$rec_location_from = "";
			$rec_location_where = "";
		}
		
		//$user_group_SQL = "AND (CASE WHEN vl.user!='VDAD' THEN vl.user_group = '$userGroup' ELSE 1=1 END)";
		if($groupId !== "ADMIN"){
			$stringv = go_getall_allowed_users($groupId);
			$user_group_SQL = "AND vl.user IN ($stringv)";
		}else{
			$user_group_SQL = "";
		}
		
		$export_fields_SQL = "";
		
		if ($RUNcampaign > 0){
			//$query = "SELECT vl.call_date,vl.phone_number,vl.status,vl.user,vu.full_name,vl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vl.length_in_sec,vl.user_group,vl.alt_dial,vi.rank,vi.owner,vi.lead_id,vl.uniqueid,vi.entry_list_id $export_fields_SQL $rec_location_fields FROM vicidial_users vu, vicidial_log vl,vicidial_list vi $rec_location_from WHERE (date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date') and vu.user=vl.user and vi.lead_id=vl.lead_id $rec_location_where $list_SQL $campaign_SQL $user_group_SQL $status_SQL group by vl.call_date order by vl.call_date ";

			$location_fields = ", gu.location_id as LocationId";
			$location_from = ", users gu, locations gl";
			$location_where = " and vu.user=gu.name and gu.location_id=gl.id";
			$sale_field = ", CASE WHEN vl.status = '60' THEN 1 ELSE 0 END as isConversion";
			//$sale_field = "";
			//$location_fields = "";
			//$location_from = "";
			//$location_where = "";

			$query = "SELECT vl.uniqueid as callId, vi.lead_id as leadId, vl.phone_code as Phone_code, vi.first_name as First_name, vi.last_name as Last_name, vl.phone_number as Phone_number, vi.email as Email, vi.address1 as Address1, vi.city as City, vi.state as State, vi.postal_code as Zip, vl.length_in_sec as CallDuration, vl.user as agentName, vu.user_id as agentId, vl.list_id as ListId, vl.campaign_id as CampaignId, vc.campaign_name as CampaignName $location_fields, vl.call_date as TransactionDate, vl.status as ResultCode, (val.dispo_sec + val.dispo_epoch) as AfterDispo $sale_field $export_fields_SQL $rec_location_fields FROM asteriskV4.vicidial_users vu, asteriskV4.vicidial_log vl, asteriskV4.vicidial_agent_log val, asteriskV4.vicidial_list vi, asteriskV4.vicidial_campaigns vc $location_from $rec_location_from WHERE $id_SQL and val.uniqueid = vl.uniqueid and vu.user=vl.user $location_where and vi.lead_id=vl.lead_id and vl.campaign_id=vc.campaign_id and vl.status NOT IN ('INCALL', 'DISPO') and vl.end_epoch IS NOT NULL $rec_location_where $list_SQL  $user_SQL $campaign_SQL $user_group_SQL $status_SQL group by vl.call_date order by $sortBy $sortOrder LIMIT $limit";
		}else{
			$err_msg = error_handle("40001");
			$apiresults = array("code" => "40001", "result" => $err_msg);
		}
		
		$result = mysqli_query($linkgo, $query) or die(mysqli_error($linkgo));

		//OUTPUT DATA HEADER//
		while ($fieldinfo=mysqli_fetch_field($result))
		{
			$csv_header[] = $fieldinfo->name;
		}
		if($per_call_notes == "Y"){
			array_push($csv_header, "call_notes");
		}

		//OUTPUT CUSTOM FIELDS IN HEADER
		if($custom_fields === "Y") {
		    for($i = 0 ; $i < count($array_list); $i++){
				$list_id = $array_list[$i];
				$query_CF_list = mysqli_query($link, "DESC custom_$list_id;");
				if($query_CF_list){
					$n=0;
					while ($field_list=mysqli_fetch_array($query_CF_list)){
						$exec_query_CF_list = $field_list["Field"];

						if($exec_query_CF_list != "lead_id"){
							$active_list_fields["custom_$list_id"][$n] = $exec_query_CF_list;
							$n++;
						}
					}
				}
			}
			$header_CF = array();
			$keys = array_keys($active_list_fields);
			for($i = 0 ; $i < count($keys); $i++){
				$list_id = $keys[$i];
				for($x=0;$x < count($active_list_fields[$list_id]);$x++){
					$field = $active_list_fields[$list_id][$x];
					if(!in_array($field,$header_CF)){
						$header_CF[] = $field;
					}
				}
				
			}
			$csv_header = array_merge($csv_header,$header_CF);
			//$active_list_fields = array_unique($active_list_fields, SORT_REGULAR);
			//$active_list_fields2 = array_values($active_list_fields);
		}

		//OUTPUT DATA ROW//
		$count_row = 1;
		while($row = mysqli_fetch_row($result)) {

			$lead_id = $row[1];
			$list_id_spec = $row[14];
			// $row[3] = str_replace("\'", "'", $row[3]);
			// $row[4] = str_replace("\'", "'", $row[4]);
			// $row[5] = str_replace("\'", "'", $row[7]);
			// $row[6] = str_replace("\'", "'", $row[8]);

			// $row[3] = str_replace("\\", "", $row[3]);
			// $row[4] = str_replace("\\", "", $row[4]);
			// $row[5] = str_replace("\\", "", $row[7]);
			// $row[6] = str_replace("\\", "", $row[8]);

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

            //OUTPUT CUSTOM FIELDS IN ROW
			if($custom_fields == "Y"){
				$keys = array_keys($active_list_fields); // list of active custom lists
				//var_dump($active_list_fields["custom_104"][0]);
				// var_dump($header_CF);
				// die();
				/* FOR CSV */
				for($i = 0 ; $i < count($keys); $i++){
				    $list_id = $keys[$i];
					//var_dump($active_list_fields[$list_id]);
					$fields = implode(",", $active_list_fields[$list_id]);
					
					if("custom_".$list_id_spec === $list_id){
						$query_row_sql = "SELECT $fields FROM $list_id WHERE lead_id ='$lead_id';";
						$query_CF = mysqli_query($link, $query_row_sql);
						
						//if($query_CF){
							$fetch_CF = mysqli_fetch_array($query_CF);
							
							if($fetch_CF !== NULL){
								//var_dump($fetch_CF);
								for($x=0;$x < count($header_CF);$x++){
									if(!empty($fetch_CF[$header_CF[$x]])){
										$fetch_row[] =  str_replace(",", " | ", $fetch_CF[$header_CF[$x]]);
									}else{
										$fetch_row[] =  "";
									}
								}
							}
							
							//die();
						//}
					}
					

					for($a=0;$a < count($fetch_row);$a++){
						array_push($row, $fetch_row[$a]);
					}
					$queries[] = $row;
					unset($fetch_row);
					unset($fetch_CF);
			    }
			}
			$csv_row[] = $row;
			$count_row++;
		}
		//var_dump($queries);
		$main_row = array();
		//put keys in each row
		for($i=0; $i < count($csv_row); $i++){
			//unset($re_head);
			for($a=0;$a<count($csv_header);$a++){
				//$re_head[] = $csv_header[$a];
				if($csv_header[$a] !== "AfterDispo")
				$re_row[$csv_header[$a]] = str_replace("\\", "", $csv_row[$i][$a]);
			}
			
			array_push($main_row,$re_row);
		}
		//var_dump($main_row);
		//var_dump($query_fields);
		//"query" => $query, "header" => $csv_header, 
		$paging = array("totalElements" => $count_row, "limit" => $limit); 
		

		//var_dump($return);
		if(is_numeric($export) && !empty($export) && $export == 1){
			if($count_row >= 1){
				$filename = "Call_Details_".$start_date."_".$end_date.".csv";
	        	 header('Content-type: application/csv');
	        	 header('Content-Disposition: attachment; filename='.$filename);

	        	echo implode(",",$csv_header)."\n";

	        	$count = 0;
		        for($i=0; $i <= count($csv_row); $i++){
		            $count_row = $csv_row[$i];
		            for($x=0; $x <= count($count_row); $x++){
		                if($x == count($count_row)){
		                    echo $count_row[$x]."\n";
		                }else{
		                    echo $count_row[$x].",";
		                }
						//echo "\n\n";
		            }
		        }
		        //echo $row;
		        //echo $row;
			}else{
				$err_msg = error_handle("40001");
				//"query" => $userlog_query, 
				$apiresults = array("result" => "No records retrieved from: ".$start_date." - ".$end_date);
			}
        }else{
			if($count_row < 1){
				$err_msg = error_handle("40001");
				//"query" => $userlog_query, 
				$apiresults = array("result" => "No records retrieved from: ".$start_date." - ".$end_date);
			}else{
				$apiresults = array("paging" => $paging, "rows" => $main_row);
			}
		}
		
		
		//$log_id = log_action($linkgo, 'VIEW', $user, $ip_address, "Viewed the agent log of Agent: $user", $groupId);
	}
?>
