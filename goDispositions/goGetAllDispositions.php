<?php
 /**
 * @file 		getAllDispositions.php
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

    
	$selectSQL = "";
	$campSQL = "";
	$select = $astDB->escape($_REQUEST['select']);
	$camp = $astDB->escape($_REQUEST['campaign_id']);
	$customRequest = $astDB->escape($_REQUEST['custom_request']);
	$sortBy = $astDB->escape($_REQUEST['sortBy']);
	$defCustom = "custom";
	
	if(!empty($sortBy)){
		$sortBy = $sortBy;
	}else{
		$sortBy = "status";
	}
	
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
			$camps = go_getall_allowed_campaigns($session_user, $astDB);
			
			if ($select=="N"){
				$campSQL = "WHERE campaign_id IN ($camps)";
			}else{
				$campSQL = "";
			}
		}
		
		$groupId = go_get_groupid($session_user, $astDB);
		
		if (!checkIfTenant($groupId, $goDB)) {
			$ul = "";
		} else {
			$ul = "AND user_group='$groupId'";
			$addedSQL = "WHERE user_group='$groupId'";
		}
		
		if($camp != NULL || $customRequest != NULL){
			$query = "SELECT status,status_name,campaign_id FROM vicidial_campaign_statuses $campSQL ORDER BY campaign_id";
		}else{
			$query = "SELECT status, status_name FROM vicidial_campaign_statuses UNION  SELECT status, status_name FROM vicidial_statuses ORDER BY $sortBy;";
		}
		
		$rsltv = $astDB->rawQuery($query);
		foreach ($rsltv as $fresult){
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

