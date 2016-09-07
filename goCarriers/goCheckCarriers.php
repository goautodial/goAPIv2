<?php
   #####################################################
   #### Name: goCheckUser.php	                    ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Alexander Jim H. Abenoja        ####
   #### License: AGPLv2                             ####
   #####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
    $carrier_id = $_REQUEST['carrier_id'];
    
    $queryCheck = "SELECT carrier_id FROM vicidial_server_carriers WHERE carrier_id ='$carrier_id';";
    $rsltv = mysqli_query($link, $queryCheck);
    $countCheck = mysqli_num_rows($rsltv);
    
    if($countCheck > 0) {
        $apiresults = array("result" => "Error: Carrier already exist.");
    } else {
        $apiresults = array("result" => "success");
    }
?>