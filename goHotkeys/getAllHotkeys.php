<?php
####################################################
#### Name: getAllHotkeys.php                    ####
#### Description: API to add new list           ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
#### Written by: Noel Umandap                   ####
#### License: AGPLv2                            ####
####################################################
include_once("goFunctions.php");

$camp = $_REQUEST['hotkeyCampID'];
$groupId = go_get_groupid($goUser);

if(empty($camp)) {
	$apiresults = array("result" => "Error: Set a value for Campaign ID.");
} else {
	if (!checkIfTenant($groupId)) {
	$ul = "";
	} else {
	$ul = "AND user_group='$groupId'";
	$addedSQL = "WHERE user_group='$groupId'";
	}
		
	$query = "SELECT
		status,
		hotkey,
		status_name,
		selectable,
		campaign_id
		FROM vicidial_campaign_hotkeys $ul
		WHERE campaign_id ='$camp'
		ORDER BY hotkey;";
	$rsltv = mysqli_query($link,$query);

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataStatus[] = $fresults['status'];
		$dataHotkey[] = $fresults['hotkey'];
		$dataStatusName[] = $fresults['status_name'];
		$dataSelectable[] = $fresults['selectable'];
		$dataCampaignID[] = $fresults['campaign_id'];
		$apiresults = array(
			"result" => "success",
			"status" => $dataStatus,
			"hotkey" => $dataHotkey,
			"status_name" => $dataStatusName,
			"selectable" => $dataSelectable,
			"campaign_id" => $dataCampaignID
		);
	}
}
?>