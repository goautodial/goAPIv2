<?php
 /**
 * @file 		goEditMultiTenant.php
 * @brief 		API for Modifying Multi Tenant
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
    $tenant_name = $astDB->escape($_REQUEST['tenant_name']);
    $admin = $astDB->escape($_REQUEST['admin']);
    //$access_call_times = $_REQUEST['access_call_times'];
    //$access_carriers = $_REQUEST['access_carriers'];
    //$access_phones = $_REQUEST['access_phones'];
    //$access_voicemails = $_REQUEST['access_voicemails'];
   // $values = $_REQUEST['item'];
    $active = strtoupper($astDB->escape($_REQUEST['active']));
   //tenant_id, tenant_name, admin, active
    ### Default values 
    $defActive = array("Y","N");
    $defaccess_call_times = array("Y","N");
    $defaccess_carriers = array("Y","N");
    $defaccess_phones = array("Y","N");
	$defaccess_voicemails = array("Y","N");
	
###########################
//Error Checking
    if($tenant_id == null) {
        $apiresults = array("result" => "Error: Set a value for Tenant ID.");
    } else {
		if(!in_array($active,$defActive) && $active != null) {
			$apiresults = array("result" => "Error: Default value for active is Y or N only.");
		} else {
			if(!in_array($access_call_times,$defaccess_call_times) && $access_call_times != null) {
				$apiresults = array("result" => "Error: Default value for access_call_times is Y or N only.");
			} else {
                if(!in_array($access_carriers,$defaccess_carriers) && $access_carriers != null) {
                    $apiresults = array("result" => "Error: Default value for access_carriers is Y or N only.");
                } else {
					if(!in_array($access_voicemails,$defaccess_voicemails) && $access_voicemails != null) {
						$apiresults = array("result" => "Error: Default value for access_voicemails is Y or N only.");
					} else {
						if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $tenant_id)){
							$apiresults = array("result" => "Error: Special characters found in tenant_id");
						} else {
							if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $tenant_name)){
								$apiresults = array("result" => "Error: Special characters found in tenant_name");
							} else {
								if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $admin)){
									$apiresults = array("result" => "Error: Special characters found in admin");
								} else {
									if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass)){
										$apiresults = array("result" => "Error: Special characters found in pass");
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
										foreach ($rsltv as $fresults){
											$datatenant_id = $fresults['tenant_id'];
											$datatenant_name = $fresults['tenant_name'];
											$dataadmin = $fresults['admin'];
											$dataactive = $fresults['active'];
										}
										$countResult = $goDB->getRowCount();
						
										if($countResult > 0) {
											if($tenant_id == null) { $tenant_id = $datatenant_id; }
											if($tenant_name == null) { $tenant_name = $datatenant_name; }
											if($admin == null) { $admin = $dataadmin; }
											if($active == null) { $active = $dataactive; }
											
											//$query = "UPDATE go_multi_tenant SET tenant_id = '$tenant_id', tenant_name = '$tenant_name', admin = '$admin', active = '$active' WHERE tenant_id='$tenant_id';";
											$insertData = array(
												'tenant_id' => $tenant_id,
												'tenant_name' => $tenant_name,
												'admin' => $admin,
												'active' => $active
											);
											$goDB->where('tenant_id', $tenant_id);
											$result = $goDB->update('go_multi_tenant', $insertData);
											$logQuery = $goDB->getLastQuery();
											
											$log_id = log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Multi-Tenant: $tenant_id", $log_group, $logQuery);
											$apiresults = array("result" => "success");
										} else {
											$apiresults = array("result" => "Error: Tenant doesn't exist");
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
?>