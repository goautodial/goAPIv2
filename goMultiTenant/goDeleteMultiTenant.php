<?php
    #######################################################
    #### Name: goDeleteMultiTenant.php	               ####
    #### Description: API to delete specific tenant    ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $tenant_id = $_REQUEST['tenant_id'];
    $goUser = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    ### Check tenant_id if its null or empty
	if($tenant_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Tenant ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
		// Get Allowed Campaigns
		$allowed_campaigns = go_getall_allowed_campaigns($tenant_id);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE tenant_id='$tenant_id'";
    		} else { 
			$ul = "WHERE tenant_id='$tenanat_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT tenant_id,tenant_name FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT 1;";
   		$rsltv = mysqli_query($linkgo, $query);

		// Delete Leads & List ID
                $query1 = "SELECT list_id FROM vicidial_lists WHERE campaign_id IN ('$allowed_campaigns')";
                $list_ids = mysqli_query($link, $query1);

                while($fresults1 = mysqli_fetch_array($list_ids, MYSQLI_ASSOC))
                {
                        $listIds[] = $fresults1['list_id'];
                
			$query2 = "DELETE FROM vicidial_lists WHERE list_id IN ('$listIds')";
			$list_ids = mysqli_query($link, $query2);
                	$query3 = "DELETE FROM vicidial_list WHERE list_id IN ('$listIds')";
			$list_ids1 = mysqli_query($link, $query3);
                }

		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataTenantID = $fresults['tenant_id'];
			}

			if(!$dataTenantID == null) {
				$deleteQuery = "DELETE FROM vicidial_campaigns WHERE tenant_id='$dataTenantID';"; 
   				//$deleteResult = mysql_query($deleteQuery, $link);
				//echo $deleteQuery;
                // Delete Campaigns & Stats
                $query4 = "DELETE FROM vicidial_campaigns WHERE campaign_id IN ('$allowed_campaigns')";
		$query4Result = mysqli_query($link, $query4);
                $query5 = "DELETE FROM vicidial_campaign_stats WHERE campaign_id IN ('$allowed_campaigns')";
		$query5Result = mysqli_query($link, $query5);
                $query6 = "DELETE FROM vicidial_campaign_agents WHERE campaign_id IN ('$allowed_campaigns')";
		$query6Result = mysqli_query($link, $query6);
                $query7 = "DELETE FROM vicidial_campaign_stats WHERE campaign_id IN ('$allowed_campaigns')";
		$query7Result = mysqli_query($link, $query7);
                $query8 = "DELETE FROM vicidial_remote_agents WHERE campaign_id IN ('$allowed_campaigns')";
		$query8Result = mysqli_query($link, $query8);
                $query9 = "DELETE FROM vicidial_live_agents WHERE campaign_id IN ('$allowed_campaigns')";
		$query9Result = mysqli_query($link, $query9);
                $query10 = "DELETE FROM vicidial_campaign_statuses WHERE campaign_id IN ('$allowed_campaigns')";
		$query10Result = mysqli_query($link, $query10);
                $query11 = "DELETE FROM vicidial_campaign_hotkeys WHERE campaign_id IN ('$allowed_campaigns')";
		$query11Result = mysqli_query($link, $query11);
                $query12 = "DELETE FROM vicidial_callbacks WHERE campaign_id IN ('$allowed_campaigns')";
		$query12Result = mysqli_query($link, $query12);
                $query13 = "DELETE FROM vicidial_lead_recycle WHERE campaign_id IN ('$allowed_campaigns')";
		$query13Result = mysqli_query($link, $query13);
                $query14 = "DELETE FROM vicidial_campaign_server_stats WHERE campaign_id IN ('$allowed_campaigns')";
		$query14Result = mysqli_query($link, $query14);
                $query15 = "DELETE FROM vicidial_server_trunks WHERE campaign_id IN ('$allowed_campaigns')";
		$query15Result = mysqli_query($link, $query15);
                $query16 = "DELETE FROM vicidial_pause_codes WHERE campaign_id IN ('$allowed_campaigns')";
		$query16Result = mysqli_query($link, $query16);
                $query17 = "DELETE FROM vicidial_campaigns_list_mix WHERE campaign_id IN ('$allowed_campaigns')";
		$query17Result = mysqli_query($link, $query17);
                $query18 = "DELETE FROM vicidial_xfer_presets WHERE campaign_id IN ('$allowed_campaigns')";
		$query18Result = mysqli_query($link, $query18);
                $query19 = "DELETE FROM vicidial_xfer_stats WHERE campaign_id IN ('$allowed_campaigns')";
		$query19Result = mysqli_query($link, $query19);
                $query20 = "DELETE FROM vicidial_hopper WHERE campaign_id IN ('$allowed_campaigns')";
		$query20Result = mysqli_query($link, $query20);
                $query21 = "DELETE FROM vicidial_call_menu WHERE user_group='$dataTenantID'";
		$query21Result = mysqli_query($link, $query21);
                $query22 = "DELETE FROM vicidial_inbound_dids WHERE user_group='$dataTenantID'";
		$query22Result = mysqli_query($link, $query22);
                $query23 = "DELETE FROM vicidial_inbound_groups WHERE user_group='$dataTenantID'";
		$query23Result = mysqli_query($link, $query23);

                // Delete Admins & Users
                $query24 = "DELETE FROM vicidial_users WHERE user_group='$dataTenantID'";
		$query24Result = mysqli_query($link, $query24);

                // Delete Widget
                $query25 = "DELETE FROM go_widget_position WHERE html_id='$dataTenantID'";
		$query25Result = mysqli_query($link, $query25);

                // Delete Phones
                $query26 = "DELETE FROM phones WHERE user_group='$dataTenantID'";
		$query26Result = mysqli_query($link, $query26);

                // Delete User Group
                $query27 = "DELETE FROM vicidial_user_groups WHERE user_group='$dataTenantID'";
		$query27Result = mysqli_query($link, $query27);

                // Delete Kamailio Entry
		/* ...
                if ($this->config->item('VARKAMAILIO')=="Y") {
                        $query = $this->kamilioDB->query("DELETE FROM subscriber WHERE username RLIKE '9999$tenantid'");
                }
		*/

                // Delete Other Stuff
                $query28 = "DELETE FROM go_login_type WHERE account_num='$dataTenantID'";
		$query28Result = mysqli_query($linkgo, $query28);
                $query29 = "DELETE FROM go_multi_tenant WHERE tenant_id='$dataTenantID'";
		$query29Result = mysqli_query($linkgo, $query29);
                $query30 = "DELETE FROM user_access_group WHERE user_group='$dataTenantID'";
		$query30Result = mysqli_query($linkgo, $query30);

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Tenant $tenant_id, its admin and all user logins under it and also deleted the Campaign and List ID','');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Multi-Tenant: $dataTenantID", $log_group, $query29);



				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Tenant doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: Tenant doesn't exist.");
		}
//	   }
	}//end
?>
