<?php
    #######################################################
    #### Name: goGetLeadsInfo.php	                   ####
    #### Description: API to get specific contact      ####
    #### Copyright: GOAutoDial Inc. (c) 2016           ####
    #### Written by: Alexander Abenoja _m/             ####
    #######################################################
    include_once ("goFunctions.php");
    
    ### POST or GET Variables
	$lead_id = $_REQUEST['lead_id'];
    
    ### Check user_id if its null or empty
	if($lead_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Lead ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "AND user='$user_id'";
    		} else { 
			$ul = "AND user='$user_id' AND user_group='$groupId'";  
		}

        	if ($groupId != 'ADMIN') {
        		$notAdminSQL = "AND user_group != 'ADMIN'";
                }

   		$query = "SELECT list_id,first_name,middle_initial,last_name,email,phone_number,alt_phone,address1,address2,address3,city,state,province,postal_code,gender FROM vicidial_list where lead_id='$lead_id'";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
	                $dataUser[] = $fresults['list_id'];
        	        $dataFirstName[] = $fresults['first_name'];
                	$dataMiddleInitial[] = $fresults['middle_initial'];
			$dataLastName[] = $fresults['last_name'];
			$dataEmail[] = $fresults['email'];
			$dataPhoneNumber[] = $fresults['phone_number'];
			$dataAltPhone[] = $fresults['alt_phone'];
			$dataAddress1[] = $fresults['address1'];
			$dataAddress2[] = $fresults['address2'];
			$dataAddress3[] = $fresults['address3'];
			$dataCity[] = $fresults['city'];
			$dataState[] = $fresults['state'];
			$dataProvince[] = $fresults['province'];
			$dataPostalCode[] = $fresults['postal_code'];
			$dataGender[] = $fresults['gender'];

        	        $apiresults = array("result" => "success","list_id" => $dataUser, "first_name" => $dataFirstName, "middle_initial" => $dataMiddleInitial, "last_name" => $dataLastName, "email" => $dataEmail, "phone_number" => $dataPhoneNumber, "alt_phone" => $dataAltPhone, "address1" => $dataAddress1, "address2" => $dataAddress2, "address3" => $dataAddress3, "city" => $dataCity, "state" => $dataState, "province" => $dataProvince, "postal_code" => $dataPostalCode, "gender" => $dataGender);
			}
		} else {
			$apiresults = array("result" => "Error: Contact doesn't exist.");
		}
	}
?>
