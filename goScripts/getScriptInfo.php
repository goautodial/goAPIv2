<?php
    #######################################################
    #### Name: getScriptInfo.php 	               ####
    #### Description: API to get specific Script       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    $script_id = mysqli_real_escape_string($link, $_REQUEST["script_id"]); 

        if($script_id == null) {
                $apiresults = array("result" => "Error: Set a value for Script ID.");
        } else {
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                }

                $query = "SELECT script_id, script_name, script_comments, active, user_group, script_text FROM vicidial_scripts $ul WHERE script_id ='$script_id' $ul ORDER BY script_id LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
				$exist = mysqli_num_rows($rsltv);
				
		if($exist >= 1){
                while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                        $apiresults = array("result" => "success", 
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
