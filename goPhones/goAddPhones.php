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
    $extension = $_REQUEST['extension'];
    $server_ip = $_REQUEST['server_ip'];
    $pass =    $_REQUEST['pass'];
    $protocol = $_REQUEST['protocol'];
    $dialplan_number = $_REQUEST['dialplan_number'];
    $voicemail_id = $_REQUEST['voicemail_id'];
    $status = $_REQUEST['status'];
    $active = $_REQUEST['active'];
    $fullname = $_REQUEST['fullname'];
    $messages = $_REQUEST['messages'];
    $old_messages = $_REQUEST['old_messages'];
    $user_group = $_REQUEST['user_group'];
    $ip_address = $_REQUEST['hostname'];
	$gmt = $_REQUEST['gmt'];
	if(isset($_REQUEST['seats']))
        $seats = mysqli_real_escape_string($link, $_REQUEST['seats']);
	else
		$seats = 1;
	
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user);
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
			
		$groupId = go_get_groupid($session_user);
		$error_count = 0;
		
		for($i=0;$i < $seats;$i++){
			$a = 1;
			while($a >= 1){
				if (!checkIfTenant($groupId)) {
					$ul = "WHERE extension='$extension'";
				} else {
					$ul = "WHERE extension='$extension' AND user_group='$groupId'";
				}
				$query_check = "SELECT extension,protocol,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group FROM phones $ul ORDER BY extension LIMIT 1;";
				$rsltv = mysqli_query($link,$query_check);
				$countResult = mysqli_num_rows($rsltv);
				
				if($countResult <= 0) {
					$a = 0;
					$query = "INSERT INTO `phones` (`extension`,  `dialplan_number`,  `voicemail_id`,  `phone_ip`,  `computer_ip`,  `server_ip`,  `login`,  `pass`,  `status`,  `active`,  `phone_type`,  `fullname`,  `company`,  `picture`,  `protocol`,  `local_gmt`,  `outbound_cid`,  `template_id`,    `user_group`,   `messages`,  `old_messages`) VALUES ('$extension',  '9999$extension',  '$extension',  '',  '', '$server_ip',  '$extension',  '$pass',  '$status',  '$active',  '',  '$fullname',  '$user_group',  '',  '$protocol',  '$gmt',  '0000000000',  '--NONE--', '$user_group', '$messages',  '$old_messages');";
					$resultQuery = mysqli_query($link,$query);
					
					### ADDING IN KAMAILIO DB
					
					$queryPassHash = "SELECT pass_hash_enabled from system_settings";
					$resultQueryPassHash = mysqli_query($link, $queryPassHash);
					$rPassHash = mysqli_fetch_array($resultQueryPassHash, MYSQLI_ASSOC);
					$pass_hash_enabled = $rPassHash['pass_hash_enabled'];
					
					$pass_hash = '';
					if ($pass_hash_enabled > 0) {
						$cwd = $_SERVER['DOCUMENT_ROOT'];
						$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$pass");
						$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
						//$pass = '';
					}
					
					$queryg = "SELECT value FROM settings WHERE setting='GO_agent_wss_sip';";
					$rsltg = mysqli_query($linkgo, $queryg);
					$rowg = mysqli_fetch_array($rsltg, MYSQLI_ASSOC);
					$realm = $rowg['value'];
					
					$kamha1fields = '';
					$kamha1values = '';
					if ($pass_hash_enabled > 0) {
						$ha1 = md5("{$extension}:{$realm}:{$pass}");
						$ha1b = md5("{$extension}@{$realm}:{$realm}:{$pass}");
						$kamha1fields = ", ha1, ha1b";
						$kamha1values = ", '{$ha1}', '{$ha1b}'";
						$pass = '';
					}
					
					$queryd = "SELECT value FROM settings WHERE setting='GO_agent_domain';";
					$rsltd = mysqli_query($linkgo, $queryd);
					$rowd = mysqli_fetch_array($rsltd, MYSQLI_ASSOC);
					$domain = (!is_null($rowd['value']) || $rowd['value'] !== '') ? $rowd['value'] : 'goautodial.com';
					
					$kamailioq = "INSERT INTO subscriber (username, domain, password{$kamha1fields}) VALUES ('$extension','$domain','$pass'{$kamha1values});";
					$resultkam = mysqli_query($linkgokam, $kamailioq);
					
					$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added New Phone: $extension", $log_user, $query);
					//$return_query[] = $query_check;
				} else {
					$error_count = $error_count + 1;
					//$apiresults = array("result" => "Error: Phone already exist.");
				}
				$extension = $extension + $a;
			}
		}
		
		$queryNew = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
		if($resultQuery){
			$apiresults = array("result" => "success", "errors" => $error_count);
		}
		
		
	}}}}}}}}}}}}
	}
