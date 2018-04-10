<?php
   #####################################################
   #### Name: goDeleteCustomField.php               ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Noel Umandap                    ####
   #### License: AGPLv2                             ####
   #####################################################
    $list_id        = mysqli_real_escape_string($link, $_REQUEST['list_id']);
    $field_label    = str_replace(" ","_",trim($_REQUEST['field_label']));
    $field_id       = $_REQUEST['field_id'];
    
    $selectTable = "SHOW TABLES LIKE 'custom_$list_id'";
    $queryResult = mysqli_query($link, $selectTable);
    $countResult = mysqli_num_rows($queryResult);
    
    if($countResult > 0){
        $selectColumns = "SHOW COLUMNS FROM `custom_$list_id` LIKE '$field_label';";
        $queryResult1 = mysqli_query($link, $selectColumns);
        $countResult1 = mysqli_num_rows($queryResult1);
        
        if($countResult1 > 0 && $field_label != "lead_id"){
            $table_name = 'custom_'.$list_id;

            $astDB->where('field_label', $field_label);
            $astDB->where('field_id', $field_id);
            $astDB->where('list_id', $list_id);
            $queryDeleteCF = $astDB->delete('vicidial_lists_fields');
            
            if($queryDeleteCF){
              $astDB->dropColumnFromTable($table_name, $field_label);

              $apiresults = array("result" => "success");
            }else{
              $apiresults = array("result" => "Error: Custom Field does not exist");
            }
        }else{
            $apiresults = array("result" => "Error: $field_label does not exist");
        }
    }else{
        $apiresults = array("result" => "Error: List does not exist");
    }

?>