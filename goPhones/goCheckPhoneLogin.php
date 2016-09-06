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
    $extension = $_REQUEST['extension'];
    
    $query = "SELECT extension FROM phones WHERE extension='$extension';";
    $rsltv = mysqli_query($link,$query);
    $countResult = mysqli_num_rows($rsltv);

    if($countResult <= 0) {
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Phone already exist.");
    }
?>