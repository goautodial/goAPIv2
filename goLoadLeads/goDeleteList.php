<?php
 /**
 * @file 		goDeleteList.php
 * @brief 		API for Deleting Lists
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
    
	if($list_id == null) { 
		$apiresults = array("result" => "Error: Set a value for List ID."); 
	} else {
    	$groupId = go_get_groupid($goUser, $astDB);
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

			if(!$dataListID == null) {
				$deleteQuery = "DELETE FROM vicidial_lists WHERE list_id='$dataListID';"; 
   				$deleteResult = $astDB->rawQuery($deleteQuery);
				$deleteQueryLeads = "DELETE FROM vicidial_list WHERE list_id='$dataListID';"; 
   				$deleteResultLeads = $astDB->rawQuery($deleteQueryLeads);
				$deleteQueryStmt = "DELETE FROM vicidial_lists_fields WHERE list_id='$dataListID' LIMIT 1;"; 
   				$deleteResultStmt = $astDB->rawQuery($deleteQueryStmt);
				
				$log_id = log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted List ID: $dataListID", $log_group, $deleteQuery);
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: List doesn't exist.");
			}
		} else {
			$apiresults = array("result" => "Error: List doesn't exist.");
		}
	}//end
?>