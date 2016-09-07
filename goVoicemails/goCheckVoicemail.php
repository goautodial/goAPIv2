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
    $voicemail_id = $_REQUEST['voicemail_id'];
    
    $queryCheck = "SELECT voicemail_id from vicidial_voicemail where voicemail_id='".$voicemail_id."';";
    $sqlCheck = mysqli_query($link, $queryCheck);
    $countCheck = mysqli_num_rows($sqlCheck);
    
    if($countCheck <= 0){
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Add failed, Voicemail already already exist!");
    }
?>