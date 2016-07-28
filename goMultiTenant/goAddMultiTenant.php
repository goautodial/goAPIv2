<?php
   ####################################################
   #### Name: goAddList.php                        ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
	### POST or GET Variables
        $tenant_id = $_REQUEST['tenant_id'];
        $tenant_name = $_REQUEST['tenant_name'];
        $admin = $_REQUEST['admin'];
        $pass = $_REQUEST['pass'];
        $active = $_REQUEST['active'];
        $access_call_times = $_REQUEST['access_call_times'];
        $access_carriers = $_REQUEST['access_carriers'];
        $access_phones = $_REQUEST['access_phones'];
        $access_voicemails = $_REQUEST['access_voicemails'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
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


                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "WHERE tenant_id='$tenant_id'";
                        $ug = "WHERE user_group='$tenant_id'";
                } else {
                        $ul = "WHERE tenant_id='$tenant_id' AND user_group='$groupId'";
                        $ug = "WHERE user_group=='$tenant_id' AND user_group='$groupId'";
                }

                $queryCheck = "SELECT tenant_id,tenant_name,active FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT 1;";
                $rsltv = mysqli_query($linkgo, $queryCheck);
                $countResultCheck = mysqli_num_rows($rsltv);


		if($countResultCheck > 0) {
			$apiresults = array("result" => "Error: Tenant ID already exist");
		} else {
                              /*  $items = $values;
                                foreach (explode("&",$items) as $item)
                                {
                                        list($var,$val) = explode("=",$item,2);
                                        if (strlen($val) > 0)
                                        {
                                                        $itemSQL .= "$var='".str_replace('+',' ',mysql_real_escape_string($val))."', ";

                                        }
                                }
                                $itemSQL = rtrim($itemSQL,', ');
                                */
                                $query = "INSERT INTO  go_multi_tenant SET tenant_id = '$tenant_id', tenant_name = '$tenant_name', admin = '$admin', pass = '$pass', active = '$active';";
                                $resultQuery = mysqli_query($linkgo, $query);




                // Create Tenant User Group
//                $query = "INSERT INTO go_multi_tenant VALUES('$tenant_id','$tenant_name','$tenant_admin','$tenant_pass','$active','$access_call_times','$access_carriers','$access_phones','$access_voicemails','$phone_count')";
//		$result = mysql_query($query, $linkgo);




                //-$query = $this->godb->query("SELECT id FROM user_access_group WHERE user_group='$group_template'");
                //-$access = $this->go_access->get_all_access($query->row()->id);
        	//-$this->go_access->goautodialDB->insert('user_access_group',array('user_group'=>$tenant_id,'permissions'=>$access[0]->permissions,'group_level'=>'8'));
                $queryOne = "INSERT INTO vicidial_user_groups (user_group,group_name) VALUES('$tenant_id','$tenant_name')";
		$resultOne = mysqli_query($link, $queryOne);
		//$countResultOne = mysql_num_rows($resultOne);


                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "WHERE tenant_id='$tenant_id'";
			$ug = "WHERE user_group='$tenant_id'";
                } else {
                        $ul = "WHERE tenant_id='$tenant_id' AND user_group='$groupId'";
                        $ug = "WHERE user_group=='$tenant_id' AND user_group='$groupId'";
                }

                $queryCheck = "SELECT tenant_id,tenant_name,active FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT 1;";
                $rsltv = mysqli_query($linkgo, $queryCheck);
                $countResult = mysqli_num_rows($rsltv);


                $queryVUG = "SELECT user_group,group_name FROM vicidial_user_groups $ug ORDER BY user_group LIMIT 1;";
                $rsltvVUG = mysqli_query($link, $queryVUG);
                $countResultOne = mysqli_num_rows($rsltvVUG);



                if($countResult > 0 && $countResultOne > 0 ) {


        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Tenant $tenant_id','INSERT INTO vicidial_user_groups (tenant_id, tenant_name, tenant_admin, tenant_pass, group_template, active, access_call_times, access_carriers, access_phones, access_voicemails, agent_count, phone_count, tenant_agent_pass, protocol, server_ip) VALUES($tenant_id, $tenant_name, $tenant_admin, $tenant_pass, $group_template, $active, $access_call_times, $access_carriers, $access_phones, $access_voicemails, $agent_count, $phone_count, $tenant_agent_pass,$protocol, $server_ip);');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


			$apiresults = array("result" => "success");
                } else {
                        $apiresults = array("result" => "Error: Failed to add Tenant.");
                }
                        

		}
}}}}		
}}}}
		}
}
/*
                // Create Tenant Admin
                //-$userTemplate = $this->go_access->user_templates($group_template);
                $userTemplate['user'] = $tenant_admin;
                $userTemplate['pass'] = $tenant_pass;
                $userTemplate['full_name'] = $tenant_name . " Admin";
                $userTemplate['user_group'] = $tenant_id;
                $userTemplate['user_level'] = '8';
                $userTemplate['active'] = $active;
                $userTemplate['agentonly_callbacks'] = '1';
                $userTemplate['agentcall_manual'] = '1';
                $settings = $this->go_get_systemsettings();

                $exten_prefix = "";
                //if ($this->config->item('VARKAMAILIO')=="Y" && $protocol=="EXTERNAL")
                //      $exten_prefix = "9999";

                $result = $this->gouser->insertuser('vicidial_users',$userTemplate,array('user'=>$tenant_admin)); #insert data to vicidial_users
                $this->gouser->goautodialDB->insert('go_login_type',array('account_num'=>$tenant_admin,'new_signup'=>'1'));
                //$query = $this->db->query("INSERT INTO phones (extension,dialplan_number,voicemail_id,server_ip,login,pass,status,active,fullname,protocol,local_gmt,voicemail_timezone,user_group) values('{$tenant_id}000','9999{$tenant_id}000','{$tenant_id}000','$server_ip','{$tenant_id}000','$tenant_pass','ACTIVE','$active','$tenant_id Admin Phone','$protocol','{$settings->default_local_gmt}','{$settings->default_voicemail_timezone}','$tenant_id');");
                // Update Widget for this account
                $widget = $this->go_update_widget($tenant_admin,$tenant_id);

                // Adding of Admin Extention on Kamailio
                if ($this->config->item('VARKAMAILIO')=="Y" && $protocol=="EXTERNAL") {
                        $kamialioq = "INSERT INTO subscriber (username, domain, password) VALUES ('{$exten_prefix}{$tenant_id}000','goautodial.com','$tenant_pass');";
                        $this->kamilioDB->query($kamialioq);
                }

                // Create 10 Default Agents and Phones
                $userTemplate = $this->go_access->user_templates();
                for ($i=1;$i<=$agent_count;$i++)
                {
                        if ($i<10) $lastnum = "00{$i}";
                        else $lastnum = "0{$i}";
                        $extension = "";

                        if ($i <= $phone_count) {
                                $extension = "$exten_prefix$tenant_id$lastnum";
                        }

                        $userTemplate['user'] = "$tenant_id$lastnum";
                        $userTemplate['pass'] = $tenant_agent_pass;
                        $userTemplate['full_name'] = "$tenant_id Agent $lastnum";
                        $userTemplate['user_group'] = $tenant_id;
                        $userTemplate['phone_login'] = "";
                        $userTemplate['phone_pass'] = "";
                        if (strlen($extension) > 0) {
                                $userTemplate['phone_login'] = $extension;
                                $userTemplate['phone_pass'] = $tenant_agent_pass;
                        }
                        $userTemplate['active'] = $active;
                        $userTemplate['agentonly_callbacks'] = '1';
                        $userTemplate['agentcall_manual'] = '1';

                        $result = $this->gouser->insertuser('vicidial_users',$userTemplate,array('user'=>$userTemplate['user'])); #insert data to vicidial_users

                        if (strlen($extension) > 0) {
                                $queryInsert = "INSERT INTO phones (extension,dialplan_number,voicemail_id,server_ip,login,pass,status,active,fullname,protocol,local_gmt,voicemail_timezone,user_group) values('$extension','9999$extension','$extension','$server_ip','$extension','$tenant_agent_pass','ACTIVE','$active','$tenant_id Agent Phone','$protocol','{$settings->default_local_gmt}','{$settings->default_voicemail_timezone}','$tenant_id');";
				$resultInsert = mysql_query($queryInsert, $link);

                                // Adding of Agent Extensions on Kamailio
                                if ($this->config->item('VARKAMAILIO')=="Y" && $protocol=="EXTERNAL") {
                                        $kamialioq = "INSERT INTO subscriber (username, domain, password) VALUES ('$extension','goautodial.com','$tenant_agent_pass');";
                                        $this->kamilioDB->query($kamialioq);
                                }
                        }
                }

                $queryUpdate = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
		$resultUpdate = mysql_query($queryUpdate, $link);

                // Create Campaign
                $local_call_time = "9am-9pm";
                $NOW = date("Y-m-d");
                $SQLdate = date("Y-m-d H:i:s");

                do
                {
                        $campaign_id = rand(10000000,99999999);
                        $query = $this->db->query("SELECT count(*) AS cnt FROM vicidial_campaigns WHERE campaign_id='$campaign_id'");
                } while ($query->row()->cnt > 0);
                $campaign_desc = "Outbound Campaign - $NOW";
                $query = $this->db->query("INSERT INTO vicidial_campaigns (campaign_id,campaign_name,active,dial_method,dial_status_a,
                                                                        dial_statuses,lead_order,allow_closers,hopper_level,auto_dial_level,
                                                                        next_agent_call,local_call_time,get_call_launch,campaign_changedate,
                                                                        campaign_stats_refresh,list_order_mix,dial_timeout,
                                                                        campaign_vdad_exten,campaign_recording,campaign_rec_filename,scheduled_callbacks,
                                                                        scheduled_callbacks_alert,no_hopper_leads_logins,use_internal_dnc,use_campaign_dnc,
                                                                        available_only_ratio_tally,campaign_cid,manual_dial_filter,user_group)
                                                                        VALUES('$campaign_id','$campaign_desc','$active','MANUAL','NEW',' N NA A AA DROP B NEW -','DOWN','Y','500','0','random',
                                                                        '$local_call_time','NONE','$SQLdate','Y','DISABLED','30','8369','NEVER','FULLDATE_CUSTPHONE_CAMPAIGN_AGENT',
                                                                        'Y','BLINK_RED','Y','Y','Y','Y','5164536886','DNC_ONLY','$tenant_id')");

                $query = $this->db->query("INSERT INTO vicidial_campaign_stats (campaign_id) values('$campaign_id')");
                $query = $this->db->query("UPDATE vicidial_user_groups SET allowed_campaigns=' $campaign_id -' WHERE user_group='$tenant_id'");

                // Create List ID
                $NOW = date("m-d-Y");
                $list_id = "{$tenant_id}1";
                $list_name = "ListID $list_id";
                $query = $this->db->query("INSERT INTO vicidial_lists (list_id,list_name,campaign_id,active,list_description,list_changedate)
                                                                        values('$list_id','$list_name','$campaign_id','$active','Auto-Generated ListID - $NOW','$SQLdate')");

        goEditCampaign.php        $this->commonhelper->auditadmin('ADD',"Added a New Tenant $tenant_id with $agent_count Agents/Phones using $tenant_admin as the Admin Login and created new Campaign $campaign_id and List ID $list_id");
                return "SUCCESS";
        }
*/



###########################

?>
