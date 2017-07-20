<?php
    /////////////////////////////////////////////////////////
    /// Name: getAllDispositions.php 	///
    /// Description: API to get all custom Dispositions 	///
    /// Version: 0.9 	///
    /// Copyright: GOAutoDial Inc. (c) 2011-2016 	///
    /// Written by: Jeremiah Sebastian V. Samatra 	///
    /// License: AGPLv2 	///
    /////////////////////////////////////////////////////////
    include_once ("../goFunctions.php");

	$selectSQL = "";
	$campSQL = "";
	$select = $_REQUEST['select'];
	$camp = $_REQUEST['campaign_id'];
	$customRequest = mysqli_real_escape_string($link, $_REQUEST['custom_request']);
	$defCustom = "custom";
	
	if(empty($session_user)){
		$err_msg = error_handle("40001");
        $apiresults = array("code" => "40001","result" => $err_msg);
	}elseif (!empty($customRequest) && $customRequest !== $defCustom){
		$err_msg = error_handle("41006", "custom_request");
        $apiresults = array("code" => "41006","result" => $err_msg);
	}else{
		//if ($select=="Y")
		//	$selectSQL = "WHERE selectable='Y'";
		//if (!is_null($camp))
		//	$campSQL = "AND campaign_id='$camp'";
		//if (is_null($camp)){
		if(!empty($customRequest)){
			$camps = go_getall_allowed_campaigns($session_user);
			
			if ($select=="N"){
				$campSQL = "WHERE campaign_id IN ($camps)";
			}else{
				$campSQL = "";
			}
		}
		
		$groupId = go_get_groupid($session_user);
		
		if (!checkIfTenant($groupId)) {
			$ul = "";
		} else {
			$ul = "AND user_group='$groupId'";
			$addedSQL = "WHERE user_group='$groupId'";
		}
		
		if($camp != NULL || $customRequest != NULL){
			$query = "SELECT status,status_name,campaign_id FROM vicidial_campaign_statuses $campSQL ORDER BY campaign_id";
		}else{
			$query = "SELECT status, status_name FROM vicidial_campaign_statuses UNION  SELECT status, status_name FROM vicidial_statuses ORDER BY status;";
		}
		
		$rsltv = mysqli_query($link, $query);
		while($fresult = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataStat[] = $fresult['status'];			
			$dataStatName[] = $fresult['status_name'];
			
			if($camp != NULL || $customRequest != NULL)
				$dataCampID[] = $fresult['campaign_id'];
		}
		
		if($camp != NULL || $customRequest != NULL){
			$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "status_name" => $dataStatName, "status" => $dataStat);
			//$apiresults = array("result" => "success", "query" => $query, "campaign_id" => $dataCampID, "status_name" => $dataStatName, "status" => $dataStat);
		}else{
			$apiresults = array("result" => "success", "status" => $dataStat, "status_name" => $dataStatName);
			//$apiresults = array("result" => "success", "query" => $query, "status" => $dataStat, "status_name" => $dataStatName);
		}
	}
	
	
?>

