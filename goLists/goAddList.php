<?php
/**
 * @file        goAddList.php
 * @brief       API to add new list
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
	$list_name = $astDB->escape($_REQUEST['list_name']);
	$campaign_id = $astDB->escape($_REQUEST['campaign_id']);
	$active = $astDB->escape($_REQUEST['active']);
	$list_description = $astDB->escape($_REQUEST['list_description']);
	$ip_address = $astDB->escape($_REQUEST['hostname']);
		
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);


    // Default values 
    $defActive = array("Y","N");
    
    // Check campaign_id if its null or empty
	if(empty($list_id)) {
		$err_msg = error_handle("10107");
		$apiresults = array("code" => "10107", "result" => $err_msg); 
		//$apiresults = array("result" => "Error: List ID field is required."); 
	} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $list_name) || $list_name == null){
		$err_msg = error_handle("41006", "list_name");
		$apiresults = array("code" => "41006", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Special characters found in list_name and must not be empty");
    } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $list_description)){
		$err_msg = error_handle("41006", "list_description");
		$apiresults = array("code" => "41006", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Special characters found in list_description");
    } elseif(!in_array($active,$defActive) && $active != null) {
		$err_msg = error_handle("41006", "active");
		$apiresults = array("code" => "41006", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Default value for active is Y or N only."); 
	} elseif(!is_numeric($list_id)){
		$err_msg = error_handle("41006", "list_id");
		$apiresults = array("code" => "41006", "result" => $err_msg);
		//$apiresults = array("result" => "Error: List ID must be a number or combination of number");
	} else {
		$groupId = go_get_groupid($goUser, $astDB);
		if (!checkIfTenant($groupId, $goDB)) {
			$ul = "WHERE list_id='$list_id'";
			$ulcamp = "WHERE campaign_id='$campaign_id'";
		} else {
			$ul = "WHERE list_id='$list_id' AND user_group='$groupId'";
			$ulcamp = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
		}
		
		$queryCamp = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ulcamp ORDER BY campaign_id LIMIT 1;";
		$rsltvCamp = $astDB->rawQuery($queryCamp);
		$countResultCamp = $astDB->getRowCount();
		
	    if($countResultCamp > 0) {
			$query = "SELECT list_id from vicidial_lists $ul order by list_id LIMIT 1";
			$rsltv = $astDB->rawQuery($query);
	        $countResult = $astDB->getRowCount();
	        if($countResult > 0) {
			    $apiresults = array("result" => "Error: there is already a LIST ID in the system with this ID.");
			} else {
				$SQLdate = date("Y-m-d H:i:s");
				//$addQuery = "INSERT INTO vicidial_lists (list_id,list_name,campaign_id,active,list_description,list_changedate) values('$list_id','$list_name','$campaign_id','$active','$list_description','$SQLdate');";
				$insertData = array(
					'list_id' => $list_id,
					'list_name' => $list_name,
					'campaign_id' => $campaign_id,
					'active' => $active,
					'list_description' => $list_description,
					'list_changedate' => $SQLdate
				);
				$addResult = $astDB->insert('vicidial_lists', $insertData);
				
				$log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added New List: $list_id", $log_group, $addQuery);
				
				if($addResult == false){
					$err_msg = error_handle("10010");
					$apiresults = array("code" => "10010", "result" => $err_msg);
					//$apiresults = array("result" => "Error: Failed to add");
				} else {
					$apiresults = array("result" => "success");
				}
			}
		} else {
			$err_msg = error_handle("41004", "campaign_id");
			$apiresults = array("code" => "41004", "result" => $err_msg);
			//$apiresults = array("result" => "Error: Invalid Campaign ID");
		}
	}

?>
