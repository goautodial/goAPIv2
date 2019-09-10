<?php
/**
 * @file        goGetReports.php
 * @brief       API to for Reports (Export Call Report, Stats, Agent Details etc.)
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      AlexANDer Jim Abenoja
 * @author		Demian LizANDro A. Biscocho
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it AND/or modify
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

    include_once("goAPI.php");
	include_once("goReportsFunctions.php");
	
	$campaigns 										= allowed_campaigns($log_group, $goDB, $astDB);

	// need function go_sec_convert();
    $pageTitle 										= strtolower($astDB->escape($_REQUEST['pageTitle']));
    $fromDate 										= $astDB->escape($_REQUEST['fromDate']);
    $toDate 										= $astDB->escape($_REQUEST['toDate']);
    $campaignID 									= $astDB->escape($_REQUEST['campaignID']);
    $request 										= $astDB->escape($_REQUEST['request']);
    $dispo_stats 									= $astDB->escape($_REQUEST['statuses']);
	
    if (empty($fromDate)) {
    	$fromDate 									= date("Y-m-d")." 00:00:00";
	}
    
    if (empty($toDate)) {
    	$toDate 									= date("Y-m-d")." 23:59:59";
	}
		
	$defPage 										= array(
		"stats", 
		"agent_detail", 
		"agent_pdetail", 
		"dispo", 
		"call_export_report", 
		"sales_agent", 
		"sales_tracker", 
		"inbound_report"
	);

	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($fromDate) && empty($toDate)) {
		$fromDate 									= date("Y-m-d") . " 00:00:00";
		$toDate 									= date("Y-m-d") . " 23:59:59";
		//die($fromDate." - ".$toDate);
	} elseif ($pageTitle == "sales_tracker" && empty($request)) {
		$err_msg 									= error_handle("40001");
		$apiresults 								= array(
			"code" 										=> "40001", 
			"result" 									=> $err_msg
		);
	} elseif ($pageTitle == "sales_agent" && empty($request)) {
		$err_msg 									= error_handle("40001");
		$apiresults 								= array(
			"code" 										=> "40001", 
			"result" 									=> $err_msg
		);
	} elseif (!in_array($pageTitle, $defPage)) {
	 	$err_msg 									= error_handle("10004");
		$apiresults 								= array(
			"code" 										=> "10004", 
			"result" 									=> $err_msg
		);
	} elseif ($pageTitle == "call_export_report") {
		$campaigns 									= $astDB->escape($_REQUEST['campaigns']);
		$inbounds 									= $astDB->escape($_REQUEST['inbounds']);
		$lists 										= $astDB->escape($_REQUEST['lists']);
		$custom_fields 									= $astDB->escape($_REQUEST['custom_fields']);
		$per_call_notes 								= $astDB->escape($_REQUEST['per_call_notes']);
		$rec_location 									= $astDB->escape($_REQUEST['rec_location']);
		
		$goReportsReturn 							= go_export_reports($fromDate, $toDate, $campaigns, $inbounds, $lists, $dispo_stats, $custom_fields, $per_call_notes, $rec_location, $log_group, $astDB);		
		$apiresults 								= array(
			"result" 								=> "success", 
			"getReports" 								=> $goReportsReturn
		);
	} else {
		$goReportsReturn 							= go_get_reports($pageTitle, $fromDate, $toDate, $campaignID, $request, $log_user, $log_group,$astDB, $dispo_stats, $goDB);
		$apiresults 								= array(
			"result" 									=> "success", 
			"getReports" 								=> $goReportsReturn
		);
	}
	return $apiresults;	
 
	function go_export_reports($fromDate, $toDate, $campaigns, $inbounds, $lists, $dispo_stats, $custom_fields, $per_call_notes, $rec_location,$log_group, $astDB) {		
		if (!empty($campaigns))
			$campaigns 							= explode(",",$campaigns);
		if (!empty($inbounds))
		    $inbounds 								= explode(",",$inbounds);
		if (!empty($lists))	
		    $lists 								= explode(",",$lists);
		if (!empty($dispo_stats))	
		    $dispo_stats 							= explode(",",$dispo_stats);
		
		$campaign_SQL 								= "";
		$group_SQL 								= "";
		$list_SQL 								= "";
		$status_SQL 								= "";
		
		$campaign_ct 								= count($campaigns);
		$group_ct 								= count($inbounds);
		$list_ct 								= count($lists);
		$status_ct 								= count($dispo_stats);

		if ($campaigns != "") {
			$i								= 0;
			$array_campaign 						= Array();

			while ($i < $campaign_ct) {
				//$campaign_SQL .= "'$campaigns[$i]',";
				$campaign_SQL 						.= "'?',";
				array_push($array_campaign, $campaigns[$i]);
				$i++;
			}
			
			$campaign_SQL 							= preg_replace("/,$/i",'',$campaign_SQL);
			$campaign_SQL 							= "AND vl.campaign_id IN($campaign_SQL)";
			$RUNcampaign							= $i;
			
		} else {
			$RUNcampaign							= 0;
		}
		
		if ($inbounds != "") {
			$i								= 0;
			$array_inbound 							= Array();

			while ($i < $group_ct) {
				if (strlen($inbounds[$i]) > 0) {
				  //$group_SQL .= "'$inbounds[$i]',";
					$group_SQL 						.= "'?',";
					array_push($array_inbound, $inbounds[$i]);
				}
				$i++;
			}
			
			$group_SQL 								= preg_replace("/,$/i",'',$group_SQL);
			if ($group_ct > 0) {
				$group_SQL 							= "AND vcl.campaign_id IN($group_SQL)";
			}
			
			$RUNgroup								=$i;
		} else {
			$RUNgroup								= 0;
		}
		
		if ($lists != "") {
			$list_SQL 								= "";
			
			$i									= 0;
			$array_list 								= Array();
			while ($i < $list_ct) {
				//$list_SQL .= "'$lists[$i]',";
				$list_SQL 							.= "'?',";
				array_push($array_list, $lists[$i]);
				$i++;
			}
			if (in_array("ALL", $lists)) {
				$list_SQL 							= "";
				$i								= 0;
				while ($i < $campaign_ct) {
					$camp_id 						= $campaigns[$i];
					$astDB->WHERE("campaign_id", $camp_id);
					$SELECTQuery = $astDB->getValue("vicidial_lists", "list_id");
					//$query_list = mysqli_query($astDB,"SELECT list_id FROM vicidial_lists WHERE campaign_id = '$camp_id';");
					foreach($SELECTQuery as $fetch_list) {
						$array_list[] 					= $fetch_list["list_id"];
					}
					
					$i++;
				}
				
			}
			else{
				$list_SQL 							= preg_replace("/,$/i",'',$list_SQL);
				$list_SQL 							= "AND vi.list_id IN($list_SQL)";
				$i									= 0;
				
				while ($i < $list_ct) {
					$array_list[] 						= $lists[$i];
					$i++;
				}
			}
		}
		
		if ($dispo_stats != "") {
			$i									= 0;
			$array_status 								= Array();

			while ($i < $status_ct) {
				//$status_SQL .= "'$dispo_stats[$i]',";
				$status_SQL 							.= "'?',";
				array_push($array_status, $dispo_stats[$i]);
				$i++;
			}
			
			if ( (in_array("ALL", $dispo_stats) ) or ($status_ct < 1) ) {
				$status_SQL 						= "";
			} else {
				$status_SQL 						= preg_replace("/,$/i",'',$status_SQL);
				$status_SQL 						= "AND vl.status IN ($status_SQL)";
			}
		}
		
		if ($log_group !== "ADMIN") {
			$stringv 								= go_getall_allowed_users($log_group);
			$user_group_SQL 						= "AND vl.user IN ($stringv)";
		}  else{
			$user_group_SQL 						= "";
		}
		
		$export_fields_SQL 							= "";
		
		if ($RUNcampaign > 0 && $RUNgroup < 1) {
			$WHERE_data 							= Array($fromDate, $toDate);
			$result 								= $astDB->rawQuery("SELECT vl.call_date,vl.phone_number,vl.status,vl.user,vu.full_name,vl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vl.length_in_sec,vl.user_group,vl.alt_dial,vi.rank,vi.owner,vi.lead_id,vl.uniqueid,vi.entry_list_id $export_fields_SQL FROM vicidial_users vu, vicidial_log vl,vicidial_list vi WHERE (date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '?' AND '?') AND vu.user=vl.user AND vi.lead_id=vl.lead_id $list_SQL $campaign_SQL $user_group_SQL $status_SQL order by vl.call_date", $WHERE_data);
		}
		
		if ($RUNgroup > 0 && $RUNcampaign < 1) {
			$WHERE_data 							= Array($fromDate, $toDate);
			$result 								= $astDB->rawQuery("
				SELECT vcl.call_date,
					vcl.phone_number,
					vcl.status,
					vcl.user,
					vu.full_name,
					vcl.campaign_id,
					vi.vendor_lead_code,
					vi.source_id,
					vi.list_id,
					vi.gmt_offset_now,
					vi.phone_code,
					vi.title,
					vi.first_name,
					vi.middle_initial,
					vi.last_name,
					vi.address1,
					vi.address2,
					vi.address3,
					vi.city,
					vi.state,
					vi.province,
					vi.postal_code,
					vi.country_code,
					vi.gender,
					vi.date_of_birth,
					vi.alt_phone,
					vi.email,
					vi.security_phrase,
					vi.comments,
					vcl.length_in_sec,
					vcl.user_group,
					vcl.queue_seconds,
					vi.rank,
					vi.owner,
					vi.lead_id,
					vcl.closecallid, 
					vcl.uniqueid, 
					vi.entry_list_id 
					$export_fields_SQL 
				FROM vicidial_users vu, vicidial_closer_log vcl, vicidial_list vi 
				WHERE (date_format(vcl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '?' AND '?') 
				AND vu.user=vcl.user AND vi.lead_id=vcl.lead_id AND vcl.lead_id = vcl.lead_id 
				$list_SQL $group_SQL 
				$user_group_SQL $status_SQL 
				order by vcl.call_date", 
				$WHERE_data
			);
		}
		
		if ($RUNcampaign > 0 && $RUNgroup > 0) {
			$WHERE_data = Array($fromDate, $toDate);
			$result = $astDB->rawQuery("
				(SELECT vl.call_date,
					vl.phone_number,
					vl.status,
					vl.user,
					vu.full_name,
					vl.campaign_id,
					vi.vendor_lead_code,
					vi.source_id,
					vi.list_id,
					vi.gmt_offset_now,
					vi.phone_code,
					vi.title,
					vi.first_name,
					vi.middle_initial,
					vi.last_name,
					vi.address1,
					vi.address2,
					vi.address3,
					vi.city,
					vi.state,
					vi.province,
					vi.postal_code,
					vi.country_code,
					vi.gender,
					vi.date_of_birth,
					vi.alt_phone,
					vi.email,
					vi.security_phrase,
					vi.comments,
					vl.length_in_sec,
					vl.user_group,
					vl.term_reason,
					vi.rank,
					vi.owner,
					vi.lead_id,
					vl.uniqueid, 
					vi.entry_list_id 
					$export_fields_SQL 
				FROM vicidial_users vu, vicidial_log vl,vicidial_list vi
				WHERE (date_format(vl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '?' AND '?') 
				AND vu.user=vl.user AND vi.lead_id=vl.lead_id 
				$list_SQL 
				$campaign_SQL 
				$user_group_SQL 
				$status_SQL 
				order by vl.call_date
			) UNION (
				SELECT vcl.call_date,
					vcl.phone_number,
					vcl.status,
					vcl.user,
					vu.full_name,
					vcl.campaign_id,
					vi.vendor_lead_code,
					vi.source_id,
					vi.list_id,
					vi.gmt_offset_now,
					vi.phone_code,
					vi.title,
					vi.first_name,
					vi.middle_initial,
					vi.last_name,
					vi.address1,
					vi.address2,
					vi.address3,
					vi.city,
					vi.state,
					vi.province,
					vi.postal_code,
					vi.country_code,
					vi.gender,
					vi.date_of_birth,
					vi.alt_phone,
					vi.email,
					vi.security_phrase,
					vi.comments,
					vcl.length_in_sec,
					vcl.user_group,
					vcl.term_reason,
					vi.rank,
					vi.owner,
					vi.lead_id, 
					vcl.closecallid, 
					vi.entry_list_id 
					$export_fields_SQL 
				FROM vicidial_users vu, vicidial_closer_log vcl,vicidial_list vi 
				WHERE (date_format(vcl.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '?' AND '?') 
				AND vu.user=vcl.user AND vi.lead_id=vcl.lead_id 
				$list_SQL 
				$group_SQL 
				$user_group_SQL 
				$status_SQL 
				order by vcl.call_date
			);", $WHERE_data);
        }
		
		//OUTPUT DATA HEADER//
		//while ($fieldinfo=mysqli_ftch_field($result))
		while ($fieldinfo = $astDB->getFieldNames()) {
			$csv_header[] 								= $fieldinfo->name;
		}
		
		if ($per_call_notes == "Y") {
			array_push($csv_header, "call_notes");
		}

		if ($rec_location == "Y") {
			array_push($csv_header, "recording_location");
		}
		if ($custom_fields == "Y")	{
		    for ($i = 0 ; $i < count($array_list); $i++) {
				$list_id 								= $array_list[$i];
				//$query_CF_list = "DESC custom_$list_id;");
				$cflist_data 							= Array("custom_".$list_id);
				$query_CF_list 							= rawQuery("DESC ?;", $cflist_data);
				if ($query_CF_list) {
					$n 									= 0;
					//while ($field_list=$astDB->rawQuery($query_CF_list)) {
					foreach ($query_CF_list as $field_list) {
						$exec_query_CF_list 			= $field_list["Field"];

						if ($exec_query_CF_list != "lead_id") {
							$active_list_fields["custom_$list_id"][$n] 				= $exec_query_CF_list;
							$n++;
						}
					}
				}
			}

			$header_CF 									= array();
			$keys 										= array_keys($active_list_fields);
			
			for ($i = 0; $i < count($keys); $i++) {
				$list_id 								= $keys[$i];
				for ($x = 0; $x < count($active_list_fields[$list_id]);$x++) {
					$field 								= $active_list_fields[$list_id][$x];
					if (!in_array($field,$header_CF)) {
						$header_CF[] 					= $field;
					}
				}
			}
			
			$csv_header 								= array_merge($csv_header,$header_CF);
		}
		
		//OUTPUT DATA ROW//
		foreach ($result as $row) {
			$lead_id 									= $row[34];
			$uniqueid 									= $row[35];
			$list_id_spec 								= $row[8];

			if ($per_call_notes == "Y") {
				$astDB->WHERE("lead_id", $lead_id);
				$fetch_callnotes 						= $astDB->getValue("vicidial_call_notes", "call_notes");
				//$query_callnotes = "SELECT call_notes FROM vicidial_call_notes WHERE lead_id='$lead_id' LIMIT 1;");
				$notes_ct 								= $astDB->count;
				
				if ($notes_ct > 0) {
					$notes_data 						= $fetch_callnotes["call_notes"];
					$notes_data 						= rawurldecode($notes_data);
				} else {
					$notes_data 						= "";
				}
				
				array_push($row,$notes_data);
			}

			if ($rec_location == "Y") {
				//$recording_array = Array($lead_id);
				if (isset($uniqueid2) && !empty($uniqueid2)) {
					//$condition_SQL = "AND ((vicidial_id = '$uniqueid') OR (vicidial_id = '$uniqueid2')) ";
					$astDB->WHERE("vicidial_id", $uniqueid);
					$astDB->orWHERE("vicidial_id", $uniqueid2);
				} else {
					//$condition_SQL = "AND vicidial_id = '$uniqueid'";
					$astDB->WHERE("vicidial_id", $uniqueid);
				}
				$astDB->WHERE("lead_id", $lead_id);
				$fetch_recording 						= $astDB->getValue("recording_log", "location");	
				//$query_recordings = "SELECT location FROM recording_log WHERE lead_id='$lead_id' $condition_SQL LIMIT 1;");
				$rec_ct 								= $astDB->rawQuery($query_recordings);
				if ($rec_ct > 0) {
					$rec_data 							= $fetch_recording["location"];
					//$rec_data = rawurldecode($rec_data);
				} else {
					$rec_data 							= "";
				}
				
				array_push($row,$rec_data);
			}

			// Replace special characters [,] with -
            if (!empty($row[28])) {
                $row[28] 								= preg_replace('/[,]+/', '-', trim($row[28]));
            }
            
            if (!empty($row[15])) {
                $row[15] 								= preg_replace('/[,]+/', '-', trim($row[15]));
            }
            if (!empty($row[16])) {
                $row[16] 				= preg_replace('/[,]+/', '-', trim($row[16]));
            }
            if (!empty($row[17])) {
                $row[17] 				= preg_replace('/[,]+/', '-', trim($row[17]));
            }

			if ($custom_fields == "Y")	{
				$keys 									= array_keys($active_list_fields); // list of active custom lists
				
				for ($i = 0 ; $i < count($keys); $i++) {
				    $list_id 							= $keys[$i];
					$fields 							= implode(",", $active_list_fields[$list_id]);
					
					if ("custom_".$list_id_spec === $list_id) {
						$astDB->WHERE("lead_id", $lead_id);
						$fetch_CF 						= $astDB->getValue($list_id, $fields);
						//$query_row_sql = "SELECT $fields FROM $list_id WHERE lead_id ='$lead_id';";

						if ($fetch_CF !== NULL) {
							for ($x = 0;$x < count($header_CF);$x++) {
								if (!empty($fetch_CF[$header_CF[$x]])) {
									$fetch_row[] 		=  str_replace(",", " | ", $fetch_CF[$header_CF[$x]]);
								} else {
									$fetch_row[] 		=  "";
								}
							}
						}
					}
					

					for ($a=0;$a < count($fetch_row);$a++) {
						array_push($row, $fetch_row[$a]);
					}
					
					$queries[] 							= $row;
					unset($fetch_row);
					unset($fetch_CF);
			    }
			}
			
			$csv_row[]									= $row;
		}
		// new
		// ----
		$campFilter 									= (strlen($campaigns) > 0) ? "Campaign(s): $campaigns" : "";
		$inbFilter  									= (strlen($inbounds) > 0) ? "Inbound Groups(s): $inbounds" : "";
		$listFilter 									= (strlen($lists) > 0) ? "List(s): $lists" : "";
		$log_id 									= log_action($goDB, 'DOWNLOAD', $log_user, $log_ip, "Exported Call Reports starting FROM $fromDate to $toDate using the following filters, $campFilter $inbFilter $listFilter", $log_group);
		
		$return 										= array(
			"query" 										=> $query, 
			"header" 										=> $csv_header, 
			"rows" 											=> $csv_row, 
			"return_this" 									=> $query
		);
		
		return $return; 
		}
	}
	
	function go_get_reports($pageTitle, $fromDate, $toDate, $campaignID, $request, $log_user, $log_group, $astDB, $dispo_stats, $goDB) {		
		if (!empty($campaignID) || $pageTitle == 'call_export_report') {
			$date1 										= new DateTime($fromDate);
			$date2 										= new DateTime($toDate);
			$interval 									= date_diff($date1,$date2);
			$date_diff 									= $interval->format('%d');
            		$date_array 								= implode("','",go_get_dates($fromDate, $toDate));
			$file_download 								= 1;            
            		$tenant 									= 0;
            
            // set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
            // every time we need to filter out requests
			if (checkIfTenant ($log_group, $goDB)) {
				$tenant									= 1;
			}
			
            		if ($tenant) {
				$astDB->where("user_group", $log_group);
            		} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($user_level > 8) {
						$astDB->where("user_group", $log_group);
					}
				}
            		}	
            
            //Initialise Values                        
			if ($pageTitle != 'inbound_report') {
				$resultu 								= $astDB
					->where("campaign_id", $campaignID)
					->getValue("vicidial_campaigns", "campaign_name");
			} else {
				$resultu 								= $astDB
					->where("uniqueid_status_prefix", $log_group)
					->getValue("vicidial_inbound_groups", "group_name");
			}
            
			if ($astDB->count < 1) {
				$err_msg 								= error_handle("41004", "campaignID. Doesn't exist");
				$apiresults 							= array(
					"code" 									=> "41006", 
					"result" 								=> $err_msg
				); 
			} else {
				//foreach ($resultu as $campaign_name) {
					//$return['campaign_name'] 			= $resultu['campaign_name'];
				$return['campaign_name'] 				= $resultu;				
				//}
			}
				
			if (!isset($request) || $request=='') {
				$return['request'] 						= 'daily';
			} else {
				$return['request'] 						= $request;
			}
			
			$Qstatus									= $astDB
				->where("sale", "Y")
				->getOne("vicidial_statuses", "status");
				
			$sstatusRX 									= "";
			$sstatuses 									= array();			
			$a 											= 0;
			
			if ($Qstatus) {
				//foreach ($query as $Qstatus) {
				$goTempStatVal 							= $Qstatus['status'];
				$sstatuses[$a] 							= $Qstatus['status'];
				$sstatusRX							.= "{$goTempStatVal}|";
					//$a++;
				//}			
			}
			
			if (!empty($sstatuses))
			$sstatuses 									= implode("','",$sstatuses);
			
			$Qstatus									= $astDB
				->where("sale", "Y")
				->where("campaign_id", $campaignID)
				->getOne("vicidial_campaign_statuses", "status");
			
			$cstatusRX 									= "";
			$cstatuses 									= array();			
			$b 										= 0;
			
			if ($Qstatus) {
				//foreach ($query as $Qstatus) {
				$goTempStatVal 							= $Qstatus['status'];
				$cstatuses[$b] 							= $Qstatus['status'];
				$cstatusRX							.= "{$goTempStatVal}|";
					//$b++;
				//}			
			}
			
			if (!empty($cstatuses)) {
				$cstatuses 								= implode("','",$cstatuses);
			}
			
			
			if (count($sstatuses) > 0 && count($cstatuses) > 0) {
				$statuses 								= "{$sstatuses}','{$cstatuses}";
				$statusRX 								= "{$sstatusRX}{$cstatusRX}";
			} else {
				$statuses 								= (count($sstatuses) > 0 && count($cstatuses) < 1) ? $sstatuses : $cstatuses;
				$statusRX 								= (count($sstatusRX) > 0 && count($cstatusRX) < 1) ? $sstatusRX : $cstatusRX;
			}
			
			$statusRX 									= trim($statusRX, "|");
			//End initialize		
			//Start Report		
			// Agent Statistics
			if ($pageTitle == 'stats') {				
				if ($return['request'] == 'daily') {
					$stringv 							= go_getall_closer_campaigns($campaignID, $astDB);
					$closerCampaigns 					= " AND campaign_id IN ('$stringv') ";
					$vcloserCampaigns 					= " AND vclog.campaign_id IN ('$stringv') ";
					$call_time 							= go_get_calltimes($campaignID, $astDB);
					
					if ($log_group !== "ADMIN") {
						$ul 							= "AND user_group = '$log_group'";
					} else {
						$ul 							= "";
					}
					
					if (strlen($stringv) > 0 && $stringv != '') {
						$MunionSQL 						= "UNION select date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 00, 1, 0)) as 'Hour0',sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23' from vicidial_closer_log where length_in_sec>'0' $ul and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns group by cdate";
						$TunionSQL 						= "UNION ALL select phone_number from vicidial_closer_log vcl where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns $ul";
						$DunionSQL 						= "UNION select status,count(*) as ccount from vicidial_closer_log where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $closerCampaigns $ul group by status";
					}
					
					// Total Calls Made					
					$qtotalcallsmade 					= $astDB->rawQuery("select phone_number from vicidial_log vl where length_in_sec>'0' and campaign_id = '$campaignID' and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $ul $TunionSQL");
					
					if (count($qtotalcallsmade) > 0) {
						foreach ($qtotalcallsmade as $row) {
							$cdate[] 					= $row['cdate'];
							$hour0[] 					= $row['Hour0'];
							$hour1[] 					= $row['Hour1'];
							$hour2[] 					= $row['Hour2'];
							$hour3[] 					= $row['Hour3'];
							$hour4[] 					= $row['Hour4'];
							$hour5[] 					= $row['Hour5'];
							$hour6[] 					= $row['Hour6'];
							$hour7[] 					= $row['Hour7'];
							$hour8[] 					= $row['Hour8'];
							$hour9[] 					= $row['Hour9'];
							$hour10[] 					= $row['Hour10'];
							$hour11[] 					= $row['Hour11'];
							$hour12[] 					= $row['Hour12'];
							$hour13[] 					= $row['Hour13'];
							$hour14[] 					= $row['Hour14'];
							$hour15[] 					= $row['Hour15'];
							$hour16[] 					= $row['Hour16'];
							$hour17[] 					= $row['Hour17'];
							$hour18[] 					= $row['Hour18'];
							$hour19[] 					= $row['Hour19'];
							$hour20[] 					= $row['Hour20'];
							$hour21[] 					= $row['Hour21'];
							$hour22[] 					= $row['Hour22'];
							$hour23[] 					= $row['Hour23'];							
						}						
					}	
					
					$data_calls 						= array(
						"cdate" 							=> $cdate, 
						"hour0" 							=> $hour0, 
						"hour1" 							=> $hour1, 
						"hour2" 							=> $hour2, 	
						"hour3" 							=> $hour3, 
						"hour4" 							=> $hour4, 
						"hour5" 							=> $hour5, 
						"hour6" 							=> $hour6, 
						"hour7" 							=> $hour7, 
						"hour8"	 							=> $hour8, 
						"hour9" 							=> $hour9, 
						"hour10" 							=> $hour10, 
						"hour11" 							=> $hour11,
						"hour12"	 						=> $hour12, 
						"hour13" 							=> $hour13, 
						"hour14" 							=> $hour14, 
						"hour15" 							=> $hour15, 
						"hour16" 							=> $hour16, 
						"hour17"	 						=> $hour17, 
						"hour18" 							=> $hour18, 
						"hour19" 							=> $hour19, 
						"hour20" 							=> $hour20, 
						"hour21" 							=> $hour21, 
						"hour22" 							=> $hour22, 
						"hour23" 							=> $hour23
					);					
					
					$astDB->rawQuery("select phone_number from vicidial_log vl where length_in_sec>'0' and campaign_id = '$campaignID' and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $ul $TunionSQL");
					
					$total_calls						= $astDB->getRowCount();
					
					// Total Number of Leads
					$qtotalleads 						= $astDB
						->where("vlo.campaign_id", $campaignID)
						->where("vl.list_id = vlo.list_id")
						->get("vicidial_list as vl, vicidial_lists as vlo");
						
					$total_leads						= $astDB->getRowCount();
					
					// Total Number of New Leads
					$qtotalnew							= $astDB
						->where("vlo.campaign_id", $campaignID)
						->where("vl.list_id = vlo.list_id")
						->where("vl.status = 'NEW'")
						->get("vicidial_list as vl, vicidial_lists as vlo");
					
					$total_new							= $astDB->getRowCount();
						
					// Total Agents Logged In
					$qtotalagents						= $astDB
						->where("campaign_id", $campaignID)
						->where("date_format(event_time, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate'")
						->groupBy("cuser")
						->get("vicidial_agent_log", NULL, array("date_format(event_time, '%Y-%m-%d') as cdate", "user as cuser"));					
					
					$total_agents						= $astDB->getRowCount();
					
					if (count($qtotalagents) > 0) {
						foreach ($qtotalagents as $row) {
							$cdate[] 					= $row['cdate'];
							$cuser[] 					= $row['cuser'];						
						}											
					}
					
					$data_agents 						= array(
						"cdate" 							=> $cdate, 
						"cuser" 							=> $cuser
					);
					
					// Disposition of Calls
					$astDB->rawQuery("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul and campaign_id = '$campaignID' group by status $DunionSQL) t group by status;");
					
					$total_status						= $astDB->getRowCount();
					$qtotalstatus 						= $astDB->rawQuery("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate' $ul and campaign_id = '$campaignID' group by status $DunionSQL) t group by status;");
					
					if (count($qtotalstatus) > 0) {
						foreach ($qtotalstatus as $row) {
							$status[] 					= $row['status'];
							$ccount[] 					= $row['ccount'];
							
							#getting status name
							$var_status 				= $row['status'];
							
							$fetch_statusname			= $astDB
								->where("status", $var_status)
								->getOne("vicidial_statuses", "status_name");							
							
							if (empty($fetch_statusname) || is_null($fetch_statusname)) {
								# in custom statuses
								$fetch_statusname		= $astDB
									->where("status", $var_status)
									->getOne("vicidial_campaign_statuses", "status_name");
							}
							
							$status_name[] 				= $fetch_statusname['status_name'];							
						}
					}
					
					$data_status 						= array(
						"status" 							=> $status, 
						"status_name" 						=> $status_name, 
						"ccount" 							=> $ccount, 
						"query" 							=> $qtotalcallsmade
					);
				}
				
				if ($return['request'] == 'weekly') {
					$stringv 							= go_getall_closer_campaigns($campaignID, $astDB);
					$closerCampaigns 					= " AND campaign_id IN ('$stringv') ";
					$vcloserCampaigns 					= " AND vclog.campaign_id IN ('$stringv') ";

					if (strlen($stringv) > 0 && $stringv != '') {
						$MunionSQL 						= "UNION select week(DATE_FORMAT( call_date, '%Y-%m-%d' )) as weekno, sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 0, 1, 0))  as 'Day0', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0))  as 'Day1', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0))  as 'Day2', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0))  as 'Day3', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0))  as 'Day4', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0))  as 'Day5', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0))  as 'Day6' from vicidial_closer_log where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns group by weekno";
						$TunionSQL 						= "UNION ALL select phone_number from vicidial_closer_log vcl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns";
						$DunionSQL 						= "UNION select status,count(*) as ccount from vicidial_closer_log vcl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns group by status";
					}
					
					// Total Calls Made
					$qtotalcallsmade 					= $astDB->rawQuery("select weekno, sum(Day0) as 'Day0', sum(Day1) as 'Day1', sum(Day2) as 'Day2', sum(Day3) as 'Day3', sum(Day4) as 'Day4', sum(Day5) as 'Day5', sum(Day6) as 'Day6' from (select week(DATE_FORMAT( call_date, '%Y-%m-%d' )) as weekno, sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 0, 1, 0))  as 'Day0', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0))  as 'Day1', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0))  as 'Day2', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0))  as 'Day3', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0))  as 'Day4', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0))  as 'Day5', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0))  as 'Day6' from vicidial_log where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' )) between week('$fromDate') and week('$toDate') $ul group by weekno $MunionSQL) t group by weekno;");
					
					if (count($qtotalcallsmade) > 0) {
						foreach ($qtotalcallsmade as $row) {
							$weekno[] 					= "Week ".$row['weekno'];
							$day0[] 					= $row['Day0'];
							$day1[] 					= $row['Day1'];
							$day2[] 					= $row['Day2'];
							$day3[] 					= $row['Day3'];
							$day4[] 					= $row['Day4'];
							$day5[] 					= $row['Day5'];
							$day6[] 					= $row['Day6'];						
						}
					}
					
					$data_calls 						= array(
						"weekno" 							=> $weekno, 
						"Day0" 								=> $day0, 
						"Day1" 								=> $day1, 
						"Day2" 								=> $day2, 
						"Day3" 								=> $day3, 
						"Day4" 								=> $day4, 
						"Day5" 								=> $day5, 
						"Day6" 								=> $day6
					);
					
					$astDB->rawQuery("select phone_number from vicidial_log vl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' )) between week('$fromDate') and week('$toDate') $ul $TunionSQL");
					
					$total_calls						= $astDB->getRowCount();
					
					// Total Number of Leads
					$qtotalleads 						= $astDB
						->where("vlo.campaign_id", $campaignID)
						->where("vl.list_id = vlo.list_id")
						->get("vicidial_list as vl, vicidial_lists as vlo");
						
					$total_leads						= $astDB->getRowCount();
					
					// Total Number of New Leads
					$qtotalnew							= $astDB
						->where("vlo.campaign_id", $campaignID)
						->where("vl.list_id = vlo.list_id")
						->where("vl.status = 'NEW'")
						->get("vicidial_list as vl, vicidial_lists as vlo");
					
					$total_new							= $astDB->getRowCount();
					
					// Total Agents Logged In
					$qtotalagents						= $astDB
						->where("campaign_id", $campaignID)
						->where("date_format(event_time, '%Y-%m-%d %H:%i:%s') between '$fromDate' and '$toDate'")
						->groupBy("cuser")
						->get("vicidial_agent_log", NULL, array("date_format(event_time, '%Y-%m-%d') as cdate", "user as cuser"));
					
					$total_agents						= $astDB->getRowCount();
					
					if (count($qtotalagents) > 0) {
						foreach ($qtotalagents as $row) {
							$cdate[] 					= $row['cdate'];
							$cuser[] 					= $row['cuser'];						
						}
					}
					
					$data_agents 						= array(
						"cdate" 							=> $cdate, 
						"cuser" 							=> $cuser
					);
					
					// Disposition of Calls
					$qtotalstatus 						= $astDB->rawQuery("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d %H:%i:%s' )) between week('$fromDate') and week('$toDate') $ul group by status $DunionSQL) t group by status;");
					$total_status						= $astDB->getRowCount();
					
					if (count($qtotalstatus) > 0) {
						foreach ($qtotalstatus as $row) {
							$status[] 					= $row['status'];
							$ccount[] 					= $row['ccount'];
							
							#getting status name
							$var_status 				= $row['status'];
							
							$fetch_statusname			= $astDB
								->where("status", $var_status)
								->getOne("vicidial_statuses", "status_name");							
							
							if (empty($fetch_statusname) || is_null($fetch_statusname)) {
								# in custom statuses
								$fetch_statusname		= $astDB
									->where("status", $var_status)
									->getOne("vicidial_campaign_statuses", "status_name");
							}
							
							$status_name[] 				= $fetch_statusname['status_name'];							
						}
					}
					
					$data_status 						= array(
						"status" 						=> $status, 
						"status_name" 					=> $status_name, 
						"ccount" 						=> $ccount
					);
				}
				
				if ($return['request'] == 'monthly') {
					$stringv 							= go_getall_closer_campaigns($campaignID, $astDB);
					$closerCampaigns 					= " AND campaign_id IN ('$stringv') ";
					$vcloserCampaigns 					= " AND vclog.campaign_id IN ('$stringv') ";

					if (strlen($stringv) > 0 && $stringv != '') {
						$MunionSQL 						= "UNION select MONTHNAME(DATE_FORMAT( call_date, '%Y-%m-%d' )) as monthname, sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0)) as 'Month1', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0)) as 'Month2', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0)) as 'Month3', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0)) as 'Month4', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0)) as 'Month5', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0)) as 'Month6', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 7, 1, 0)) as 'Month7', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 8, 1, 0)) as 'Month8', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 9, 1, 0)) as 'Month9', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 10, 1, 0)) as 'Month10', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 11, 1, 0)) as 'Month11', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 12, 1, 0)) as 'Month12' from vicidial_closer_log where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns group by monthname";							
						$TunionSQL 						= "UNION ALL select phone_number from vicidial_closer_log vcl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns";
						$DunionSQL 						= "UNION select status,count(*) as ccount from vicidial_closer_log vcl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns group by status";
					}

					// Total Calls Made		
					$qtotalcallsmade					= $astDB->rawQuery("select monthname, sum(Month1) as 'Month1', sum(Month2) as 'Month2', sum(Month3) as 'Month3', sum(Month4) as 'Month4', sum(Month5) as 'Month5', sum(Month6) as 'Month6', sum(Month7) as 'Month7', sum(Month8) as 'Month8', sum(Month9) as 'Month9', sum(Month10) as 'Month10', sum(Month11) as 'Month11', sum(Month12) as 'Month12' from (select MONTHNAME(DATE_FORMAT( call_date, '%Y-%m-%d' )) as monthname, sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0)) as 'Month1', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0)) as 'Month2', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0)) as 'Month3', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0)) as 'Month4', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0)) as 'Month5', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0)) as 'Month6', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 7, 1, 0)) as 'Month7', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 8, 1, 0)) as 'Month8', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 9, 1, 0)) as 'Month9', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 10, 1, 0)) as 'Month10', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 11, 1, 0)) as 'Month11', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 12, 1, 0)) as 'Month12' from vicidial_log where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul group by monthname $MunionSQL) t group by monthname;");
					
					if (count($qtotalcallsmade) > 0) {
						foreach ($qtotalcallsmade as $row) {
							$monthname[] 				= $row['monthname'];
							$month0[] 					= $row['Month1'];
							$month1[] 					= $row['Month2'];
							$month2[] 					= $row['Month3'];
							$month3[] 					= $row['Month4'];
							$month4[] 					= $row['Month5'];
							$month5[] 					= $row['Month6'];
							$month6[] 					= $row['Month7'];
							$month7[] 					= $row['Month8'];
							$month8[] 					= $row['Month9'];
							$month9[] 					= $row['Month10'];
							$month10[] 					= $row['Month11'];
							$month11[] 					= $row['Month12'];						
						}											
					}
					
					$data_calls 						= array(
						"monthname" 						=> $monthname, 
						"Month1" 							=> $month0, 
						"Month2" 							=> $month1, 
						"Month3" 							=> $month2, 
						"Month4" 							=> $month3, 
						"Month5" 							=> $month4, 
						"Month6" 							=> $month5, 
						"Month7" 							=> $month6, 
						"Month8" 							=> $month7, 
						"Month9" 							=> $month8, 
						"Month10" 							=> $month9, 
						"Month11" 							=> $month10, 
						"Month12" 							=> $month11
					);
					
					$astDB->rawQuery("select phone_number from vicidial_log vl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul $TunionSQL");
					
					$total_calls						= $astDB->getRowCount();
					
					// Total Number of Leads
					$qtotalleads 						= $astDB
						->where("vlo.campaign_id", $campaignID)
						->where("vl.list_id = vlo.list_id")
						->get("vicidial_list as vl, vicidial_lists as vlo");
						
					$total_leads						= $astDB->getRowCount();
					
					// Total Number of New Leads
					$qtotalnew							= $astDB
						->where("vlo.campaign_id", $campaignID)
						->where("vl.list_id = vlo.list_id")
						->where("vl.status = 'NEW'")
						->get("vicidial_list as vl, vicidial_lists as vlo");
					
					$total_new							= $astDB->getRowCount();
					
					// Total Agents Logged In
					$qtotalagents						= $astDB
						->where("campaign_id", $campaignID)
						->where("MONTH(event_time)", "MONTH(event_time) between MONTH('$fromDate') and MONTH('$toDate')")
						->groupBy("cuser")
						->get("vicidial_agent_log", NULL, array("date_format(event_time, '%Y-%m-%d') as cdate", "user as cuser"));
						
					$total_agents						= $astDB->getRowCount();
					
					if (count($qtotalagents) > 0) {
						foreach ($qtotalagents as $row) {
							$cdate[] 					= $row['cdate'];
							$cuser[] 					= $row['cuser'];						
						}											
					}
					
					$data_agents 						= array(
						"cdate" 							=> $cdate, 
						"cuser" 							=> $cuser
					);					
					
					// Disposition of Calls					
					$qtotalstatus 						= $astDB->rawQuery("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul group by status $DunionSQL) t group by status;");
					$total_status						= $astDB->getRowCount();
					
					if (count($qtotalstatus) > 0) {
						foreach ($qtotalstatus as $row) {
							$status[] 					= $row['status'];
							$ccount[] 					= $row['ccount'];
							
							#getting status name
							$var_status 				= $row['status'];
							
							$fetch_statusname			= $astDB
								->where("status", $var_status)
								->getOne("vicidial_statuses", "status_name");							
							
							if (empty($fetch_statusname) || is_null($fetch_statusname)) {
								# in custom statuses
								$fetch_statusname		= $astDB
									->where("status", $var_status)
									->getOne("vicidial_campaign_statuses", "status_name");
							}
							
							$status_name[] 				= $fetch_statusname['status_name'];							
						}
					}
					
					$data_status 						= array(
						"status" 							=> $status, 
						"status_name" 						=> $status_name, 
						"ccount" 							=> $ccount
					);
				}
				
				$apiresults 							= array(
					"call_time" 							=> $call_time, 
					"data_calls" 							=> $data_calls, 
					"data_status" 							=> $data_status, 
					"data_agents" 							=> $data_agents, 
					"total_calls" 							=> $total_calls, 
					"total_leads" 							=> $total_leads, 
					"total_new" 							=> $total_new, 
					"total_status" 							=> $total_status
				);
				
				return $apiresults;
			}
			
			// Agent Time Detail
			if ($pageTitle == "agent_detail") {
				if ($log_group !== "ADMIN") {
					$ul 								= "AND user_group = '$log_group'";
				} else {
					$ul 								= "";
				}

				// BEGIN gather user IDs AND names for matching up later
				/*$query 									= "
					SELECT full_name,user FROM vicidial_users 
					ORDER BY user 
					LIMIT 100000
				";
				
				$user_ct 								= $astDB->rawQuery($query);*/
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($user_level > 8) {
							$astDB->where("user_group", $log_group);
						}
					}					
				}				
				
				$quserct 								= $astDB
					->orderBy("user")
					->get("vicidial_users", 1000, array("full_name" ,"user"));
					
				$user_ct								= $astDB->getRowCount();
					
				if (count($quserct) > 0) {
					foreach ($quserct as $row) {
						$ULname[] 						= $row['full_name'];
						$ULuser[] 						= $row['user'];					
					}
				}
								
				/*while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {
					$ULname[] 							= $row['full_name'];
					$ULuser[] 							= $row['user'];
				}*/
				
				// END gather user IDs AND names for matching up later
			
				// BEGIN gather timeclock records per agent
				/*$query 									= "
					SELECT user,SUM(login_sec) AS login_sec FROM vicidial_timeclock_log 
					WHERE event IN('LOGIN','START') AND date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
					GROUP BY user 
					LIMIT 10000000
				";
				
				$timeclock_ct 							= $astDB->rawQuery($query);*/
				if ($tenant) {
					$astDB->where("user_group", $log_group);
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($user_level > 8) {
							$astDB->where("user_group", $log_group);
						}
					}					
				}
				
				$timeclock_ct 							= $astDB
					->where("event", array("LOGIN", "START"), "IN")
					->where("date_format(event_date, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->get("vicidial_timeclock_log", "user, SUM(login_sec) as login_sec");
				
				if ($astDB->count > 0) {
					foreach ($timeclock_ct as $row) {
						$TCuser[] 						= $row['user'];
						$TCtime[] 						= $row['login_sec'];					
					}
				}
				
				/*while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {
					$TCuser[] 							= $row['user'];
					$TCtime[] 							= $row['login_sec'];
				}*/
				
				// END gather timeclock records per agent				
				// BEGIN gather pause code information by user IDs
				$sub_statuses 							= '-';
				$sub_statusesTXT 						= '';
				$sub_statusesHEAD 						= '';
				$sub_statusesHTML 						= '';
				$sub_statusesFILE 						= '';
				$sub_statusesTOP 						= array();
				$sub_statusesARY 						= $MT;
				
				$PCusers 							= '-';
				$PCusersARY 							= $MT;
				$PCuser_namesARY 						= $MT;

				$i								= 0;
				$a								= 1;
				$sub_status_count						= 0;
				$user_count							= 0;
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($user_level > 8) {
							$astDB->where("user_group", $log_group);
						}
					}					
				}
			
				$pause_sec_ct 							= $astDB
					->where("date_format(event_time, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->where("pause_sec", 0, ">")
					->where("pause_sec", 65000, "<")
					->where("campaign_id", $campaignID)
					->groupBy("user, sub_status")
					->orderBy("user", "DESC", array("sub_status"))
					->get("vicidial_agent_log", 1000, "user, SUM(pause_sec) as pause_sec, sub_status");
		
				if ($astDB->count > 0) {
					foreach ($pause_sec_ct as $row) {
						$PCuser[] 						= $row['user'];
						$PCpause_sec[] 					= $row['pause_sec'];
						$sub_status[] 					= $row['sub_status'];
						
						if (!preg_match("/-$sub_status-/", $sub_statuses)) {
							$sub_statusesFILE 			.= ",$sub_status";
							$sub_statuses 				.= "$sub_status-";
							$sub_statusesARY[$sub_status_count] = $sub_status;
							$sub_statusesTOP[] 			= $sub_status;
							//$sub_status_count++;
						}
						
						if (!preg_match("/-$PCuser-/", $PCusers)) {
							$PCusersARY[$user_count] 	= $PCuser;
							//$user_count++;
						}						
					}
				}
				
				$j										= 0;
				$k										= 0;
				$uc										= 0;
				
				if ($tenant) {
					$astDB->where("user_group", $log_group);
				} else {
					if (strtoupper($log_group) != 'ADMIN') {
						if ($user_level > 8) {
							$astDB->where("user_group", $log_group);
						}
					}					
				}
				
				$cols									= array(
					"user",
					"wait_sec",
					"talk_sec",
					"dispo_sec",
					"pause_sec",
					"lead_id",
					"status",
					"dead_sec"
				);
				
				$agent_time_ct 							= $astDB
					->where("date_format(event_time, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
					->where("campaign_id", $campaignID)
					->get("vicidial_agent_log", 1000, $cols);
					
				if ($astDB->count >0) {
					foreach ($agent_time_ct as $row) {
						$user 							= $row['user'];
						$wait 							= $row['wait_sec'];
						$talk 							= $row['talk_sec'];
						$dispo 							= $row['dispo_sec'];
						$pause 							= $row['pause_sec'];
						$lead 							= $row['lead_id'];
						$status 						= $row['status'];
						$dead 							= $row['dead_sec'];
						
						if ($wait > 65000) { $wait  	= 0; }
						if ($talk > 65000) { $talk		= 0; }
						if ($dispo > 65000) { $dispo	= 0; }
						if ($pause > 65000) { $pause	= 0; }
						if ($dead > 65000) { $dead		= 0; }
						
						$customer 						= ($talk - $dead);
						
						if ($customer < 1) {
							$customer					= 0;
						}
						
						$TOTwait 						= ($TOTwait + $wait);
						$TOTtalk 						= ($TOTtalk + $talk);
						$TOTdispo 						= ($TOTdispo + $dispo);
						$TOTpause 						= ($TOTpause + $pause);
						$TOTdead 						= ($TOTdead + $dead);
						$TOTcustomer 						= ($TOTcustomer + $customer);
						$TOTALtime 						= ($TOTALtime + $pause + $dispo + $talk + $wait);
						
						if ( ($lead > 0) AND ((!preg_match("/NULL/",$status)) AND (strlen($status) > 0)) ) {
							$TOTcalls++;
						}
						
						$user_found						= 0;
						
						if ($uc < 1) {
							$Suser[$uc] 				= $user;
							$uc++;
						}
						
						$m								= 0;
						
						while ( ($m < $uc) AND ($m < 50000) ) {
							if ($user == $Suser[$m]) {
								$user_found++;
								$Swait[$m] 				= ($Swait[$m] + $wait);
								$Stalk[$m] 				= ($Stalk[$m] + $talk);
								$Sdispo[$m] 				= ($Sdispo[$m] + $dispo);
								$Spause[$m] 				= ($Spause[$m] + $pause);
								$Sdead[$m] 				= ($Sdead[$m] + $dead);
								$Scustomer[$m] 				= ($Scustomer[$m] + $customer);
								
								if ( ($lead > 0) AND ((!preg_match("/NULL/",$status)) AND (strlen($status) > 0)) ) {
									$Scalls[$m]++;
								}
							}
							
							$m++;
						}
						
						if ($user_found < 1) {
							$Scalls[$uc] 				= 0;
							$Suser[$uc] 				= $user;
							$Swait[$uc] 				= $wait;
							$Stalk[$uc] 				= $talk;
							$Sdispo[$uc] 				= $dispo;
							$Spause[$uc] 				= $pause;
							$Sdead[$uc] 				= $dead;
							$Scustomer[$uc] 			= $customer;
							
							if ($lead > 0) {
								$Scalls[$uc]++;
							}
							
							$uc++;
						}						
					}
				}
							
				/*while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {
					$user 								= $row['user'];
					$wait 								= $row['wait_sec'];
					$talk 								= $row['talk_sec'];
					$dispo 								= $row['dispo_sec'];
					$pause 								= $row['pause_sec'];
					$lead 								= $row['lead_id'];
					$status 							= $row['status'];
					$dead 								= $row['dead_sec'];
					
					if ($wait > 65000) {$wait=0;}
					if ($talk > 65000) {$talk=0;}
					if ($dispo > 65000) {$dispo=0;}
					if ($pause > 65000) {$pause=0;}
					if ($dead > 65000) {$dead=0;}
					
					$customer 							= ($talk - $dead);
					
					if ($customer < 1) {
						$customer						= 0;
					}
					
					$TOTwait 							= ($TOTwait + $wait);
					$TOTtalk 							= ($TOTtalk + $talk);
					$TOTdispo 							= ($TOTdispo + $dispo);
					$TOTpause 							= ($TOTpause + $pause);
					$TOTdead 							= ($TOTdead + $dead);
					$TOTcustomer 						= ($TOTcustomer + $customer);
					$TOTALtime 							= ($TOTALtime + $pause + $dispo + $talk + $wait);
					
					if ( ($lead > 0) AND ((!preg_match("/NULL/",$status)) AND (strlen($status) > 0)) ) {
						$TOTcalls++;
					}
					
					$user_found							= 0;
					
					if ($uc < 1) {
						$Suser[$uc] 					= $user;
						$uc++;
					}
					
					$m									= 0;
					
					while ( ($m < $uc) AND ($m < 50000) ) {
						if ($user == $Suser[$m]) {
							$user_found++;
							$Swait[$m] 					= ($Swait[$m] + $wait);
							$Stalk[$m] 					= ($Stalk[$m] + $talk);
							$Sdispo[$m] 				= ($Sdispo[$m] + $dispo);
							$Spause[$m] 				= ($Spause[$m] + $pause);
							$Sdead[$m] 					= ($Sdead[$m] + $dead);
							$Scustomer[$m] 				= ($Scustomer[$m] + $customer);
							
							if ( ($lead > 0) AND ((!preg_match("/NULL/",$status)) AND (strlen($status) > 0)) ) {
								$Scalls[$m]++;
							}
						}
						
						$m++;
					}
					
					if ($user_found < 1) {
						$Scalls[$uc] 					= 0;
						$Suser[$uc] 					= $user;
						$Swait[$uc] 					= $wait;
						$Stalk[$uc] 					= $talk;
						$Sdispo[$uc] 					= $dispo;
						$Spause[$uc] 					= $pause;
						$Sdead[$uc] 					= $dead;
						$Scustomer[$uc] 				= $customer;
						
						if ($lead > 0) {
							$Scalls[$uc]++;
						}
						
						$uc++;
					}
				}*/
				//# END Gather all agent time records AND parse through them in PHP to save on DB load
			
				//////////////////////////////////////
				//# END gathering information FROM the database section
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
				$AUTOLOGOUTflag							= 0;
				$m								= 0;
				$rowId								= 1;
				
				while ( ($m < $uc) AND ($m < 50000) ) {
					$SstatusesHTML						= "";
					$SstatusesFILE						= "";
					$Stime[$m] 						= ($Swait[$m] + $Stalk[$m] + $Sdispo[$m] + $Spause[$m]);
					$RAWuser 						= $Suser[$m];
					$RAWcalls 						= $Scalls[$m];
					$RAWtimeSEC 						= $Stime[$m];
			
					$Swait[$m] 						= gmdate('H:i:s', $Swait[$m]); 
					$Stalk[$m] 						= gmdate('H:i:s', $Stalk[$m]); 
					$Sdispo[$m] 						= gmdate('H:i:s', $Sdispo[$m]); 
					$Spause[$m] 						= gmdate('H:i:s', $Spause[$m]); 
					$Sdead[$m] 						= gmdate('H:i:s', $Sdead[$m]); 
					$Scustomer[$m] 						= gmdate('H:i:s', $Scustomer[$m]); 
					$Stime[$m] 						= gmdate('H:i:s', $Stime[$m]); 
			
					$RAWtime 						= $Stime[$m];
					$RAWwait 						= $Swait[$m];
					$RAWtalk 						= $Stalk[$m];
					$RAWdispo						= $Sdispo[$m];
					$RAWpause						= $Spause[$m];
					$RAWdead 						= $Sdead[$m];
					$RAWcustomer 						= $Scustomer[$m];
			
					$n							= 0;
					$user_name_found					= 0;
					
					while ($n < $user_ct) {
						if ($Suser[$m] == $ULuser[$n]) {
							$user_name_found++;
							$RAWname 					= $ULname[$n];
							$Sname[$m] 					= $ULname[$n];
						}
						
						$n++;
					}
					
					if ($user_name_found < 1) {
						$RAWname 						= "NOT IN SYSTEM";
						$Sname[$m] 						= $RAWname;
					}
			
					$n 							= 0;
					$punches_found						= 0;
					
					while ($n < $punches_to_print) {
						if ($Suser[$m] == $TCuser[$n]) {
							$punches_found++;
							$RAWtimeTCsec					= $TCtime[$n];
							$TOTtimeTC 					= ($TOTtimeTC + $TCtime[$n]);
							$StimeTC[$m] 					= gmdate('H:i:s', $TCtime[$n]); 
							$RAWtimeTC 					= $StimeTC[$m];
							$StimeTC[$m] 					= sprintf("%10s", $StimeTC[$m]);
						}
						
						$n++;
					}
					
					if ($punches_found < 1) {
						$RAWtimeTCsec 					= "0";
						$StimeTC[$m] 					= "0:00"; 
						$RAWtimeTC 					= $StimeTC[$m];
						$StimeTC[$m] 					= sprintf("%10s", $StimeTC[$m]);
					}
			
					// Check if the user had an AUTOLOGOUT timeclock event during the time period
					$TCuserAUTOLOGOUT 					= ' ';
					/*$query 								= "
						SELECT COUNT(*) as cnt FROM vicidial_timeclock_log 
						WHERE event='AUTOLOGOUT' AND user = '$Suser[$m]' 
						AND date_format(event_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'
					";
					
					$timeclock_ct 						= $astDB->rawQuery($query);*/
									
					$timeclock_ct						= $astDB						
						->where("event", "AUTOLOGOUT")
						->where("user", $user[$m])
						->where("date_format(event_date, '%Y-%m-%d %H:%i:%s')", array($fromDate, $toDate), "BETWEEN")
						->getValue("vicidial_timeclock_log", "count(*)");
						
					if ($timeclock_ct > 0) {
						$TCuserAUTOLOGOUT 				= '*';
						$AUTOLOGOUTflag++;
					}
			
					// BEGIN loop through each status //
					$n							= 0;
					
					while ($n < $sub_status_count) {
						$Sstatus						= $sub_statusesARY[$n];
						$SstatusTXT						= "";
						
						// BEGIN loop through each stat line //
						$i						= 0;
						$status_found					= 0;
						
						while ( ($i < $pause_sec_ct) AND ($status_found < 1) ) {
							if ( ($Suser[$m] == $PCuser[$i]) AND ($Sstatus == $sub_status[$i]) ) {
								$USERcodePAUSE_MS 		= gmdate('H:i:s', $PCpause_sec[$i]);
								
								if (strlen($USERcodePAUSE_MS)<1) {
									$USERcodePAUSE_MS	= '0';
								}
								
								$pfUSERcodePAUSE_MS 		= sprintf("%10s", $USERcodePAUSE_MS);
	
								$SstatusesFILE 			.= ",$USERcodePAUSE_MS";
								//$sub_statusesTOP[$m]
								$Sstatuses[$m] 			.= "$USERcodePAUSE_MS";
								$status_found++;
							}
								
							$i++;
						}
						
						if ($status_found < 1) {
							$SstatusesFILE 				.= ",0:00";
							//$Sstatuses[$m] .= " 0:00";
						}
						// END loop through each stat line //
						
						$n++;
						
						if (!empty($Sstatuses[$m])) {
							$Sstatuses[$m] 				.= ",";
						}
					}

					// END loop through each status //					
					if (is_null($Scalls[$m])) {
						$Scalls[$m] 					= 0;
					}

					$Toutput 						= array(
						"name" 							=> $Sname[$m], 
						"user" 							=> $Suser[$m], 
						"number_of_calls" 					=> $Scalls[$m], 
						"agent_time" 						=> $Stime[$m], 
						"wait_time" 						=> $Swait[$m], 
						"talk_time" 						=> $Stalk[$m], 
						"dispo_time" 						=> $Sdispo[$m], 
						"pause_time" 						=> $Spause[$m], 
						"wrap_up" 						=> $Sdead[$m], 
						"customer_time" 					=> $Scustomer[$m]
					);
			
					$Sstatuses[$m] 						= rtrim( $Sstatuses[$m], ",");
					
					$Boutput 						= array(
						"rowID" 						=> $rowId, 
						"name" 							=> $Sname[$m], 
						"statuses" 						=> $Sstatuses[$m]
					);
					
					$BoutputFile 						= array(
						"statuses" 						=> $Sstatuses[$m]
					);

					$TOPsorted_output[$m] 				= $Toutput;
					$BOTsorted_output[$m] 				= $Boutput;
					$TOPsorted_outputFILE[$m] 			= array_merge($Toutput, $BoutputFile);
			
					if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) {
						if ($file_download > 0) {
							$file_output 				.= "$fileToutput";
						}
					}
					
					if ($TOPsortMAX < $TOPsortTALLY[$m]) {
						$TOPsortMAX 					= $TOPsortTALLY[$m];
					}
			
					$m++;
					$rowId++;
				}
					//# END loop through each user formatting data for output
								
				$TOT_AGENTS 							= 'AGENTS: '.$m;
				// 	// BEGIN sort through output to display properly //
				if ( ($TOT_AGENTS > 0) AND (preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) ) {
					if (preg_match("/ID/",$stage)) {
						sort($TOPsort, SORT_NUMERIC);
					}
					
					if (preg_match("/TIME|LEADS|TCLOCK/",$stage)) {
						rsort($TOPsort, SORT_NUMERIC);
					}
					
					if (preg_match("/NAME/",$stage)) {
						rsort($TOPsort, SORT_STRING);
					}
			
					$m							= 0;
					
					while ($m < $k) {
						$sort_split 					= explode("-----",$TOPsort[$m]);
						$i 								= $sort_split[1];
						$sort_order[$m] 				= "$i";
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
				$SUMstatusesHTML						= "";
				//$SUMstatusesFILE						= "";
				$TOTtotPAUSE 							= 0;
				$n								= 0;
				
				while ($n < $sub_status_count) {
					$Scalls								= 0;
					$Sstatus=$sub_statusesARY[$n];
					$SUMstatusTXT							= "";
					// BEGIN loop through each stat line //
					$i								= 0; 
					$status_found							= 0;
					
					while ($i < $pause_sec_ct) {
						if ($Sstatus == "$sub_status[$i]") {
							$Scalls 					= ($Scalls + $PCpause_sec[$i]);
							$status_found++;
						}
						
						$i++;
					}
					// END loop through each stat line //
					if ($status_found < 1) {
						$SUMstatuses[$n] 				= "00:00:00";
					} else {
						$TOTtotPAUSE 					= ($TOTtotPAUSE + $Scalls);
			
						//$USERsumstatPAUSE_MS 			= gmdate('H:i:s', $Scalls);
						$USERsumstatPAUSE_MS			= convert($Scalls); 
						$pfUSERsumstatPAUSE_MS 			= sprintf("%11s", $USERsumstatPAUSE_MS);
	
						//$SUMstatusesFILE .= ",$USERsumstatPAUSE_MS";
						$SUMstatuses[$n] 				= $USERsumstatPAUSE_MS;
					}
					
					$n++;
				}
				// END loop through each status //
			
				// call function to calculate AND print dialable leads
				/*$TOTwait 								= gmdate('H:i:s', $TOTwait);
				$TOTtalk 								= gmdate('H:i:s', $TOTtalk);
				$TOTdispo 								= gmdate('H:i:s', $TOTdispo);
				$TOTpause 								= gmdate('H:i:s', $TOTpause);
				$TOTdead 								= gmdate('H:i:s', $TOTdead);
				$TOTcustomer 								= gmdate('H:i:s', $TOTcustomer);
				$TOTALtime 								= gmdate('H:i:s', $TOTALtime);
				$TOTtimeTC 								= gmdate('H:i:s', $TOTtimeTC);*/

				$TOTwait                                                                = convert($TOTwait);
                                $TOTtalk                                                                = convert($TOTtalk);
                                $TOTdispo                                                               = convert($TOTdispo);
                                $TOTpause                                                               = convert($TOTpause);
                                $TOTdead                                                                = convert($TOTdead);
                                $TOTcustomer                                                    	= convert($TOTcustomer);
                                $TOTALtime                                                              = convert($TOTALtime);
                                $TOTtimeTC                                                              = convert($TOTtimeTC);
				
				$apiresults 							= array(
					"result" 							=> "success", 
					"TOPsorted_output" 						=> $TOPsorted_output, 
					"sub_statusesTOP" 						=> $sub_statusesTOP, 
					"BOTsorted_output" 						=> $BOTsorted_output, 
					"SUMstatuses" 							=> $SUMstatuses, 
					"TOTwait" 							=> $TOTwait, 
					"TOTtalk" 							=> $TOTtalk, 
					"TOTdispo" 							=> $TOTdispo, 
					"TOTpause" 							=> $TOTpause, 
					"TOTdead" 							=> $TOTdead, 
					"TOTcustomer" 							=> $TOTcustomer, 
					"TOTALtime" 							=> $TOTALtime, 
					"TOTtimeTC" 							=> $TOTtimeTC, 
					"TOT_AGENTS" 							=> $TOT_AGENTS, 
					"TOTcalls" 							=> $TOTcalls, 
					"FileExport" 							=> $TOPsorted_outputFILE
				);
				
				return $apiresults;				
			}
			
			// Agent Performance Detail
			if ($pageTitle == "agent_pdetail") {
				$statusesFILE							= "";
				$statuses								= '-';
				$statusesARY[0]							= "";
				$j										= 0;
				$users									= '-';
				$usersARY[0]							= "";
				$user_namesARY[0]						= "";
				$k										= 0;
				//if (inner_checkIfTenant($log_group, $goDB))
				if ($log_group !== "ADMIN") {
					$log_groupSQL 						= "AND vicidial_users.user_group='$log_group'";
				}
				
				if ($date_diff <= 0) {
					$filters 							= "AND pause_sec < 65000 AND wait_sec<65000 AND talk_sec<65000 AND dispo_sec<65000 ";
				}
				
				$perfdetails_sql 						= "SELECT count(*) as calls,sum(talk_sec) as talk,full_name,vicidial_users.user as user,sum(pause_sec) as pause_sec,sum(wait_sec) as wait_sec,sum(dispo_sec) as dispo_sec,status,sum(dead_sec) as dead_sec FROM vicidial_users,vicidial_agent_log WHERE date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' AND vicidial_users.user=vicidial_agent_log.user $log_groupSQL AND campaign_id = '$campaignID' GROUP BY user,full_name,status order by full_name,user,status desc limit 500000";
				$query 									= $perfdetails_sql;
				
				$rows_to_print 							= $astDB->rawQuery($query);
				
				$i										= 0;
				
				while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {
					$calls[$i] 							= $row['calls'];
					$talk_sec[$i] 						= $row['talk'];
					$full_name[$i] 						= $row['full_name'];
					$user[$i] 							= $row['user'];
					$pause_sec[$i] 						= $row['pause_sec'];
					$wait_sec[$i] 						= $row['wait_sec'];
					$dispo_sec[$i] 						= $row['dispo_sec'];
					$status[$i] 						= $row['status'];
					$dead_sec[$i] 						= $row['dead_sec'];
					$customer_sec[$i] 					= ($talk_sec[$i] - $dead_sec[$i]);
					
					if ($customer_sec[$i] < 1) {
						$customer_sec[$i]				= 0;
					}
					
					if ( (!preg_match("/-$status[$i]-/", $statuses)) AND (strlen($status[$i])>0) ) {
						$statusesFILE 					.= ",$status[$i]";
						$statuses 						.= "$status[$i]-";
						$SUMstatuses 					.= "$status[$i] ";
						$statusesARY[$j] 				= $status[$i];
							
						## getting status name
						$var_status 					= $status[$i];
						
						# in default statuses
						$query 							= "
							SELECT status_name FROM vicidial_statuses 
							WHERE status = '$var_status' LIMIT 1;
						";
						
						if ($query) {
							$fetch_statusname 			= $astDB->rawQuery($query);
						}
						
						if (!isset($fetch_statusname) || $fetch_statusname == NULL) {
							# in custom statuses
							$query 						= "
								SELECT status_name FROM vicidial_campaign_statuses 
								WHERE status = '$var_status' LIMIT 1;
							";
							
							$fetch_statusname 			= $astDB->rawQuery($query);
						}
						
						$legend[] 						= $status[$i]." = ".$fetch_statusname['status_name'];
						
						## end of getting status name						
						$SstatusesTOP 					.= "<th> $status[$i] </th>";
						$j++;
					}
					
					if (!preg_match("/-$user[$i]-/", $users)) {
						$users 							.= "$user[$i]-";
						$usersARY[$k] 					= $user[$i];
						$user_namesARY[$k] 				= $full_name[$i];
						$k++;
					}
					
					$i++;
				}
								
				if ($file_download > 0) {
					$file_output  						= "CAMPAIGN,$campaignID - ".$resultu->campaign_name."\n";
					$file_output 						.= "DATE RANGE,$fromDate TO $toDate\n\n";
					$file_output 						.= "USER NAME,ID,CALLS,AGENT TIME,PAUSE,PAUSE AVG,WAIT,WAIT AVG,TALK,TALK AVG,DISPO,DISPO AVG,WRAPUP,WRAPUP AVG,CUSTOMER,CUST AVG $statusesFILE\n";
				}
				
				// BEGIN loop through each user //
				$m										= 0;
				
				while ($m < $k) {
					$Suser								= $usersARY[$m];
					$Sfull_name							= $user_namesARY[$m];
					$Stime								= 0;
					$Scalls								= 0;
					$Stalk_sec							= 0;
					$Spause_sec							= 0;
					$Swait_sec							= 0;
					$Sdispo_sec							= 0;
					$Sdead_sec							= 0;
					$Scustomer_sec						= 0;
					$SstatusesHTML						= "";
					$SstatusesFILE						= "";
				
					// BEGIN loop through each status //
					$n									= 0;
					
					while ($n < $j) {
						$Sstatus						= $statusesARY[$n];
						$SstatusTXT						= "";
						// BEGIN loop through each stat line //
						$i								= 0; 
						$status_found					= 0;
						
						while ($i < $rows_to_print) {
							if ( ($Suser=="$user[$i]") AND ($Sstatus == "$status[$i]") ) {
								$Scalls 				= ($Scalls + $calls[$i]);
								$Stalk_sec 				= ($Stalk_sec + $talk_sec[$i]);
								$Spause_sec 			= ($Spause_sec + $pause_sec[$i]);
								$Swait_sec 				= ($Swait_sec + $wait_sec[$i]);
								$Sdispo_sec 			= ($Sdispo_sec + $dispo_sec[$i]);
								$Sdead_sec 				= ($Sdead_sec + $dead_sec[$i]);
								$Scustomer_sec 			= ($Scustomer_sec + $customer_sec[$i]);
								$SstatusesFILE 			.= ",$calls[$i]";
								$SstatusesMID[$m] 		.= "<td> $calls[$i] </td>";
								$status_found++;
							}
							
							$i++;
						}
						
						if ($status_found < 1) {
							$SstatusesFILE 				.= ",0";
							$SstatusesMID[$m] 			.= "<td> 0 </td>";
						}
						// END loop through each stat line //
						$n++;
					}
					
					// END loop through each status //
					$Stime 								= ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
					$TOTcalls							= ($TOTcalls + $Scalls);
					$TOTtime							= ($TOTtime + $Stime);
					$TOTtotTALK							= ($TOTtotTALK + $Stalk_sec);
					$TOTtotWAIT							= ($TOTtotWAIT + $Swait_sec);
					$TOTtotPAUSE						= ($TOTtotPAUSE + $Spause_sec);
					$TOTtotDISPO						= ($TOTtotDISPO + $Sdispo_sec);
					$TOTtotDEAD							= ($TOTtotDEAD + $Sdead_sec);
					$TOTtotCUSTOMER						= ($TOTtotCUSTOMER + $Scustomer_sec);
					$Stime 								= ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
					
					if ( ($Scalls > 0) AND ($Stalk_sec > 0) ) {
						$Stalk_avg 						= ($Stalk_sec/$Scalls);
					} else {
						$Stalk_avg						= 0;					
					}
					
					if ( ($Scalls > 0) AND ($Spause_sec > 0) ) {
						$Spause_avg 					= ($Spause_sec/$Scalls);
					} else {
						$Spause_avg						= 0;
					}
					
					if ( ($Scalls > 0) AND ($Swait_sec > 0) ) {
						$Swait_avg 						= ($Swait_sec/$Scalls);
					} else {
						$Swait_avg						= 0;
					}
					
					if ( ($Scalls > 0) AND ($Sdispo_sec > 0) ) {
						$Sdispo_avg 					= ($Sdispo_sec/$Scalls); 
					} else {
						$Sdispo_avg						= 0;
					}
					
					if ( ($Scalls > 0) AND ($Sdead_sec > 0) ) {
						$Sdead_avg 						= ($Sdead_sec/$Scalls);
					} else {
						$Sdead_avg						= 0;
					}
					
					if ( ($Scalls > 0) AND ($Scustomer_sec > 0) ) {
						$Scustomer_avg 					= ($Scustomer_sec/$Scalls);
					} else {
						$Scustomer_avg					= 0;
					}
				
					$RAWuser 							= $Suser;
					$RAWcalls 							= $Scalls;
				
					$pfUSERtime_MS 						= gmdate('H:i:s', $Stime); 
					$pfUSERtotTALK_MS					= gmdate('H:i:s', $Stalk_sec); 
					$pfUSERavgTALK_MS					= gmdate('H:i:s', $Stalk_avg);
					$pfUSERtotPAUSE_MS 					= gmdate('H:i:s', $Spause_sec);
					$pfUSERavgPAUSE_MS 					= gmdate('H:i:s', $Spause_avg);
					$pfUSERtotWAIT_MS					= gmdate('H:i:s', $Swait_sec); 
					$pfUSERavgWAIT_MS					= gmdate('H:i:s', $Swait_avg); 
					$pfUSERtotDISPO_MS 					= gmdate('H:i:s', $Sdispo_sec); 
					$pfUSERavgDISPO_MS 					= gmdate('H:i:s', $Sdispo_avg); 
					$pfUSERtotDEAD_MS					= gmdate('H:i:s', $Sdead_sec); 
					$pfUSERavgDEAD_MS					= gmdate('H:i:s', $Sdead_avg); 
					$pfUSERtotCUSTOMER_MS 				= gmdate('H:i:s', $Scustomer_sec); 
					$pfUSERavgCUSTOMER_MS 				= gmdate('H:i:s', $Scustomer_avg); 
				
					$PAUSEtotal[$m] 					= $pfUSERtotPAUSE_MS;
				
					if ($file_download > 0) {
						$fileToutput 					= "$Sfull_name,=\"$Suser\",$Scalls,$pfUSERtime_MS,$pfUSERtotPAUSE_MS,$pfUSERavgPAUSE_MS,$pfUSERtotWAIT_MS,$pfUSERavgWAIT_MS,$pfUSERtotTALK_MS,$pfUSERavgTALK_MS,$pfUSERtotDISPO_MS,$pfUSERavgDISPO_MS,$pfUSERtotDEAD_MS,$pfUSERavgDEAD_MS,$pfUSERtotCUSTOMER_MS,$pfUSERavgCUSTOMER_MS$SstatusesFILE\n";
					}
					
					if ($x == 0) {
						$bgcolor 						= "#E0F8E0";
						$x								= 1;
					} else {
						$bgcolor 						= "#EFFBEF";
						$x								= 0;
					}
					
					$Toutput 							= "<tr>
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
				
					$Moutput 							= "<tr>
						<td> $Sfull_name </td>
						$SstatusesMID[$m]
						</tr>";
				
					$TOPsorted_output[$m] 				= $Toutput;
					$MIDsorted_output[$m] 				= $Moutput;
					$TOPsorted_outputFILE[$m] 			= $fileToutput;
				
					if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) {
						if ($file_download > 0) {
							$file_output 				.= "$fileToutput";
						}
				
						$m++;
					}
				}
				// END loop through each user //
				
				// BEGIN sort through output to display properly //
				if (preg_match("/ID|TIME|LEADS/",$stage)) {
					if (preg_match("/ID/",$stage)) {
						sort($TOPsort, SORT_NUMERIC);
					}
					
					if (preg_match("/TIME|LEADS/",$stage)) {
						rsort($TOPsort, SORT_NUMERIC);
					}
				
					$m									= 0;
					
					while ($m < $k) {
						$sort_split 					= explode("-----",$TOPsort[$m]);
						$i 								= $sort_split[1];
						$sort_order[$m] 				= "$i";
						
						if ($file_download > 0) {
							$file_output 				.= "$TOPsorted_outputFILE[$i]";
						}
						
						$m++;
					}
				}
				// END sort through output to display properly //												
				//## LAST LINE FORMATTING ////##
				// BEGIN loop through each status //
				$SUMstatusesHTML						= "";
				$SUMstatusesFILE						= "";
				$n										= 0;
				
				while ($n < $j) {
					$Scalls								= 0;
					$Sstatus							= $statusesARY[$n];
					$SUMstatusTXT						= "";
					// BEGIN loop through each stat line //
					$i									= 0; 
					$status_found						= 0;
					
					while ($i < $rows_to_print) {
						if ($Sstatus == "$status[$i]") {
							$Scalls 					= ($Scalls + $calls[$i]);
							$status_found++;
						}
						
						$i++;
					}
					
					// END loop through each stat line //
					if ($status_found < 1) {
						$SUMstatusesFILE 				.= ",0";
						$SstatusesSUM 					.= "<th> 0 </th>";
					} else {
						$SUMstatusesFILE 				.= ",$Scalls";
						$SstatusesSUM 					.= "<th> $Scalls </th>";
					}
					
					$n++;
				}
				// END loop through each status //
				$TOT_AGENTS 							= '<th nowrap>AGENTS: '.$m.'</th>';
				
				if ($TOTtotTALK < 1) {
					$TOTavgTALK 						= '0';
				} else {
					$TOTavgTALK 						= ($TOTtotTALK / $TOTcalls);
				}
				
				if ($TOTtotDISPO < 1) {
					$TOTavgDISPO 						= '0';
				} else {
					$TOTavgDISPO 						= ($TOTtotDISPO / $TOTcalls);
				}
				
				if ($TOTtotDEAD < 1) {
					$TOTavgDEAD 						= '0';
				} else {
					$TOTavgDEAD 						= ($TOTtotDEAD / $TOTcalls);
				}
				
				if ($TOTtotPAUSE < 1) {
					$TOTavgPAUSE 						= '0';
				} else {
					$TOTavgPAUSE 						= ($TOTtotPAUSE / $TOTcalls);
				}
				
				if ($TOTtotWAIT < 1) {
					$TOTavgWAIT 						= '0';
				} else {
					$TOTavgWAIT 						= ($TOTtotWAIT / $TOTcalls);
				}
				
				if ($TOTtotCUSTOMER < 1) {
					$TOTavgCUSTOMER 					= '0';
				} else {
					$TOTavgCUSTOMER 					= ($TOTtotCUSTOMER / $TOTcalls);
				}
				
				$TOTcalls 								= '<th nowrap>'.$TOTcalls.'</th>';
				$TOTtime_MS 							= '<th nowrap>'.gmdate('H:i:s', $TOTtime).'</th>'; 
				$TOTtotTALK_MS 							= '<th nowrap>'.gmdate('H:i:s', $TOTtotTALK).'</th>'; 
				$TOTtotDISPO_MS 						= '<th nowrap>'.gmdate('H:i:s', $TOTtotDISPO).'</th>'; 
				$TOTtotDEAD_MS 							= '<th nowrap>'.gmdate('H:i:s', $TOTtotDEAD).'</th>'; 
				$TOTtotPAUSE_MS 						= '<th nowrap>'.gmdate('H:i:s', $TOTtotPAUSE).'</th>'; 
				$TOTtotWAIT_MS 							= '<th nowrap>'.gmdate('H:i:s', $TOTtotWAIT).'</th>'; 
				$TOTtotCUSTOMER_MS 						= '<th nowrap>'.gmdate('H:i:s', $TOTtotCUSTOMER).'</th>'; 
				$TOTavgTALK_MS 							= '<th nowrap>'.gmdate('H:i:s', $TOTavgTALK).'</th>'; 
				$TOTavgDISPO_MS 						= '<th nowrap>'.gmdate('H:i:s', $TOTavgDISPO).'</th>'; 
				$TOTavgDEAD_MS 							= '<th nowrap>'.gmdate('H:i:s', $TOTavgDEAD).'</th>'; 
				$TOTavgPAUSE_MS 						= '<th nowrap>'.gmdate('H:i:s', $TOTavgPAUSE).'</th>'; 
				$TOTavgWAIT_MS 							= '<th nowrap>'.gmdate('H:i:s', $TOTavgWAIT).'</th>'; 
				$TOTavgCUSTOMER_MS 						= '<th nowrap>'.gmdate('H:i:s', $TOTavgCUSTOMER).'</th>'; 
				
				if ($file_download > 0) {
					$file_output 						.= "TOTAL AGENTS: $TOT_AGENTS,$TOTcalls,$TOTtime_MS,$TOTtotPAUSE_MS,$TOTavgPAUSE_MS,$TOTtotWAIT_MS,$TOTavgWAIT_MS,$TOTtotTALK_MS,$TOTavgTALK_MS,$TOTtotDISPO_MS,$TOTavgDISPO_MS,$TOTtotDEAD_MS,$TOTavgDEAD_MS,$TOTtotCUSTOMER_MS,$TOTavgCUSTOMER_MS$SUMstatusesFILE\n";
				}
				
				$sub_statuses							= '-';
				$sub_statusesTXT						= "";
				$sub_statusesFILE						= "";
				$sub_statusesHEAD						= "";
				$sub_statusesHTML						= "";
				$sub_statusesARY						= $MT;
				$j										= 0;
				$PCusers								= '-';
				$PCusersARY								= $MT;
				$PCuser_namesARY						= $MT;
				$k										= 0;
				
				$query 									= "
					SELECT full_name,vicidial_users.user as user,
						sum(pause_sec) as pause_sec,sub_status,
						sum(wait_sec + talk_sec + dispo_sec) as non_pause_sec 
					FROM vicidial_users,vicidial_agent_log 
					WHERE date_format(event_time, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
					AND vicidial_users.user = vicidial_agent_log.user 
					$log_groupSQL 
					AND campaign_id = '$campaignID' 
					AND pause_sec < 65000 
					GROUP BY user,full_name,sub_status 
					order by full_name,user,sub_status desc 
					limit 1000
				";
				
				$subs_to_print 							= $astDB->rawQuery($query);				
				$i										= 0;             
				
				while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {				
					$PCfull_name[$i] 					= $row['full_name'];
					$PCuser[$i] 						= $row['user'];
					$PCpause_sec[$i] 					= $row['pause_sec'];
					$sub_status[$i] 					= $row['sub_status'];
					$PCnon_pause_sec[$i] 				= $row['non_pause_sec'];
					
					if (!preg_match("/-$sub_status[$i]-/", $sub_statuses)) {
						$sub_statuses 					.= "$sub_status[$i]-";
						$sub_statusesFILE 				.= ",$sub_status[$i]";
						$sub_statusesARY[$j] 			= $sub_status[$i];
						$SstatusesBOT 					.= "<th> $sub_status[$i] </th>";
						$j++;
					}
					
					if (!preg_match("/-$PCuser[$i]-/", $PCusers)) {
						$PCusers 						.= "$PCuser[$i]-";
						$PCusersARY[$k] 				= $PCuser[$i];
						$PCuser_namesARY[$k] 			= $PCfull_name[$i];
						$k++;
					}
					$i++;
				}
				
				if ($file_download > 0) {
					$file_output 						.= "\n\nUSER NAME,ID,TOTAL,NONPAUSE,PAUSE,$sub_statusesFILE\n";
				}
				
				// BEGIN loop through each user //
				$m										= 0;
				$Suser_ct 								= count($usersARY);
				$TOTtotNONPAUSE 						= 0;
				$TOTtotTOTAL 							= 0;
				
				while ($m < $k) {
					$d									= 0;
					
					while ($d < $Suser_ct) {
						if ($usersARY[$d] === "$PCusersARY[$m]") {
							$pcPAUSEtotal 				= $PAUSEtotal[$d];
						}
						
						$d++;
					}
					
					$Suser								= $PCusersARY[$m];
					$Sfull_name							= $PCuser_namesARY[$m];
					$Spause_sec							= 0;
					$Snon_pause_sec						= 0;
					$Stotal_sec							= 0;
					$SstatusesHTML						= "";
					$Ssub_statusesFILE					= "";
				
					// BEGIN loop through each status //
					$n									= 0;
					
					while ($n < $j) {
						$Sstatus						= $sub_statusesARY[$n];
						$SstatusTXT						= "";
						// BEGIN loop through each stat line //
						$i								= 0; 
						$status_found					= 0;
						
						while ($i < $subs_to_print) {
							if ( ($Suser == "$PCuser[$i]") AND ($Sstatus == "$sub_status[$i]") ) {
								$Spause_sec 			= ($Spause_sec + $PCpause_sec[$i]);
								$Snon_pause_sec 		= ($Snon_pause_sec + $PCnon_pause_sec[$i]);
								$Stotal_sec 			= ($Stotal_sec + $PCnon_pause_sec[$i] + $PCpause_sec[$i]);
				
								$USERcodePAUSE_MS 		= gmdate('H:i:s', $PCpause_sec[$i]); 
								$pfUSERcodePAUSE_MS 	= sprintf("%6s", $USERcodePAUSE_MS);
				
								$Ssub_statusesFILE 		.= ",$USERcodePAUSE_MS";
								$SstatusesBOTR[$m] 		.= "<td> $USERcodePAUSE_MS </td>";
								$status_found++;
							}
							
							$i++;
						}
						
						if ($status_found < 1) {
							$Ssub_statusesFILE 			.= ",0";
							$SstatusesBOTR[$m] 			.= "<td> 0:00 </td>";
						}
						// END loop through each stat line //
						$n++;
					}
					// END loop through each status //
					$TOTtotPAUSE						= ($TOTtotPAUSE + $Spause_sec);				
					$TOTtotNONPAUSE 					= ($TOTtotNONPAUSE + $Snon_pause_sec);
					$TOTtotTOTAL 						= ($TOTtotTOTAL + $Stotal_sec);
				
					$pfUSERtotPAUSE_MS					= gmdate('H:i:s', $Spause_sec); 
					$pfUSERtotNONPAUSE_MS				= gmdate('H:i:s', $Snon_pause_sec); 
					$pfUSERtotTOTAL_MS					= gmdate('H:i:s', $Stotal_sec); 
				
					if ($file_download > 0) {
						$fileToutput 					= "$Sfull_name,=\"$Suser\",$pfUSERtotTOTAL_MS,$pfUSERtotNONPAUSE_MS,$pfUSERtotPAUSE_MS,$Ssub_statusesFILE\n";
					}
					
					if ($x == 1) {
						$bgcolor 						= "#E0F8E0";
						$x								= 0;
					} else {
						$bgcolor 						= "#EFFBEF";
						$x								= 1;
					}
					
					$Boutput 							= "<tr>
						<td> $Sfull_name </td>
						<td> $Suser </td>
						<td> $pfUSERtotTOTAL_MS </td>
						<td> $pfUSERtotNONPAUSE_MS </td>
						<td> $pfUSERtotPAUSE_MS </td>
						</tr>";
				
					$BOTsorted_output[$m] 				= $Boutput;
				
					if (!preg_match("/NAME|ID|TIME|LEADS|TCLOCK/",$stage)) {
						if ($file_download > 0) {
							$file_output 				.= "$fileToutput";
						}
				
						$m++;
					}
				}
				// END loop through each user //
				
				// BEGIN sort through output to display properly //
				if (preg_match("/ID|TIME|LEADS/",$stage)) {
					$n									= 0;
					while ($n <= $m) {
						$i 								= $sort_order[$m];
						if ($file_download > 0) {
							$file_output 				.= "$TOPsorted_outputFILE[$i]";
						}
						
						$m--;
					}
				}
				// END sort through output to display properly //
				
				//## LAST LINE FORMATTING ////##
				// BEGIN loop through each status //
				$SUMstatusesHTML						= "";
				$SUMsub_statusesFILE					= "";
				$TOTtotPAUSE							= 0;
				$n										= 0;
				
				while ($n < $j) {
					$Scalls								= 0;
					$Sstatus							= $sub_statusesARY[$n];
					$SUMstatusTXT						= "";
					// BEGIN loop through each stat line //
					$i									= 0; 
					$status_found						= 0;
					
					while ($i < $subs_to_print) {
						if ($Sstatus == "$sub_status[$i]") {
							$Scalls 					= ($Scalls + $PCpause_sec[$i]);
							$status_found++;
						}
						
						$i++;
					}
					// END loop through each stat line //
					if ($status_found < 1) {
						$SUMsub_statusesFILE 			.= ",0";
						$SstatusesBSUM 					.= "<th nowrap> 0:00 </th>";
					} else {
						$TOTtotPAUSE 					= ($TOTtotPAUSE + $Scalls);				
						$USERsumstatPAUSE_MS			= gmdate('H:i:s', $Scalls); 				
						$SUMsub_statusesFILE 			.= ",$USERsumstatPAUSE_MS";
						$SstatusesBSUM 					.= "<th nowrap> $USERsumstatPAUSE_MS </th>";
					}
					
					$n++;
				}
				// END loop through each status //
				$TOT_AGENTS 							= '<th nowrap>AGENTS: '.$m.'</th>';			
				$TOTtotPAUSEB_MS 						= '<th nowrap>'.gmdate('H:i:s', $TOTtotPAUSE).'</th>'; 
				$TOTtotNONPAUSE_MS 						= '<th nowrap>'.gmdate('H:i:s', $TOTtotNONPAUSE).'</th>'; 
				$TOTtotTOTAL_MS 						= '<th nowrap>'.gmdate('H:i:s', $TOTtotTOTAL).'</th>'; 
			
				if ($file_download > 0) {
					$file_output 						.= "TOTAL AGENTS: $TOT_AGENTS,$TOTtotTOTAL_MS,$TOTtotNONPAUSE_MS,$TOTtotPAUSE_MS,$SUMsub_statusesFILE\n";
				}
				
				$apiresults 							= array(
					"result" 							=> "success",
					"TOPsorted_output" 					=> $TOPsorted_output,
					"BOTsorted_output" 					=> $BOTsorted_output,
					"TOPsorted_outputFILE"				=> $TOPsorted_outputFILE,
					"TOTwait" 							=> $TOTwait,
					"TOTtalk" 							=> $TOTtalk,
					"TOTdispo" 							=> $TOTdispo,
					"TOTpause" 							=> $TOTpause,
					"TOTdead" 							=> $TOTdead,
					"TOTcustomer" 						=> $TOTcustomer,
					"TOTALtime"							=> $TOTALtime,
					"TOTtimeTC" 						=> $TOTtimeTC,
					"sub_statusesTOP" 					=> $sub_statusesTOP,
					"SUMstatuses" 						=> $SUMstatuses,
					"TOT_AGENTS" 						=> $TOT_AGENTS,
					"TOTcalls" 							=> $TOTcalls,
					"TOTtime_MS" 						=> $TOTtime_MS, 
					"TOTtotTALK_MS"						=> $TOTtotTALK_MS, 
					"TOTtotDISPO_MS" 					=> $TOTtotDISPO_MS, 
					"TOTtotDEAD_MS"						=> $TOTtotDEAD_MS, 
					"TOTtotPAUSE_MS" 					=> $TOTtotPAUSE_MS, 
					"TOTtotWAIT_MS"						=> $TOTtotWAIT_MS, 
					"TOTtotCUSTOMER_MS" 				=> $TOTtotCUSTOMER_MS, 
					"TOTavgTALK_MS"						=> $TOTavgTALK_MS, 
					"TOTavgDISPO_MS" 					=> $TOTavgDISPO_MS, 
					"TOTavgDEAD_MS"						=> $TOTavgDEAD_MS, 
					"TOTavgPAUSE_MS" 					=> $TOTavgPAUSE_MS, 
					"TOTavgWAIT_MS" 					=> $TOTavgWAIT_MS, 
					"TOTavgCUSTOMER_MS"					=> $TOTavgCUSTOMER_MS, 
					"TOTtotTOTAL_MS" 					=> $TOTtotTOTAL_MS,
					"TOTtotNONPAUSE_MS"					=> $TOTtotNONPAUSE_MS, 
					"TOTtotPAUSEB_MS" 					=> $TOTtotPAUSEB_MS, 
					"MIDsorted_output"					=> $MIDsorted_output, 
					"SstatusesTOP" 						=> $SstatusesTOP, 
					"SstatusesSUM" 						=> $SstatusesSUM,
					"SstatusesBOT" 						=> $SstatusesBOT, 
					"SstatusesBOTR"						=> $SstatusesBOTR,
					"SstatusesBSUM"						=> $SstatusesBSUM,
					"Legend" 							=> $legend,
					"query" 							=> $perfdetails_sql
				);
				
				return $apiresults;
			}
			
			//Dial Statuses Summary
			if ($pageTitle == "dispo") {
				$list_ids[0] 						= "ALL";
				$total_all							= ($list_ids[0] == "ALL") ? 'ALL List IDs under '.$campaignID : 'List ID(s): '.implode(',',$list_ids);
				
				if (isset($list_ids) && $list_ids[0] == "ALL") {
					/*$query 							= "
						SELECT list_id FROM vicidial_lists 
						WHERE campaign_id = '$campaignID' 
						ORDER BY list_id
					";*/
	
					$qlistid						= $astDB
						->where("campaign_id", $campaignID)
						->orderBy("list_id")
						->get("vicidial_lists", NULL, "list_id");
						
					if ($astDB->count > 0) {
						foreach ($qlistid as $row) {
							$list_ids[]				= $row['list_id'];
						}
					}
					
					//$i								= 0;
					
					/*while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {
						$list_ids[$i]				= $row['list_id'];
						$i++;
					}*/
				}
				
				$list 								= "'".implode("','",$list_ids)."'";
				// grab names of global statuses AND statuses in the SELECTed campaign
				/*$query 								= "
					SELECT status,status_name FROM vicidial_statuses
					order by status
				";
				$statuses_to_print 					= $astDB->rawQuery($query);*/
				
				$qsstatuses							= $astDB
					->orderBy("status")
					->get("vicidial_statuses", NULL, array("status", "status_name"));
				
				if ($astDB->count > 0) {
					foreach ($qsstatuses as $row) {
						$statuses_list[$row['status']] 	= $row['status_name'];
					}
				}
				
				$qcstatuses							= $astDB
					->where("campaign_id", $campaignID)
					->get("vicidial_campaign_statuses", NULL, array("status", "status_name"));
				
				if ($astDB->count > 0) {
					foreach ($qcstatuses as $row) {
						$statuses_list[$row['status']] 	= $row['status_name'];
					}
				}			
				
				/*while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {
					$statuses_list[$row['status']] 	= $row['status_name'];
				}*/
		
				/*$query 								= "
					SELECT status, status_name FROM vicidial_campaign_statuses 
					WHERE campaign_id = '$campaignID'; 
				";
				
				$query_list = $astDB->rawQuery($query);
				
				while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {
					$query_name 					= "
						SELECT status, status_name FROM vicidial_campaign_statuses 
						WHERE campaign_id = '$campaignID' AND list_id
					";
					
					$statuses_list[$row['status']] 	= $row['status_name'];
				}
				# end grab status names*/
				
				$leads_in_list 						= 0;
				$leads_in_list_N 					= 0;
				$leads_in_list_Y 					= 0;
				
				/*$queryx 							= "
					SELECT status, if (called_count >= 10, 10, called_count) as called_count, count(*) as count FROM vicidial_list 
					WHERE list_id IN(".$list.") AND status NOT IN('DC','DNCC','XDROP') 
					GROUP BY status, if (called_count >= 10, 10, called_count) 
					order by status,called_count
				";*/
				
				$cols								= array(
					"status", 
					"if (called_count >= 10, 10, called_count) as called_count", 
					"count(*) as count"
				);
				
				$queryx								= $astDB
					->where("list_id", $list_ids, "IN")
					->where("status", array("DC", "DNCC", "XDROP"), "NOT IN")
					->groupBy("status, if (called_count >= 10, 10, called_count)")
					->orderBy("status, called_count")
					->get("vicidial_list", NULL, $cols);
				
				//$query 								= $astDB->rawQuery($queryx);
				
				$sts								= 0;
				$first_row							= 1;
				$all_called_first					= 1000;
				$all_called_last					= 0;				
				$o									= 0;
				
				if ($astDB->count >0) {
					foreach ($queryx as $row) {
						$leads_in_list 				= ($leads_in_list + $row['count']);
						$count_statuses[]			= $row['status'];
						$count_called[]				= $row['called_count'];
						$count_count[]				= $row['count'];
						
						$all_called_count[$row['called_count']] = ($all_called_count[$row['called_count']] + $row['count']);					
						
						if ( (strlen($status) < 1) or ($status != $row['status']) ) {
							if ($first_row) {
								$first_row			= 0;
							}
							
							$status[] 				= $row['status'];
							$status_called_first[] 	= $row['called_count'];
							
							if ($status_called_first < $all_called_first) {
								$all_called_first 	= $status_called_first;
							}
						}
						
						$leads_in_sts[] 			= ($leads_in_sts + $row['count']);
						$status_called_last[] 		= $row['called_count'];
						
						if ($status_called_last > $all_called_last) {
							$all_called_last 		= $status_called_last;
						}						
					}
				}
				
				/*while ($row = $astDB->rawQuery($query, MYSQLI_ASSOC)) {
					$leads_in_list 					= ($leads_in_list + $row['count']);
					$count_statuses[$o]				= $row['status'];
					$count_called[$o]				= $row['called_count'];
					$count_count[$o]				= $row['count'];
					$all_called_count[$row['called_count']] = ($all_called_count[$row['called_count']] + $row['count']);
					
					if ( (strlen($status[$sts]) < 1) or ($status[$sts] != $row['status']) ) {
						if ($first_row) {
							$first_row				= 0;
						} else {
							$sts++;
						}
						
						$status[$sts] 				= $row['status'];
						$status_called_first[$sts] 	= $row['called_count'];
						
						if ($status_called_first[$sts] < $all_called_first) {
							$all_called_first 		= $status_called_first[$sts];
						}
					}
					
					$leads_in_sts[$sts] 			= ($leads_in_sts[$sts] + $row['count']);
					$status_called_last[$sts] 		= $row['called_count'];
					
					if ($status_called_last[$sts] > $all_called_last) {
						$all_called_last 			= $status_called_last[$sts];
					}
					
					$o++;
				}*/
							
				$TOPsorted_output 					= "<center>\n";
				$TOPsorted_output 					.= "<TABLE class='table table-striped table-bordered table-hover' id='dispo'>\n";
				$TOPsorted_output 					.= "
					<thead>
					<tr>
					<th>STATUS</th>
					<th>Status Name</th>
				";
					
				$first 								= $all_called_first;
				
				while ($first <= $all_called_last) {
					if ($first >= 10) {
						$Fplus						="+";
					} else {
						$Fplus						= "";
					}
					
					$TOPsorted_output 				.= "<th> $first$Fplus </th>";
					$first++;
				}
				
				$TOPsorted_output 					.= "
					<th nowrap> SUB-TOTAL </th>
						</tr></thead><tbody>\n
					";
		
				$sts								= 0;
				$statuses_called_to_print 			= count($status);
				
				while ($statuses_called_to_print > $sts) {
					$Pstatus 						= $status[$sts];					
					$TOPsorted_output 				.= "
						<tr>
							<td nowrap> ".$Pstatus." </td>
							<td nowrap> ".$statuses_list[$Pstatus]." </td>
						";
		
					$first 							= $all_called_first;
					
					while ($first <= $all_called_last) {							
						$called_printed				= 0;
						$o							= 0;
						
						while ($status_called_to_print > $o) {
							if ( ($count_statuses[$o] == "$Pstatus") AND ($count_called[$o] == "$first") ) {
								$called_printed++;
								$TOPsorted_output 	.= "<td nowrap> ".$count_count[$o]." </td>";
							}
		
							$o++;
						}
						
						if (!$called_printed) {
							$TOPsorted_output 		.= "<td nowrap> 0 </td>";
						}
						
						$first++;
					}
					
					$TOPsorted_output 				.= "<td nowrap> ".$leads_in_sts[$sts]." </td></tr>\n\n";
					$sts++;
				}
		
				$TOPsorted_output 					.= "
					</tbody>
						<tfoot><tr class='warning'>
						<th nowrap colspan='2'> Total For <i>".$total_all."</i> </th>
					";
					
				$first 								= $all_called_first;
				
				while ($first <= $all_called_last) {
					if ($all_called_count[$first]) {
						$TOPsorted_output 			.= "
							<th> $all_called_count[$first] </th>
						";
					} else {
						$TOPsorted_output 			.= "
							<td> 0 </td>
						";
					}
					
					$first++;
				}
				
				$TOPsorted_output 					.= "<th>$leads_in_list</th></tr>\n";				
				$TOPsorted_output 					.= "</tfoot></table>";
				
				$queryforBot 						= "
					SELECT DISTINCT gmt_offset_now FROM vicidial_list 
					WHERE list_id IN (".$list.");
				";
				
				$sqlBot 							= $queryforBot;
				$numBot 							= $astDB->rawQuery($sqlBot);
				
				$BOTsorted_output 					= "<TABLE class='table table-striped table-bordered table-hover' id='dispo_bot'>";
				$BOTsorted_output 					.= "
					<tr><thead>
						<th>Timezone</th>
						<th>Called</th>
						<th>Not Called</th>	
						<thead></tr><tbody>
					";
					
				if ($numBot > 0) {
					while ($rowBot = $astDB->rawQuery($sqlBot)) {
						$timezone_now 				= $rowBot['gmt_offset_now'];
						$CALLEDsql 					= "
							SELECT count(gmt_offset_now) as Clead_count FROM vicidial_list 
							WHERE list_id IN (".$list.") 
							AND status != 'NEW' AND gmt_offset_now = '$timezone_now'
						";
						
						$queryCALLED 				= $astDB->rawQuery($CALLEDsql);
						$fetchCalled 				= $astDB->rawQuery($queryCALLED);
						$called_leadCount 			= $fetchCalled['Clead_count'];
						
						$NOTCALLEDsql 				= "
							SELECT count(gmt_offset_now) as NClead_count FROM vicidial_list 
							WHERE list_id IN (".$list.") AND status = 'NEW'
							AND gmt_offset_now = '$timezone_now'
						";
						
						$queryNOTCALLED 			= $NOTCALLEDsql;
						$fetchCalled 				= $astDB->rawQuery($queryNOTCALLED);
						$notcalled_leadCount 		= $fetchCalled['NClead_count'];

						$BOTsorted_output 			.= "<tr>";
						$BOTsorted_output 			.= "<td>".$timezone_now."</td><td>".$called_leadCount."</td><td>".$notcalled_leadCount."</td>";
						$BOTsorted_output 			.= "</tr>";
					}
				} else {
					$BOTsorted_output 				.= "<tr><td colspan='3'><center>No available Leads</center></td></tr>";
				}	
				
				$BOTsorted_output 					.= "</tbody></TABLE>";
				$return['TOPsorted_output']			= $TOPsorted_output;
				$return['SUMstatuses']				= $sts;
				
				$apiresults 						= array(
					"result" 							=> "success", 
					"SUMstatuses" 						=> $sts, 
					"TOPsorted_output" 					=> $TOPsorted_output, 
					"BOTsorted_output" 					=> $BOTsorted_output, 
					"query_list" 						=> $query_list, 
					"queryx" 							=> $queryx
				);
				
				return $apiresults;
			}
			
			// SALES PER AGENT
			if ($pageTitle == "sales_agent") {
				if ($log_group !== "ADMIN") {
					$ul 							= "AND us.user_group = '$log_group'";
				} else {
					$ul 							= "";
				}
				
				if ($request == "outbound") {
					// Outbound Sales //					
					$outbound_query 				= "
						SELECT us.full_name AS full_name, us.user AS user, 
							SUM(if (vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale 
						FROM vicidial_users as us, vicidial_log as vlog, vicidial_list as vl 
						WHERE us.user = vlog.user AND vl.phone_number = vlog.phone_number 
						AND vl.lead_id = vlog.lead_id AND vlog.length_in_sec > '0' 
						AND vlog.status in ('$statuses') AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
						AND vlog.campaign_id = '$campaignID' $ul 
						GROUP BY us.full_name
					";
					
					$query 							= $outbound_query;
					
					$TOPsorted_output 				= "";
					$total_out_sales 				= "";
					
					if ($query) {
						$total_sales				= 0;
						
						while ($row = $astDB->rawQuery($query)) {
							$TOPsorted_output	 	.= "<tr>";
							$TOPsorted_output 		.= "<td nowrap>".$row['full_name']."</td>";
							$TOPsorted_output 		.= "<td nowrap>".$row['user']."</td>";
							$TOPsorted_output		.= "<td nowrap>".$row['sale']."</td>";
							$TOPsorted_output 		.= "</tr>";
							$total_out_sales		 = $total_out_sales+$row['sale'];							
						}
					}
				}
				
				if ($request == "inbound") {
					// Inbound Sales //
					$inbound_query 					= "
						SELECT closer_campaigns FROM vicidial_campaigns 
						WHERE campaign_id='".$campaignID."' 
						ORDER BY campaign_id
					";
					
					$query 							= $inbound_query;
					$row 							= $astDB->rawQuery($query);
					$closer_camp_array				= explode(" ",$row['closer_campaigns']);
					$num 							= count($closer_camp_array);				
					$x								= 0;
					
					while ($x<$num) {
						if ($closer_camp_array[$x]!="-") {
							$closer_campaigns[$x] 	= $closer_camp_array[$x];
						}
						
						$x++;
					}
					
					$campaign_inb_query				= "vlog.campaign_id IN ('".implode("','",$closer_campaigns)."')";
					
					$query 							= "
						SELECT us.full_name AS full_name, us.user AS user, 
							SUM(if (vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale 
							FROM vicidial_users as us, vicidial_closer_log as vlog, vicidial_list as vl 
							WHERE us.user = vlog.user AND vl.phone_number = vlog.phone_number 
							AND vl.lead_id = vlog.lead_id AND vlog.length_in_sec > '0'  
							AND vlog.status in ('$statuses') AND date_format(vlog.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
							AND $campaign_inb_query $ul 
							GROUP BY us.full_name
						";
					
					$BOTsorted_output 				= "";
					$total_in_sales 				= "";
					
					if ($query) {
						$total_sales				= 0;
						
						//foreach($query->result() as $row) {
						while ($row = $astDB->rawQuery($query)) {
							$BOTsorted_output 		.= "<tr>";
							$BOTsorted_output 		.= "<td nowrap> ".$row['full_name']." </td>";
							$BOTsorted_output 		.= "<td nowrap> ".$row['user']." </td>";
							$BOTsorted_output 		.= "<td nowrap> ".$row['sale']." </td>";
							$BOTsorted_output 		.= "</tr>";
							$total_in_sales 		= $total_in_sales + $row['sale'];
						}
					}
				}
				
				$apiresults 						= array(
					"TOPsorted_output" 					=> $TOPsorted_output, 
					"BOTsorted_output" 					=> $BOTsorted_output, 
					"TOToutbound" 						=> $total_out_sales, 
					"TOTinbound" 						=> $total_in_sales, 
					"query" 							=> $outbound_query
				);
				
				return $apiresults;
			}
			
			// SALES TRACKER
			if ($pageTitle == "sales_tracker") {
				if ($log_group !== "ADMIN") {
					$ul 							= "AND us.user_group = '$log_group'";
				} else {
					$ul 							= "";
				}
				
				if ($request == 'outbound') {
					$outbound_query 				= "
						SELECT distinct(vl.phone_number) as phone_number, 
							vl.lead_id as lead_id, 
							vlo.call_date as call_date,
							us.full_name as agent, 
							vl.first_name as first_name,
							vl.last_name as last_name,
							vl.address1 as address,
							vl.city as city,
							vl.state as state, 
							vl.postal_code as postal,
							vl.email as email,
							vl.alt_phone as alt_phone,
							vl.comments as comments,vl.lead_id 
						FROM vicidial_log as vlo, vicidial_list as vl, vicidial_users as us 
						WHERE us.user = vlo.user AND vl.phone_number = vlo.phone_number 
						AND vl.lead_id = vlo.lead_id AND vlo.length_in_sec > '0'
						AND vlo.status in ('$statuses') AND date_format(vlo.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
						AND vlo.campaign_id = '$campaignID' $ul 
						order by vlo.call_date ASC 
						limit 2000
					";
					
					$query 							= $outbound_query;
					$outbound_result 				= "";
					$sale_num_value 				= 1;
					
					while ($row = $astDB->rawQuery($query)) {
						$sale_num[] 				= $sale_num_value;
						$outbound_result 			= $row['phone_number'];
						$call_date[] 				= $row['call_date'];
						$agent[] 					= $row['agent'];
						$lead_id[] 					= $row['lead_id'];
						$phone_number[] 			= $row['phone_number'];
						$first_name[] 				= $row['first_name'];
						$last_name[] 				= $row['last_name'];
						$address[] 					= $row['address'];
						$city[] 					= $row['city'];
						$state[] 					= $row['state'];
						$postal[] 					= $row['postal'];
						$email[] 					= $row['email'];
						$alt_phone[] 				= $row['alt_phone'];
						$comments[] 				= $row['comments'];
						$sale_num_value++;
					}
				}
			
				if ($request == 'inbound') {
					$query 							= "
						SELECT closer_campaigns FROM vicidial_campaigns 
						WHERE campaign_id = '$campaignID' 
						ORDER BY campaign_id
					";
					
					$row 							= $astDB->rawQuery($query);
					$closer_camp_array 				= explode(" ",$row['closer_campaigns']);
					$num 							= count($closer_camp_array);				
					$x								= 0;
					
					while ($x<$num) {
						if ($closer_camp_array[$x]!="-") {
							$closer_campaigns[$x]	= $closer_camp_array[$x];
						}
						
						$x++;
					}
					
					$campaign_inb_query				= "vlo.campaign_id IN ('".implode("','",$closer_campaigns)."')";
				
					$query 							= "
						SELECT distinct(vl.phone_number) as phone_number, 
							vl.lead_id as lead_id, 
							vlo.call_date as call_date,
							us.full_name as agent, 	
							vl.first_name as first_name,
							vl.last_name as last_name,
							vl.address1 as address,
							vl.city as city,
							vl.state as state, 
							vl.postal_code as postal,
							vl.email as email,
							vl.alt_phone as alt_phone,
							vl.comments as comments,
							vl.lead_id FROM vicidial_closer_log as vlo, 
							vicidial_list as vl, 
							vicidial_users as us 
						WHERE us.user = vl.user 
						AND vl.phone_number = vlo.phone_number 
						AND vl.lead_id=vlo.lead_id 
						AND vlo.length_in_sec > '0' 
						AND date_format(vlo.call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate' 
						AND $campaign_inb_query AND vlo.status in ('$statuses') $ul 
						order by vlo.call_date ASC 
						limit 2000
					";
					
					$inbound_result 				= "";
					$sale_num_value 				= 1;
					
					while ($row = $astDB->rawQuery($query)) {
						$sale_num[] 				= $sale_num_value;
						$inbound_result 			= $row['phone_number'];
						$call_date[] 				= $row['call_date'];
						$agent[] 					= $row['agent'];
						$lead_id[] 					= $row['lead_id'];
						$phone_number[] 			= $row['phone_number'];
						$first_name[] 				= $row['first_name'];
						$last_name[] 				= $row['last_name'];
						$address[] 					= $row['address'];
						$city[] 					= $row['city'];
						$state[] 					= $row['state'];
						$postal[] 					= $row['postal'];
						$email[] 					= $row['email'];
						$alt_phone[] 				= $row['alt_phone'];
						$comments[] 				= $row['comments'];
						$sale_num_value++;
					}
				}
				
				//$return['TOPsorted_output']		= $TOPsorted_output;
				//$return['file_output']			= $file_output;
				$apiresults 						= array(
					"outbound_result" 					=> $outbound_result, 
					"inbound_result" 					=> $inbound_result, 
					"sale_num" 							=> $sale_num, 
					"call_date" 						=> $call_date, 
					"agent" 							=> $agent, 
					"phone_number" 						=> $phone_number, 
					"lead_id" 							=> $lead_id, 
					"first_name" 						=> $first_name, 
					"last_name" 						=> $last_name,
					"address" 							=> $address, 
					"city" 								=> $city, 
					"state" 							=> $state, 
					"postal" 							=> $postal, 
					"email" 							=> $email, 
					"alt_phone" 						=> $alt_phone, 
					"comments" 							=> $comments,
					"query" 							=> $outbound_query
				);
				
				return $apiresults;
			}
			
			// INBOUND CALL REPORT
			if ($pageTitle == "inbound_report") {				
				if ($dispo_stats != NULL) {
					$ul 							= " AND status = '$dispo_stats' ";
				} else {
					$ul 							= "";
				}
				
				$inbound_report_query 				= "
					SELECT * FROM vicidial_closer_log 
					WHERE campaign_id = '$campaignID' $ul 
					AND date_format(call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'
				";
				
				$query 								= $inbound_report_query;
				$TOPsorted_output 					= "";
				$number 							= 1;
				
				while ($row = $astDB->rawQuery($query)) {
					$TOPsorted_output[] 			.= '<tr>';
					$TOPsorted_output[] 			.= '<td nowrap>'.$number.'</td>';
					
					$date 							= strtotime($row['call_date']);
					$date 							= date("Y-m-d", $date);
					$TOPsorted_output[] 			.= '<td nowrap>'.$date.'</td>';
					
					$TOPsorted_output[] 			.= '<td nowrap>'.$row['user'].'</td>';
					$TOPsorted_output[] 			.= '<td nowrap>'.$row['phone_number'].'</td>';
					
					//$time = strtotime($row['call_date']);
					$time 							= $row['end_epoch'] + $row['start_epoch'];
					$time 							= date("h:i:s", $time);
					$TOPsorted_output[] 			.= '<td nowrap>'.$time.'</td>';					
					$TOPsorted_output[] 			.= '<td nowrap style="padding-left:40px;">'.$row['length_in_sec'].'</td>';					
					$TOPsorted_output[] 			.= '<td nowrap>'.$row['status'].'</td>';
					$TOPsorted_output[] 			.= '</tr>';
					$number++;
				}
				
				$apiresults 						= array(
					"TOPsorted_output" 					=> $TOPsorted_output, 
					"query" 							=> $inbound_report_query
				);
				
				return $apiresults;
			}
		}
	}
	}

?>
