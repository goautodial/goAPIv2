<?php
 /**
 * @file 		goDeleteMultiTenant.php
 * @brief 		API for Deleting Multi Tenant
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
		// Get Allowed Campaigns
		$allowed_campaigns = go_getall_allowed_campaigns($tenant_id, $astDB);
    
		if (!checkIfTenant($groupId, $goDB)) {
        	//$ul = "WHERE tenant_id='$tenant_id'";
			$goDB->where('tenant_id', $tenant_id);
    	} else { 
			//$ul = "WHERE tenant_id='$tenanat_id' AND user_group='$groupId'";
			$goDB->where('tenant_id', $tenant_id);
			$goDB->where('user_group', $groupId);
		}

   		//$query = "SELECT tenant_id,tenant_name FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT 1;";
		$goDB->orderBy('tenant_id', 'desc');
   		$rsltv = $goDB->getOne('go_multi_tenant');
		$countResult = $goDB->getRowCount();

		// Delete Leads & List ID
		$query1 = "SELECT list_id FROM vicidial_lists WHERE campaign_id IN ('$allowed_campaigns')";
		$list_ids = $astDB->rawQuery($query1);

		foreach ($list_ids as $fresults1) {
            $listId = $fresults1['list_id'];
            
			//$query2 = "DELETE FROM vicidial_lists WHERE list_id IN ('$listIds')";
			$astDB->where('list_id', $listId);
			$list_ids = $astDB->delete('vicidial_lists');
            //$query3 = "DELETE FROM vicidial_list WHERE list_id IN ('$listIds')";
			$astDB->where('list_id', $listId);
			$list_ids1 = $astDB->delete('vicidial_list');
        }

		//$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			$dataTenantID = $rsltv['tenant_id'];

			if(!$dataTenantID == null) {
				//$deleteQuery = "DELETE FROM vicidial_campaigns WHERE tenant_id='$dataTenantID';";
   				//$deleteResult = mysql_query($deleteQuery, $link);
				//echo $deleteQuery;
                // Delete Campaigns & Stats
				$query4 = "DELETE FROM vicidial_campaigns WHERE campaign_id IN ('$allowed_campaigns')";
				$query4Result = $astDB->rawQuery($query4);
				$query5 = "DELETE FROM vicidial_campaign_stats WHERE campaign_id IN ('$allowed_campaigns')";
				$query5Result = $astDB->rawQuery($query5);
				$query6 = "DELETE FROM vicidial_campaign_agents WHERE campaign_id IN ('$allowed_campaigns')";
				$query6Result = $astDB->rawQuery($query6);
				$query7 = "DELETE FROM vicidial_campaign_stats WHERE campaign_id IN ('$allowed_campaigns')";
				$query7Result = $astDB->rawQuery($query7);
				$query8 = "DELETE FROM vicidial_remote_agents WHERE campaign_id IN ('$allowed_campaigns')";
				$query8Result = $astDB->rawQuery($query8);
				$query9 = "DELETE FROM vicidial_live_agents WHERE campaign_id IN ('$allowed_campaigns')";
				$query9Result = $astDB->rawQuery($query9);
				$query10 = "DELETE FROM vicidial_campaign_statuses WHERE campaign_id IN ('$allowed_campaigns')";
				$query10Result = $astDB->rawQuery($query10);
				$query11 = "DELETE FROM vicidial_campaign_hotkeys WHERE campaign_id IN ('$allowed_campaigns')";
				$query11Result = $astDB->rawQuery($query11);
				$query12 = "DELETE FROM vicidial_callbacks WHERE campaign_id IN ('$allowed_campaigns')";
				$query12Result = $astDB->rawQuery($query12);
				$query13 = "DELETE FROM vicidial_lead_recycle WHERE campaign_id IN ('$allowed_campaigns')";
				$query13Result = $astDB->rawQuery($query13);
				$query14 = "DELETE FROM vicidial_campaign_server_stats WHERE campaign_id IN ('$allowed_campaigns')";
				$query14Result = $astDB->rawQuery($query14);
				$query15 = "DELETE FROM vicidial_server_trunks WHERE campaign_id IN ('$allowed_campaigns')";
				$query15Result = $astDB->rawQuery($query15);
				$query16 = "DELETE FROM vicidial_pause_codes WHERE campaign_id IN ('$allowed_campaigns')";
				$query16Result = $astDB->rawQuery($query16);
				$query17 = "DELETE FROM vicidial_campaigns_list_mix WHERE campaign_id IN ('$allowed_campaigns')";
				$query17Result = $astDB->rawQuery($query17);
				$query18 = "DELETE FROM vicidial_xfer_presets WHERE campaign_id IN ('$allowed_campaigns')";
				$query18Result = $astDB->rawQuery($query18);
				$query19 = "DELETE FROM vicidial_xfer_stats WHERE campaign_id IN ('$allowed_campaigns')";
				$query19Result = $astDB->rawQuery($query19);
				$query20 = "DELETE FROM vicidial_hopper WHERE campaign_id IN ('$allowed_campaigns')";
				$query20Result = $astDB->rawQuery($query20);
				$query21 = "DELETE FROM vicidial_call_menu WHERE user_group='$dataTenantID'";
				$query21Result = $astDB->rawQuery($query21);
				$query22 = "DELETE FROM vicidial_inbound_dids WHERE user_group='$dataTenantID'";
				$query22Result = $astDB->rawQuery($query22);
				$query23 = "DELETE FROM vicidial_inbound_groups WHERE user_group='$dataTenantID'";
				$query23Result = $astDB->rawQuery($query23);
				
				// Delete Admins & Users
				$query24 = "DELETE FROM vicidial_users WHERE user_group='$dataTenantID'";
				$query24Result = $astDB->rawQuery($query24);
				
				// Delete Widget
				$query25 = "DELETE FROM go_widget_position WHERE html_id='$dataTenantID'";
				$query25Result = $astDB->rawQuery($query25);
				
				// Delete Phones
				$query26 = "DELETE FROM phones WHERE user_group='$dataTenantID'";
				$query26Result = $astDB->rawQuery($query26);
				
				// Delete User Group
				$query27 = "DELETE FROM vicidial_user_groups WHERE user_group='$dataTenantID'";
				$query27Result = $astDB->rawQuery($query27);
				
				// Delete Kamailio Entry
				/* ...
				if ($this->config->item('VARKAMAILIO')=="Y") {
					$query = $this->kamilioDB->query("DELETE FROM subscriber WHERE username RLIKE '9999$tenantid'");
				}
				*/
				
				// Delete Other Stuff
				$query28 = "DELETE FROM go_login_type WHERE account_num='$dataTenantID'";
				$query28Result = $goDB->rawQuery($query28);
				$query29 = "DELETE FROM go_multi_tenant WHERE tenant_id='$dataTenantID'";
				$query29Result = $goDB->rawQuery($query29);
				$logQuery = $goDB->getLastQuery();
				$query30 = "DELETE FROM user_access_group WHERE user_group='$dataTenantID'";
				$query30Result = $goDB->rawQuery($query30);

				$log_id = log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted Multi-Tenant: $dataTenantID", $log_group, $logQuery);

				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Tenant doesn't exist.");
			}
		} else {
			$apiresults = array("result" => "Error: Tenant doesn't exist.");
		}
	}//end
?>