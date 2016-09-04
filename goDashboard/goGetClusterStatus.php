<?php
    ####################################################
    #### Name: goGetClusterStatus.php               ####
    #### Type: API to get total agents online       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jeremiah Sebastian Samatra     ####
    ####            Demian Lizandro A. Biscocho     ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    
    $query = "SELECT s.server_id, s.server_description, s.server_ip, s.active, s.sysload, s.channels_total, s.cpu_idle_percent, s.disk_usage, su.last_update as s_time,UNIX_TIMESTAMP(su.last_update) as u_time FROM servers s, server_updater su WHERE s.server_ip=su.server_ip LIMIT 100";

    $rsltv = mysqli_query($link,$query);
    $countResult = mysqli_num_rows($rsltv);

    if($countResult > 0){
    
        $data = array();
        
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){       
            array_push($data, $fresults);
        }
        
        $apiresults = array("result" => "success", "data" => $data);
    } 


?>
