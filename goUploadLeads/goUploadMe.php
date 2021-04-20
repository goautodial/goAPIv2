<?php
 /**
 * @file 		goUploadMe.php
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
	include_once ("goAPI.php");

	ini_set('memory_limit','2048M');
	ini_set('upload_max_filesize', '600M');
	ini_set('post_max_size', '600M');
    ini_set('max_execution_time', 3600);
	
	//ini_set('display_errors', 1);
	//error_reporting(E_ALL);
	
	$thefile = $_FILES['goFileMe']['tmp_name'];
	$theList = $astDB->escape($_REQUEST["goListId"]);
	$goDupcheck = $astDB->escape($_REQUEST["goDupcheck"]);
	$goCountInsertedLeads = 0;
	$default_delimiter = ",";
	$phone_code_override = $astDB->escape($_REQUEST["phone_code_override"]);
	
	$lead_mapping = NULL;
	if(!empty($_REQUEST["lead_mapping"]))	
		$lead_mapping = $astDB->escape($_REQUEST["lead_mapping"]);

	$alex = array();
	$goGetCheckcustomFieldNamesCorrect = ""; //constant
	
	// path where your CSV file is located
	define('CSV_PATH','/tmp/');

	// Name of your CSV file
	//$csv_file = CSV_PATH . "$thefile"; 
	$csv_file = $thefile;

	// REPLACE DELIMITER to SEMI-COLON -- CUSTOMIZATION!!!!!
        if(!empty($_REQUEST["custom_delimiter"]) && isset($_REQUEST["custom_delimiter"])){
           //$default_delimiter = $_REQUEST["custom_delimiter"];
		
	   $delimiters = explode(" ", $_REQUEST["custom_delimiter"]);
           $str = file_get_contents($csv_file);
           $str1 = str_replace($delimiters, $default_delimiter, $str);
           file_put_contents($csv_file, $str1);
        }

	// REGEX to prevent weird characters from ending up in the fields
        $field_regx = "/['\"`\\;]/";
        $field_regx = str_replace($delimiters, "", $field_regx);

	$duplicates = 0;
	$getHeder = "";
	$goCountTheHeader = "";
	//die($theList."<br>".$thefile."<br>".$csv_file);
	if (($handle = fopen($csv_file, "r")) !== FALSE) {
		$getHeder = fgetcsv($handle);
		//$goInsertSuccess = 0;
		//$array 21 last column
		
		//for custom fields start GLOBAL varaibles
		$goCountTheHeader = count($getHeder);
		
		if($goCountTheHeader > 21 && !empty($lead_mapping)) {
			for($x=21; $x < count($getHeder); $x++) {
				$goGetLastHeader .= $x.","; #get digits for specific data
				$goGetLastCustomFiledsName .= $getHeder[$x].","; #get the digits for specific custom field
			}
			
			$goGetLastHeader = preg_replace("/,$/",'',$goGetLastHeader);
			$goGetLastHeader2 = explode(",",$goGetLastHeader);
			$goGetLastCustomFiledsName = preg_replace("/,$/",'',$goGetLastCustomFiledsName);
			$goGetLastCustomFiledsName2 = explode(",",$goGetLastCustomFiledsName);
				
		} elseif($goCountTheHeader > 21) {
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
			if($goGetCheckcustomFieldNamesCorrect != "success") { 
				fclose($handle);
			}
		}
		//end for custom fields start GLOBAL varaibles
		
		
		while (($data = fgetcsv($handle, 1000, $default_delimiter)) !== FALSE) {
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
			// PHONE CODE OVERRIDE
			if(!empty($phone_code_override))
				$phone_code = preg_replace($field_regx, "", $phone_code_override);
			else
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
			
			// LEAD MAPPING -- CUSTOMIZATION!!!!!
			if(!empty($lead_mapping)){
				$lead_mapping_data = explode(",",$_REQUEST["lead_mapping_data"]);
				$lead_mapping_fields = explode(",", $_REQUEST["lead_mapping_fields"]);
				$standard_fields = array("Phone","VendorLeadCode","PhoneCode","Title","FirstName","MiddleInitial","LastName","Address1","Address2","Address3","City","State","Province","PostalCode","CountryCode","Gender","DateOfBirth","AltPhone","Email","SecurityPhrase","Comments");
				// MAKE MAP FIELDS AN INDEX OF MAP DATA & SEPARATE STANDARD FROM CUSTOM ARRAYS

				for($l=0; $l < count($lead_mapping_fields);$l++){
					if(in_array($lead_mapping_fields[$l], $standard_fields))
						$standard_array[$lead_mapping_fields[$l]] = $lead_mapping_data[$l];
					else
						$custom_array[$lead_mapping_fields[$l]] = $lead_mapping_data[$l];
				}
				//set default values to none
				$phone_number = "";
                $vendor_lead_code = "";
				if(!empty($phone_code_override))
					$phone_code = $phone_code_override;
				else
                    $phone_code = 1;
				$log = $phone_code_override;
				$title = "";
				$first_name = "";
				$middle_initial = "";
				$last_name = "";
				$address1 = "";
				$address2 = "";
				$address3 = "";
				$city = "";
				$state = "";
				$province = "";
				$postal_code = "";
				$country_code = "";
				$gender = "";
				$date_of_birth = "";
				$alt_phone = "";
				$email = "";
				$security_phrase = "";
				$comments = "";				
				
				//get arrayed lead mapping requests
				foreach($standard_array as $l => $map_data){
					//$logthis[] = $map_data;
					if($map_data !== "" || $map_data !== "."){
						// one by one sort through columns to overwrite lead mapping data
						if($l == "Phone")
							$phone_number = $col[$map_data];
						if($l == "VendorLeadCode")
							$vendor_lead_code = $col[$map_data];
						if($l == "PhoneCode"){
							if(!empty($phone_code_override))
								$phone_code = $phone_code_override;
							else
								$phone_code = $col[$map_data];
						}if($l == "Title")
							$title = $col[$map_data];
						if($l == "FirstName")
							$first_name = $col[$map_data];
						if($l == "MiddleInitial")
							$middle_initial = $col[$map_data];
						if($l == "LastName")
							$last_name = $col[$map_data];
						if($l == "Address1")
							$address1 = $col[$map_data];
						if($l == "Address2")
							$address2 = $col[$map_data];
						if($l == "Address3")
							$address3 = $col[$map_data];
						if($l == "City")
							$city = $col[$map_data];
						if($l == "State")
							$state = $col[$map_data];
						if($l == "Province")
							$province = $col[$map_data];
						if($l == "PostalCode")
							$postal_code = $col[$map_data];
						if($l == "CountryCode")
							$country_code = $col[$map_data];
						if($l == "Gender")
							$gender = $col[$map_data];
						if($l == "DateOfBirth")
							$date_of_birth = $col[$map_data];
						if($l == "AltPhone")
							$alt_phone = $col[$map_data];
						if($l == "Email")
							$email = $col[$map_data];
						if($l == "SecurityPhrase")
							$security_phrase = $col[$map_data];
						if($l == "Comments")
							$comments = $col[$map_data];
					}// end if
				}// end loop
			} // END OF LEAD MAPPING
			

			if($goDupcheck === "DUPSYS"){ // Duplicate check all phone numbers in entire system
				if($goGetCheckcustomFieldNamesCorrect == "error" && empty($lead_mapping)) {
					fclose($handle);
				} else {
					//check in vicidial_list
					$astDB->where('phone_number', $phone_number);
					$resultCheckPhone = $astDB->getOne('vicidial_list', 'phone_number');
					$countCheck1 = $astDB->getRowCount();
					
					//check in vicidial_dnc
					$astDB->where('phone_number', $phone_number);
					$resultCheckPhone = $astDB->getOne('vicidial_dnc', 'phone_number');
					$countCheck2 = $astDB->getRowCount();

					if($countCheck1 < 1 && $countCheck2 < 1){
						$USarea = substr($phone_number, 0, 3);
						$gmt_offset = lookup_gmt($astDB, $phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code,$owner);
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
							'first_name' => utf8_encode($first_name),
							'middle_initial' => utf8_encode($middle_initial),
							'last_name' => utf8_encode($last_name),
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
							'entry_list_id' => $entry_list_id,
							'last_local_call_time' => '0000-00-00 00:00:00'
						);
						$insertQuery = $astDB->insert('vicidial_list', $insertData);
						$goLastInsertedLeadIDDUPSYS = $astDB->getInsertId();
						
						# start set query for custom fields
						if(!empty($lead_mapping) && !empty($custom_array)){ // LEAD MAPPING CUSTOMIZATION
							$goCustomKeyData = array();
							$goCustomValuesData = array();
							$goCustomUpdateData = array();
							
							foreach($custom_array as $custom_key => $map_data){
								$goCustomValues = $col[$map_data];
								array_push($goCustomKeyData, "$custom_key");
								array_push($goCustomValuesData, "'$goCustomValues'");
								array_push($goCustomUpdateData, "$custom_key='$goCustomValues'");
							}
							
							$custom_keyValues = implode(",", $goCustomKeyData);
							$goCustomValues = implode(",", $goCustomValuesData);
							$goCustomUpdate = implode(", ",  $goCustomUpdateData);
							
							$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $custom_keyValues) 
								VALUES('$goLastInsertedLeadIDDUPSYS', $goCustomValues) 
								ON DUPLICATE KEY UPDATE $goCustomUpdate";
							$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);

						}elseif($goCountTheHeader > 21) {
							$goShowCustomFields = "DESC custom_$list_id;";
							$rsltgoShowCustomFields = $astDB->rawQuery($goShowCustomFields);
							$countResultrsltgoShowCustomFields = $astDB->getRowCount();
					
							if($countResultrsltgoShowCustomFields > 1) {
								$totalExplode = count($goGetLastHeader2);
								
								$goCustomValuesData = array();
								$goCustomUpdateData = array();
								
								for($ax=0; $ax < $totalExplode; $ax++) {
									$goHeaderOfCustomFields = $goGetLastCustomFiledsName2[$ax]; #get the header name of the custom fields
									$goCustomValues = $col[$goGetLastHeader2[$ax]]; #get the values of the custom fields
									array_push($goCustomValuesData, "'$goCustomValues'");
									array_push($goCustomUpdateData, "$goHeaderOfCustomFields='$goCustomValues'");
								}
								$goHeaderOfCustomFields = implode(",", $goGetLastCustomFiledsName2);
								$goCustomValues = implode(",", $goCustomValuesData);
								$goCustomUpdate = implode(", ",  $goCustomUpdateData);
								$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $goHeaderOfCustomFields) 
									VALUES('$goLastInsertedLeadIDDUPSYS', $goCustomValues) 
									ON DUPLICATE KEY UPDATE $goCustomUpdate";
								$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
							}
						}// end set query for custom fields
						$goCountInsertedLeads++;						
					}else{
						$duplicates++;
					}
				}// end else dupsys
			}elseif($goDupcheck == "DUPCAMP") {
				#Duplicate check all LIST in CAMPAIGN
				if($goGetCheckcustomFieldNamesCorrect == "error" && empty($lead_mapping)) {
					fclose($handle);
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
								'first_name' => utf8_encode($first_name),
								'middle_initial' => utf8_encode($middle_initial),
								'last_name' => utf8_encode($last_name),
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
								'entry_list_id' => $entry_list_id,
								'last_local_call_time' => '0000-00-00 00:00:00'
							);
							$rsltGoQueryInsNotDUP = $astDB->insert('vicidial_list', $insertData);
							$goLastInsertedLeadIDDUPCAMP = $astDB->getInsertId();
							
							# start set query for custom fields
							if(!empty($lead_mapping) && !empty($custom_array)){ // LEAD MAPPING CUSTOMIZATION
								$goCustomKeyData = array();
								$goCustomValuesData = array();
								$goCustomUpdateData = array();

								foreach($custom_array as $custom_key => $map_data){
									$goCustomValues = $col[$map_data];
									array_push($goCustomKeyData, "$custom_key");
									array_push($goCustomValuesData, "'$goCustomValues'");
									array_push($goCustomUpdateData, "$custom_key='$goCustomValues'");

									//$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $custom_key) VALUES('$goLastInsertedLeadIDNODUP', '$goCustomValues') ON DUPLICATE KEY UPDATE $custom_key='$goCustomValues'";
									//$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
								}

								$custom_keyValues = implode(",", $goCustomKeyData);
								$goCustomValues = implode(",", $goCustomValuesData);
								$goCustomUpdate = implode(", ",  $goCustomUpdateData);

								$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $custom_keyValues) VALUES('$goLastInsertedLeadIDDUPCAMP', $goCustomValues) ON DUPLICATE KEY UPDATE $goCustomUpdate";
								$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);

							}elseif($goCountTheHeader > 21) {
								$goShowCustomFields = "DESC custom_$list_id;";
								$rsltgoShowCustomFields = $astDB->rawQuery($goShowCustomFields);
								$countResultrsltgoShowCustomFields = $astDB->getRowCount();
								
								if($countResultrsltgoShowCustomFields > 1) {
									$totalExplode = count($goGetLastHeader2);
									$goCustomValuesData = array();
																		$goCustomUpdateData = array();

									for($ax=0; $ax < $totalExplode; $ax++) {
										$goHeaderOfCustomFields = $goGetLastCustomFiledsName2[$ax]; #get the header name of the custom fields
										$goCustomValues = $col[$goGetLastHeader2[$ax]]; #get the values of the custom fields
										#$goQueryCustomFields .= "INSERT INTO custom_$theList (lead_id,".$goHeaderOfCustomFields.") VALUES ('$goLastInsertedLeadIDDUPCAMP','".$goCustomValues."');";
										#$rsltGoQueryCustomFields = mysqli_query($link, $goQueryCustomFields);
										
										#$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $goHeaderOfCustomFields) VALUES('$goLastInsertedLeadIDDUPCAMP', '$goCustomValues') ON DUPLICATE KEY UPDATE $goHeaderOfCustomFields='$goCustomValues'";
										#$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
										
										#$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");
										array_push($goCustomValuesData, "'$goCustomValues'");
										array_push($goCustomUpdateData, "$goHeaderOfCustomFields='$goCustomValues'");

									}

									$goHeaderOfCustomFields = implode(",", $goGetLastCustomFiledsName2);
									$goCustomValues = implode(",", $goCustomValuesData);
									$goCustomUpdate = implode(", ",  $goCustomUpdateData);
									$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $goHeaderOfCustomFields) VALUES('$goLastInsertedLeadIDDUPCAMP', $goCustomValues) ON DUPLICATE KEY UPDATE $goCustomUpdate";
									$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);

								} 
							} 
							# end set query for custom fields
							$goCountInsertedLeads++;
															$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");
						}// end of IF
						else{
							$duplicates++;
						}
					}
				}
				
			}elseif($goDupcheck == "DUPLIST") {
				#Duplicate check within the LIST
				if($goGetCheckcustomFieldNamesCorrect === "error" && empty($lead_mapping)) {
					fclose($handle);
				} else {
					//$goQueryCheckDupPhone = "SELECT phone_number FROM vicidial_list WHERE phone_number='$phone_number' AND list_id='$list_id';";
					$astDB->where('phone_number', $phone_number);
					$astDB->where('list_id', $list_id);
					$rsltCheckDupPhone = $astDB->get('vicidial_list', null, 'phone_number');
					$countResult = $astDB->getRowCount();
					
					////check in vicidial_dnc
					//$astDB->where('phone_number', $phone_number);
					//$resultCheckPhone = $astDB->getOne('vicidial_dnc', 'phone_number');
					//$countResult2 = $astDB->getRowCount();
						
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
							'first_name' => utf8_encode($first_name),
							'middle_initial' => utf8_encode($middle_initial),
							'last_name' => utf8_encode($last_name),
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
							'entry_list_id' => $entry_list_id,
							'last_local_call_time' => '0000-00-00 00:00:00'
						);
						$rsltGoQueryInsDupList = $astDB->insert('vicidial_list', $insertData);
						$goLastInsertedLeadIDDUPLIST = $astDB->getInsertId();
						$alex["insertquery"] = $astDB->getLastQuery();
						# start set query for custom fields
						if(!empty($lead_mapping) && !empty($custom_array)){ // LEAD MAPPING CUSTOMIZATION
							$goCustomKeyData = array();
							$goCustomValuesData = array();
							$goCustomUpdateData = array();

							foreach($custom_array as $custom_key => $map_data){
								$goCustomValues = $col[$map_data];
								array_push($goCustomKeyData, "$custom_key");
								array_push($goCustomValuesData, "'$goCustomValues'");
								array_push($goCustomUpdateData, "$custom_key='$goCustomValues'");

								//$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $custom_key) VALUES('$goLastInsertedLeadIDNODUP', '$goCustomValues') ON DUPLICATE KEY UPDATE $custom_key='$goCustomValues'";
								//$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
							}

							$custom_keyValues = implode(",", $goCustomKeyData);
							$goCustomValues = implode(",", $goCustomValuesData);
							$goCustomUpdate = implode(", ",  $goCustomUpdateData);

							$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $custom_keyValues) VALUES('$goLastInsertedLeadIDDUPLIST', $goCustomValues) ON DUPLICATE KEY UPDATE $goCustomUpdate";
							$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
						}elseif($goCountTheHeader > 21) {
							$goShowCustomFields = "DESC custom_$list_id;";
							$rsltgoShowCustomFields = $astDB->rawQuery($goShowCustomFields);
							$countResultrsltgoShowCustomFields = $astDB->getRowCount();
							
							if($countResultrsltgoShowCustomFields > 1) {
								$totalExplode = count($goGetLastHeader2);

								$goCustomValuesData = array();
	                                                        $goCustomUpdateData = array();

								for($ax=0; $ax < $totalExplode; $ax++) {
									$goHeaderOfCustomFields = $goGetLastCustomFiledsName2[$ax]; #get the header name of the custom fields
									$goCustomValues = $col[$goGetLastHeader2[$ax]]; #get the values of the custom fields
										
									#$goQueryCustomFields = "INSERT INTO custom_$theList (lead_id,".$goHeaderOfCustomFields.") VALUES ('$goLastInsertedLeadIDDUPLIST','".$goCustomValues."');";
									#$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $goHeaderOfCustomFields) VALUES('$goLastInsertedLeadIDDUPLIST', '$goCustomValues') ON DUPLICATE KEY UPDATE $goHeaderOfCustomFields='$goCustomValues'";
									#$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
									
									#$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");
									array_push($goCustomValuesData, "'$goCustomValues'");
                                                                        array_push($goCustomUpdateData, "$goHeaderOfCustomFields='$goCustomValues'");

								}
								$goHeaderOfCustomFields = implode(",", $goGetLastCustomFiledsName2);
								$goCustomValues = implode(",", $goCustomValuesData);
								$goCustomUpdate = implode(", ",  $goCustomUpdateData);
								$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $goHeaderOfCustomFields) VALUES('$goLastInsertedLeadIDDUPLIST', $goCustomValues) ON DUPLICATE KEY UPDATE $goCustomUpdate";

								$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
							}
						}
						$goCountInsertedLeads++;
                        $apiresults = array("result" => "success", "message" => "$goCountInsertedLeads"); 
						# end set query for custom fields
					}//end if
					else{
						//fclose($handle);
						$duplicates++;
						//$apiresults = array("result" => "error" , "message" => "Error: Lead File Contains Duplicates in List");
					}
				}
			} else {
				#NO DUPLICATE CHECK
				if($goGetCheckcustomFieldNamesCorrect === "error" && empty($lead_mapping)) {
					fclose($handle);
				} else {
					$USarea = substr($phone_number, 0, 3);
					$gmt_offset = lookup_gmt($astDB, $phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code,$owner);
			
					//$test_query = "INSERT INTO vicidial_list (lead_id, entry_date, status, vendor_lead_code, list_id, gmt_offset_now, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, entry_list_id, last_local_call_time) VALUES ('', '$entry_date', '$status', '$vendor_lead_code', '$list_id', '$gmt_offset', '$phone_code', '$phone_number', '$title',	'$first_name', '$middle_initial', '$last_name',	'$address1', '$address2', '$address3', '$city',	'$state', '$province', '$postal_code', '$country_code',	'$gender', '$date_of_birth', '$alt_phone', '$email', '$security_phrase', '$comments', '$entry_list_id', '0000-00-00 00:00:00');";
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
						'first_name' => utf8_encode($first_name),
						'middle_initial' => utf8_encode($middle_initial),
						'last_name' => utf8_encode($last_name),
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
						'entry_list_id' => $entry_list_id,
						'last_local_call_time' => '0000-00-00 00:00:00'
					);
					$rsltGoQueryIns = $astDB->insert('vicidial_list', $insertData);
					$goLastInsertedLeadIDNODUP = $astDB->getInsertId();

					$alex["query_insert"] = $astDB->getLastQuery();
					$alex["error_insert"] = $astDB->getLastError();
	
					# start set query for custom fields
					if(!empty($lead_mapping) && !empty($custom_array)){ //LEAD MAPPING CUSTOMIZATION
						$goCustomKeyData = array();
						$goCustomValuesData = array();
                                                $goCustomUpdateData = array();

						foreach($custom_array as $custom_key => $map_data){
							$goCustomValues = $col[$map_data];
							array_push($goCustomKeyData, "$custom_key");
							array_push($goCustomValuesData, "'$goCustomValues'");
                            array_push($goCustomUpdateData, "$custom_key='$goCustomValues'");

							//$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $custom_key) VALUES('$goLastInsertedLeadIDNODUP', '$goCustomValues') ON DUPLICATE KEY UPDATE $custom_key='$goCustomValues'";
                            //$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
						}

						$custom_keyValues = implode(",", $goCustomKeyData);
						$goCustomValues = implode(",", $goCustomValuesData);
						$goCustomUpdate = implode(", ",  $goCustomUpdateData);
					
						$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $custom_keyValues) VALUES('$goLastInsertedLeadIDNODUP', $goCustomValues) ON DUPLICATE KEY UPDATE $goCustomUpdate";
                        $rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
					
					}elseif($goCountTheHeader > 21) {
						$goShowCustomFields = "DESC custom_$list_id;";
						$rsltgoShowCustomFields = $astDB->rawQuery($goShowCustomFields);
						$countResultrsltgoShowCustomFields = $astDB->getRowCount();
						
						if($countResultrsltgoShowCustomFields > 1) {
							$totalExplode = count($goGetLastHeader2);

							$goCustomValuesData = array();
							$goCustomUpdateData = array();

							for($ax=0; $ax < $totalExplode; $ax++) {
								$goHeaderOfCustomFields = $goGetLastCustomFiledsName2[$ax]; #get the header name of the custom fields
								$goCustomValues = $col[$goGetLastHeader2[$ax]]; #get the values of the custom fields
									
								#$rsltGoQueryCustomFields = mysqli_query($link, $goQueryCustomFields);
								
//								$goQueryCustomFields .= "INSERT INTO custom_$theList(lead_id, $goHeaderOfCustomFields) VALUES('$goLastInsertedLeadIDNODUP', '$goCustomValues') ON DUPLICATE KEY UPDATE $goHeaderOfCustomFields='$goCustomValues';";
								//$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);

								#$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads");

								array_push($goCustomValuesData, "'$goCustomValues'");
								array_push($goCustomUpdateData, "$goHeaderOfCustomFields='$goCustomValues'");

							}

							$goHeaderOfCustomFields = implode(",", $goGetLastCustomFiledsName2);
							$goCustomValues = implode(",", $goCustomValuesData);
							$goCustomUpdate = implode(", ",  $goCustomUpdateData);
							$goQueryCustomFields = "INSERT INTO custom_$theList(lead_id, $goHeaderOfCustomFields) VALUES('$goLastInsertedLeadIDNODUP', $goCustomValues) ON DUPLICATE KEY UPDATE $goCustomUpdate";

							$rsltGoQueryCustomFields = $astDB->rawQuery($goQueryCustomFields);
						} 	
					}
					
					$goCountInsertedLeads++;
					$apiresults = array("result" => "success", "message" => "$goCountInsertedLeads", "alex_data" => $alex);

				# end set query for custom fields
				}
			} #end No Duplicate check
			//fclose($handle);
			$counter++;
		} #end while
	
		fclose($handle);

		if($goCountInsertedLeads > 0 && $duplicates < 1) {
			$apiresults = array("result" => "success", "message" => "Total Uploaded Leads: $goCountInsertedLeads" , "alex_data" => $alex);
		}elseif($goCountInsertedLeads > 0 && $duplicates > 0){
			$apiresults = array("result" => "success", "message" => "Uploaded:$goCountInsertedLeads    Duplicates:$duplicates");
		} elseif($goGetCheckcustomFieldNamesCorrect == "error"){
			$apiresults = array("result" => "error" , "message" => "Error: Lead File Not Compatible with List. Incompatible Field Names. Check the File Headers $goGetCheckcustomFieldNamesCorrect");
		}elseif($duplicates > 0){
			$apiresults = array("result" => "error" , "message" => "Duplicates Found : $duplicates");
		}else {
			$apiresults = array("result" => "error", "message" => "$goCountInsertedLeads", "duplicates" => $duplicates, "alex_data" => $alex);
		}
		
		$log_id = log_action($goDB, 'UPLOAD', $log_user, $log_ip, "Uploaded {$goCountInsertedLeads} leads on List ID $theList", $log_group);
		
	} // END IF handle
	
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

		$goCustomCheckQuery = "SELECT EXISTS(SELECT $gocustomFieldsCSV FROM custom_$goCClistID)";
		$customCheck = $link->rawQuery($goCustomCheckQuery);
		$countCustomCheck = $link->getRowCount();
	
		if( $countCustomCheck === 0 ){
			return "error";
		} else {
			return "success";
		}

		/*$rsltSQLCHECK = $link->get("custom_$goCClistID", null, "$gocustomFieldsCSV");
		$query = $link->getLastQuery();
		
		if(!$rsltSQLCHECK){
			$goRetMessage = "error";
			$goRetMessage = "$query";
		} else {
			/*$goShowCustomFields = "DESC custom_$goCClistID;";
			$rsltgoShowCustomFields = $link->rawQuery($goShowCustomFields);
			$countResultrsltgoShowCustomFields = $link->getRowCount();
				
			if($countResultrsltgoShowCustomFields > 1) {
				foreach ($rsltgoShowCustomFields as $fresultsShow){
					$goCustomFields .= $fresultsShow['Field'].",";
				}
				
				$goRetMessage = preg_replace("/,$/",'',$goCustomFields);
			}
			$goRetMessage = "success";
		}
				
		return $goRetMessage;*/
	}
	
	// check 1st if fields are not less than 21
	// check 2nd if greater than 21 check the field name spelling from csv vs on the DB custom_LISTID
	// lookup_gmt extgetval
	// $field_regx = "/['\"`\\;]/";
	// $vendor_lead_code =             preg_match($field_regx, "", $vendor_lead_code);
	// $vendor_lead_code =             preg_replace($field_regx, "", $vendor_lead_code);

//echo "File data successfully imported to database!!";
?>
