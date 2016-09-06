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
    $user_group= $_REQUEST['user_group'];
    
    $query = "SELECT user_group FROM vicidial_user_groups WHERE user_group='$user_group';";
    $rsltv = mysqli_query($link, $query);
    $countResult = mysqli_num_rows($rsltv);
    
    if($countResult > 0) {
        $apiresults = array("result" => "Error: User Group already exist.");
    } else {
        $apiresults = array("result" => "success");
    }
?>