<?php
   //////////////////////////////////////////////////
   /// Name: goAddList.php                        ///
   /// Description: API to add new list           ///
   /// Version: 0.9                               ///
   /// Copyright: GOAutoDial Ltd. (c) 2011-2015   ///
   /// Written by: Jeremiah Sebastian Samatra     ///
   /// License: AGPLv2                            ///
   //////////////////////////////////////////////////
    
   include_once("../goFunctions.php");

	/* POST or GET Variables */
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
		  $log_user = $session_user;
		  $log_group = $_REQUEST['log_group'];
/* Inbound Campaign */
	$campaign_id 	= $_REQUEST['campaign_id'];
	$campaign_name 	= $_REQUEST['campaign_name'];
	$campaign_type 	= strtoupper($_REQUEST['campaign_type']);
	$active 	= $_REQUEST['active'];
	$dial_method 	= $_REQUEST['dial_method'];
	$dial_statuses 	= $_REQUEST['dial_statuses'];
	$lead_order 	= $_REQUEST['lead_order'];
	$allow_closers 	= $_REQUEST['allow_closers'];
	$hopper_level 	= $_REQUEST['hopper_level'];
	$auto_dial_level 	= $_REQUEST['auto_dial_level'];
	$auto_dial_level_adv 	= $_REQUEST['auto_dial_level_adv'];
	$dial_prefix 	= $_REQUEST['dial_prefix'];
	$campaign_changedate 	= $_REQUEST['campaign_changedate'];
	$campaign_stats_refresh 	= $_REQUEST['campaign_stats_refresh'];
	$campaign_vdad_exten 	= $_REQUEST['campaign_vdad_exten'];
	$campaign_recording 	= $_REQUEST['campaign_recording'];
	$campaign_rec_filename 	= $_REQUEST['campaign_rec_filename'];
	$scheduled_callbacks 	= $_REQUEST['scheduled_callbacks'];
	$scheduled_callbacks_alert 	= $_REQUEST['scheduled_callbacks_alert'];
	$no_hopper_leads_logins 	= $_REQUEST['no_hopper_leads_logins'];
	$use_internal_dnc 	= $_REQUEST['use_internal_dnc'];
	$use_campaign_dnc 	= $_REQUEST['use_campaign_dnc'];
	$campaign_cid 	= $_REQUEST['campaign_cid'];
	$user_group 	= $_REQUEST['user_group'];
	$drop_call_seconds 	= $_REQUEST['drop_call_seconds'];
	$goUsers 	= $_REQUEST['goUser'];
	$values 	= $_REQUEST['items'];
	$did_pattern 	= $_REQUEST['did_tfn_extension'];
	$group_color 	= $_REQUEST['group_color'];
	$call_route 	= $_REQUEST['call_route'];
	$call_route_text 	= $_REQUEST['call_route_text'];
	$survey_type 	= $_REQUEST['survey_type'];
	$number_channels 	= $_REQUEST['no_channels'];
	$copy_from_campaign 	= $_REQUEST['copy_from_campaign'];
	$list_id 	= $_REQUEST['list_id'];
	$country 	= $_REQUEST['country'];
	$check_for_duplicates 	= $_REQUEST['check_for_duplicates'];			
	$dial_prefix 	= $_REQUEST['dial_prefix'];
	$custom_dial_prefix	= $_REQUEST['custom_dial_prefix'];
	$status 	= $_REQUEST['status'];									
	$script 	= $_REQUEST['script'];						
	$answering_machine_detection 	= $_REQUEST['answering_machine_detection'];
	if($answering_machine_detection == ""){
		if($dial_method == "MANUAL" && $dial_method == "INBOUND_MAN"){
			$answering_machine_detection = '8368';
		}else{
			$answering_machine_detection = '8369';
		}
	}
	$caller_id 	= $_REQUEST['caller_id']; 					
	$force_reset_hopper 	= $_REQUEST['force_reset_hopper'];			
	$inbound_man 	= $_REQUEST['inbound_man'];					
	$phone_numbers 	= $_REQUEST['phone_numbers'];
	$lead_file	= $_FILES['lead_file']['tmp_name'];
	$leads	= $_FILES['leads']['tmp_name'];
	
	$call_time 		= $_REQUEST['call_time'];
	$dial_status 		= $_REQUEST['dial_status'];
	$list_order 		= $_REQUEST['list_order'];
	$lead_filter 		= $_REQUEST['lead_filter'];
	$dial_timeout 		= $_REQUEST['dial_timeout'];
	$manual_dial_prefix 		= $_REQUEST['manual_dial_prefix'];
	$call_launch 		= $_REQUEST['call_lunch'];
	$answering_machine_message 		= $_REQUEST['answering_machine_message'];
	$pause_codes 		= $_REQUEST['pause_codes'];
	$manual_dial_filter 		= $_REQUEST['manual_dial_filter'];
	$manual_dial_list_id 		= $_REQUEST['manual_dial_list_id'];
	$availability_only_tally 		= $_REQUEST['availability_only_tally'];
	$recording_filename 		= $_REQUEST['recording_filename'];
	$next_agent_call 		= $_REQUEST['next_agent_call'];
	$caller_id_3_way_call 		= $_REQUEST['caller_id_3_way_call'];
	$dial_prefix_3_way_call 		= $_REQUEST['dial_prefix_3_way_call'];
	$three_way_hangup_logging 		= $_REQUEST['three_way_hangup_logging'];
	$three_way_hangup_seconds 		= $_REQUEST['three_way_hangup_seconds'];
	$three_way_hangup_action 		= $_REQUEST['three_way_hangup_action'];
	$reset_leads_on_hopper 		= $_REQUEST['reset_leads_on_hopper'];

	$location = mysqli_real_escape_string($link, $_REQUEST['location_id']);

	/* Default values */ 
    	$defActive = array("Y","N");
    	$defType = array("OUTBOUND", "INBOUND", "BLENDED", "SURVEY", "COPY");
		
		
		if($dial_prefix == "CUSTOM"){
				$sippy_dial_prefix = $custom_dial_prefix;
		}else{
				$sippy_dial_prefix = $dial_prefix;
		}
		
		if($dial_method == "MANUAL"){
				$autoDialLevel = 0;
		}elseif($dial_method == "ADAPT_TAPERED"){
				$autoDialLevel = 1;
		}else{
			switch($auto_dial_level){
				case "OFF":
					$autoDialLevel = 0;
					break;
				case "SLOW":
					$autoDialLevel = 1;
					break;
				case "NORMAL":
					$autoDialLevel = 2;
					break;
				case "HIGH":
					$autoDialLevel = 4;
					break;
				case "MAX":
					$autoDialLevel = 6;
					break;
				case "MAX_PREDICTIVE":
					$autoDialLevel = 10;
					break;
				case "ADVANCE":
					$autoDialLevel = $auto_dial_level_adv;
					break;
				default:
					//DEFAULT HERE
			}
		}   

    if($campaign_id == null ||  $campaign_type == null || $campaign_name == null) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Set a value for Campaign ID, Campaign Type or Campaign Name.");
    } else {
    if(!in_array($campaign_type,$defType) && $campaign_type != null) {
		$err_msg = error_handle("10003", "campaign_type");
		$apiresults = array("code" => "10003", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Default value for campaign type is OUTBOUND, INBOUND, BLENDED and  SURVEY only.");
    } elseif(strlen($campaign_id) < 8 ){
    	$err_msg = error_handle("41006", "campaign_id. Limit is 8 Characters.");
		$apiresults = array("code" => "41006", "result" => $err_msg);
    } else{
        if (!checkIfTenant($groupId)) {
			$ul = "WHERE campaign_id='$campaign_id'";
		} else {
			$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
		}
            
            if(!empty($location)){
				$result_location = go_check_location($location, $user_group);
				if($result_location < 1){
					$err_msg = error_handle("41006", "location. User group does not exist in the location selected.");
					$apiresults = array("code" => "41006", "result" => $err_msg);
					$location = "";
				}
			}else{
				$location = "";
			}

            $queryCampaign = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
            $rsltvCampaign = mysqli_query($link, $queryCampaign);
            $countResultCampaign = mysqli_num_rows($rsltvCampaign);
		    
            if($countResultCampaign > 0) {
				$err_msg = error_handle("10109");
                $apiresults = array("result" => "$err_msg");
            } else {
            	$campaign_id = mysqli_real_escape_string($link, $campaign_id);
                $campaign_desc = mysqli_real_escape_string($link, str_replace('+',' ',$campaign_name));
                $SQLdate = date("Y-m-d H:i:s");
                $NOW = date("Y-m-d");
				
				// Outbound Campaign here
				if($campaign_type == "OUTBOUND"){
				
					//$groupId = go_get_groupid($goUser);
					$groupId = go_get_groupid($session_user);

					if (!checkIfTenant($groupId)) {
					$tenant_id = '---ALL---';
					} else {
					$tenant_id = "$groupId";
					}

					if ($campaign_id != 'undefined' && $campaign_id != ''){
						$queryCampID = "SELECT campaign_id FROM vicidial_campaigns WHERE campaign_id = '$campaign_id'";
						$rsltvCampID = mysqli_query($link, $queryCampID);
						$campNum = mysqli_num_rows($rsltvCampID);
						
                		if ($campNum < 1){
                			$local_call_time = "9am-9pm";

                            //if($VARSERVTYPE == "cloud" || $VARSERVTYPE == "gofree") {
                            //        $sippy_dial_prefix = "8888".$goUsers;
                            //} elseif($VARSERVTYPE == "gopackages") {
                            //        $sippy_dial_prefix = "9";
                            //}
								
                            $queryAdd = "INSERT INTO vicidial_campaigns (
												campaign_id, campaign_name, active, dial_method, dial_status_a,				
												dial_statuses, lead_order, allow_closers, hopper_level, auto_dial_level,			
												next_agent_call, local_call_time, dial_prefix, get_call_launch, campaign_changedate,		
												campaign_stats_refresh, list_order_mix, dial_timeout, campaign_recording,			
												campaign_rec_filename, scheduled_callbacks, scheduled_callbacks_alert, no_hopper_leads_logins, use_internal_dnc,			
												use_campaign_dnc, available_only_ratio_tally, campaign_cid, manual_dial_filter, user_group,					
												manual_dial_list_id, drop_call_seconds, campaign_vdad_exten, disable_alter_custdata, disable_alter_custphone, campaign_script
										)
										VALUES(
												'$campaign_id','$campaign_desc','Y','$dial_method','NEW',
												' N NA A AA DROP B NEW -','DOWN','Y','100','0',
												'oldest_call_finish','$local_call_time','$sippy_dial_prefix','NONE','$SQLdate',
												'Y','DISABLED','30','$campaign_recording',
												'FULLDATE_CUSTPHONE_CAMPAIGN_AGENT','Y','BLINK_RED','Y','Y',
												'Y','Y','5164536886','DNC_ONLY','$tenant_id',
												'${$tenant_id}998','7', '$answering_machine_detection', 'N', 'Y', '$script'
										)";
							$rsltvAdd = mysqli_query($link, $queryAdd);
							$queryVCS = "INSERT INTO vicidial_campaign_stats (campaign_id) values('$campaign_id')";
							$rsltvVCS = mysqli_query($link, $queryVCS);
								
							$allowed_campaigns = go_getall_allowed_campaigns($groupId);

                            if (strlen($allowed_campaigns) < 1) { 
                            	$allowed_campaigns = " -"; 
                            }
								
                            $queryVUG = "UPDATE vicidial_user_groups SET allowed_campaigns=' {$campaign_id}$allowed_campaigns' WHERE user_group='$tenant_id'";
							$rsltvVUG = mysqli_query($link, $queryVUG);

							$groupId = go_get_groupid($goUser);

							if (!checkIfTenant($groupId)) {
								$ul = "WHERE campaign_id='$campaign_id'";
							} else {
								$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
							}

							$query = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1";
							$rsltv = mysqli_query($link, $query);
							$countResult = mysqli_num_rows($rsltv);
							
							if($countResult > 0) {
							// Admin logs
								$SQLdate = date("Y-m-d H:i:s");
								//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Campaign $campaign_id','')";
								//$rsltvLog = mysqli_query($linkgo, $queryLog);
								$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Outbound Campaign: $campaign_id", $log_group, $queryAdd);
								
								if(!empty($location))
									$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type, location_id) values('$campaign_id', '$campaign_type', '$location')";
								else
									$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type) values('$campaign_id', '$campaign_type')";

								$rsltvGoCampaign = mysqli_query($linkgo, $queryGoCampaign);
					
								$apiresults = array("result" => "success");
							} else {
								$err_msg = error_handle("10010");
								$apiresults = array("code" => "10010", "result" => "$err_msg");
							}
                		}
	                }
                }
                // End of OUTBOUND

                // Inbound Campaign here
                if($campaign_type == "INBOUND"){
					$defCallRoute = array("INGROUP","IVR","AGENT","VOICEMAIL");
					$callRoute = strtoupper($call_route);
					$campaign_id = mysqli_real_escape_string($link, $campaign_id);
					$campaign_desc = mysqli_real_escape_string($link, str_replace('+',' ',$campaign_name));
					$SQLdate = date("Y-m-d H:i:s");
					$NOW = date("Y-m-d");
					//$groupId = go_get_groupid($goUser);
					$groupId = go_get_groupid($session_user);
					if (!checkIfTenant($groupId)){
						$tenant_id = '---ALL---';
					} else {
						$tenant_id = "$groupId";
					}

	                if ($campaign_id!='undefined' && $campaign_id!=''){
					$local_call_time = "9am-9pm";
					$auth_user = $goUsers;
					//if($VARSERVTYPE == "cloud" || $VARSERVTYPE == "gofree") {
					//		$sippy_dial_prefix = "8888".$auth_user;
					//} elseif($VARSERVTYPE == "gopackages") {
					//		$sippy_dial_prefix = "9";
					//}
						
					$queryAddInbound = "INSERT INTO vicidial_campaigns (
							campaign_id, campaign_name, active, dial_method, dial_status_a,
							dial_statuses, lead_order, allow_closers, hopper_level, auto_dial_level,
							next_agent_call, local_call_time, dial_prefix, get_call_launch, campaign_changedate,
							campaign_stats_refresh, list_order_mix, dial_timeout, campaign_vdad_exten, campaign_recording,
							campaign_rec_filename, scheduled_callbacks, scheduled_callbacks_alert, no_hopper_leads_logins, use_internal_dnc,
							use_campaign_dnc, available_only_ratio_tally, campaign_cid, manual_dial_filter, user_group,
							manual_dial_list_id, drop_call_seconds, manual_dial_prefix, am_message_exten, agent_pause_codes_active,
							three_way_call_cid, three_way_dial_prefix, customer_3way_hangup_logging, customer_3way_hangup_seconds, customer_3way_hangup_action, campaign_allow_inbound, disable_alter_custdata, disable_alter_custphone, campaign_script
						)
						VALUES(
							'$campaign_id','$campaign_desc','Y','$dial_method','NEW',
							' N NA A AA DROP B NEW -','DOWN','Y','100','1.0',
							'oldest_call_finish','$local_call_time','$sippy_dial_prefix','NONE','$SQLdate',
							'Y','DISABLED','30','$answering_machine_detection','ALLFORCE',
							'FULLDATE_CUSTPHONE_CAMPAIGN_AGENT','Y','BLINK_RED','Y','Y',
							'Y','Y','5164536886','DNC_ONLY','$tenant_id',
							'{$tenant_id}998','7','$manual_dial_prefix','$answering_machine_message','$pause_codes',
							'$caller_id_3_way_call','$dial_prefix_3_way_call','$three_way_hangup_logging','$three_way_hangup_seconds','$three_way_hangup_action', 'Y', 'N', 'Y', '$script'
						)";

						$rsltvInbound = mysqli_query($link, $queryAddInbound);
						$queryAddVCS = "INSERT INTO vicidial_campaign_stats (campaign_id) values('$campaign_id')";
						$rsltvqueryAddVCS = mysqli_query($link, $queryAddVCS);
						$allowed_campaigns = go_getall_allowed_campaigns($groupId);
						
						if (strlen($allowed_campaigns) < 1) { 
							$allowed_campaigns = " -"; 
						}
						
						$queryUpdateVUG = "UPDATE vicidial_user_groups SET allowed_campaigns=' {$campaign_id}$allowed_campaigns' WHERE user_group='$tenant_id'";
						$rsltvqueryUpdateVUG = mysqli_query($link, $queryUpdateVUG);
						
						$groupId = go_get_groupid($goUser);
						
						if (!checkIfTenant($groupId)) {
							$ul = "WHERE campaign_id='$campaign_id'";
						} else {
							$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
						}
						
						$query = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
						$rsltv = mysqli_query($link, $query);
						$countResult = mysqli_num_rows($rsltv);
						if($countResult > 0) {
							if ($callRoute != null){
								// Call Route
								$didDesc = "$campaign_id $campaign_type DID";
								$didPattern = $call_route_text;
								
								$queryDID = "SELECT did_pattern FROM vicidial_inbound_dids WHERE did_pattern = '$did_pattern' LIMIT 1;";
								$rsltvDID = mysqli_query($link, $queryDID);
								$countResultDID = mysqli_num_rows($rsltvDID);
								$serverIP = $_SERVER['REMOTE_ADDR'];
								switch ($callRoute){
									case "INGROUP":
										if($countResultDID > 0){
												$queryING = "UPDATE vicidial_inbound_dids
																SET 
																	did_description = '$didDesc',
																	did_active = 'Y',
																	did_route = 'IN_GROUP',
																	user_route_settings_ingroup = '$call_route_text',
																	campaign_id = '$campaign_id',
																	record_call = 'N',
																	filter_list_id = '$list_id',
																	filter_campaign_id = '$campaign_id',
																	group_id = '$call_route_text',
																	server_ip = '$serverIP',
																	user_group = '$tenant_id'
																WHERE
																	did_pattern='$did_pattern';";	
										}else{
												$queryING = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,user_route_settings_ingroup,campaign_id,record_call,filter_list_id,filter_campaign_id,group_id,server_ip,user_group)VALUES ('$did_pattern','$didDesc','Y','IN_GROUP','AGENTDIRECT','$campaign_id','N','$list_id','$campaign_id','AGENTDIRECT','$serverIP','$tenant_id')";		
										}
										$rsltvING = mysqli_query($link, $queryING);
										$queryUpdateVC = "UPDATE vicidial_campaigns SET xfer_groups = '$call_route_text -', closer_campaigns = '$call_route_text -' WHERE campaign_id = '$campaign_id'";
										$rsltvVC = mysqli_query($link, $queryUpdateVC);
									break;
		
									case "IVR":
										$menuID = "$call_route_text";
										$queryVCM = "INSERT INTO vicidial_call_menu (menu_id,menu_name,user_group) values('$menuID','$menuID Inbound Call Menu','$tenant_id')";
										$rsltvVCM = mysqli_query($link, $queryVCM);
										if($countResultDID > 0){
												$queryVID = "UPDATE vicidial_inbound_dids
																SET 
																		did_description = '$didDesc',
																		did_active = 'Y',
																		did_route = 'CALLMENU',
																		campaign_id = '$campaign_id',
																		record_call = 'N',
																		filter_list_id = '$list_id',
																		filter_campaign_id = '$campaign_id',
																		server_ip = '$serverIP',
																		menu_id = '$call_route_text',
																		user_group = '$tenant_id'
																WHERE
																	did_pattern='$did_pattern';";	
										}else{
												$queryVID = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,campaign_id,record_call,filter_list_id,filter_campaign_id,server_ip,menu_id,user_group)VALUES ('$did_pattern','$didDesc','Y','CALLMENU','$campaign_id','N','$list_id','$campaign_id','$serverIP','defaultlog','$tenant_id')";	
										}
										
										$rsltvVID = mysqli_query($link, $queryVID);
									break;
		
									case "AGENT":
										$queryVID = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,user_route_settings_ingroup,campaign_id,record_call,filter_list_id,filter_campaign_id,user,group_id,server_ip,user_group)VALUES ('$did_pattern','$didDesc','Y','AGENT','$group_id','$campaign_id','N','$list_id','$campaign_id','$call_route_text','$group_id','$ip_address','$tenant_id')";
										$rsltvVID = mysqli_query($link, $queryVID);
									break;
		
									case "VOICEMAIL":
										if ($emailORagent=='undefined')
											$emailORagent='';
		
										$queryVV = "INSERT INTO vicidial_voicemail SET voicemail_id='$campaign_id',pass='$campaign_id',email='$emailORagent',fullname='$campaign_id VOICEMAIL',active='Y',user_group='$tenant_id'";
										$rsltvVV = mysqli_query($link, $queryVV);
		
										$queryVID = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,user_route_settings_ingroup,campaign_id,record_call,filter_list_id,filter_campaign_id,voicemail_ext,user_group,server_ip)VALUES ('$did_pattern','$didDesc','Y','VOICEMAIL','$group_id','$campaign_id','N','$list_id','$campaign_id','$call_route_text','$tenant_id','$ip_address')";
										$rsltvVID = mysqli_query($link, $queryVID);
									break;
								}
			
								$queryUpdateVC = "UPDATE vicidial_campaigns SET campaign_allow_inbound = 'Y' WHERE campaign_id = '$campaign_id'";
								$rsltvVC = mysqli_query($link, $queryUpdateVC);
								
								$queryUpdateVU = "UPDATE vicidial_users set modify_inbound_dids='1' where user='$userID'";
								$rsltvVU = mysqli_query($link, $queryUpdateVU);
							}
		
						 // Admin logs
							$SQLdate = date("Y-m-d H:i:s");
							//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Campaign $campaign_id','');";
							//$rsltvLog = mysqli_query($linkgo, $queryLog);
							$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Inbound Campaign: $campaign_id", $log_group, $queryAddInbound);
							
							if(!empty($location))
									$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type, location_id) values('$campaign_id', '$campaign_type', '$location')";
							else
								$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type) values('$campaign_id', '$campaign_type')";
							
						    $rsltvGoCampaign = mysqli_query($linkgo, $queryGoCampaign);
							
							$apiresults = array("result" => "success");
						} else {
							$err_msg = error_handle("10010");
							$apiresults = array("code" => "10010", "result" => $err_msg);
						}
	                }
                }
                // End of INBOUND
				
                // Blended Campaign here
                if($campaign_type == "BLENDED"){
					$defCallRoute = array("INGROUP","IVR","AGENT","VOICEMAIL");
					$campaign_id = mysqli_real_escape_string($link, $campaign_id);
					$didPattern = $did_pattern;
					$groupColor = $group_color;
					$emailORagent = $goUsers;
					$campaign_desc = mysqli_real_escape_string($link, str_replace('+',' ',$campaign_name));
					$callRoute = strtoupper($call_route);
					$SQLxdate = date("Y-m-d H:i:s");
					$NOW = date("m-d-Y");
					
					if($groupColor == null && $callRoute == null){
						$err_msg = error_handle("40001", "group_color & call_route");
						$apiresults = array("code" => "40001", "result" => $err_msg);
					} else {
						if(!in_array($callRoute,$defCallRoute) || $callRoute == null) {
							$err_msg = error_handle("10003", "call_route");
							$apiresults = array("code" => "40001", "result" => $err_msg);
						} else {
							//$groupId = go_get_groupid($goUser);
							$groupId = go_get_groupid($session_user);
							
							if (!checkIfTenant($groupId)){
							$tenant_id = "---ALL---";
							} else {
							$tenant_id = "$groupId";
							}
							
						if ($campaign_id!='undefined' && $campaign_id!=''){
							$queryCampaign = "SELECT campaign_id FROM vicidial_campaigns WHERE campaign_id = '$campaign_id'";
							$rsltvCampaign = mysqli_query($link, $queryCampaign);
							$campNum = mysqli_num_rows($rsltvCampaign);
								
							if ($campNum < 1){
								$local_call_time = "9am-9pm";
								$group_id = "ING$didPattern";
								$group_name = "$campType Group $didPattern";
								
								// Insert new Inbound group
								$queryAdd = "INSERT INTO vicidial_inbound_groups (group_id,group_name,group_color,active,web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,web_form_address_two,start_call_url,dispo_call_url,add_lead_url,call_time_id,user_group) VALUES('$group_id','$group_name','$groupColor','Y','','','oldest_call_finish','Y','NONE','NONE','','','','','$local_call_time','$tenant_id')";
								
								$rsltvAdd = mysqli_query($link, $queryAdd);
								
								$querySelect = "SELECT campaign_id FROM vicidial_campaigns WHERE campaign_id = '$campaign_id'";
								$rsltvSelect = mysqli_query($link, $querySelect);
								$campNum1 = mysqli_num_rows($rsltvSelect);
							
								if ($campNum1 < 1){
									// Insert new Inbound Campaign
									$manualDialPrefix = '';
									$manualDialPrefixVal = '';
									$local_call_time = "9am-9pm";
									
									if ($campType=='Inbound'){
										$manualDialPrefix = ',manual_dial_prefix';
										$manualDialPrefixVal = ",'$manual_dial_prefix'";
									}
									
									//Sippy
									//jin
									$auth_user = $goUsers;
									//$VARSERVTYPE = $this->config->item('VARSERVTYPE');
									//if($VARSERVTYPE == "cloud"){
									//if($VARSERVTYPE == "cloud" || $VARSERVTYPE == "gofree") {
									//        $sippy_dial_prefix = "8888".$auth_user;
									//} elseif($VARSERVTYPE == "gopackages") {
									//        $sippy_dial_prefix = "9";
									//}
									
									$queryInsert = "INSERT INTO vicidial_campaigns (
									campaign_id, campaign_name, active, dial_method, dial_status_a,
									dial_statuses, lead_order, allow_closers, hopper_level, auto_dial_level,
									next_agent_call, local_call_time, dial_prefix, get_call_launch, campaign_changedate,
									campaign_stats_refresh, list_order_mix, dial_timeout, campaign_vdad_exten, campaign_recording,
									campaign_rec_filename, scheduled_callbacks, scheduled_callbacks_alert, no_hopper_leads_logins, use_internal_dnc,
									use_campaign_dnc, available_only_ratio_tally, campaign_cid, manual_dial_filter, user_group,
									manual_dial_list_id, drop_call_seconds, manual_dial_prefix, am_message_exten, agent_pause_codes_active,
									three_way_call_cid, three_way_dial_prefix, customer_3way_hangup_logging, customer_3way_hangup_seconds, customer_3way_hangup_action, campaign_allow_inbound, disable_alter_custdata, disable_alter_custphone, campaign_script
									)
									VALUES(
									'$campaign_id','$campaign_desc','Y','$dial_method','NEW',
									' N NA A AA DROP B NEW -','DOWN','Y','100','1.0',
									'oldest_call_finish','$local_call_time','$sippy_dial_prefix','NONE','$SQLdate',
									'Y','DISABLED','30','8369','ALLFORCE',
									'FULLDATE_CUSTPHONE_CAMPAIGN_AGENT','Y','BLINK_RED','Y','Y',
									'Y','Y','5164536886','DNC_ONLY','$tenant_id',
									'998','7','$manual_dial_prefix','$answering_machine_message','$pause_codes',
									'CAMPAIGN','$dial_prefix_3_way_call','$three_way_hangup_logging','$three_way_hangup_seconds','$three_way_hangup_action', 'Y', 'N', 'Y', '$script'
									)";
									
									$rsltvInsert = mysqli_query($link, $queryInsert);
									$queryVCS = "INSERT INTO vicidial_campaign_stats (campaign_id) values('$campaign_id')";
									$rsltvVCS = mysqli_query($link, $queryVCS);
									
									$allowed_campaigns = go_getall_allowed_campaigns($groupId);
									
									if (strlen($allowed_campaigns) < 1) { 
										$allowed_campaigns = " -"; 
									}
									
									$queryVUG = "UPDATE vicidial_user_groups SET allowed_campaigns=' {$campaign_id}$allowed_campaigns' WHERE user_group='$tenant_id'";
									$rsltvVUG = mysqli_query($link, $queryVUG);
								}
							}
						
							if ($callRoute != null){
								// Call Route
								$didDesc = "$campaign_id $campaign_type DID";
								$didPattern = $call_route_text;
								
								$queryDID = "SELECT did_pattern FROM vicidial_inbound_dids WHERE did_pattern = '$did_pattern' LIMIT 1;";
								$rsltvDID = mysqli_query($link, $queryDID);
								$countResultDID = mysqli_num_rows($rsltvDID);
								$serverIP = $_SERVER['REMOTE_ADDR'];
								switch ($callRoute){
									case "INGROUP":
									if($countResultDID > 0){
										$queryING = "UPDATE vicidial_inbound_dids
										SET 
										did_description = '$didDesc',
										did_active = 'Y',
										did_route = 'IN_GROUP',
										user_route_settings_ingroup = '$call_route_text',
										campaign_id = '$campaign_id',
										record_call = 'N',
										filter_list_id = '$list_id',
										filter_campaign_id = '$campaign_id',
										group_id = '$call_route_text',
										server_ip = '$serverIP',
										user_group = '$tenant_id'
										WHERE
										did_pattern='$did_pattern';";	
									}else{
										$queryING = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,
										user_route_settings_ingroup,campaign_id,record_call,filter_list_id,
										filter_campaign_id,group_id,server_ip,user_group)
										VALUES ('$did_pattern','$didDesc','Y','IN_GROUP',
										'AGENTDIRECT','$campaign_id','N','$list_id',
										'$campaign_id','AGENTDIRECT','$serverIP','$tenant_id')";		
									}
										$rsltvING = mysqli_query($link, $queryING);
										$queryUpdateVC = "UPDATE vicidial_campaigns SET xfer_groups = '$call_route_text -', closer_campaigns = '$call_route_text -' WHERE campaign_id = '$campaign_id'";
										$rsltvVC = mysqli_query($link, $queryUpdateVC);
									break;
									
									case "IVR":
										$menuID = "$call_route_text";
										$queryVCM = "INSERT INTO vicidial_call_menu (menu_id,menu_name,user_group) values('$menuID','$menuID Inbound Call Menu','$tenant_id')";
										$rsltvVCM = mysqli_query($link, $queryVCM);
										if($countResultDID > 0){
											$queryVID = "UPDATE vicidial_inbound_dids
											SET 
											did_description = '$didDesc',
											did_active = 'Y',
											did_route = 'CALLMENU',
											campaign_id = '$campaign_id',
											record_call = 'N',
											filter_list_id = '$list_id',
											filter_campaign_id = '$campaign_id',
											server_ip = '$serverIP',
											menu_id = '$call_route_text',
											user_group = '$tenant_id'
											WHERE
											did_pattern='$did_pattern';";	
										}else{
											$queryVID = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,campaign_id,record_call,
											filter_list_id,filter_campaign_id,server_ip,menu_id,user_group)
											VALUES ('$did_pattern','$didDesc','Y','CALLMENU','$campaign_id','N','$list_id','$campaign_id','$serverIP','defaultlog','$tenant_id')";	
										}
										
										$rsltvVID = mysqli_query($link, $queryVID);
									break;
									
									case "AGENT":
										$queryVID = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,user_route_settings_ingroup,
										campaign_id,record_call,filter_list_id,filter_campaign_id,user,group_id,server_ip,user_group)
										VALUES ('$did_pattern','$didDesc','Y','AGENT','$group_id','$campaign_id','N','$list_id','$campaign_id','$call_route_text',
										'$group_id','$ip_address','$tenant_id')";
										$rsltvVID = mysqli_query($link, $queryVID);
									break;
									
									case "VOICEMAIL":
										if ($emailORagent=='undefined')
										$emailORagent='';
										
										$queryVV = "INSERT INTO vicidial_voicemail SET voicemail_id='$campaign_id',pass='$campaign_id',email='$emailORagent',fullname='$campaign_id VOICEMAIL',active='Y',user_group='$tenant_id'";
										$rsltvVV = mysqli_query($link, $queryVV);
										
										$queryVID = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,user_route_settings_ingroup,
										campaign_id,record_call,filter_list_id,filter_campaign_id,voicemail_ext,user_group,server_ip)
										VALUES ('$did_pattern','$didDesc','Y','VOICEMAIL','$group_id','$campaign_id','N','$list_id','$campaign_id','$call_route_text','$tenant_id','$ip_address')";
										$rsltvVID = mysqli_query($link, $queryVID);
									break;
								}
								
								$queryUpdateVC = "UPDATE vicidial_campaigns SET campaign_allow_inbound = 'Y' WHERE campaign_id = '$campaign_id'";
								$rsltvVC = mysqli_query($link, $queryUpdateVC);
								
								$queryUpdateVU = "UPDATE vicidial_users set modify_inbound_dids='1' where user='$userID'";
								$rsltvVU = mysqli_query($link, $queryUpdateVU);
							}
						}
					
							$groupId = go_get_groupid($goUser);
							
							if (!checkIfTenant($groupId)) {
								$ul = "WHERE campaign_id='$campaign_id'";
							} else {
								$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
							}
							
							$query = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
							$rsltv = mysqli_query($link, $query);
							$countResult = mysqli_num_rows($rsltv);
							
							if($countResult > 0) {
								$SQLdate = date("Y-m-d H:i:s");
								//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Campaign $campaign_id','');";
								//$rsltvLog = mysqli_query($linkgo, $queryLog);
								$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Blended Campaign: $campaign_id", $log_group, $queryInsert);
								
								if(!empty($location))
									$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type, location_id) values('$campaign_id', '$campaign_type', '$location')";
								else
									$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type) values('$campaign_id', '$campaign_type')";
								
								$rsltvGoCampaign = mysqli_query($linkgo, $queryGoCampaign);
								
								$apiresults = array("result" => "success");
							} else {
								$err_msg = error_handle("41004", "campaign_id");
								$apiresults = array("code" => "41004", "result" => $err_msg);
							}
						}
					}
                }
                // End of BLENDED

                // Survey Campaign here
                if($campaign_type == "SURVEY"){
					$userID = $goUsers;
					$campType = $campaign_type;
					$campaign_id = mysqli_real_escape_string($link, $campaign_id);
					$surveyType = strtoupper($survey_type);
					$numChannels = $number_channels;
					$campaign_desc = mysqli_real_escape_string($link, str_replace('+',' ',$campaign_name));
					$SQLdate = date("Y-m-d H:i:s");
					$NOW = date("m-d-Y");
	                $defSurveyType = array('BROADCAST','PRESS1');
					$defNumCha = array(1,5,10,15,20,30);
					
					if(!in_array($surveyType,$defSurveyType) && $surveyType == null) {
						$err_msg = error_handle("10003", "survey_type");
						$apiresults = array("code" => "10003", "result" => $err_msg);
	                } else {
	                if(!in_array($numChannels,$defNumCha) && $numChannels == null) {
						$err_msg = error_handle("10003", "no_channels");
						$apiresults = array("code" => "10003", "result" => $err_msg);
	                } else {
	                //$groupId = go_get_groupid($goUser);
						$groupId = go_get_groupid($session_user);
						if (!checkIfTenant($groupId)) {
							$tenant_id = "---ALL---";
						} else {
							$tenant_id = "$groupId";
						}
						
						switch ($surveyType){
							case "BROADCAST":
								$routingExten = 8373;
								break;
							case "PRESS1":
								$routingExten = 8366;
								break;
						}
						
			                // Create New Survey Campaign
			                if ($campaign_id!='undefined' && $campaign_id!='' || $campaign_id != null){
		                        //if($VARSERVTYPE == "cloud"){
		                        $queryServer = "SELECT server_ip FROM servers WHERE LOWER(server_description) RLIKE 'meetme';";
								$rsltvServer = mysqli_query($link, $queryServer);
								while($fresults = mysqli_fetch_array($rsltvServer, MYSQLI_ASSOC)){
									$main_server_ip = $fresults['server_ip'];
								}

								$queryCheck = "SELECT campaign_id FROM vicidial_campaigns WHERE campaign_id = '$campaign_id'";
								$rsltvCheck = mysqli_query($link, $queryCheck);
                                $campNum = mysqli_num_rows($rsltvCheck); 

                                if ($campNum < 1){
                                	$local_call_time = "9am-9pm";
                                    //if($VARSERVTYPE == "gopackages") { $dial_prefix = "9"; }
                                    //Sippy
                                    $auth_user = $goUsers;

                                    //if($VARSERVTYPE == "cloud" || $VARSERVTYPE == "gofree") {
                                    //        $sippy_dial_prefix = "8888".$auth_user;
                                    //} elseif($VARSERVTYPE == "gopackages") {
                                    //        $sippy_dial_prefix = "9";
                                    //}
								    
									$wavfile_name = $_FILES["uploaded_wav"]['name'];
									$wavfile_orig = $_FILES['uploaded_wav']['name'];
									$wavfile_dir = $_FILES['uploaded_wav']['tmp_name'];
									$wavfile_size = $_FILES['uploaded_wav']['size'];
									$WeBServeRRooT = '/var/lib/asterisk';
									$sounds_web_directory = 'sounds';
									
									if (preg_match("/\.(wav|mp3)$/i",$wavfile_orig)) {
										$wavfile_dir = preg_replace("/ /",'\ ',$wavfile_dir);
										$wavfile_dir = preg_replace("/@/",'\@',$wavfile_dir);
										$wavfile_name = preg_replace("/ /",'',"go_".$wavfile_name);
										$wavfile_name = preg_replace("/@/",'',$wavfile_name);
										$wavfile_size = formatSizeUnits($wavfile_size);
										
										$get_sounds = "SELECT * FROM sounds WHERE goFilename = '$wavfile_name' AND goDirectory = '$path_sounds';";
										$exec_get_sounds = mysqli_query($linkgo, $get_sounds);
										$count_sounds = mysqli_num_rows($exec_get_sounds);
										
										if($count_sounds <= 0){
											copy($wavfile_dir, "$path_sounds/$wavfile_name");
											chmod("$path_sounds/$wavfile_name", 0766);
											
											$query_sounds = "INSERT INTO sounds(goFilename, goDirectory, goFileDate, goFilesize, uploaded_by) VALUES('$wavfile_name', '$path_sounds', NOW(), '$wavfile_size', '$session_user');";
											$exec_sounds = mysqli_query($linkgo, $query_sounds);
											
											if(!$exec_sounds){
												$err_msg = error_handle("10008");
												$apiresults = array("code" => "40001", "result" => $err_msg);
											}
										}
									}
									
								    $wavfile_name = substr($wavfile_name, 0, -4);
									if(empty($wavfile_name))
									$wavfile_name = "US_pol_survey_hello";
									
                                    //if($VARSERVTYPE == "cloud"){  
                                    $queryInsert = "INSERT INTO vicidial_campaigns (campaign_id,campaign_name,campaign_description,active,dial_method,dial_status_a,dial_statuses,lead_order,park_ext,park_file_name,web_form_address,allow_closers,hopper_level,auto_dial_level,available_only_ratio_tally,next_agent_call,local_call_time,dial_prefix,voicemail_ext,campaign_script,get_call_launch,campaign_changedate,campaign_stats_refresh,list_order_mix,web_form_address_two,start_call_url,dispo_call_url,dial_timeout,campaign_vdad_exten,campaign_recording,campaign_rec_filename,scheduled_callbacks,scheduled_callbacks_alert,no_hopper_leads_logins,per_call_notes,agent_lead_search,use_internal_dnc,use_campaign_dnc,campaign_cid,user_group,manual_dial_list_id,drop_call_seconds,survey_opt_in_audio_file,survey_first_audio_file, survey_method, disable_alter_custdata, disable_alter_custphone)VALUES('$campaign_id','$campaign_desc','$campaign_desc','N','RATIO','NEW',' N NA A AA DROP B NEW -','DOWN','','','','Y','100','1','Y','random','$local_call_time','$sippy_dial_prefix','','$script','','$SQLdate','Y','DISABLED','','','','30','8366','$campaign_recording','FULLDATE_CUSTPHONE_CAMPAIGN_AGENT','Y','BLINK_RED','Y','ENABLED','ENABLED','Y','Y','5164536886','$tenant_id','{$tenant_id}998','7','','$wavfile_name', 'EXTENSION', 'N', 'Y')";
                                    $rsltvInsert = mysqli_query($link, $queryInsert);

                                    $queryNew = "INSERT INTO vicidial_campaign_stats (campaign_id) values('$campaign_id')";
									$rsltvNew = mysqli_query($link, $queryNew);

									$allowed_campaigns = go_getall_allowed_campaigns($groupId);

                                    if (strlen($allowed_campaigns) < 1) { 
                                    	$allowed_campaigns = " -"; 
                                    }

                                    $queryVUG = "UPDATE vicidial_user_groups SET allowed_campaigns=' {$campaign_id}$allowed_campaigns' WHERE user_group='$tenant_id'";
									$rsltvVUG = mysqli_query($link, $queryVUG);

									do {
                                        $agvar= mt_rand();
                                        $queryVU = "SELECT user FROM vicidial_users WHERE user='$agvar';";
										$rsltvVU = mysqli_query($link, $queryVU);
                                        $user_exist = mysqli_num_rows($rsltvVU);
                                    }
                                    while ($user_exist > 0);

                                    $pass= substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
									$survey_method = "EXTENSION";
								    if($survey_method != "AGENT_XFER" && $active == 'Y'){
										$remote_agent_status = 'Y';
									}else{
										$remote_agent_status = 'N';
									}
                                    $agent_user="$agvar";
                                    $agent_name="Survey Agent - $campaign_id";
                                    $agent_phone="$agvar";

                                    $queryVRA = "INSERT INTO vicidial_remote_agents (user_start,number_of_lines,server_ip,conf_exten,status,campaign_id,closer_campaigns) values('$agent_user','$numChannels','$main_server_ip','8300','$remote_agent_status','$campaign_id','')";
									$rsltvVRA = mysqli_query($link, $queryVRA);

									if ($countAll < 1){
                                        $tenant_id = ($tenant_id=='---ALL---') ? "AGENTS" : "$tenant_id";
                                        $queryAdd = "INSERT INTO vicidial_users (user,pass,full_name,user_level,user_group) values('$agent_user','$pass','$agent_name','4','$tenant_id')";
										$rsltvAdd = mysqli_query($link, $queryAdd);
                                    }
                                }
			                }

			                $groupId = go_get_groupid($goUser);

			                if (!checkIfTenant($groupId)) {
			                    $ul = "WHERE campaign_id='$campaign_id'";
			                } else {
			                    $ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
			                }

			                $queryCampaign = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
			                $rsltvCampaign = mysqli_query($link, $queryCampaign);
			                $countResult = mysqli_num_rows($rsltvCampaign);
			                if($countResult > 0) {
				        		// Admin logs
	                            $SQLdate = date("Y-m-d H:i:s");
	                            //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Campaign $campaign_id','');";
	                            //$rsltvLog = mysqli_query($linkgo, $queryLog);
										 $log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Survey Campaign: $campaign_id", $log_group, $queryInsert);

								if(!empty($location))
									$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type, location_id) values('$campaign_id', '$campaign_type', '$location')";
								else
									$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type) values('$campaign_id', '$campaign_type')";
								
								$rsltvGoCampaign = mysqli_query($linkgo, $queryGoCampaign);

			                    $apiresults = array("result" => "success");
			                } else {
								$err_msg = error_handle("10010");
								$apiresults = array("code" => "10010", "result" => $err_msg);
			                }
						}
					}
                }
                // End of SURVEY
				
				if($campaign_type == "COPY"){
					if(count($_REQUEST) == 8){
						// proceed copy campaign
						$getToCopy = "SELECT * FROM vicidial_campaigns WHERE campaign_id ='$copy_from_campaign'";
						$rsltvToCopy = mysqli_query($link, $getToCopy);
						while($fresults = mysqli_fetch_array($rsltvToCopy, MYSQLI_ASSOC)){
								$active_FROMARRAY 	= $fresults['active'];
								$dial_status_a_FROMARRAY 	= $fresults['dial_status_a'];
								$dial_status_b_FROMARRAY 	= $fresults['dial_status_b'];
								$dial_status_c_FROMARRAY 	= $fresults['dial_status_c'];
								$dial_status_d_FROMARRAY 	= $fresults['dial_status_d'];
								$dial_status_e_FROMARRAY 	= $fresults['dial_status_e'];
								$lead_order_FROMARRAY 	= $fresults['lead_order'];
								$park_ext_FROMARRAY 	= $fresults['park_ext'];
								$park_file_name_FROMARRAY 	= $fresults['park_file_name'];
								$web_form_address_FROMARRAY 	= $fresults['web_form_address'];
								$allow_closers_FROMARRAY 	= $fresults['allow_closers'];
								$hopper_level_FROMARRAY 	= $fresults['hopper_level'];
								$next_agent_call_FROMARRAY 	= $fresults['next_agent_call'];
								$local_call_time_FROMARRAY 	= $fresults['local_call_time'];
								$voicemail_ext_FROMARRAY 	= $fresults['voicemail_ext'];
								$dial_timeout_FROMARRAY 	= $fresults['dial_timeout'];
								$dial_prefix_FROMARRAY 	= $fresults['dial_prefix'];
								$campaign_cid_FROMARRAY 	= $fresults['campaign_cid'];
								$campaign_vdad_exten_FROMARRAY 	= $fresults['campaign_vdad_exten'];
								$campaign_rec_exten_FROMARRAY 	= $fresults['campaign_rec_exten'];
								$campaign_recording_FROMARRAY 	= $fresults['campaign_recording'];
								$campaign_rec_filename_FROMARRAY 					= $fresults['campaign_rec_filename'];
								$campaign_script_FROMARRAY 							= $fresults['campaign_script'];
								$get_call_launch_FROMARRAY 							= $fresults['get_call_launch'];
								$am_message_exten_FROMARRAY 						= $fresults['am_message_exten'];
								$amd_send_to_vmx_FROMARRAY 							= $fresults['amd_send_to_vmx'];
								$xferconf_a_dtmf_FROMARRAY 							= $fresults['xferconf_a_dtmf'];
								$xferconf_a_number_FROMARRAY 						= $fresults['xferconf_a_number'];
								$xferconf_b_dtmf_FROMARRAY 							= $fresults['xferconf_b_dtmf'];
								$xferconf_b_number_FROMARRAY 						= $fresults['xferconf_b_number'];
								$alt_number_dialing_FROMARRAY 						= $fresults['alt_number_dialing'];
								$scheduled_callbacks_FROMARRAY 						= $fresults['scheduled_callbacks'];
								$lead_filter_id_FROMARRAY 							= $fresults['lead_filter_id'];
								$drop_call_seconds_FROMARRAY 						= $fresults['drop_call_seconds'];
								$drop_action_FROMARRAY 								= $fresults['drop_action'];
								$safe_harbor_exten_FROMARRAY 						= $fresults['safe_harbor_exten'];
								$display_dialable_count_FROMARRAY 					= $fresults['display_dialable_count'];
								$wrapup_seconds_FROMARRAY 							= $fresults['wrapup_seconds'];
								$wrapup_message_FROMARRAY 							= $fresults['wrapup_message'];
								$closer_campaigns_FROMARRAY 						= $fresults['closer_campaigns'];
								$use_internal_dnc_FROMARRAY 						= $fresults['use_internal_dnc'];
								$allcalls_delay_FROMARRAY 							= $fresults['allcalls_delay'];
								$omit_phone_code_FROMARRAY 							= $fresults['omit_phone_code'];
								$available_only_ratio_tally_FROMARRAY 				= $fresults['available_only_ratio_tally'];
								$adaptive_dropped_percentage_FROMARRAY 				= $fresults['adaptive_dropped_percentage'];
								$adaptive_maximum_level_FROMARRAY 					= $fresults['adaptive_maximum_level'];
								$adaptive_latest_server_time_FROMARRAY 				= $fresults['adaptive_latest_server_time'];
								$adaptive_intensity_FROMARRAY 						= $fresults['adaptive_intensity'];
								$adaptive_dl_diff_target_FROMARRAY 					= $fresults['adaptive_dl_diff_target'];
								$concurrent_transfers_FROMARRAY 					= $fresults['concurrent_transfers'];
								$auto_alt_dial_FROMARRAY 							= $fresults['auto_alt_dial'];
								$auto_alt_dial_statuses_FROMARRAY 					= $fresults['auto_alt_dial_statuses'];
								$agent_pause_codes_active_FROMARRAY 				= $fresults['agent_pause_codes_active'];
								$campaign_description_FROMARRAY 					= $fresults['campaign_description'];
								$campaign_changedate_FROMARRAY 						= $fresults['campaign_changedate'];
								$campaign_stats_refresh_FROMARRAY 					= $fresults['campaign_stats_refresh'];
								$campaign_logindate_FROMARRAY 						= $fresults['campaign_logindate'];
								$dial_statuses_FROMARRAY 							= $fresults['dial_statuses'];
								$disable_alter_custdata_FROMARRAY 					= $fresults['disable_alter_custdata'];
								$no_hopper_leads_logins_FROMARRAY 					= $fresults['no_hopper_leads_logins'];
								$list_order_mix_FROMARRAY 							= $fresults['list_order_mix'];
								$campaign_allow_inbound_FROMARRAY 					= $fresults['campaign_allow_inbound'];
								$manual_dial_list_id_FROMARRAY 						= $fresults['manual_dial_list_id'];
								$default_xfer_group_FROMARRAY 						= $fresults['default_xfer_group'];
								$xfer_groups_FROMARRAY 								= $fresults['xfer_groups'];
								$queue_priority_FROMARRAY 							= $fresults['queue_priority'];
								$drop_inbound_group_FROMARRAY 						= $fresults['drop_inbound_group'];
								$qc_enabled_FROMARRAY 								= $fresults['qc_enabled'];
								$qc_statuses_FROMARRAY 								= $fresults['qc_statuses'];
								$qc_lists_FROMARRAY 								= $fresults['qc_lists'];
								$qc_shift_id_FROMARRAY 								= $fresults['qc_shift_id'];
								$qc_get_record_launch_FROMARRAY 					= $fresults['qc_get_record_launch'];
								$qc_show_recording_FROMARRAY 						= $fresults['qc_show_recording'];
								$qc_web_form_address_FROMARRAY 						= $fresults['qc_web_form_address'];
								$qc_script_FROMARRAY 								= $fresults['qc_script'];
								$survey_first_audio_file_FROMARRAY 					= $fresults['survey_first_audio_file'];
								$survey_dtmf_digits_FROMARRAY 						= $fresults['survey_dtmf_digits'];
								$survey_ni_digit_FROMARRAY 							= $fresults['survey_ni_digit'];
								$survey_opt_in_audio_file_FROMARRAY 				= $fresults['survey_opt_in_audio_file'];
								$survey_ni_audio_file_FROMARRAY 					= $fresults['survey_ni_audio_file'];
								$survey_method_FROMARRAY 							= $fresults['survey_method'];
								$survey_no_response_action_FROMARRAY 				= $fresults['survey_no_response_action'];
								$survey_ni_status_FROMARRAY 						= $fresults['survey_ni_status'];
								$survey_response_digit_map_FROMARRAY 				= $fresults['survey_response_digit_map'];
								$survey_xfer_exten_FROMARRAY 						= $fresults['survey_xfer_exten'];
								$survey_camp_record_dir_FROMARRAY 					= $fresults['survey_camp_record_dir'];
								$disable_alter_custphone_FROMARRAY 					= $fresults['disable_alter_custphone'];
								$display_queue_count_FROMARRAY 						= $fresults['display_queue_count'];
								$manual_dial_filter_FROMARRAY 						= $fresults['manual_dial_filter'];
								$agent_clipboard_copy_FROMARRAY 					= $fresults['agent_clipboard_copy'];
								$agent_extended_alt_dial_FROMARRAY 					= $fresults['agent_extended_alt_dial'];
								$use_campaign_dnc_FROMARRAY 						= $fresults['use_campaign_dnc'];
								$three_way_call_cid_FROMARRAY 						= $fresults['three_way_call_cid'];
								$three_way_dial_prefix_FROMARRAY 					= $fresults['three_way_dial_prefix'];
								$web_form_target_FROMARRAY 							= $fresults['web_form_target'];
								$vtiger_search_category_FROMARRAY 					= $fresults['vtiger_search_category'];
								$vtiger_create_call_record_FROMARRAY 				= $fresults['vtiger_create_call_record'];
								$vtiger_create_lead_record_FROMARRAY 				= $fresults['vtiger_create_lead_record'];
								$vtiger_screen_login_FROMARRAY 						= $fresults['vtiger_screen_login'];
								$cpd_amd_action_FROMARRAY 							= $fresults['cpd_amd_action'];
								$agent_allow_group_alias_FROMARRAY 					= $fresults['agent_allow_group_alias'];
								$default_group_alias_FROMARRAY 						= $fresults['default_group_alias'];
								$vtiger_search_dead_FROMARRAY 						= $fresults['vtiger_search_dead'];
								$vtiger_status_call_FROMARRAY 						= $fresults['vtiger_status_call'];
								$survey_third_digit_FROMARRAY 						= $fresults['survey_third_digit'];
								$survey_third_audio_file_FROMARRAY 					= $fresults['survey_third_audio_file'];
								$survey_third_status_FROMARRAY 						= $fresults['survey_third_status'];
								$survey_third_exten_FROMARRAY 						= $fresults['survey_third_exten'];
								$survey_fourth_digit_FROMARRAY 						= $fresults['survey_fourth_digit'];
								$survey_fourth_audio_file_FROMARRAY 				= $fresults['survey_fourth_audio_file'];
								$survey_fourth_status_FROMARRAY 					= $fresults['survey_fourth_status'];
								$survey_fourth_exten_FROMARRAY 						= $fresults['survey_fourth_exten'];
								$drop_lockout_time_FROMARRAY 						= $fresults['drop_lockout_time'];
								$quick_transfer_button_FROMARRAY 					= $fresults['quick_transfer_button'];
								$prepopulate_transfer_preset_FROMARRAY 				= $fresults['prepopulate_transfer_preset'];
								$drop_rate_group_FROMARRAY 							= $fresults['drop_rate_group'];
								$view_calls_in_queue_FROMARRAY 						= $fresults['view_calls_in_queue'];
								$view_calls_in_queue_launch_FROMARRAY 				= $fresults['view_calls_in_queue_launch'];
								$grab_calls_in_queue_FROMARRAY 						= $fresults['grab_calls_in_queue'];
								$call_requeue_button_FROMARRAY 						= $fresults['call_requeue_button'];
								$pause_after_each_call_FROMARRAY 					= $fresults['pause_after_each_call'];
								$no_hopper_dialing_FROMARRAY 						= $fresults['no_hopper_dialing'];
								$agent_dial_owner_only_FROMARRAY 					= $fresults['agent_dial_owner_only'];
								$agent_display_dialable_leads_FROMARRAY 			= $fresults['agent_display_dialable_leads'];
								$web_form_address_two_FROMARRAY 					= $fresults['web_form_address_two'];
								$waitforsilence_options_FROMARRAY 					= $fresults['waitforsilence_options'];
								$agent_select_territories_FROMARRAY 				= $fresults['agent_select_territories'];
								$campaign_calldate_FROMARRAY 						= $fresults['campaign_calldate'];
								$crm_popup_login_FROMARRAY 							= $fresults['crm_popup_login'];
								$crm_login_address_FROMARRAY 						= $fresults['crm_login_address'];
								$timer_action_FROMARRAY 							= $fresults['timer_action'];
								$timer_action_message_FROMARRAY 					= $fresults['timer_action_message'];
								$timer_action_seconds_FROMARRAY 					= $fresults['timer_action_seconds'];
								$start_call_url_FROMARRAY 							= $fresults['start_call_url'];
								$dispo_call_url_FROMARRAY 							= $fresults['dispo_call_url'];
								$xferconf_c_number_FROMARRAY 						= $fresults['xferconf_c_number'];
								$xferconf_d_number_FROMARRAY 						= $fresults['xferconf_d_number'];
								$xferconf_e_number_FROMARRAY 						= $fresults['xferconf_e_number'];
								$use_custom_cid_FROMARRAY 							= $fresults['use_custom_cid'];
								$scheduled_callbacks_alert_FROMARRAY 				= $fresults['scheduled_callbacks_alert'];
								$queuemetrics_callstatus_override_FROMARRAY 		= $fresults['queuemetrics_callstatus_override'];
								$extension_appended_cidname_FROMARRAY 				= $fresults['extension_appended_cidname'];
								$scheduled_callbacks_count_FROMARRAY 				= $fresults['scheduled_callbacks_count'];
								$manual_dial_override_FROMARRAY 					= $fresults['manual_dial_override'];
								$blind_monitor_warning_FROMARRAY 					= $fresults['blind_monitor_warning'];
								$blind_monitor_message_FROMARRAY 					= $fresults['blind_monitor_message'];
								$blind_monitor_filename_FROMARRAY 					= $fresults['blind_monitor_filename'];
								$inbound_queue_no_dial_FROMARRAY 					= $fresults['inbound_queue_no_dial'];
								$timer_action_destination_FROMARRAY 				= $fresults['timer_action_destination'];
								$enable_xfer_presets_FROMARRAY 						= $fresults['enable_xfer_presets'];
								$hide_xfer_number_to_dial_FROMARRAY 				= $fresults['hide_xfer_number_to_dial'];
								$manual_dial_prefix_FROMARRAY 						= $fresults['manual_dial_prefix'];
								$customer_3way_hangup_logging_FROMARRAY 			= $fresults['customer_3way_hangup_logging'];
								$customer_3way_hangup_seconds_FROMARRAY 			= $fresults['customer_3way_hangup_seconds'];
								$customer_3way_hangup_action_FROMARRAY 				= $fresults['customer_3way_hangup_action'];
								$ivr_park_call_FROMARRAY 							= $fresults['ivr_park_call'];
								$ivr_park_call_agi_FROMARRAY 						= $fresults['ivr_park_call_agi'];
								$manual_preview_dial_FROMARRAY 						= $fresults['manual_preview_dial'];
								$realtime_agent_time_stats_FROMARRAY 				= $fresults['realtime_agent_time_stats'];
								$use_auto_hopper_FROMARRAY 							= $fresults['use_auto_hopper'];
								$auto_hopper_multi_FROMARRAY 						= $fresults['auto_hopper_multi'];
								$auto_hopper_level_FROMARRAY 						= $fresults['auto_hopper_level'];
								$auto_trim_hopper_FROMARRAY 						= $fresults['auto_trim_hopper'];
								$api_manual_dial_FROMARRAY 							= $fresults['api_manual_dial'];
								$manual_dial_call_time_check_FROMARRAY 				= $fresults['manual_dial_call_time_check'];
								$display_leads_count_FROMARRAY 						= $fresults['display_leads_count'];
								$lead_order_randomize_FROMARRAY 					= $fresults['lead_order_randomize'];
								$lead_order_secondary_FROMARRAY 					= $fresults['lead_order_secondary'];
								$per_call_notes_FROMARRAY 							= $fresults['per_call_notes'];
								$my_callback_option_FROMARRAY 						= $fresults['my_callback_option'];
								$agent_lead_search_FROMARRAY 						= $fresults['agent_lead_search'];
								$agent_lead_search_method_FROMARRAY 				= $fresults['agent_lead_search_method'];
								$queuemetrics_phone_environment_FROMARRAY 			= $fresults['queuemetrics_phone_environment'];
								$auto_pause_precall_FROMARRAY 						= $fresults['auto_pause_precall'];
								$auto_pause_precall_code_FROMARRAY 					= $fresults['auto_pause_precall_code'];
								$auto_resume_precall_FROMARRAY 						= $fresults['auto_resume_precall'];
								$manual_dial_cid_FROMARRAY 							= $fresults['manual_dial_cid'];
								$post_phone_time_diff_alert_FROMARRAY 				= $fresults['post_phone_time_diff_alert'];
								$custom_3way_button_transfer_FROMARRAY 				= $fresults['custom_3way_button_transfer'];
								$available_only_tally_threshold_FROMARRAY 			= $fresults['available_only_tally_threshold'];
								$available_only_tally_threshold_agents_FROMARRAY 	= $fresults['available_only_tally_threshold_agents'];
								$dial_level_threshold_FROMARRAY 					= $fresults['dial_level_threshold'];
								$dial_level_threshold_agents_FROMARRAY 				= $fresults['dial_level_threshold_agents'];
								$safe_harbor_audio_FROMARRAY 						= $fresults['safe_harbor_audio'];
								$safe_harbor_menu_id_FROMARRAY 						= $fresults['safe_harbor_menu_id'];
								$survey_menu_id_FROMARRAY 							= $fresults['survey_menu_id'];
								$callback_days_limit_FROMARRAY 						= $fresults['callback_days_limit'];
								$dl_diff_target_method_FROMARRAY 					= $fresults['dl_diff_target_method'];
								$disable_dispo_screen_FROMARRAY 					= $fresults['disable_dispo_screen'];
								$disable_dispo_status_FROMARRAY 					= $fresults['disable_dispo_status'];
								$screen_labels_FROMARRAY 							= $fresults['screen_labels'];
								$status_display_fields_FROMARRAY 					= $fresults['status_display_fields'];
								$na_call_url_FROMARRAY 								= $fresults['na_call_url'];
								$survey_recording_FROMARRAY 						= $fresults['survey_recording'];
								$pllb_grouping_FROMARRAY 							= $fresults['pllb_grouping'];
								$pllb_grouping_limit_FROMARRAY 						= $fresults['pllb_grouping_limit'];
								$call_count_limit_FROMARRAY 						= $fresults['call_count_limit'];
								$call_count_target_FROMARRAY 						= $fresults['call_count_target'];
								$callback_hours_block_FROMARRAY 					= $fresults['callback_hours_block'];
								$callback_list_calltime_FROMARRAY 					= $fresults['callback_list_calltime'];
								$user_group_FROMARRAY 								= $fresults['user_group'];
								$hopper_vlc_dup_check_FROMARRAY 					= $fresults['hopper_vlc_dup_check'];
								$in_group_dial_FROMARRAY 							= $fresults['in_group_dial'];
								$in_group_dial_select_FROMARRAY 					= $fresults['in_group_dial_select'];
								$safe_harbor_audio_field_FROMARRAY 					= $fresults['safe_harbor_audio_field'];
								$pause_after_next_call_FROMARRAY 					= $fresults['pause_after_next_call'];
								$owner_populate_FROMARRAY 							= $fresults['owner_populate'];
								$use_other_campaign_dnc_FROMARRAY 					= $fresults['use_other_campaign_dnc'];
								$allow_emails_FROMARRAY 							= $fresults['allow_emails'];
								$amd_inbound_group_FROMARRAY 						= $fresults['amd_inbound_group'];
								$amd_callmenu_FROMARRAY 							= $fresults['amd_callmenu'];
								$survey_wait_sec_FROMARRAY 							= $fresults['survey_wait_sec'];
								$manual_dial_lead_id_FROMARRAY 						= $fresults['manual_dial_lead_id'];
								$dead_max_FROMARRAY 								= $fresults['dead_max'];
								$dead_max_dispo_FROMARRAY 							= $fresults['dead_max_dispo'];
								$dispo_max_FROMARRAY 								= $fresults['dispo_max'];
								$dispo_max_dispo_FROMARRAY 							= $fresults['dispo_max_dispo'];
								$pause_max_FROMARRAY 								= $fresults['pause_max'];
								$max_inbound_calls_FROMARRAY 						= $fresults['max_inbound_calls'];
								$manual_dial_search_checkbox_FROMARRAY 				= $fresults['manual_dial_search_checkbox'];
								$hide_call_log_info_FROMARRAY 						= $fresults['hide_call_log_info'];
								$timer_alt_seconds_FROMARRAY 						= $fresults['timer_alt_seconds'];
								$wrapup_bypass_FROMARRAY 							= $fresults['wrapup_bypass'];
								$wrapup_after_hotkey_FROMARRAY 						= $fresults['wrapup_after_hotkey'];
								$callback_active_limit_FROMARRAY 					= $fresults['callback_active_limit'];
								$callback_active_limit_override_FROMARRAY 			= $fresults['callback_active_limit_override'];
								$allow_chats_FROMARRAY 								= $fresults['allow_chats'];
								$comments_all_tabs_FROMARRAY 						= $fresults['comments_all_tabs'];
								$comments_dispo_screen_FROMARRAY 					= $fresults['comments_dispo_screen'];
								$comments_callback_screen_FROMARRAY 				= $fresults['comments_callback_screen'];
								$qc_comment_history_FROMARRAY 						= $fresults['qc_comment_history'];
								$show_previous_callback_FROMARRAY 					= $fresults['show_previous_callback'];
								$clear_script_FROMARRAY 							= $fresults['clear_script'];
								$cpd_unknown_action_FROMARRAY 						= $fresults['cpd_unknown_action'];
								$manual_dial_search_filter_FROMARRAY 				= $fresults['manual_dial_search_filter'];
								$web_form_address_three_FROMARRAY 					= $fresults['web_form_address_three'];
								$manual_dial_override_field_FROMARRAY 				= $fresults['manual_dial_override_field'];
								$status_display_ingroup_FROMARRAY 					= $fresults['status_display_ingroup'];
								$customer_gone_seconds_FROMARRAY 					= $fresults['customer_gone_seconds'];
								$agent_display_fields_FROMARRAY 					= $fresults['agent_display_fields'];
								$am_message_wildcards_FROMARRAY 					= $fresults['am_message_wildcards'];
								$manual_dial_timeout_FROMARRAY 						= $fresults['manual_dial_timeout'];
								$routing_initiated_recordings_FROMARRAY 			= $fresults['routing_initiated_recordings'];
								$manual_dial_hopper_check_FROMARRAY 				= $fresults['manual_dial_hopper_check'];
								$callback_useronly_move_minutes_FROMARRAY 			= $fresults['callback_useronly_move_minutes'];
								$ofcom_uk_drop_calc_FROMARRAY 						= $fresults['ofcom_uk_drop_calc'];
								$dial_method_FROMARRAY								= $fresults['dial_method'];
								$auto_dial_level_FROMARRAY							= $fresults['auto_dial_level'];
						}
						
						$GOCopy = "SELECT * FROM go_campaigns WHERE campaign_id='$copy_from_campaign'";
						$rsltGOCopy = mysqli_query($linkgo, $GOCopy);
						while($fgoresults = mysqli_fetch_array($rsltGOCopy, MYSQLI_ASSOC)){
								$dataGOCampaign = $fgoresults;
						}

						$queryAddCopy = "INSERT INTO vicidial_campaigns (campaign_id, campaign_name, dial_method, auto_dial_level, active,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order, park_ext,park_file_name,web_form_address,allow_closers,hopper_level,next_agent_call,local_call_time, voicemail_ext,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording, campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,amd_send_to_vmx,xferconf_a_dtmf,xferconf_a_number, xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,lead_filter_id,drop_call_seconds,drop_action, safe_harbor_exten,display_dialable_count,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay, omit_phone_code,available_only_ratio_tally,adaptive_dropped_percentage,adaptive_maximum_level,adaptive_latest_server_time,adaptive_intensity,adaptive_dl_diff_target, concurrent_transfers,auto_alt_dial,auto_alt_dial_statuses,agent_pause_codes_active,campaign_description,campaign_changedate,campaign_stats_refresh, campaign_logindate,dial_statuses,disable_alter_custdata,no_hopper_leads_logins,list_order_mix,campaign_allow_inbound,manual_dial_list_id, default_xfer_group,xfer_groups,queue_priority,drop_inbound_group,qc_enabled,qc_statuses,qc_lists, qc_shift_id,qc_get_record_launch,qc_show_recording,qc_web_form_address,qc_script,survey_first_audio_file,survey_dtmf_digits, survey_ni_digit,survey_opt_in_audio_file,survey_ni_audio_file,survey_method,survey_no_response_action,survey_ni_status,survey_response_digit_map, survey_xfer_exten,survey_camp_record_dir,disable_alter_custphone,display_queue_count,manual_dial_filter,agent_clipboard_copy,agent_extended_alt_dial, use_campaign_dnc,three_way_call_cid,three_way_dial_prefix,web_form_target,vtiger_search_category,vtiger_create_call_record,vtiger_create_lead_record, vtiger_screen_login,cpd_amd_action,agent_allow_group_alias,default_group_alias,vtiger_search_dead,vtiger_status_call,survey_third_digit, survey_third_audio_file,survey_third_status,survey_third_exten,survey_fourth_digit,survey_fourth_audio_file,survey_fourth_status,survey_fourth_exten, drop_lockout_time,quick_transfer_button,prepopulate_transfer_preset,drop_rate_group,view_calls_in_queue,view_calls_in_queue_launch,grab_calls_in_queue, call_requeue_button,pause_after_each_call,no_hopper_dialing,agent_dial_owner_only,agent_display_dialable_leads,web_form_address_two,waitforsilence_options, agent_select_territories,campaign_calldate,crm_popup_login,crm_login_address,timer_action,timer_action_message,timer_action_seconds, start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,use_custom_cid,scheduled_callbacks_alert, queuemetrics_callstatus_override,extension_appended_cidname,scheduled_callbacks_count,manual_dial_override,blind_monitor_warning,blind_monitor_message,blind_monitor_filename, inbound_queue_no_dial,timer_action_destination,enable_xfer_presets,hide_xfer_number_to_dial,manual_dial_prefix,customer_3way_hangup_logging,customer_3way_hangup_seconds, customer_3way_hangup_action,ivr_park_call,ivr_park_call_agi,manual_preview_dial,realtime_agent_time_stats,use_auto_hopper,auto_hopper_multi, auto_hopper_level,auto_trim_hopper,api_manual_dial,manual_dial_call_time_check,display_leads_count,lead_order_randomize,lead_order_secondary, per_call_notes,my_callback_option,agent_lead_search,agent_lead_search_method,queuemetrics_phone_environment,auto_pause_precall,auto_pause_precall_code, auto_resume_precall,manual_dial_cid,post_phone_time_diff_alert,custom_3way_button_transfer,available_only_tally_threshold,available_only_tally_threshold_agents,dial_level_threshold, dial_level_threshold_agents,safe_harbor_audio,safe_harbor_menu_id,survey_menu_id,callback_days_limit,dl_diff_target_method,disable_dispo_screen, disable_dispo_status,screen_labels,status_display_fields,na_call_url,survey_recording,pllb_grouping,pllb_grouping_limit, call_count_limit,call_count_target,callback_hours_block,callback_list_calltime,user_group,hopper_vlc_dup_check,in_group_dial, in_group_dial_select,safe_harbor_audio_field,pause_after_next_call,owner_populate,use_other_campaign_dnc,allow_emails,amd_inbound_group, amd_callmenu,survey_wait_sec,manual_dial_lead_id,dead_max,dead_max_dispo,dispo_max,dispo_max_dispo, pause_max,max_inbound_calls,manual_dial_search_checkbox,hide_call_log_info,timer_alt_seconds,wrapup_bypass,wrapup_after_hotkey, callback_active_limit,callback_active_limit_override,allow_chats,comments_all_tabs,comments_dispo_screen,comments_callback_screen,qc_comment_history, show_previous_callback,clear_script,cpd_unknown_action,manual_dial_search_filter,web_form_address_three,manual_dial_override_field,status_display_ingroup, customer_gone_seconds,agent_display_fields,am_message_wildcards,manual_dial_timeout,routing_initiated_recordings,manual_dial_hopper_check,callback_useronly_move_minutes, ofcom_uk_drop_calc ) VALUES('$campaign_id', '$campaign_name', '$dial_method_FROMARRAY', '$auto_dial_level_FROMARRAY', '$active_FROMARRAY','$dial_status_a_FROMARRAY','$dial_status_b_FROMARRAY','$dial_status_c_FROMARRAY','$dial_status_d_FROMARRAY','$dial_status_e_FROMARRAY','$lead_order_FROMARRAY', '$park_ext_FROMARRAY','$park_file_name_FROMARRAY','$web_form_address_FROMARRAY','$allow_closers_FROMARRAY','$hopper_level_FROMARRAY','$next_agent_call_FROMARRAY','$local_call_time_FROMARRAY', '$voicemail_ext_FROMARRAY','$dial_timeout_FROMARRAY','$dial_prefix_FROMARRAY','$campaign_cid_FROMARRAY','$campaign_vdad_exten_FROMARRAY','$campaign_rec_exten_FROMARRAY','$campaign_recording_FROMARRAY', '$campaign_rec_filename_FROMARRAY','$campaign_script_FROMARRAY','$get_call_launch_FROMARRAY','$am_message_exten_FROMARRAY','$amd_send_to_vmx_FROMARRAY','$xferconf_a_dtmf_FROMARRAY','$xferconf_a_number_FROMARRAY', '$xferconf_b_dtmf_FROMARRAY','$xferconf_b_number_FROMARRAY','$alt_number_dialing_FROMARRAY','$scheduled_callbacks_FROMARRAY','$lead_filter_id_FROMARRAY','$drop_call_seconds_FROMARRAY','$drop_action_FROMARRAY', '$safe_harbor_exten_FROMARRAY','$display_dialable_count_FROMARRAY','$wrapup_seconds_FROMARRAY','$wrapup_message_FROMARRAY','$closer_campaigns_FROMARRAY','$use_internal_dnc_FROMARRAY','$allcalls_delay_FROMARRAY', '$omit_phone_code_FROMARRAY','$available_only_ratio_tally_FROMARRAY','$adaptive_dropped_percentage_FROMARRAY','$adaptive_maximum_level_FROMARRAY','$adaptive_latest_server_time_FROMARRAY','$adaptive_intensity_FROMARRAY','$adaptive_dl_diff_target_FROMARRAY', '$concurrent_transfers_FROMARRAY','$auto_alt_dial_FROMARRAY','$auto_alt_dial_statuses_FROMARRAY','$agent_pause_codes_active_FROMARRAY','$campaign_description_FROMARRAY','$campaign_changedate_FROMARRAY','$campaign_stats_refresh_FROMARRAY', '$campaign_logindate_FROMARRAY','$dial_statuses_FROMARRAY','$disable_alter_custdata_FROMARRAY','$no_hopper_leads_logins_FROMARRAY','$list_order_mix_FROMARRAY','$campaign_allow_inbound_FROMARRAY','$manual_dial_list_id_FROMARRAY', '$default_xfer_group_FROMARRAY','$xfer_groups_FROMARRAY','$queue_priority_FROMARRAY','$drop_inbound_group_FROMARRAY','$qc_enabled_FROMARRAY','$qc_statuses_FROMARRAY','$qc_lists_FROMARRAY', '$qc_shift_id_FROMARRAY','$qc_get_record_launch_FROMARRAY','$qc_show_recording_FROMARRAY','$qc_web_form_address_FROMARRAY','$qc_script_FROMARRAY','$survey_first_audio_file_FROMARRAY','$survey_dtmf_digits_FROMARRAY', '$survey_ni_digit_FROMARRAY','$survey_opt_in_audio_file_FROMARRAY','$survey_ni_audio_file_FROMARRAY','$survey_method_FROMARRAY','$survey_no_response_action_FROMARRAY','$survey_ni_status_FROMARRAY','', '$survey_xfer_exten_FROMARRAY','$survey_camp_record_dir_FROMARRAY','$disable_alter_custphone_FROMARRAY','$display_queue_count_FROMARRAY','$manual_dial_filter_FROMARRAY','$agent_clipboard_copy_FROMARRAY','$agent_extended_alt_dial_FROMARRAY', '$use_campaign_dnc_FROMARRAY','$three_way_call_cid_FROMARRAY','$three_way_dial_prefix_FROMARRAY','$web_form_target_FROMARRAY','$vtiger_search_category_FROMARRAY','$vtiger_create_call_record_FROMARRAY','$vtiger_create_lead_record_FROMARRAY', '$vtiger_screen_login_FROMARRAY','$cpd_amd_action_FROMARRAY','$agent_allow_group_alias_FROMARRAY','$default_group_alias_FROMARRAY','$vtiger_search_dead_FROMARRAY','$vtiger_status_call_FROMARRAY','$survey_third_digit_FROMARRAY', '$survey_third_audio_file_FROMARRAY','$survey_third_status_FROMARRAY','$survey_third_exten_FROMARRAY','$survey_fourth_digit_FROMARRAY','$survey_fourth_audio_file_FROMARRAY','$survey_fourth_status_FROMARRAY','$survey_fourth_exten_FROMARRAY', '$drop_lockout_time_FROMARRAY','$quick_transfer_button_FROMARRAY','$prepopulate_transfer_preset_FROMARRAY','$drop_rate_group_FROMARRAY','$view_calls_in_queue_FROMARRAY','$view_calls_in_queue_launch_FROMARRAY','$grab_calls_in_queue_FROMARRAY', '$call_requeue_button_FROMARRAY','$pause_after_each_call_FROMARRAY','$no_hopper_dialing_FROMARRAY','$agent_dial_owner_only_FROMARRAY','$agent_display_dialable_leads_FROMARRAY','$web_form_address_two_FROMARRAY','$waitforsilence_options_FROMARRAY', '$agent_select_territories_FROMARRAY','$campaign_calldate_FROMARRAY','$crm_popup_login_FROMARRAY','$crm_login_address_FROMARRAY','$timer_action_FROMARRAY','$timer_action_message_FROMARRAY','$timer_action_seconds_FROMARRAY', '$start_call_url_FROMARRAY','$dispo_call_url_FROMARRAY','$xferconf_c_number_FROMARRAY','$xferconf_d_number_FROMARRAY','$xferconf_e_number_FROMARRAY','$use_custom_cid_FROMARRAY','$scheduled_callbacks_alert_FROMARRAY', '$queuemetrics_callstatus_override_FROMARRAY','$extension_appended_cidname_FROMARRAY','$scheduled_callbacks_count_FROMARRAY','$manual_dial_override_FROMARRAY','$blind_monitor_warning_FROMARRAY','$blind_monitor_message_FROMARRAY','$blind_monitor_filename_FROMARRAY', '$inbound_queue_no_dial_FROMARRAY','$timer_action_destination_FROMARRAY','$enable_xfer_presets_FROMARRAY','$hide_xfer_number_to_dial_FROMARRAY','$manual_dial_prefix_FROMARRAY','$customer_3way_hangup_logging_FROMARRAY','$customer_3way_hangup_seconds_FROMARRAY', '$customer_3way_hangup_action_FROMARRAY','$ivr_park_call_FROMARRAY','$ivr_park_call_agi_FROMARRAY','$manual_preview_dial_FROMARRAY','$realtime_agent_time_stats_FROMARRAY','$use_auto_hopper_FROMARRAY','$auto_hopper_multi_FROMARRAY', '$auto_hopper_level_FROMARRAY','$auto_trim_hopper_FROMARRAY','$api_manual_dial_FROMARRAY','$manual_dial_call_time_check_FROMARRAY','$display_leads_count_FROMARRAY','$lead_order_randomize_FROMARRAY','$lead_order_secondary_FROMARRAY', '$per_call_notes_FROMARRAY','$my_callback_option_FROMARRAY','$agent_lead_search_FROMARRAY','$agent_lead_search_method_FROMARRAY','$queuemetrics_phone_environment_FROMARRAY','$auto_pause_precall_FROMARRAY','$auto_pause_precall_code_FROMARRAY', '$auto_resume_precall_FROMARRAY','$manual_dial_cid_FROMARRAY','$post_phone_time_diff_alert_FROMARRAY','$custom_3way_button_transfer_FROMARRAY','$available_only_tally_threshold_FROMARRAY','$available_only_tally_threshold_agents_FROMARRAY','$dial_level_threshold_FROMARRAY', '$dial_level_threshold_agents_FROMARRAY','$safe_harbor_audio_FROMARRAY','$safe_harbor_menu_id_FROMARRAY','$survey_menu_id_FROMARRAY','$callback_days_limit_FROMARRAY','$dl_diff_target_method_FROMARRAY','$disable_dispo_screen_FROMARRAY', '$disable_dispo_status_FROMARRAY','$screen_labels_FROMARRAY','$status_display_fields_FROMARRAY','$na_call_url_FROMARRAY','$survey_recording_FROMARRAY','$pllb_grouping_FROMARRAY','$pllb_grouping_limit_FROMARRAY', '$call_count_limit_FROMARRAY','$call_count_target_FROMARRAY','$callback_hours_block_FROMARRAY','$callback_list_calltime_FROMARRAY','$user_group_FROMARRAY','$hopper_vlc_dup_check_FROMARRAY','$in_group_dial_FROMARRAY', '$in_group_dial_select_FROMARRAY','$safe_harbor_audio_field_FROMARRAY','$pause_after_next_call_FROMARRAY','$owner_populate_FROMARRAY','$use_other_campaign_dnc_FROMARRAY','$allow_emails_FROMARRAY','$amd_inbound_group_FROMARRAY', '$amd_callmenu_FROMARRAY','$survey_wait_sec_FROMARRAY','$manual_dial_lead_id_FROMARRAY','$dead_max_FROMARRAY','$dead_max_dispo_FROMARRAY','$dispo_max_FROMARRAY','$dispo_max_dispo_FROMARRAY', '$pause_max_FROMARRAY','$max_inbound_calls_FROMARRAY','$manual_dial_search_checkbox_FROMARRAY','$hide_call_log_info_FROMARRAY','$timer_alt_seconds_FROMARRAY','$wrapup_bypass_FROMARRAY','$wrapup_after_hotkey_FROMARRAY', '$callback_active_limit_FROMARRAY','$callback_active_limit_override_FROMARRAY','$allow_chats_FROMARRAY','$comments_all_tabs_FROMARRAY','$comments_dispo_screen_FROMARRAY','$comments_callback_screen_FROMARRAY','$qc_comment_history_FROMARRAY', '$show_previous_callback_FROMARRAY','$clear_script_FROMARRAY','$cpd_unknown_action_FROMARRAY','$manual_dial_search_filter_FROMARRAY','$web_form_address_three_FROMARRAY','$manual_dial_override_field_FROMARRAY','$status_display_ingroup_FROMARRAY', '$customer_gone_seconds_FROMARRAY','$agent_display_fields_FROMARRAY','$am_message_wildcards_FROMARRAY','$manual_dial_timeout_FROMARRAY','$routing_initiated_recordings_FROMARRAY','$manual_dial_hopper_check_FROMARRAY','$callback_useronly_move_minutes_FROMARRAY', '$ofcom_uk_drop_calc_FROMARRAY' );";
						// $apiresults = array("result" => "success", "query" => $queryAddCopy, "godetails" => $dataGOCampaign);
						$rsltvAddCopy = mysqli_query($link, $queryAddCopy);
						$queryVCS = "INSERT INTO vicidial_campaign_stats (campaign_id) values('$campaign_id')";
						$rsltvVCS = mysqli_query($link, $queryVCS);
							
						$allowed_campaigns = go_getall_allowed_campaigns($groupId);
						
						if (strlen($allowed_campaigns) < 1) { 
							$allowed_campaigns = " -"; 
						}
							
						$queryVUG = "UPDATE vicidial_user_groups SET allowed_campaigns=' {$campaign_id}$allowed_campaigns' WHERE user_group='$tenant_id'";
						$rsltvVUG = mysqli_query($link, $queryVUG);
				
						$groupId = go_get_groupid($goUser);
						
						if (!checkIfTenant($groupId)) {
							$ul = "WHERE campaign_id='$campaign_id'";
						} else {
							$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
						}
							
						$query = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1";
						$rsltv = mysqli_query($link, $query);
						$countResult = mysqli_num_rows($rsltv);
						if($countResult > 0) {
							$campType = $dataGOCampaign['campaign_type'];
							$SQLdate = date("Y-m-d H:i:s");
							//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Campaign $campaign_id','')";
							//$rsltvLog = mysqli_query($linkgo, $queryLog);
							$log_id = log_action($linkgo, 'COPY', $log_user, $ip_address, "Copied campaign settings from $copy_from_campaign to $campaign_id", $log_group, $queryAddCopy);
							
							if(!empty($location))
								$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type, location_id) values('$campaign_id', '$campType', '$location')";
							else
								$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type) values('$campaign_id', '$campType')";
							
							$rsltvGoCampaign = mysqli_query($linkgo, $queryGoCampaign);
							
							$apiresults = array("result" => "success");
						} else {
							$err_msg = error_handle("10010");
							$apiresults = array("code" => "10010", "result" => $err_msg);
						}
					}else{
						// cancel copy
						$apiresults = array("result" => "error", "message" => "8 post parameters are only needed for copy campaign(goUser,goPass,goAction,responsetype,campaign_id,campaign_name,campaign_type,copy_from_campaign). Please check your API url and post parameters.");
					}
						
				}
            }
        }
    }

?>
