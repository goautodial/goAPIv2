<?php
   ####################################################
   #### Name: goEditCarrier.php                    ####
   #### Description: API to edit specific carrier  ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
    $carrier_id = $_REQUEST['carrier_id'];
    $carrier_name = $_REQUEST['carrier_name'];
    $carrier_description = $_REQUEST['carrier_description'];
    $protocol = $_REQUEST['protocol'];
    $server_ip = $_REQUEST['server_ip'];
  //  $registration_string = $_REQUEST['registration_string'];
  //  $account_entry = $_REQUEST['account_entry'];
  //  $global_string = $_REQUEST['global_string'];
    $active = strtoupper($_REQUEST['active']);
    $goUser = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
    //$values = $_REQUEST['item'];
   
    ### Default values 
    $defActive = array("Y","N");
    $defDialMethod = array("MANUAL","RATIO","ADAPT_HARD_LIMIT","ADAPT_TAPERED","ADAPT_AVERAGE","INBOUND_MAN"); 
    $defProtocol = array('SIP','Zap','IAX2','EXTERNAL');

#############################
//To be continue
        if($carrier_id == null) {
                $apiresults = array("result" => "Error: Set a value for Carrier ID.");
        } else   {
                if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {

                if(!in_array($protocol,$defProtocol) && $protocol != null) {
                        $apiresults = array("result" => "Error: Default value for protocol is SIP, Zap, IAX2 or EXTERNAL only");
                } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $registration_string)){
                $apiresults = array("result" => "Error: Special characters found in registration_string");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $carrier_name)){
                $apiresults = array("result" => "Error: Special characters found in carrier_name");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $carrier_description)){
                $apiresults = array("result" => "Error: Special characters found in carrier_description");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $global_string)){
                $apiresults = array("result" => "Error: Special characters found in global_string");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $account_entry)){
                $apiresults = array("result" => "Error: Special characters found in account_entry");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $server_ip)){
                $apiresults = array("result" => "Error: Special characters found in server_ip");
        } else {



                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "WHERE carrier_id ='$carrier_id'";
                } else {
                        $ul = "WHERE carrier_id ='$carrier_id' AND user_group='$groupId'";
                }

                $query = "SELECT carrier_id,carrier_name,server_ip,protocol,registration_string,active,user_group FROM vicidial_server_carriers $ul ORDER BY carrier_id LIMIT 3;";
                $rsltv = mysqli_query($link, $query);
                while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
					$datacarrier_name = $fresults['carrier_name'];
       					$datacarrier_description = $fresults['carrier_description'];
       					$dataprotocol = $fresults['protocol'];
       					$dataserver_ip = $fresults['server_ip'];
                			$dataactive = $fresults['active'];
				}
                
                $countResult = mysqli_num_rows($rsltv);

                if($countResult > 0) {

                                $items = explode("&",str_replace(";","",$values));
                                foreach ($items as $item)
                                {
                                        list($var,$val) = explode("=",$item,2);
                                        //if (strlen($val) > 0)
                                        //{
                                                if ($var!="carrier_id" && $var!="reg_auth" && $var!="reg_user"
                                                        && $var!="reg_pass" && $var!="reg_host" && $var!="reg_port"
                                                        && $var!="ip_user" && $var!="ip_pass" && $var!="ip_host"
                                                        && $var!="allow_gsm" && $var!="allow_ulaw" && $var!="allow_alaw"
                                                        && $var!="allow_g729" && $var!="dtmf_mode" && $var!="customDTMF"
                                                        && $var!="dialprefix" && $var!="customProtocol")
                                                        $itemSQL .= "$var='".str_replace('+',' ',mysqli_real_escape_string($val))."', ";

                                                if ($var=="carrier_id")
                                                        $carrier_id="$val";

                                                if ($var=="server_ip")
                                                        $server_ip="$val";

                                                if ($var=="registration_string")
                                                        $reg_string="$val";
						if ($var=="user_group")
							$reg_ug="$val";
                                        //}
                                }
					
                                $reg_string = substr($reg_string,0,strpos($reg_string,":5060"));
                                $reg_string = substr($reg_string,strrpos($reg_string,"@") + 1);
/*no effect
                                if ($reg_string=="dal.justgovoip.com" || $reg_string=="208.43.27.84")
                                {
                                        $query = $this->db->query("SELECT * FROM vicidial_server_carriers WHERE carrier_id='$carrier_id' AND server_ip='$server_ip';");
                                        $isExist = $query->num_rows();
                                }
*/
                                //if (!$isExist)
                                //{
                                        $itemSQL = rtrim($itemSQL,', ');
                                        if($carrier_name == null){$carrier_name = $datacarrier_name;}
                                        if($carrier_description == null){$carrier_description = $datacarrier_description;}
                                        if($protocol == null){$protocol = $dataprotocol;}
                                        if($server_ip == null) {$server_ip = $dataserver_ip;}
                                        if($active == null){$active = $dataactive;}
                                        $query = "UPDATE vicidial_server_carriers SET  carrier_name = '$carrier_name', carrier_description = '$carrier_description', protocol = '$protocol', server_ip = '$server_ip', active = '$active' WHERE carrier_id='$carrier_id';";
					$result = mysqli_query($link, $query);
                                        //var_dump("UPDATE vicidial_server_carriers SET $itemSQL WHERE carrier_id='$carrier_id';");
                                        //echo "UPDATE phones SET $itemSQL WHERE extension='$extension';";

                                                $queryNew = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
						$resultNew = mysqli_query($link, $queryNew);

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Carrier ID $carrier_id','UPDATE vicidial_server_carriers SET carrier_id=$carrier_id, carrier_name=$carrier_name, carrier_description=$carrier_description, protocol=$protocol, server_ip=$server_ip,  registration_string=$registration_string, account_entry=$account_entry, global_string=$global_string, dialplan_entry=$dialplan_entry, active=$active  WHERE carrier_id=$carrier_id;');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


						$apiresults = array("result" => "success");
                                //
                                //      $return = "ERROR: Only one GoAutoDial-JustGoVoIP is allowed per server ip";
                                //}
				} else {
					$apiresults = array("result" => "Error: Carrier doesn't exist.");
					}
				}
				}
                        }

}}}}}}


#############################
?>
