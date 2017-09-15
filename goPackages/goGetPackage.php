<?php
   #####################################################
   #### Name: goGetPackage.php	                    ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Alexander Jim H. Abenoja        ####
   #### License: AGPLv2                             ####
   #####################################################
    
    include_once ("../goFunctions.php");
    
    $apiresults = array("result" => "success", "show_carrier_settings" => $VARBRINGOWNVOIP, "packagetype" => $VARPACKAGETYPE);
    
?>