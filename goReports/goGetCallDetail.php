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
/** @var mysqli $link */
/** @var mysqli $linkgo */
/** @var MySQLiDB $astDB */
/** @var MySQLiDB $goDB */
/** @var string $session_user */

    $default_date = date("Y-m-d");
	$def_start_date = $default_date." 00:00:00";
	$def_end_date = $default_date." 23:59:59";
	$array_campaign = [];
	$array_list = [];
	$active_list_fields = [];
	$csv_header = [];
	$csv_row = [];
	$queries = [];

    // POST or GET Variables
    $user = mysqli_real_escape_string($link, ($_REQUEST['user'] ?? ''));
    $start_date = mysqli_real_escape_string($link, ($_REQUEST['fromDate'] ?? ''));
    // if(empty($start_date))
    // 	$start_date = $def_start_date;
	$end_date = mysqli_real_escape_string($link, ($_REQUEST['toDate'] ?? ''));
	// if(empty($end_date))
	// 	$end_date = $def_end_date;
	$campaign_id = mysqli_real_escape_string($link, ($_REQUEST['campaign_id'] ?? ''));
	$list_id = mysqli_real_escape_string($link, ($_REQUEST['list_id'] ?? ''));
	$groupId = go_get_groupid($session_user, $astDB);
	//$ip_address = mysqli_real_escape_string($link, ($_REQUEST['log_ip'] ?? ''));
	$id = mysqli_real_escape_string($link, ($_REQUEST['id'] ?? ''));
	$export = mysqli_real_escape_string($link, ($_REQUEST['export'] ?? ''));

	$limit = mysqli_real_escape_string($link, ($_REQUEST['limit'] ?? ''));
	$sortOrder = mysqli_real_escape_string($link, ($_REQUEST['sortOrder'] ?? ''));
	$sortBy = mysqli_real_escape_string($link, ($_REQUEST['sortBy'] ?? ''));


	if($limit === '' || $limit === '0')
    	$limit = 1000;

	if($sortBy === '' || $sortBy === '0'){
		$sortBy = "AfterDispo";
	}else{
		switch($sortBy){
			case "callId":
            case "call_notes":
				$sortBy = "vl.uniqueid";
			break;
			case "leadId":
				$sortBy = "vi.lead_id";
			break;
			case "Phone_code":
				$sortBy = "vl.phone_code";
			break;
			case "Last_name":
				$sortBy = "vi.last_name";
			break;
			case "Phone_number":
				$sortBy = "vl.phone_number";
			break;
			case "CallDuration":
				$sortBy = "vl.length_in_sec";
			break;
			case "agentName":
				$sortBy = "vl.user";
			break;
			case "agentId":
				$sortBy = "vu.user_id";
			break;
			case "CampaignId":
				$sortBy = "vl.campaign_id";
			break;
			case "CampaignName":
				$sortBy = "vc.campaign_name";
			break;
			case "TransactionDate":
				$sortBy = "vl.call_date";
			break;
			case "ResultCode":
				$sortBy = "vl.status";
			break;
			case "isConversion":
				$sortBy = "isConversion";
			break;
		}
	}

	if($sortOrder === '' || $sortOrder === '0'){
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
    if(empty($session_user) || (($start_date === '' || $start_date === '0') && ($id === '' || $id === '0'))) {
        $err_msg = error_handle("40001");
		$apiresults = ["code" => "40001", "result" => $err_msg];
    }elseif($difference > 3){
    	$err_msg = error_handle("41004", "date range. The allowed date range is 3 months or less.");
		$apiresults = ["code" => "41004", "result" => $err_msg];
    }elseif(!is_numeric($id) && ($id !== '' && $id !== '0')){
    	$err_msg = error_handle("41002", "id");
		$apiresults = ["code" => "41002", "result" => $err_msg];
    } else{

        if (checkIfTenant($groupId, $goDB)) {
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

        if($user !== '' && $user !== '0')
        	$user = explode(",",$user);
		else
			$user = ["ALL"];

		if($campaigns != "")
			$campaigns = explode(",",$campaign_id);
		else
			$campaigns = ["ALL"];

		if($lists != "")
		    $lists = explode(",",$list_id);
		else
			$lists = ["ALL"];

		if($dispo_stats != "")
		    $dispo_stats = explode(",",$dispo_stats);
		else
			$dispo_stats = ["ALL"];

		$id_SQL = "";
		$campaign_SQL = "";
		$list_SQL = "";
		$status_SQL = "";

		$id_ct = (is_countable($id) ? count($id) : 0);
		$user_ct = (is_countable($user) ? count($user) : 0);
		$campaign_ct = (is_countable($campaigns) ? count($campaigns) : 0);
		$list_ct = (is_countable($lists) ? count($lists) : 0);
		$status_ct = (is_countable($dispo_stats) ? count($dispo_stats) : 0);

		if($user != ""){
			if (in_array("ALL", (is_array($user) ? $user : []))){
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

		if($id !== ""){
			// if (in_array("ALL", (is_array($id) ? $id : []))){
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

				if($start_date !== '' && $start_date !== '0' || $end_date !== '' && $end_date !== '0'){
					if($start_date === '' || $start_date === '0')
				    	$start_date = $def_start_date;
					if($end_date === '' || $end_date === '0')
						$end_date = $def_end_date;

					$id_SQL = "(date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";

					if(!empty($time_end))
						$id_SQL .= " and (val.dispo_sec + val.dispo_epoch) > '$time_end'";

				}else{
					if(!empty($time_end))
						$id_SQL = "(val.dispo_sec + val.dispo_epoch) > '$time_end'";
					else{
						$start_date = $def_start_date;
	                    $end_date = $def_end_date;
	                    $id_SQL = "(date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
					}
				}


				$RUNcampaign=1;
			//}
		}else{
			if($start_date === '0')
		    	$start_date = $def_start_date;
			if($end_date === '' || $end_date === '0')
				$end_date = $def_end_date;

			$id_SQL = "(date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$start_date' AND '$end_date')";
		}

		if($campaigns != ""){
			if (in_array("ALL", (is_array($campaigns) ? $campaigns : []))){
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
			if (in_array("ALL", (is_array($lists) ? $lists : []))){
				$list_SQL = "";

				if(isset($array_campaign) && !empty($array_campaign)){
					$i=0;
					while($i < (is_countable($array_campaign) ? count($array_campaign) : 0)){
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
			if ( in_array("ALL", (is_array($dispo_stats) ? $dispo_stats : [])) || $status_ct < 1 ){
				$status_SQL = "";
			}
			else{
				$status_SQL = preg_replace("/,$/i",'',$status_SQL);
				$status_SQL = "and vl.status IN ($status_SQL)";
			}
		}

		if($rec_location === "Y"){
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
			$apiresults = ["code" => "40001", "result" => $err_msg];
		}

		$result = mysqli_query($linkgo, $query) or die(mysqli_error($linkgo));

		//OUTPUT DATA HEADER//
		while ($fieldinfo=mysqli_fetch_field($result))
		{
			$csv_header[] = $fieldinfo->name;
		}
        $csv_header[] = "call_notes";
        $counter = (is_countable($array_list) ? count($array_list) : 0);
        $active_list_fields = [];
        //OUTPUT CUSTOM FIELDS IN HEADER
        for($i = 0 ; $i < $counter; $i++){
					$list_id = preg_replace('/[^0-9]/', '', (string) $array_list[$i]);
					if ($list_id === '') {
						continue;
					}
					$custom_table = "custom_$list_id";
					$safe_custom_table = mysqli_real_escape_string($link, $custom_table);
					$table_exists = mysqli_query($link, "SHOW TABLES LIKE '$safe_custom_table';");
					if (!$table_exists || mysqli_num_rows($table_exists) < 1) {
						continue;
					}
					$query_CF_list = mysqli_query($link, "DESC $safe_custom_table;");
					if($query_CF_list){
					$n=0;
					while ($field_list=mysqli_fetch_array($query_CF_list)){
						$exec_query_CF_list = $field_list["Field"];

						if($exec_query_CF_list != "lead_id"){
							$active_list_fields[$custom_table][$n] = $exec_query_CF_list;
							$n++;
						}
					}
				}
			}
        $header_CF = [];
        $keys = array_keys($active_list_fields);
        $counter = (is_countable($keys) ? count($keys) : 0);
        for($i = 0 ; $i < $counter; $i++){
				$list_id = $keys[$i];
				for($x=0;$x < count($active_list_fields[$list_id]);$x++){
					$field = $active_list_fields[$list_id][$x];
					if(!in_array($field, (is_array($header_CF) ? $header_CF : []))){
						$header_CF[] = $field;
					}
				}

			}
        $csv_header = array_merge($csv_header,$header_CF);
        //$active_list_fields = array_unique($active_list_fields, SORT_REGULAR);
        //$active_list_fields2 = array_values($active_list_fields);


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
            $query_callnotes = mysqli_query($link, "SELECT call_notes from vicidial_call_notes where lead_id='$lead_id' LIMIT 1;");
            $notes_ct = mysqli_num_rows($query_callnotes);
            if ($notes_ct > 0){
					$fetch_callnotes = mysqli_fetch_array($query_callnotes);
					$notes_data =	$fetch_callnotes["call_notes"];
					$notes_data = rawurldecode((string) $notes_data);
				}else{
					$notes_data = "";
				}
            $row[] = $notes_data;
            //OUTPUT CUSTOM FIELDS IN ROW
            $keys = array_keys($active_list_fields);
            $counter = (is_countable($keys) ? count($keys) : 0);
            // list of active custom lists
            //var_dump($active_list_fields["custom_104"][0]);
            // var_dump($header_CF);
            // die();
            /* FOR CSV */
            for($i = 0 ; $i < $counter; $i++){
				    $list_id = $keys[$i];
					//var_dump($active_list_fields[$list_id]);
					$fields = implode(",", $active_list_fields[$list_id]);

					if("custom_".$list_id_spec === $list_id){
						$safeCustomRowTable = mysqli_real_escape_string($link, (string) $list_id);
						$safeLeadId = mysqli_real_escape_string($link, (string) $lead_id);
						$table_exists = mysqli_query($link, "SHOW TABLES LIKE '$safeCustomRowTable';");
						$query_CF = false;
						if ($table_exists && mysqli_num_rows($table_exists) > 0) {
							$query_row_sql = "SELECT $fields FROM $safeCustomRowTable WHERE lead_id ='$safeLeadId';";
							$query_CF = mysqli_query($link, $query_row_sql);
						}

						//if($query_CF){
							$fetch_CF = $query_CF ? mysqli_fetch_array($query_CF) : null;

							if($fetch_CF !== NULL && $fetch_CF !== false){
								$fetch_row = [];
								//var_dump($fetch_CF);
								for($x=0;$x < (is_countable($header_CF) ? count($header_CF) : 0);$x++){
									if(!empty($fetch_CF[$header_CF[$x]])){
										$fetch_row[] =  str_replace(",", " | ", (string) $fetch_CF[$header_CF[$x]]);
									}else{
										$fetch_row[] =  "";
									}
								}
							}

							//die();
						//}
					}


					for($a=0;$a < (is_countable($fetch_row) ? count($fetch_row) : 0);$a++){
						$row[] = $fetch_row[$a];
					}
					$queries[] = $row;
					unset($fetch_row);
					unset($fetch_CF);
			    }
			$csv_row[] = $row;
			$count_row++;
		}
		//var_dump($queries);
		$main_row = [];
        $counter = (is_countable($csv_row) ? count($csv_row) : 0);
		//put keys in each row
		for($i=0; $i < $counter; $i++){
			//unset($re_head);
			$re_row = [];
			for($a=0;$a<(is_countable($csv_header) ? count($csv_header) : 0);$a++){
				//$re_head[] = $csv_header[$a];
				if($csv_header[$a] !== "AfterDispo")
				$re_row[$csv_header[$a]] = str_replace("\\", "", (string) ($csv_row[$i][$a] ?? ''));
			}

			$main_row[] = $re_row;
		}
		//var_dump($main_row);
		//var_dump($query_fields);
		//"query" => $query, "header" => $csv_header,
		$paging = ["totalElements" => $count_row, "limit" => $limit];


		//var_dump($return);
		if(is_numeric($export) && ($export !== '' && $export !== '0') && $export == 1){
			if($count_row >= 1){
				$filename = "Call_Details_".$start_date."_".$end_date.".csv";
	        	 header('Content-type: application/csv');
	        	 header('Content-Disposition: attachment; filename='.$filename);

	        	echo implode(",",$csv_header)."\n";

	        	$count = 0;
                $counter = (is_countable($csv_row) ? count($csv_row) : 0);
		        for($i=0; $i <= $counter; $i++){
		            $count_row = $csv_row[$i];
		            for($x=0; $x <= (is_countable($count_row) ? count($count_row) : 0); $x++){
		                if($x === (is_countable($count_row) ? count($count_row) : 0)){
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
				$apiresults = ["result" => "No records retrieved from: ".$start_date." - ".$end_date];
			}
        }else{
			if($count_row < 1){
				$err_msg = error_handle("40001");
				//"query" => $userlog_query,
				$apiresults = ["result" => "No records retrieved from: ".$start_date." - ".$end_date];
			}else{
				$apiresults = ["paging" => $paging, "rows" => $main_row];
			}
		}


		//$log_id = log_action($linkgo, 'VIEW', $user, $ip_address, "Viewed the agent log of Agent: $user", $groupId);
	}
?>
