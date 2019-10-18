<?php
 /**
 * @file 		goEditCampaign.php
 * @brief 		API for Modifying Campaigns
 * @copyright 	Copyright (c) 2019 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author		Jericho James Milo
 * @author		Noel Umandap
 * @author     	Chris Lomuntad
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

    include_once ( "goAPI.php" );

	$allowed_campaigns									= allowed_campaigns($log_group, $goDB, $astDB);
	$campaign_id 										= $astDB->escape($_REQUEST['campaign_id']);
	$campaign_name 										= $astDB->escape($_REQUEST['campaign_name']);
	$campaign_desc 										= $astDB->escape($_REQUEST['campaign_desc']);
	$active 											= $astDB->escape(strtoupper($_REQUEST['active']));
	$dial_method 										= $astDB->escape($_REQUEST['dial_method']);
	$auto_dial_level									= $astDB->escape($_REQUEST['auto_dial_level']);
	$auto_dial_level_adv 								= $astDB->escape($_REQUEST['auto_dial_level_adv']);
	$dial_prefix 										= $astDB->escape($_REQUEST['dial_prefix']);
	$custom_prefix 										= $astDB->escape($_REQUEST['custom_prefix']);
	$campaign_script 									= $astDB->escape($_REQUEST['campaign_script']);
	$webform											= $astDB->escape($_REQUEST['web_form_address']);
	$campaign_cid 										= $astDB->escape($_REQUEST['campaign_cid']);
	$campaign_recording 								= $astDB->escape($_REQUEST['campaign_recording']);
	$campaign_vdad_exten 								= $astDB->escape($_REQUEST['campaign_vdad_exten']);
	$local_call_time 									= $astDB->escape($_REQUEST['local_call_time']);
	$hopper_level 										= $astDB->escape($_REQUEST['hopper_level']);
	$force_reset_hopper 								= $astDB->escape($_REQUEST['force_reset_hopper']);
	$dial_status 										= $astDB->escape($_REQUEST['dial_status']);
	$lead_order 										= $astDB->escape($_REQUEST['lead_order']);
	$lead_order_secondary 								= $astDB->escape($_REQUEST['lead_order_secondary']);
	$lead_filter 										= $astDB->escape($_REQUEST['lead_filter']);
	$dial_timeout 										= $astDB->escape($_REQUEST['dial_timeout']);
	$manual_dial_prefix 								= $astDB->escape($_REQUEST['manual_dial_prefix']);
	$get_call_launch 									= $astDB->escape($_REQUEST['get_call_launch']);
	$am_message_exten 									= $astDB->escape($_REQUEST['am_message_exten']);
	$am_message_chooser 								= $astDB->escape($_REQUEST['am_message_chooser']);
	$agent_pause_codes_active 							= $astDB->escape($_REQUEST['agent_pause_codes_active']);
	$manual_dial_filter 								= $astDB->escape($_REQUEST['manual_dial_filter']);
	$manual_dial_search_filter							= $astDB->escape($_REQUEST['manual_dial_search_filter']);
	$use_internal_dnc 									= $astDB->escape($_REQUEST['use_internal_dnc']);
	$use_campaign_dnc 									= $astDB->escape($_REQUEST['use_campaign_dnc']);
	$manual_dial_list_id 								= $astDB->escape($_REQUEST['manual_dial_list_id']);
	$available_only_ratio_tally 						= $astDB->escape($_REQUEST['available_only_ratio_tally']);
	$campaign_rec_filename 								= $astDB->escape($_REQUEST['campaign_rec_filename']);
	$next_agent_call 									= $astDB->escape($_REQUEST['next_agent_call']);
	$xferconf_a_number 									= $astDB->escape($_REQUEST["xferconf_a_number"]);
	$xferconf_b_number 									= $astDB->escape($_REQUEST["xferconf_b_number"]);
	$three_way_call_cid 								= $astDB->escape($_REQUEST['three_way_call_cid']);
	$three_way_dial_prefix 								= $astDB->escape($_REQUEST['three_way_dial_prefix']);
	$customer_3way_hangup_logging 						= $astDB->escape($_REQUEST['customer_3way_hangup_logging']);
	$customer_3way_hangup_seconds 						= $astDB->escape($_REQUEST['customer_3way_hangup_seconds']);
	$customer_3way_hangup_action 						= $astDB->escape($_REQUEST['customer_3way_hangup_action']);
	$inbound_man 										= $astDB->escape($_REQUEST['inbound_man']);
	$campaign_allow_inbound								= $astDB->escape($_REQUEST['campaign_allow_inbound']);
	$closer_campaigns									= $astDB->escape($_REQUEST['closer_campaigns']);
	$xfer_groups										= $astDB->escape($_REQUEST['xfer_groups']);
	$custom_fields_launch								= $astDB->escape($_REQUEST['custom_fields_launch']);
    $manual_dial_min_digits                             = $astDB->escape($_REQUEST['manual_dial_min_digits']);
	$campaign_type										= $astDB->escape($_REQUEST['campaign_type']);
	$custom_fields_list_id								= $astDB->escape($_REQUEST['custom_fields_list_id']);
	$per_call_notes 									= $astDB->escape($_REQUEST['per_call_notes']);
	$url_tab_first_title								= $astDB->escape($_REQUEST['url_tab_first_title']);
	$url_tab_first_url									= $astDB->escape($_REQUEST['url_tab_first_url']);
	$url_tab_second_title								= $astDB->escape($_REQUEST['url_tab_second_title']);
	$url_tab_second_url									= $astDB->escape($_REQUEST['url_tab_second_url']);
	$enable_callback_alert								= $astDB->escape($_REQUEST['enable_callback_alert']);
	$cb_noexpire										= $astDB->escape($_REQUEST['cb_noexpire']);
	$cb_sendemail										= $astDB->escape($_REQUEST['cb_sendemail']);
	$agent_lead_search									= $astDB->escape($_REQUEST['agent_lead_search']);
	$agent_lead_search_method 							= $astDB->escape($_REQUEST['agent_lead_search_method']);
    $omit_phone_code 									= $astDB->escape($_REQUEST['omit_phone_code']);
	$alt_number_dialing 								= $astDB->escape($_REQUEST['alt_number_dialing']);
	$dynamic_cid 										= $astDB->escape($_REQUEST['dynamic_cid']);
	$nextdial_seconds 									= $astDB->escape($_REQUEST['nextdial_seconds']);
	$my_callback_option 								= $astDB->escape($_REQUEST['my_callback_option']);
	$survey_first_audio_file 							= $astDB->escape($_REQUEST['survey_first_audio_file']);
	$survey_method 										= $astDB->escape($_REQUEST['survey_method']);
	$survey_menu_id 									= $astDB->escape($_REQUEST['survey_menu_id']);
	$survey_dtmf_digits 								= $astDB->escape($_REQUEST['survey_dtmf_digits']);
	$survey_xfer_exten 									= $astDB->escape($_REQUEST['survey_xfer_exten']);
	$survey_ni_digit 									= $astDB->escape($_REQUEST['survey_ni_digit']);
	$survey_ni_audio_file 								= $astDB->escape($_REQUEST['survey_ni_audio_file']);
	$survey_ni_status 									= $astDB->escape($_REQUEST['survey_ni_status']);
	$survey_third_digit 								= $astDB->escape($_REQUEST['survey_third_digit']);
	$survey_third_audio_file 							= $astDB->escape($_REQUEST['survey_third_audio_file']);
	$survey_third_status 								= $astDB->escape($_REQUEST['survey_third_status']);
	$survey_third_exten 								= $astDB->escape($_REQUEST['survey_third_exten']);
	$survey_fourth_digit 								= $astDB->escape($_REQUEST['survey_fourth_digit']);
	$survey_fourth_audio_file 							= $astDB->escape($_REQUEST['survey_fourth_audio_file']);
	$survey_fourth_status 								= $astDB->escape($_REQUEST['survey_fourth_status']);
	$survey_fourth_exten 								= $astDB->escape($_REQUEST['survey_fourth_exten']);
    $no_channels 										= $astDB->escape($_REQUEST['no_channels']);
    $disable_alter_custdata 							= $astDB->escape($_REQUEST['disable_alter_custdata']);
    $disable_alter_custphone 							= $astDB->escape($_REQUEST['disable_alter_custphone']);
	$amd_send_to_vmx 									= $astDB->escape($_REQUEST['amd_send_to_vmx']);
	$waitforsilence_options 							= $astDB->escape($_REQUEST['waitforsilence_options']);
	$location 											= $astDB->escape($_REQUEST['location_id']);

    // Default values 
    $defActive 											= array( "Y", "N" );	
    $defEnable 											= array( 0, 1 );	    
    $defDialMethod 										= array( "MANUAL", "RATIO", "ADAPT_HARD_LIMIT", "ADAPT_TAPERED", "ADAPT_AVERAGE", "INBOUND_MAN" );
    
    // Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif ( empty($campaign_id) || is_null($campaign_id)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= array(
			"code" 											=> "40001",
			"result" 										=> $err_msg
		);
    } elseif (!in_array($active,$defActive) && $active != null ) {
		$err_msg 										= error_handle( "41006", "active" );
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		); 
		//$apiresults = array("result" => "Error: Default value for active is Y or N only."); 
	} elseif (!in_array( $dial_method,$defDialMethod ) && $dial_method != null ) {
		$err_msg 										= error_handle( "41006", "dial_method" );
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		); 
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			// check campaign_id if it exists
			$resultGet									= $astDB
				->where( 'campaign_id', $campaign_id )
				->getOne( 'vicidial_campaigns' );
					
			if ( $astDB->count > 0 ) {
				//check if user has sufficient rights for campaign_id
				if ( in_array($campaign_id, $allowed_campaigns)) {						
					$dynamic_cid_SQL 					= "";
					$dynamic_cid_COL 					= "";
					$dynamic_cid_VAL 					= "";
					$checkColumn 						= $goDB->rawQuery( "SHOW COLUMNS FROM `go_campaigns` LIKE 'dynamic_cid'" );
					$columnRows 						= $goDB->getRowCount();

					if ( $columnRows > 0 ) {
						$data_update_go					= array(
							'dynamic_cid'					=> $dynamic_cid
						);
						
						$data_insert_go 				= array(
							'dynamic_cid' 					=> $dynamic_cid
						);
					}
					
					if (!empty($location)) {
						$data_update_go 				= array(
							'location_id' 					=> $location
						);
						
						$data_insert_go 				= array(
							'location_id' 					=> $location
						);
					}
					
					if (!empty($nextdial_seconds)) {
						$data_update 					= array(
							'nextdial_seconds' 				=> $nextdial_seconds
						);
					}

					if ( $campaign_type == "SURVEY" ) {
						if (empty($dial_method)) {
							$dial_method 				= "RATIO";
						}
					}
					
					if ( $dial_prefix == "CUSTOM" ) {
						$dialprefix 					= $custom_prefix;
					} else {
						$dialprefix 					= $dial_prefix;
					}
					
					if (!empty($am_message_chooser)) {
						$amMessageExten 				= $am_message_chooser;
					} else {
						$amMessageExten 				= $am_message_exten;
					}
					
					if ( $dial_method == "MANUAL" ) {
						$autoDialLevel 					= 0;
					} elseif ( $dial_method == "ADAPT_TAPERED" ) {
						$autoDialLevel 					= 1;
					} else {
						switch ( $auto_dial_level ) {
							case "OFF":
							
							$autoDialLevel 				= 0;
							break;
							
							case "SLOW":
						
							$autoDialLevel 				= 1;
							break;
							
							case "NORMAL":
						
							$autoDialLevel 				= 2;
							break;
							
							case "HIGH":
						
							$autoDialLevel 				= 4;
							break;
							
							case "MAX":
						
							$autoDialLevel 				= 6;
							break;
							
							case "MAX_PREDICTIVE":
						
							$autoDialLevel 				= 10;
							break;
							
							case "ADVANCE":
						
							$autoDialLevel 				= $auto_dial_level_adv;
							break;
							
							default:
							$autoDialLevel 				= 1;
							//DEFAULT HERE
						}
					}
					
					if ( $campaign_type != 'SURVEY' ) {
						$data_array01 						= array(
							'campaign_allow_inbound' 			=> (!empty($campaign_allow_inbound)) ? $campaign_allow_inbound : $resultGet['campaign_allow_inbound'], 
							'available_only_ratio_tally' 		=> (!empty($available_only_ratio_tally)) ? $available_only_ratio_tally : $resultGet['available_only_ratio_tally'], 
							'campaign_recording' 				=> (!empty($campaign_recording)) ? $campaign_recording : $resultGet['campaign_recording'], 
							'campaign_rec_filename' 			=> (!empty($campaign_rec_filename)) ? $campaign_rec_filename : $resultGet['campaign_rec_filename'], 
							'per_call_notes' 					=> (!empty($per_call_notes)) ? $per_call_notes : $resultGet['per_call_notes'], 
							'am_message_exten' 					=> $amMessageExten, 
							'agent_pause_codes_active' 			=> (!empty($agent_pause_codes_active)) ? $agent_pause_codes_active : $resultGet['agent_pause_codes_active'], 
							'manual_dial_filter' 				=> (!empty($manual_dial_filter)) ? $manual_dial_filter : $resultGet['manual_dial_filter'], 
							'manual_dial_search_filter' 		=> (!empty($manual_dial_search_filter)) ? $manual_dial_search_filter : $resultGet['manual_dial_search_filter'],
							'customer_3way_hangup_logging' 		=> (!empty($customer_3way_hangup_logging)) ? $customer_3way_hangup_logging : $resultGet['customer_3way_hangup_logging'], 
							'customer_3way_hangup_seconds' 		=> (!empty($customer_3way_hangup_seconds)) ? $customer_3way_hangup_seconds : $resultGet['customer_3way_hangup_seconds'], 
							'customer_3way_hangup_action' 		=> (!empty($customer_3way_hangup_action)) ? $customer_3way_hangup_action : $resultGet['customer_3way_hangup_action'],
							'alt_number_dialing' 				=> (!empty($alt_number_dialing)) ? $alt_number_dialing : $resultGet['alt_number_dialing']
						);
					}
						
					if ($campaign_type != 'SURVEY' && $dial_method != "INBOUND_MAN") {
						$data_array02 						= array(
							'use_internal_dnc' 					=> (!empty($use_internal_dnc)) ? $use_internal_dnc : $resultGet['use_internal_dnc'],
							'use_campaign_dnc' 					=> (!empty($use_campaign_dnc)) ? $use_campaign_dnc : $resultGet['use_campaign_dnc'],
							'three_way_call_cid' 				=> (!empty($three_way_call_cid)) ? $three_way_call_cid : $resultGet['three_way_call_cid'],
							//'three_way_call_cid' 				=> (!empty($three_way_call_cid)) ? $three_way_call_cid : "",
							'hopper_level' 						=> (!empty($hopper_level)) ? $hopper_level : $resultGet['hopper_level'],
							'alt_number_dialing' 				=> (!empty($alt_number_dialing)) ? $alt_number_dialing : $resultGet['alt_number_dialing']
						);
					}

					$data_array03							= array(
						'campaign_name' 						=> (!empty($campaign_name)) ? $campaign_name : $resultGet['campaign_name'],
						'campaign_description' 					=> (!empty($campaign_desc)) ? $campaign_desc : $resultGet['campaign_desc'], 
						'active' 								=> (!empty($active)) ? $active : $resultGet['active'], 
						'dial_method' 							=> (gettype($dial_method) != NULL) ? $dial_method : $resultGet['dial_method'], 
						'auto_dial_level' 						=> $autoDialLevel, 
						'dial_prefix' 							=> $dialprefix,
						'web_form_address' 						=> (!empty($webform)) ? $webform : $resultGet['web_form_address'], 
						'campaign_script' 						=> (!empty($campaign_script)) ? $campaign_script : $resultGet['campaign_script'], 
						'campaign_cid' 							=> (!empty($campaign_cid)) ? $campaign_cid : $resultGet['campaign_cid'], 
						'campaign_vdad_exten' 					=> (!empty($campaign_vdad_exten)) ? $campaign_vdad_exten : $resultGet['campaign_vdad_exten'], 
						'local_call_time' 						=> (!empty($local_call_time)) ? $local_call_time : $resultGet['local_call_time'],  
						'dial_status_a' 						=> (!empty($dial_status)) ? $dial_status : $resultGet['dial_status'], 
						//'lead_filter_id' 						=> (!empty($lead_filter)) ? $lead_filter : $resultGet['lead_filter_id'],
						'lead_filter_id' 						=> (!empty($lead_filter)) ? $lead_filter : '',
						'dial_timeout' 							=> (!empty($dial_timeout)) ? $dial_timeout : $resultGet['dial_timeout'], 
						//'manual_dial_prefix' 					=> (!empty($manual_dial_prefix)) ? $manual_dial_prefix : $resultGet['manual_dial_prefix'],
						'manual_dial_prefix' 					=> $manual_dial_prefix,
						'manual_dial_list_id' 				    => (!empty($manual_dial_list_id)) ? $manual_dial_list_id : $resultGet['manual_dial_list_id'], 
						'get_call_launch' 						=> (!empty($get_call_launch)) ? $get_call_launch : $resultGet['get_call_launch'], 
						'next_agent_call' 						=> (!empty($next_agent_call)) ? $next_agent_call : $resultGet['next_agent_call'], 
						'xferconf_a_number' 					=> (!empty($xferconf_a_number)) ? $xferconf_a_number : $resultGet['xferconf_a_number'], 
						'xferconf_b_number' 					=> (!empty($xferconf_b_number)) ? $xferconf_b_number : $resultGet['xferconf_b_number'], 
						//'three_way_dial_prefix' 				=> (!empty($three_way_dial_prefix)) ? $three_way_dial_prefix : $resultGet['three_way_dial_prefix'],
						'three_way_dial_prefix' 				=> $three_way_dial_prefix,
						'closer_campaigns' 						=> (!empty($closer_campaigns)) ? $closer_campaigns : $resultGet['closer_campaigns'],
						'xfer_groups' 							=> (!empty($xfer_groups)) ? $xfer_groups : $resultGet['xfer_groups'],
						'survey_first_audio_file' 				=> (!empty($survey_first_audio_file)) ? $survey_first_audio_file : $resultGet['survey_first_audio_file'],
						'survey_method' 						=> (!empty($survey_method)) ? $survey_method : $resultGet['survey_method'],
						'survey_menu_id' 						=> (!empty($survey_menu_id)) ? $survey_menu_id : $resultGet['survey_menu_id'],
						'survey_dtmf_digits' 					=> (!empty($survey_dtmf_digits)) ? $survey_dtmf_digits : $resultGet['survey_dtmf_digits'],
						'survey_xfer_exten' 					=> (!empty($survey_xfer_exten)) ? $survey_xfer_exten : $resultGet['survey_xfer_exten'],
						'survey_ni_digit' 						=> (!empty($survey_ni_digit)) ? $survey_ni_digit : $resultGet['survey_ni_digit'],
						'survey_ni_audio_file' 					=> (!empty($survey_ni_audio_file)) ? $survey_ni_audio_file : $resultGet['survey_ni_audio_file'],
						'survey_ni_status' 						=> (!empty($survey_ni_status)) ? $survey_ni_status : $resultGet['survey_ni_status'],
						'survey_third_digit' 					=> (!empty($survey_third_digit)) ? $survey_third_digit : $resultGet['survey_third_digit'],
						'survey_third_audio_file' 				=> (!empty($survey_third_audio_file)) ? $survey_third_audio_file : $resultGet['survey_third_audio_file'],
						'survey_third_status' 					=> (!empty($survey_third_status)) ? $survey_third_status : $resultGet['survey_third_status'],
						'survey_third_exten' 					=> (!empty($survey_third_exten)) ? $survey_third_exten : $resultGet['survey_third_exten'],
						'survey_fourth_digit' 					=> (!empty($survey_fourth_digit)) ? $survey_fourth_digit : $resultGet['survey_fourth_digit'],
						'survey_fourth_audio_file' 				=> (!empty($survey_fourth_audio_file)) ? $survey_fourth_audio_file : $resultGet['survey_fourth_audio_file'],
						'survey_fourth_status' 					=> (!empty($survey_fourth_status)) ? $survey_fourth_status : $resultGet['survey_fourth_status'],
						'survey_fourth_exten' 					=> (!empty($survey_fourth_exten)) ? $survey_fourth_exten : $resultGet['survey_fourth_exten'],
						'amd_send_to_vmx' 						=> (!empty($amd_send_to_vmx)) ? $amd_send_to_vmx : $resultGet['amd_send_to_vmx'],
						'waitforsilence_options' 				=> ( gettype($waitforsilence_options) != NULL) ? $waitforsilence_options : $resultGet['waitforsilence_options'],
						'agent_lead_search' 					=> (!empty($agent_lead_search)) ? $agent_lead_search : $resultGet['agent_lead_search'],
						'agent_lead_search_method' 				=> (!empty($agent_lead_search_method)) ? $agent_lead_search_method : $resultGet['agent_lead_search_method'],
						'omit_phone_code' 						=> (!empty($omit_phone_code)) ? $omit_phone_code : $resultGet['omit_phone_code'],
						'disable_alter_custdata' 				=> (!empty($disable_alter_custdata)) ? $disable_alter_custdata : $resultGet['disable_alter_custdata'],
						'disable_alter_custphone' 				=> (!empty($disable_alter_custphone)) ? $disable_alter_custphone : $resultGet['disable_alter_custphone'],
						'my_callback_option' 					=> (!empty($my_callback_option)) ? $my_callback_option : $resultGet['my_callback_option'],
						'lead_order' 							=> (!empty($lead_order)) ? $lead_order : $resultGet['lead_order'],
						'lead_order_secondary'					=> (!empty($lead_order_secondary)) ? $lead_order_secondary : $resultGet['lead_order_secondary'],
                                                'campaign_recording'                            => (!empty($campaign_recording)) ? $campaign_recording : $resultGet['campaign_recording'],
                                                'campaign_rec_filename'                         => (!empty($campaign_rec_filename)) ? $campaign_rec_filename : $resultGet['campaign_rec_filename'],
						'hopper_level'                                          => (!empty($hopper_level)) ? $hopper_level : $resultGet['hopper_level']
					);
					
					if ( $campaign_type == 'SURVEY' ) {
						$data_update						= $data_array03;
					} else {
						if (gettype($data_array02) == "array" ) {
							$data_update 					= array_merge( $data_array01, $data_array02, $data_array03 );
						} else {
							$data_update 					= array_merge( $data_array01, $data_array03 );
						}
					}
					
					$astDB->where( 'campaign_id', $campaign_id );
					$astDB->update( 'vicidial_campaigns', $data_update );
					
					$log_id 								= log_action( $goDB, 'MODIFY', $log_user, $log_ip, "Updated campaign settings for $campaign_id", $log_group, $astDB->getLastQuery());
					
					$goDB->where( 'campaign_id', $campaign_id );
					$checkCampGODB 							= $goDB->get( 'go_campaigns' );

					$url_tab_first_url 						= str_replace( "http://", "https://", $url_tab_first_url );
					$url_tab_second_url 					= str_replace( "http://", "https://", $url_tab_second_url );
					
					if ( $checkCampGODB ) {
						$data_update_go 					= array(
							'custom_fields_launch' 				=> (!empty($custom_fields_launch)) ? $custom_fields_launch : $resultGet['custom_fields_launch'], 
							'custom_fields_list_id' 			=> (!empty($custom_fields_list_id)) ? $custom_fields_list_id : $resultGet['custom_fields_list_id'],
							'url_tab_first_title' 				=> (!empty($url_tab_first_title)) ? $url_tab_first_title : $resultGet['url_tab_first_title'],
							'url_tab_first_url' 				=> (!empty($url_tab_first_url)) ? $url_tab_first_url : $resultGet['url_tab_first_url'],
							'url_tab_second_title' 				=> (!empty($url_tab_second_title)) ? $url_tab_second_title : $resultGet['url_tab_second_title'],
							'url_tab_second_url' 				=> (!empty($url_tab_second_url)) ? $url_tab_second_url : $resultGet['url_tab_second_url'],
							'enable_callback_alert' 			=> (gettype($enable_callback_alert) != 'NULL') ? $enable_callback_alert : $resultGet['enable_callback_alert'],
							'cb_noexpire' 						=> (gettype($cb_noexpire) != 'NULL') ? $cb_noexpire : $resultGet['cb_noexpire'],
							'cb_sendemail' 						=> (gettype($cb_sendemail) != 'NULL') ? $cb_sendemail : $resultGet['cb_sendemail'],
                            'manual_dial_min_digits'            => (gettype($manual_dial_min_digits) != 'NULL') ? $manual_dial_min_digits : $resultGet['manual_dial_min_digits']
						);
						
						$goDB->where( 'campaign_id', $campaign_id );
						$goDB->update( 'go_campaigns', $data_update_go );
						
						$log_id 							= log_action( $goDB, 'MODIFY', $log_user, $log_ip, "Updated campaign settings for $campaign_id", $log_group, $goDB->getLastQuery());
						
					} else {
						$campaign_type 						= ( strlen($campaign_type) > 0 ) ? $campaign_type : "OUTBOUND";
						$data_insert_go 					= array(
							'campaign_id' 						=> $campaign_id, 
							'campaign_type' 					=> $campaign_type, 
							'custom_fields_launch' 				=> (!empty($custom_fields_launch)) ? $custom_fields_launch : $resultGet['custom_fields_launch'], 
							'custom_fields_list_id' 			=> (!empty($custom_fields_list_id)) ? $custom_fields_list_id : $resultGet['custom_fields_list_id'],
							'url_tab_first_title' 				=> (!empty($url_tab_first_title)) ? $url_tab_first_title : $resultGet['url_tab_first_title'],
							'url_tab_first_url' 				=> (!empty($url_tab_first_url)) ? $url_tab_first_url : $resultGet['url_tab_first_url'],
							'url_tab_second_title' 				=> (!empty($url_tab_second_title)) ? $url_tab_second_title : $resultGet['url_tab_second_title'],
							'url_tab_second_url' 				=> (!empty($url_tab_second_url)) ? $url_tab_second_url : $resultGet['url_tab_second_url'],
							'enable_callback_alert' 			=> ( gettype($enable_callback_alert) != 'NULL' ) ? $enable_callback_alert : $resultGet['enable_callback_alert'],
							'cb_noexpire' 						=> ( gettype($cb_noexpire) != 'NULL' ) ? $cb_noexpire : $resultGet['cb_noexpire'],
							'cb_sendemail' 						=> ( gettype($cb_sendemail) != 'NULL' ) ? $cb_sendemail : $resultGet['cb_sendemail'],
                            'manual_dial_min_digits'            => ( gettype($manual_dial_min_digits) != 'NULL' ) ? $manual_dial_min_digits : $resultGet['manual_dial_min_digits']
						);

						$goDB->insert('go_campaigns', $data_insert_go);
						$log_id 						= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Updated campaign settings for $campaign_id", $log_group, $goDB->getLastQuery());
					}
				
					if ( $force_reset_hopper == "Y" ) {
						$astDB->where( 'campaign_id', $campaign_id );
						$astDB->where( 'status', array( 'READY','QUEUE','DONE' ), 'IN' );
						$astDB->delete( 'vicidial_hopper' );
						
						$log_id 						= log_action( $goDB, 'MODIFY', $log_user, $log_ip, "Updated campaign settings for $campaign_id", $log_group, $astDB->getLastQuery());
					}
					
					if ( $campaign_type == "SURVEY" ) {
						if ( $survey_method != "AGENT_XFER" && $active == 'Y' ) {
							$astDB->where( 'campaign_id', $campaign_id );
							$astDB->update( 'vicidial_remote_agents', array( 'status' => 'ACTIVE'));
							
							$log_id 					= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Updated campaign settings for $campaign_id", $log_group, $astDB->getLastQuery());
							
						} else {
							$astDB->where( 'campaign_id', $campaign_id );
							$astDB->update( 'vicidial_remote_agents', array( 'status' => 'INACTIVE'));
							
							$log_id 					= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Updated campaign settings for $campaign_id", $log_group, $astDB->getLastQuery());
						}
						
						if (!empty($no_channels)) {
							$astDB->where( 'campaign_id', $campaign_id );
							$astDB->update( 'vicidial_remote_agents', array( 'number_of_lines' => $no_channels));
							
							$log_id 					= log_action( $goDB, 'MODIFY', $log_user, $log_ip, "Updated campaign settings for $campaign_id", $log_group, $astDB->getLastQuery());
						}
					}
					
					$apiresults 						= array(
						"result" 							=> "success"
					);
				} else {
					$err_msg 							= error_handle( "10001", "Insufficient permision" );
					$apiresults 						= array(
						"code" 								=> "10001", 
						"result" 							=> $err_msg
					);			
				}
			} else {
				$err_msg 								= error_handle( "41004", "Campaign doesn't exist" );
				$apiresults 							= array(
					"code" 									=> "41004", 
					"result" 								=> $err_msg
				); 
			}				
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}

?>
