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
    $call_time_id = $_REQUEST['call_time_id'];

    $queryCheck = "SELECT call_time_id from vicidial_call_times where call_time_id='$call_time_id';";
    $sqlCheck = mysqli_query($link, $queryCheck);
    $countCheck = mysqli_num_rows($sqlCheck);
    
    if($countCheck <= 0){
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Call Time ID already exists!");
    }
?>