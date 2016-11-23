<?php
   #####################################################
   #### Name: goDeleteCustomField.php               ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Noel Umandap                    ####
   #### License: AGPLv2                             ####
   #####################################################
    
    include_once ("../goFunctions.php");
    
    $list_id        = mysqli_real_escape_string($link, $_REQUEST['list_id']);
    
    #$selectTable = "SHOW TABLES LIKE 'custom_$list_id'";
    $goTableName = "custom_".$list_id;
    $selectTable = "DESC $goTableName;";
    $queryResult = mysqli_query($link, $selectTable);
    $countResult = mysqli_num_rows($queryResult);
    
    if($queryResult > 0){
        $deleteColumnTable = "DROP TABLE `custom_$list_id`";
        $queryDelete = mysqli_query($link, $deleteColumnTable);
        
        $deleteAllColumn = "DELETE FROM vicidial_lists_fields
                        WHERE list_id='$list_id';";
        $query = mysqli_query($link, $deleteAllColumn);
        //$result = mysqli_num_rows($query);
        
        if($query){
            $apiresults = array("result" => "success");
        }else{
            $apiresults = array("result" => "Error: Custom Field does not exist");
        }
    }else{
        $apiresults = array("result" => "Error: List does not exist");
    }

?>
