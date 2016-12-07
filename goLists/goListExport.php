 <?php
   ####################################################
   #### Name: goEditCarrier.php                    ####
   #### Description: API to edit specific carrier  ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
ini_set('memory_limit', '2048M');
//ini_set('memory_limit', -1);
    include_once ("../goFunctions.php");
	
	$listid = $_REQUEST["list_id"];
	
	if($listid != NULL){
		$query = mysqli_query($link,"SELECT custom_fields_enabled FROM system_settings;");
		$fetch = mysqli_fetch_array($query);
		$custom_fields_enabled = $fetch["custom_fields_enabled"];
	
		if ($custom_fields_enabled <= 1){
			$custom_table = "custom_".$listid;
				//$cllist = mysqli_query($link,"SELECT field_label FROM vicidial_lists_fields WHERE list_id ='$listid';");
				$cllist_query = "SHOW COLUMNS FROM $custom_table;";
				$cllist = mysqli_query($link, $cllist_query);
				$clcount = mysqli_num_rows($cllist);
				$header_columns = "";
				//$u=0;
				/*
				foreach($cllist->result() as $clrow){
					$column = $clrow->Field;
					if ($column!='lead_id')
						 $header_columns .= ",$column";
				}*/
				
				while($clrow = mysqli_fetch_array($cllist)){
					if ($clrow[0] != 'lead_id'){
						$header_columns .= ",".$clrow[0];
					}
				}
				
				$added_custom_SQL  = ", $custom_table ct";
				$added_custom_SQL2 = "AND vl.lead_id=ct.lead_id";
				
				if($clcount){
					$added_custom_SQL3  = "$custom_table ct";
					$added_custom_SQL4 = "vl.lead_id=ct.lead_id";	
				}else{
					$added_custom_SQL3  = "";
					$added_custom_SQL4 = "";	
				}
				
			if(!empty($added_custom_SQL3)) {
				$stmt = "SELECT vl.lead_id AS lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner{$header_columns}
				FROM vicidial_list vl
				LEFT OUTER JOIN {$added_custom_SQL3} ON {$added_custom_SQL4}
				WHERE vl.list_id='{$listid}' LIMIT 20;";
			} else {
				$stmt = "SELECT vl.lead_id AS lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner{$header_columns} FROM vicidial_list vl{$added_custom_SQL} WHERE list_id='$listid' $added_custom_SQL2 LIMIT 20; ";
			}
			
			$dllist = mysqli_query($link, $stmt);
			
			while($fetch_header = mysqli_fetch_field($dllist)){
				$header[] = $fetch_header->name;
			}
			
			$u=0;
			$count_header = count($header);
			while($fetch_row = mysqli_fetch_row($dllist)){
				$array_fetch = $fetch_row[0];
				$u = $u+1;
				while($u <= $count_header){
					$array_fetch .= "|".$fetch_row[$u];
					$u++;
				}
				$row[] = $array_fetch;
				$array_fetch = "";
				$u = 0;
			}
			/*
			$u=0;
			$x=0;
			$count_header = count($header);
			while($fetch_row = mysqli_fetch_row($dllist)){
				$array_fetch = $fetch_row[0];
				$u = $u+1;
				while($u <= $count_header){
					$array_fetch .= "|".$fetch_row[$u];
					$u++;
				}
				$explode_array = explode("|",$array_fetch);
				$row[$x] = $explode_array;
				$array_fetch = "";
				$u = 0;
				$x++;
			}
			 */
			
			$apiresults = array("result" => "success", "header" => $header, "row" => $row, "query" => $stmt, "query_custom_list" => $custom_table);
		}
	}else{
		$apiresults = array("result" => "Error: List ID not defined");
	}
	

?>