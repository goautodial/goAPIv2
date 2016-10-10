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
	$goUser 						= $_REQUEST['goUser'];
    $ip_address 					= $_REQUEST['hostname'];

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
	$force_reset_hopper 			= $_REQUEST['force_reset_hopper'];
	$dial_status 					= $_REQUEST['dial_status'];
	$lead_order 					= $_REQUEST['lead_order'];
	$lead_filter 					= $_REQUEST['lead_filter'];
	$dial_timeout 					= $_REQUEST['dial_timeout'];
	$manual_dial_prefix 			= $_REQUEST['manual_dial_prefix'];
	$get_call_launch 				= $_REQUEST['get_call_launch'];
	$am_message_exten 				= $_REQUEST['am_message_exten'];
	$am_message_chooser 			= $_REQUEST['am_message_chooser'];
	$agent_pause_codes_active 		= $_REQUEST['agent_pause_codes_active'];
	$manual_dial_filter 			= $_REQUEST['manual_dial_filter'];
	$manual_dial_list_id 			= $_REQUEST['manual_dial_list_id'];
	$available_only_ratio_tally 	= $_REQUEST['available_only_ratio_tally'];
	$campaign_rec_filename 			= $_REQUEST['campaign_rec_filename'];
	$next_agent_call 				= $_REQUEST['next_agent_call'];
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

   	//$apiresults = array("data" => $_REQUEST); 

    ### Default values 
    $defActive = array("Y","N");
    $defDialMethod = array("MANUAL","RATIO","ADAPT_HARD_LIMIT","ADAPT_TAPERED","ADAPT_AVERAGE","INBOUND_MAN"); 
    
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
										dial_timeout = '$dial_timeout', 
										manual_dial_prefix = '$manual_dial_prefix', 
										get_call_launch = '$get_call_launch', 
										am_message_exten = '$amMessageExten', 
										agent_pause_codes_active = '$agent_pause_codes_active', 
										manual_dial_filter = '$manual_dial_filter', 
										manual_dial_list_id = '$manual_dial_list_id', 
										available_only_ratio_tally = '$available_only_ratio_tally', 
										campaign_rec_filename = '$campaign_rec_filename', 
										next_agent_call = '$next_agent_call', 
										three_way_call_cid = '$three_way_call_cid', 
										three_way_dial_prefix = '$three_way_dial_prefix', 
										customer_3way_hangup_logging = '$customer_3way_hangup_logging', 
										customer_3way_hangup_seconds = '$customer_3way_hangup_seconds', 
										customer_3way_hangup_action = '$customer_3way_hangup_action',
										campaign_allow_inbound = '$campaign_allow_inbound',
										closer_campaigns = '$closer_campaigns',
										xfer_groups = '$xfer_groups'
									WHERE campaign_id='$campaign_id'
									LIMIT 1;";
					//echo $updateQuery;
			   		$updateResult = mysqli_query($link, $updateQuery);
					
					$stmtGO = "SELECT * FROM go_campaigns WHERE campaign_id='$campaign_id'";
					$rsltGO = mysqli_query($linkgo, $stmtGO);
					$numGO = mysqli_num_rows($rsltGO);
					if ($numGO > 0) {
						$updateGO = "UPDATE go_campaigns SET custom_fields_launch='$custom_fields_launch' WHERE campaign_id='$campaign_id';";
						$resultGO = mysqli_query($linkgo, $updateGO);
					} else {
						$campaign_type = (strlen($campaign_type) > 0) ? $campaign_type : "OUTBOUND";
						$insertGO = "INSERT INTO go_campaigns (campaign_id, campaign_type, custom_fields_launch) VALUES('$campaign_id', '$campaign_type', '$custom_fields_launch');";
						$resultGO = mysqli_query($linkgo, $insertGO);
					}
					### Admin logs
					$SQLdate = date("Y-m-d H:i:s");
					$queryLog = "INSERT INTO go_action_logs (
									user,
									ip_address,
									event_date,
									action,
									details,
									db_query
								) values(
									'$goUser',
									'$ip_address',
									'$SQLdate','MODIFY',
									'MODIFY NEW CAMPAIGN $campaign_id',
									'UPDATE vicidial_campaigns SET campaign_name=$uCampaignName,
									dial_method=$uDialMethod,
									active=$uActive
									WHERE campaign_id=$dataCampID LIMIT 1;'
								)";
					$rsltvLog = mysqli_query($linkgo, $queryLog);
					
					$apiresults = array("result" => "success");
				} else {
					$apiresults = array("result" => "Error: Campaign doens't exist.");
				}
			}
		}
	}//end

?>
