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
    $did_pattern = $_REQUEST['did_pattern'];
    
    $stmtdf = "SELECT did_pattern from vicidial_inbound_dids where did_pattern='$did_pattern';";
    $querydf = mysqli_query($link, $stmtdf);
    $rowdf = mysqli_num_rows($querydf);

    if ($rowdf > 0) {
        $apiresults = array("result" => "<br>DID NOT ADDED - DID already exist.\n");
    } else {
        $apiresults = array("result" => "success");
    }
?>