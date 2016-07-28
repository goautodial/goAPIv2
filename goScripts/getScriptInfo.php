<?php
    #######################################################
    #### Name: getScriptInfo.php 	               ####
    #### Description: API to get specific Script       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    $script_id = $_REQUEST["script_id"]; 

        if($script_id == null) {
                $apiresults = array("result" => "Error: Set a value for Script ID.");
        } else {
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                }

                $query = "SELECT script_id, script_name, active, user_group FROM vicidial_scripts $ul WHERE script_id ='".mysqli_real_escape_string($script_id)."' $ul $addedSQL ORDER BY script_id LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
		$exist = mysqli_num_rows($rsltv);
		if($exist >= 1){
                while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){

                        $dataScriptID[] = $fresults['script_id'];
                        $dataScriptName[] = $fresults['script_name'];
                        $dataActive[] = $fresults['active'];
                        $dataUserGroup[] = $fresults['user_group'];
                        $apiresults = array("result" => "success", "script_id" => $dataScriptID, "script_name" => $dataScriptName, "active" => $dataActive, "user_group" => $dataUserGroup);
                }
	        } else {

                $apiresults = array("result" => "Error: Script does not exist.");

        	}
        	}
?>
