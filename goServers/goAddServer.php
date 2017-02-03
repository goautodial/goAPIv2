<?php
   ####################################################
   #### Name: goAddServer.php                      ####
   #### Description: API to add Servers	           ####
   #### Version: 4.0                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Alexander Jim H. Abenoja       ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once("../goFunctions.php");
 
    ### POST or GET Variables
        $server_id = mysqli_real_escape_string($link, $_REQUEST['server_id']);
        $server_description = mysqli_real_escape_string($link, $_REQUEST['server_description']);
        $server_ip = mysqli_real_escape_string($link, $_REQUEST['server_ip']);
        $active = $_REQUEST['active'];
        $asterisk_version = mysqli_real_escape_string($link, $_REQUEST['asterisk_version']);
		$max_vicidial_trunks = mysqli_real_escape_string($link, $_REQUEST['max_vicidial_trunks']);
		$user_group = $_REQUEST['user_group'];
		$local_gmt = "-5.00";
        
		$ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
		$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
		$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

    ### ERROR CHECKING 
        if($server_id == null) {
                $apiresults = array("result" => "Error: Set a value for Script ID not less than 3 characters.");
        } else {
				if($server_description == null){
							$apiresults = array("result" => "Error: Set a value for Server IP");
				} else {
						
						$groupId = go_get_groupid($goUser);
								
						if (!checkIfTenant($groupId)) {
								$ul = "";
						} else {
								$ul = "AND user_group='$groupId'";
						}
						
						$queryCheck = "SELECT server_id FROM servers where server_id='$server_id' OR server_ip = '$server_ip';";
						$sqlCheck = mysqli_query($link, $queryCheck);
						$countCheck = mysqli_num_rows($sqlCheck);
						
						if($countCheck <= 0){
						
								# getting the usergroup of the agent logged in
								$get_usergroup_query = "select user_group from vicidial_users where user='$user'";
								$init_getquery = mysqli_query($link, $get_usergroup_query);
								
								$usergroup_result = mysqli_fetch_array($init_getquery);
								$var_usergroup = $usergroup_result['user_group'];
								
								if($user != NULL){
										$user_group = $var_usergroup;
								}else {
										$user_group = $groupId;
								}
												
								$newQuery = "INSERT INTO servers(server_id, server_description, server_ip, active, asterisk_version, max_vicidial_trunks, local_gmt, user_group)
								VALUES('$server_id', '$server_description', '$server_ip', '$active', '$asterisk_version', '$max_vicidial_trunks', '$local_gmt', '$user_group');";
								$rsltv = mysqli_query($link, $newQuery);
						
								//$apiresults = array("usergroup" => $user_group, "query" => $newQuery);
								
								if($rsltv == false){
										$apiresults = array("result" => "Error: Add failed, check your details");
								} else {
										$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added New Server: $server_id", $log_group, $newQuery);
										$apiresults = array("result" => "success");
								}
							
						}else {
								$apiresults = array("result" => "Error: Add failed, Server already already exist!");
		
						}		
				}
		}

?>
