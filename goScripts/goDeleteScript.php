<?php
    ////////////////////////////////////////////////////
    /// Name: goDeleteScript.php 	///
    /// Description: API to delete specific Script 	///
    /// Version: 0.9 	///
    /// Copyright: GOAutoDial Inc. (c) 2011-2016 	///
    /// Written by: Jeremiah Sebastian V. Samatra 	///
    /// License: AGPLv2 	///
    ////////////////////////////////////////////////////
    
    // POST or GET Variables
    $script_id = mysqli_real_escape_string($link, $_REQUEST['script_id']);
    $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
	
    $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
	if($script_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Script ID."); 
	} else {
        $groupId = go_get_groupid($goUser);

        if (!checkIfTenant($groupId)) {
            //do nothing
        } else { 
            $astDB->where('user_group', $agent->user_group);  
        }
        
        $astDB->where('script_id', $script_id);
        $getScripts = $astDB->get('vicidial_scripts', null, 'script_id');

		if($getScripts) {
            $astDB->where('script_id', $script_id);
            $astDB->delete('vicidial_scripts');
			$deleteQuery = $astDB->getLastQuery();

            $astDB->where('script_id', $script_id);
            $astDB->delete('go_scripts');

            $data_update = array('campaign_script' => '');
            $astDB->where('campaign_script', $script_id);
            $astDB->update('vicidial_campaigns', $data_update);

			$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Script ID: $script_id", $log_group, $deleteQuery);

			$apiresults = array("result" => "success");
		} else {
			$apiresults = array("result" => "Error: Script doesn't exist.");
		}
	}
?>
