<?php
   ####################################################
   #### Name: goAddScript.php                      ####
   #### Description: API to add Script	           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    ### POST or GET Variables
	$agent = get_settings('user', $astDB, $goUser);
	
	$script_type = mysqli_real_escape_string($link, $_REQUEST['script_type']);
    $script_id = mysqli_real_escape_string($link, $_REQUEST['script_id']);
    $script_name = mysqli_real_escape_string($link, $_REQUEST['script_name']);
    $script_comments = mysqli_real_escape_string($link, $_REQUEST['script_comments']);
    $script_text = $_REQUEST['script_text'];
    $active = mysqli_real_escape_string($link, $_REQUEST['active']);
    //$campaign_id = $_REQUEST['campaign_id'];
	$user = mysqli_real_escape_string($link, $_REQUEST['user']);
	$user_group = mysqli_real_escape_string($link, $_REQUEST['user_group']);
    $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
    $goUser = mysqli_real_escape_string($link, $_REQUEST['goUser']);
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

    ### Default values 
    $defActive = array("Y","N");

    ### ERROR CHECKING 
    if($script_id == null) {
        $apiresults = array("result" => "Error: Set a value for Script ID not less than 3 characters.");
    } else {
		if($script_text == null){
					$apiresults = array("result" => "Error: Set a value for Script Text");
		} else {
			$groupId = go_get_groupid($goUser);

			$astDB->where('script_id', $script_id);
    		$script = $astDB->getOne('vicidial_scripts', null, 'script_id');
			
			if($script){
				if($script_type == "default"){
					$subscript = 0;
				}else{
					$subscript = 1;
				}		
					$queryColumn = "SHOW COLUMNS FROM `vicidial_scripts` LIKE 'subscript';";
					$resultColumn = mysqli_query($link, $queryColumn);	
					$resultCpulumnNumRows = mysqli_num_rows($resultColumn);

					if($resultCpulumnNumRows > 0){
						$data_script = array('subscript' => subscript);
					}
					$data_script = array(
						'script_id' 		=> $script_id, 
						'script_comments' 	=> $script_comments, 
						'script_name' 		=> $script_name, 
						'active' 			=> $active, 
						'user_group' 		=> $user_group, 
						'script_text' 		=> $script_text
					);
					$insertScript = $astDB->insert('vicidial_scripts', $data_script);
					$insertQuery = $astDB->getLastQuery();
				if(!$insertScript){
						$apiresults = array("result" => "Error: Add failed, check your details");
				} else {
						$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added New Script: $script_id", $log_group, $insertQuery);
						$apiresults = array("result" => "success");
				}
			}else {
				$apiresults = array("result" => "Error: Add failed, Script already already exist!");
			}		
		}
	}

?>
