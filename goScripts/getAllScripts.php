<?php
    #######################################################
    #### Name: getAllScripts.php                ####
    #### Description: API to get all scripts  ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                  
                }

		$query = "SELECT script_id, script_name, active, user_group FROM vicidial_scripts $ul ORDER BY script_id ASC;";
   		$rsltv = mysqli_query($link, $query);

		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataScriptID[] = $fresults['script_id'];
			$dataScriptName[] = $fresults['script_name'];
			$dataActive[] = $fresults['active'];
			$dataUserGroup[] = $fresults['user_group'];
 	  		$apiresults = array(
					"result" => "success",
					"script_id" => $dataScriptID,
					"script_name" => $dataScriptName,
					"active" => $dataActive,
					"user_group" => $dataUserGroup
			);
		}

?>
