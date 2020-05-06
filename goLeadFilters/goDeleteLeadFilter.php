<?php
 /**
 * @file 		goDeleteLeadFilter.php
 * @brief 		API for Deleting Lead Filters
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
    
    ### Check lead filter ID if its null or empty
	if($lead_filter_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Lead Filter ID."); 
	} else {
		$groupId = go_get_groupid($goUser, $astDB);

		if (!checkIfTenant($groupId, $goDB)) {
			//$ul = "";
		} else {
			//$ul = "AND user_group='$groupId'";
			//$addedSQL = "WHERE user_group='$groupId'";
			$astDB->where('user_group', $groupId);
		}
   		//$query = "SELECT lead_filter_id FROM vicidial_lead_filters $ul where lead_filter_id='$lead_filter_id';";
		$astDB->where('lead_filter_id', $lead_filter_id);
   		$rsltv = $astDB->get('vicidial_lead_filters');
		$countResult = $astDB->getRowCount();

		if($countResult > 0) {
			$dataLeadFilterID = $lead_filter_id;

			if(!$dataLeadFilterID == null) {
				//$deleteQuery = "DELETE FROM vicidial_lead_filters WHERE lead_filter_id='$dataLeadFilterID';";
				$astDB->where('lead_filter_id', $dataLeadFilterID);
   				$deleteResult = $astDB->delete('vicidial_lead_filters');
				
				$log_id = log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Lead Filter ID: $dataLeadFilterID", $log_group, $astDB->getLastQuery());
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Lead Filter doesn't exist.");
			}
		} else {
			$apiresults = array("result" => "Error: Lead Filter doesn't exist.");
		}
	}//end
?>