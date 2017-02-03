<?php
    #######################################################
    #### Name: goGetServerInfo.php 	             		####
    #### Description: API to get specific Server       ####
    #### Version: 4.0                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Alexander Jim H. Abenoja			 ####
    #### License: AGPLv2                               ####
    #######################################################
    
    include_once ("../goFunctions.php");
	
    $server_id = mysqli_real_escape_string($link, $_REQUEST["server_id"]); 

        if($server_id == null) {
                $apiresults = array("result" => "Error: Set a value for Server ID.");
        } else {
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                }

                $query = "SELECT * FROM servers $ul WHERE server_id ='$server_id' $ul LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
				$exist = mysqli_num_rows($rsltv);
				$data = mysqli_fetch_array($rsltv);
				
			if($exist <= 1){
				$apiresults = array("result" => "success", "data" => $data);
	        } else {
                $apiresults = array("result" => "Error: Server does not exist.");
			}
        	}
?>
