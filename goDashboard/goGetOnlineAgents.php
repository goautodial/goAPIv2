<?php
 ####################################################
 #### Name: goGetOnlineAgents.php                ####
 #### Type: API for dashboard php encode         ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
 #### Written by: Demian Lizandro Biscocho       ####
 #### License: AGPLv2                            ####
 ####################################################

 include "goFunctions.php";
 
 $groupId = go_get_groupid($goUser);

    if (!checkIfTenant($groupId)) {
        $ul = "";
            } else {
                    $stringv = go_getall_allowed_campaigns($goUser);
                    $ul = " and campaign_id IN ('$stringv') ";
    }
    
    
    $query = "CALL get_OnlineAgents()";
    $rsltv = mysqli_query($link,$query);
    $countResult = mysqli_num_rows($rsltv);

    if($countResult > 0) {
        $data = array();
            while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){               
                array_push($data, $fresults);
            }
            $apiresults = array("result" => "success", "data" => $data);
    } else {
            $apiresults = array("result" => "Error: Can't retrieve data", "COUNT:" => $countResult);
    } 
    
?>
