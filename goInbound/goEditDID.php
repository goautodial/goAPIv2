<?php
   ####################################################
   #### Name: goEditCampaign.php                   ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jerico James Milo              ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
    	// $values = $_REQUEST['items'];
        $did_pattern = $_REQUEST['did_pattern'];
        $did_description = $_REQUEST['did_description'];
        $active = strtoupper($_REQUEST['did_active']);
        $did_route = strtoupper($_REQUEST['did_route']);
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
		$filter_clean_cid_number = mysqli_real_escape_string($link, $_REQUEST['filter_clean_cid_number']);

        ### Agent
        $user = $_REQUEST['user'];
        $user_unavailable_action = strtoupper($_REQUEST['user_unavailable_action']);
		$user_route_settings_ingroup = $_REQUEST['user_route_settings_ingroup'];
		if($user != NULL){
			$agent_sql = ", user = '$user', user_unavailable_action = '$user_unavailable_action', user_route_settings_ingroup = '$user_route_settings_ingroup' ";	
		}else{
			$agent_sql = "";	
		}
		
        ### Ingroup
        $group_id = $_REQUEST['group_id'];
		
		if($group_id != NULL){
			$group_id_sql = ", group_id = '$group_id' ";	
		}else{
			$group_id_sql = "";	
		}
		
        ### Phone
        $phone = $_REQUEST['phone'];
        $server_ip = $_REQUEST['server_ip'];
		
		if($phone != NULL){
			$phone_sql = ", phone = '$phone', server_ip = '$server_ip' ";
		}else{
			$phone_sql = "";	
		}
		
        ### IVR
        $menu_id = $_REQUEST['menu_id'];
		
		if($menu_id != NULL){
			$menu_id_sql = ", menu_id = '$menu_id' ";
		}else{
			$menu_id_sql = "";	
		}
		
        ### Voicemail
        $voicemail_ext = $_REQUEST['voicemail_ext'];
		
		if($voicemail_ext != NULL){
			$voicemail_ext_sql = ", voicemail_ext = '$voicemail_ext' ";
		}else{
			$voicemail_ext_sql = "";	
		}
		
        ### Custon Extension
        $extension = $_REQUEST['extension'];
        $exten_context = $_REQUEST['exten_context'];
		
		if($extension != NULL){
			$extension_sql = ", extension = '$extension', exten_context = '$exten_context' ";
		}else{
			$extension_sql = "";	
		}


        $did_id = $_REQUEST['did_id'];
   
    ### Default values 
    $defUUA = array('IN_GROUP','EXTEN','VOICEMAIL','PHONE','VMAIL_NO_INST');
    $defRoute = array('EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU','VMAIL_NO_INST');
    $defRecordCall = array('Y','N','Y_QUEUESTOP');
    $defActive = array("Y","N");

        if($did_id == null) {
                $apiresults = array("result" => "Error: Set a value for DID ID.");
        } else {

                  $stmtdf="SELECT did_id, did_pattern from vicidial_inbound_dids where did_id='$did_id';";
                  $querydf = mysqli_query($link, $stmtdf);
                  $rowdf = mysqli_num_rows($querydf);

                  if ($rowdf <= 0) {
                        $apiresults = array("result" => "DID not found.\n");
                  } else {

                                while($fresults = mysqli_fetch_array($querydf, MYSQLI_ASSOC)){
                                        $dataID = $fresults['did_id'];
                                        $dataPattern = $fresults['did_pattern'];
                                        
                                }


                  $queryCheck="SELECT did_pattern from vicidial_inbound_dids where did_pattern='$did_pattern' AND did_id !='$dataID';";
                  $queryr = mysqli_query($link, $queryCheck);
                  $rowr = mysqli_num_rows($queryr);
		  

                  if ($rowr > 0) {
                        $apiresults = array("result" => "Duplicate did_pattern, It must be unique!\n");
                  } else {



        if($did_pattern != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $did_pattern)){
                $apiresults = array("result" => "Error: Special characters found in did_pattern");
        } else {
        if($did_description != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $did_description)){
                $apiresults = array("result" => "Error: Special characters found in did_description");
        } else {
		if(!in_array($user_unavailable_action,$defUUA) && $user_unavailable_action != null) {
				$apiresults = array("result" => "Error: Default value for user_unavailable_action is IN_GROUP','EXTEN','VOICEMAIL','PHONE', or 'VMAIL_NO_INST'.");
		} else {
		if(!in_array($active,$defActive) && $active != null) {
				$apiresults = array("result" => "Error: Default value for active is Y or N only.");
		} else {
		if(!in_array($did_route,$defRoute) && $did_route != null) {
				$apiresults = array("result" => "Error: Default value for did_route are EXTEN, VOICEMAIL, AGENT, PHONE, IN_GROUP, or CALLMENU  only.");
		} else {
		if(!in_array($record_call,$defRecordCall) && $record_call != null) {
				$apiresults = array("result" => "Error: Default value for Record Call are Y, N and Y_QUEUESTOP  only.");
		} else {

        if($group_id != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $group_id)){
                $apiresults = array("result" => "Error: Special characters found in group_id");
        } else {
        if($phone != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone)){
                $apiresults = array("result" => "Error: Special characters found in phone");
        } else {
        if($server_ip != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $server_ip)){
                $apiresults = array("result" => "Error: Special characters found in server_ip");
        } else {
        if($menu_id  != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_id)){
                $apiresults = array("result" => "Error: Special characters found in menu_id");
        } else {
        if($voicemail_ext != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_ext)){
                $apiresults = array("result" => "Error: Special characters found in voicemail_ext");
        } else {

        if($extension != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $extension)){
                $apiresults = array("result" => "Error: Special characters found in extension");
        } else {
        if($exten_context != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $exten_context)){
                $apiresults = array("result" => "Error: Special characters found in exten_context");
        } else {

				if($dataID != NULL){
			    if($did_pattern == null){ $did_pattern = $dataPattern;}
			    if($did_description == null) { $did_description = $datadid_description;} else {$did_description = $did_description;}
			    if($did_active == null) {$did_active = $datadid_active;} else {$did_active = $did_active;}
			    if($did_route == null) {$did_route = $datadid_route;} else { $did_route = $did_route;}
                                $query = "UPDATE vicidial_inbound_dids
								SET did_pattern = '$did_pattern', did_description = '$did_description', did_active = '$active',
								did_route = '$did_route', filter_clean_cid_number = '$filter_clean_cid_number' $agent_sql $group_id_sql $phone_sql
								,user_route_settings_ingroup='$user_route_settings_ingroup'
								$menu_id_sql $voicemail_ext_sql $extension_sql 
								WHERE did_id='$did_id';";
                                $resultQuery = mysqli_query($link, $query);

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified DID ID $did_id','UPDATE vicidial_inbound_dids SET did_id=$did_id, did_active=$active, did_pattern=$did_pattern, did_route=$did_route, extension=$extension, user_route_settings_ingroup=$user_route_settings_ingroup WHERE did_id=$did_id;');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


                                $apiresults = array("result" => "success");
                                }
                        else {
                                $apiresults = array("result" => "Error: Failed to modified the Group ID");
                                }

			}
			}}}}}}}
                }}}
	}}}
     } //exist did
}



?>

