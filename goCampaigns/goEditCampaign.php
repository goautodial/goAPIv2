<?php
   ####################################################
   #### Name: goEditCampaign.php                   ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jerico James Milo              ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once("../goFunctions.php");

	### POST or GET Variables
	$goUser 		= $_REQUEST['goUser'];
	$ip_address 		= $_REQUEST['hostname'];
	$log_user			= $_REQUEST['log_user'];
	$log_group			= $_REQUEST['log_group'];
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
	$hopper_level			= $_REQUEST['hopper_level'];
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
	$use_internal_dnc			= $_REQUEST['use_internal_dnc'];
	$use_campaign_dnc			= $_REQUEST['use_campaign_dnc'];
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
	$per_call_notes				= $_REQUEST['per_call_notes'];
	$url_tab_first_title		= $_REQUEST['url_tab_first_title'];
	$url_tab_first_url		= $_REQUEST['url_tab_first_url'];
	$url_tab_second_title		= $_REQUEST['url_tab_second_title'];
	$url_tab_second_url		= $_REQUEST['url_tab_second_url'];
	$agent_lead_search		= $_REQUEST['agent_lead_search'];
	$agent_lead_search_method = $_REQUEST['agent_lead_search_method'];
	
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
	
	$amd_send_to_vmx = $_REQUEST['amd_send_to_vmx'];
	$waitforsilence_options = $_REQUEST['waitforsilence_options'];

   	//$apiresults = array("data" => $_REQUEST); 

    ### Default values 
    $defActive = array("Y","N");
    $defDialMethod = array("MANUAL","RATIO","ADAPT_HARD_LIMIT","ADAPT_TAPERED","ADAPT_AVERAGE","INBOUND_MAN");
    
    if($campaign_type == "SURVEY"){
        if(!empty($dial_method)){
            $dial_method = $dial_method;
        }else{
            $dial_method = "RATIO";
        }
    }
    
    ### Check campaign_id if its null or empty
	if($campaign_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Campaign ID."); 
	} else {
    		### Check value compare to default values
		if(!in_array($active,$defActive) && $active != null) { 
			$apiresults = array("result" => "Error: Default value for active is Y or N only."); 
		} else {
			if(!in_array($dial_method,$defDialMethod) && $dial_method != null) { 
				$apiresults = array("result" => "Error: Default value for dial method are MANUAL,RATIO,ADAPT_HARD_LIMIT,ADAPT_TAPERED,ADAPT_AVERAGE,INBOUND_MAN only."); 
			} else {
				
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

				if($campaign_id != null) {
					$updateQuery = "UPDATE vicidial_campaigns SET
										campaign_name = '$campaign_name', 
										active = '$active', 
										dial_method = '$dial_method', 
										auto_dial_level = '$autoDialLevel', 
										dial_prefix = '$dialprefix',
										web_form_address = '$webform', 
										campaign_script = '$campaign_script', 
										campaign_cid = '$campaign_cid', 
										campaign_recording = '$campaign_recording', 
										campaign_vdad_exten = '$campaign_vdad_exten', 
										local_call_time = '$local_call_time',  
										dial_status_a = '$dial_status', 
										lead_order = '$lead_order', 
										lead_filter_id = '$lead_filter_id',
										hopper_level = '$hopper_level', 
										dial_timeout = '$dial_timeout', 
										manual_dial_prefix = '$manual_dial_prefix', 
										get_call_launch = '$get_call_launch', 
										am_message_exten = '$amMessageExten', 
										agent_pause_codes_active = '$agent_pause_codes_active', 
										manual_dial_filter = '$manual_dial_filter',
										use_internal_dnc = '$use_internal_dnc',
										use_campaign_dnc = '$use_campaign_dnc', 
										manual_dial_list_id = '$manual_dial_list_id', 
										available_only_ratio_tally = '$available_only_ratio_tally', 
										campaign_rec_filename = '$campaign_rec_filename', 
										next_agent_call = '$next_agent_call', 
										xferconf_a_number = '$xferconf_a_number', 
										xferconf_b_number = '$xferconf_b_number', 
										three_way_call_cid = '$three_way_call_cid', 
										three_way_dial_prefix = '$three_way_dial_prefix', 
										customer_3way_hangup_logging = '$customer_3way_hangup_logging', 
										customer_3way_hangup_seconds = '$customer_3way_hangup_seconds', 
										customer_3way_hangup_action = '$customer_3way_hangup_action',
										campaign_allow_inbound = '$campaign_allow_inbound',
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
										per_call_notes = '$per_call_notes',
										agent_lead_search = '$agent_lead_search',
										agent_lead_search_method = '$agent_lead_search_method'
									WHERE campaign_id='$campaign_id'
									LIMIT 1;";
					//echo $updateQuery;
			   		$updateResult = mysqli_query($link, $updateQuery);
					
					$stmtGO = "SELECT * FROM go_campaigns WHERE campaign_id='$campaign_id'";
					$rsltGO = mysqli_query($linkgo, $stmtGO);
					$numGO = mysqli_num_rows($rsltGO);
					$url_tab_first_url = str_replace("http://", "https://", $url_tab_first_url);
					$url_tab_second_url = str_replace("http://", "https://", $url_tab_second_url);
					if ($numGO > 0) {
						$updateGO = "UPDATE go_campaigns SET custom_fields_launch='$custom_fields_launch', custom_fields_list_id='$custom_fields_list_id',url_tab_first_title='$url_tab_first_title',url_tab_first_url='$url_tab_first_url',url_tab_second_title='$url_tab_second_title',url_tab_second_url='$url_tab_second_url' WHERE campaign_id='$campaign_id';";
						$resultGO = mysqli_query($linkgo, $updateGO);
					} else {
						$campaign_type = (strlen($campaign_type) > 0) ? $campaign_type : "OUTBOUND";
						$insertGO = "INSERT INTO go_campaigns (campaign_id, campaign_type, custom_fields_launch, custom_fields_list_id,url_tab_first_title,url_tab_first_url,url_tab_second_title,url_tab_second_url) VALUES('$campaign_id', '$campaign_type', '$custom_fields_launch', '$custom_fields_list_id','$url_tab_first_title','$url_tab_first_url','$url_tab_second_title','$url_tab_second_url');";
						$resultGO = mysqli_query($linkgo, $insertGO);
					}
					### Admin logs
					$SQLdate = date("Y-m-d H:i:s");
					//$queryLog = "INSERT INTO go_action_logs (
					//				user,
					//				ip_address,
					//				event_date,
					//				action,
					//				details,
					//				db_query
					//			) values(
					//				'$goUser',
					//				'$ip_address',
					//				'$SQLdate','MODIFY',
					//				'MODIFY NEW CAMPAIGN $campaign_id',
					//				'UPDATE vicidial_campaigns SET campaign_name=$uCampaignName,
					//				dial_method=$uDialMethod,
					//				active=$uActive
					//				WHERE campaign_id=$dataCampID LIMIT 1;'
					//			)";
					//$rsltvLog = mysqli_query($linkgo, $queryLog);
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
				} else {
					$apiresults = array("result" => "Error: Campaign doens't exist.");
				}
			}
		}
	}//end

?>
