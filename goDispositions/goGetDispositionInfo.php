<?php
 /**
 * @file 		getDispositionInfo.php
 * @brief 		API for Dispositions
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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

    
    $camp = $astDB->escape($_REQUEST["campaign_id"]);
    $camp = $astDB->escape($camp);
	$status = $astDB->escape($_REQUEST["status"]);
	
	$sortBy = $astDB->escape($_REQUEST["sortBy"]);
	if(!empty($sortBy)){
		$sortBy = $sortBy;
	}else{
		$sortBy = "status";
	}
	
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
			$camps = go_getall_allowed_campaigns($goUser, $astDB);
			if ($select=="N")
				$campSQL = "AND campaign_id IN ($camps)";
			else
				$campSQL = "AND campaign_id IN ($camps)";
		}

		$groupId = go_get_groupid($goUser, $astDB);

		if (!checkIfTenant($groupId, $goDB)) {
			$ul = "";
		} else {
			$ul = "AND user_group='$groupId'";
		   $addedSQL = "WHERE user_group='$groupId'";
		}
		
		$chkStatus = "SHOW TABLES LIKE 'go_statuses'";
		$statusRslt = $goDB->rawQuery($chkStatus);
		$statusExist = $goDB->getRowCount();
		$statusTBL = '';
		$statusSQL = '';
		if ($statusExist > 0) {
			//$chkCamp = "SELECT * FROM go_statuses WHERE campaign_id='$camp';";
			$goDB->where('campaign_id', $camp);
			$campRslt = $goDB->get('go_statuses');
			$campExist = $goDB->getRowCount();
			
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
		$rsltv = $astDB->rawQuery($query);
		$exist = $astDB->getRowCount();
		
		if($exist >= 1){
			foreach ($rsltv as $fresult){
				if ($statusExist > 0) {
					//$statusQuery = "SELECT priority,color FROM go_statuses WHERE status='".$fresult['status']."' AND campaign_id='".$fresult['campaign_id']."'";
					$goDB->where('status', $fresult['status']);
					$goDB->where('campaign_id', $fresult['campaign_id']);
					$statusData = $goDB->getOne('go_statuses', 'priority,color');
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
			
			$log_id = log_action($goDB, 'VIEW', $log_user, $log_ip, "Viewed the dispositions of campaign $camp", $log_group);
			
		} else {
			$err_msg = error_handle("41004", "campaign_id. Campaign Disposition does not exist!");
			$apiresults = array("code" => "41004", "result" => $err_msg);
			//$apiresults = array("result" => "Error: Campaign disposition does not exist.");
		}
	}
?>
