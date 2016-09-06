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
    $moh_id = $_REQUEST['moh_id'];
    
    $queryCheck = "SELECT moh_id from vicidial_music_on_hold where moh_id='".$moh_id."';";
    $sqlCheck = mysqli_query($link, $queryCheck);
    $countCheck = mysqli_num_rows($sqlCheck);
    
    if($countCheck <= 0){
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Add failed, Music On Hold already already exist!");
    }
?>