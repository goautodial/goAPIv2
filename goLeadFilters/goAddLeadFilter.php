<?php
 /**
 * @file 		goAddLeadFilter.php
 * @brief 		API for Adding Lead Filters
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
	$lead_filter_id = $astDB->escape($_REQUEST['lead_filter_id']);
	$lead_filter_name = $astDB->escape($_REQUEST['lead_filter_name']);
	$lead_filter_comments = $astDB->escape($_REQUEST['lead_filter_comments']);
	$lead_filter_sql = $astDB->escape($_REQUEST['lead_filter_sql']);
	$user_group = $astDB->escape($_REQUEST['user_group']);

    ### ERROR CHECKING 
	if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $lead_filter_id) || $lead_filter_id == null || $lead_filter_id < 4){
		$apiresults = array("result" => "Error: Special characters found in lead_filter_id, must not be empty and not less than 3 characters");
	} else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $lead_filter_name) || $lead_filter_name == null){
            $apiresults = array("result" => "Error: Special characters found in lead_filter_name and must not be empty");
        } else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $lead_filter_comments) || $lead_filter_comments == null){
                $apiresults = array("result" => "Error: Special characters found in lead_filter_comments and must not be empty");
			} else {
				if($lead_filter_sql == null){
					$apiresults = array("result" => "Error: lead_filter_sql must not be empty");
				} else {
					if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group) || $user_group == null){
						$apiresults = array("result" => "Error: Special characters found in user_group and must not be empty");
					} else {
						$groupId = go_get_groupid($goUser, $astDB);
		
						if (!checkIfTenant($groupId, $goDB)) {
							//$ul = "";
						} else {
							//$ul = "AND user_group='$groupId'";
							//$addedSQL = "WHERE user_group='$groupId'";
							$astDB->where('user_group', $groupId);
						}
		
						//$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups WHERE user_group='".mysqli_real_escape_string($link, $user_group)."' $ul ORDER BY user_group LIMIT 1;";
						$astDB->where('user_group', $user_group);
						$astDB->orderBy('user_group', 'desc');
						$rsltv = $astDB->getOne('vicidial_user_groups', 'user_group,group_name,forced_timeclock_login');
						$countResult = $astDB->getRowCount();
		
						if($countResult > 0) {
							//$queryCheck = "SELECT lead_filter_id from vicidial_lead_filters where lead_filter_id='".mysqli_real_escape_string($link, $lead_filter_id)."';";
							$astDB->where('lead_filter_id', $lead_filter_id);
							$sqlCheck = $astDB->get('vicidial_lead_filters');
							$countCheck = $astDB->getRowCount();
							if($countCheck <= 0){
								//$newQuery = "INSERT INTO vicidial_lead_filters (lead_filter_id, lead_filter_name, lead_filter_comments, lead_filter_sql, user_group) VALUES ('".mysqli_real_escape_string($link, $lead_filter_id)."', '".mysqli_real_escape_string($link, $lead_filter_name)."', '".mysqli_real_escape_string($link, $lead_filter_comments)."', '".mysqli_real_escape_string($link, $lead_filter_sql)."', '".mysqli_real_escape_string($link, $user_group)."');";
								$insertData = array(
									'lead_filter_id' => $lead_filter_id,
									'lead_filter_name' => $lead_filter_name,
									'lead_filter_comments' => $lead_filter_comments,
									'lead_filter_sql' => $lead_filter_sql,
									'user_group' => $user_group
								);
								$rsltv = $astDB->insert('vicidial_lead_filters');
								$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Lead Filter: $lead_filter_id", $log_group, $astDB->getLastQuery());

								if(!$rsltv){
									$apiresults = array("result" => "Error: Add failed, check your details");
								} else {
                                    $apiresults = array("result" => "success");
								}
							} else {
                                $apiresults = array("result" => "Error: Add failed, Lead Filter already exist!");
							}
						} else {
							$apiresults = array("result" => "Error: Invalid User Group");
						}
					}
				}
			}
		}
	}
?>