<?php
   ####################################################
   #### Name: goEditMultiTenant.php                ####
   #### Description: API to edit specific tenant   ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
    $tenant_id = $_REQUEST['tenant_id'];
    $tenant_name = $_REQUEST['tenant_name'];
    $admin = $_REQUEST['admin'];
    //$access_call_times = $_REQUEST['access_call_times'];
    //$access_carriers = $_REQUEST['access_carriers'];
    //$access_phones = $_REQUEST['access_phones'];
    //$access_voicemails = $_REQUEST['access_voicemails'];
   // $values = $_REQUEST['item'];
    $goUser = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
    $active = strtoupper($_REQUEST['active']);
   //tenant_id, tenant_name, admin, active
    ### Default values 
    $defActive = array("Y","N");
    $defaccess_call_times = array("Y","N");
    $defaccess_carriers = array("Y","N");
    $defaccess_phones = array("Y","N");
    $defaccess_voicemails = array("Y","N");
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

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


                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "WHERE tenant_id='$tenant_id'";
                } else {
                        $ul = "WHERE tenant_id='$tenant_id' AND user_group='$groupId'";
                }

                $query = "SELECT tenant_id,tenant_name,active FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT 1;";
                $rsltv = mysqli_query($linkgo, $query);
                while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
					$datatenant_id = $fresults['tenant_id'];
					$datatenant_name = $fresults['tenant_name'];
					$dataadmin = $fresults['admin'];
                			$dataactive = $fresults['active'];
                			
				}

                $countResult = mysqli_num_rows($rsltv);

                if($countResult > 0) {

/*
                $items = explode("&",str_replace(";","",$values));

                foreach ($items as $item)
                {
                        list($var,$val) = explode("=",$item,2);
                        if ($var!="tenant_id")
                                $itemSQL .= "$var='".str_replace('+',' ',mysql_real_escape_string($val))."', ";

                        if ($var=="tenant_id")
                                $tenant_id = "$val";

                        if ($var=="active")
                                $status = "$val";
                }

                $itemSQL = rtrim($itemSQL,', ');
                
                */
                if($tenant_id == null){$tenant_id = $datatenant_id;} if($tenant_name == null){$tenant_name = $datatenant_name;} if($admin == null){$admin = $dataadmin;} if($active == null){$active = $dataactive;}
                $query = "UPDATE go_multi_tenant SET tenant_id = '$tenant_id', tenant_name = '$tenant_name', admin = '$admin', active = '$active' WHERE tenant_id='$tenant_id';";
		$result = mysqli_query($linkgo, $query);

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Tenant ID $tenant_id','UPDATE go_multi_tenant SET tenant_id=$tenant_id, tenant_name=$tenant_name, admin=$admin, active=$active WHERE tenant_id=$tenant_id;');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
				$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified Multi-Tenant: $tenant_id", $log_group, $query);



		$apiresults = array("result" => "success");

		} else {
		$apiresults = array("result" => "Error: Tenant doesn't exist");
		}
        	}        
	}}}}}}}
        }



?>
