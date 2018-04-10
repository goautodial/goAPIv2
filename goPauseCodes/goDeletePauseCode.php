<?php
    ########################################################
    #### Name: goDeletePauseCode.php	                ####
    #### Description: API to delete specific Pause Code ####
    #### Version: 0.9                                   ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016       ####
    #### Written by: Jeremiah Sebastian V. Samatra      ####
    #### License: AGPLv2                                ####
    ########################################################
    ### POST or GET Variables
	$camp = $_REQUEST['pauseCampID'];
	$code = $_REQUEST['pause_code'];
	$ip_address = $_REQUEST['hostname'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    ### Check Voicemail ID if its null or empty
	if($camp == null || $code == null) { 
		$apiresults = array("result" => "Error: Set a value for Campaign ID and Pause Code."); 
	} else {
        $astDB->where('campaign_id', $camp);
        $astDB->where('pause_code', $code);
        $checkPC = $astDB->get('vicidial_pause_codes', null, 'pause_code, campaign_id');

		if($checkPC) {
                $astDB->where('campaign_id', $camp);
                $astDB->where('pause_code', $code);
                $astDB->delete('vicidial_pause_codes');
                $deleteQuery = $astDB->getLastQuery();

				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Pause Code $code from Campaign ID $camp", $log_group, $deleteQuery);
				$apiresults = array("result" => "success");
		} else {
			$apiresults = array("result" => "Error: Pause Code doesn't exist.");
		}
	}//end
?>
