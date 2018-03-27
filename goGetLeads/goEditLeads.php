<?php
 /**
 * @file 		goEditLeads.php
 * @brief 		API for Editing Leads
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Abenoja  <alex@goautodial.com>
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

    // POST or GET Variables
	$goUser = $astDB->escape($_REQUEST['goUser']);
	$ip_address = $astDB->escape($_REQUEST['hostname']);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);

	$lead_id 		= $astDB->escape($_REQUEST['lead_id']);
	$first_name 		= $astDB->escape($_REQUEST['first_name']);
	$middle_initial 		= $astDB->escape($_REQUEST['middle_initial']);
	$last_name 		= $astDB->escape($_REQUEST['last_name']);
	$gender 		= $astDB->escape($_REQUEST['gender']);
	$email 		= $astDB->escape($_REQUEST['email']);
	$phone_number 		= $astDB->escape($_REQUEST['phone_number']);
	$alt_phone 		= $astDB->escape($_REQUEST['alt_phone']);
	$address1 		= $astDB->escape($_REQUEST['address1']);
	$address2 		= $astDB->escape($_REQUEST['address2']);
	$address3 		= $astDB->escape($_REQUEST['address3']);
	$city 		= $astDB->escape($_REQUEST['city']);
	$province 		= $astDB->escape($_REQUEST['province']);
	$postal_code 		= $astDB->escape($_REQUEST['postal_code']);
	$country_code 		= $astDB->escape($_REQUEST['country_code']);
	$date_of_birth 		= $astDB->escape($_REQUEST['date_of_birth']);
	$title 		= $astDB->escape($_REQUEST['title']);
	$status 		= $astDB->escape($_REQUEST['status']);
	$is_customer 		= $astDB->escape($_REQUEST['is_customer']);

	$user_id 		= $astDB->escape($_REQUEST['user_id']);
	$user 		= $astDB->escape($_REQUEST['user']);
	$avatar 		= $astDB->escape($_REQUEST['avatar']); //base64 encoded
	
	$defGender = array("M", "F", "U");
	
	if(empty($lead_id) || empty($session_user)){
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	}elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $first_name) && !empty($first_name)){
		$err_msg = error_handle("41004", "first_name");
		$apiresults = array("code" => "41004", "result" => $err_msg);
	}elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $middle_initial) && !empty($middle_initial)){
		$err_msg = error_handle("41004", "middle_initial");
		$apiresults = array("code" => "41004", "result" => $err_msg);
	}elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $last_name) && !empty($last_name)){
		$err_msg = error_handle("41004", "last_name");
		$apiresults = array("code" => "41004", "result" => $err_msg);
	}elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_number) && !empty($phone_number)){
		$err_msg = error_handle("41004", "phone_number");
		$apiresults = array("code" => "41004", "result" => $err_msg);
	}elseif(!in_array($gender,$defGender) && $gender != null){
		$err_msg = error_handle("41006", "gender");
		$apiresults = array("code" => "41006", "result" => $err_msg);
	}elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $web_form_address)){
		$err_msg = error_handle("41004", "web_form_address");
		$apiresults = array("code" => "41004", "result" => $err_msg);
	}else{
		
		//$query_check = "SELECT * FROM vicidial_list WHERE lead_id = '$lead_id';";
		$astDB->where('lead_id', $lead_id);
		$exec_query_check = $astDB->get('vicidial_list');
		$num_query_check = $astDB->getRowCount();
		
		if($num_query_check > 0){
			foreach ($exec_query_check as $fresults){
				$data_first_name = $fresults['first_name'];
				$data_middle_initial = $fresults['middle_initial'];
				$data_last_name = $fresults['last_name'];
				$data_gender = $fresults['gender'];
				$data_email = $fresults['email'];
				$data_phone_number = $fresults['phone_number'];
				$data_alt_phone = $fresults['alt_phone'];
				$data_address1 = $fresults['address1'];
				$data_address2 = $fresults['address2'];
				$data_address3 = $fresults['address3'];
				$data_city = $fresults['city'];
				$data_province = $fresults['province'];
				$data_postal_code = $fresults['postal_code'];
				$data_country_code = $fresults['country_code'];
				$data_date_of_birth = $fresults['date_of_birth'];
				$data_title = $fresults['title'];
				$data_status = $fresults['status'];
			}
			if(empty($first_name))
				$first_name = $data_first_name;
			if(empty($middle_initial))
				$middle_initial = $data_middle_initial;
			if(empty($last_name))
				$last_name = $data_last_name;
			if(empty($gender))
				$gender = $data_gender;
			if(empty($email))
				$email = $data_email;
			if(empty($phone_number))
				$phone_number = $data_phone_number;
			if(empty($alt_phone))
				$alt_phone = $data_alt_phone;
			if(empty($address1))
				$address1 = $data_address1;
			if(empty($address2))
				$address2 = $data_address2;
			if(empty($address3))
				$address3 = $data_address3;
			if(empty($city))
				$city = $data_city;
			if(empty($province))
				$province = $data_province;
			if(empty($postal_code))
				$postal_code = $data_postal_code;
			if(empty($country_code))
				$country_code = $data_country_code;
			if(empty($date_of_birth))
				$date_of_birth = $data_date_of_birth;
			if(empty($title))
				$title = $data_title;
			if(empty($status))
				$status = $data_status;
				
			$query = "UPDATE vicidial_list
			SET first_name = '$first_name',
			middle_initial = '$middle_initial',
			last_name = '$last_name',
			gender = '$gender',
			email = '$email',
			phone_number = '$phone_number',
			alt_phone = '$alt_phone',
			address1 = '$address1',
			address2 = '$address2',
			address3 = '$address3',
			city = '$city',
			province = '$province',
			postal_code = '$postal_code',
			country_code = '$country_code',
			date_of_birth = '$date_of_birth',
			title = '$title',
			status = '$status'
			WHERE lead_id = '$lead_id';";
			
			$querygo = "SELECT '$lead_id'
			FROM go_customers
			WHERE lead_id ='$lead_id' 
			LIMIT 1;";                
			
			$updateQuery = $astDB->rawQuery($query);
			$updateCnter = $astDB->getRowCount();
			$updateQuerygo = $goDB->rawQuery($querygo);
			$countrsltgo = $goDB->getRowCount();
			
			if($updateCnter > 0){
				if ($is_customer > 0) {
					if(!empty($session_user)) {
						//$rsltu = mysqli_query($link, "SELECT user_group FROM vicidial_users WHERE user='$session_user';") or die(mysqli_error($link));
						$astDB->where('user', $session_user);
						$rsltu = $astDB->get('vicidial_users', null, 'user_group');
					} else {
						//$rsltu = mysqli_query($link, "SELECT user_group FROM vicidial_users WHERE user_id='$user_id';") or die(mysqli_error($link));
						$astDB->where('user_id', $user_id);
						$rsltu = $astDB->get('vicidial_users', null, 'user_group');
					}
					
					$fresults = $rsltu[0];
					$user_group = $fresults['user_group'];
					
					//$rsltg = mysqli_query($linkgo, "SELECT group_list_id FROM user_access_group WHERE user_group='$user_group';") or die(mysqli_error($linkgo));
					$goDB->where('user_group', $user_group);
					$fresults = $goDB->getOne('user_access_group', 'group_list_id');
					$group_list_id = $fresults['group_list_id'];
					
					if ($countrsltgo < 1) {
						$querygo = "INSERT INTO go_customers VALUES (null, '$lead_id', '$group_list_id', '$avatar');";
						$fresultsgo = $goDB->rawQuery($querygo);
						
					} else {
						$querygo = "UPDATE go_customers SET avatar = '$avatar', group_list_id='$group_list_id' WHERE lead_id ='$lead_id';";
						$fresultsgo = $goDB->rawQuery($querygo);
					}
					
				}
				
				$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified the Lead ID: $lead_id", $log_group, $query, $querygo);
				
				$apiresults = array("result" => "success");
			}else{
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010", "result" => $err_msg);
				//$apiresults = array("result" => "Error: Failed to Update");
			}
		}else{
			$err_msg = error_handle("41004", "lead_id. Doesn't exist");
			$apiresults = array("code" => "41004", "result" => $err_msg);
		}
	}
?>