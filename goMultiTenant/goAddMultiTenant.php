<?php
 /**
 * @file 		goAddMultiTenant.php
 * @brief 		API for Adding Multi Tenant
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
	$pass = $astDB->escape($_REQUEST['pass']);
	$active = $astDB->escape($_REQUEST['active']);
	$access_call_times = $astDB->escape($_REQUEST['access_call_times']);
	$access_carriers = $astDB->escape($_REQUEST['access_carriers']);
	$access_phones = $astDB->escape($_REQUEST['access_phones']);
	$access_voicemails = $astDB->escape($_REQUEST['access_voicemails']);	
	// $values = $_REQUEST['items'];
	// $list_changedate = $_REQUEST['list_changedate'];

	//tenant_id, tenant_name, admin, pass, active
    ### Default values 
    $defActive = array("Y","N");
    $defaccess_call_times = array("Y","N");
    $defaccess_carriers = array("Y","N");
    $defaccess_phones = array("Y","N");
    $defaccess_voicemails = array("Y","N");


###########################

    if($tenant_id == null) {
        $apiresults = array("result" => "Error: Set a value for Tenant ID.");
    } else {
        if($tenant_name == null) {
            $apiresults = array("result" => "Error: Set a value for Tenant name.");
        } else {
            if(!in_array($active,$defActive)) {
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
									if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $admin) || $admin  == null){
										$apiresults = array("result" => "Error: Special characters found in admin and must not be empty");
									} else {
										if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass) || $pass == null){
											$apiresults = array("result" => "Error: Special characters found in pass and must not be empty");
										} else {
											$groupId = go_get_groupid($goUser, $astDB);
											
											if (!checkIfTenant($groupId, $goDB)) {
												//$ul = "WHERE tenant_id='$tenant_id'";
												//$ug = "WHERE user_group='$tenant_id'";
												$goDB->where('tenant_id', $tenant_id);
											} else {
												//$ul = "WHERE tenant_id='$tenant_id' AND user_group='$groupId'";
												//$ug = "WHERE user_group=='$tenant_id' AND user_group='$groupId'";
												$goDB->where('tenant_id', $tenant_id);
												$goDB->where('user_group', $groupId);
											}
							
											//$queryCheck = "SELECT tenant_id,tenant_name,active FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT 1;";
											$goDB->orderBy('tenant_id', 'desc');
											$rsltv = $goDB->getOne('go_multi_tenant', 'tenant_id,tenant_name');
											$countResultCheck = $goDB->getRowCount();
											
											if($countResultCheck > 0) {
												$apiresults = array("result" => "Error: Tenant ID already exist");
											} else {
												//$queryTenant = "INSERT INTO  go_multi_tenant SET tenant_id = '$tenant_id', tenant_name = '$tenant_name', admin = '$admin', pass = '$pass', active = '$active';";
												$insertData = array(
													'tenant_id' => $tenant_id,
													'tenant_name' => $tenant_name,
													'admin' => $admin,
													'pass' => $pass,
													'active' => $active
												);
												$resultQuery = $goDB->insert('go_multi_tenant', $insertData);
												$queryTenant = $goDB->getLastQuery();
												
												// Create Tenant User Group
												//                $query = "INSERT INTO go_multi_tenant VALUES('$tenant_id','$tenant_name','$tenant_admin','$tenant_pass','$active','$access_call_times','$access_carriers','$access_phones','$access_voicemails','$phone_count')";
												//		$result = mysql_query($query, $linkgo);
												
												//-$query = $this->godb->query("SELECT id FROM user_access_group WHERE user_group='$group_template'");
												//-$access = $this->go_access->get_all_access($query->row()->id);
												//-$this->go_access->goautodialDB->insert('user_access_group',array('user_group'=>$tenant_id,'permissions'=>$access[0]->permissions,'group_level'=>'8'));
												//$queryOne = "INSERT INTO vicidial_user_groups (user_group,group_name) VALUES('$tenant_id','$tenant_name')";
												$insertData = array(
													'user_group' => $tenant_id,
													'group_name' => $tenant_name
												);
												$resultOne = $astDB->insert('vicidial_user_groups', $insertData);
												//$countResultOne = mysql_num_rows($resultOne);
												
												$groupId = go_get_groupid($goUser, $astDB);
												
												if (!checkIfTenant($groupId, $goDB)) {
													//$ul = "WHERE tenant_id='$tenant_id'";
													//$ug = "WHERE user_group='$tenant_id'";
													$goDB->where('tenant_id', $tenant_id);
												} else {
													//$ul = "WHERE tenant_id='$tenant_id' AND user_group='$groupId'";
													//$ug = "WHERE user_group=='$tenant_id' AND user_group='$groupId'";
													$goDB->where('tenant_id', $tenant_id);
													$goDB->where('user_group', $groupId);
												}
												
												//$queryCheck = "SELECT tenant_id,tenant_name,active FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT 1;";
												$goDB->orderBy('tenant_id', 'desc');
												$rsltv = $goDB->getOne('go_multi_tenant');
												$countResult = $goDB->getRowCount();
												
												if (!checkIfTenant($groupId, $goDB)) {
													$astDB->where('user_group', $tenant_id);
												} else {
													$astDB->where('user_group', $tenant_id);
													$astDB->where('user_group', $groupId);
												}
												//$queryVUG = "SELECT user_group,group_name FROM vicidial_user_groups $ug ORDER BY user_group LIMIT 1;";
												$astDB->orderBy('user_group', 'desc');
												$rsltvVUG = $astDB->getOne('vicidial_user_groups');
												$countResultOne = $astDB->getRowCount();
												
												if($countResult > 0 && $countResultOne > 0 ) {
													$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Added New Multi-Tenant Group: $tenant_id", $log_group, $queryTenant);
													$apiresults = array("result" => "success");
												} else {
													$apiresults = array("result" => "Error: Failed to add Tenant.");
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
		}
	}
?>