<?php
 /**
 * @file 		goGetMultiTenantInfo.php
 * @brief 		API for Getting Multi Tenant Info
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
    $tenant_id = $astDB->escape($_REQUEST['tenant_id']);
    
    ### Check tenant_id if its null or empty
	if($tenant_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Tenant ID."); 
	} else {
    	$groupId = go_get_groupid($goUser, $astDB);
    
		if (!checkIfTenant($groupId, $goDB)) {
        	//$ul = "WHERE tenant_id='$tenant_id'";
			$goDB->where('tenant_id', $tenant_id);
    	} else { 
			//$ul = "WHERE tenant_id='$tenant_id' AND user_group='$groupId'";
			$goDB->where('tenant_id', $tenant_id);
			$goDB->where('user_group', $groupId);
		}

   		//$query = "SELECT tenant_id,tenant_name,active FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT 1;";
		$goDB->orderBy('tenant_id', 'desc');
   		$rsltv = $goDB->getOne('go_multi_tenant', 'tenant_id,tenant_name,active');
		$countResult = $goDB->getRowCount();

		if($countResult > 0) {
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
		} else {
			$apiresults = array("result" => "Error: Tenant doesn't exist.");
		}
	}
?>