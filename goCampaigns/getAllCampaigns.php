<?php
    #######################################################
    #### Name: getAllCampaigns.php	               ####
    #### Description: API to get all campaigns         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
	
	$groupId = go_get_groupid($session_user);
	$user_group = $_REQUEST['user_group'];
    $allowed_campaigns = go_getall_allowed_campaigns($user_group);
	$allowed_camps = "";
	
	if (checkIfTenant($user_group)) {
        $ul='';
    } else {
		if($user_group !== "ADMIN")
		$ul = "WHERE user_group='$user_group'";
		else
		$ul = "";
	}
	
	if (isset($user_group) && strlen($user_group) > 0) {
		if (!preg_match("/ALLCAMPAIGNS/", $allowed_campaigns)) {
			$allowed_camps = "WHERE campaign_id IN ";
			$allowed_camps .= "(".$allowed_campaigns.")";
		}
	}
	
   	$query = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $allowed_camps ORDER BY campaign_id";
   	$rsltv = mysqli_query($link, $query);
	
	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataCampID[] = $fresults['campaign_id'];
		$dataCampName[] = $fresults['campaign_name'];// .$fresults['dial_method'].$fresults['active'];
		$dataDialMethod[] = $fresults['dial_method'];
		$dataActive[] = $fresults['active'];
		$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "campaign_name" => $dataCampName, "dial_method" => $dataDialMethod, "active" => $dataActive);
	}
	
?>
