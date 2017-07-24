<?php
   //////////////////////////////////////////////////////
   /// Name: goEditLeads.php 		///
   /// Description: API to edit specific lead 		///
   /// Version: 4.0 		///
   /// Copyright: GOAutoDial Ltd. (c) 2011-2016 		///
   /// Written by: Alexander Abenoja _m/ 		///
   /// License: AGPLv2 		///
   /////////////////////////////////////////////////////
	
    include_once ("../goFunctions.php");
    
    // POST or GET Variables
        $goUser = $_REQUEST['goUser'];
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
        $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
        $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

        $lead_id 		= mysqli_real_escape_string($link, $_REQUEST['lead_id']);
        $first_name 		= mysqli_real_escape_string($link, $_REQUEST['first_name']);
        $middle_initial 		= mysqli_real_escape_string($link, $_REQUEST['middle_initial']);
        $last_name 		= mysqli_real_escape_string($link, $_REQUEST['last_name']);
        $gender 		= mysqli_real_escape_string($link, $_REQUEST['gender']);
        $email 		= mysqli_real_escape_string($link, $_REQUEST['email']);
        $phone_number 		= mysqli_real_escape_string($link, $_REQUEST['phone_number']);
        $alt_phone 		= mysqli_real_escape_string($link, $_REQUEST['alt_phone']);
        $address1 		= mysqli_real_escape_string($link, $_REQUEST['address1']);
        $address2 		= mysqli_real_escape_string($link, $_REQUEST['address2']);
        $address3 		= mysqli_real_escape_string($link, $_REQUEST['address3']);
        $city 		= mysqli_real_escape_string($link, $_REQUEST['city']);
        $province 		= mysqli_real_escape_string($link, $_REQUEST['province']);
        $postal_code 		= mysqli_real_escape_string($link, $_REQUEST['postal_code']);
        $country_code 		= mysqli_real_escape_string($link, $_REQUEST['country_code']);
        $date_of_birth 		= mysqli_real_escape_string($link, $_REQUEST['date_of_birth']);
        $title 		= mysqli_real_escape_string($link, $_REQUEST['title']);
        $status 		= mysqli_real_escape_string($link, $_REQUEST['status']);
        $is_customer 		= mysqli_real_escape_string($link, $_REQUEST['is_customer']);

	$user_id 		= mysqli_real_escape_string($link, $_REQUEST['user_id']);
	$user 		= mysqli_real_escape_string($link, $_REQUEST['user']);
	$avatar 		= mysqli_real_escape_string($link, $_REQUEST['avatar']); //base64 encoded
        
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
		
		$query_check = "SELECT * FROM vicidial_list WHERE lead_id = '$lead_id';";
		$exec_query_check = mysqli_query($link, $query_check);
		$num_query_check = mysqli_num_rows($exec_query_check);
		
		if($num_query_check > 0){
			while($fresults = mysqli_fetch_array($exec_query_check)){
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
			
			$updateQuery = mysqli_query($link, $query) or die(mysqli_error($link));
			$updateQuerygo = mysqli_query($linkgo, $querygo) or die(mysqli_error($linkgo));
			
			if($updateQuery > 0){
				if ($is_customer) {
					if(!empty($session_user))
					$rsltu = mysqli_query($link, "SELECT user_group FROM vicidial_users WHERE user='$session_user';") or die(mysqli_error($link));
					else
					$rsltu = mysqli_query($link, "SELECT user_group FROM vicidial_users WHERE user_id='$user_id';") or die(mysqli_error($link));
					
					$fresults = mysqli_fetch_array($rsltu, MYSQLI_ASSOC);
					$user_group = $fresults['user_group'];
					
					$rsltg = mysqli_query($linkgo, "SELECT group_list_id FROM user_access_group WHERE user_group='$user_group';") or die(mysqli_error($linkgo));
					$fresults = mysqli_fetch_array($rsltg, MYSQLI_ASSOC);
					$group_list_id = $fresults['group_list_id'];
					
					$countrsltgo = mysqli_num_rows($updateQuerygo);                
					
					if ($countrsltgo < 1) {
						$querygo = "INSERT
						INTO go_customers 
						VALUES (null, '$lead_id', '$group_list_id', '$avatar');";
						$rsltgo = mysqli_query($linkgo, $querygo) or die(mysqli_error($linkgo));
						$fresultsgo = mysqli_fetch_array($rsltgo, MYSQLI_ASSOC);
						
					} else {
						$querygo = "UPDATE go_customers 
						SET avatar = '$avatar', group_list_id='$group_list_id'
						WHERE lead_id ='$lead_id';";
						$rsltgo = mysqli_query($linkgo, $querygo) or die(mysqli_error($linkgo));
						$fresultsgo = mysqli_fetch_array($rsltgo, MYSQLI_ASSOC);
					}
					
				} else {
					$querygo = "INSERT
					INTO go_customers 
					VALUES (null, '$lead_id', '$group_list_id', '$avatar') 
					WHERE lead_id ='$lead_id';"; 
					$rsltgo = mysqli_query($linkgo, $querygo) or die(mysqli_error($linkgo));
					$fresultsgo = mysqli_fetch_array($rsltgo, MYSQLI_ASSOC);
				}
				
				$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified the Lead ID: $lead_id", $log_group, $query, $querygo);
				
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