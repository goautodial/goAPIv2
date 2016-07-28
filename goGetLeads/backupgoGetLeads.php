<?php
    #######################################################
    #### Name: goGetLeads.php     	               ####
    #### Description: API to get Leads                 ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Warren Ipac Briones               ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
                /*$groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }*/
        //$goVarLimit = $_REQUEST["goVarLimit"];
	//$userid = $_REQUEST["user_id"];
	
//	if($goVarLimit > 0) {
//		$goMyLimit = "LIMIT $goVarLimit";
//	} else {
//		$goMyLimit ="";
//	}
	
//	if($userid == NULL){
//		$userid = "DOVAkhiin";
//	}

	$apiresults = array("result" => "success");
   		
//$apiresults = array("result" => "success", "list_id" => $dataListid, "first_name" => $dataFirstName, "middle_initial" => $dataMiddleInitial, "last_name" => $dataLastName, "email" => $dataEmail, "phone_number" => $dataPhoneNumber, "alt_phone" => $dataAltPhone, "address1" => $dataAddress1, "address2" => $dataAddress2, "address3" => $dataAddress3, "city" => $dataCity, "state" => $dataState, "province" => $dataProvince, "postal_code" => $dataPostalCode, "country_code" => $dataCountryCode, "date_of_birth" => $dataDateofbirth, "user" => $dataUser, "gender" => $dataGender, "comments" => $dataComments);
   	//$query = "SELECT list_id,first_name,middle_initial,last_name,email,phone_number,alt_phone,address1,address2,address3,city,state,province,postal_code,country_code,date_of_birth,entry_date,user,gender,comments FROM vicidial_list $goMyLimit";
   	//$rsltv = mysqli_query($link, $query);

	$getAllowedCampaigns_query = "select vicidial_users.user_group, vicidial_user_groups.allowed_campaigns 
				FROM vicidial_users, vicidial_user_groups 
				WHERE 
				vicidial_users.user_group = vicidial_user_groups.user_group AND vicidial_users.user ='$userid'";  	
	$allowedCampaigns_result = mysqli_query($link, $getAllowedCampaigns_query);
	$allowedCampaigns = $allowedCampaigns_result['allowed_campaigns'];

	//if admin
	if($allowedCampaigns == "-ALL-CAMPAIGNS- -"){
		$query = "SELECT list_id,first_name,middle_initial,last_name,email,phone_number,alt_phone,address1,address2,address3,city,state,province,postal_code,country_code,date_of_birth,entry_date,user,gender,comments FROM vicidial_list $goMyLimit";
        	$rsltv = mysqli_query($link, $query);
	
	}else{ //if multiple allowed campaigns
		$multiple_campaigns = explode(" ", $allowedCampaigns, "-");
		$allowedCampaigns = implode("','", $multiple_campaigns);
		$allowedCampaigns = "'".$allowedCampaigns."'";
		
		//get lists id from return campaigns
		$getListsID_query = "select list_id, campaign_id from vicidial_lists where campaign_id IN($allowedCampaigns)";
		$listsID_result = mysqli_query($link, $getListsID_query);
		
		$list_results[] = "";
		while($listsID_result = mysqli_query($link, $getListsID_query)){
			$list_results[] = " ".$listsID_result['list_id']." .";
		}

		//get all leads from return list_id
		$getAllLeads_query = "select * from vicidial_list where list_id IN('101','1000')";
		
		
	}

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$datauserid[] = $_REQUEST['user_id'];
		$dataListid[] = $fresults['list_id'];
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
		$dataCountryCode[] = $fresults['country_code'];
		$dataDateofbirth[] = $fresults['date_of_birth'];
		$dataEntryDate[] = $fresults['entry_date'];
		$dataUser[] = $fresults['user'];
		$dataGender[] = $fresults['gender'];
		$dataComments[] = $fresults['comments'];
   	//	$apiresults = array("result" => "success", "list_id" => $dataListid, "first_name" => $dataFirstName, "middle_initial" => $dataMiddleInitial, "last_name" => $dataLastName, "email" => $dataEmail, "phone_number" => $dataPhoneNumber, "alt_phone" => $dataAltPhone, "address1" => $dataAddress1, "address2" => $dataAddress2, "address3" => $dataAddress3, "city" => $dataCity, "state" => $dataState, "province" => $dataProvince, "postal_code" => $dataPostalCode, "country_code" => $dataCountryCode, "date_of_birth" => $dataDateofbirth, "user" => $dataUser, "gender" => $dataGender, "comments" => $dataComments);
	}

?>
