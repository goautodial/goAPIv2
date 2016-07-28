<?php
    #######################################################
    #### Name: getAllCountryCodes.php	               ####
    #### Description: API to get all campaigns         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $campaign_id = $_REQUEST['campaign_id'];
    
    $query = "SELECT did_id,did_pattern,did_description,did_active,did_route
            FROM vicidial_inbound_dids
            WHERE camapign_id = '$campaign_id'
            ORDER BY did_pattern";
   	$rsltv = mysqli_query($link, $query);
    
    while($fresults = mysqli_fetch_assoc($rsltv)){
        $dataDidID[] 			= $fresults['did_id'];
        $dataDidPattern[] 		=  $fresults['did_pattern'];
        $dataDidDescription[] 	=  $fresults['did_description'];
        $dataActive[] 			=  $fresults['did_active'];
        $dataDidRoute[] 		=  $fresults['did_route'];

        $apiresults = array(
                        "result" => "success",
                        "did_id" => $dataDidID,
                        "did_pattern" => $dataDidPattern,
                        "did_description" => $dataDidDescription,
                        "active" => $dataActive,
                        "did_route" => $dataDidRoute
                    );
	}
?>