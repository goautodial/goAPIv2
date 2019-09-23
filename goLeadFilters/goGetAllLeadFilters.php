<?php
 /**
 * @file 		goGetAllLeadFilters.php
 * @brief 		API for Getting All Lead Filters
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
	
	$groupId = go_get_groupid($goUser, $astDB);
	
	if (!checkIfTenant($groupId, $goDB)) {
		//$ul = "";
	} else {
		//$ul = "AND user_group='$groupId'";
		//$addedSQL = "WHERE user_group='$groupId'";
		$astDB->where('user_group', $groupId);
	}


   	//$query = "SELECT lead_filter_id,lead_filter_name FROM vicidial_lead_filters $ul $addedSQL ORDER BY lead_filter_id;";
	$astDB->orderBy('lead_filter_id', 'desc');
   	$rsltv = $astDB->get('vicidial_lead_filters', null, 'lead_filter_id,lead_filter_name');

	foreach ($rsltv as $fresults){
		$dataLeadFilterID[] = $fresults['lead_filter_id'];
       	$dataLeadFilterName[] = $fresults['lead_filter_name'];
   		$apiresults = array("result" => "success", "lead_filter_id" => $dataLeadFilterID, "lead_filter_name" => $dataLeadFilterName);
	}
?>