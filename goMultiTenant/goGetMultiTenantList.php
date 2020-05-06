<?php
 /**
 * @file 		goGetMultiTenantList.php
 * @brief 		API for Getting Multi Tenant List
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
	
    $limit = $astDB->escape($_REQUEST['limit']);
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }
 
    $groupId = go_get_groupid($goUser, $astDB);
    
	if (!checkIfTenant($groupId, $goDB)) {
		//$ul='';
	} else {
		//$ul = "WHERE user_group='$groupId'";
		$goDB->where('user_group', $groupId);
	}

   	//$query = "SELECT tenant_id,tenant_name,active FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT $limit;";
	$goDB->orderBy('tenant_id', 'desc');
   	$rsltv = $goDB->get('go_multi_tenant', $limit, 'tenant_id,tenant_name,active');

	foreach ($rsltv as $fresults){
		$dataTenantId[] = $fresults['tenant_id'];
       	$dataTenantName[] = $fresults['tenant_name'];// .$fresults['dial_method'].$fresults['active'];
		//$dataDialMethod[] = $fresults['active'];
		$dataActive[] = $fresults['active'];

		//$query1 = "SELECT count(*) as cnt FROM vicidial_users WHERE user_group='{$fresults['tenant_id']}' AND user_level < '7';";
		$astDB->where('user_group', $fresults['tenant_id']);
		$astDB->where('user_level', 7, '<');
		$rsltv1 = $astDB->get('vicidial_users');
		$dataCount = $astDB->getRowCount();
		
   		$apiresults = array("result" => "success", "cnt" => $dataCount, "tenant_id" => $dataTenantId, "tenant_name" => $dataTenantName, "active" => $dataActive);
	}
?>