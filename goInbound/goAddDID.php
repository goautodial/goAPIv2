<?php
    include_once("../goFunctions.php");
	include_once("../goDBgoautodial.php");
	include_once("../goDBasterisk.php");
	### POST or GET Variables
        $did_pattern = $_REQUEST['did_pattern'];
        $did_description = $_REQUEST['did_description'];
        $active = strtoupper($_REQUEST['did_active']);
        $did_route = strtoupper($_REQUEST['did_route']);
        $user_group = $_REQUEST['user_group'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
        $record_call = "Y"; //$_REQUEST['record_call'];

	### Agent
		$user = $_REQUEST['user'];
        $user_unavailable_action = strtoupper($_REQUEST['user_unavailable_action']);

	### Ingroup
		$group_id = $_REQUEST['group_id'];

	### Phone
		$phone = $_REQUEST['phone'];
		$server_ip = $_REQUEST['server_ip'];

	### IVR
		$menu_id = $_REQUEST['menu_id'];

	### Voicemail
		$voicemail_ext = $_REQUEST['voicemail_ext'];

	### Custon Extension
		$extension = $_REQUEST['extension'];
		$exten_context = $_REQUEST['exten_context'];

    ### Default values 
    $defUUA = array('IN_GROUP','EXTEN','VOICEMAIL','PHONE','VMAIL_NO_INST');
    $defRoute = array('EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU','VMAIL_NO_INST');
    $defRecordCall = array('Y','N','Y_QUEUESTOP');
    $defActive = array("Y","N");

######################

        if($did_pattern == null) {
                $apiresults = array("result" => "Error: Set a value for DID pattern.");
		}else if($did_description == null) {
				$apiresults = array("result" => "Error: Set a value for did_description.");
		}else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $did_pattern)){
				$apiresults = array("result" => "Error: Special characters found in did_pattern");
		}else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $did_description)){
				$apiresults = array("result" => "Error: Special characters found in did_description");
		}else if(!in_array($user_unavailable_action,$defUUA) && $user_unavailable_action != null) {
				$apiresults = array("result" => "Error: Default value for user_unavailable_action is IN_GROUP','EXTEN','VOICEMAIL','PHONE', or 'VMAIL_NO_INST'.");
		}else if(!in_array($active,$defActive) && $active != null) {
				$apiresults = array("result" => "Error: Default value for active is Y or N only.");
		}else if(!in_array($did_route,$defRoute) && $did_route != null) {
				$apiresults = array("result" => "Error: Default value for did_route are EXTEN, VOICEMAIL, AGENT, PHONE, IN_GROUP, or CALLMENU  only.");
		}

/*
                if(!in_array($record_call,$defRecordCall) && $record_call != null) {
                        $apiresults = array("result" => "Error: Default value for Record Call are Y, N and Y_QUEUESTOP  only.");
                } else {

*/

		// if DID ROUTE == AGENT
		if($did_route == "AGENT" && $user == null){
				$apiresults = array("result" => "Error: Set Value for user"); 
		}else if($did_route == "AGENT" && $user_unavailable_action == null){
				$apiresults = array("result" => "Error: Set Value for user_unavailable_action");
		}
		

		// if DID ROUTE == IN GROUP
		if($did_route == "IN_GROUP" && $group_id == null){
				$apiresults = array("result" => "Error: Set Value for group_id");
		}

		// if DID ROUTE == PHONE
		if($did_route == "PHONE" && $phone == null){
				$apiresults = array("result" => "Error: Set Value for phone");
		}else if($did_route == "PHONE" && $server_ip == null){
				$apiresults = array("result" => "Error: Set Value for server_ip");
		}else if($phone != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone)){
                $apiresults = array("result" => "Error: Special characters found in phone");
		}else if($server_ip != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $server_ip)){
                $apiresults = array("result" => "Error: Special characters found in server_ip");
		}


		// if DID ROUTE == CALLMENU
		if($did_route == "CALLMENU" && $menu_id == null){
				$apiresults = array("result" => "Error: Set Value for menu_id");
		}else if($menu_id  != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_id)){
                $apiresults = array("result" => "Error: Special characters found in menu_id");
		}

	
		// if DID ROUTE == VOICEMAIL
		if($did_route == "VOICEMAIL" && $voicemail_ext == null){
				$apiresults = array("result" => "Error: Set Value for voicemail_ext");
		}else if($voicemail_ext != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_ext)){
                $apiresults = array("result" => "Error: Special characters found in voicemail_ext");
		}
	
	
		// IF DID ROUTE == EXTEN
		if($did_route == "EXTEN" && $extension == null){
				$apiresults = array("result" => "Error: Set Value for extension");
		}else if($did_route == "EXTEN" && $exten_context == null){
				$apiresults = array("result" => "Error: Set Value for exten_context");		
		}else if($extension != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $extension)){
                $apiresults = array("result" => "Error: Special characters found in extension");
		}else if($exten_context != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $exten_context)){
                $apiresults = array("result" => "Error: Special characters found in exten_context");
		}
			
		if($group_id != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_id)){
                $apiresults = array("result" => "Error: Special characters found in group_id");
		}

		$groupId = get_inner_groupid($goUser, $link);
		
		if (!inner_checkIfTenant($groupId, $linkgo)) {
				$ul = "WHERE user_group='$user_group'";
		} else {
				$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
		}
		
		
		$queryUG = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		
		$rsltvUG = mysqli_query($link, $queryUG);
		
		$countUG = mysqli_num_rows($rsltvUG);
		

		if($countUG > 0){
			
				
				$stmtdf = "SELECT did_pattern from vicidial_inbound_dids where did_pattern='$did_pattern';";
				$querydf = mysqli_query($link, $stmtdf);
				$rowdf = mysqli_num_rows($querydf);
			
				
				if ($rowdf > 0) {
						$apiresults = array("result" => "<br>DID NOT ADDED - DID already exist.\n");
						
				} else {
						
						if($did_route == "AGENT"){
						
								$queryAgent = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, user, user_unavailable_action, user_route_settings_ingroup)
												values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group', '$user', '$user_unavailable_action', '$user_route_settings_ingroup');";
								$queryAgentResult = mysqli_query($link, $queryAgent);
						
						//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, user, user_unavailable_action, user_route_settings_ingroup) values('0000', 'Test', 'AGENT', 'N', 'ADMIN', '', 'VOICEMAIL', 'AGENTDIRECT');
						
						}
						
						if($did_route == "PHONE"){
						
								$queryPhone = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, phone, server_ip)
												values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group', '$phone', '$server_ip');";
								
								$queryPhoneResult = mysqli_query($link, $queryPhone);
						//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, phone, server_ip) values('00000', 'Sample', 'PHONE', 'N', 'ADMIN', '', '');
						
						}
						
							
						if($did_route == "CALLMENU"){
						
								$queryCallmenu = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, menu_id)
												values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group', '$menu_id');";
										$queryCMResult = mysqli_query($link, $queryCallmenu);
						//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, menu_id) values('000000', 'test call menu', 'CALLMENU', 'N', 'ADMIN', '0000');
						
						}
						
						
						if($did_route == "VOICEMAIL"){
						
								$queryVM = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, voicemail_ext)
												values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group', '$voicemail_ext');";
										$queryVMResult = mysqli_query($link, $queryVM);
						//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, voicemail_ext) values('0000000', 'vm', 'VOICEMAIL', 'N', 'ADMIN', '0000000');
						
						}
						
						
						if($did_route == "EXTEN"){
								$queryExten = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, extension, exten_context)
												values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group', '$extension', '$exten_context');";
										$queryExtenResult = mysqli_query($link, $queryExten);
						//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, extension, exten_context) values('000000000', 'ce', 'EXTEN', 'N', 'ADMIN', '9998811112', 'default');
						}
						
						if($did_route == "IN_GROUP"){
						
										$queryIG = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, group_id)
														values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group','$group_id');";
										$queryIGResult = mysqli_query($link, $queryIG);
						//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, extension, exten_context) values('000000000', 'ce', 'EXTEN', 'N', 'ADMIN', '9998811112', 'default');
						}
						
						$queryCheck = "SELECT did_pattern from vicidial_inbound_dids where did_pattern='$did_pattern';";
								$result = mysqli_query($link, $queryCheck);
								$result = mysqli_num_rows($result);
								
								if ($result > 0) {

						### Admin logs
								$SQLdate = date("Y-m-d H:i:s");
								$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New DID $did_pattern','INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_active, did_route, user_group, user,user_unavailable_action,group_id,phone, server_ip,voicemail_ext,record_call) VALUES ($did_pattern, $did_description, $did_active, $did_route, $user_group, $user,$user_unavailable_action,$group_id,$phone, $server_ip,$voicemail_ext,$record_call)');";
								$rsltvLog = mysqli_query($linkgo, $queryLog);
				
								$apiresults = array("result" => "success");
						
								} else {
								
								$apiresults = array("result" => "DID NOT ADDED, Check your details");
								
								}
						
				}

		} else {
				$apiresults = array("result" => "Error: Invalid User Group");
		}

						
 ##### get usergroup #########
function get_inner_groupid($goUser, $link){
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
function inner_checkIfTenant($groupId, $linkgo){
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
