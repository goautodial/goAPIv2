<?php

	####################################################
	#### Name: goUploadMe.php                       ####
	#### Description: API for Uploading Leads       ####
	#### Version: 4                                 ####
	#### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
	#### Written by: Jerico James Milo              ####
	#### License: AGPLv2                            ####
	####################################################
	
	ini_set('memory_limit','1024M');
	ini_set('upload_max_filesize', '6000M');
	ini_set('post_max_size', '6000M');
	
	#ini_set('display_errors', 'on');
    #error_reporting(E_ALL);
	
	include_once("../goFunctions.php");
	include_once("goLookupGMT.php");
	
	$goDupcheck = $_REQUEST["goDupcheck"];
	$jsonDataRequest = json_decode($_REQUEST['jsonData'], true);
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
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

	$queryGetCustomFields = "SELECT column_name FROM information_schema.columns WHERE table_name='custom_$list_id';";
	$sqlCF = mysqli_query($link, $queryGetCustomFields);

	while($fresults = mysqli_fetch_array($sqlCF, MYSQLI_ASSOC)){
		$customFields[] = $fresults['column_name'];
	}

	foreach($leads as $lead){
		$leadsFields = $lead['fields'];
		$insertFields = array();
		$insertValues = array();
		$insertCustomFields = array();
		$insertCustomValues = array();
		$lead_id = "";
		foreach($leadsFields as $fields){
			if(in_array($fields['FieldName'], $baseFields) && $fields['FieldType'] != "custom"){
				$insertFields[] = "`".$fields['FieldName']."`";
				if($fields['FieldName'] == "phone_code"){
					if(!empty($fields['FieldValue'])){
						$insertValues[] = '"'.$fields['FieldValue'].'"';
					}else{
						$insertValues[] = '"1"';
					}
				}else{
					$insertValues[] = '"'.$fields['FieldValue'].'"';
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

		// Base fields and values
		$insertFields = implode(",", $insertFields);
    	$insertValues = implode(",", $insertValues);
    	// Custom Fields and Values
    	$insertCustomFields = implode(",", $insertCustomFields);
    	$insertCustomValues = implode(",", $insertCustomValues);

    	// INSERT TO DATABASE
    	// insert to vicidial_list
    	if(in_array("phone_code", $insertFields)){
    		$phone_code_field = "";
    		$phone_code_value = "";
    	}else{
    		$phone_code_field = ", `phone_code`";
    		$phone_code_value = ", '1'";
    	}
    	$insertListQuery = "INSERT INTO vicidial_list (`list_id`, `status`, $insertFields{$phone_code_field}) VALUES ('$list_id', 'NEW', $insertValues{$phone_code_value});";
    	$resultInsertList = mysqli_query($link, $insertListQuery);

    	if($resultInsertList){
    		//true
    		array_push($resultOfInserts, "ok");
    		$getLastInserted = "SELECT lead_id FROM vicidial_list ORDER BY lead_id DESC LIMIT 1;";
	    	$resultLastInserted = mysqli_query($link,  $getLastInserted);

	    	while($last_id = mysqli_fetch_array($resultLastInserted, MYSQLI_ASSOC)){
				$LastID = $last_id['lead_id'];
			}
    	}else{
    		array_push($resultOfInserts, "error");
    		array_push($leadsNotSaved, array("fields" => $insertFields, "value" => $insertValues));
    	}

    	if(!empty($insertCustomFields) && !empty($insertCustomValues)){
    		// insert to custom_$list_id
	    	$insertCustomFieldQuery = "INSERT INTO custom_$list_id(`lead_id`, $insertCustomFields) VALUES('$LastID', $insertCustomValues);";
	    	$resultInsertCustomField = mysqli_query($link, $insertCustomFieldQuery);
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
		$log_id = log_action($linkgo, 'UPLOAD', $log_user, $ip_address, "Error in uploading leads on List ID $list_id", $log_group);
	}else{
		$apiresults = array("result" => "success", "message" => "Uploading Leads success!");
		$log_id = log_action($linkgo, 'UPLOAD', $log_user, $ip_address, "Successfully uploaded leads on List ID $list_id", $log_group);
	}


?>
