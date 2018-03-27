<?php
 /**
 * @file 		goUpdateLead.php
 * @brief 		API for Agent UI
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad <chris@goautodial.com>
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

$is_logged_in = check_agent_login($astDB, $goUser);

$agent = get_settings('user', $astDB, $goUser);
$system_settings = get_settings('system', $astDB);
$phone_settings = get_settings('phone', $astDB, $agent->phone_login, $agent->phone_pass);
$user = $agent->user;

if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goVendorLeadCode'])) { $vendor_lead_code = $astDB->escape($_GET['goVendorLeadCode']); }
    else if (isset($_POST['goVendorLeadCode'])) { $vendor_lead_code = $astDB->escape($_POST['goVendorLeadCode']); }
if (isset($_GET['goPhoneNumber'])) { $phone_number = $astDB->escape($_GET['goPhoneNumber']); }
    else if (isset($_POST['goPhoneNumber'])) { $phone_number = $astDB->escape($_POST['goPhoneNumber']); }
if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
    else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }
if (isset($_GET['goTitle'])) { $title = $astDB->escape($_GET['goTitle']); }
    else if (isset($_POST['goTitle'])) { $title = $astDB->escape($_POST['goTitle']); }
if (isset($_GET['goFirstName'])) { $first_name = $astDB->escape($_GET['goFirstName']); }
    else if (isset($_POST['goFirstName'])) { $first_name = $astDB->escape($_POST['goFirstName']); }
if (isset($_GET['goMiddleInitial'])) { $middle_initial = $astDB->escape($_GET['goMiddleInitial']); }
    else if (isset($_POST['goMiddleInitial'])) { $middle_initial = $astDB->escape($_POST['goMiddleInitial']); }
if (isset($_GET['goLastName'])) { $last_name = $astDB->escape($_GET['goLastName']); }
    else if (isset($_POST['goLastName'])) { $last_name = $astDB->escape($_POST['goLastName']); }
if (isset($_GET['goAddress1'])) { $address1 = $astDB->escape($_GET['goAddress1']); }
    else if (isset($_POST['goAddress1'])) { $address1 = $astDB->escape($_POST['goAddress1']); }
if (isset($_GET['goAddress2'])) { $address2 = $astDB->escape($_GET['goAddress2']); }
    else if (isset($_POST['goAddress2'])) { $address2 = $astDB->escape($_POST['goAddress2']); }
if (isset($_GET['goAddress3'])) { $address3 = $astDB->escape($_GET['goAddress3']); }
    else if (isset($_POST['goAddress3'])) { $address3 = $astDB->escape($_POST['goAddress3']); }
if (isset($_GET['goCity'])) { $city = $astDB->escape($_GET['goCity']); }
    else if (isset($_POST['goCity'])) { $city = $astDB->escape($_POST['goCity']); }
if (isset($_GET['goState'])) { $state = $astDB->escape($_GET['goState']); }
    else if (isset($_POST['goState'])) { $state = $astDB->escape($_POST['goState']); }
if (isset($_GET['goProvince'])) { $province = $astDB->escape($_GET['goProvince']); }
    else if (isset($_POST['goProvince'])) { $province = $astDB->escape($_POST['goProvince']); }
if (isset($_GET['goPostalCode'])) { $postal_code = $astDB->escape($_GET['goPostalCode']); }
    else if (isset($_POST['goPostalCode'])) { $postal_code = $astDB->escape($_POST['goPostalCode']); }
if (isset($_GET['goCountryCode'])) { $country_code = $astDB->escape($_GET['goCountryCode']); }
    else if (isset($_POST['goCountryCode'])) { $country_code = $astDB->escape($_POST['goCountryCode']); }
if (isset($_GET['goGender'])) { $gender = $astDB->escape($_GET['goGender']); }
    else if (isset($_POST['goGender'])) { $gender = $astDB->escape($_POST['goGender']); }
if (isset($_GET['goDateOfBirth'])) { $date_of_birth = $astDB->escape($_GET['goDateOfBirth']); }
    else if (isset($_POST['goDateOfBirth'])) { $date_of_birth = $astDB->escape($_POST['goDateOfBirth']); }
if (isset($_GET['goComments'])) { $comments = $astDB->escape($_GET['goComments']); }
    else if (isset($_POST['goComments'])) { $comments = $astDB->escape($_POST['goComments']); }
if (isset($_GET['goALTPhone'])) { $alt_phone = $astDB->escape($_GET['goALTPhone']); }
    else if (isset($_POST['goALTPhone'])) { $alt_phone = $astDB->escape($_POST['goALTPhone']); }
if (isset($_GET['goEmail'])) { $email = $astDB->escape($_GET['goEmail']); }
    else if (isset($_POST['goEmail'])) { $email = $astDB->escape($_POST['goEmail']); }
if (isset($_GET['goSecurity'])) { $security_phrase = $astDB->escape($_GET['goSecurity']); }
    else if (isset($_POST['goSecurity'])) { $security_phrase = $astDB->escape($_POST['goSecurity']); }

if (isset($_GET['goCustomFields'])) { $custom_fields = $astDB->escape($_GET['goCustomFields']); }
    else if (isset($_POST['goCustomFields'])) { $custom_fields = $astDB->escape($_POST['goCustomFields']); }

$MT[0] = '';
$errormsg = 0;
$DO_NOT_UPDATE = 0;
$DO_NOT_UPDATE_text = '';

if ($is_logged_in) {
	if ( (strlen($phone_number) < 1) || (strlen($lead_id) < 1) ) {
        $APIResult = array( "result" => "error", "message" => "Phone Number or Lead ID is NOT valid" );
	} else {
		//$stmt = "SELECT disable_alter_custdata,disable_alter_custphone FROM vicidial_campaigns where campaign_id='$campaign'";
        $astDB->where('campaign_id', $campaign);
        $rslt = $astDB->get('vicidial_campaigns', null, 'disable_alter_custdata,disable_alter_custphone');
		$dac_conf_ct = $astDB->getRowCount();
		$i = 0;
		while ($i < $dac_conf_ct) {
			$row = $rslt[$i];
			$disable_alter_custdata =	$row['disable_alter_custdata'];
			$disable_alter_custphone =	$row['disable_alter_custphone'];
			$i++;
		}
		if ( (preg_match('/Y/', $disable_alter_custdata)) or (preg_match('/Y/', $disable_alter_custphone)) ) {
			if (preg_match('/Y/',$disable_alter_custdata)) {
				$DO_NOT_UPDATE = 1;
				$DO_NOT_UPDATE_text = ' NOT';
			}
			if (preg_match('/Y/',$disable_alter_custphone)) 				{
				$DO_NOT_UPDATEphone = 1;
			}
			//$stmt = "SELECT alter_custdata_override,alter_custphone_override FROM vicidial_users where user='$user'";
            $astDB->where('user', $user);
            $rslt = $astDB->get('vicidial_users', null, 'alter_custdata_override,alter_custphone_override');
			$aco_conf_ct = $astDB->getRowCount();
			$i = 0;
			while ($i < $aco_conf_ct) {
				$row = $rslt[$i];
				$alter_custdata_override =	$row['alter_custdata_override'];
				$alter_custphone_override = $row['alter_custphone_override'];
				$i++;
			}
			if (preg_match('/ALLOW_ALTER/', $alter_custdata_override)) {
				$DO_NOT_UPDATE = 0;
				$DO_NOT_UPDATE_text = '';
			}
			if (preg_match('/ALLOW_ALTER/', $alter_custphone_override)) {
				$DO_NOT_UPDATEphone = 0;
			}
		}

		if ($DO_NOT_UPDATE < 1) {
			$comments = preg_replace("/\r/i", '', $comments);
			$comments = preg_replace("/\n/i", '!N', $comments);
			$comments = preg_replace("/--AMP--/i", '&', $comments);
			$comments = preg_replace("/--QUES--/i", '?', $comments);
			$comments = preg_replace("/--POUND--/i", '#', $comments);
			
			$address1 = preg_replace("/\r/i", '', $address1);
			$address1 = preg_replace("/\n/i", '!N', $address1);
			
			$address2 = preg_replace("/\r/i", '', $address2);
			$address2 = preg_replace("/\n/i", '!N', $address2);

			$updateData = array(
                'vendor_lead_code' => $vendor_lead_code,
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
                'comments' => $comments
            );
			if ($DO_NOT_UPDATEphone < 1) {
                $phoneSQL = array(
                    'phone_number' => $phone_number
                );
                $updateData = array_merge($updateData, $phoneSQL);
            }

			//$stmt="UPDATE vicidial_list set vendor_lead_code='" . mysqli_real_escape_string($vendor_lead_code) . "', title='" . mysqli_real_escape_string($title) . "', first_name='" . mysqli_real_escape_string($first_name) . "', middle_initial='" . mysqli_real_escape_string($middle_initial) . "', last_name='" . mysqli_real_escape_string($last_name) . "', address1='" . mysqli_real_escape_string($address1) . "', address2='" . mysqli_real_escape_string($address2) . "', address3='" . mysqli_real_escape_string($address3) . "', city='" . mysqli_real_escape_string($city) . "', state='" . mysqli_real_escape_string($state) . "', province='" . mysqli_real_escape_string($province) . "', postal_code='" . mysqli_real_escape_string($postal_code) . "', country_code='" . mysqli_real_escape_string($country_code) . "', gender='" . mysqli_real_escape_string($gender) . "', date_of_birth='" . mysqli_real_escape_string($date_of_birth) . "', alt_phone='" . mysqli_real_escape_string($alt_phone) . "', email='" . mysqli_real_escape_string($email) . "', security_phrase='" . mysqli_real_escape_string($security_phrase) . "', comments='" . mysqli_real_escape_string($comments) . "' $phoneSQL where lead_id='$lead_id';";
            $astDB->where('lead_id', $lead_id);
            $rslt = $astDB->update('vicidial_list', $updateData);
		}
		
		if ($system_settings->custom_fields_enabled > 0 && (isset($custom_fields) && strlen($custom_fields) > 0)) {
			$custom_fields = explode(',', $custom_fields);
			$fields = array();
			$custom_fields_SQL = '';
			foreach($custom_fields as $label) {
				if (isset($_GET[$label])) { $fields[$label] = $astDB->escape($_GET[$label]); }
					else if (isset($_POST[$label])) { $fields[$label] = $astDB->escape($_POST[$label]); }
				
				$fields[$label] = preg_replace("/\r/i", '', $fields[$label]);
				$fields[$label] = preg_replace("/\n/i", '!N', $fields[$label]);
				$fields[$label] = preg_replace("/--AMP--/i", '&', $fields[$label]);
				$fields[$label] = preg_replace("/--QUES--/i", '?', $fields[$label]);
				$fields[$label] = preg_replace("/--POUND--/i", '#', $fields[$label]);
				
				if (strlen($fields[$label]) > 0) {
					$custom_fields_SQL .= "$label,";
				}
			}
			$custom_fields_SQL = trim($custom_fields_SQL, ",");
			
			$astDB->where('lead_id', $lead_id);
			$rslt = $astDB->getOne('vicidial_list', 'list_id');
			$list_id = $rslt['list_id'];
			$custom_listid = "custom_{$list_id}";
			
			$astDB->has($custom_listid);
			$lastError = $astDB->getLastError();
			if (strlen($lastError) < 1) {
				$astDB->where('lead_id', $lead_id);
				$rslt = $astDB->getOne($custom_listid);
				$lead_exist = $astDB->getRowCount();
				
				if ($lead_exist) {
					$astDB->where('lead_id', $lead_id);
					$astDB->update($custom_listid, $fields);
					
					$update_success = $astDB->getRowCount();
				} else {
					$fields['lead_id'] = $lead_id;
					
					$astDB->insert($custom_listid, $fields);
					$lastError = $astDB->getLastError();
					$insert_success = $astDB->getRowCount();
				}
			}
		}
		
		$random = (rand(1000000, 9999999) + 10000000);
		//$stmt="UPDATE vicidial_live_agents set random_id='$random' where user='$user' and server_ip='$server_ip';";
        $astDB->where('user', $user);
        $astDB->where('server_ip', $server_ip);
        $rslt = $astDB->update('vicidial_live_agents', array( 'random_id' => $random ));
        $errno = strlen($astDB->getLastError());
		$retry_count = 0;
		while ( ($errno > 0) and ($retry_count < 9) ) {
            $astDB->where('user', $user);
            $astDB->where('server_ip', $server_ip);
            $rslt = $astDB->update('vicidial_live_agents', array( 'random_id' => $random ));
            $errno = strlen($astDB->getLastError());
			$retry_count++;
		}
        
        $APIResult = array( "result" => "success", "message" => "Lead $lead_id information has$DO_NOT_UPDATE_text been updated", "last_error" => $lastError );
    }
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>