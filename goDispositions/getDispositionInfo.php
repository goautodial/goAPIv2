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
	
	$sortBy = mysqli_real_escape_string($link, $_REQUEST["sortBy"]);
	if(!empty($sortBy)){
		$sortBy = $sortBy;
	}else{
		$sortBy = "status";
	}
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
		
		$chkStatus = "SHOW TABLES LIKE 'go_statuses'";
		$statusRslt = mysqli_query($linkgo, $chkStatus);
		$statusExist = mysqli_num_rows($statusRslt);
		$statusTBL = '';
		$statusSQL = '';
		if ($statusExist > 0) {
			$chkCamp = "SELECT * FROM go_statuses WHERE campaign_id='$camp';";
			$campRslt = mysqli_query($linkgo, $chkCamp);
			$campExist = mysqli_num_rows($campRslt);
			
			if ($campExist) {
				$statusTBL = "LEFT JOIN `$VARDBgo_database`.go_statuses gs ON vcs.status=gs.status AND vcs.campaign_id=gs.campaign_id";
				$statusSQL = "GROUP BY status,campaign_id ORDER BY priority,vcs.status";
			}
		}
		
		if($status != NULL){
			$query = "SELECT status,status_name,campaign_id, selectable, human_answered, sale, dnc, customer_contact, not_interested, unworkable, scheduled_callback FROM vicidial_campaign_statuses WHERE campaign_id='$camp' AND status='$status' ORDER BY $sortBy;";
		}else{
			$query = "SELECT vcs.status,status_name,vcs.campaign_id, selectable, human_answered, sale, dnc, customer_contact, not_interested, unworkable, scheduled_callback FROM vicidial_campaign_statuses vcs $statusTBL WHERE vcs.campaign_id='$camp' $statusSQL;";
		}
		$rsltv = mysqli_query($link, $query);
		$exist = mysqli_num_rows($rsltv);
		
		if($exist >= 1){
			while($fresult = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				if ($statusExist > 0) {
					$statusQuery = "SELECT priority,color FROM go_statuses WHERE status='".$fresult['status']."' AND campaign_id='".$fresult['campaign_id']."'";
					$statusRslt = mysqli_query($linkgo, $statusQuery);
					$statusData = mysqli_fetch_array($statusRslt, MYSQLI_ASSOC);
				}
				
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
				$dataPriority[] = (!is_null($statusData['priority'])) ? $statusData['priority'] : "1";
				$dataColor[] = (!is_null($statusData['color'])) ? $statusData['color'] : "#b5b5b5";
				
				//$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "status_name" => $dataStatName, "status" => $dataStat, "selectable" => $dataSelectable, "human_answered" => $dataHuman_answered, "sale" => $dataSale, "dnc" => $dataDnc, "customer_contact" => $dataCustomer_contact, "not_interested" => $dataNot_interested, "unworkable" => $dataUnworkable, "scheduled_callback" => $dataScheduled_callback);
			}
			
			$apiresults = array("result" => "success", "campaign_id" => $camp, "status_name" => $dataStatName, "status" => $dataStat, "selectable" => $dataSelectable, "human_answered" => $dataHuman_answered, "sale" => $dataSale, "dnc" => $dataDnc, "customer_contact" => $dataCustomer_contact, "not_interested" => $dataNot_interested, "unworkable" => $dataUnworkable, "scheduled_callback" => $dataScheduled_callback, "priority" => $dataPriority, "color" => $dataColor);
			
			$log_id = log_action($linkgo, 'VIEW', $log_user, $ip_address, "Viewed the dispositions of campaign $camp", $log_group);
			
		} else {
			$err_msg = error_handle("41004", "campaign_id. Campaign Disposition does not exist!");
			$apiresults = array("code" => "41004", "result" => $err_msg);
			//$apiresults = array("result" => "Error: Campaign disposition does not exist.");
		}
	}
?>
