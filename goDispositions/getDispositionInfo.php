<?php
    //////////////////////////////////////////////////////////
    /// Name: getLeadFilterInfo.php 		///
    /// Description: API to get specific lead filter 		///
    /// Version: 0.9 		///
    /// Copyright: GOAutoDial Inc. (c) 2011-2016 		///
    /// Written by: Jeremiah Sebastian V. Samatra 		///
    /// License: AGPLv2 		///
    //////////////////////////////////////////////////////////
    include_once ("../goFunctions.php");
    $camp = $_REQUEST["campaign_id"]; 
    $camp = mysqli_real_escape_string($link, $camp);
	$status = mysqli_real_escape_string($link, $_REQUEST["status"]);
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
	
	if($camp == null) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for Campaign ID.");
	} else {
		$campSQL = "";
		if ($select=="Y")
			$selectSQL = "WHERE selectable='Y'";
		if (!is_null($camp))
			$campSQL = "AND campaign_id='$camp'";
		if (is_null($camp)) {
			$camps = go_getall_allowed_campaigns($goUser);
			if ($select=="N")
				$campSQL = "AND campaign_id IN ($camps)";
			else
				$campSQL = "AND campaign_id IN ($camps)";
		}

		$groupId = go_get_groupid($goUser);

		if (!checkIfTenant($groupId)) {
			$ul = "";
		} else {
			$ul = "AND user_group='$groupId'";
		   $addedSQL = "WHERE user_group='$groupId'";
		}
		
		if($status != NULL){
			$query = "SELECT status,status_name,campaign_id, selectable, human_answered, sale, dnc, customer_contact, not_interested, unworkable, scheduled_callback FROM vicidial_campaign_statuses WHERE campaign_id='$camp' AND status='$status';";
		}else{
			$query = "SELECT status,status_name,campaign_id, selectable, human_answered, sale, dnc, customer_contact, not_interested, unworkable, scheduled_callback FROM vicidial_campaign_statuses WHERE campaign_id='$camp';";
		}
		$rsltv = mysqli_query($link, $query);
		$exist = mysqli_num_rows($rsltv);
		
		if($exist >= 1){
			while($fresult = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataStat[] = $fresult['status'];
				$dataStatName[] = $fresult['status_name'];
				$dataCampID[] = $fresult['campaign_id'];
				$dataSelectable[] = $fresult['selectable'];
				$dataHuman_answered[] = $fresult['human_answered'];
				$dataSale[] = $fresult['sale'];
				$dataDnc[] = $fresult['dnc'];
				$dataCustomer_contact[] = $fresult['customer_contact'];
				$dataNot_interested[] = $fresult['not_interested'];
				$dataUnworkable[] = $fresult['unworkable'];
				$dataScheduled_callback[] = $fresult['scheduled_callback'];
				
				//$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "status_name" => $dataStatName, "status" => $dataStat, "selectable" => $dataSelectable, "human_answered" => $dataHuman_answered, "sale" => $dataSale, "dnc" => $dataDnc, "customer_contact" => $dataCustomer_contact, "not_interested" => $dataNot_interested, "unworkable" => $dataUnworkable, "scheduled_callback" => $dataScheduled_callback);
			}
			
			$apiresults = array("result" => "success", "campaign_id" => $camp, "status_name" => $dataStatName, "status" => $dataStat, "selectable" => $dataSelectable, "human_answered" => $dataHuman_answered, "sale" => $dataSale, "dnc" => $dataDnc, "customer_contact" => $dataCustomer_contact, "not_interested" => $dataNot_interested, "unworkable" => $dataUnworkable, "scheduled_callback" => $dataScheduled_callback);
			
			$log_id = log_action($linkgo, 'VIEW', $log_user, $ip_address, "Viewed the dispositions of campaign $camp", $log_group);
			
		} else {
			$err_msg = error_handle("41004", "campaign_id. Campaign Disposition does not exist!");
			$apiresults = array("code" => "41004", "result" => $err_msg);
			//$apiresults = array("result" => "Error: Campaign disposition does not exist.");
		}
	}
?>
