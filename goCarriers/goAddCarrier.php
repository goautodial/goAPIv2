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
	include_once ("../goDBasterisk.php");
	include_once ("../goDBkamailio.php");
 
	### POST or GET Variables
		$carrier_type = $_REQUEST['carrier_type'];
		
		if($carrier_type == "justgo"){
			$company		= mysqli_real_escape_string($link, $_REQUEST['company']);
			$firstname 		= mysqli_real_escape_string($link, $_REQUEST['firstname']);
			$lastname 		= mysqli_real_escape_string($link, $_REQUEST['lastname']);
			$address 		= mysqli_real_escape_string($link, $_REQUEST['address']);
			$city 		= mysqli_real_escape_string($link, $_REQUEST['city']);
			$state 		= mysqli_real_escape_string($link, $_REQUEST['state']);
			$postal 		= mysqli_real_escape_string($link, $_REQUEST['postal']);
			$country 		= mysqli_real_escape_string($link, $_REQUEST['country']);
			$timezone 		= mysqli_real_escape_string($link, $_REQUEST['timezone']);
			$phone 		= mysqli_real_escape_string($link, $_REQUEST['phone']);
			$mobilephone 		= mysqli_real_escape_string($link, $_REQUEST['mobilephone']);
			$email 		= mysqli_real_escape_string($link, $_REQUEST['email']);
		}
		
		if($carrier_type == "manual" || $carrier_type == "copy"){
			$carrier_id	= mysqli_real_escape_string($link, $_REQUEST['carrier_id']);
			$carrier_name 	= mysqli_real_escape_string($link, $_REQUEST['carrier_name']);
			$active	= $_REQUEST['active'];
			
		}
		
		if($carrier_type == "manual"){
			$carrier_description	= mysqli_real_escape_string($link, $_REQUEST['carrier_description']);
			$user_group	= $_REQUEST['user_group'];
			$authentication	= $_REQUEST['authentication'];
			
			if($authentication == "auth_ip"){
				$host = $_REQUEST['sip_server_ip'];
			}
			if($authentication == "auth_reg"){
				$host = mysqli_real_escape_string($link, $_REQUEST['reg_host']);
				$username = mysqli_real_escape_string($link, $_REQUEST['username']);
				$password = mysqli_real_escape_string($link, $_REQUEST['password']);
				$reg_port = mysqli_real_escape_string($link, $_REQUEST['reg_port']);
			}
			
			$codecs			= $_REQUEST['codecs'];
			$codecs 			= explode("&", $codecs);
			
			$dtmf			= $_REQUEST['dtmf'];
			$custom_dtmf			= mysqli_real_escape_string($link, $_REQUEST['custom_dtmf']);
			$dialprefix			= mysqli_real_escape_string($link, $_REQUEST['dialprefix']);
			$protocol			= $_REQUEST['protocol'];
			$server_ip			= $_REQUEST['manual_server_ip'];
			
			$values = "";
			if(in_array("GSM", $codecs))
				$values .= "&allow_gsm=allow_gsm";
			if(in_array("ULAW", $codecs))
				$values .= "&allow_ulaw=allow_ulaw";
			if(in_array("ALAW", $codecs))
				$values .= "&allow_alaw=allow_alaw";
			if(in_array("G729", $codecs))
				$values .= "&allow_g729=allow_g729";
				
			if($dtmf == "rfc2833")
				$values .= "&dtmf_mode=rfc2833";
			if($dtmf == "inband")
				$values .= "&dtmf_mode=inband";
			if($dtmf == "custom")
				$values .= "&dtmf_mode=custom";
			
			if($protocol != "CUSTOM"){
				$registration_string = "";
				$account_entry = "";
				$global_string = "";
				if($authentication == "auth_reg"){
					$registration_string .= "register => ".$username.":".$password."@".$host.":".$reg_port."/".$username;
				}
				
				$account_entry .= "[".$carrier_id."]\r\n";
				$account_entry .= "disallow=all\r\n";
				
				if(in_array("GSM", $codecs))
					$account_entry .= "allow=gsm\r\n";
				if(in_array("ULAW", $codecs))
					$account_entry .= "allow=ulaw\r\n";
				
				$account_entry .= "type=friend\r\n";
				
				if($dtmf == "custom"){
					$account_entry .= "dtmfmode=".$custom_dtmf."\r\n";
				}else{
					$account_entry .= "dtmfmode=".$dtmf."\r\n";
				}
				$account_entry .= "context=trunkinbound\r\n";
				$account_entry .= "qualify=yes\r\n";
				$account_entry .= "insecure=invite,port\r\n";
				$account_entry .= "nat=yes\r\n";
				$account_entry .= "host=".$host."\r\n";
				
				if($authentication == "auth_reg"){
					$account_entry .= "username=".$username."\r\n";
					$account_entry .= "secret=".$password."\r\n";
				}
				
				if(in_array("ALAW", $codecs))
					$account_entry .= "allow=alaw\r\n";
				if(in_array("G729", $codecs))
					$account_entry .= "allow=g729\r\n";
				
				$protocol_m = $protocol."/";	
				
				$dialplan_entry = "";
				$dialplan_entry .= "exten => _". $dialprefix .".,1,AGI(agi://127.0.0.1:4577/call_log)\r\n";
				$dialplan_entry .= "exten => _". $dialprefix .".,2,Dial(". $protocol_m ."\${EXTEN:10}@". $carrier_id .",,tTo)\r\n";
				$dialplan_entry .= "exten => _". $dialprefix .".,3,Hangup";
				
			}else{
				$protocol	= mysqli_real_escape_string($link, $_REQUEST['cust_protocol']);
				$registration_string	= mysqli_real_escape_string($link, $_REQUEST['registration_string']);
				$account_entry	= mysqli_real_escape_string($link, $_REQUEST['account_entry']);
				$global_string	= mysqli_real_escape_string($link, $_REQUEST['global_string']);
				$dialplan_entry	= mysqli_real_escape_string($link, $_REQUEST['dialplan_entry']);
			}
			
		}
		
		if($carrier_type == "copy"){
			$server_ip	= mysqli_real_escape_string($link, $_REQUEST['copy_server_ip']);
			$source_carrier	= mysqli_real_escape_string($link, $_REQUEST['source_carrier']);
		}

		$goUser = $_REQUEST['goUser'];
		$ip_address = $_REQUEST['hostname'];


	$defProtocol = array('SIP','Zap','IAX2','EXTERNAL');
    $defActive = array("Y","N");
		
	
		
########################################

        if($carrier_id == null && $carrier_type != "justgo") {
			$apiresults = array("result" => "Error: Set a value for Carrier ID.");
        } elseif($carrier_name == null && $carrier_type != "justgo") {
			$apiresults = array("result" => "Error: Set a value for Carrier Name.");
		} elseif(!in_array($active,$defActive) && $active != null && $carrier_type != "justgo") {
			$apiresults = array("result" => "Error: Default value for active is Y or N only.");
		} elseif(!in_array($protocol,$defProtocol) && $protocol != null && $carrier_type != "justgo") {
			$apiresults = array("result" => "Error: Default value for protocol is SIP, Zap, IAX2 or EXTERNAL only.");
		} elseif((preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $carrier_id)) && $carrier_type != "justgo"){
			$apiresults = array("result" => "Error: Special characters found in carrier_id");
		}
		
			//$groupId = go_get_groupid($goUser);

			//if (!checkIfTenant($groupId)) {
				$ul = "WHERE carrier_id ='$carrier_id'";
				$ulug = "WHERE user_group='$user_group'";
			//} else {
			//	$ul = "WHERE carrier_id ='$carrier_id' AND user_group='$groupId'";
			//	$ulug = "WHERE user_group='$user_group' AND user_group='$groupId'";
			//}
			
			if($carrier_type == "copy"){
				$query_copy = mysqli_query($link, "SELECT carrier_description, user_group, protocol, registration_string FROM vicidial_server_carriers WHERE carrier_id = '$source_carrier' LIMIT 1;");
				$fetch_copy = mysqli_fetch_array($query_copy);
				$user_group = $fetch_copy["user_group"]; 
				$ulug = "WHERE user_group = '$user_group'";
				
				$protocol = $fetch_copy["protocol"];
				$carrier_description = $fetch_copy["carrier_description"];
			}

			$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ulug ORDER BY user_group LIMIT 1;";
			$rsltv = mysqli_query($link, $query);
			$countResult = mysqli_num_rows($rsltv);

			if($countResult <= 0 && $user_group != "---ALL---") {
				$apiresults = array("result" => "Error: Invalid usergroup.");
			} else {
				$queryCheck = "SELECT carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
				$rsltv = mysqli_query($link, $queryCheck);
				$countCheck = mysqli_num_rows($rsltv);

			if($countCheck > 0) {
				$apiresults = array("result" => "Error: Carrier already exist.", "items0" => $codecs);
			} else {
//                 if ($action == "add_new_carrier")
//              {
/*
			$items = explode("&",str_replace(";","",$values));
			foreach ($items as $item)
			{
					list($var,$val) = explode("=",$item,2);
					if (strlen($val) > 0)
					{
							if($var!="reg_auth" && $var!="reg_user" && $var!="reg_pass" && $var!="reg_host" && $var!="reg_port" && $var!="ip_user" && $var!="ip_pass" && $var!="ip_host" && $var!="allow_gsm" && $var!="allow_ulaw" && $var!="allow_alaw" && $var!="allow_g729" && $var!="dtmf_mode" && $var!="customDTMF" && $var!="dialprefix" && $var!="allow_custom" && $var!="customCodecs" && $var!="customProtocol"){
								$varSQL .= "$var,";
								$valSQL .= str_replace('+',' ',$val).",";
							}

							if ($var=="carrier_id")
									$carrier_id="$val";

							if ($var=="server_ip")
									$server_ip="$val";

							if ($var=="registration_string")
									$reg_string="$val";
					}
			}
			
			//$reg_string_orig = $reg_string;
			//$reg_string = substr($reg_string,0,strpos($reg_string,":5060"));
			//$reg_string = substr($reg_string,strrpos($reg_string,"@") + 1);
			//$get_dns = dns_get_record("$reg_string");

			//foreach ($get_dns as $dns)
			//{
			//        if ($dns['type'] == "A")
			//        {
			//                $reg_ipSQL = "OR registration_string rlike '@".$dns['ip'].":'";
			//        }
			//}*/
			$reg_string = $host;
			
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
			
			$queryVSC = "INSERT INTO vicidial_server_carriers (carrier_id, carrier_name, registration_string, account_entry, carrier_description, user_group, protocol, dialplan_entry, server_ip, globals_string, active) VALUES ('$carrier_id', '$carrier_name', '$registration_string', '$account_entry', '$carrier_description', '$user_group', '$protocol', '$dialplan_entry', '$server_ip', '$global_string', '$active');";
			$resultVSC = mysqli_query($link, $queryVSC);
			
				if($resultVSC) {
				//$this->commonhelper->auditadmin('ADD',"Added New Carrier $carrier_id","INSERT INTO vicidial_server_carriers $itemSQL;");
				$queryUpdate = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
				$resultVSC = mysqli_query($link, $queryUpdate);

	### Admin logs
					$SQLdate = date("Y-m-d H:i:s");
					$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Carrier ID $carrier_id','INSERT INTO vicidial_server_carriers (carrier_id, carrier_name, registration_string, account_entry, carrier_description, user_group, protocol, dialplan_entry, server_ip, globals_string) VALUES ($carrier_id, $carrier_name, $registration_string, $account_entry, $carrier_description, $user_group, $protocol, $dialplan_entry, $server_ip, $global_string);');";
					$rsltvLog = mysqli_query($linkgo, $queryLog);

					$apiresults = array("result" => "success", "data" => $queryVSC);
				}else{
					$apiresults = array("result" => "Error in Saving: It appears something has occured, please consult the system administrator", "data" => $queryVSC);
				}
												
			} else {
					$apiresults = array("result" => "Error: Carrier  doens't exist.", "data" => $querySelect);
			}
			
			}
#######################################
		}
?>
