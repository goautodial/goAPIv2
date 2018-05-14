<?php
/**
 * @file        goDeleteList.php
 * @brief       API to delete specific List
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
	
    // POST or GET Variables
    $list_id = $astDB->escape($_REQUEST['list_id']);
	$action = strtolower($astDB->escape($_REQUEST['action']));
    $ip_address = $astDB->escape($_REQUEST['hostname']);
    $goUser = $astDB->escape($_REQUEST['goUser']);
		
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
    
	if($list_id == null) {
		$err_msg = error_handle("10107");
		$apiresults = array("code" => "10107", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for List ID."); 
	} elseif(empty($session_user)) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	}else{
		if($action == strtolower("delete_selected")){
			$exploded = explode(",",$list_id);
			$error_count = 0;
			foreach ($exploded as $ex_list_id){
				//$query = "SELECT list_id,list_name FROM vicidial_lists WHERE list_id='".$ex_list_id."' order by list_id LIMIT 1";
				$astDB->where('list_id', $ex_list_id);
				$astDB->orderBy('list_id', 'desc');
				$rsltv = $astDB->getOne('vicidial_lists', 'list_id,list_name');
				$countResult = $astDB->getRowCount();
				
				if($countResult > 0) {
					$dataListID = $rsltv['list_id'];
					if($dataListID != null) {
						//$deleteQuery = "DELETE FROM vicidial_lists WHERE list_id='$dataListID';";
						$astDB->where('list_id', $dataListID);
						$deleteResult = $astDB->delete('vicidial_lists');
						//$deleteQueryLeads = "DELETE FROM vicidial_list WHERE list_id='$dataListID';";
						$astDB->where('list_id', $dataListID);
						$deleteResultLeads = $astDB->delete('vicidial_list');
						//$deleteQueryStmt = "DELETE FROM vicidial_lists_fields WHERE list_id='$dataListID' LIMIT 1;";
						$astDB->where('list_id', $dataListID);
						$deleteResultStmt = $astDB->delete('vicidial_lists_fields', 1);
						//echo $deleteQuery.$deleteQueryLeads.$deleteQueryStmt;
						
					// Admin Logs
						$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted List ID: $dataListID", $log_group, $deleteQuery);
						
						$apiresults = array("result" => "success");
					} else {
						$error_count = $error_count + 1;
					}
		
				} else {
					$error_count = $error_count + 1;
				}
			}
			if($error_count > 0) {
				$err_msg = error_handle("10010");
				$apiresults = array("code" => "10010", "result" => $err_msg);
				//$apiresults = array("result" => "( $error_count ) Errors Found: Delete Failed");
			} else {
				$apiresults = array("result" => "success"); 
			}
		}else{
			$groupId = go_get_groupid($session_user, $astDB);
			if (!checkIfTenant($groupId, $goDB)) {
				//$ul = "WHERE list_id='$list_id'";
				$astDB->where('list_id', $list_id);
			} else { 
				//$ul = "WHERE list_id='$list_id' AND user_group='$groupId'";
				$astDB->where('list_id', $list_id);
				$astDB->where('user_group', $groupId);
			}
			
			//$query = "SELECT list_id,list_name FROM vicidial_lists $ul order by list_id LIMIT 1";
			$astDB->orderBy('list_id', 'desc');
			$rsltv = $astDB->getOne('vicidial_lists', 'list_id,list_name');
			$countResult = $astDB->getRowCount();
			
			if($countResult > 0) {
				$dataListID = $rsltv['list_id'];
				if($dataListID != null) {
					//$deleteQuery = "DELETE FROM vicidial_lists WHERE list_id='$dataListID';";
					$astDB->where('list_id', $dataListID);
					$deleteResult = $astDB->delete('vicidial_lists');
					//$deleteQueryLeads = "DELETE FROM vicidial_list WHERE list_id='$dataListID';";
					$astDB->where('list_id', $dataListID);
					$deleteResultLeads = $astDB->delete('vicidial_list');
					//$deleteQueryStmt = "DELETE FROM vicidial_lists_fields WHERE list_id='$dataListID' LIMIT 1;";
					$astDB->where('list_id', $dataListID);
					$deleteResultStmt = $astDB->delete('vicidial_lists_fields', 1);
					//echo $deleteQuery.$deleteQueryLeads.$deleteQueryStmt;
					
				// Admin Logs
					$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted List ID: $dataListID", $log_group, $deleteQuery);
					
					$apiresults = array("result" => "success");
				} else {
					$apiresults = array("result" => "Error: List doesn't exist.");
				}
	
			} else {
				$apiresults = array("result" => "Error: List doesn't exist.");
			}
		}
	}//end
?>
