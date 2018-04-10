<?php
    #######################################################
    #### Name: getScriptInfo.php 	               ####
    #### Description: API to get specific Script       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    $agent = get_settings('user', $astDB, $goUser);
    $script_id = mysqli_real_escape_string($link, $_REQUEST["script_id"]); 

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
        $script = $astDB->getOne('vicidial_scripts', null, 'script_id, script_name, script_comments, active, user_group, script_text');
		
	    if($script){
            foreach($script as $fresults)
                $apiresults = array(
                    "result" => "success", 
                    "script_id" => $fresults['script_id'], 
                    "script_name" => $fresults['script_name'], 
                    "script_comments" => $fresults['script_comments'], 
                    "active" => $fresults['active'], 
                    "user_group" => $fresults['user_group'], 
                    "script_text" => $fresults['script_text']
                );
            }
        } else {
            $apiresults = array("result" => "Error: Script does not exist.");
    	}
    }
?>
