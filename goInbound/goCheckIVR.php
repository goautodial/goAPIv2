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
    $menu_id = $_REQUEST['menu_id'];
    
    $stmtCheck = "SELECT menu_id from vicidial_call_menu where menu_id='$menu_id';";
    $queryCheck =  mysqli_query($link, $stmtCheck);
    $row = mysqli_num_rows($queryCheck);
    
    if ($row > 0) {
        $apiresults = array("result" => "Error: CALL MENU NOT ADDED - there is already a CALL MENU in the system with this ID");
    }else{
        $apiresults = array("result" => "success");
    }
?>