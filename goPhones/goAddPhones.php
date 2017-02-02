<?php
   ####################################################
   #### Name: goAddList.php                        ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    include_once("../goDBgoautodial.php");
    include_once("../goDBasterisk.php");
    include_once ("../goFunctions.php");
 
	### POST or GET Variables
    $extension = $_REQUEST['extension'];
    $server_ip = $_REQUEST['server_ip'];
    $pass =    $_REQUEST['pass'];
    $protocol = $_REQUEST['protocol'];
   // $server_ip = $_REQUEST['server_ip'];
    $dialplan_number = $_REQUEST['dialplan_number'];
    $voicemail_id = $_REQUEST['voicemail_id'];
    $status = $_REQUEST['status'];
    $active = $_REQUEST['active'];
    $fullname = $_REQUEST['fullname'];
    $messages = $_REQUEST['messages'];
    $old_messages = $_REQUEST['old_messages'];
    $user_group = $_REQUEST['user_group'];
    $goUser = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
   //     $values = $_REQUEST['item'];
//extension, server_ip, pass, protocol, dialplan_number, voicemail_id, status, active, fullname, messages, old_messages, user_group
  $defStatus = array('ACTIVE','SUSPENDED','CLOSED','PENDING,ADMIN');
  $defProtocol = array('SIP','Zap','IAX2','EXTERNAL');
  $defActive = array("Y","N");
###################


        if($extension == null) {
                $apiresults = array("result" => "Error: Set a value for Extension.");
        } else  {

                if(!in_array($status,$defStatus)) {
                        $apiresults = array("result" => "Error: Default value for status is ACTIVE, SUSPENDED, CLOSED, PENDING, ADMIN only.");
                } else {
                if(!in_array($active,$defActive)) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {

                if(!in_array($protocol,$defProtocol)) {
                        $apiresults = array("result" => "Error: Default value for protocol is SIP, Zap, IAX2, EXTERNAL.");
                } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $extension)){
                $apiresults = array("result" => "Error: Special characters found in extension");
        } else {

        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $server_ip) || $server_ip == null){
                $apiresults = array("result" => "Error: Special characters found in server_ip or must not be null");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass) || $pass == null){
                $apiresults = array("result" => "Error: Special characters found in password and must not be null");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $dialplan_number) || $dialplan_number == null){
                $apiresults = array("result" => "Error: Special characters found in dialplan_number and must not be null");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_id) || $voicemail_id == null){
                $apiresults = array("result" => "Error: Special characters found in voicemail_id and must not be null");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status) || $status == null){
                $apiresults = array("result" => "Error: Special characters found in status and must not be null");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $fullname) || $fullname == null){
                $apiresults = array("result" => "Error: Special characters found in fullname and must not be null");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $messages) || $messages == null){
                $apiresults = array("result" => "Error: Special characters found in messages and must not be null");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $old_messages) || $old_messages == null){
                $apiresults = array("result" => "Error: Special characters found in old_messages and must not be null");
        } else {
        /*if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group) || $user_group == null){
                $apiresults = array("result" => "Error: Special characters found in user_group and must not be null");
        } else {
	*/

		$groupId = inner_go_get_groupid($goUser);

                if (!inner_checkIfTenant($groupId)) {
                        $ul = "WHERE extension='$extension'";
                } else {
                        $ul = "WHERE extension='$extension' AND user_group='$groupId'";
                }

                $query = "SELECT extension,protocol,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group FROM phones $ul ORDER BY extension LIMIT 1;";
                $rsltv = mysqli_query($link,$query);
                $countResult = mysqli_num_rows($rsltv);

                if($countResult <= 0) {
                              /*  $items = explode("&",str_replace(";","",$values));
                                foreach ($items as $item)
                                {
                                        list($var,$val) = explode("=",$item,2);
                                        if (strlen($val) > 0)
                                        {
                                                $varSQL .= "$var,";
                                                $valSQL .= "'".str_replace('+',' ',mysql_real_escape_string($val))."',";

                                                if ($var=="extension")
                                                        $extension="$val";

                                                if ($var=="server_ip")
                                                        $server_ip="$val";
                                        }
                                }
                                $varSQL = rtrim($varSQL,",");
                                $valSQL = rtrim($valSQL,",");
                                $itemSQL = "($varSQL) VALUES ($valSQL)";*/
                                $query = "INSERT INTO phones (extension, server_ip, pass, protocol, dialplan_number, voicemail_id, status, active, fullname, messages, old_messages, user_group)
										VALUES ('$extension', '$server_ip', '$pass', '$protocol', '$dialplan_number', '$voicemail_id', '$status', '$active', '$fullname', '$messages', '$old_messages', '$user_group');";
				$resultQuery = mysqli_query($link,$query);

                                //if ($this->db->affected_rows())
                                //{
                                  //      $this->commonhelper->auditadmin("ADD","Added Phone $extension");
                                        $queryNew = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
					$resultNew = mysqli_query($link,$queryNew);

        ### Admin logs
//                                        $SQLdate = date("Y-m-d H:i:s");
//                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query)
//														values('$goUser','$ip_address','$SQLdate','ADD','Added New Phone $extension','INSERT INTO phones (extension, server_ip, pass, protocol, dialplan_number, voicemail_id, status, active, fullname, messages, old_messages, user_group) VALUES ($extension, $server_ip, $pass, $protocol, $dialplan_number, $voicemail_id, $status, $active, $fullname, $messages, $old_messages, $user_group)');";
//                                        $rsltvLog = mysqli_query($linkgo,$queryLog);
					$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added New Phone: $extension", $log_user, $query);



					$apiresults = array("result" => "success");
                                    //    $return = "SUCCESS";
                                //}
                        } else {
				$apiresults = array("result" => "Error: Phone already exist.");
			}//}
		}}}}}}}}}}}}

	}



###################
##### get usergroup #########
    function inner_go_get_groupid($goUser){
        $query_userv = "SELECT user_group FROM vicidial_users WHERE user='$goUser'";
        $rsltv = mysqli_query($link, $query_userv);
        $check_resultv = mysqli_num_rows($rsltv);
    
        if ($check_resultv > 0) {
            $rowc=mysqli_fetch_assoc($rsltv);
            $goUser_group = $rowc["user_group"];
            return $goUser_group;
        }
        
    }
    
    ##### checkiftenant ######
    function inner_checkIfTenant($groupId){
        $query_tenant = "SELECT * FROM go_multi_tenant WHERE tenant_id='$groupId'";
        $rslt_tenant = mysqli_query($linkgo,$query_tenant);
        $check_result_tenant = mysqli_num_rows($rslt_tenant);
    
        if ($check_result_tenant > 0) {
            return true;
        } else {
            return false;
        }
    }
?>
