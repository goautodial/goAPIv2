<?php
    #######################################################
    #### Name: goEditServer.php		     				####
    #### Description: API to edit specific Server      ####
    #### Version: 4.0                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Alexander Jim H. Abenoja          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
        $server_id = $_REQUEST['server_id'];
		$server_description = mysqli_real_escape_string($link, $_REQUEST['server_description']);
		$server_ip = mysqli_real_escape_string($link, $_REQUEST['server_ip']);
		$active = mysqli_real_escape_string($link, $_REQUEST['active']);
		$user_group = mysqli_real_escape_string($link, $_REQUEST['user_group']);
		$asterisk_version = mysqli_real_escape_string($link, $_REQUEST['asterisk_version']);
		$max_vicidial_trunks = mysqli_real_escape_string($link, $_REQUEST['max_vicidial_trunks']);
		$outbound_calls_per_second = mysqli_real_escape_string($link, $_REQUEST['outbound_calls_per_second']);
		$vicidial_balance_active = mysqli_real_escape_string($link, $_REQUEST['vicidial_balance_active']);
		$local_gmt = mysqli_real_escape_string($link, $_REQUEST['local_gmt']);
		$generate_vicidial_conf = mysqli_real_escape_string($link, $_REQUEST['generate_vicidial_conf']);
		$rebuild_conf_files = mysqli_real_escape_string($link, $_REQUEST['rebuild_conf_files']);
		$rebuild_music_on_hold = mysqli_real_escape_string($link, $_REQUEST['rebuild_music_on_hold']);
		$recording_web_link = mysqli_real_escape_string($link, $_REQUEST['recording_web_link']);
		$alt_server_ip = mysqli_real_escape_string($link, $_REQUEST['alt_server_ip']);
		$external_server_ip = mysqli_real_escape_string($link, $_REQUEST['external_server_ip']);
		
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
        $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
        $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    ### Check Voicemail ID if its null or empty
	if($server_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Server ID."); 
	} else {
 
		$groupId = go_get_groupid($goUser);

		if (!checkIfTenant($groupId)) {
				$ul = "";
		} else {
				$ul = "AND user_group='$groupId'";
		}
		
   		$queryOne = "SELECT server_id FROM servers where server_id='$server_id' $ul;";
   		$rsltvOne = mysqli_query($link, $queryOne);
		$countResult = mysqli_num_rows($rsltvOne);

		if($countResult > 0) {
				$updateQuery = "UPDATE servers SET server_description = '$server_description', server_ip = '$server_ip', active = '$active', user_group = '$user_group', asterisk_version = '$asterisk_version', max_vicidial_trunks = '$max_vicidial_trunks', outbound_calls_per_second = '$outbound_calls_per_second', vicidial_balance_active = '$vicidial_balance_active', local_gmt = '$local_gmt', generate_vicidial_conf = '$generate_vicidial_conf', rebuild_conf_files = '$rebuild_conf_files', rebuild_music_on_hold = '$rebuild_music_on_hold', recording_web_link = '$recording_web_link', alt_server_ip = '$alt_server_ip', external_server_ip = '$external_server_ip' WHERE server_id= '$server_id';"; 
   				$updateResult = mysqli_query($link, $updateQuery);
				
				$log_id = log_action($linkgo, 'UPDATE', $log_user, $ip_address, "Updated Server ID: $server_id", $log_group, $updateQuery);
				
				$apiresults = array("result" => "success");

		} else {
			$apiresults = array("result" => "Error: Server doesn't exist.");
		}
	}//end
?>
