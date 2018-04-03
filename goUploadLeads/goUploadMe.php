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
	
	$thefile = $_FILES['goFileMe']['tmp_name'];
	$theList = $astDB->escape($_REQUEST["goListId"]);
	$goDupcheck = $astDB->escape($_REQUEST["goDupcheck"]);
	$goCountInsertedLeads = 0;
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
	$ip_address = $astDB->escape($_REQUEST['hostname']);

	// path where your CSV file is located
	define('CSV_PATH','/tmp/');

	// Name of your CSV file
	//$csv_file = CSV_PATH . "$thefile"; 
	$csv_file = $thefile;

	//die($theList."<br>".$thefile."<br>".$csv_file);
	if (($handle = fopen($csv_file, "r")) !== FALSE) {
		$getHeder = fgetcsv($handle);
		#$goInsertSuccess = 0;
		#$array 21 last column
		
		#for custom fields start GLOBAL varaibles
		$goCountTheHeader = count($getHeder);
		
		if($goCountTheHeader > 21) {
			for($x=21; $x < count($getHeder); $x++) {
				$goGetLastHeader .= $x.","; #get digits for specific data
				$goGetLastCustomFiledsName .= $getHeder[$x].","; #get the digits for specific custom field
			}
			
			$goGetLastHeader = preg_replace("/,$/",'',$goGetLastHeader);
			$goGetLastHeader2 = explode(",",$goGetLastHeader);
			
			$goGetLastCustomFiledsName = preg_replace("/,$/",'',$goGetLastCustomFiledsName);
			$goGetLastCustomFiledsName2 = explode(",",$goGetLastCustomFiledsName);
			
			# check custom field names are correct
			$goGetLastCustomFiledsNameWithLeadID = "lead_id,".$goGetLastCustomFiledsName;		
			$goGetCheckcustomFieldNamesCorrect = goCheckCustomFieldsName($astDB, $theList, $goGetLastCustomFiledsNameWithLeadID);
			
			if($goGetCheckcustomFieldNamesCorrect == "Error Field Name") {
				$apiresults = array("result" => "Error" , "message" => "$goGetCheckcustomFieldNamesCorrect");
			}
		
		}
		#end for custom fields start GLOBAL varaibles
		
		
		
		
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			
			$num = count($data);
			for ($c=0; $c < $num; $c++) {
				$col[$c] = $data[$c];
			}
			# REGEX to prevent weird characters from ending up in the fields
			$field_regx = "/['\"`\\;]/";
			
			# SQL Query to insert data into DataBase
			
			$entry_date = date("Y-m-d H:i:s");
			$status = "NEW";
			$vendor_lead_code = preg_replace($field_regx, "", $col[1]);
			$list_id = $theList;
			$gmt_offset = "0";
			$phone_code = preg_replace($field_regx, "", $col[2]);
			$phone_number = preg_replace($field_regx, "", $col[0]);
			$title = preg_replace($field_regx, "", $col[3]);
			$first_name = preg_replace($field_regx, "", $col[4]);
			$middle_initial = preg_replace($field_regx, "", $col[5]);
			$last_name = preg_replace($field_regx, "", $col[6]);
			$address1 = preg_replace($field_regx, "", $col[7]);
			$address2 = preg_replace($field_regx, "", $col[8]);
			$address3 = preg_replace($field_regx, "", $col[9]);
			$city = preg_replace($field_regx, "", $col[10]);
			$state = preg_replace($field_regx, "", $col[11]);
			$province = preg_replace($field_regx, "", $col[12]);
			$postal_code = preg_replace($field_regx, "", $col[13]);
			$country_code = preg_replace($field_regx, "", $col[14]);
			$gender = preg_replace($field_regx, "", $col[15]);
			$date_of_birth = preg_replace($field_regx, "", $col[16]);
			$date_of_birth = date("Y-m-d", strtotime($date_of_birth));
			$alt_phone = preg_replace($field_regx, "", $col[17]);
			$email = preg_replace($field_regx, "", $col[18]);
			$security_phrase = preg_replace($field_regx, "", $col[19]);
			$comments = preg_replace($field_regx, "", $col[20]);
			$entry_list_id = 0;
			$called_since_last_reset = "N";

			if($goDupcheck == "DUPCAMP") {
				#Duplicate check all LIST in CAMPAIGN
				if($goGetCheckcustomFieldNamesCorrect == "Error Field Name") {
					$apiresults = array("result" => "Error" , "message" => "$goGetCheckcustomFieldNamesCorrect");
				} else {
				
						//$goQueryCheckDupPhone = "SELECT campaign_id, list_id FROM vicidial_lists WHERE list_id='$list_id';";
						$astDB->where('list_id', $list_id);
						$rsltCheckDupPhone = $astDB->get('vicidial_lists', null, 'campaign_id');
						$countResult = $astDB->getRowCount();
						
						if($countResult > 0) {
							foreach ($rsltCheckDupPhone as $fresults){
								$goCampaignID = $fresults['campaign_id'];								
								$goReturnCampList = goGetCampaignList($astDB, $goCampaignID);
							}
							$CampLists = $goReturnCampList;
							
							$goDUPLists = preg_replace("/,$/",'',$CampLists);
							
							//$goCheckCampPhoneList = "SELECT phone_number FROM vicidial_list WHERE phone_number='$phone_number' AND list_id IN($goDUPLists) LIMIT 1;";
							$astDB->where('phone_number', $phone_number);
							$astDB->where('list_id', explode(',', $goDUPLists), 'in');
							$rsltgoCheckCampPhoneList = $astDB->getOne('vicidial_list', 'phone_number');
							$countCheckCampPhoneList = $astDB->getRowCount();
							
							if($countCheckCampPhoneList < 1) {
								$USarea = substr($phone_number, 0, 3);
								$gmt_offset = lookup_gmt($astDB, $phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code,$owner);				
								//$goQueryInsNotDUP = "INSERT INTO vicidial_list (lead_id, entry_date, status, vendor_lead_code, list_id, gmt_offset_now, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, entry_list_id) VALUES ('', '$entry_date', '$status', '$vendor_lead_code', '$list_id', '$gmt_offset', '$phone_code', '$phone_number', '$title',	'$first_name', '$middle_initial', '$last_name',	'$address1', '$address2', '$address3', '$city',	'$state', '$province', '$postal_code', '$country_code',	'$gender', '$date_of_birth', '$alt_phone', '$email', '$security_phrase', '$comments', '$entry_list_id');";
								$insertData = array(
									'lead_id' => '',
									'entry_date' => $entry_date,
									'status' => $status,
									'vendor_lead_code' => $vendor_lead_code,
									'list_id' => $list_id,
									'gmt_offset_now' => $gmt_offset,
									'phone_code' => $phone_code,
									'phone_number' => $phone_number,
									'title' => $title,
									'first_name' => $first_name,
									'middle_initial' => $middle_initial,
									'last_name' => $last_name,
									'address1' => $address1,
									'address2' => $address2,
									'address3' => $address3,
									'city' => $city,
									'state' => $state,
									'province' => $province,
									'postal_code' => $postal_code,
									'country_code' => $country_code,
									'gender' => $gender,
									'date_of_birth' => $date_of_birth,
									'alt_phone' => $alt_phone,
									'email' => $email,
									'security_phrase' => $security_phrase,
									'comments' => $comments,
									'entry_list_id' => $entry_list_id
								);
								$rsltGoQueryInsNotDUP = $astDB->insert('vicidial_list', $insertData);
								$goLastInsertedLeadIDDUPCAMP = $astDB->getInsertId();
								
								$goCountInsertedLeads++;
								$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");


								# start set query for custom fields
								if($goCountTheHeader > 21) {
									$goShowCustomFields = "DESC custom_$list_id;";
									$rsltgoShowCustomFields = $astDB->rawQuery($goShowCustomFields);
									$countResultrsltgoShowCustomFields = $astDB->getRowCount();
									
									if($countResultrsltgoShowCustomFields > 1) {
										$totalExplode = count($goGetLastHeader2);
										for($ax=0; $ax < $totalExplode; $ax++) {
											$goHeaderOfCustomFields = $goGetLastCustomFiledsName2[$ax]; #get the header name of the custom fields
											$goCustomValues = $col[$goGetLastHeader2[$ax]]; #get the values of the custom fields
											
											#$goQueryCustomFields .= "INSERT INTO custom_$theList (lead_id,".$goHeaderOfCustomFields.") VALUES ('$goLastInsertedLeadIDDUPCAMP','".$goCustomValues."');";
											#$rsltGoQueryCustomFields = mysqli_query($link, $goQueryCustomFields);
											
											$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $goHeaderOfCustomFields) VALUES('$goLastInsertedLeadIDDUPCAMP', '$goCustomValues') ON DUPLICATE KEY UPDATE $goHeaderOfCustomFields='$goCustomValues'";
											$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
											
											#$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");
										}
										
									} 
								} 
								# end set query for custom fields
								
							}
						}
				}
				
			} elseif ($goDupcheck == "DUPLIST") {
				#Duplicate check within the LIST
				if($goGetCheckcustomFieldNamesCorrect == "Error Field Name") {
				
					$apiresults = array("result" => "Error" , "message" => "$goGetCheckcustomFieldNamesCorrect");
				
				} else {
					//$goQueryCheckDupPhone = "SELECT phone_number FROM vicidial_list WHERE phone_number='$phone_number' AND list_id='$list_id';";
					$astDB->where('phone_number', $phone_number);
					$astDB->where('list_id', $list_id);
					$rsltCheckDupPhone = $astDB->get('vicidial_list', null, 'phone_number');
					$countResult = $astDB->getRowCount();

					if($countResult < 1) {
						$USarea = substr($phone_number, 0, 3);
						$gmt_offset = lookup_gmt($astDB, $phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code,$owner);
						
						//$goQueryInsDupList = "INSERT INTO vicidial_list (lead_id, entry_date, status, vendor_lead_code, list_id, gmt_offset_now, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, entry_list_id) VALUES ('', '$entry_date', '$status', '$vendor_lead_code', '$list_id', '$gmt_offset', '$phone_code', '$phone_number', '$title',	'$first_name', '$middle_initial', '$last_name',	'$address1', '$address2', '$address3', '$city',	'$state', '$province', '$postal_code', '$country_code',	'$gender', '$date_of_birth', '$alt_phone', '$email', '$security_phrase', '$comments', '$entry_list_id');";
						$insertData = array(
							'lead_id' => '',
							'entry_date' => $entry_date,
							'status' => $status,
							'vendor_lead_code' => $vendor_lead_code,
							'list_id' => $list_id,
							'gmt_offset_now' => $gmt_offset,
							'phone_code' => $phone_code,
							'phone_number' => $phone_number,
							'title' => $title,
							'first_name' => $first_name,
							'middle_initial' => $middle_initial,
							'last_name' => $last_name,
							'address1' => $address1,
							'address2' => $address2,
							'address3' => $address3,
							'city' => $city,
							'state' => $state,
							'province' => $province,
							'postal_code' => $postal_code,
							'country_code' => $country_code,
							'gender' => $gender,
							'date_of_birth' => $date_of_birth,
							'alt_phone' => $alt_phone,
							'email' => $email,
							'security_phrase' => $security_phrase,
							'comments' => $comments,
							'entry_list_id' => $entry_list_id
						);
						$rsltGoQueryInsDupList = $astDB->insert('vicidial_list', $insertData);
						$goLastInsertedLeadIDDUPLIST = $astDB->getInsertId();
						
						$goCountInsertedLeads++;
						$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");
						
						# start set query for custom fields
						if($goCountTheHeader > 21) {
							$goShowCustomFields = "DESC custom_$list_id;";
							$rsltgoShowCustomFields = $astDB->rawQuery($goShowCustomFields);
							$countResultrsltgoShowCustomFields = $astDB->getRowCount();
							
							if($countResultrsltgoShowCustomFields > 1) {
								$totalExplode = count($goGetLastHeader2);
								for($ax=0; $ax < $totalExplode; $ax++) {
									$goHeaderOfCustomFields = $goGetLastCustomFiledsName2[$ax]; #get the header name of the custom fields
									$goCustomValues = $col[$goGetLastHeader2[$ax]]; #get the values of the custom fields
										
									#$goQueryCustomFields = "INSERT INTO custom_$theList (lead_id,".$goHeaderOfCustomFields.") VALUES ('$goLastInsertedLeadIDDUPLIST','".$goCustomValues."');";
									$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $goHeaderOfCustomFields) VALUES('$goLastInsertedLeadIDDUPLIST', '$goCustomValues') ON DUPLICATE KEY UPDATE $goHeaderOfCustomFields='$goCustomValues'";
									$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
									
									#$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");
								}
								
							}
							
						} 
						# end set query for custom fields
					}
				}
			} else {
				#NO DUPLICATE CHECK
				if($goGetCheckcustomFieldNamesCorrect == "Error Field Name") {
					$apiresults = array("result" => "Error" , "message" => "$goGetCheckcustomFieldNamesCorrect");
				} else {
					$USarea = substr($phone_number, 0, 3);
					$gmt_offset = lookup_gmt($astDB, $phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code,$owner);
			
					//$goQueryIns = "INSERT INTO vicidial_list (lead_id, entry_date, status, vendor_lead_code, list_id, gmt_offset_now, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, entry_list_id) VALUES ('', '$entry_date', '$status', '$vendor_lead_code', '$list_id', '$gmt_offset', '$phone_code', '$phone_number', '$title',	'$first_name', '$middle_initial', '$last_name',	'$address1', '$address2', '$address3', '$city',	'$state', '$province', '$postal_code', '$country_code',	'$gender', '$date_of_birth', '$alt_phone', '$email', '$security_phrase', '$comments', '$entry_list_id');";
					$insertData = array(
						'lead_id' => '',
						'entry_date' => $entry_date,
						'status' => $status,
						'vendor_lead_code' => $vendor_lead_code,
						'list_id' => $list_id,
						'gmt_offset_now' => $gmt_offset,
						'phone_code' => $phone_code,
						'phone_number' => $phone_number,
						'title' => $title,
						'first_name' => $first_name,
						'middle_initial' => $middle_initial,
						'last_name' => $last_name,
						'address1' => $address1,
						'address2' => $address2,
						'address3' => $address3,
						'city' => $city,
						'state' => $state,
						'province' => $province,
						'postal_code' => $postal_code,
						'country_code' => $country_code,
						'gender' => $gender,
						'date_of_birth' => $date_of_birth,
						'alt_phone' => $alt_phone,
						'email' => $email,
						'security_phrase' => $security_phrase,
						'comments' => $comments,
						'entry_list_id' => $entry_list_id
					);
					$rsltGoQueryIns = $astDB->insert('vicidial_list', $insertData);
					$goLastInsertedLeadIDNODUP = $astDB->getInsertId();
					
					$goCountInsertedLeads++;
					$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");
					
					# start set query for custom fields
					if($goCountTheHeader > 21) {
						$goShowCustomFields = "DESC custom_$list_id;";
						$rsltgoShowCustomFields = $astDB->rawQuery($goShowCustomFields);
						$countResultrsltgoShowCustomFields = $astDB->getRowCount();
						
						if($countResultrsltgoShowCustomFields > 1) {
							$totalExplode = count($goGetLastHeader2);
							for($ax=0; $ax < $totalExplode; $ax++) {
								$goHeaderOfCustomFields = $goGetLastCustomFiledsName2[$ax]; #get the header name of the custom fields
								$goCustomValues = $col[$goGetLastHeader2[$ax]]; #get the values of the custom fields
									
								#$goQueryCustomFields .= "INSERT INTO custom_$theList (lead_id,".$goHeaderOfCustomFields.") VALUES ('$goLastInsertedLeadIDNODUP','".$goCustomValues."');";
								
								#$rsltGoQueryCustomFields = mysqli_query($link, $goQueryCustomFields);
								
								$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $goHeaderOfCustomFields) VALUES('$goLastInsertedLeadIDNODUP', '$goCustomValues') ON DUPLICATE KEY UPDATE $goHeaderOfCustomFields='$goCustomValues'";
								$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
								
								#$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");
							}
						} 
						
					}
				# end set query for custom fields
				}
			} #end No Duplicate check
		} #end while
		
		fclose($handle);
		if($goCountInsertedLeads > 0) {
			$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");
		} else {
			$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");
		}
		
		$log_id = log_action($goDB, 'UPLOAD', $log_user, $ip_address, "Uploaded {$goCountInsertedLeads} leads on List ID $theList", $log_group);
		
	}
	
	function goGetCampaignList($link, $goCampaignID) {
		//$goCheckCamp = "SELECT list_id FROM vicidial_lists WHERE campaign_id='$goCampaignID';";
		$link->where('campaign_id', $goCampaignID);
		$rsltgoCheckCamp = $link->get('vicidial_lists', null, 'list_id');
		$countResultCamp = $link->getRowCount();
		
		foreach ($rsltgoCheckCamp as $fresultsDup) {
			$goDUPLists .= $fresultsDup['list_id'].",";
		}
		return $goDUPLists;
	}
	
	function goCheckCustomFieldsName($link, $goCClistID, $gocustomFieldsCSV) {
		// check fieldnames are correct
		//$goSQLCheckFieldsCustom = "SELECT $gocustomFieldsCSV FROM custom_$goCClistID;";
		$rsltSQLCHECK = $link->get("custom_$goCClistID", null, "$gocustomFieldsCSV");
		
		if(!$rsltSQLCHECK){
			$goRetMessage = "Error Field Name";
		} else {
			$goShowCustomFields = "DESC custom_$goCClistID;";
			$rsltgoShowCustomFields = $link->rawQuery($goShowCustomFields);
			$countResultrsltgoShowCustomFields = $link->getRowCount();
			
			if($countResultrsltgoShowCustomFields > 1) {
				foreach ($rsltgoShowCustomFields as $fresultsShow){
					$goCustomFields .= $fresultsShow['Field'].",";
				}
				
				$goRetMessage = preg_replace("/,$/",'',$goCustomFields);
			}
		}
				
		return $goRetMessage;
	}
	
	// check 1st if fields are not less than 21
	// check 2nd if greater than 21 check the field name spelling from csv vs on the DB custom_LISTID
	// lookup_gmt extgetval
	// $field_regx = "/['\"`\\;]/";
	// $vendor_lead_code =             preg_match($field_regx, "", $vendor_lead_code);
	// $vendor_lead_code =             preg_replace($field_regx, "", $vendor_lead_code);

//echo "File data successfully imported to database!!";
?>
