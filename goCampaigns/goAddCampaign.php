<?php
   ####################################################
   #### Name: goAddList.php                        ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    
   include_once("../goFunctions.php");
   
    if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf")) {
            $conf_path = "{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf";
    } elseif (file_exists("/etc/goautodial.conf")) {
            $conf_path = "/etc/goautodial.conf";
    } else {
            die ($lang['go_conf_file_not_found']);
    }
		
	### POST or GET Variables
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];

	### Inbound Campaign
        $campaign_id 					= $_REQUEST['campaign_id'];
        $campaign_name 					= $_REQUEST['campaign_name'];
        $campaign_type 					= strtoupper($_REQUEST['campaign_type']);
        $active 						= $_REQUEST['active'];
        $dial_method 					= $_REQUEST['dial_method'];
        $dial_statuses 					= $_REQUEST['dial_statuses'];
        $lead_order 					= $_REQUEST['lead_order'];
        $allow_closers 					= $_REQUEST['allow_closers'];
        $hopper_level 					= $_REQUEST['hopper_level'];
        $auto_dial_level 				= $_REQUEST['auto_dial_level'];
        $dial_prefix 					= $_REQUEST['dial_prefix'];
        $campaign_changedate 			= $_REQUEST['campaign_changedate'];
        $campaign_stats_refresh 		= $_REQUEST['campaign_stats_refresh'];
        $campaign_vdad_exten 			= $_REQUEST['campaign_vdad_exten'];
        $campaign_recording 			= $_REQUEST['campaign_recording'];
        $campaign_rec_filename 			= $_REQUEST['campaign_rec_filename'];
        $scheduled_callbacks 			= $_REQUEST['scheduled_callbacks'];
        $scheduled_callbacks_alert 		= $_REQUEST['scheduled_callbacks_alert'];
        $no_hopper_leads_logins 		= $_REQUEST['no_hopper_leads_logins'];
        $use_internal_dnc 				= $_REQUEST['use_internal_dnc'];
        $use_campaign_dnc 				= $_REQUEST['use_campaign_dnc'];
        $campaign_cid 					= $_REQUEST['campaign_cid'];
        $user_group 					= $_REQUEST['user_group'];
        $drop_call_seconds 				= $_REQUEST['drop_call_seconds'];
        $goUsers 						= $_REQUEST['goUser'];
		$values 						= $_REQUEST['items'];
        $did_pattern 					= $_REQUEST['did_tfn_extension'];
        $group_color 					= $_REQUEST['group_color'];
        $call_route 					= $_REQUEST['call_route'];
        $survey_type 					= $_REQUEST['survey_type'];
        $number_channels 				= $_REQUEST['no_channels'];
		$copy_from_campaign 			= $_REQUEST['copy_from_campaign'];			
		$list_id 						= $_REQUEST['list_id'];						
		$country 						= $_REQUEST['country'];						
		$check_for_duplicates 			= $_REQUEST['check_for_duplicates'];			
		$dial_prefix 					= $_REQUEST['dial_prefix'];
		$custom_dial_prefix				= $_REQUEST['custom_dial_prefix'];
		$status 						= $_REQUEST['status'];									
		$script 						= $_REQUEST['script'];						
		$answering_machine_detection 	= $_REQUEST['answering_machine_detection'];	
		$caller_id 						= $_REQUEST['caller_id']; 					
		$force_reset_hopper 			= $_REQUEST['force_reset_hopper'];			
		$inbound_man 					= $_REQUEST['inbound_man'];					
		$phone_numbers 					= $_REQUEST['phone_numbers'];
		$lead_file						= $_FILES['lead_file']['tmp_name'];
		$leads							= $_FILES['leads']['tmp_name'];
		
		$call_time 						= $_REQUEST['call_time'];
		$dial_status 					= $_REQUEST['dial_status'];
		$list_order 					= $_REQUEST['list_order'];
		$lead_filter 					= $_REQUEST['lead_filter'];
		$dial_timeout 					= $_REQUEST['dial_timeout'];
		$manual_dial_prefix 			= $_REQUEST['manual_dial_prefix'];
		$call_launch 					= $_REQUEST['call_lunch'];
		$answering_machine_message 		= $_REQUEST['answering_machine_message'];
		$pause_codes 					= $_REQUEST['pause_codes'];
		$manual_dial_filter 			= $_REQUEST['manual_dial_filter'];
		$manual_dial_list_id 			= $_REQUEST['manual_dial_list_id'];
		$availability_only_tally 		= $_REQUEST['availability_only_tally'];
		$recording_filename 			= $_REQUEST['recording_filename'];
		$next_agent_call 				= $_REQUEST['next_agent_call'];
		$caller_id_3_way_call 			= $_REQUEST['caller_id_3_way_call'];
		$dial_prefix_3_way_call 		= $_REQUEST['dial_prefix_3_way_call'];
		$three_way_hangup_logging 		= $_REQUEST['three_way_hangup_logging'];
		$three_way_hangup_seconds 		= $_REQUEST['three_way_hangup_seconds'];
		$three_way_hangup_action 		= $_REQUEST['three_way_hangup_action'];
		$reset_leads_on_hopper 			= $_REQUEST['reset_leads_on_hopper'];

		### Default values 
    	$defActive = array("Y","N");
    	$defType = array("OUTBOUND", "INBOUND", "BLENDED", "SURVEY");
		
		
		if($dial_prefix == "CUSTOM"){
				$sippy_dial_prefix = $custom_dial_prefix;
		}else{
				$sippy_dial_prefix = $dial_prefix;
		}

    if($campaign_id == null ||  $campaign_type == null || $campaign_name == null) {
        $apiresults = array("result" => "Error: Set a value for Campaign ID, Campaign Type or Campaign Name.");
    } else {
    	if(!in_array($campaign_type,$defType) && $campaign_type != null) {
            $apiresults = array("result" => "Error: Default value for campaign type is OUTBOUND, INBOUND, BLENDED and  SURVEY only.");
        } else {
        	if (!checkIfTenant($groupId)) {
                $ul = "WHERE campaign_id='$campaign_id'";
            } else {
                $ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
            }       
            
            $queryCampaign = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
            $rsltvCampaign = mysqli_query($link, $queryCampaign);
            $countResultCampaign = mysqli_num_rows($rsltvCampaign);
		    
            if($countResultCampaign > 0) {
                $apiresults = array("result" => "Error: Campaign Already Exist!");
            } else {
            	$campaign_id = mysqli_real_escape_string($link, $campaign_id);
                $campaign_desc = mysqli_real_escape_string($link, str_replace('+',' ',$campaign_name));
                $SQLdate = date("Y-m-d H:i:s");
                $NOW = date("Y-m-d");
				
                // Outbound Campaign here
                if($campaign_type == "OUTBOUND"){
						
                	$groupId = go_get_groupid($goUser);

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
												campaign_id, campaign_name, active,	dial_method, dial_status_a,				
												dial_statuses, lead_order, allow_closers, hopper_level, auto_dial_level,			
												next_agent_call, local_call_time, dial_prefix, get_call_launch, campaign_changedate,		
												campaign_stats_refresh, list_order_mix, dial_timeout, campaign_vdad_exten, campaign_recording,			
												campaign_rec_filename, scheduled_callbacks, scheduled_callbacks_alert, no_hopper_leads_logins, use_internal_dnc,			
												use_campaign_dnc, available_only_ratio_tally, campaign_cid,	manual_dial_filter,	user_group,					
												manual_dial_list_id, drop_call_seconds
										)
										VALUES(
												'$campaign_id','$campaign_desc','Y','$dial_method','NEW',
												' N NA A AA DROP B NEW -','DOWN','Y','100','$auto_dial_level',
												'oldest_call_finish','$local_call_time','$sippy_dial_prefix','NONE','$SQLdate',
												'Y','DISABLED','30','$answering_machine_detection','$campaign_recording',
												'FULLDATE_CUSTPHONE_CAMPAIGN_AGENT','Y','BLINK_RED','Y','Y',
												'Y','Y','5164536886','DNC_ONLY','$tenant_id',
												'${$tenant_id}0','7'
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
							### Admin logs
								$SQLdate = date("Y-m-d H:i:s");
								$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Campaign $campaign_id','')";
								$rsltvLog = mysqli_query($linkgo, $queryLog);
								
								$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type) values('$campaign_id', '$campaign_type')";
								$rsltvGoCampaign = mysqli_query($linkgo, $queryGoCampaign);
					
								$apiresults = array("result" => "success");
							} else {
								$apiresults = array("result" => "Error: Failed to add campaign.");
							}
                		}
	                }

                }
                // End of OUTBOUND

                // Inbound Campaign here
                if($campaign_type == "INBOUND"){
                	$campaign_id = mysqli_real_escape_string($link, $campaign_id);
	                $campaign_desc = mysqli_real_escape_string($link, str_replace('+',' ',$campaign_name));
	                $SQLdate = date("Y-m-d H:i:s");
	                $NOW = date("Y-m-d");
	                $groupId = go_get_groupid($goUser);

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
											three_way_call_cid, three_way_dial_prefix, customer_3way_hangup_logging, customer_3way_hangup_seconds, customer_3way_hangup_action
										)
										VALUES(
											'$campaign_id','$campaign_desc','Y','$dial_method','$dial_status',
											' N NA A AA DROP B NEW -','DOWN','Y','100','$auto_dial_level',
											'$next_agent_call','$call_time','$sippy_dial_prefix','$call_launch','$SQLdate',
											'Y','$list_order','$dial_timeout','$answering_machine_detection','$campaign_recording',
											'$recording_filename','Y','BLINK_RED','Y','Y',
											'Y','$availability_only_tally','5164536886','$manual_dial_filter','$tenant_id',
											'$manual_dial_list_id','7','$manual_dial_prefix','$answering_machine_message','$pause_codes',
											'$caller_id_3_way_call','$dial_prefix_3_way_call','$three_way_hangup_logging','$three_way_hangup_seconds','$three_way_hangup_action'
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
		
						 ### Admin logs
							$SQLdate = date("Y-m-d H:i:s");
							$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Campaign $campaign_id','');";
							$rsltvLog = mysqli_query($linkgo, $queryLog);
							
							$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type) values('$campaign_id', '$campaign_type')";
						    $rsltvGoCampaign = mysqli_query($linkgo, $queryGoCampaign);
		
		
							$apiresults = array("result" => "success");
						} else {
							$apiresults = array("result" => "Error: Failed to add campaign.");
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

	                if($groupColor == null){
	                    $apiresults = array("result" => "Error: Set value for group_color");
	                } else {
	                	if(!in_array($callRoute,$defCallRoute) || $callRoute == null) {
		                    $apiresults = array("result" => "Error: Default value for call route is INGROUP, IVR, AGENT and  VOICEMAIL only.");
		                } else {
		                	$groupId = go_get_groupid($goUser);

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
	                                $queryAdd = "INSERT INTO vicidial_inbound_groups (group_id,group_name,group_color,active,web_form_address,voicemail_ext,next_agent_call,
					                                                                    fronter_display,ingroup_script,get_call_launch,web_form_address_two,start_call_url,dispo_call_url,add_lead_url,
					                                                                    call_time_id,user_group)
					                                    VALUES('$group_id','$group_name','$groupColor','Y','','','oldest_call_finish','Y','NONE','NONE','','',
					                                                                    '','','$local_call_time','$tenant_id')";

	                              	$rsltvAdd = mysqli_query($link, $queryAdd);

	                              	$querySelect = "SELECT campaign_id FROM vicidial_campaigns WHERE campaign_id = '$campaign_id'";
			                        $rsltvSelect = mysqli_query($link, $querySelect);
			                        $campNum1 = mysqli_num_rows($rsltvSelect);

			                        if ($campNum1 < 1){
			                        	// Insert new Inbound Campaign
                                        $manualDialPrefix = '';
                                        $manualDialPrefixVal = '';
                                        $local_call_time = "9am-9pm";

                                        if ($campType=='Inbound')
                                        {
                                                $manualDialPrefix = ',manual_dial_prefix';
                                                $manualDialPrefixVal = ",'$manual_dial_prefix'";
                                        }

                                        #Sippy
		                                #jin
		                                $auth_user = $goUsers;
		                                //$VARSERVTYPE = $this->config->item('VARSERVTYPE');
		                                //if($VARSERVTYPE == "cloud"){
		                                //if($VARSERVTYPE == "cloud" || $VARSERVTYPE == "gofree") {
		                                //        $sippy_dial_prefix = "8888".$auth_user;
		                                //} elseif($VARSERVTYPE == "gopackages") {
		                                //        $sippy_dial_prefix = "9";
		                                //}

		                                $queryInsert = "INSERT INTO vicidial_campaigns (
																campaign_id, campaign_name, campaign_description, active, dial_method,
																dial_status_a, dial_statuses, lead_order, park_ext, park_file_name,
																web_form_address, allow_closers, hopper_level, auto_dial_level, available_only_ratio_tally,
																next_agent_call, local_call_time, dial_prefix, voicemail_ext, campaign_script,
																get_call_launch, campaign_changedate, campaign_stats_refresh, list_order_mix, web_form_address_two,
																start_call_url, dispo_call_url, dial_timeout, campaign_vdad_exten, campaign_recording,
																campaign_rec_filename, scheduled_callbacks, scheduled_callbacks_alert, no_hopper_leads_logins, per_call_notes,
																agent_lead_search, campaign_allow_inbound, use_internal_dnc, use_campaign_dnc, campaign_cid,
																manual_dial_filter, user_group, manual_dial_list_id, drop_call_seconds $manualDialPrefix
														)
														VALUES (
																'$campaign_id','$campaign_desc','','Y','$dial_method',
																'NEW',' N NA A AA DROP B NEW -','DOWN','','',
																'','Y','100','$auto_dial_level','Y',
																'oldest_call_finish','$local_call_time','$sippy_dial_prefix','','',
																'','$SQLdate','Y','DISABLED','',
																'','','30','$answering_machine_detection','$campaign_recording',
																'FULLDATE_CUSTPHONE_CAMPAIGN_AGENT','Y','BLINK_RED','Y','ENABLED',
																'ENABLED','Y','Y','Y','5164536886',
																'DNC_ONLY','$tenant_id','{$tenant_id}0','7' $manualDialPrefixVal
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

		                        	switch ($callRoute){
                                		case "INGROUP":
                                        	$queryING = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,
                                                                                user_route_settings_ingroup,campaign_id,record_call,filter_list_id,
                                                                                filter_campaign_id,group_id,server_ip,user_group)
                                                                                VALUES ('$didPattern','$didDesc','Y','IN_GROUP',
                                                                                '$group_id','$campaign_id','Y','$list_id',
                                                                                '$campaign_id','$group_id','10.0.0.12','$tenant_id')";
											$rsltvING = mysqli_query($link, $queryING);
                                        break;

                                		case "IVR":
                                        	$menuID = "$cntX";
                                       		$queryVCM = "INSERT INTO vicidial_call_menu (menu_id,menu_name,user_group) values('$menuID','$menuID Inbound Call Menu','$tenant_id')";
											$rsltvVCM = mysqli_query($link, $queryVCM);
                                        	$queryVID = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,campaign_id,record_call,
                                                                                filter_list_id,filter_campaign_id,server_ip,menu_id,user_group)
                                                                                VALUES ('$didPattern','$didDesc','Y','CALLMENU','$campaign_id','Y','$list_id','$campaign_id','10.0.0.12','$menuID','$tenant_id')";
											$rsltvVID = mysqli_query($link, $queryVID);
                                        break;

                                		case "AGENT":
                                        	$queryVID = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,user_route_settings_ingroup,
                                                                                campaign_id,record_call,filter_list_id,filter_campaign_id,user,group_id,server_ip,user_group)
                                                                                VALUES ('$didPattern','$didDesc','Y','AGENT','$group_id','$campaign_id','Y','$list_id','$campaign_id','$emailORagent',
                                                                                '$group_id','10.10.10.12','$tenant_id')";
											$rsltvVID = mysqli_query($link, $queryVID);
                                        break;

                                		case "VOICEMAIL":
                                        	if ($emailORagent=='undefined')
                                                $emailORagent='';

                                        	$queryVV = "INSERT INTO vicidial_voicemail SET voicemail_id='$campaign_id',pass='$campaign_id',email='$emailORagent',fullname='$campaign_id VOICEMAIL',active='Y',user_group='$tenant_id'";
											$rsltvVV = mysqli_query($link, $queryVV);

                                        	$queryVID = "INSERT INTO vicidial_inbound_dids (did_pattern,did_description,did_active,did_route,user_route_settings_ingroup,
                                                                                campaign_id,record_call,filter_list_id,filter_campaign_id,voicemail_ext,user_group)
                                                                                VALUES ('$didPattern','$didDesc','Y','VOICEMAIL','$group_id','$campaign_id','Y','$list_id','$campaign_id','$campaign_id','$tenant_id')";
											$rsltvVID = mysqli_query($link, $queryVID);
                                        break;
                        			}

                        			$queryUpdateVC = "UPDATE vicidial_campaigns SET closer_campaigns = ' $group_id -',campaign_allow_inbound = 'Y' WHERE campaign_id = '$campaign_id'";
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
                                    $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Campaign $campaign_id','');";
                                    $rsltvLog = mysqli_query($linkgo, $queryLog);
										
									$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type) values('$campaign_id', '$campaign_type')";
								    $rsltvGoCampaign = mysqli_query($linkgo, $queryGoCampaign);
										
			                    $apiresults = array("result" => "success");
			                } else {
			                    $apiresults = array("result" => "Error: Failed to add campaign.");
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
                        $apiresults = array("result" => "Error: Default value for survey type is BROADCAST or PRESS1 only.");
	                } else {
	                	if(!in_array($numChannels,$defNumCha) && $numChannels == null) {
	                        $apiresults = array("result" => "Error: Default value for number channel is 1,5,10,15,20 or 30 only.");
	                	} else {
	                		$groupId = go_get_groupid($goUser);

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
			                	#jin
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
                                    #Sippy
                                    $auth_user = $goUsers;

                                    //if($VARSERVTYPE == "cloud" || $VARSERVTYPE == "gofree") {
                                    //        $sippy_dial_prefix = "8888".$auth_user;
                                    //} elseif($VARSERVTYPE == "gopackages") {
                                    //        $sippy_dial_prefix = "9";
                                    //}

                                    //if($VARSERVTYPE == "cloud"){  
                                    $queryInsert = "INSERT INTO vicidial_campaigns (campaign_id,campaign_name,campaign_description,active,dial_method,
                                                                                    dial_status_a,dial_statuses,lead_order,park_ext,park_file_name,
                                                                                    web_form_address,allow_closers,hopper_level,auto_dial_level,
                                                                                    available_only_ratio_tally,next_agent_call,local_call_time,dial_prefix,voicemail_ext,
                                                                                    campaign_script,get_call_launch,campaign_changedate,campaign_stats_refresh,
                                                                                    list_order_mix,web_form_address_two,start_call_url,dispo_call_url,
                                                                                    dial_timeout,campaign_vdad_exten,campaign_recording,
                                                                                    campaign_rec_filename,scheduled_callbacks,scheduled_callbacks_alert,
                                                                                    no_hopper_leads_logins,per_call_notes,agent_lead_search,use_internal_dnc,
                                                                                    use_campaign_dnc,campaign_cid,user_group,manual_dial_list_id,drop_call_seconds,survey_opt_in_audio_file)
                                                                                    VALUES('$campaign_id','$campaign_desc','','N','$dial_method','NEW',
                                                                                    ' N NA A AA DROP B NEW -','DOWN','','','','Y','100','$auto_dial_level',
                                                                                    'Y','random','$local_call_time','$sippy_dial_prefix','','','','$SQLdate','Y','DISABLED','','','',
                                                                                    '30','$routingExten','$campaign_recording','FULLDATE_CUSTPHONE_CAMPAIGN_AGENT','Y',
                                                                                    'BLINK_RED','Y','ENABLED','ENABLED','Y','Y','5164536886','$tenant_id','{$tenant_id}0','7','')";
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
                                        $agvar= mt_rand(0,10);
                                        $queryVU = "SELECT user FROM vicidial_users WHERE user='$agvar';";
										$rsltvVU = mysqli_query($link, $queryVU);
                                        $user_exist = mysqli_num_rows($rsltvVU);
                                    }
                                    while ($user_exist > 0);

                                    $pass= substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

                                    $agent_user="$agvar";
                                    $agent_name="Survey Agent - $campaign_id";
                                    $agent_phone="$agvar";

                                    $queryVRA = "INSERT INTO vicidial_remote_agents (user_start,number_of_lines,server_ip,conf_exten,status,campaign_id,closer_campaigns) values('$agent_user','$numChannels','$main_server_ip','8300','INACTIVE','$campaign_id','')";
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
				        		### Admin logs
	                            $SQLdate = date("Y-m-d H:i:s");
	                            $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Campaign $campaign_id','');";
	                            $rsltvLog = mysqli_query($linkgo, $queryLog);

								$queryGoCampaign = "INSERT INTO go_campaigns (campaign_id, campaign_type) values('$campaign_id', '$campaign_type')";
								$rsltvGoCampaign = mysqli_query($linkgo, $queryGoCampaign);


			                    $apiresults = array("result" => "success");
			                } else {
			                    $apiresults = array("result" => "Error: Failed to add campaign.");
			                }
						}
					}
                }
                // End of SURVEY

            }
        }
    }

?>
