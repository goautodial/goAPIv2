<?php
/**
 * @file        goDeleteAllCustomFields.php
 * @brief       API to delete all custom fields for a list
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <jeremiah@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
  $list_id = $astDB->escape($_REQUEST['list_id']);
  
  #$selectTable = "SHOW TABLES LIKE 'custom_$list_id'";
  $goTableName = "custom_".$list_id;
  $selectTable = "DESC $goTableName;";
  $queryResult = $astDB->rawQuery($selectTable);
  $countResult = $astDB->getRowCount();
  
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
