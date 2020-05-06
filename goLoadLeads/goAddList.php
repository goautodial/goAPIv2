<?php
 /**
 * @file 		goAddList.php
 * @brief 		API for Adding Lists
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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
	include_once ("goAPI.php");

	### POST or GET Variables
	$list_id = $astDB->escape($_REQUEST['list_id']);
	$list_name = $astDB->escape($_REQUEST['list_name']);
	$campaign_id = $astDB->escape($_REQUEST['campaign_id']);
	$active = $astDB->escape($_REQUEST['active']);
	$list_description = $astDB->escape($_REQUEST['list_description']);

    ### Default values 
    $defActive = array("Y","N");
    
    ### Check campaign_id if its null or empty
	if($list_id == null || $list_id == "") { 
		$apiresults = array("result" => "Error: List ID field is required."); 
	} else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $list_name) || $list_name == null){
            $apiresults = array("result" => "Error: Special characters found in list_name and must not be empty");
        } else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $list_description) || $list_description == null){
                $apiresults = array("result" => "Error: Special characters found in list_description and must not be empty");
			} else {
				### Check value compare to default values
				if(!in_array($active,$defActive) && $active != null) { 
					$apiresults = array("result" => "Error: Default value for active is Y or N only."); 
				} else {
					if(!is_numeric($list_id)){
						$apiresults = array("result" => "Error: List ID must be a number or combination of number");
					} else {
						$groupId = go_get_groupid($goUser, $astDB);
					
						if (!checkIfTenant($groupId, $goDB)) {
							//$ul = "WHERE list_id='$list_id'";
							//$ulcamp = "WHERE campaign_id='$campaign_id'";
							$astDB->where('campaign_id', $campaign_id);
						} else {
							//$ul = "WHERE list_id='$list_id' AND user_group='$groupId'";
							//$ulcamp = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
							$astDB->where('campaign_id', $campaign_id);
							$astDB->where('user_group', $groupId);
						}
					
						//$queryCamp = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ulcamp ORDER BY campaign_id LIMIT 1;";
						$astDB->orderBy('campaign_id', 'desc');
						$rsltvCamp = $astDB->getOne('vicidial_campaigns', 'campaign_id,campaign_name,dial_method,active');
						$countResultCamp = $astDB->getRowCount();
				
						if($countResultCamp > 0) {
							if (!checkIfTenant($groupId, $goDB)) {
								$astDB->where('list_id', $list_id);
							} else {
								$astDB->where('list_id', $list_id);
								$astDB->where('user_group', $groupId);
							}
							//$query = "SELECT list_id from vicidial_lists $ul order by list_id LIMIT 1";
							$astDB->orderBy('list_id', 'desc');
							$rsltv = $astDB->getOne('vicidial_lists', 'list_id');
							$countResult = $astDB->getRowCount();
						
							if($countResult > 0) {
								$apiresults = array("result" => "Error: there is already a LIST ID in the system with this ID.");
							} else {
								$SQLdate = date("Y-m-d H:i:s");
								$addQuery = "INSERT INTO vicidial_lists (list_id,list_name,campaign_id,active,list_description,list_changedate) values('$list_id','".mysqli_real_escape_string($list_name)."','$campaign_id','$active','$list_description','$SQLdate');";
								$addResult = $astDB->rawQuery($addQuery);
								
								$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Added New List: $list_id", $log_group, $addQuery);
						
								if(!$addResult) {
									$apiresults = array("result" => "Error: Failed to add");
								} else {
									$apiresults = array("result" => "success");
								}
							}
						} else {
							$apiresults = array("result" => "Error: Invalid Campaign ID");
						}
					}
				}
			}
		}
	}
?>