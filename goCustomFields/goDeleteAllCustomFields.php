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
  
  #$selectTable = "SHOW TABLES LIKE 'custom_$list_id'";
  $goTableName = "custom_".$list_id;
  $selectTable = "DESC $goTableName;";
  $queryResult = mysqli_query($link, $selectTable);
  $countResult = mysqli_num_rows($queryResult);
  
  if($countResult > 0){
      $astDB->where('list_id', $list_id);
      $queryDeleteCF = $astDB->delete('vicidial_lists_fields');
      
      if($queryDeleteCF){
        $astDB->dropTable($goTableName);

        $apiresults = array("result" => "success");
      }else{
        $apiresults = array("result" => "Error: Custom Field does not exist");
      }
  }else{
      $apiresults = array("result" => "Error: List does not exist");
  }
?>
