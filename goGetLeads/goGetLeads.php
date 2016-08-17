<?php
    #######################################################
    #### Name: goGetLeads.php     	               	   ####
    #### Description: API to get Leads                 ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Warren Ipac Briones			   ####
	#### Modified by: Alexander Jim Abenoja			   ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");

    function remove_empty($array) {
    	return array_filter($array, '_remove_empty_internal');
    }

    function _remove_empty_internal($value) {
  	return !empty($value) || $value === 0;
    }
                /*$groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }*/
    $goVarLimit = $_REQUEST["goVarLimit"];
	$userid = $_REQUEST["user_id"];
	$search = mysqli_real_escape_string($link, $_REQUEST['search']);
	$disposition_filter = mysqli_real_escape_string($link, $_REQUEST['disposition_filter']);
	$list_filter = mysqli_real_escape_string($link, $_REQUEST['list_filter']);
	$address_filter = mysqli_real_escape_string($link, $_REQUEST['address_filter']);
	$city_filter = mysqli_real_escape_string($link, $_REQUEST['city_filter']);
	$state_filter = mysqli_real_escape_string($link, $_REQUEST['state_filter']);
	
	$goSearch = "";
	
	if($goVarLimit > 0) {
		$goMyLimit = "LIMIT $goVarLimit";
	} else {
		$goMyLimit ="";
	}
	
	if(!empty($search)) {
		$goSearch = "AND (phone_number LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR lead_id LIKE '$search')";
	}
	if(!empty($disposition_filter)){
		$filterDispo = "AND status = '$disposition_filter'";
	}
	if(!empty($list_filter)){
		$filterList = "AND list_id = '$list_filter'";
	}
	if(!empty($address_filter)){
		$filterAddress = "AND (address1 LIKE '%$address_filter%' OR address2 LIKE '%$address_filter%')";
	}
	if(!empty($city_filter)){
		$filterCity = "AND city LIKE '%$city_filter%'";
	}
	if(!empty($state_filter)){
		$filterState = "AND state LIKE '%$state_filter%'";
	}
//echo $userid;
	//$apiresults = array("result" => "success", "userid"=>$userid);
   		
 //  	$query = "SELECT list_id,first_name,middle_initial,last_name,email,phone_number,alt_phone,address1,address2,address3,city,state,province,postal_code,country_code,date_of_birth,entry_date,user,gender,comments FROM vicidial_list $goMyLimit";
   //	$rsltv = mysqli_query($link, $query);

	$getAllowedCampaigns_query = "SELECT vicidial_users.user_group, vicidial_user_groups.allowed_campaigns FROM vicidial_users, vicidial_user_groups WHERE vicidial_users.user_group = vicidial_user_groups.user_group AND vicidial_users.user ='$userid'";  	
	$allowedCampaigns_result = mysqli_query($link, $getAllowedCampaigns_query);
	$allowedCampaignsFetch = mysqli_fetch_array($allowedCampaigns_result, MYSQLI_ASSOC);
	$allowedCampaigns = $allowedCampaignsFetch['allowed_campaigns'];
	
	//if admin
	if($allowedCampaigns == " -ALL-CAMPAIGNS- -"){
		$queryx = "SELECT lead_id,list_id,first_name,middle_initial,last_name,phone_number, status FROM vicidial_list WHERE phone_number != '' $goSearch $filterDispo $filterList $filterAddress $filterCity $filterState $goMyLimit";

        	$returnRes = mysqli_query($link, $queryx);

	} else { //if multiple allowed campaigns
		$multiple_campaigns = explode("-", $allowedCampaigns);
		$allowedCampaignsx = $multiple_campaigns[0];
		$allowedCampaignsx = explode(" ",$allowedCampaignsx);	
		$allowedCampaignsx = remove_empty($allowedCampaignsx);
		$allowedCampaignsx = implode("','", $allowedCampaignsx);
		$allowedCampaignsx = "'".$allowedCampaignsx."'";
		
		//get lists id from return campaigns
		$getListsID_query = "select list_id from vicidial_lists where campaign_id IN($allowedCampaignsx)";
		$listsID_result = mysqli_query($link, $getListsID_query);
		//$listsIDFetch = mysqli_fetch_array($listsID_result, MYSQLI_ASSOC);
		
		while($listsID_resultx = mysqli_fetch_array($listsID_result, MYSQLI_ASSOC)){
			$list_results .= $listsID_resultx['list_id']." ";
		}
		$fetchLists = explode(" ", $list_results);
		$fetchLists = remove_empty($fetchLists);
		$fetchLists = implode("','", $fetchLists);
		$fetchLists = "'".$fetchLists."'";
	
		//get all leads from return list_id
//		$queryx = "SELECT count(*) as xxx FROM vicidial_list WHERE list_id IN($fetchLists);";
		$queryx = "SELECT lead_id,list_id,first_name,middle_initial,last_name,phone_number, status FROM vicidial_list WHERE phone_number != '' AND list_id IN($fetchLists) $goSearch $filterDispo $filterList $filterAddress $filterCity $filterState $goMyLimit;";
		$returnRes = mysqli_query($link, $queryx); 
		
	}

	while($fresults = mysqli_fetch_array($returnRes, MYSQLI_ASSOC)){
		$dataLeadid[] = $fresults['lead_id'];
		$dataListid[] = $fresults['list_id'];
		$dataFirstName[] = $fresults['first_name'];
       	$dataMiddleInitial[] = $fresults['middle_initial'];
        $dataLastName[] = $fresults['last_name'];
		$dataPhoneNumber[] = $fresults['phone_number'];
		$dataDispo[] = $fresults['status'];
		
   	$apiresults = array("result" => "success", "lead_id" => $dataLeadid, "list_id" => $dataListid, "first_name" => $dataFirstName, "middle_initial" => $dataMiddleInitial, "last_name" => $dataLastName, "phone_number" => $dataPhoneNumber, "status" => $dataDispo); 
	}

?>
