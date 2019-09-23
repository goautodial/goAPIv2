<?php
 /**
 * @file 		goEditLeadFilter.php
 * @brief 		API for Modifying Lead Filters
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

    ### ERROR CHECKING ...
    if($lead_filter_id == null) { 
        $apiresults = array("result" => "Error: Set a value for Lead Filter ID."); 
    } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $lead_filter_name)){
            $apiresults = array("result" => "Error: Special characters found in lead filter name");
        } else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $lead_filr_comments)){
                $apiresults = array("result" => "Error: Special characters found in lead filter comments");
			} else {
				$lead_filter_id = $astDB->escape($lead_filter_id);

                $groupId = go_get_groupid($goUser, $astDB);

                if (!checkIfTenant($groupId, $goDB)) {
                    //$ul = "";
                } else {
                    //$ul = "AND user_group='$groupId'";
					//$addedSQL = "WHERE user_group='$groupId'";
					$astDB->where('user_group', $groupId);
                }
				//$queryCheck = "SELECT lead_filter_id, lead_filter_name, lead_filter_comments, lead_filter_sql, user_group from vicidial_lead_filters where lead_filter_id='$lead_filter_id' $ul $addedSQL;";
				$astDB->where('lead_filter_id', $lead_filter_id);
				$sqlCheck = $astDB->get('vicidial_lead_filters', null, 'lead_filter_id, lead_filter_name, lead_filter_comments, lead_filter_sql, user_group');

                foreach ($sqlCheck as $fresults){
					$dataLF_id = $fresults['lead_filter_id'];
					$dataLF_name = $fresults['lead_filter_name'];
					$dataLF_comments = $fresults['lead_filter_comments'];
					$dataLF_sql = $fresults['lead_filter_sql'];
					$dataLF_ug = $fresults['user_group'];
				}
                $countLF = $astDB->getRowCount();
				
                if($countLF > 0) {
					if($lead_filter_id == null) {$lead_filter_id = $dataLF_id;}
					if($lead_filter_name == null) {$lead_filter_name = $dataLF_name;}
					if($lead_filter_comments == null) {$lead_filter_comments = $dataLF_comments;}
					if($lead_filter_sql == null) {$lead_filter_sql = $dataLF_sql;}
					if($user_group == null) {$user_group = $dataLF_ug;}

					//$queryVM ="UPDATE vicidial_lead_filters SET lead_filter_name='".mysqli_real_escape_string($link, $lead_filter_name)."',  lead_filter_comments='".mysqli_real_escape_string($link, $lead_filter_comments)."',  lead_filter_sql='".mysqli_real_escape_string($link, $lead_filter_sql)."',  user_group='".mysqli_real_escape_string($link, $user_group)."' WHERE lead_filter_id='".mysqli_real_escape_string($link, $lead_filter_id)."'";
					$updateData = array(
						'lead_filter_name' => $lead_filter_name,
						'lead_filter_comments' => $lead_filter_comments,
						'lead_filter_sql' => $lead_filter_sql,
						'user_group' => $user_group
					);
					$astDB->where('lead_filter_id', $lead_filter_id);
					$rsltv1 = $astDB->update('vicidial_lead_filters', $updateData);
					
					if(!$rsltv1){
						$apiresults = array("result" => "Error: Try updating Lead Filter Again");
					} else {
						$apiresults = array("result" => "success");
						$log_id = log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Lead Filter ID: $lead_filter_id", $log_group, $astDB->getLastQuery());
					}
				} else {
					$apiresults = array("result" => "Error: Lead Filter doesn't exist", "count" => $countLF);
				}
			}
		}
	}
?>