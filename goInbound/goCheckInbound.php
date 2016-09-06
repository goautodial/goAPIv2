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
    $group_id = mysqli_real_escape_string($link, $_REQUEST['group_id']);
    
    $stmtCheck = "SELECT group_id from vicidial_inbound_groups where group_id='$group_id';";
    $queryCheck =  mysqli_query($link, $stmtCheck);
    $row = mysqli_num_rows($queryCheck);
    
    if ($row > 0) {
        $apiresults = array("result" => "GROUP NOT ADDED - there is already a Inbound in the system with this ID\n");
    } else {
        $apiresults = array("result" => "success");
    }
        
?>