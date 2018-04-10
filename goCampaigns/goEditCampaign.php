<?php
   //////////////////////////////////#
   //# Name: goEditCampaign.php                   //#
   //# Description: API to edit specific campaign //#
   //# Version: 0.9                               //#
   //# Copyright: GOAutoDial Ltd. (c) 2011-2015   //#
   //# Written by: Jerico James Milo              //#
   //# License: AGPLv2                            //#
   //////////////////////////////////#
	// POST or GET Variables
	$goUser 						= $_REQUEST['goUser'];
	$ip_address 					= $_REQUEST['hostname'];
	$log_user 						= $_REQUEST['log_user'];
	$log_group 						= $_REQUEST['log_group'];
	$campaign_id 					= $_REQUEST['campaign_id'];
	$campaign_name 					= $_REQUEST['campaign_name'];
	$campaign_desc 					= $_REQUEST['campaign_desc'];
	$active 						= strtoupper($_REQUEST['active']);;
	$dial_method 					= strtoupper($_REQUEST['dial_method']);
	$auto_dial_level				= $_REQUEST['auto_dial_level'];
	$auto_dial_level_adv 			= $_REQUEST['auto_dial_level_adv'];
	$dial_prefix 					= $_REQUEST['dial_prefix'];
	$custom_prefix 					= $_REQUEST['custom_prefix'];
	$campaign_script 				= $_REQUEST['campaign_script'];
	$webform						= $_REQUEST['web_form_address'];
	$campaign_cid 					= $_REQUEST['campaign_cid'];
	$campaign_recording 			= $_REQUEST['campaign_recording'];
	$campaign_vdad_exten 			= $_REQUEST['campaign_vdad_exten'];
	$local_call_time 				= $_REQUEST['local_call_time'];
	$hopper_level 					= $_REQUEST['hopper_level'];
	$force_reset_hopper 			= $_REQUEST['force_reset_hopper'];
	$dial_status 					= $_REQUEST['dial_status'];
	$lead_order 					= $_REQUEST['lead_order'];
	$lead_order_secondary 			= $_REQUEST['lead_order_secondary'];
	$lead_filter 					= $_REQUEST['lead_filter'];
	$dial_timeout 					= $_REQUEST['dial_timeout'];
	$manual_dial_prefix 			= $_REQUEST['manual_dial_prefix'];
	$get_call_launch 				= $_REQUEST['get_call_launch'];
	$am_message_exten 				= $_REQUEST['am_message_exten'];
	$am_message_chooser 			= $_REQUEST['am_message_chooser'];
	$agent_pause_codes_active 		= $_REQUEST['agent_pause_codes_active'];
	$manual_dial_filter 			= $_REQUEST['manual_dial_filter'];
	$use_internal_dnc 				= $_REQUEST['use_internal_dnc'];
	$use_campaign_dnc 				= $_REQUEST['use_campaign_dnc'];
	$manual_dial_list_id 			= $_REQUEST['manual_dial_list_id'];
	$available_only_ratio_tally 	= $_REQUEST['available_only_ratio_tally'];
	$campaign_rec_filename 			= $_REQUEST['campaign_rec_filename'];
	$next_agent_call 				= $_REQUEST['next_agent_call'];
	$xferconf_a_number 				= $_REQUEST["xferconf_a_number"];
	$xferconf_b_number 				= $_REQUEST["xferconf_b_number"];
	$three_way_call_cid 			= $_REQUEST['three_way_call_cid'];
	$three_way_dial_prefix 			= $_REQUEST['three_way_dial_prefix'];
	$customer_3way_hangup_logging 	= $_REQUEST['customer_3way_hangup_logging'];
	$customer_3way_hangup_seconds 	= $_REQUEST['customer_3way_hangup_seconds'];
	$customer_3way_hangup_action 	= $_REQUEST['customer_3way_hangup_action'];
	$inbound_man 					= $_REQUEST['inbound_man'];
	$campaign_allow_inbound			= $_REQUEST['campaign_allow_inbound'];
	$closer_campaigns				= $_REQUEST['closer_campaigns'];
	$xfer_groups					= $_REQUEST['xfer_groups'];
	$custom_fields_launch			= $_REQUEST['custom_fields_launch'];
	$campaign_type					= $_REQUEST['campaign_type'];
	$custom_fields_list_id			= $_REQUEST['custom_fields_list_id'];
	$per_call_notes 				= $_REQUEST['per_call_notes'];
	$url_tab_first_title			= $_REQUEST['url_tab_first_title'];
	$url_tab_first_url				= $_REQUEST['url_tab_first_url'];
	$url_tab_second_title			= $_REQUEST['url_tab_second_title'];
	$url_tab_second_url				= $_REQUEST['url_tab_second_url'];
	$agent_lead_search				= $_REQUEST['agent_lead_search'];
	$agent_lead_search_method 		= $_REQUEST['agent_lead_search_method'];
    $omit_phone_code 				= $_REQUEST['omit_phone_code'];
	$alt_number_dialing 			= $_REQUEST['alt_number_dialing'];
	$dynamic_cid 					= $_REQUEST['dynamic_cid'];
	$nextdial_seconds 				= $_REQUEST['nextdial_seconds'];
	$my_callback_option 			= $_REQUEST['my_callback_option'];
	$survey_first_audio_file 		= $_REQUEST['survey_first_audio_file'];
	$survey_method 					= $_REQUEST['survey_method'];
	$survey_menu_id 				= $_REQUEST['survey_menu_id'];
	$survey_dtmf_digits 			= $_REQUEST['survey_dtmf_digits'];
	$survey_xfer_exten 				= $_REQUEST['survey_xfer_exten'];
	$survey_ni_digit 				= $_REQUEST['survey_ni_digit'];
	$survey_ni_audio_file 			= $_REQUEST['survey_ni_audio_file'];
	$survey_ni_status 				= $_REQUEST['survey_ni_status'];
	$survey_third_digit 			= $_REQUEST['survey_third_digit'];
	$survey_third_audio_file 		= $_REQUEST['survey_third_audio_file'];
	$survey_third_status 			= $_REQUEST['survey_third_status'];
	$survey_third_exten 			= $_REQUEST['survey_third_exten'];
	$survey_fourth_digit 			= $_REQUEST['survey_fourth_digit'];
	$survey_fourth_audio_file 		= $_REQUEST['survey_fourth_audio_file'];
	$survey_fourth_status 			= $_REQUEST['survey_fourth_status'];
	$survey_fourth_exten 			= $_REQUEST['survey_fourth_exten'];
    $no_channels 					= $_REQUEST['no_channels'];
    $disable_alter_custdata 		= $_REQUEST['disable_alter_custdata'];
    $disable_alter_custphone 		= $_REQUEST['disable_alter_custphone'];
	$amd_send_to_vmx 				= $_REQUEST['amd_send_to_vmx'];
	$waitforsilence_options 		= $_REQUEST['waitforsilence_options'];

	$location = mysqli_real_escape_string($link, $_REQUEST['location_id']);
   	//$apiresults = array("data" => $_REQUEST); 

    // Default values 
    $defActive = array("Y","N");
    $defDialMethod = array("MANUAL","RATIO","ADAPT_HARD_LIMIT","ADAPT_TAPERED","ADAPT_AVERAGE","INBOUND_MAN");
    
    // Check campaign_id if its null or empty
	if($campaign_id == null) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg); 
		//$apiresults = array("result" => "Error: Set a value for Campaign ID."); 
	} else {
		// Check value compare to default values
		if(!in_array($active,$defActive) && $active != null) {
			$err_msg = error_handle("41006", "active");
			$apiresults = array("code" => "41006", "result" => $err_msg); 
			//$apiresults = array("result" => "Error: Default value for active is Y or N only."); 
		} else {
			if(!in_array($dial_method,$defDialMethod) && $dial_method != null) {
				$err_msg = error_handle("41006", "dial_method");
				$apiresults = array("code" => "41006", "result" => $err_msg); 
				//$apiresults = array("result" => "Error: Default value for dial method are MANUAL,RATIO,ADAPT_HARD_LIMIT,ADAPT_TAPERED,ADAPT_AVERAGE,INBOUND_MAN only."); 
			} else {
				$astDB->where('campaign_id', $campaign_id);
				$resultGet = $astDB->getOne('vicidial_campaigns', null, '*');
				
				if($resultGet){
					$dynamic_cid_SQL = "";
					$dynamic_cid_COL = "";
					$dynamic_cid_VAL = "";
					$checkColumn = mysqli_query($linkgo, "SHOW COLUMNS FROM `go_campaigns` LIKE 'dynamic_cid'");
					$columnRows = mysqli_num_rows($checkColumn);

					if ($columnRows > 0) {
						$data_update_go = array('dynamic_cid' => $dynamic_cid);
						$data_insert_go = array('dynamic_cid' => $dynamic_cid);
					}
					
					if(!empty($location)){
						$data_update_go = array('location_id' => $location);
						$data_insert_go = array('location_id' => $location);
					}
					
					if (!empty($nextdial_seconds)) {
						$data_update = array(
							'nextdial_seconds' => $nextdial_seconds
						);
					}

					if($campaign_type == "SURVEY"){
						if(!empty($dial_method)){
							$dial_method = $dial_method;
						}else{
							$dial_method = "RATIO";
						}
					}
					if($dial_prefix == "CUSTOM"){
						$dialprefix = $custom_prefix;
					}else{
						$dialprefix = $dial_prefix;
					}
					
					if(!empty($am_message_chooser)){
						$amMessageExten = $am_message_chooser;
					}else{
						$amMessageExten = $am_message_exten;
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
					
					$addedQuery = '';
					if ($campaign_type != 'SURVEY') {
						$data_update = array(
							'campaign_allow_inbound' 		=> (!empty($campaign_allow_inbound)) ? $campaign_allow_inbound : $resultGet['campaign_allow_inbound'], 
							'available_only_ratio_tally' 	=> (!empty($available_only_ratio_tally)) ? $available_only_ratio_tally : $resultGet['available_only_ratio_tally'], 
							'campaign_recording' 			=> (!empty($campaign_recording)) ? $campaign_recording : $resultGet['campaign_recording'], 
							'campaign_rec_filename' 		=> (!empty($campaign_rec_filename)) ? $campaign_rec_filename : $resultGet['campaign_rec_filename'], 
							'per_call_notes' 				=> (!empty($per_call_notes)) ? $per_call_notes : $resultGet['per_call_notes'], 
							'am_message_exten' 				=> $amMessageExten, 
							'agent_pause_codes_active' 		=> (!empty($agent_pause_codes_active)) ? $agent_pause_codes_active : $resultGet['agent_pause_codes_active'], 
							'manual_dial_filter' 			=> (!empty($manual_dial_filter)) ? $manual_dial_filter : $resultGet['manual_dial_filter'], 
							'customer_3way_hangup_logging' 	=> (!empty($customer_3way_hangup_logging)) ? $customer_3way_hangup_logging : $resultGet['customer_3way_hangup_logging'], 
							'customer_3way_hangup_seconds' 	=> (!empty($customer_3way_hangup_seconds)) ? $customer_3way_hangup_seconds : $resultGet['customer_3way_hangup_seconds'], 
							'customer_3way_hangup_action' 	=> (!empty($customer_3way_hangup_action)) ? $customer_3way_hangup_action : $resultGet['customer_3way_hangup_action'],
							'alt_number_dialing' 			=> (!empty($alt_number_dialing)) ? $alt_number_dialing : $resultGet['alt_number_dialing']
						);
					}
						
					if($campaign_type != 'SURVEY' && $dial_method != "INBOUND_MAN") {
						$data_update = array(
							'use_internal_dnc' 		=> (!empty($use_internal_dnc)) ? $use_internal_dnc : $resultGet['use_internal_dnc'],
							'use_campaign_dnc' 		=> (!empty($use_campaign_dnc)) ? $use_campaign_dnc : $resultGet['use_campaign_dnc'],
							'three_way_call_cid' 	=> (!empty($three_way_call_cid)) ? $three_way_call_cid : $resultGet['three_way_call_cid'], 
							'manual_dial_list_id' 	=> (!empty($manual_dial_list_id)) ? $manual_dial_list_id : $resultGet['manual_dial_list_id'], 
							'hopper_level' 			=> (!empty($hopper_level)) ? $hopper_level : $resultGet['hopper_level'],
							'alt_number_dialing' 	=> (!empty($alt_number_dialing)) ? $alt_number_dialing : $resultGet['alt_number_dialing']
						);
					}

					$data_update = array(
						'campaign_name' 			=> (!empty($campaign_name)) ? $campaign_name : $resultGet['campaign_name'],
						'campaign_description' 		=> (!empty($campaign_desc)) ? $campaign_desc : $resultGet['campaign_desc'], 
						'active' 					=> (!empty($active)) ? $active : $resultGet['active'], 
						'dial_method' 				=> (!empty($dial_method)) ? $dial_method : $resultGet['dial_method'], 
						'auto_dial_level' 			=> $autoDialLevel, 
						'dial_prefix' 				=> $dialprefix,
						'web_form_address' 			=> (!empty($webform)) ? $webform : $resultGet['web_form_address'], 
						'campaign_script' 			=> (!empty($campaign_script)) ? $campaign_script : $resultGet['campaign_script'], 
						'campaign_cid' 				=> (!empty($campaign_cid)) ? $campaign_cid : $resultGet['campaign_cid'], 
						'campaign_vdad_exten' 		=> (!empty($campaign_vdad_exten)) ? $campaign_vdad_exten : $resultGet['campaign_vdad_exten'], 
						'local_call_time' 			=> (!empty($local_call_time)) ? $local_call_time : $resultGet['local_call_time'],  
						'dial_status_a' 			=> (!empty($dial_status)) ? $dial_status : $resultGet['dial_status'], 
						'lead_filter_id' 			=> (!empty($lead_filter)) ? $lead_filter : $resultGet['lead_filter_id'],
						'dial_timeout' 				=> (!empty($dial_timeout)) ? $dial_timeout : $resultGet['dial_timeout'], 
						'manual_dial_prefix' 		=> (!empty($manual_dial_prefix)) ? $manual_dial_prefix : $resultGet['manual_dial_prefix'], 
						'get_call_launch' 			=> (!empty($get_call_launch)) ? $get_call_launch : $resultGet['get_call_launch'], 
						'next_agent_call' 			=> (!empty($next_agent_call)) ? $next_agent_call : $resultGet['next_agent_call'], 
						'xferconf_a_number' 		=> (!empty($xferconf_a_number)) ? $xferconf_a_number : $resultGet['xferconf_a_number'], 
						'xferconf_b_number' 		=> (!empty($xferconf_b_number)) ? $xferconf_b_number : $resultGet['xferconf_b_number'], 
						'three_way_dial_prefix' 	=> (!empty($three_way_dial_prefix)) ? $three_way_dial_prefix : $resultGet['three_way_dial_prefix'], 
						'closer_campaigns' 			=> (!empty($closer_campaigns)) ? $closer_campaigns : $resultGet['closer_campaigns'],
						'xfer_groups' 				=> (!empty($xfer_groups)) ? $xfer_groups : $resultGet['xfer_groups'],
						'survey_first_audio_file' 	=> (!empty($survey_first_audio_file)) ? $survey_first_audio_file : $resultGet['survey_first_audio_file'],
						'survey_method' 			=> (!empty($survey_method)) ? $survey_method : $resultGet['survey_method'],
						'survey_menu_id' 			=> (!empty($survey_menu_id)) ? $survey_menu_id : $resultGet['survey_menu_id'],
						'survey_dtmf_digits' 		=> (!empty($survey_dtmf_digits)) ? $survey_dtmf_digits : $resultGet['survey_dtmf_digits'],
						'survey_xfer_exten' 		=> (!empty($survey_xfer_exten)) ? $survey_xfer_exten : $resultGet['survey_xfer_exten'],
						'survey_ni_digit' 			=> (!empty($survey_ni_digit)) ? $survey_ni_digit : $resultGet['survey_ni_digit'],
						'survey_ni_audio_file' 		=> (!empty($survey_ni_audio_file)) ? $survey_ni_audio_file : $resultGet['survey_ni_audio_file'],
						'survey_ni_status' 			=> (!empty($survey_ni_status)) ? $survey_ni_status : $resultGet['survey_ni_status'],
						'survey_third_digit' 		=> (!empty($survey_third_digit)) ? $survey_third_digit : $resultGet['survey_third_digit'],
						'survey_third_audio_file' 	=> (!empty($survey_third_audio_file)) ? $survey_third_audio_file : $resultGet['survey_third_audio_file'],
						'survey_third_status' 		=> (!empty($survey_third_status)) ? $survey_third_status : $resultGet['survey_third_status'],
						'survey_third_exten' 		=> (!empty($survey_third_exten)) ? $survey_third_exten : $resultGet['survey_third_exten'],
						'survey_fourth_digit' 		=> (!empty($survey_fourth_digit)) ? $survey_fourth_digit : $resultGet['survey_fourth_digit'],
						'survey_fourth_audio_file' 	=> (!empty($survey_fourth_audio_file)) ? $survey_fourth_audio_file : $resultGet['survey_fourth_audio_file'],
						'survey_fourth_status' 		=> (!empty($survey_fourth_status)) ? $survey_fourth_status : $resultGet['survey_fourth_status'],
						'survey_fourth_exten' 		=> (!empty($survey_fourth_exten)) ? $survey_fourth_exten : $resultGet['survey_fourth_exten'],
						'amd_send_to_vmx' 			=> (!empty($amd_send_to_vmx)) ? $amd_send_to_vmx : $resultGet['amd_send_to_vmx'],
						'waitforsilence_options' 	=> (!empty($waitforsilence_options)) ? $waitforsilence_options : $resultGet['waitforsilence_options'],
						'agent_lead_search' 		=> (!empty($agent_lead_search)) ? $agent_lead_search : $resultGet['agent_lead_search'],
						'agent_lead_search_method' 	=> (!empty($agent_lead_search_method)) ? $agent_lead_search_method : $resultGet['agent_lead_search_method'],
						'omit_phone_code' 			=> (!empty($omit_phone_code)) ? $omit_phone_code : $resultGet['omit_phone_code'],
						'disable_alter_custdata' 	=> (!empty($disable_alter_custdata)) ? $disable_alter_custdata : $resultGet['disable_alter_custdata'],
						'disable_alter_custphone' 	=> (!empty($disable_alter_custphone)) ? $disable_alter_custphone : $resultGet['disable_alter_custphone'],
						'my_callback_option' 		=> (!empty($my_callback_option)) ? $my_callback_option : $resultGet['my_callback_option'],
						'lead_order' 				=> (!empty($lead_order)) ? $lead_order : $resultGet['lead_order'],
						'lead_order_secondary'		=> (!empty($lead_order_secondary)) ? $lead_order_secondary : $resultGet['lead_order_secondary']
					);
					$astDB->where('campaign_id', $campaign_id);
					$astDB->update('vicidial_campaigns', $data_update);
					$updateQuery = $astDB->getLastQuery();
					
					$goDB->where('campaign_id', $campaign_id);
					$checkCampGODB = $goDB->get('go_campaigns', null, '*');

					$url_tab_first_url = str_replace("http://", "https://", $url_tab_first_url);
					$url_tab_second_url = str_replace("http://", "https://", $url_tab_second_url);
					if ($checkCampGODB) {
						$data_update_go = array(
							'custom_fields_launch' 	=> (!empty($custom_fields_launch)) ? $custom_fields_launch : $resultGet['custom_fields_launch'], 
							'custom_fields_list_id' => (!empty($custom_fields_list_id)) ? $custom_fields_list_id : $resultGet['custom_fields_list_id'],
							'url_tab_first_title' 	=> (!empty($url_tab_first_title)) ? $url_tab_first_title : $resultGet['url_tab_first_title'],
							'url_tab_first_url' 	=> (!empty($url_tab_first_url)) ? $url_tab_first_url : $resultGet['url_tab_first_url'],
							'url_tab_second_title' 	=> (!empty($url_tab_second_title)) ? $url_tab_second_title : $resultGet['url_tab_second_title'],
							'url_tab_second_url' 	=> (!empty($url_tab_second_url)) ? $url_tab_second_url : $resultGet['url_tab_second_url']
						);
						$goDB->where('campaign_id', $campaign_id);
						$goDB->update('go_campaigns', $data_update_go);
					} else {
						$campaign_type = (strlen($campaign_type) > 0) ? $campaign_type : "OUTBOUND";
						$data_insert_go = array(
							'campaign_id' 			=> $campaign_id, 
							'campaign_type' 		=> $campaign_type, 
							'custom_fields_launch' 	=> (!empty($custom_fields_launch)) ? $custom_fields_launch : $resultGet['custom_fields_launch'], 
							'custom_fields_list_id' => (!empty($custom_fields_list_id)) ? $custom_fields_list_id : $resultGet['custom_fields_list_id'],
							'url_tab_first_title' 	=> (!empty($url_tab_first_title)) ? $url_tab_first_title : $resultGet['url_tab_first_title'],
							'url_tab_first_url' 	=> (!empty($url_tab_first_url)) ? $url_tab_first_url : $resultGet['url_tab_first_url'],
							'url_tab_second_title' 	=> (!empty($url_tab_second_title)) ? $url_tab_second_title : $resultGet['url_tab_second_title'],
							'url_tab_second_url' 	=> (!empty($url_tab_second_url)) ? $url_tab_second_url : $resultGet['url_tab_second_url']
						);

						$goDB->insert('go_campaigns', $data_insert_go);
					}
					
					$SQLdate = date("Y-m-d H:i:s");
					$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Updated campaign settings for $campaign_id", $log_group, $updateQuery);
					
					if($force_reset_hopper == "Y"){
						$astDB->where('campaign_id', $campaign_id);
						$astDB->where('status', array('READY','QUEUE','DONE'), 'in');
						$astDB->delete('vicidial_hopper');
					}
					
					if($campaign_type == "SURVEY"){
						if($survey_method != "AGENT_XFER" && $active == 'Y'){
							$astDB->where('campaign_id', $campaign_id);
							$astDB->update('vicidial_remote_agents', array('status' => 'ACTIVE'));
						}else{
							$astDB->where('campaign_id', $campaign_id);
							$astDB->update('vicidial_remote_agents', array('status' => 'INACTIVE'));
						}
						
						if(!empty($no_channels)){
							$astDB->where('campaign_id', $campaign_id);
							$astDB->update('vicidial_remote_agents', array('number_of_lines' => '$no_channels'));
						}
					}
					
					$apiresults = array("result" => "success");
				}else{
					$err_msg = error_handle("41004", "campaign. Doesn't exist");
					$apiresults = array("code" => "41004", "result" => $err_msg); 
					//$apiresults = array("result" => "Error: Campaign doens't exist.");
				}
				
			}
		}
	}//end

?>
