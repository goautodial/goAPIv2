<?php
/**
 * @file 		goAddCampaign.php
 * @brief 		API to add campaign
 * @copyright 	Copyright (c) 2019 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
 * @author     	Alexander Jim Abenoja 
 * @author     	Jeremiah Sebastian Samatra
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
    
    include_once ("goAPI.php");

	$campaign_id 										= $astDB->escape( $_REQUEST['campaign_id'] );
	$campaign_name 										= $astDB->escape( $_REQUEST['campaign_name'] );
	$campaign_type 										= $astDB->escape( strtoupper($_REQUEST['campaign_type']) );
	$active 											= $astDB->escape( $_REQUEST['active'] );
	$dial_method 										= $astDB->escape( $_REQUEST['dial_method'] );
	$dial_statuses 										= $astDB->escape( $_REQUEST['dial_statuses'] );
	$lead_order 										= $astDB->escape( $_REQUEST['lead_order'] );
	$allow_closers 										= $astDB->escape( $_REQUEST['allow_closers'] );
	$hopper_level 										= $astDB->escape( $_REQUEST['hopper_level'] );
	$auto_dial_level 									= $astDB->escape( $_REQUEST['auto_dial_level'] );
	$auto_dial_level_adv 								= $astDB->escape( $_REQUEST['auto_dial_level_adv'] );
	$dial_prefix 										= $astDB->escape( $_REQUEST['dial_prefix'] );
	$campaign_changedate 								= $astDB->escape( $_REQUEST['campaign_changedate'] );
	$campaign_stats_refresh 							= $astDB->escape( $_REQUEST['campaign_stats_refresh'] );
	$campaign_vdad_exten 								= $astDB->escape( $_REQUEST['campaign_vdad_exten'] );
	$campaign_recording 								= $astDB->escape( $_REQUEST['campaign_recording'] );
	$campaign_rec_filename 								= $astDB->escape( $_REQUEST['campaign_rec_filename'] );
	$scheduled_callbacks 								= $astDB->escape( $_REQUEST['scheduled_callbacks'] );
	$scheduled_callbacks_alert 							= $astDB->escape( $_REQUEST['scheduled_callbacks_alert'] );
	$no_hopper_leads_logins 							= $astDB->escape( $_REQUEST['no_hopper_leads_logins'] );
	$use_internal_dnc 									= $astDB->escape( $_REQUEST['use_internal_dnc'] );
	$use_campaign_dnc 									= $astDB->escape( $_REQUEST['use_campaign_dnc'] );
	$campaign_cid 										= $astDB->escape( $_REQUEST['campaign_cid'] );
	$user_group 										= $astDB->escape( $_REQUEST['user_group'] );
	$drop_call_seconds 									= $astDB->escape( $_REQUEST['drop_call_seconds'] );
	$goUsers 											= $astDB->escape( $_REQUEST['goUser'] );
	$values 											= $astDB->escape( $_REQUEST['items'] );
	$did_pattern 										= $astDB->escape( $_REQUEST['did_tfn_extension'] );
	$group_color 										= $astDB->escape( $_REQUEST['group_color'] );
	$call_route 										= $astDB->escape( $_REQUEST['call_route'] );
	$call_route_text 									= $astDB->escape( $_REQUEST['call_route_text'] );
	$survey_type 										= $astDB->escape( $_REQUEST['survey_type'] );
	$number_channels 									= $astDB->escape( $_REQUEST['no_channels'] );
	$copy_from_campaign 								= $astDB->escape( $_REQUEST['copy_from_campaign'] );
	$list_id 											= $astDB->escape( $_REQUEST['list_id'] );
	$country 											= $astDB->escape( $_REQUEST['country'] );
	$check_for_duplicates 								= $astDB->escape( $_REQUEST['check_for_duplicates'] );			
	$dial_prefix 										= $astDB->escape( $_REQUEST['dial_prefix'] );
	$custom_dial_prefix									= $astDB->escape( $_REQUEST['custom_dial_prefix'] );
	$status 											= $astDB->escape( $_REQUEST['status'] );									
	$script 											= $astDB->escape( $_REQUEST['script'] );						
	$ans_machine_detection 								= $astDB->escape( $_REQUEST['answering_machine_detection'] );
	
	if ( $ans_machine_detection == "" ) {
		if ( $dial_method == "MANUAL" && $dial_method == "INBOUND_MAN" ) {
			$ans_machine_detection 						= '8368';
		} else {
			$ans_machine_detection 						= '8369';
		}
	}
	
	$caller_id 											= $astDB->escape( $_REQUEST['caller_id'] ); 					
	$force_reset_hopper 								= $astDB->escape( $_REQUEST['force_reset_hopper'] );			
	$inbound_man 										= $astDB->escape( $_REQUEST['inbound_man'] );					
	$phone_numbers 										= $astDB->escape( $_REQUEST['phone_numbers'] );
	$lead_file											= $_FILES['lead_file']['tmp_name'];
	$leads												= $_FILES['leads']['tmp_name'];	
	$call_time 											= $astDB->escape( $_REQUEST['call_time'] );
	$dial_status 										= $astDB->escape( $_REQUEST['dial_status'] );
	$list_order 										= $astDB->escape( $_REQUEST['list_order'] );
	$lead_filter 										= $astDB->escape( $_REQUEST['lead_filter'] );
	$dial_timeout 										= $astDB->escape( $_REQUEST['dial_timeout'] );
	$manual_dial_prefix 								= $astDB->escape( $_REQUEST['manual_dial_prefix'] );
	$call_launch 										= $astDB->escape( $_REQUEST['call_lunch'] );
	$answering_machine_message 							= $astDB->escape( $_REQUEST['answering_machine_message'] );
	$pause_codes 										= $astDB->escape( $_REQUEST['pause_codes'] );
	$manual_dial_filter 								= $astDB->escape( $_REQUEST['manual_dial_filter'] );
	$manual_dial_list_id 								= $astDB->escape( $_REQUEST['manual_dial_list_id'] );
	$availability_only_tally 							= $astDB->escape( $_REQUEST['availability_only_tally'] );
	$recording_filename 								= $astDB->escape( $_REQUEST['recording_filename'] );
	$next_agent_call 									= $astDB->escape( $_REQUEST['next_agent_call'] );
	$caller_id_3_way_call 								= $astDB->escape( $_REQUEST['caller_id_3_way_call'] );
	$dial_prefix_3_way_call 							= $astDB->escape( $_REQUEST['dial_prefix_3_way_call'] );
	$three_way_hangup_logging 							= $astDB->escape( $_REQUEST['three_way_hangup_logging'] );
	$three_way_hangup_seconds 							= $astDB->escape( $_REQUEST['three_way_hangup_seconds'] );
	$three_way_hangup_action 							= $astDB->escape( $_REQUEST['three_way_hangup_action'] );
	$reset_leads_on_hopper 								= $astDB->escape( $_REQUEST['reset_leads_on_hopper'] );
	$location 											= $astDB->escape( $_REQUEST['location_id'] );

	/* Default values */ 
	$defActive 											= array( "Y", "N" );
	$defType 											= array( "OUTBOUND", "INBOUND", "BLENDED", "SURVEY", "COPY" );
		
	if ( $dial_prefix == "CUSTOM" ) {
		$sippy_dial_prefix 								= $custom_dial_prefix;
	} else {
		$sippy_dial_prefix 								= $dial_prefix;
	}
	
	if ( $dial_method == "MANUAL" ) {
		$autoDialLevel 									= 0;
	} elseif ( $dial_method == "ADAPT_TAPERED" ) {
		$autoDialLevel 									= 1;
	} else {
		switch ( $auto_dial_level ) {
			case "OFF":
				$autoDialLevel 							= 0;
				break;
			case "SLOW":
				$autoDialLevel 							= 1;
				break;
			case "NORMAL":
				$autoDialLevel 							= 2;
				break;
			case "HIGH":
				$autoDialLevel 							= 4;
				break;
			case "MAX":
				$autoDialLevel 							= 6;
				break;
			case "MAX_PREDICTIVE":
				$autoDialLevel 							= 10;
				break;
			case "ADVANCE":
				$autoDialLevel 							= $auto_dial_level_adv;
				break;
			default:
				//DEFAULT HERE
		}
	}  

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
	} elseif ( empty( $campaign_id ) || empty( $campaign_type ) || empty( $campaign_name ) ) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= array(
			"code" 											=> "40001",
			"result" 										=> $err_msg
		);
    } elseif ( !in_array( $campaign_type,$defType ) && $campaign_type != null ) {
		$err_msg 										= error_handle( "10003", "campaign_type" );
		$apiresults 									= array(
			"code" 											=> "10003", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Default value for campaign type is OUTBOUND, INBOUND, BLENDED and  SURVEY only.");
    } elseif ( strlen($campaign_id) < 8  ) {
    	$err_msg 										= error_handle( "41006", "campaign_id. Limit is 8 Characters." );
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		);
    } elseif ( !empty($location) ) {
		$result_location 								= go_check_location( $location, $user_group );
		if ( $result_location < 1 ) {
			$err_msg 									= error_handle( "41006", "location. User group does not exist in the location selected." );
			$apiresults 								= array(
				"code" 										=> "41006", 
				"result" 									=> $err_msg
			);
			
			$location 									= "";
		}
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {				
			$location 									= "";
		
			// check if already existing in whole system
			$astDB->where( "campaign_id", $campaign_id );
			$astDB->getOne( "vicidial_campaigns", "campaign_id" );
		    
			if ( $astDB->count > 0 ) {
				$err_msg 									= error_handle( "10109" );
				$apiresults 								= array(
					"result" 									=> $err_msg
				);
			} else {
				//$campaign_id  = $astDB->escape( $campaign_id);
				$campaign_desc 								= str_replace( '+',' ',$campaign_name );
				$SQLdate 									= date( "Y-m-d H:i:s" );
				$NOW 										= date( "Y-m-d" );
				
				// Outbound Campaign here
				if ( $campaign_type == "OUTBOUND" ) {
					// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
					// every time we need to filter out requests
					$tenant									=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
					
					if ($tenant) {
						$tenant_id 							= "$log_group";
					} else {
						$tenant_id 							= '---ALL---';
						
						if (strtoupper($log_group) !== 'ADMIN') {
							//if ($userlevel > 8) {
								$tenant_id 					= "$log_group";
							//}
						}					
					}

					if ( $campaign_id != 'undefined' && $campaign_id != '' ) {
						$local_call_time 					= "9am-9pm";

						$data_outbound 						= array(
							'campaign_id' 						=> $campaign_id, 
							'campaign_name' 					=> $campaign_desc, 
							'active' 							=> 'Y', 
							'dial_method' 						=> $dial_method, 
							'dial_status_a' 					=> 'NEW',	
							'dial_statuses' 					=> ' N NA A AA DROP B NEW -', 
							'lead_order' 						=> 'DOWN', 
							'allow_closers' 					=> 'Y', 
							'hopper_level'						=> 100, 
							'auto_dial_level' 					=> 0, 
							'next_agent_call' 					=> 'oldest_call_finish', 
							'local_call_time' 					=> $local_call_time, 
							'dial_prefix' 						=> $sippy_dial_prefix, 
							'get_call_launch' 					=> 'NONE', 
							'campaign_changedate' 				=> $SQLdate, 
							'campaign_stats_refresh' 			=> 'Y', 
							'list_order_mix' 					=> 'DISABLED', 
							'dial_timeout' 						=> 30, 
							'campaign_recording' 				=> $campaign_recording, 
							'campaign_rec_filename' 			=> 'FULLDATE_CUSTPHONE_CAMPAIGN_AGENT', 
							'scheduled_callbacks' 				=> 'Y', 
							'scheduled_callbacks_alert' 		=> 'BLINK_RED', 
							'no_hopper_leads_logins' 			=> 'Y', 
							'use_internal_dnc' 					=> 'Y', 
							'use_campaign_dnc' 					=> 'Y', 
							'available_only_ratio_tally' 		=> 'Y', 
							'campaign_cid' 						=> 5164536886, 
							//'manual_dial_filter' 				=> 'DNC_AND_CAMPLISTS_ALL',
							'manual_dial_filter' 				=> 'NONE',
							'manual_dial_search_filter'			=> 'CAMPLISTS_ALL', 
							'user_group' 						=> $tenant_id,	
							'manual_dial_list_id' 				=> $tenant_id.'998', 
							'drop_call_seconds' 				=> 7, 
							'campaign_vdad_exten' 				=> $ans_machine_detection, 
							'disable_alter_custdata' 			=> 'N', 
							'disable_alter_custphone' 			=> 'Y', 
							'campaign_script' 					=> $script
						);
						
						$q_insertOutbound 					= $astDB->insert( 'vicidial_campaigns', $data_outbound );					
						$log_id 							= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Outbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
                        
                        $astDB->where('user_group', $log_group);
                        $allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
                        $allowed_campaigns = $allowed_camps['allowed_campaigns'];
                        
                        if (strlen($allowed_campaigns) < 1) { 
                            $allowed_campaigns = " -"; 
                        }
                        
                        if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
                            $update_data = array(
                                'allowed_campaigns' 					=>  " $campaign_id " . trim($allowed_campaigns)
                            );
                            
                            $astDB->where('user_group', $log_group);
                            $q_updateAllowedCampaign = $astDB->update('vicidial_user_groups', $update_data);
                        }
						
						if ( $q_insertOutbound ) {
							$datago_campaign 				= array(
								'campaign_id' 					=> $campaign_id, 
								'campaign_type' 				=> $campaign_type
								// 'location_id' 				=> (!empty($location))? $location:''
							);
							
							$goDB->insert( "go_campaigns", $datago_campaign );
							$log_id 						= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Outbound Campaign: $campaign_id", $log_group, $goDB->getLastQuery() );
							
							$apiresults 					= array(
								"result" 						=> "success"
							);
						} else {
							$err_msg 						= error_handle( "10010" );
							$apiresults 					= array(
								"code" 							=> "10010", 
								"result" 						=> $err_msg
							);
						}
					}
				}
				// End of OUTBOUND

				// Inbound Campaign here
				if ( $campaign_type == "INBOUND" ) {
					$defCallRoute 							= array( "INGROUP", "IVR", "AGENT", "VOICEMAIL" );
					
					$callRoute 								= strtoupper( $call_route );
					$campaign_desc 							= str_replace( '+',' ',$campaign_name );
					$SQLdate 								= date( "Y-m-d H:i:s" );
					$NOW 									= date( "Y-m-d" );

					if ( $tenant ) {
						$tenant_id 							= "$log_group";
					} else {
						$tenant_id 							= '---ALL---';
						
						if (strtoupper($log_group) !== 'ADMIN') {
							$tenant_id 					    = "$log_group";
						}
					}

					$local_call_time 						= "9am-9pm";
					$auth_user 								= $goUsers;
					
					$data_inbound 							= array(
						'campaign_id' 							=> $campaign_id, 
						'campaign_name' 						=> $campaign_desc, 
						'active' 								=> 'Y', 
						'dial_method' 							=> $dial_method, 
						'dial_status_a' 						=> 'NEW', 
						'dial_statuses' 						=> ' N NA A AA DROP B NEW -', 
						'lead_order' 							=> 'DOWN', 
						'allow_closers' 						=> 'Y', 
						'hopper_level' 							=> 100, 
						'auto_dial_level' 						=> 1.0, 
						'next_agent_call' 						=> 'oldest_call_finish', 
						'local_call_time' 						=> $local_call_time, 
						'dial_prefix' 							=> $sippy_dial_prefix, 
						'get_call_launch' 						=> 'NONE', 
						'campaign_changedate' 					=> $SQLdate, 
						'campaign_stats_refresh' 				=> 'Y', 
						'list_order_mix' 						=> 'DISABLED', 
						'dial_timeout' 							=> 30, 
						'campaign_vdad_exten' 					=> $ans_machine_detection, 
						'campaign_recording' 					=> 'ALLFORCE', 
						'campaign_rec_filename' 				=> 'FULLDATE_CUSTPHONE_CAMPAIGN_AGENT', 
						'scheduled_callbacks' 					=> 'Y', 
						'scheduled_callbacks_alert' 			=> 'BLINK_RED', 
						'no_hopper_leads_logins' 				=> 'Y', 
						'use_internal_dnc' 						=> 'Y', 
						'use_campaign_dnc' 						=> 'Y', 
						'available_only_ratio_tally' 			=> 'Y', 
						'campaign_cid' 							=> 5164536886, 
						//'manual_dial_filter' 					=> 'DNC_AND_CAMPLISTS_ALL',
						'manual_dial_filter' 					=> 'NONE',
						'manual_dial_search_filter'				=> 'CAMPLISTS_ALL',
						'user_group' 							=> $tenant_id, 
						'manual_dial_list_id' 					=> $tenant_id.'998', 
						'drop_call_seconds' 					=> 7, 
						'manual_dial_prefix' 					=> $manual_dial_prefix, 
						'am_message_exten' 						=> $answering_machine_message, 
						'agent_pause_codes_active' 				=> $pause_codes, 
						'three_way_call_cid' 					=> $caller_id_3_way_call, 
						'three_way_dial_prefix' 				=> $dial_prefix_3_way_call, 
						'customer_3way_hangup_logging' 			=> $three_way_hangup_logging, 
						'customer_3way_hangup_seconds' 			=> $three_way_hangup_seconds, 
						'customer_3way_hangup_action' 			=> $three_way_hangup_action, 
						'campaign_allow_inbound' 				=> 'Y', 
						'disable_alter_custdata' 				=> 'N', 
						'disable_alter_custphone' 				=> 'Y', 
						'campaign_script' 						=> $script
					);
					
					$q_insertInbound 						= $astDB->insert( 'vicidial_campaigns', $data_inbound );
					$log_id 								= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Outbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );

					$q_insertVCS 							= $astDB->insert( 'vicidial_campaign_stats', array('campaign_id' => $campaign_id) );
					$log_id 								= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Outbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
                    
                    $astDB->where('user_group', $log_group);
                    $allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
                    $allowed_campaigns = $allowed_camps['allowed_campaigns'];
                    
                    if (strlen($allowed_campaigns) < 1) { 
                        $allowed_campaigns = " -"; 
                    }
                    
                    if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
                        $update_data = array(
                            'allowed_campaigns' 					=>  " $campaign_id " . trim($allowed_campaigns)
                        );
                        
                        $astDB->where('user_group', $log_group);
                        $q_updateAllowedCampaign = $astDB->update('vicidial_user_groups', $update_data);
                    }
					
					if ( $q_insertInbound ) {
						if ( $callRoute != null ) {
							// Call Route
							$didDesc 						= $campaign_id." ".$campaign_type." DID";
							$didPattern 					= $call_route_text;
							
							$astDB->where( 'did_pattern', $did_pattern );
							$resultDID 						= $astDB->getOne( 'vicidial_inbound_dids', 'did_pattern' );
							$serverIP 						= $_SERVER['REMOTE_ADDR'];
							
							switch ( $callRoute ) {
								case "INGROUP":
								
								if ( $resultDID ) {
									$update_ing 					= array(
										'did_description' 				=> $didDesc,
										'did_active' 					=> 'Y',
										'did_route' 					=> 'IN_GROUP',
										'user_route_settings_ingroup' 	=> $call_route_text,
										'campaign_id' 					=> $campaign_id,
										'record_call' 					=> 'N',
										'filter_list_id' 				=> $list_id,
										'filter_campaign_id' 			=> $campaign_id,
										'group_id' 						=> $call_route_text,
										'server_ip' 					=> $serverIP,
										'user_group' 					=> $tenant_id
									);
									
									$astDB->where( 'did_pattern', $did_pattern );
									$astDB->update( 'vicidial_inbound_dids', $update_ing );
									$log_id 						= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
									
								} else {
									$data_ing 						= array(
										'did_pattern' 					=> $did_pattern,
										'did_description' 				=> $didDesc,
										'did_active' 					=> 'Y',
										'did_route' 					=> 'IN_GROUP',
										'user_route_settings_ingroup' 	=> 'AGENTDIRECT',
										'campaign_id' 					=> $campaign_id,
										'record_call' 					=> 'N',
										'filter_list_id' 				=> $list_id,
										'filter_campaign_id' 			=> $campaign_id,
										'group_id' 						=> 'AGENTDIRECT',
										'server_ip' 					=> $serverIP,
										'user_group' 					=> $tenant_id
									);
									
									$astDB->insert( 'vicidial_inbound_dids', $data_ing );	
									$log_id 						= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
								}
								
								$update_VC 							= array(
									'xfer_groups' 						=> $call_route_text.' -', 
									'closer_campaigns' 					=> $call_route_text.' -' 
								);
								
								$astDB->where( 'campaign_id', $campaign_id );
								$astDB->update( 'vicidial_campaigns', $update_VC );
								$log_id 							= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
									
								break;

								case "IVR":
								
								$menuID 							= $call_route_text;
								$data_VCM 							= array(
									'menu_id' 							=> $menuID,
									'menu_name' 						=> $menuID.' Inbound Call Menu',
									'user_group' 						=> $tenant_id
								);
								
								$astDB->insert( 'vicidial_call_menu', $data_VCM );
								$log_id 							= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
								
								if ( $resultDID ) {
									$update_VID 					= array(
										'did_description' 				=> $didDesc,
										'did_active' 					=> 'Y',
										'did_route' 					=> 'CALLMENU',
										'campaign_id' 					=> $campaign_id,
										'record_call' 					=> 'N',
										'filter_list_id' 				=> $list_id,
										'filter_campaign_id' 			=> $campaign_id,
										'server_ip' 					=> $serverIP,
										'menu_id' 						=> $call_route_text,
										'user_group' 					=> $tenant_id
									);
									$astDB->where( 'did_pattern', $did_pattern );
									$astDB->update( 'vicidial_inbound_dids', $update_VID );
								$log_id 							= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
									
								} else {
									$data_vid 						= array(
										'did_pattern' 					=> $did_pattern,
										'did_description' 				=> $didDesc,
										'did_active' 					=> 'Y',
										'did_route' 					=> 'CALLMENU',
										'campaign_id' 					=> $campaign_id,
										'record_call' 					=> 'N',
										'filter_list_id' 				=> $list_id,
										'filter_campaign_id' 			=> $campaign_id,
										'server_ip' 					=> $serverIP,
										'menu_id' 						=> 'defaultlog',
										'user_group' 					=> $tenant_id
									);
									
									$astDB->insert( 'vicidial_inbound_dids', $data_vid );	
									$log_id 						= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
									
								}
								
								break;

								case "AGENT":
								
								$data_agent	 						= array(
									'did_pattern' 						=> $did_pattern,
									'did_description' 					=> $didDesc,
									'did_active' 						=> 'Y',
									'did_route' 						=> 'AGENT',
									'user_route_settings_ingroup' 		=> $group_id,
									'campaign_id' 						=> $campaign_id,
									'record_call' 						=> 'N',
									'filter_list_id' 					=> $list_id,
									'filter_campaign_id' 				=> $campaign_id,
									'user' 								=> $call_route_text,
									'group_id' 							=> $group_id,
									'server_ip' 						=> $serverIP,
									'user_group' 						=> $tenant_id
								);
								
								$astDB->insert( 'vicidial_inbound_dids', $data_agent );	
								$log_id 								= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
								
								break;

								case "VOICEMAIL":
								
								if ( $emailORagent=='undefined' ) $emailORagent='';
								
								$data_vv 							= array(
									'voicemail_id' 						=> $campaign_id,
									'pass' 								=> $campaign_id,
									'email' 							=> $emailORagent,
									'fullname' 							=> $campaign_id.' VOICEMAIL',
									'active' 							=> 'Y',
									'user_group' 						=> $tenant_id
								);
								
								$astDB->insert( 'vicidial_voicemail', $data_vv );
								$log_id 							= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
								
								$data_vociemail 					= array(
									'did_pattern'						=> $did_pattern,
									'did_description'					=> $didDesc,
									'did_active'						=> 'Y',
									'did_route'							=> 'VOICEMAIL',
									'user_route_settings_ingroup'		=> $group_id,
									'campaign_id'						=> $campaign_id,
									'record_call'						=> 'N',
									'filter_list_id'					=> $list_id,
									'filter_campaign_id'				=> $campaign_id,
									'voicemail_ext'						=> $call_route_text,
									'user_group'						=> $tenant_id,
									'server_ip' 						=> $serverIP
								);
								
								$astDB->insert( 'vicidial_inbound_dids', $data_vociemail );
								$log_id 					= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
									
								break;
							}
							
							$astDB->where( 'campaign_id', $campaign_id);
							$astDB->update( 'vicidial_campaigns', array('campaign_allow_inbound' => 'Y') );
							$log_id 						= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
							
							$astDB->where( 'user', $userID);
							$astDB->update( 'vicidial_campaigns', array('modify_inbound_dids' => 1) );
							$log_id 						= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
						}

						$SQLdate 							= date( "Y-m-d H:i:s" );
						//$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $insertQuery);

						$datago_campaign 					= array(
							'campaign_id' 						=> $campaign_id, 
							'campaign_type'						=> $campaign_type
							//'location_id' 					=> (!empty($location))? $location:''
						);
						
						$goDB->insert( 'go_campaigns', $datago_campaign );
						$log_id 							= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $goDB->getLastQuery() );
						
						$apiresults 						= array(
							"result" 							=> "success"
						);					
					} else {
						$err_msg 							= error_handle( "10010" );
						$apiresults 						= array(
							"code" 								=> "10010", 
							"result" 							=> $err_msg
						);
					}
				}
				// End of INBOUND
				
				// Blended Campaign here
				if ( $campaign_type == "BLENDED" ) {
					$defCallRoute 							= array( "INGROUP", "IVR", "AGENT", "VOICEMAIL" );
					
					//$campaign_id = $astDB->escape( $campaign_id);
					$didPattern 							= $did_pattern;
					$groupColor 							= $group_color;
					$emailORagent 							= $goUsers;
					$campaign_desc 							= str_replace('+',' ',$campaign_name);
					$callRoute 								= strtoupper($call_route);
					$SQLxdate 								= date("Y-m-d H:i:s");
					$NOW 									= date("m-d-Y");
					
					if($groupColor == null && $callRoute == null){
						$err_msg 							= error_handle("40001", "group_color & call_route");
						$apiresults 						= array(
							"code" 								=> "40001", 
							"result" 							=> $err_msg
						);
					} else {
						if(!in_array($callRoute,$defCallRoute) || $callRoute == null) {
							$err_msg 						= error_handle("10003", "call_route");
							$apiresults 					= array(
								"code" 							=> "40001", 
								"result" 						=> $err_msg
							);
						} else {						
							if ($tenant){
								$tenant_id 					= "$log_group";								
							} else {
								$tenant_id 					= "---ALL---";
                                
                                if (strtoupper($log_group) !== 'ADMIN') {
                                    $tenant_id 					    = "$log_group";
                                }
							}
							
							if ($campaign_id != 'undefined' && $campaign_id != '') {
								$local_call_time 			= "9am-9pm";
								$group_id 					= "ING".$didPattern;
								$group_name 				= $campType." Group ".$didPattern;
									
								// Insert new Inbound group
								$data_inbound_group 		= array(
									'group_id' 					=> $group_id,
									'group_name' 				=> $group_name,
									'group_color' 				=> $groupColor,
									'active' 					=> 'Y',
									'web_form_address' 			=> '',
									'voicemail_ext' 			=> '',
									'next_agent_call' 			=> 'oldest_call_finish',
									'fronter_display' 			=> 'Y',
									'ingroup_script' 			=> 'NONE',
									'get_call_launch' 			=> 'NONE',
									'web_form_address_two' 		=> '',
									'start_call_url' 			=> '',
									'dispo_call_url' 			=> '',
									'add_lead_url' 				=> '',
									'call_time_id' 				=> $local_call_time,
									'user_group' 				=> $tenant_id
								);
								$q_insertInboundGroup 		= $astDB->insert('vicidial_inbound_groups', $data_inbound_group);
								$log_id 					= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $astDB->getLastQuery());	
								// Insert new Inbound Campaign
								$manualDialPrefix 			= '';
								$manualDialPrefixVal 		= '';
								$local_call_time 			= "9am-9pm";
								
								if ($campType=='Inbound') {
									$manualDialPrefix 		= ',manual_dial_prefix';
									$manualDialPrefixVal 	= ",'$manual_dial_prefix'";
								}
								
								$auth_user 					= $goUsers;

								$data_blended 				= array(
									'campaign_id' 				=> $campaign_id, 
									'campaign_name' 			=> $campaign_desc, 
									'active' 					=> 'Y', 
									'dial_method' 				=> $dial_method, 
									'dial_status_a' 			=> 'NEW', 
									'dial_statuses' 			=> ' N NA A AA DROP B NEW -', 
									'lead_order' 				=> 'DOWN', 
									'allow_closers' 			=> 'Y', 
									'hopper_level' 				=> 100, 
									'auto_dial_level' 			=> 1.0, 
									'next_agent_call' 			=> 'oldest_call_finish', 
									'local_call_time' 			=> $local_call_time, 
									'dial_prefix' 				=> $sippy_dial_prefix, 
									'get_call_launch' 			=> 'NONE', 
									'campaign_changedate' 		=> $SQLdate, 
									'campaign_stats_refresh'	=> 'Y', 
									'list_order_mix' 			=> 'DISABLED', 
									'dial_timeout' 				=> 30, 
									'campaign_vdad_exten' 		=> 8369, 
									'campaign_recording' 		=> 'ALLFORCE', 
									'campaign_rec_filename' 	=> 'FULLDATE_CUSTPHONE_CAMPAIGN_AGENT', 
									'scheduled_callbacks' 		=> 'Y', 
									'scheduled_callbacks_alert' => 'BLINK_RED', 
									'no_hopper_leads_logins' 	=> 'Y', 
									'use_internal_dnc' 			=> 'Y', 
									'use_campaign_dnc' 			=> 'Y', 
									'available_only_ratio_tally'=> 'Y', 
									'campaign_cid' 				=> 5164536886, 
									//'manual_dial_filter' 		=> 'DNC_AND_CAMPLISTS_ALL',
                                    'manual_dial_filter' 		=> 'NONE',
									'manual_dial_search_filter'	=> 'CAMPLISTS_ALL', 
									'user_group' 				=> $tenant_id, 
									'manual_dial_list_id' 		=> 998, 
									'drop_call_seconds' 		=> 7, 
									'manual_dial_prefix' 		=> $manual_dial_prefix, 
									'am_message_exten' 			=> $answering_machine_message, 
									'agent_pause_codes_active' 	=> $pause_codes, 
									'three_way_call_cid' 		=> 'CAMPAIGN', 
									'three_way_dial_prefix' 	=> $dial_prefix_3_way_call, 
									'customer_3way_hangup_logging' 	=> $three_way_hangup_logging, 
									'customer_3way_hangup_seconds' 	=> $three_way_hangup_seconds, 
									'customer_3way_hangup_action' 	=> $three_way_hangup_action, 
									'campaign_allow_inbound' 	=> 'Y', 
									'disable_alter_custdata' 	=> 'N', 
									'disable_alter_custphone' 	=> 'Y', 
									'campaign_script' 			=> $script
								);
								
								$q_insertBlended 			= $astDB->insert( 'vicidial_campaigns', $data_blended );
								$log_id 					= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
								//$insertQuery = $astDB->getLastQuery();

								$q_insertVCS 				= $astDB->insert( 'vicidial_campaign_stats', array('campaign_id' => $campaign_id) );
								$log_id 					= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
                                
                                $astDB->where('user_group', $log_group);
                                $allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
                                $allowed_campaigns = $allowed_camps['allowed_campaigns'];
                                
                                if (strlen($allowed_campaigns) < 1) { 
                                    $allowed_campaigns = " -"; 
                                }
                                
                                if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
                                    $update_data = array(
                                        'allowed_campaigns' 					=>  " $campaign_id " . trim($allowed_campaigns)
                                    );
                                    
                                    $astDB->where('user_group', $log_group);
                                    $q_updateAllowedCampaign = $astDB->update('vicidial_user_groups', $update_data);
                                }
								
								if ( $q_insertBlended ) {
									if ( $callRoute != null ) {
										// Call Route
										$didDesc 			= $campaign_id." ".$campaign_type." DID";
										$didPattern 		= $call_route_text;
										
										$astDB->where( 'did_pattern', $did_pattern );
										$resultDID 			= $astDB->getOne( 'vicidial_inbound_dids', 'did_pattern' );
										$serverIP 			= $_SERVER['REMOTE_ADDR'];
										
										switch ( $callRoute ) {
											case "INGROUP":
											
											if ( $resultDID ) {
												$update_ing 					= array(
													'did_description' 				=> $didDesc,
													'did_active' 					=> 'Y',
													'did_route' 					=> 'IN_GROUP',
													'user_route_settings_ingroup' 	=> $call_route_text,
													'campaign_id' 					=> $campaign_id,
													'record_call' 					=> 'N',
													'filter_list_id' 				=> $list_id,
													'filter_campaign_id' 			=> $campaign_id,
													'group_id' 						=> $call_route_text,
													'server_ip' 					=> $serverIP,
													'user_group' 					=> $tenant_id
												);
												
												$astDB->where('did_pattern', $did_pattern);
												$astDB->update('vicidial_inbound_dids', $update_ing);	
												$log_id 							= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());
												
											} else {	
												$data_ing 						= array(
													'did_pattern' 					=> $did_pattern,
													'did_description' 				=> $didDesc,
													'did_active' 					=> 'Y',
													'did_route' 					=> 'IN_GROUP',
													'user_route_settings_ingroup' 	=> 'AGENTDIRECT',
													'campaign_id' 					=> $campaign_id,
													'record_call' 					=> 'N',
													'filter_list_id' 				=> $list_id,
													'filter_campaign_id' 			=> $campaign_id,
													'group_id' 						=> 'AGENTDIRECT',
													'server_ip' 					=> $serverIP,
													'user_group' 					=> $tenant_id
												);
												
												$astDB->insert('vicidial_inbound_dids', $data_ing);	
												$log_id 						= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());
											}

											$update_VC 							= array(
												'xfer_groups' 						=> $call_route_text.' -', 
												'closer_campaigns' 					=> $call_route_text.' -' 
											);
											
											$astDB->where('campaign_id', $campaign_id);
											$astDB->update('vicidial_campaigns', $update_VC);
											$log_id 							= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());
												
											break;
											
											case "IVR":
											
											$menuID 							= "$call_route_text";

											$data_VCM 							= array(
												'menu_id' 							=> $menuID,
												'menu_name' 						=> $menuID.' Inbound Call Menu',
												'user_group' 						=> $tenant_id
											);
											
											$astDB->insert('vicidial_call_menu', $data_VCM);
											$log_id 							= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());

											if ($resultDID) {
												$update_VID 					= array(
													'did_description' 				=> $didDesc,
													'did_active' 					=> 'Y',
													'did_route' 					=> 'CALLMENU',
													'campaign_id' 					=> $campaign_id,
													'record_call' 					=> 'N',
													'filter_list_id' 				=> $list_id,
													'filter_campaign_id' 			=> $campaign_id,
													'server_ip' 					=> $serverIP,
													'menu_id' 						=> $call_route_text,
													'user_group' 					=> $tenant_id
												);
												
												$astDB->where('did_pattern', $did_pattern);
												$astDB->update('vicidial_inbound_dids', $update_VID);
												$log_id 						= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());
												
											} else {
												$data_vid 						= array(
													'did_pattern' 					=> $did_pattern,
													'did_description' 				=> $didDesc,
													'did_active' 					=> 'Y',
													'did_route' 					=> 'CALLMENU',
													'campaign_id' 					=> $campaign_id,
													'record_call' 					=> 'N',
													'filter_list_id' 				=> $list_id,
													'filter_campaign_id' 			=> $campaign_id,
													'server_ip' 					=> $serverIP,
													'menu_id' 						=> 'defaultlog',
													'user_group' 					=> $tenant_id
												);
												
												$astDB->insert('vicidial_inbound_dids', $data_vid);	
												$log_id 						= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());
											}
											
											break;
											
											case "AGENT":
										
											$data_agent 						= array(
												'did_pattern' 						=> $did_pattern,
												'did_description' 					=> $didDesc,
												'did_active' 						=> 'Y',
												'did_route' 						=> 'AGENT',
												'user_route_settings_ingroup' 		=> $group_id,
												'campaign_id' 						=> $campaign_id,
												'record_call' 						=> 'N',
												'filter_list_id' 					=> $list_id,
												'filter_campaign_id' 				=> $campaign_id,
												'user' 								=> $call_route_text,
												'group_id' 							=> $group_id,
												'server_ip' 						=> $serverIP,
												'user_group' 						=> $tenant_id
											);
											
											$astDB->insert('vicidial_inbound_dids', $data_agent);	
											$log_id 							= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());
											
											break;
											
											case "VOICEMAIL":
											
											if ($emailORagent=='undefined') {
												$emailORagent = '';
											}
											$data_vv 							= array(
												'voicemail_id' 						=> $campaign_id,
												'pass' 								=> $campaign_id,
												'email' 							=> $emailORagent,
												'fullname' 							=> $campaign_id.' VOICEMAIL',
												'active' 							=> 'Y',
												'user_group' 						=> $tenant_id
											);
											
											$astDB->insert('vicidial_voicemail', $data_vv);
											$log_id 							= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());

											$data_vociemail 					= array(
												'did_pattern'						=> $did_pattern,
												'did_description'					=> $didDesc,
												'did_active'						=> 'Y',
												'did_route'							=> 'VOICEMAIL',
												'user_route_settings_ingroup'		=> $group_id,
												'campaign_id'						=> $campaign_id,
												'record_call'						=> 'N',
												'filter_list_id'					=> $list_id,
												'filter_campaign_id'				=> $campaign_id,
												'voicemail_ext'						=> $call_route_text,
												'user_group'						=> $tenant_id,
												'server_ip' 						=> $serverIP
											);
											
											$astDB->insert('vicidial_inbound_dids', $data_vociemail);
											$log_id 							= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());
													
											break;
										}

										$astDB->where('campaign_id', $campaign_id);
										$astDB->update('vicidial_campaigns', array('campaign_allow_inbound' => 'Y'));
										$log_id 									= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());
										
										$astDB->where('user', $userID);
										$astDB->update('vicidial_campaigns', array('modify_inbound_dids' => 1));
										$log_id 									= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $astDB->getLastQuery());
									}

									$SQLdate 						= date("Y-m-d H:i:s");
									$log_id 						= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Inbound Campaign: $campaign_id", $log_group, $insertQuery);

									$datago_campaign 				= array(
										'campaign_id' 					=> $campaign_id, 
										'campaign_type' 				=> $campaign_type
										// 'location_id' 	=> (!empty($location))? $location:''
									);
									
									$goDB->insert('go_campaigns', $datago_campaign);
									$log_id 						= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Blended Campaign: $campaign_id", $log_group, $goDB->getLastQuery());								
									$apiresults 					= array(
										"result" 						=> "success"
									);
								} else {
									$err_msg 						= error_handle("10010");
									$apiresults				 		= array(
										"code" 							=> "10010", 
										"result" 						=> $err_msg
									);
								}
							} else {
								$err_msg 							= error_handle("41004", "campaign_id");
								$apiresults 						= array(
									"code" 								=> "41004", 
									"result" 							=> $err_msg
								);
							}
						}
					}
				}
				// End of BLENDED

				// Survey Campaign here
				if ( $campaign_type == "SURVEY" ) {
					//$userID = $goUsers;
					$campType 									= $campaign_type;
					//$campaign_id = $astDB->escape( $campaign_id);
					$surveyType 								= strtoupper( $survey_type );
					$numChannels 								= $number_channels;
					$campaign_desc 								= str_replace( '+',' ',$campaign_name );
					$SQLdate 									= date( "Y-m-d H:i:s" );
					$NOW 										= date( "m-d-Y" );
					$defSurveyType 								= array( 'BROADCAST','PRESS1' );
					$defNumCha 									= array( 1,5,10,15,20,30 );
					
					if ( !in_array($surveyType,$defSurveyType) && $surveyType == null ) {
						$err_msg 								= error_handle( "10003", "survey_type" );
						$apiresults 							= array(
							"code" 									=> "10003", 
							"result" 								=> $err_msg
						);
					} else {
						if( !in_array($numChannels,$defNumCha) && $numChannels == null ) {
							$err_msg 							= error_handle( "10003", "no_channels" );
							$apiresults							= array(
								"code" 								=> "10003", 
								"result" 							=> $err_msg
							);
						} else {						
							if ( $surveyType == "BROADCAST" ) {
								$routingExten 					= 8373;
							} 
							
							if ( $surveyType == "PRESS1" ) {
								$routingExten 					= 8366;
							}
							
							// Create New Survey Campaign
							if ( $campaign_id!='undefined' && $campaign_id!='' || $campaign_id != null ){
								//if($VARSERVTYPE == "cloud"){
								//$astDB->where("LOWER(server_description)", "meetme", "RLIKE");
								$fresults						= $astDB->getOne( "servers", "server_ip" );
								
								if ( $astDB->count >0 ) {
									$main_server_ip 			= $fresults['server_ip'];
								}
								
								/*$queryServer = "SELECT server_ip FROM servers WHERE LOWER(server_description) RLIKE 'meetme';";
								$rsltvServer = mysqli_query($link, $queryServer);
								while($fresults = mysqli_fetch_array($rsltvServer, MYSQLI_ASSOC)){
									$main_server_ip = $fresults['server_ip'];
								}*/

								if ( checkIfTenant($log_group, $goDB) ) {
									$tenant_id 					= "$log_group";
									$astDB->where( "user_group", $log_group );													
								} else {
									$tenant_id 					= "---ALL---";
                                    
                                    if (strtoupper($log_group) !== 'ADMIN') {
                                        $tenant_id 				= "$log_group";
                                        $astDB->where( "user_group", $log_group );	
                                    }
								}
								
								$astDB->where( 'campaign_id', $campaign_id );
								$astDB->getOne( 'vicidial_campaigns', 'campaign_id' ); 

								if ($astDB->count < 1) {
									$local_call_time 			= "9am-9pm";
									$auth_user 					= $goUsers;								
									$wavfile_name 				= $_FILES["uploaded_wav"]['name'];
									$wavfile_orig 				= $_FILES['uploaded_wav']['name'];
									$wavfile_dir 				= $_FILES['uploaded_wav']['tmp_name'];
									$wavfile_size 				= $_FILES['uploaded_wav']['size'];
									$WeBServeRRooT				= '/var/lib/asterisk';
									$sounds_web_directory 		= 'sounds';								
									$wavfile_name 				= substr( $wavfile_name, 0, -4 );
									
									if ( empty($wavfile_name) ) {
										$wavfile_name 			= "US_pol_survey_hello";
									}								

									$data_survey 					= array(
										'campaign_id' 					=> $campaign_id,
										'campaign_name' 				=> $campaign_desc,
										'campaign_description' 			=> $campaign_desc,
										'active' 						=> 'N',
										'dial_method' 					=> 'RATIO',
										'dial_status_a' 				=> 'NEW',
										'dial_statuses' 				=> ' N NA A AA DROP B NEW -',
										'lead_order' 					=> 'DOWN',
										'park_ext' 						=> '',
										'park_file_name' 				=> '',
										'web_form_address' 				=> '',
										'allow_closers' 				=> 'Y',
										'hopper_level' 					=> 100,
										'auto_dial_level' 				=> 1,
										'available_only_ratio_tally' 	=> 'Y',
										'next_agent_call'			 	=> 'random',
										'local_call_time' 				=> $local_call_time,
										'dial_prefix' 					=> $sippy_dial_prefix,
										'voicemail_ext' 				=> '',
										'campaign_script' 				=> $script,
										'get_call_launch' 				=> '',
										'campaign_changedate' 			=> $SQLdate,
										'campaign_stats_refresh' 		=> 'Y',
										'list_order_mix' 				=> 'DISABLED',
										'web_form_address_two' 			=> '',
										'start_call_url' 				=> '',
										'dispo_call_url' 				=> '',
										'dial_timeout' 					=> 30,
										'campaign_vdad_exten' 			=> 8366,
										'campaign_recording' 			=> $campaign_recording,
										'campaign_rec_filename' 		=> 'FULLDATE_CUSTPHONE_CAMPAIGN_AGENT',
										'scheduled_callbacks' 			=> 'Y',
										'scheduled_callbacks_alert' 	=> 'BLINK_RED',
										'no_hopper_leads_logins' 		=> 'Y',
										'per_call_notes' 				=> 'ENABLED',
										'agent_lead_search' 			=> 'ENABLED',
										'use_internal_dnc' 				=> 'Y',
										'use_campaign_dnc' 				=> 'Y',
										'campaign_cid' 					=> 5164569886,
										'user_group' 					=> $tenant_id,
										'manual_dial_list_id' 			=> $tenant_id.'998',
										'drop_call_seconds' 			=> 7,
										'survey_opt_in_audio_file' 		=> '',
										'survey_first_audio_file' 		=> $wavfile_name, 
										'survey_method' 				=> 'EXTENSION', 
										'disable_alter_custdata' 		=> 'N', 
										'disable_alter_custphone' 		=> 'Y'
									);
									
									$q_insertSurvey 				= $astDB->insert( 'vicidial_campaigns', $data_survey );
									$log_id 						= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Survey Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );

									$astDB->insert( "vicidial_campaign_stats", array( "campaign_id" => $campaign_id ) );
									$log_id 						= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Survey Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
                                    
                                    $astDB->where('user_group', $log_group);
                                    $allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
                                    $allowed_campaigns = $allowed_camps['allowed_campaigns'];
                                    
                                    if (strlen($allowed_campaigns) < 1) { 
                                        $allowed_campaigns = " -"; 
                                    }
                                    
                                    if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
                                        $update_data = array(
                                            'allowed_campaigns' 					=>  " $campaign_id " . trim($allowed_campaigns)
                                        );
                                        
                                        $astDB->where('user_group', $log_group);
                                        $q_updateAllowedCampaign = $astDB->update('vicidial_user_groups', $update_data);
                                    }

									if ( $q_insertSurvey ) {
										if ( preg_match("/\.(wav|mp3)$/i",$wavfile_orig) ) {
											$wavfile_dir 			= preg_replace( "/ /",'\ ', $wavfile_dir );
											$wavfile_dir 			= preg_replace( "/@/",'\@', $wavfile_dir );
											$wavfile_name 			= preg_replace( "/ /",'', "go_".$wavfile_name );
											$wavfile_name 			= preg_replace( "/@/",'', $wavfile_name );
											$wavfile_size 			= formatSizeUnits( $wavfile_size );

											$goDB->where( 'goFilename', $wavfile_name );
											$goDB->where( 'goDirectory', $path_sounds );
											$goDB->get( 'sounds' ); 
											
											if ( $goDB->count > 0 ) {
												copy( $wavfile_dir, "$path_sounds/$wavfile_name" );
												chmod( "$path_sounds/$wavfile_name", 0766 );

												$data_sounds 		= array(
													'goFilename' 		=> $wavfile_name, 
													'goDirectory' 		=> $path_sounds, 
													'goFileDate' 		=> date( 'Y-m-d H:i:s' ), 
													'goFilesize' 		=> $wavfile_size, 
													'uploaded_by' 		=> $session_user
												);
												
												$q_insertSounds 	= $goDB->insert( 'sounds', $data_sounds );
												$log_id 			= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Survey Campaign: $campaign_id", $log_group, $goDB->getLastQuery() );
												
												if ( !$q_insertSounds ) {
													$err_msg 		= error_handle( "10008" );
													$apiresults 	= array(
														"code" 			=> "40001", 
														"result" 		=> $err_msg
													);
												}
											}
										}

										do {
											$agvar 					= mt_rand();
											$astDB->where( 'user', $agvar );
											$user_exist 			= $astDB->get( 'vicidial_users', null, 'user' ); 
										}
										
										while ($user_exist);

										$pass 						= substr( str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10 );
										$survey_method 				= "EXTENSION";
										
										if ( $survey_method != "AGENT_XFER" && $active == 'Y' ) {
											$remote_agent_status 	= 'Y';
										} else {
											$remote_agent_status 	= 'N';
										}
										
										$agent_user 				= $agvar;
										$agent_name 				= "Survey Agent - ".$campaign_id;
										$agent_phone 				= $agvar;

										//$queryVRA = "INSERT INTO vicidial_remote_agents (user_start,number_of_lines,server_ip,conf_exten,status,campaign_id,closer_campaigns) values('$agent_user','$numChannels','$main_server_ip','8300','$remote_agent_status','$campaign_id','')";
										//$rsltvVRA = mysqli_query($link, $queryVRA);

										$data_vra					 = array(
											'user_start' 				=> $agent_user,
											'number_of_lines' 			=> $numChannels,
											'server_ip' 				=> $main_server_ip,
											'conf_exten' 				=> 8300,
											'status' 					=> $remote_agent_status,
											'campaign_id' 				=> $campaign_id,
											'closer_campaigns' 			=> ''
										);
										
										$astDB->insert( 'vicidial_remote_agents', $data_vra );
										$log_id 					= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Survey Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );

										if ( $countAll < 1 ){
											$tenant_id 				= ( $tenant_id=='---ALL---' ) ? "AGENTS" : "$tenant_id";

											$data_vu 				= array(
												'user' 					=> $agent_user,
												'pass' 					=> $pass,
												'full_name' 			=> $agent_name,
												'user_level' 			=> 4,
												'user_group' 			=> $tenant_id
											);
											
											$astDB->insert( 'vicidial_users', $data_vu );
											$log_id 				= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Survey Campaign: $campaign_id", $log_group, $astDB->getLastQuery() );
										}

										$SQLdate 					= date( "Y-m-d H:i:s" );
										//$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Survey Campaign: $campaign_id", $log_group, $insertQuery);
										
										$datago_campaign 			= array(
											'campaign_id' 				=> $campaign_id, 
											'campaign_type' 			=> $campaign_type
											// 'location_id' 	=> (!empty($location))? $location:''
										);
										
										$goDB->insert( 'go_campaigns', $datago_campaign );
										$log_id 					= log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New Survey Campaign: $campaign_id", $log_group, $goDB->getLastQuery() );
										$apiresults 				= array(
											"result" 					=> "success"
										);
									} else {
										$err_msg 					= error_handle( "10010" );
										$apiresults 				= array(
											"code" 						=> "10010", 
											"result" 					=> $err_msg
										);
									}
								} else {
									$err_msg 						= error_handle( "10010" );
									$apiresults 					= array(
										"code" 							=> "10010", 
										"result" 						=> $err_msg
									);
								}
							} else {
								$err_msg 							= error_handle( "10010" );
								$apiresults 						= array(
									"code" 								=> "10010", 
									"result" 							=> $err_msg
								);
							}
						}
					}
				}
				// End of SURVEY
				
				if ( $campaign_type == "COPY" ) {
					$astDB->where('campaign_id', $copy_from_campaign);
					$resultGetCopy 									= $astDB->getOne('vicidial_campaigns');
					
					$goDB->where('campaign_id', $copy_from_campaign);
					$rsltGOCopy 									= $goDB->get('go_campaigns', null, '*');

					if ($resultGetCopy) {
						$data_copy 									= array(
							'campaign_id' 								=> $campaign_id, 
							'campaign_name' 							=> $campaign_name, 
							'dial_method' 								=> $resultGetCopy['dial_method'], 
							'auto_dial_level' 							=> $resultGetCopy['auto_dial_level'], 
							'active' 									=> $resultGetCopy['active'],
							'dial_status_a' 							=> $resultGetCopy['dial_status_a'],
							'dial_status_b' 							=> $resultGetCopy['dial_status_b'],
							'dial_status_c' 							=> $resultGetCopy['dial_status_c'],
							'dial_status_d' 							=> $resultGetCopy['dial_status_d'],
							'dial_status_e' 							=> $resultGetCopy['dial_status_e'],
							'lead_order' 								=> $resultGetCopy['lead_order'], 
							'park_ext' 									=> $resultGetCopy['park_ext'],
							'park_file_name' 							=> $resultGetCopy['park_file_name'],
							'web_form_address' 							=> $resultGetCopy['web_form_address'],
							'allow_closers' 							=> $resultGetCopy['allow_closers'],
							'hopper_level' 								=> $resultGetCopy['hopper_level'],
							'next_agent_call' 							=> $resultGetCopy['next_agent_call'],
							'local_call_time' 							=> $resultGetCopy['local_call_time'], 
							'voicemail_ext' 							=> $resultGetCopy['voicemail_ext'],
							'dial_timeout' 								=> $resultGetCopy['dial_timeout'],
							'dial_prefix' 								=> $resultGetCopy['dial_prefix'],
							'campaign_cid' 								=> $resultGetCopy['campaign_cid'],
							'campaign_vdad_exten' 						=> $resultGetCopy['campaign_vdad_exten'],
							'campaign_rec_exten' 						=> $resultGetCopy['campaign_rec_exten'],
							'campaign_recording' 						=> $resultGetCopy['campaign_recording'], 
							'campaign_rec_filename' 					=> $resultGetCopy['campaign_rec_filename'],
							'campaign_script' 							=> $resultGetCopy['campaign_script'],
							'get_call_launch' 							=> $resultGetCopy['get_call_launch'],
							'am_message_exten' 							=> $resultGetCopy['am_message_exten'],
							'amd_send_to_vmx' 							=> $resultGetCopy['amd_send_to_vmx'],
							'xferconf_a_dtmf' 							=> $resultGetCopy['xferconf_a_dtmf'],
							'xferconf_a_number' 						=> $resultGetCopy['xferconf_a_number'], 
							'xferconf_b_dtmf' 							=> $resultGetCopy['xferconf_b_dtmf'],
							'xferconf_b_number' 						=> $resultGetCopy['xferconf_b_number'],
							'alt_number_dialing' 						=> $resultGetCopy['alt_number_dialing'],
							'scheduled_callbacks' 						=> $resultGetCopy['scheduled_callbacks'],
							'lead_filter_id' 							=> $resultGetCopy['lead_filter_id'],
							'drop_call_seconds' 						=> $resultGetCopy['drop_call_seconds'],
							'drop_action' 								=> $resultGetCopy['drop_action'], 
							'safe_harbor_exten' 						=> $resultGetCopy['safe_harbor_exten'],
							'display_dialable_count' 					=> $resultGetCopy['display_dialable_count'],
							'wrapup_seconds' 							=> $resultGetCopy['wrapup_seconds'],
							'wrapup_message' 							=> $resultGetCopy['wrapup_message'],
							'closer_campaigns' 							=> $resultGetCopy['closer_campaigns'],
							'use_internal_dnc' 							=> $resultGetCopy['use_internal_dnc'],
							'allcalls_delay' 							=> $resultGetCopy['allcalls_delay'], 
							'omit_phone_code' 							=> $resultGetCopy['omit_phone_code'],
							'available_only_ratio_tally' 				=> $resultGetCopy['available_only_ratio_tally'],
							'adaptive_dropped_percentage' 				=> $resultGetCopy['adaptive_dropped_percentage'],
							'adaptive_maximum_level' 					=> $resultGetCopy['adaptive_maximum_level'],
							'adaptive_latest_server_time' 				=> $resultGetCopy['adaptive_latest_server_time'],
							'adaptive_intensity' 						=> $resultGetCopy['adaptive_intensity'],
							'adaptive_dl_diff_target' 					=> $resultGetCopy['adaptive_dl_diff_target'], 
							'concurrent_transfers' 						=> $resultGetCopy['concurrent_transfers'],
							'auto_alt_dial' 							=> $resultGetCopy['auto_alt_dial'],
							'auto_alt_dial_statuses' 					=> $resultGetCopy['auto_alt_dial_statuses'],
							'agent_pause_codes_active' 					=> $resultGetCopy['agent_pause_codes_active'],
							'campaign_description' 						=> $resultGetCopy['campaign_description'],
							'campaign_changedate' 						=> $resultGetCopy['campaign_changedate'],
							'campaign_stats_refresh' 					=> $resultGetCopy['campaign_stats_refresh'], 
							'campaign_logindate' 						=> $resultGetCopy['campaign_logindate'],
							'dial_statuses' 							=> $resultGetCopy['dial_statuses'],
							'disable_alter_custdata' 					=> $resultGetCopy['disable_alter_custdata'],
							'no_hopper_leads_logins' 					=> $resultGetCopy['no_hopper_leads_logins'],
							'list_order_mix' 							=> $resultGetCopy['list_order_mix'],
							'campaign_allow_inbound' 					=> $resultGetCopy['campaign_allow_inbound'],
							'manual_dial_list_id' 						=> $resultGetCopy['manual_dial_list_id'], 
							'default_xfer_group' 						=> $resultGetCopy['default_xfer_group'],
							'xfer_groups' 								=> $resultGetCopy['xfer_groups'],
							'queue_priority' 							=> $resultGetCopy['queue_priority'],
							'drop_inbound_group' 						=> $resultGetCopy['drop_inbound_group'],
							'qc_enabled' 								=> $resultGetCopy['qc_enabled'],
							'qc_statuses' 								=> $resultGetCopy['qc_statuses'],
							'qc_lists' 									=> $resultGetCopy['qc_lists'], 
							'qc_shift_id' 								=> $resultGetCopy['qc_shift_id'],
							'qc_get_record_launch' 						=> $resultGetCopy['qc_get_record_launch'],
							'qc_show_recording' 						=> $resultGetCopy['qc_show_recording'],
							'qc_web_form_address' 						=> $resultGetCopy['qc_web_form_address'],
							'qc_script' 								=> $resultGetCopy['qc_script'],
							'survey_first_audio_file' 					=> $resultGetCopy['survey_first_audio_file'],
							'survey_dtmf_digits' 						=> $resultGetCopy['survey_dtmf_digits'], 
							'survey_ni_digit' 							=> $resultGetCopy['survey_ni_digit'],
							'survey_opt_in_audio_file' 					=> $resultGetCopy['survey_opt_in_audio_file'],
							'survey_ni_audio_file' 						=> $resultGetCopy['survey_ni_audio_file'],
							'survey_method' 							=> $resultGetCopy['survey_method'],
							'survey_no_response_action' 				=> $resultGetCopy['survey_no_response_action'],
							'survey_ni_status' 							=> $resultGetCopy['survey_ni_status'],
							'survey_response_digit_map' 				=> $resultGetCopy['survey_response_digit_map'], 
							'survey_xfer_exten' 						=> $resultGetCopy['survey_xfer_exten'],
							'survey_camp_record_dir' 					=> $resultGetCopy['survey_camp_record_dir'],
							'disable_alter_custphone' 					=> $resultGetCopy['disable_alter_custphone'],
							'display_queue_count' 						=> $resultGetCopy['display_queue_count'],
							'manual_dial_filter' 						=> $resultGetCopy['manual_dial_filter'],
							'manual_dial_search_filter' 				=> $resultGetCopy['manual_dial_search_filter'],
							'agent_clipboard_copy' 						=> $resultGetCopy['agent_clipboard_copy'],
							'agent_extended_alt_dial' 					=> $resultGetCopy['agent_extended_alt_dial'], 
							'use_campaign_dnc' 							=> $resultGetCopy['use_campaign_dnc'],
							'three_way_call_cid' 						=> $resultGetCopy['three_way_call_cid'],
							'three_way_dial_prefix' 					=> $resultGetCopy['three_way_dial_prefix'],
							'web_form_target' 							=> $resultGetCopy['web_form_target'],
							'vtiger_search_category' 					=> $resultGetCopy['vtiger_search_category'],
							'vtiger_create_call_record' 				=> $resultGetCopy['vtiger_create_call_record'],
							'vtiger_create_lead_record' 				=> $resultGetCopy['vtiger_create_lead_record'], 
							'vtiger_screen_login' 						=> $resultGetCopy['vtiger_screen_login'],
							'cpd_amd_action' 							=> $resultGetCopy['cpd_amd_action'],
							'agent_allow_group_alias' 					=> $resultGetCopy['agent_allow_group_alias'],
							'default_group_alias' 						=> $resultGetCopy['default_group_alias'],
							'vtiger_search_dead' 						=> $resultGetCopy['vtiger_search_dead'],
							'vtiger_status_call' 						=> $resultGetCopy['vtiger_status_call'],
							'survey_third_digit' 						=> $resultGetCopy['survey_third_digit'], 
							'survey_third_audio_file' 					=> $resultGetCopy['survey_third_audio_file'],
							'survey_third_status' 						=> $resultGetCopy['survey_third_status'],
							'survey_third_exten' 						=> $resultGetCopy['survey_third_exten'],
							'survey_fourth_digit' 						=> $resultGetCopy['survey_fourth_digit'],
							'survey_fourth_audio_file' 					=> $resultGetCopy['survey_fourth_audio_file'],
							'survey_fourth_status' 						=> $resultGetCopy['survey_fourth_status'],
							'survey_fourth_exten' 						=> $resultGetCopy['survey_fourth_exten'], 
							'drop_lockout_time' 						=> $resultGetCopy['drop_lockout_time'],
							'quick_transfer_button' 					=> $resultGetCopy['quick_transfer_button'],
							'prepopulate_transfer_preset' 				=> $resultGetCopy['prepopulate_transfer_preset'],
							'drop_rate_group' 							=> $resultGetCopy['drop_rate_group'],
							'view_calls_in_queue' 						=> $resultGetCopy['view_calls_in_queue'],
							'view_calls_in_queue_launch' 				=> $resultGetCopy['view_calls_in_queue_launch'],
							'grab_calls_in_queue' 						=> $resultGetCopy['grab_calls_in_queue'], 
							'call_requeue_button' 						=> $resultGetCopy['call_requeue_button'],
							'pause_after_each_call' 					=> $resultGetCopy['pause_after_each_call'],
							'no_hopper_dialing' 						=> $resultGetCopy['no_hopper_dialing'],
							'agent_dial_owner_only' 					=> $resultGetCopy['agent_dial_owner_only'],
							'agent_display_dialable_leads' 				=> $resultGetCopy['agent_display_dialable_leads'],
							'web_form_address_two' 						=> $resultGetCopy['web_form_address_two'],
							'waitforsilence_options' 					=> $resultGetCopy['waitforsilence_options'], 
							'agent_select_territories' 					=> $resultGetCopy['agent_select_territories'],
							'campaign_calldate' 						=> $resultGetCopy['campaign_calldate'],
							'crm_popup_login' 							=> $resultGetCopy['crm_popup_login'],
							'crm_login_address' 						=> $resultGetCopy['crm_login_address'],
							'timer_action' 								=> $resultGetCopy['timer_action'],
							'timer_action_message' 						=> $resultGetCopy['timer_action_message'],
							'timer_action_seconds' 						=> $resultGetCopy['timer_action_seconds'], 
							'start_call_url' 							=> $resultGetCopy['start_call_url'],
							'dispo_call_url' 							=> $resultGetCopy['dispo_call_url'],
							'xferconf_c_number' 						=> $resultGetCopy['xferconf_c_number'],
							'xferconf_d_number' 						=> $resultGetCopy['xferconf_d_number'],
							'xferconf_e_number' 						=> $resultGetCopy['xferconf_e_number'],
							'use_custom_cid' 							=> $resultGetCopy['use_custom_cid'],
							'scheduled_callbacks_alert' 				=> $resultGetCopy['scheduled_callbacks_alert'], 
							'queuemetrics_callstatus_override' 			=> $resultGetCopy['queuemetrics_callstatus_override'],
							'extension_appended_cidname' 				=> $resultGetCopy['extension_appended_cidname'],
							'scheduled_callbacks_count' 				=> $resultGetCopy['scheduled_callbacks_count'],
							'manual_dial_override' 						=> $resultGetCopy['manual_dial_override'],
							'blind_monitor_warning' 					=> $resultGetCopy['blind_monitor_warning'],
							'blind_monitor_message' 					=> $resultGetCopy['blind_monitor_message'],
							'blind_monitor_filename' 					=> $resultGetCopy['blind_monitor_filename'], 
							'inbound_queue_no_dial' 					=> $resultGetCopy['inbound_queue_no_dial'],
							'timer_action_destination' 					=> $resultGetCopy['timer_action_destination'],
							'enable_xfer_presets' 						=> $resultGetCopy['enable_xfer_presets'],
							'hide_xfer_number_to_dial' 					=> $resultGetCopy['hide_xfer_number_to_dial'],
							'manual_dial_prefix' 						=> $resultGetCopy['manual_dial_prefix'],
							'customer_3way_hangup_logging' 				=> $resultGetCopy['customer_3way_hangup_logging'],
							'customer_3way_hangup_seconds' 				=> $resultGetCopy['customer_3way_hangup_seconds'], 
							'customer_3way_hangup_action' 				=> $resultGetCopy['customer_3way_hangup_action'],
							'ivr_park_call' 							=> $resultGetCopy['ivr_park_call'],
							'ivr_park_call_agi' 						=> $resultGetCopy['ivr_park_call_agi'],
							'manual_preview_dial' 						=> $resultGetCopy['manual_preview_dial'],
							'realtime_agent_time_stats' 				=> $resultGetCopy['realtime_agent_time_stats'],
							'use_auto_hopper' 							=> $resultGetCopy['use_auto_hopper'],
							'auto_hopper_multi' 						=> $resultGetCopy['auto_hopper_multi'], 
							'auto_hopper_level' 						=> $resultGetCopy['auto_hopper_level'],
							'auto_trim_hopper' 							=> $resultGetCopy['auto_trim_hopper'],
							'api_manual_dial' 							=> $resultGetCopy['api_manual_dial'],
							'manual_dial_call_time_check' 				=> $resultGetCopy['manual_dial_call_time_check'],
							'display_leads_count' 						=> $resultGetCopy['display_leads_count'],
							'lead_order_randomize' 						=> $resultGetCopy['lead_order_randomize'],
							'lead_order_secondary' 						=> $resultGetCopy['lead_order_secondary'], 
							'per_call_notes' 							=> $resultGetCopy['per_call_notes'],
							'my_callback_option' 						=> $resultGetCopy['my_callback_option'],
							'agent_lead_search' 						=> $resultGetCopy['agent_lead_search'],
							'agent_lead_search_method' 					=> $resultGetCopy['agent_lead_search_method'],
							'queuemetrics_phone_environment' 			=> $resultGetCopy['queuemetrics_phone_environment'],
							'auto_pause_precall' 						=> $resultGetCopy['auto_pause_precall'],
							'auto_pause_precall_code' 					=> $resultGetCopy['auto_pause_precall_code'], 
							'auto_resume_precall' 						=> $resultGetCopy['auto_resume_precall'],
							'manual_dial_cid' 							=> $resultGetCopy['manual_dial_cid'],
							'post_phone_time_diff_alert' 				=> $resultGetCopy['post_phone_time_diff_alert'],
							'custom_3way_button_transfer' 				=> $resultGetCopy['custom_3way_button_transfer'],
							'available_only_tally_threshold' 			=> $resultGetCopy['available_only_tally_threshold'],
							'available_only_tally_threshold_agents' 	=> $resultGetCopy['available_only_tally_threshold_agents'],
							'dial_level_threshold' 						=> $resultGetCopy['dial_level_threshold'], 
							'dial_level_threshold_agents' 				=> $resultGetCopy['dial_level_threshold_agents'],
							'safe_harbor_audio' 						=> $resultGetCopy['safe_harbor_audio'],
							'safe_harbor_menu_id' 						=> $resultGetCopy['safe_harbor_menu_id'],
							'survey_menu_id' 							=> $resultGetCopy['survey_menu_id'],
							'callback_days_limit' 						=> $resultGetCopy['callback_days_limit'],
							'dl_diff_target_method' 					=> $resultGetCopy['dl_diff_target_method'],
							'disable_dispo_screen' 						=> $resultGetCopy['disable_dispo_screen'], 
							'disable_dispo_status' 						=> $resultGetCopy['disable_dispo_status'],
							'screen_labels' 							=> $resultGetCopy['screen_labels'],
							'status_display_fields' 					=> $resultGetCopy['status_display_fields'],
							'na_call_url' 								=> $resultGetCopy['na_call_url'],
							'survey_recording' 							=> $resultGetCopy['survey_recording'],
							'pllb_grouping' 							=> $resultGetCopy['pllb_grouping'],
							'pllb_grouping_limit' 						=> $resultGetCopy['pllb_grouping_limit'], 
							// 'call_coun_tlimit' 							=> $resultGetCopy['call_count_limit'],
							'call_count_target' 						=> $resultGetCopy['call_count_target'],
							'callback_hours_block' 						=> $resultGetCopy['callback_hours_block'],
							'callback_list_calltime' 					=> $resultGetCopy['callback_list_calltime'],
							'user_group' 								=> $resultGetCopy['user_group'],
							'hopper_vlc_dup_check' 						=> $resultGetCopy['hopper_vlc_dup_check'],
							'in_group_dial' 							=> $resultGetCopy['in_group_dial'], 
							'in_group_dial_select' 						=> $resultGetCopy['in_group_dial_select'],
							'safe_harbor_audio_field' 					=> $resultGetCopy['safe_harbor_audio_field'],
							'pause_after_next_call' 					=> $resultGetCopy['pause_after_next_call'],
							'owner_populate' 							=> $resultGetCopy['owner_populate'],
							'use_other_campaign_dnc' 					=> $resultGetCopy['use_other_campaign_dnc'],
							'allow_emails' 								=> $resultGetCopy['allow_emails'],
							'amd_inbound_group' 						=> $resultGetCopy['amd_inbound_group'], 
							'amd_callmenu' 								=> $resultGetCopy['amd_callmenu'],
							'survey_wait_sec' 							=> $resultGetCopy['survey_wait_sec'],
							'manual_dial_lead_id' 						=> $resultGetCopy['manual_dial_lead_id'],
							'dead_max' 									=> $resultGetCopy['dead_max'],
							'dead_max_dispo' 							=> $resultGetCopy['dead_max_dispo'],
							'dispo_max' 								=> $resultGetCopy['dispo_max'],
							'dispo_max_dispo' 							=> $resultGetCopy['dispo_max_dispo'], 
							'pause_max' 								=> $resultGetCopy['pause_max'],
							'max_inbound_calls' 						=> $resultGetCopy['max_inbound_calls'],
							'manual_dial_search_checkbox' 				=> $resultGetCopy['manual_dial_search_checkbox'],
							'hide_call_log_info' 						=> $resultGetCopy['hide_call_log_info'],
							'timer_alt_seconds' 						=> $resultGetCopy['timer_alt_seconds'],
							'wrapup_bypass' 							=> $resultGetCopy['wrapup_bypass'],
							'wrapup_after_hotkey' 						=> $resultGetCopy['wrapup_after_hotkey'], 
							'callback_active_limit' 					=> $resultGetCopy['callback_active_limit'],
							'callback_active_limit_override' 			=> $resultGetCopy['callback_active_limit_override'],
							'allow_chats' 								=> $resultGetCopy['allow_chats'],
							'comments_all_tabs' 						=> $resultGetCopy['comments_all_tabs'],
							'comments_dispo_screen' 					=> $resultGetCopy['comments_dispo_screen'],
							'comments_callback_screen' 					=> $resultGetCopy['comments_callback_screen'],
							'qc_comment_history' 						=> $resultGetCopy['qc_comment_history'], 
							'show_previous_callback' 					=> $resultGetCopy['show_previous_callback'],
							'clear_script' 								=> $resultGetCopy['clear_script'],
							'cpd_unknown_action' 						=> $resultGetCopy['cpd_unknown_action'],
							'manual_dial_search_filter' 				=> $resultGetCopy['manual_dial_search_filter'],
							'web_form_address_three' 					=> $resultGetCopy['web_form_address_three'],
							'manual_dial_override_field' 				=> $resultGetCopy['manual_dial_override_field'],
							'status_display_ingroup' 					=> $resultGetCopy['status_display_ingroup'], 
							'customer_gone_seconds' 					=> $resultGetCopy['customer_gone_seconds'],
							'agent_display_fields' 						=> $resultGetCopy['agent_display_fields'],
							'am_message_wildcards' 						=> $resultGetCopy['am_message_wildcards'],
							'manual_dial_timeout' 						=> $resultGetCopy['manual_dial_timeout'],
							'routing_initiated_recordings' 				=> $resultGetCopy['routing_initiated_recordings'],
							'manual_dial_hopper_check' 					=> $resultGetCopy['manual_dial_hopper_check'],
							'callback_useronly_move_minutes' 			=> $resultGetCopy['callback_useronly_move_minutes'], 
							'ofcom_uk_drop_calc' 						=> $resultGetCopy['ofcom_uk_drop_calc']
						);
						
						$q_insertCopy = $astDB->insert('vicidial_campaigns', $data_copy);
						$log_id 									= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Campaign: $campaign_id (copied from: $copy_from_campaign)", $log_group, $astDB->getLastQuery());
                        
                        $astDB->where('user_group', $log_group);
                        $allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
                        $allowed_campaigns = $allowed_camps['allowed_campaigns'];
                        
                        if (strlen($allowed_campaigns) < 1) { 
                            $allowed_campaigns = " -"; 
                        }
                        
                        if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
                            $update_data = array(
                                'allowed_campaigns' 					=>  " $campaign_id " . trim($allowed_campaigns)
                            );
                            
                            $astDB->where('user_group', $log_group);
                            $q_updateAllowedCampaign = $astDB->update('vicidial_user_groups', $update_data);
                        }

						if($q_insertCopy){
							foreach ($rsltGOCopy as $sourceCamp) {
								$campType 							= $sourceCamp['campaign_type'];
							}
							
							$SQLdate 								= date("Y-m-d H:i:s");
							//$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Copied campaign settings from $copy_from_campaign to $campaign_id", $log_group, $insertQuery);
							
							$datago_campaign 						= array(
								'campaign_id' 							=> $campaign_id, 
								'campaign_type'							=> $campType
								// 'location_id' 	=> (!empty($location))? $location:''
							);
							
							$goDB->insert('go_campaigns', $datago_campaign);
							$log_id 								= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Campaign: $campaign_id (copied from: $copy_from_campaign)", $log_group, $astDB->getLastQuery());
							$apiresults 							= array(
								"result" 								=> "success"
							);
						} else {
							$err_msg 								= error_handle("10010");
							$apiresults 							= array(
								"code" 									=> "10010", 
								"result" 								=> $err_msg
							);
						}
					} else {
						$err_msg 									= error_handle("10010");
						$apiresults 								= array(
							"code" 										=> "10010", 
							"result" 									=> $err_msg
						);
					}
				}
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
