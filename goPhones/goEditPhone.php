<?php
   ####################################################
   #### Name: goEditCampaign.php                   ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jerico James Milo              ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("goFunctions.php");
 
    ### POST or GET Variables
    $extension = $_REQUEST['extension'];
    $server_ip = $_REQUEST['server_ip'];
    $pass =    $_REQUEST['pass'];
    $protocol = strtoupper($_REQUEST['protocol']);
    $dialplan_number = $_REQUEST['dialplan_number'];
    $voicemail_id = $_REQUEST['voicemail_id'];
    $status = $_REQUEST['status'];
    $active = strtoupper($_REQUEST['active']);
    $fullname = $_REQUEST['fullname'];
    $messages = $_REQUEST['messages'];
    $old_messages = $_REQUEST['old_messages'];
    $user_group = $_REQUEST['user_group'];
    $goUser = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
   // $values =	$_REQUEST['item'];   

    ### Default values 
        $defActive = array("Y","N");
	$defProtocol = array('SIP','Zap','IAX2','EXTERNAL');
        $defStatus = array('ACTIVE','SUSPENDED','CLOSED','PENDING','ADMIN');
#############################
//Error Checking Next
                                //$items = explode("&",str_replace(";","",$this->input->post("items")));
                                //foreach ($items as $item)
        if($extension == null) {
                $apiresults = array("result" => "Error: Set a value for Extension.");
        } else {
                if(!in_array($status,$defStatus) && $status != null) {
                        $apiresults = array("result" => "Error: Default value for status is ACTIVE, SUSPENDED, CLOSED, PENDING, ADMIN only.");
                } else {
                if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {

                if(!in_array($protocol,$defProtocol) && $protocol != null) {
                        $apiresults = array("result" => "Error: Default value for protocol is SIP, Zap, IAX2, EXTERNAL.");
                } else {

        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $server_ip)){
                $apiresults = array("result" => "Error: Special characters found in server_ip");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass)){
                $apiresults = array("result" => "Error: Special characters found in password");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $dialplan_number)){
                $apiresults = array("result" => "Error: Special characters found in dialplan_number");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_id)){
                $apiresults = array("result" => "Error: Special characters found in voicemail_id");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status)){
                $apiresults = array("result" => "Error: Special characters found in status");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $fullname)){
                $apiresults = array("result" => "Error: Special characters found in fullname");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $messages)){
                $apiresults = array("result" => "Error: Special characters found in messages");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $old_messages)){
                $apiresults = array("result" => "Error: Special characters found in old_messages");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group)){
                $apiresults = array("result" => "Error: Special characters found in user_group");
        } else {



                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "WHERE extension='$extension'";
                } else {
                        $ul = "WHERE extension='$extension' AND user_group='$groupId'";
                }

                $query = "SELECT extension,protocol,pass,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group FROM phones $ul ORDER BY extension LIMIT 1;";
                $rsltv = mysqli_query($link,$query);
                while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
					$dataextension = $fresults['extension'];
					$dataprotocol = $fresults['protocol'];
					$datapass = $fresults['pass'];
					$dataserver_ip = $fresults['server_ip'];
					$datadialplan_number = $fresults['dialplan_number'];
					$datavoicemail_id = $fresults['voicemail_id'];
					$datastatus = $fresults['status'];
					$dataactive = $fresults['active'];
					$datafullname = $fresults['fullname'];
					$datamessages = $fresults['messages'];
					$dataold_messages = $fresults['old_messages'];
					$datauser_group = $fresults['user_group'];

				}
                $countResult = mysqli_num_rows($rsltv);

                if($countResult > 0) {

		if($dataextension != null){
				/*$items = $values;
				foreach (explode("&",$items) as $item)
                                {
                                        list($var,$val) = explode("=",$item,2);
                                        if (strlen($val) > 0)
                                        {
                                                if ($var!="extension")
                                                        $itemSQL .= "$var='".str_replace('+',' ',mysql_real_escape_string($val))."', ";

                                                if ($var=="extension")
                                                        $extension="$val";

                                                if ($var=="server_ip")
                                                        $server_ip="$val";

                                                if ($var=="pass")
                                                        $passwd="$val";
                                        }
                                }
                                $itemSQL = rtrim($itemSQL,', ');
                                */
                                if($server_ip ==  null){$server_ip = $dataserver_ip;} if($pass == null) {$pass = $datapass;} if($protocol == null){$protocol = $dataprotocol;} if($dialplan_number == null){$dialplan_number = $datadialplan_number;} if($voicemail_id == null){$voicemail_id = $datavoicemail_id;} if($status == null){$status = $datastatus;} if($active == null){$active = $dataactive;} if($fullname == null){$fullname = $datafullname;} if($messages == null) {$messages = $datamessages;} if($old_messages == null){$old_messages = $dataold_messages;} if($user_group == null){ $user_group = $datauser_group;}
                            
                                $query = "UPDATE phones SET server_ip='$server_ip', pass='$pass', protocol='$protocol', dialplan_number='$dialplan_number', voicemail_id='$voicemail_id', status='$status', active='$active', fullname='$fullname', messages='$messages', old_messages='$old_messages', user_group='$user_group' WHERE extension='$extension';";
				$resultQuery = mysqli_query($link,$query);
                                //echo "UPDATE phones SET $itemSQL WHERE extension='$extension';";

                                $queryNew = "UPDATE vicidial_users SET phone_pass='$passwd' WHERE phone_login='$extension';";
				$resultNew = mysqli_query($link,$queryNew);
                               // if ($this->db->affected_rows())
                                //{
                                        $queryUpdate = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
					$resultUpdate = mysqli_query($link,$queryUpdate);
                                        //$return = "SUCCESS";
                               // }
                                 //       $return = "SUCCESS";


        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Phone $extension','UPDATE phones SET extension=$extension, server_ip=$server_ip, pass=$pass, protocol=$protocol, dialplan_number=$dialplan_number, voicemail_id=$voicemail_id, status=$status, active=$active, fullname=$fullname, messages=$messages, old_messages=$old_messages, user_group=$user_group WHERE extension=$extension');";
                                        $rsltvLog = mysqli_query($linkgo,$queryLog);



					$apiresults = array("result" => "success");
				} else {
					$apiresults = array("result" => "Error: Failed to update");
				}

				} else {
	                               $apiresults = array("result" => "Error: Phone doesn't  exist.");

				}
		}	}}
		}}}}}}}}}
		}
#############################

?>
