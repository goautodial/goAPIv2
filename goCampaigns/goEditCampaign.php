<?php
   //////////////////////////////////#
   //# Name: goEditCampaign.php                   //#
   //# Description: API to edit specific campaign //#
   //# Version: 0.9                               //#
   //# Copyright: GOAutoDial Ltd. (c) 2011-2015   //#
   //# Written by: Jerico James Milo              //#
   //# License: AGPLv2                            //#
   //////////////////////////////////#
    
    include_once("../goFunctions.php");

	// POST or GET Variables
	$goUser 		= $_REQUEST['goUser'];
	$ip_address 		= $_REQUEST['hostname'];
	$log_user 		= $_REQUEST['log_user'];
	$log_group 		= $_REQUEST['log_group'];
	$campaign_id 		= $_REQUEST['campaign_id'];
	$campaign_name 		= $_REQUEST['campaign_name'];
	$campaign_desc 		= $_REQUEST['campaign_desc'];
	$active 		= strtoupper($_REQUEST['active']);;
	$dial_method 		= strtoupper($_REQUEST['dial_method']);
	$auto_dial_level		= $_REQUEST['auto_dial_level'];
	$auto_dial_level_adv 		= $_REQUEST['auto_dial_level_adv'];
	$dial_prefix 		= $_REQUEST['dial_prefix'];
	$custom_prefix 		= $_REQUEST['custom_prefix'];
	$campaign_script 		= $_REQUEST['campaign_script'];
	$webform		= $_REQUEST['web_form_address'];
	$campaign_cid 		= $_REQUEST['campaign_cid'];
	$campaign_recording 		= $_REQUEST['campaign_recording'];
	$campaign_vdad_exten 		= $_REQUEST['campaign_vdad_exten'];
	$local_call_time 		= $_REQUEST['local_call_time'];
	$hopper_level 		= $_REQUEST['hopper_level'];
	$force_reset_hopper 		= $_REQUEST['force_reset_hopper'];
	$dial_status 		= $_REQUEST['dial_status'];
	$lead_order 		= $_REQUEST['lead_order'];
	$lead_filter 		= $_REQUEST['lead_filter'];
	$dial_timeout 		= $_REQUEST['dial_timeout'];
	$manual_dial_prefix 		= $_REQUEST['manual_dial_prefix'];
	$get_call_launch 		= $_REQUEST['get_call_launch'];
	$am_message_exten 		= $_REQUEST['am_message_exten'];
	$am_message_chooser 		= $_REQUEST['am_message_chooser'];
	$agent_pause_codes_active 		= $_REQUEST['agent_pause_codes_active'];
	$manual_dial_filter 		= $_REQUEST['manual_dial_filter'];
	$use_internal_dnc 		= $_REQUEST['use_internal_dnc'];
	$use_campaign_dnc 		= $_REQUEST['use_campaign_dnc'];
	$manual_dial_list_id 		= $_REQUEST['manual_dial_list_id'];
	$available_only_ratio_tally 		= $_REQUEST['available_only_ratio_tally'];
	$campaign_rec_filename 		= $_REQUEST['campaign_rec_filename'];
	$next_agent_call 		= $_REQUEST['next_agent_call'];
	$xferconf_a_number 		= $_REQUEST["xferconf_a_number"];
	$xferconf_b_number 		= $_REQUEST["xferconf_b_number"];
	$three_way_call_cid 		= $_REQUEST['three_way_call_cid'];
	$three_way_dial_prefix 		= $_REQUEST['three_way_dial_prefix'];
	$customer_3way_hangup_logging 		= $_REQUEST['customer_3way_hangup_logging'];
	$customer_3way_hangup_seconds 		= $_REQUEST['customer_3way_hangup_seconds'];
	$customer_3way_hangup_action 		= $_REQUEST['customer_3way_hangup_action'];
	$inbound_man 		= $_REQUEST['inbound_man'];
	$campaign_allow_inbound		= $_REQUEST['campaign_allow_inbound'];
	$closer_campaigns		= $_REQUEST['closer_campaigns'];
	$xfer_groups		= $_REQUEST['xfer_groups'];
	$custom_fields_launch		= $_REQUEST['custom_fields_launch'];
	$campaign_type		= $_REQUEST['campaign_type'];
	$custom_fields_list_id		= $_REQUEST['custom_fields_list_id'];
	$per_call_notes 		= $_REQUEST['per_call_notes'];
	$url_tab_first_title		= $_REQUEST['url_tab_first_title'];
	$url_tab_first_url		= $_REQUEST['url_tab_first_url'];
	$url_tab_second_title		= $_REQUEST['url_tab_second_title'];
	$url_tab_second_url		= $_REQUEST['url_tab_second_url'];
	$agent_lead_search		= $_REQUEST['agent_lead_search'];
	$agent_lead_search_method 		= $_REQUEST['agent_lead_search_method'];
    $omit_phone_code = $_REQUEST['omit_phone_code'];
	$alt_number_dialing = $_REQUEST['alt_number_dialing'];
	$dynamic_cid = $_REQUEST['dynamic_cid'];
	$nextdial_seconds = $_REQUEST['nextdial_seconds'];
	$my_callback_option = $_REQUEST['my_callback_option'];
	
	$survey_first_audio_file = $_REQUEST['survey_first_audio_file'];
	$survey_method = $_REQUEST['survey_method'];
	$survey_menu_id = $_REQUEST['survey_menu_id'];
	$survey_dtmf_digits = $_REQUEST['survey_dtmf_digits'];
	$survey_xfer_exten = $_REQUEST['survey_xfer_exten'];
	$survey_ni_digit = $_REQUEST['survey_ni_digit'];
	$survey_ni_audio_file = $_REQUEST['survey_ni_audio_file'];
	$survey_ni_status = $_REQUEST['survey_ni_status'];
	$survey_third_digit = $_REQUEST['survey_third_digit'];
	$survey_third_audio_file = $_REQUEST['survey_third_audio_file'];
	$survey_third_status = $_REQUEST['survey_third_status'];
	$survey_third_exten = $_REQUEST['survey_third_exten'];
	$survey_fourth_digit = $_REQUEST['survey_fourth_digit'];
	$survey_fourth_audio_file = $_REQUEST['survey_fourth_audio_file'];
	$survey_fourth_status = $_REQUEST['survey_fourth_status'];
	$survey_fourth_exten = $_REQUEST['survey_fourth_exten'];
    $no_channels = $_REQUEST['no_channels'];
    $disable_alter_custdata = $_REQUEST['disable_alter_custdata'];
    $disable_alter_custphone = $_REQUEST['disable_alter_custphone'];
	
	$amd_send_to_vmx = $_REQUEST['amd_send_to_vmx'];
	$waitforsilence_options = $_REQUEST['waitforsilence_options'];

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
				$check_exist = mysqli_query($link, "SELECT * FROM vicidial_campaigns WHERE campaign_id = '$campaign_id';");
				$num_check  = mysqli_num_rows($check_exist);
				
				if($num_check > 0){
					while($fetch_exist = mysqli_fetch_array($check_exist)){
						$campaign_type = $fetch_exist["campaign_type"];
						$data_campaign_name = $fetch_exist['campaign_name'];
						$data_campaign_desc = $fetch_exist['campaign_desc'];
						$data_dial_method = $fetch_exist['dial_method'];
						$data_active = $fetch_exist['active'];
						$data_auto_dial_level = $fetch_exist['auto_dial_level'];
						$data_auto_dial_level_adv = $fetch_exist['auto_dial_level_adv'];
						$data_dial_prefix = $fetch_exist['dial_prefix'];
						$data_custom_prefix = $fetch_exist['custom_prefix'];
						$data_campaign_script = $fetch_exist['campaign_script'];
						$data_web_form_address = $fetch_exist['web_form_address'];
						$data_campaign_cid = $fetch_exist['campaign_cid'];
						$data_campaign_recording = $fetch_exist['campaign_recording'];
						$data_campaign_vdad_exten = $fetch_exist['campaign_vdad_exten'];
						$data_local_call_time = $fetch_exist['local_call_time'];
						$data_hopper_level = $fetch_exist['hopper_level'];
						$data_force_reset_hopper = $fetch_exist['force_reset_hopper'];
						$data_dial_status = $fetch_exist['dial_status'];
						$data_lead_order = $fetch_exist['lead_order'];
						$data_lead_filter = $fetch_exist['lead_filter'];
						$data_dial_timeout = $fetch_exist['dial_timeout'];
						$data_manual_dial_prefix = $fetch_exist['manual_dial_prefix'];
						$data_get_call_launch = $fetch_exist['get_call_launch'];
						$data_am_message_exten = $fetch_exist['am_message_exten'];
						$data_am_message_chooser = $fetch_exist['am_message_chooser'];
						$data_agent_pause_codes_active = $fetch_exist['agent_pause_codes_active'];
						$data_manual_dial_filter = $fetch_exist['manual_dial_filter'];
						$data_use_internal_dnc = $fetch_exist['use_internal_dnc'];
						$data_use_campaign_dnc = $fetch_exist['use_campaign_dnc'];
						$data_manual_dial_list_id = $fetch_exist['manual_dial_list_id'];
						$data_available_only_ratio_tally = $fetch_exist['available_only_ratio_tally'];
						$data_campaign_rec_filename = $fetch_exist['campaign_rec_filename'];
						$data_next_agent_call = $fetch_exist['next_agent_call'];
						$data_xferconf_a_number = $fetch_exist['xferconf_a_number'];
						$data_xferconf_b_number = $fetch_exist['xferconf_b_number'];
						$data_three_way_call_cid = $fetch_exist['three_way_call_cid'];
						$data_three_way_dial_prefix = $fetch_exist['three_way_dial_prefix'];
						$data_customer_3way_hangup_logging = $fetch_exist['customer_3way_hangup_logging'];
						$data_customer_3way_hangup_seconds = $fetch_exist['customer_3way_hangup_seconds'];
						$data_customer_3way_hangup_action = $fetch_exist['customer_3way_hangup_action'];
						$data_inbound_man = $fetch_exist['inbound_man'];
						$data_campaign_allow_inbound = $fetch_exist['campaign_allow_inbound'];
						$data_closer_campaigns = $fetch_exist['closer_campaigns'];
						$data_xfer_groups = $fetch_exist['xfer_groups'];
						$data_custom_fields_launch = $fetch_exist['custom_fields_launch'];
						$data_campaign_type = $fetch_exist['campaign_type'];
						$data_custom_fields_list_id = $fetch_exist['custom_fields_list_id'];
						$data_per_call_notes = $fetch_exist['per_call_notes'];
						$data_url_tab_first_title = $fetch_exist['url_tab_first_title'];
						$data_url_tab_first_url = $fetch_exist['url_tab_first_url'];
						$data_url_tab_second_title = $fetch_exist['url_tab_second_title'];
						$data_url_tab_second_url = $fetch_exist['url_tab_second_url'];
						$data_agent_lead_search = $fetch_exist['agent_lead_search'];
						$data_agent_lead_search_method = $fetch_exist['agent_lead_search_method'];
						$data_omit_phone_code = $fetch_exist['omit_phone_code'];
						$data_alt_number_dialing = $fetch_exist['alt_number_dialing'];
						$data_survey_first_audio_file = $fetch_exist['survey_first_audio_file'];
						$data_survey_method = $fetch_exist['survey_method'];
						$data_survey_menu_id = $fetch_exist['survey_menu_id'];
						$data_survey_dtmf_digits = $fetch_exist['survey_dtmf_digits'];
						$data_survey_xfer_exten = $fetch_exist['survey_xfer_exten'];
						$data_survey_ni_digit = $fetch_exist['survey_ni_digit'];
						$data_survey_ni_audio_file = $fetch_exist['survey_ni_audio_file'];
						$data_survey_ni_status = $fetch_exist['survey_ni_status'];
						$data_survey_third_digit = $fetch_exist['survey_third_digit'];
						$data_survey_third_audio_file = $fetch_exist['survey_third_audio_file'];
						$data_survey_third_status = $fetch_exist['survey_third_status'];
						$data_survey_third_exten = $fetch_exist['survey_third_exten'];
						$data_survey_fourth_digit = $fetch_exist['survey_fourth_digit'];
						$data_survey_fourth_audio_file = $fetch_exist['survey_fourth_audio_file'];
						$data_survey_fourth_status = $fetch_exist['survey_fourth_status'];
						$data_survey_fourth_exten = $fetch_exist['survey_fourth_exten'];
						$data_no_channels = $fetch_exist['no_channels'];
						$data_disable_alter_custdata = $fetch_exist['disable_alter_custdata'];
						$data_disable_alter_custphone = $fetch_exist['disable_alter_custphone'];
						$data_amd_send_to_vmx = $fetch_exist['amd_send_to_vmx'];
						$data_waitforsilence_options = $fetch_exist['waitforsilence_options'];
						$data_dynamic_cid = $fetch_exist['dynamic_cid'];
						$data_nextdial_seconds = $fetch_exist['nextdial_seconds'];
						$data_my_callback_option = $fetch_exist['my_callback_option'];
					}
					
					if(empty($campaign_name))
						$campaign_name = $data_campaign_name;
					if(empty($campaign_desc))
						$campaign_desc = $data_campaign_desc;
					if(empty($dial_method))
						$dial_method = $data_dial_method;
					if(empty($active))
						$active = $data_active;
					if(empty($auto_dial_level))
						$auto_dial_level = $data_auto_dial_level;
					if(empty($auto_dial_level_adv))
						$auto_dial_level_adv = $data_auto_dial_level_adv;
					if(empty($dial_prefix))
						$dial_prefix = $data_dial_prefix;
					if(empty($campaign_script))
						$campaign_script = $data_campaign_script;
					if(empty($web_form_address))
						$web_form_address = $data_web_form_address;
					if(empty($campaign_cid))
						$campaign_cid = $data_campaign_cid;
					if(empty($campaign_recording))
						$campaign_recording = $data_campaign_recording;
					if(empty($campaign_vdad_exten))
						$campaign_vdad_exten = $data_campaign_vdad_exten;
					if(empty($local_call_time))
						$local_call_time = $data_local_call_time;
					if(empty($hopper_level))
						$hopper_level = $data_hopper_level;
					if(empty($force_reset_hopper))
						$force_reset_hopper = $data_force_reset_hopper;
					if(empty($dial_status))
						$dial_status = $data_dial_status;
					if(empty($lead_order))
						$lead_order = $data_lead_order;
					if(empty($lead_filter))
						$lead_filter = $data_lead_filter;
					if(empty($dial_timeout))
						$dial_timeout = $data_dial_timeout;
					if(empty($manual_dial_prefix))
						$manual_dial_prefix = $data_manual_dial_prefix;
					if(empty($get_call_launch))
						$get_call_launch = $data_get_call_launch;
					if(empty($am_message_exten))
						$am_message_exten = $data_am_message_exten;
					if(empty($am_message_chooser))
						$am_message_chooser = $data_am_message_chooser;
					if(empty($agent_pause_codes_active))
						$agent_pause_codes_active = $data_agent_pause_codes_active;
					if(empty($manual_dial_filter))
						$manual_dial_filter = $data_manual_dial_filter;
					if(empty($use_internal_dnc))
						$use_internal_dnc = $data_use_internal_dnc;
					if(empty($use_campaign_dnc))
						$use_campaign_dnc = $data_use_campaign_dnc;
					if(empty($manual_dial_list_id))
						$manual_dial_list_id = $data_manual_dial_list_id;
					if(empty($available_only_ratio_tally))
						$available_only_ratio_tally = $data_available_only_ratio_tally;
					if(empty($campaign_rec_filename))
						$campaign_rec_filename = $data_campaign_rec_filename;
					if(empty($next_agent_call))
						$next_agent_call = $data_next_agent_call;
					if(empty($xferconf_a_number))
						$xferconf_a_number = $data_xferconf_a_number;
					if(empty($xferconf_b_number))
						$xferconf_b_number = $data_xferconf_b_number;
					if(empty($three_way_call_cid))
						$three_way_call_cid = $data_three_way_call_cid;
					if(empty($three_way_dial_prefix))
						$three_way_dial_prefix = $data_three_way_dial_prefix;
					if(empty($customer_3way_hangup_logging))
						$customer_3way_hangup_logging = $data_customer_3way_hangup_logging;
					if(empty($customer_3way_hangup_seconds))
						$customer_3way_hangup_seconds = $data_customer_3way_hangup_seconds;
					if(empty($customer_3way_hangup_action))
						$customer_3way_hangup_action = $data_customer_3way_hangup_action;
					if(empty($inbound_man))
						$inbound_man = $data_inbound_man;
					if(empty($campaign_allow_inbound))
						$campaign_allow_inbound = $data_campaign_allow_inbound;
					if(empty($closer_campaigns))
						$closer_campaigns = $data_closer_campaigns;
					if(empty($xfer_groups))
						$xfer_groups = $data_xfer_groups;
					if(empty($custom_fields_launch))
						$custom_fields_launch = $data_custom_fields_launch;
					if(empty($campaign_type))
						$campaign_type = $data_campaign_type;
					if(empty($custom_fields_list_id))
						$custom_fields_list_id = $data_custom_fields_list_id;
					if(empty($per_call_notes))
						$per_call_notes = $data_per_call_notes;
					if(empty($url_tab_first_title))
						$url_tab_first_title = $data_url_tab_first_title;
					if(empty($url_tab_first_url))
						$url_tab_first_url = $data_url_tab_first_url;
					if(empty($url_tab_second_title))
						$url_tab_second_title = $data_url_tab_second_title;
					if(empty($url_tab_second_url))
						$url_tab_second_url = $data_url_tab_second_url;
					if(empty($agent_lead_search))
						$agent_lead_search = $data_agent_lead_search;
					if(empty($agent_lead_search_method))
						$agent_lead_search_method = $data_agent_lead_search_method;
					if(empty($omit_phone_code))
						$omit_phone_code = $data_omit_phone_code;
					if(empty($alt_number_dialing))
						$alt_number_dialing = $data_alt_number_dialing;
					if(empty($survey_first_audio_file))
						$survey_first_audio_file = $data_survey_first_audio_file;
					if(empty($survey_method))
						$survey_method = $data_survey_method;
					if(empty($survey_menu_id))
						$survey_menu_id = $data_survey_menu_id;
					if(empty($survey_dtmf_digits))
						$survey_dtmf_digits = $data_survey_dtmf_digits;
					if(empty($survey_xfer_exten))
						$survey_xfer_exten = $data_survey_xfer_exten;
					if(empty($survey_ni_digit))
						$survey_ni_digit = $data_survey_ni_digit;
					if(empty($survey_ni_audio_file))
						$survey_ni_audio_file = $data_survey_ni_audio_file;
					if(empty($survey_ni_status))
						$survey_ni_status = $data_survey_ni_status;
					if(empty($survey_third_digit))
						$survey_third_digit = $data_survey_third_digit;
					if(empty($survey_third_audio_file))
						$survey_third_audio_file = $data_survey_third_audio_file;
					if(empty($survey_third_status))
						$survey_third_status = $data_survey_third_status;
					if(empty($survey_third_exten))
						$survey_third_exten = $data_survey_third_exten;
					if(empty($survey_fourth_digit))
						$survey_fourth_digit = $data_survey_fourth_digit;
					if(empty($survey_fourth_audio_file))
						$survey_fourth_audio_file = $data_survey_fourth_audio_file;
					if(empty($survey_fourth_status))
						$survey_fourth_status = $data_survey_fourth_status;
					if(empty($survey_fourth_exten))
						$survey_fourth_exten = $data_survey_fourth_exten;
					if(empty($no_channels))
						$no_channels = $data_no_channels;
					if(empty($disable_alter_custdata))
						$disable_alter_custdata = $data_disable_alter_custdata;
					if(empty($disable_alter_custphone))
						$disable_alter_custphone = $data_disable_alter_custphone;
					if(empty($amd_send_to_vmx))
						$amd_send_to_vmx = $data_amd_send_to_vmx;
					if(empty($waitforsilence_options))
						$waitforsilence_options = $data_waitforsilence_options;
					
					if(empty($dynamic_cid))
						$dynamic_cid = $data_dynamic_cid;
					
					if(empty($nextdial_seconds))
						$nextdial_seconds = $data_nextdial_seconds;
					if(empty($my_callback_option))
						$my_callback_option = $data_my_callback_option;
					
					$dynamic_cid_SQL = "";
					$dynamic_cid_COL = "";
					$dynamic_cid_VAL = "";
					$checkColumn = mysqli_query($linkgo, "SHOW COLUMNS FROM `go_campaigns` LIKE 'dynamic_cid'");
					$columnRows = mysqli_num_rows($checkColumn);
					if ($columnRows > 0) {
						$dynamic_cid_SQL = ", `dynamic_cid` = '$dynamic_cid' ";
						$dynamic_cid_COL = ", dynamic_cid";
						$dynamic_cid_VAL = ", '$dynamic_cid'";
					}
					
					if(!empty($location)){
						//$result_location = go_check_location($location, $user_group);
						// if($result_location < 1){
						// 	$err_msg = error_handle("41006", "location. User group does not exist in the location selected.");
						// 	$apiresults = array("code" => "41006", "result" => $err_msg);
						// }
						$location_SQL = ", `location_id` = '$location' ";
						$location_COL = ", location_id";
						$location_VAL = ",'$location'";
					}else{
						$location = "";
						$location_SQL = "";
						$location_COL = "";
						$location_VAL = "";
					}
					
					if (!empty($nextdial_seconds)) {
						$nextdial_seconds_SQL = ", nextdial_seconds = '$nextdial_seconds'";
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
							$addedQuery .= ",campaign_allow_inbound = '$campaign_allow_inbound', 
										available_only_ratio_tally = '$available_only_ratio_tally', 
										campaign_recording = '$campaign_recording', 
										campaign_rec_filename = '$campaign_rec_filename', 
										per_call_notes = '$per_call_notes', 
										am_message_exten = '$amMessageExten', 
										agent_pause_codes_active = '$agent_pause_codes_active', 
										manual_dial_filter = '$manual_dial_filter', 
										customer_3way_hangup_logging = '$customer_3way_hangup_logging', 
										customer_3way_hangup_seconds = '$customer_3way_hangup_seconds', 
										customer_3way_hangup_action = '$customer_3way_hangup_action',
										alt_number_dialing ='$alt_number_dialing'";
						}
						
						if($campaign_type != 'SURVEY' && $dial_method != "INBOUND_MAN") {
							$addedQuery .= ",lead_order = '$lead_order',
										use_internal_dnc = '$use_internal_dnc',
										use_campaign_dnc = '$use_campaign_dnc',
										three_way_call_cid = '$three_way_call_cid', 
										manual_dial_list_id = '$manual_dial_list_id', 
										hopper_level = '$hopper_level',
										alt_number_dialing = '$alt_number_dialing'"; 
						}
						$updateQuery = "UPDATE vicidial_campaigns SET
											campaign_name = '$campaign_name',
											campaign_description = '$campaign_desc', 
											active = '$active', 
											dial_method = '$dial_method', 
											auto_dial_level = '$autoDialLevel', 
											dial_prefix = '$dialprefix',
											web_form_address = '$webform', 
											campaign_script = '$campaign_script', 
											campaign_cid = '$campaign_cid', 
											campaign_vdad_exten = '$campaign_vdad_exten', 
											local_call_time = '$local_call_time',  
											dial_status_a = '$dial_status', 
											lead_filter_id = '$lead_filter_id',
											dial_timeout = '$dial_timeout', 
											manual_dial_prefix = '$manual_dial_prefix', 
											get_call_launch = '$get_call_launch', 
											next_agent_call = '$next_agent_call', 
											xferconf_a_number = '$xferconf_a_number', 
											xferconf_b_number = '$xferconf_b_number', 
											three_way_dial_prefix = '$three_way_dial_prefix', 
											closer_campaigns = '$closer_campaigns',
											xfer_groups = '$xfer_groups',
											survey_first_audio_file = '$survey_first_audio_file',
											survey_method = '$survey_method',
											survey_menu_id = '$survey_menu_id',
											survey_dtmf_digits = '$survey_dtmf_digits',
											survey_xfer_exten = '$survey_xfer_exten',
											survey_ni_digit = '$survey_ni_digit',
											survey_ni_audio_file = '$survey_ni_audio_file',
											survey_ni_status = '$survey_ni_status',
											survey_third_digit = '$survey_third_digit',
											survey_third_audio_file = '$survey_third_audio_file',
											survey_third_status = '$survey_third_status',
											survey_third_exten = '$survey_third_exten',
											survey_fourth_digit = '$survey_fourth_digit',
											survey_fourth_audio_file = '$survey_fourth_audio_file',
											survey_fourth_status = '$survey_fourth_status',
											survey_fourth_exten = '$survey_fourth_exten',
											amd_send_to_vmx = '$amd_send_to_vmx',
											waitforsilence_options = '$waitforsilence_options',
											agent_lead_search = '$agent_lead_search',
											agent_lead_search_method = '$agent_lead_search_method',
											omit_phone_code = '$omit_phone_code',
											disable_alter_custdata = '$disable_alter_custdata',
											disable_alter_custphone = '$disable_alter_custphone',
											my_callback_option = '$my_callback_option' 
											$addedQuery
											$nextdial_seconds_SQL
										WHERE campaign_id='$campaign_id'
										LIMIT 1;";
										
						$updateResult = mysqli_query($link, $updateQuery);
						
						$stmtGO = "SELECT * FROM go_campaigns WHERE campaign_id='$campaign_id'";
						$rsltGO = mysqli_query($linkgo, $stmtGO);
						$numGO = mysqli_num_rows($rsltGO);
						$url_tab_first_url = str_replace("http://", "https://", $url_tab_first_url);
						$url_tab_second_url = str_replace("http://", "https://", $url_tab_second_url);
						if ($numGO > 0) {
							$updateGO = "UPDATE go_campaigns SET custom_fields_launch='$custom_fields_launch', custom_fields_list_id='$custom_fields_list_id',url_tab_first_title='$url_tab_first_title',url_tab_first_url='$url_tab_first_url',url_tab_second_title='$url_tab_second_title',url_tab_second_url='$url_tab_second_url' $location_SQL $dynamic_cid_SQL WHERE campaign_id='$campaign_id';";
							$resultGO = mysqli_query($linkgo, $updateGO);
						} else {
							$campaign_type = (strlen($campaign_type) > 0) ? $campaign_type : "OUTBOUND";
							$insertGO = "INSERT INTO go_campaigns (campaign_id, campaign_type, custom_fields_launch, custom_fields_list_id,url_tab_first_title,url_tab_first_url,url_tab_second_title,url_tab_second_url{$location_COL} {$dynamic_cid_COL}) VALUES('$campaign_id', '$campaign_type', '$custom_fields_launch', '$custom_fields_list_id','$url_tab_first_title','$url_tab_first_url','$url_tab_second_title','$url_tab_second_url'{$location_VAL} {$dynamic_cid_VAL});";
							$resultGO = mysqli_query($linkgo, $insertGO);
						}
						
						$SQLdate = date("Y-m-d H:i:s");
						$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Updated campaign settings for $campaign_id", $log_group, $updateQuery);
						
						if($force_reset_hopper == "Y"){
							$queryDelete = "DELETE from vicidial_hopper where campaign_id='$campaign_id' and status IN('READY','QUEUE','DONE');";
							$rsltvDelete = mysqli_query($link, $queryDelete);
						}
						
						if($campaign_type == "SURVEY"){
							if($survey_method != "AGENT_XFER" && $active == 'Y'){
								$updateRemoteUserStatus = "UPDATE vicidial_remote_agents SET status = 'ACTIVE' WHERE campaign_id='$campaign_id'";
								$rsltvVRA = mysqli_query($link, $updateRemoteUserStatus);
							}else{
								$updateRemoteUserStatus = "UPDATE vicidial_remote_agents SET status = 'INACTIVE' WHERE campaign_id='$campaign_id'";
								$rsltvVRA = mysqli_query($link, $updateRemoteUserStatus);
							}
							
							if(!empty($no_channels)){
								$updateRemoteUserNOLINES = "UPDATE vicidial_remote_agents SET number_of_lines = '$no_channels' WHERE campaign_id='$campaign_id'";
								$rsltvVRALines = mysqli_query($link, $updateRemoteUserNOLINES);
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
