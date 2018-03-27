<?php
 /**
 * @file 		goAddCarrier.php
 * @brief 		API for Carriers
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra <jeremiah@goautodial.com>
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

	### POST or GET Variables
		$carrier_type = $astDB->escape($_REQUEST['carrier_type']);
		
		if($carrier_type == "justgo"){
			$company		= $astDB->escape($_REQUEST['company']);
			$firstname 		= $astDB->escape($_REQUEST['firstname']);
			$lastname 		= $astDB->escape($_REQUEST['lastname']);
			$address 		= $astDB->escape($_REQUEST['address']);
			$city 		= $astDB->escape($_REQUEST['city']);
			$state 		= $astDB->escape($_REQUEST['state']);
			$postal 		= $astDB->escape($_REQUEST['postal']);
			$country 		= $astDB->escape($_REQUEST['country']);
			$timezone 		= $astDB->escape($_REQUEST['timezone']);
			$phone 		= $astDB->escape($_REQUEST['phone']);
			$mobilephone 		= $astDB->escape($_REQUEST['mobilephone']);
			$email 		= $astDB->escape($_REQUEST['email']);
		}
		
		if($carrier_type == "manual" || $carrier_type == "copy"){
			$carrier_id	= $astDB->escape($_REQUEST['carrier_id']);
			$carrier_name 	= $astDB->escape($_REQUEST['carrier_name']);
			$active	= $_REQUEST['active'];
		}
		
		if($carrier_type == "manual"){
			$carrier_description	= $astDB->escape($_REQUEST['carrier_description']);
			$user_group	= $_REQUEST['user_group'];
			$authentication	= $_REQUEST['authentication'];
			
			if($authentication == "auth_ip"){
				$host = $_REQUEST['sip_server_ip'];
			}
			if($authentication == "auth_reg"){
				$host = $astDB->escape($_REQUEST['reg_host']);
				$username = $astDB->escape($_REQUEST['username']);
				$password = $astDB->escape($_REQUEST['password']);
				$reg_port = $astDB->escape($_REQUEST['reg_port']);
			}
			
			$codecs			= $_REQUEST['codecs'];
			$codecs 			= explode("&", $codecs);
			
			$dtmf			= $_REQUEST['dtmf'];
			$custom_dtmf			= $astDB->escape($_REQUEST['custom_dtmf']);
			$dialprefix			= $astDB->escape($_REQUEST['dialprefix']);
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
				$account_entry .= "nat=force_rport,comedia\r\n";
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
				$protocol	= $astDB->escape($_REQUEST['cust_protocol']);
				$registration_string	= $astDB->escape($_REQUEST['registration_string']);
				$account_entry	= $astDB->escape($_REQUEST['account_entry']);
				$global_string	= $astDB->escape($_REQUEST['global_string']);
				$dialplan_entry	= $astDB->escape($_REQUEST['dialplan_entry']);
			}
			
		}
		
		if($carrier_type == "copy"){
			$server_ip	= $_REQUEST['copy_server_ip'];
			$source_carrier	= $astDB->escape($_REQUEST['source_carrier']);
		}

		$goUser = $_REQUEST['goUser'];
		$ip_address = $_REQUEST['hostname'];
		
		$log_user = $astDB->escape($_REQUEST['log_user']);
		$log_group = $astDB->escape($_REQUEST['log_group']);


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
				//$query_copy = mysqli_query($link, "SELECT * FROM vicidial_server_carriers WHERE carrier_id = '$source_carrier' LIMIT 1;");
				$astDB->where('carrier_id', $source_carrier);
				$fetch_copy = $astDB->getOne('vicidial_server_carriers');
				$user_group = $fetch_copy["user_group"]; 
				$ulug = "WHERE user_group = '$user_group'";
				
				// fetch credentials of source carrier
				$protocol = $fetch_copy["protocol"];
				$carrier_description = $fetch_copy["carrier_description"];
				$registration_string = $fetch_copy["registration_string"];
				$account_entry_to_be_filtered = $fetch_copy["account_entry"];
				//$account_entry = str_replace('['.$source_carrier.']','['.$carrier_id.']',$account_entry_to_be_filtered);
				$account_entry = $account_entry_to_be_filtered;
				$global_string = $fetch_copy["globals_string"];
				$dialplan_entry_to_be_filtered = $fetch_copy["dialplan_entry"];
				$dialplan_entry = str_replace('@'.$source_carrier,'@'.$carrier_id,$dialplan_entry_to_be_filtered);
			}

			$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ulug ORDER BY user_group LIMIT 1;";
			$rsltv = $astDB->rawQuery($query);
			$countResult = $astDB->getRowCount();

			if($countResult <= 0 && $user_group != "---ALL---") {
				$apiresults = array("result" => "Error: Invalid usergroup.");
			} else {
				$queryCheck = "SELECT carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
				$rsltv = $astDB->rawQuery($queryCheck);
				$countCheck = $astDB->getRowCount();

			if($countCheck > 0) {
				$apiresults = array("result" => "Error: Carrier already exist.");
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
			/* sir dems deactivate
			if($carrier_type == "manual"){
				$reg_string = $host;
				
				if ($reg_string=="208.43.27.84")
				{
					$reg_string = "dal.justgovoip.com";
					$reg_ipSQL = "OR registration_string rlike '@208.43.27.84:'";
				}
				
				$additional_sql = "registration_string rlike '@$reg_string:' $reg_ipSQL AND ";
			}else{
				$additional_sql = "";
			}
			
				$querySelect = "select carrier_id from vicidial_server_carriers where $additional_sql server_ip='$server_ip';";
				$resultSelect = mysqli_query($link, $querySelect);
				$isExist = mysqli_num_rows($resultSelect);
			*/	
			if (!$isExist || $carrier_type == "copy")
			{
					/*if ($reg_string=="dal.justgovoip.com" || $reg_string=="208.43.27.84")
					{
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
					}*/
					$varSQL = rtrim($varSQL,",");
					$valSQL = rtrim($valSQL,",");
					$itemSQL = "($varSQL) VALUES ($valSQL)";
			
			$queryVSC = "INSERT INTO vicidial_server_carriers (carrier_id, carrier_name, registration_string, account_entry, carrier_description, user_group, protocol, dialplan_entry, server_ip, globals_string, active) VALUES ('$carrier_id', '$carrier_name', '$registration_string', '$account_entry', '$carrier_description', '$user_group', '$protocol', '$dialplan_entry', '$server_ip', '$global_string', '$active');";
			$resultVSC = $astDB->rawQuery($queryVSC);
			
				if($resultVSC) {
				//$this->commonhelper->auditadmin('ADD',"Added New Carrier $carrier_id","INSERT INTO vicidial_server_carriers $itemSQL;");
				$queryUpdate = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
				$resultVSC = $astDB->rawQuery($queryUpdate);

	### Admin logs
					//$SQLdate = date("Y-m-d H:i:s");
					//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Carrier ID $carrier_id','INSERT INTO vicidial_server_carriers (carrier_id, carrier_name, registration_string, account_entry, carrier_description, user_group, protocol, dialplan_entry, server_ip, globals_string) VALUES ($carrier_id, $carrier_name, $registration_string, $account_entry, $carrier_description, $user_group, $protocol, $dialplan_entry, $server_ip, $global_string);');";
					//$rsltvLog = mysqli_query($linkgo, $queryLog);
					$log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added a New Carrier: $carrier_id", $log_group, $queryVSC);

					$apiresults = array("result" => "success", "data" => $queryUpdate);
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
