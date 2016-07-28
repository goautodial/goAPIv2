<?php
   ####################################################
   #### Name: goAddCarrier.php                     ####
   #### Description: API to add new carrier        ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
	### POST or GET Variables
        $carrier_id = $_REQUEST['carrier_id'];
        $carrier_name = $_REQUEST['carrier_name'];
        $carrier_description = $_REQUEST['carrier_description'];
        $user_group = $_REQUEST['user_group'];
       // $account_entry = $_REQUEST['account_entry'];
       // $protocol = $_REQUEST['protocol'];
      //  $dialplan_entry = $_REQUEST['dialplan_entry'];
       $active = $_REQUEST['active'];
       // $values = $_REQUEST['item'];
        $protocol = $_REQUEST['protocol'];
        $server_ip = $_REQUEST['server_ip'];

        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];


	$defProtocol = array('SIP','Zap','IAX2','EXTERNAL');
        $defActive = array("Y","N");

########################################

        if($carrier_id == null) {
                $apiresults = array("result" => "Error: Set a value for Carrier ID.");
        } else {
        if($carrier_name == null) {
                $apiresults = array("result" => "Error: Set a value for Carrier Name.");
        } else {
        if($carrier_description == null) {
                $apiresults = array("result" => "Error: Set a value for Carrier Description.");
        } else {
        if($user_group == null) {
                $apiresults = array("result" => "Error: Set a value for User Group.");
        } else {
        if($server_ip == null) {
                $apiresults = array("result" => "Error: Set a value for server ip.");
        } else {


                if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {
                if(!in_array($protocol,$defProtocol) && $protocol != null) {
                        $apiresults = array("result" => "Error: Default value for protocol is SIP, Zap, IAX2 or EXTERNAL only.");
                } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $carrier_id)){
                $apiresults = array("result" => "Error: Special characters found in carrier_id");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $carrier_name)){
                $apiresults = array("result" => "Error: Special characters found in carrier_name");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $carrier_description)){
                $apiresults = array("result" => "Error: Special characters found in carrier_description");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group)){
                $apiresults = array("result" => "Error: Special characters found in user_group");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $account_entry)){
                $apiresults = array("result" => "Error: Special characters found in account_entry");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $dialplan_entry)){
                $apiresults = array("result" => "Error: Special characters found in dialplan_entry");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $server_ip)){
                $apiresults = array("result" => "Error: Special characters found in server_ip");
        } else {



                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "WHERE carrier_id ='$carrier_id'";
			$ulug = "WHERE user_group='$user_group'";
                } else {
                        $ul = "WHERE carrier_id ='$carrier_id' AND user_group='$groupId'";
			$ulug = "WHERE user_group='$user_group' AND user_group='$groupId'";
                }

                $query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ulug ORDER BY user_group LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
                $countResult = mysqli_num_rows($rsltv);

            if($countResult <= 0) {
			$apiresults = array("result" => "Error: Invalid User Group.");
		} else {


                $queryCheck = "SELECT carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
                $rsltv = mysqli_query($link, $queryCheck);
                $countCheck = mysqli_num_rows($rsltv);

                if($countCheck > 0) {
			$apiresults = array("result" => "Error: Carrier already exist.");
		} else {

       //                 if ($action == "add_new_carrier")
          //              {
                                $items = explode("&",str_replace(";","",$values));
                                foreach ($items as $item)
                                {
                                        list($var,$val) = explode("=",$item,2);
                                        if (strlen($val) > 0)
                                        {
                                                if ($var!="reg_auth" && $var!="reg_user"
                                                        && $var!="reg_pass" && $var!="reg_host" && $var!="reg_port"
                                                        && $var!="ip_user" && $var!="ip_pass" && $var!="ip_host"
                                                        && $var!="allow_gsm" && $var!="allow_ulaw" && $var!="allow_alaw"
                                                        && $var!="allow_g729" && $var!="dtmf_mode" && $var!="customDTMF"
                                                        && $var!="dialprefix" && $var!="allow_custom" && $var!="customCodecs"
                                                        && $var!="customProtocol")
                                                {
                                                        $varSQL .= "$var,";
                                                        $valSQL .= "'".str_replace('+',' ',mysqli_real_escape_string($val))."',";
                                                }

                                                if ($var=="carrier_id")
                                                        $carrier_id="$val";

                                                if ($var=="server_ip")
                                                        $server_ip="$val";

                                                if ($var=="registration_string")
                                                        $reg_string="$val";
                                        }
                                }
                                $reg_string_orig = $reg_string;
                                $reg_string = substr($reg_string,0,strpos($reg_string,":5060"));
                                $reg_string = substr($reg_string,strrpos($reg_string,"@") + 1);
                                $get_dns = dns_get_record("$reg_string");

                                foreach ($get_dns as $dns)
                                {
                                        if ($dns['type'] == "A")
                                        {
                                                $reg_ipSQL = "OR registration_string rlike '@".$dns['ip'].":'";
                                        }
                                }

                                if ($reg_string=="208.43.27.84")
                                {
                                        $reg_string = "dal.justgovoip.com";
                                        $reg_ipSQL = "OR registration_string rlike '@208.43.27.84:'";
                                }

                                $querySelect = "select carrier_id from vicidial_server_carriers where registration_string rlike '@$reg_string:' $reg_ipSQL AND server_ip='$server_ip';";
				$resultSelect = mysqli_query($link, $querySelect);

                                $isExist = mysqli_num_rows($resultSelect);
                                if (!$isExist)
                                {
                                        if ($reg_string=="dal.justgovoip.com" || $reg_string=="208.43.27.84")
                                        {/*
                                                $r = $this->commonhelper->getAccountInfo("username",substr($reg_string_orig,strrpos($reg_string_orig,"/") + 1));
                                                $data['carrier_id']             = $carrier_id;
                                                $data['username']               = $r->structmem('username')->getval();
                                                $data['web_password']   = "Check your email for your web password.";
                                                $data['authname']               = $r->structmem('authname')->getval();
                                                $voip_password                  = substr($reg_string_orig,strpos($reg_string_orig,":") + 1);
                                                $voip_password                  = substr($voip_password,0,strrpos($voip_password,"@"));
                                                $data['voip_password']  = $voip_password;
                                                $data['vm_password']    = '';
                                                $data['i_account']              = $r->structmem('i_account')->getval();

                                                $this->go_carriers->goautodialDB->insert('justgovoip_sippy_info',$data);
                                        */}
                                        $varSQL = rtrim($varSQL,",");
                                        $valSQL = rtrim($valSQL,",");
                                        $itemSQL = "($varSQL) VALUES ($valSQL)";
                                        $queryVSC = "INSERT INTO vicidial_server_carriers (carrier_id, carrier_name,  carrier_description, user_group, protocol, server_ip) VALUES ('$carrier_id', '$carrier_name',  '$carrier_description', '$user_group', '$protocol', '$server_ip');";
					$resultVSC = mysqli_query($link, $queryVSC);

			                $query = "SELECT carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
                			$rsltv = mysqli_query($link, $query);
                			$countResult = mysqli_num_rows($rsltv);

                			if($countResult > 0) {
                                                //$this->commonhelper->auditadmin('ADD',"Added New Carrier $carrier_id","INSERT INTO vicidial_server_carriers $itemSQL;");
                                                $queryUpdate = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
						$resultVSC = mysqli_query($link, $queryUpdate);

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Carrier ID $carrier_id','INSERT INTO vicidial_server_carriers (carrier_id, carrier_name,  carrier_description, user_group, protocol, server_ip) VALUES ($carrier_id, $carrier_name,  $carrier_description, $user_group, $protocol, $server_ip)');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


                                                $apiresults = array("result" => "success");
					}	
                                        			
                                	} else {
						$apiresults = array("result" => "Error: Carrier  doens't exist.");
                                	}
			}}
#######################################
}}}}}}}}
}}}}
}}
?>
