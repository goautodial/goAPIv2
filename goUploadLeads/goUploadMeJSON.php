<?php
 /**
 * @file 		goUploadMeJSON.php
 * @brief 		API for Uploading Leads
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jericho James Milo  <james@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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

	ini_set('memory_limit','1024M');
	ini_set('upload_max_filesize', '6000M');
	ini_set('post_max_size', '6000M');
	
	$goDupcheck = $astDB->escape($_REQUEST["goDupcheck"]);
	$jsonDataRequest = json_decode($_REQUEST['jsonData'], true);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
	$ip_address = $astDB->escape($_REQUEST['hostname']);
	$jsonDataPost = (array) json_decode(file_get_contents('php://input'), TRUE);

	if(!empty($jsonDataRequest)){
		$jsonData = $jsonDataRequest;
	}else{
		$jsonData = $jsonDataPost;
	}

	$list_id = $jsonData['list_id'];
	$leads = $jsonData['leads'];
	$baseFields = array("lead_id", "entry_date", "status", "vendor_lead_code", "list_id", "gmt_offset_now", "phone_code", "phone_number", "title", "first_name", "middle_initial", "last_name", "address1", "address2", "address3", "city", "state", "province", "postal_code", "country_code", "gender", "date_of_birth", "alt_phone", "email", "security_phrase", "comments", "entry_list_id");

	$resultOfInserts = array();
	$leadsNotSaved = array();

	//$queryGetCustomFields = "SELECT column_name FROM information_schema.columns WHERE table_name='custom_$list_id';";
	$astDB->where('table_name', "custom_$list_id");
	$sqlCF = $astDB->get('information_schema.columns', null, 'column_name');

	foreach ($sqlCF as $fresults){
		$customFields[] = $fresults['column_name'];
	}

	foreach($leads as $lead){
		$leadsFields = $lead['fields'];
		$insertFields = array();
		$insertValues = array();
		$insertCustomFields = array();
		$insertCustomValues = array();
		$lead_id = "";
		$phone_number = ''; 
		$phone_code = '';
		$state = '';
		$postal_code = '';
		foreach($leadsFields as $fields){
			if(in_array($fields['FieldName'], $baseFields) && $fields['FieldType'] != "custom"){
				$insertFields[] = "`".$fields['FieldName']."`";
				if($fields['FieldName'] == "phone_code"){
					if(!empty($fields['FieldValue'])){
						$insertValues[] = '"'.$fields['FieldValue'].'"';
						$phone_code = $fields['FieldValue'];
					}else{
						$insertValues[] = '"1"';
						$phone_code = '';
					}
				}else{
					$insertValues[] = '"'.$fields['FieldValue'].'"';
				}

				if($fields['FieldName'] == "state"){
					if(!empty($fields['FieldValue'])){
						$state = $fields['FieldValue'];
					}else{
						$state = '';
					}
				}

				if($fields['FieldName'] == "phone_number"){
					if(!empty($fields['FieldValue'])){
						$phone_number = $fields['FieldValue'];
					}else{
						$phone_number = '';
					}
				}

				if($fields['FieldName'] == "postal_code"){
					if(!empty($fields['FieldValue'])){
						$postal_code = $fields['FieldValue'];
					}else{
						$postal_code = '';
					}
				}
			}else{
				if(in_array($fields['FieldName'], $customFields)){
					$insertCustomFields[] = "`".$fields['FieldName']."`";
					$insertCustomValues[] = '"'.$fields['FieldValue'].'"';
				}
				
			}

			if($fields['FieldName'] == "lead_id"){
				$lead_id = $fields['FieldValue'];
			}
		}
		$USarea = substr($phone_number, 0, 3);
		$gmt_offset = lookup_gmt($astDB, $phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code,$owner);
		if(strpos($insertFields, 'gmt_offset_now') !== false){
			$insertFields[] = "`gmt_offset_now`";
			$insertValues[] = '"'.$gmt_offset.'"';
		}

		// Base fields and values
		$insertFields = implode(",", $insertFields);
    	$insertValues = implode(",", $insertValues);
    	// Custom Fields and Values
    	$insertCustomFields = implode(",", $insertCustomFields);
    	$insertCustomValues = implode(",", $insertCustomValues);

    	// INSERT TO DATABASE
    	// insert to vicidial_list
    	if(strpos($insertFields, 'phone_code') !== false){
    		$phone_code_field = "";
    		$phone_code_value = "";
    	}else{
    		$phone_code_field = ", `phone_code`";
    		$phone_code_value = ", '1'";
    	}
    	
    	$insertListQuery = "INSERT INTO vicidial_list (`list_id`, `status`, $insertFields{$phone_code_field}) VALUES ('$list_id', 'NEW', $insertValues{$phone_code_value});";
    	$resultInsertList = $astDB->rawQuery($insertListQuery);
    	if($resultInsertList){
    		//true
    		array_push($resultOfInserts, "ok");
    		//$getLastInserted = "SELECT lead_id FROM vicidial_list ORDER BY lead_id DESC LIMIT 1;";
			$astDB->orderBy('lead_id', 'desc');
	    	$resultLastInserted = $astDB->getOne('vicidial_list', 'lead_id');
			$LastID = $resultLastInserted['lead_id'];
    	}else{
    		array_push($resultOfInserts, "error");
    		array_push($leadsNotSaved, array("fields" => $insertFields, "value" => $insertValues));
    	}

    	if(!empty($insertCustomFields) && !empty($insertCustomValues)){
    		// insert to custom_$list_id
	    	$insertCustomFieldQuery = "INSERT INTO custom_$list_id(`lead_id`, $insertCustomFields) VALUES('$LastID', $insertCustomValues);";
	    	$resultInsertCustomField = $astDB->rawQuery($insertCustomFieldQuery);
	    	// array_push($resultOfInserts, $insertCustomFieldQuery);
	    	// array_push($resultOfInserts, $resultInsertCustomField);
	    	if($resultInsertCustomField){
	    		//true
	    		array_push($resultOfInserts, "ok");
	    	}else{
	    		array_push($resultOfInserts, "error");
	    		array_push($leadsNotSaved, array("fields" => $insertCustomFields, "value" => $insertCustomValues));
	    	}
    	}

	}
	// print_r($resultOfInserts);die;
	if(in_array("error", $resultOfInserts)){
		$apiresults = array("result" => "error", "message" => "Uploading Leads interrupted due too some errors. Please contact administrator.", "LeadNotSaved" => $leadsNotSaved);
		$log_id = log_action($goDB, 'UPLOAD', $log_user, $ip_address, "Error in uploading leads on List ID $list_id", $log_group);
	}else{
		$apiresults = array("result" => "success", "message" => "Uploading Leads success!");
		$log_id = log_action($goDB, 'UPLOAD', $log_user, $ip_address, "Successfully uploaded leads on List ID $list_id", $log_group);
	}
?>