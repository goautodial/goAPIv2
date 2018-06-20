<?php
/**
 * @file        goGetUserGroupsList.php
 * @brief       API to get all user group details
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho 
 * @author      Alexander Jim H. Abenoja
 * @author      Jeremiah Sebastian V. Samatra
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
    
    //$limit = $astDB->escape($_REQUEST['limit']);
    //if($limit < 1){ $limit = 100; } else { $limit = $limit; }
 
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
    
	if (!checkIfTenant($log_group, $goDB)) {
		if($log_group !== "ADMIN")
		$astDB->where("user_group", $log_group);
    } else {
		$astDB->where("user_group", $log_group);
		$astDB->where("user_group", $log_group);
	}

	$group_type = "Default";
	$cols = Array("user_group", "group_name", "forced_timeclock_login");
	$select = $astDB->get("vicidial_user_groups", NULL, $cols);
   	//$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY group_name LIMIT $limit;";
   	
	foreach($select as $fresults){
		$group_type = 'Default';
		$goDB->where("tenant_id", $fresults["user_group"]);
		$goDB->getOne("go_multi_tenant");
		//$gQuery = "SELECT * FROM go_multi_tenant WHERE tenant_id='{$fresults['user_group']}';";
		
		if ($goDB->count > 0) {
			$group_type = 'Multi-tenant';
		}

		$dataUserGroup[] = $fresults['user_group'];
       	$dataGroupName[] = $fresults['group_name'];// .$fresults['dial_method'].$fresults['active'];
		$dataGroupType[] = $group_type;
		$dataForced[] = $fresults['forced_timeclock_login'];
	}

	$apiresults = array("result" => "success", "user_group" => $dataUserGroup, "group_name" => $dataGroupName, "group_type" => $dataGroupType, "forced_timeclock_login" => $dataForced);

?>
