<?php
    ####################################################
    #### Name: goGetClusterStatus.php               ####
    #### Type: API to get total agents online       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jeremiah Sebastian Samatra     ####
    ####             Demian Lizandro A. Biscocho    ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");

	    
	$query = "SELECT server_id,server_description,server_ip,active,sysload,channels_total,cpu_idle_percent,disk_usage from servers order by server_id;";
	$rsltv = mysqli_query($link,$query);
	$countResult = mysqli_num_rows($rsltv);

        if($countResult > 0) {
            $data = array();
                while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){               
                    array_push($data, $fresults);
                }
                $apiresults = array("result" => "success", "data" => $data);
        } 	
	

?>
