<?php
   #####################################################
   #### Name: goCheckUser.php	                    ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Alexander Jim H. Abenoja        ####
   #### License: AGPLv2                             ####
   #####################################################
    
    ### POST or GET Variables
    $carrier_id = $astDB->escape($_REQUEST['carrier_id']);
    
    //$queryCheck = "SELECT carrier_id FROM vicidial_server_carriers WHERE carrier_id ='$carrier_id';";
    $astDB->where('carrier_id', $carrier_id);
    $rsltv = $astDB->get('vicidial_server_carriers');
    $countCheck = $astDB->getRowCount();
    
    if($countCheck > 0) {
        $apiresults = array("result" => "Error: Carrier already exist.");
    } else {
        $apiresults = array("result" => "success");
    }
?>