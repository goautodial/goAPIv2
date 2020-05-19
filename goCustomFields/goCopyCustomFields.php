<?php
/**
 * @file        goCopyCustomFields.php
 * @brief       API to copy a custom field from another list to an existing list
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
    
    include_once ("../goFunctions.php");
    
    $list_to   = $astDB->escape($_REQUEST['list_to']);
    $list_from = $astDB->escape($_REQUEST['list_from']);
    $copy_option = $astDB->escape($_REQUEST['copy_option']);
    
    $goUser = $astDB->escape($_REQUEST['goUser']);
    $ip_address = $astDB->escape($_REQUEST['hostname']);
    $log_user = $astDB->escape($_REQUEST['log_user']);
    $log_group = $astDB->escape($_REQUEST['log_group']);
    
    $vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|';
    
    if($copy_option == "UPDATE"){
        $astDB->where('list_id', $list_from);
        $fromList = $astDB->get('vicidial_lists_fields', null, 'field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order');
        
        $output = array();
        foreach($fromList as $fresults){
            $field_id          = $fresults['field_id'];
            $field_label       = $fresults['field_label'];
            $field_name        = $fresults['field_name'];
            $field_description = $fresults['field_description'];
            $field_rank        = $fresults['field_rank'];
            $field_help        = $fresults['field_help'];
            $field_type        = $fresults['field_type'];
            $field_options     = $fresults['field_options'];
            $field_size        = $fresults['field_size'];
            $field_max         = $fresults['field_max'];
            $field_default     = $fresults['field_default'];
            $field_cost        = $fresults['field_cost'];
            $field_required    = $fresults['field_required'];
            $multi_position    = $fresults['multi_position'];
            $name_position     = $fresults['name_position'];
            $field_order       = $fresults['field_order'];

            $astDB->where('list_id', $list_to);
            $astDB->where('field_label', $field_label);
            $checkField = $astDB->get('vicidial_lists_fields', null, '*');
            
            if($checkField){
                $checkTable = "SHOW TABLES LIKE 'custom_$list_to'";
                $queryCheckTable = $astDB->rawQuery($checkTable);
                
                if ( ($field_type=='SELECT') or ($field_type=='RADIO') ) {
                    $field_options_array = explode("\n",$field_options);
                    $field_options_count = count($field_options_array);
                    $te=0;
                    while ($te < $field_options_count)
                        {
                        if (preg_match("/,/",$field_options_array[$te]))
                            {
                            $field_options_value_array = explode(",",$field_options_array[$te]);
                            $field_options_ENUM .= str_replace(" ","_",$field_options_value_array[0].",");
                            }
                        $te++;
                       }
                    
                    $field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
                    $fieldcatch = $field_options_ENUM;
                    $field_cost = strlen($field_options_ENUM);
                    if ($field_cost < 1) {$field_cost=1;};
                    $field_sql .= "ENUM($field_options_ENUM) ";
                }
                 
                
                if ( ($field_type=='MULTI') or ($field_type=='CHECKBOX') ){
                    $field_options_array = explode("\n",$field_options);
                    $field_options_count = count($field_options_array);
                    $te=0;
                    while ($te < $field_options_count)
                        {
                        if (preg_match("/,/",$field_options_array[$te]))
                            {
                            $field_options_value_array = explode(",",$field_options_array[$te]);
                            $field_options_ENUM .= str_replace(" ","_",$field_options_value_array[0].",");
                            }
                        $te++;
                       }
                    $field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
                    $field_cost = strlen($field_options_ENUM);
                    if ($field_cost < 1) {$field_cost=1;};
                    $field_sql .= "VARCHAR($field_cost) ";
                }
                
                if ($field_type=='TEXT') {
                    if ($field_max < 1) {$field_max=1;};
                    $field_sql .= "VARCHAR($field_max) ";
                }
                
                if ($field_type=='AREA') {
                    $field_sql .= "TEXT ";
                    $field_cost = 15;
                }
                
                if ($field_type=='DATE') {
                    $field_sql .= "DATE ";
                    $field_cost = 10;
                }
                
                if ($field_type=='TIME') {
                    $field_sql .= "TIME ";
                    $field_cost = 8;
                }
                
                if ( (!empty($field_default) ) and ($field_default != ' ') and ($field_type!='AREA') and ($field_type!='DATE') and ($field_type!='TIME') ){
                    //if($fieldcatch == "") {
                        $field_sql .= "default '$field_default'";
                    //} else {
                    //    $field_sql .= "default $fieldcatch";
                    //}
                }
                
                if ( empty($field_default) ) {
                    $field_sql .="";  
                }
                
                $field_sql .= ";";
                $stmtCUSTOM="$field_sql";
                $rslt = $astDB->rawQuery($stmtCUSTOM);

                $data_update = array(
                    'field_label'       => $field_label,
                    'field_name'        => $field_name,
                    'field_description' => $field_description,
                    'field_rank'        => $field_rank,
                    'field_help'        => (!empty($field_help) ? $field_help : ''),
                    'field_type'        => $field_type,
                    'field_options'     => (!empty($field_options) ? $field_options : ''),
                    'field_size'        => $field_size,
                    'field_max'         => $field_max,
                    'field_default'     => (!empty($field_default) ? $field_default : ''),
                    'field_required'    => $field_required,
                    'field_cost'        => $field_cost,
                    'multi_position'    => $multi_position,
                    'name_position'     => $name_position,
                    'field_order'       => $field_order
                );
                $astDB->where('list_id', $list_to);
                $astDB->where('field_id', $field_id);
                $queryUpdate = $astDB->update('vicidial_lists_fields', $data_update);
                $updateQuery = $astDB->getLastQuery();

                if($queryUpdate){
                    $log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added a New Custom Field $field_label on List ID $list_to", $log_group, $updateQuery);
                   
                    $output[] = "success";
                }else{
                    $output[] = "error";
                }
            }
        }
        
        if(in_array("error", $output)){
            $apiresults = array("result" => "success", "data" => "some fields are detected as duplicate and skipped");
        }else{
            $apiresults = array("result" => "success", "query" => $field_sql);
        }
    }elseif($copy_option == "APPEND"){
        $astDB->where('list_id', $list_from);
        $fromList = $astDB->get('vicidial_lists_fields', null, 'field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order');
        
        $output = array();
        $output1 = array();
        $field_sql ='';
        foreach($fromList as $fresults){
            $field_label       = $fresults['field_label'];
            $field_name        = $fresults['field_name'];
            $field_description = $fresults['field_description'];
            $field_rank        = $fresults['field_rank'];
            $field_help        = $fresults['field_help'];
            $field_type        = $fresults['field_type'];
            $field_options     = $fresults['field_options'];
            $field_size        = $fresults['field_size'];
            $field_max         = $fresults['field_max'];
            $field_default     = $fresults['field_default'];
            $field_cost        = $fresults['field_cost'];
            $field_required    = $fresults['field_required'];
            $multi_position    = $fresults['multi_position'];
            $name_position     = $fresults['name_position'];
            $field_order       = $fresults['field_order'];

            $astDB->where('list_id', $list_to);
            $astDB->where('field_label', $field_label);
            $checkField = $astDB->get('vicidial_lists_fields', null, '*');
    
            if(!$checkField){
                $tableName = "custom_".$list_to;
                $tableCheck="SHOW TABLES LIKE '$tableName'";
                $tableCheckResult = $astDB->rawQuery($tableCheck);
                $tableExist = $astDB->getRowCount();
                
                if ($tableExist < 1) {
                    $field_sql = "CREATE TABLE custom_$list_to (lead_id INT(9) UNSIGNED PRIMARY KEY NOT NULL, $field_label ";      
                }else{
                    $field_sql = "ALTER TABLE custom_$list_to ADD $field_label ";
                }

               
                if ( ($field_type==strtoupper('select')) or ($field_type=='RADIO') ) {
                    $field_options_array = explode("\n",$field_options);
                    $field_options_count = count($field_options_array);
                    $te=0;
                    $field_options_ENUM='';
                    while ($te < $field_options_count)
                        {
                        if (preg_match("/,/",$field_options_array[$te]))
                            {
                            $field_options_value_array = explode(",",$field_options_array[$te]);
                            $field_options_ENUM .= str_replace(" ","_","'".$field_options_value_array[0]."',");
                            }
                        $te++;
                       }
                    
                    $field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
                    $fieldcatch = $field_options_ENUM;
                    $field_cost = strlen($field_options_ENUM);
                    if ($field_cost < 1) {$field_cost=1;};
                    $field_sql .= "ENUM($field_options_ENUM) ";
                }
                 
                
                if ( ($field_type=='MULTI') or ($field_type=='CHECKBOX') ){
                    $field_options_array = explode("\n",$field_options);
                    $field_options_count = count($field_options_array);
                    $te=0;
                    while ($te < $field_options_count)
                        {
                        if (preg_match("/,/",$field_options_array[$te]))
                            {
                            $field_options_value_array = explode(",",$field_options_array[$te]);
                            $field_options_ENUM .= str_replace(" ","_",$field_options_value_array[0].",");
                            }
                        $te++;
                       }
                    $field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
                    $field_cost = strlen($field_options_ENUM);
                    if ($field_cost < 1) {$field_cost=1;};
                    $field_sql .= "VARCHAR($field_cost) ";
                }
                
                if ( ($field_type=='TEXT') or ($field_type=='HIDDEN') ) {
                    if ($field_max < 1) {$field_max=1;};
                    $field_sql .= "VARCHAR($field_max) ";
                }
                
                if ($field_type=='AREA') {
                    $field_sql .= "TEXT ";
                    $field_cost = 15;
                }
                
                if ($field_type=='DATE') {
                    $field_sql .= "DATE ";
                    $field_cost = 10;
                }
                
                if ($field_type=='TIME') {
                    $field_sql .= "TIME ";
                    $field_cost = 8;
                }
                
                if ( (!empty($field_default) ) and ($field_default != ' ') and ($field_type!='AREA') and ($field_type!='DATE') and ($field_type!='TIME') ){
                    //if($fieldcatch == "") {
                        $field_sql .= "default '$field_default'";
                    //} else {
                    //    $field_sql .= "default $fieldcatch";
                    //}
                }
                
                if ( empty($field_default) ) {
                    $field_sql .="";  
                }
                
                if ($tableExist > 0) {
                    $field_sql .= ";";
                } else {
                    $field_sql .= ");";
                }
                $stmtCUSTOM="$field_sql";
                $output1[] = $field_sql;
                
//$fp = fopen('testfile.txt', 'a');
//fwrite($fp, $field_sql);
                
                $rslt = $astDB->rawQuery($stmtCUSTOM);
                // $output[] = mysqli_error($link);

                $data_insert = array(
                    'list_id'           => $list_to,
                    'field_label'       => $field_label,
                    'field_name'        => $field_name,
                    'field_description' => $field_description,
                    'field_rank'        => $field_rank,
                    'field_help'        => (!empty($field_help)) ? $field_help:"",
                    'field_type'        => $field_type,
                    'field_options'     => (!empty($field_options)) ? $field_options:"",
                    'field_size'        => $field_size,
                    'field_max'         => $field_max,
                    'field_default'     => (!empty($field_default)) ? $field_default:"",
                    'field_required'    => (!empty($field_required)) ? $field_required:"N",
                    'field_cost'        => (!empty($field_cost)) ? $field_cost:"0",
                    'multi_position'    => (!empty($multi_position)) ? $multi_position:"HORIZONTAL",
                    'name_position'     => (!empty($name_position)) ? $name_position:"LEFT",
                    'field_order'       => $field_order
                );
                $queryInsert = $astDB->insert('vicidial_lists_fields', $data_insert);
                $insertQuery = $astDB->getLastQuery();

                if($queryInsert){
                    $SQLdate = date("Y-m-d H:i:s");
                    $log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added a New Custom Field $field_label on List ID $list_to", $log_group, $insertQuery);
                   
                    $output[] = "success";
                }else{
                    $output[] = $insertQuery;
                }
            }
        }
        $apiresults = array("result" => "success", "query" => $output, "query1" => $output1);
        // if(in_array("error", $output)){
        //     $apiresults = array("result" => "success", "data" => "some fields are detected as duplicate and skipped");
        // }else{
        //     $apiresults = array("result" => "success", "query" => $field_sql);
        // }
    }elseif($copy_option == "REPLACE"){
        //delete first existing
        $selectTable = "SHOW TABLES LIKE 'custom_$list_to'";
        $queryResult = $astDB->rawQuery($selectTable);
        $countResult = $astDB->getRowCount();
        
        if($queryResult > 0){
            $deleteColumnTable = "ALTER TABLE `custom_$list_to`";
            $queryDelete = $astDB->rawQuery($deleteColumnTable);
            
            $deleteAllColumn = "DELETE FROM vicidial_lists_fields
                            WHERE list_id='$list_to';";
            $query = $astDB->rawQuery($deleteAllColumn);
            //$result = mysqli_num_rows($query);
            
            if($query){
                $astDB->where('list_id', $list_from);
                $fromList = $astDB->get('vicidial_lists_fields', null, 'field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order');
                
                $output = array();
                foreach($fromList as $fresults){
                    $field_label       = $fresults['field_label'];
                    $field_name        = $fresults['field_name'];
                    $field_description = $fresults['field_description'];
                    $field_rank        = $fresults['field_rank'];
                    $field_help        = $fresults['field_help'];
                    $field_type        = $fresults['field_type'];
                    $field_options     = $fresults['field_options'];
                    $field_size        = $fresults['field_size'];
                    $field_max         = $fresults['field_max'];
                    $field_default     = $fresults['field_default'];
                    $field_cost        = $fresults['field_cost'];
                    $field_required    = $fresults['field_required'];
                    $multi_position    = $fresults['multi_position'];
                    $name_position     = $fresults['name_position'];
                    $field_order       = $fresults['field_order'];
                    
                    $counterquery = "SELECT count(*) as countchecking from vicidial_lists_fields where list_id='$list_to' and field_label='$field_label';";
                    $counterresult = $astDB->rawQuery($counterquery);
            
                    if($counterresult){
                        $checkTable = "SHOW TABLES LIKE 'custom_$list_to'";
                        $queryCheckTable = $astDB->rawQuery($checkTable);
                        
                        if($astDB->getRowCount()){
                            $field_sql = "ALTER TABLE custom_$list_to ADD $field_label ";
                        }else{
                            $field_sql = "CREATE TABLE custom_$list_to (lead_id INT(9) UNSIGNED PRIMARY KEY NOT NULL, $field_label ";
                        }
                        
                       
                        if ( ($field_type=='SELECT') or ($field_type=='RADIO') ) {
                            $field_options_array = explode("\n",$field_options);
                            $field_options_count = count($field_options_array);
                            $te=0;
                            while ($te < $field_options_count)
                                {
                                if (preg_match("/,/",$field_options_array[$te]))
                                    {
                                    $field_options_value_array = explode(",",$field_options_array[$te]);
                                    $field_options_ENUM .= str_replace(" ","_",$field_options_value_array[0].",");
                                    }
                                $te++;
                               }
                            
                            $field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
                            $fieldcatch = $field_options_ENUM;
                            $field_cost = strlen($field_options_ENUM);
                            if ($field_cost < 1) {$field_cost=1;};
                            $field_sql .= "ENUM($field_options_ENUM) ";
                        }
                         
                        
                        if ( ($field_type=='MULTI') or ($field_type=='CHECKBOX') ){
                            $field_options_array = explode("\n",$field_options);
                            $field_options_count = count($field_options_array);
                            $te=0;
                            while ($te < $field_options_count)
                                {
                                if (preg_match("/,/",$field_options_array[$te]))
                                    {
                                    $field_options_value_array = explode(",",$field_options_array[$te]);
                                    $field_options_ENUM .= str_replace(" ","_",$field_options_value_array[0].",");
                                    }
                                $te++;
                               }
                            $field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
                            $field_cost = strlen($field_options_ENUM);
                            if ($field_cost < 1) {$field_cost=1;};
                            $field_sql .= "VARCHAR($field_cost) ";
                        }
                        
                        if ($field_type=='TEXT') {
                            if ($field_max < 1) {$field_max=1;};
                            $field_sql .= "VARCHAR($field_max) ";
                        }
                        
                        if ($field_type=='AREA') {
                            $field_sql .= "TEXT ";
                            $field_cost = 15;
                        }
                        
                        if ($field_type=='DATE') {
                            $field_sql .= "DATE ";
                            $field_cost = 10;
                        }
                        
                        if ($field_type=='TIME') {
                            $field_sql .= "TIME ";
                            $field_cost = 8;
                        }
                        
                        if ( (!empty($field_default) ) and ($field_default != ' ') and ($field_type!='AREA') and ($field_type!='DATE') and ($field_type!='TIME') ){
                            //if($fieldcatch == "") {
                                $field_sql .= "default '$field_default'";
                            //} else {
                            //    $field_sql .= "default $fieldcatch";
                            //}
                        }
                        
                        if ( empty($field_default) ) {
                            $field_sql .="";  
                        }
                        
                        if ($astDB->getRowCount()) {
                            $field_sql .= ";";
                        } else {
                            $field_sql .= ");";
                        }
                        $stmtCUSTOM="$field_sql";
                        $rslt = $astDB->rawQuery($stmtCUSTOM);
        
                        $data_insert = array(
                            'field_label'       => $field_label,
                            'field_name'        => $field_name,
                            'field_description' => $field_description,
                            'field_rank'        => $field_rank,
                            'field_help'        => $field_help,
                            'field_type'        => $field_type,
                            'field_options'     => $field_options,
                            'field_size'        => $field_size,
                            'field_max'         => $field_max,
                            'field_default'     => $field_default,
                            'field_required'    => $field_required,
                            'field_cost'        => $field_cost,
                            'multi_position'    => $multi_position,
                            'name_position'     => $name_position,
                            'field_order'       => $field_order
                        );
                        $queryInsert = $astDB->insert('vicidial_lists_fields', $data_insert);
                        $insertQuery = $astDB->getLastQuery();
                        if($queryInsert){
                            $log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added a New Custom Field $field_label on List ID $list_to", $log_group, $insertQuery);
                           
                            $fullData[] = "success";
                        }else{
                            $fullData[] = "error";
                        }
                    }
                }
                
                if(in_array("error", $output)){
                    $apiresults = array("result" => "success", "data" => "some fields are detected as duplicate and skipped");
                }else{
                    $apiresults = array("result" => "success");
                }
            }else{
                $apiresults = array("result" => "Error: Custom Field does not exist");
            }
        }else{
            $apiresults = array("result" => "Error: List does not exist");
        }
    }else{
        $apiresults = array("result" => "Error: Unknown action submitted");
    }

?>
