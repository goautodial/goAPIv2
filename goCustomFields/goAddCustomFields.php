<?php
/**
 * @file        goAddCustomFields.php
 * @brief       API to add new custom field to a list
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

    $list_id            = $astDB->escape($_REQUEST['list_id']);
    $field_label        = str_replace(" ","_",trim($_REQUEST['field_label']));
    $field_name         = $_REQUEST['field_name'];
    $field_description  = $_REQUEST['field_description'];
    $field_rank         = $_REQUEST['field_rank'];
    $field_help         = (isset($_REQUEST['field_help'])) ? $_REQUEST['field_help']:"";
    $field_type         = $_REQUEST['field_type'];
    $field_options      = $_REQUEST['field_options'];
    $field_size         = $_REQUEST['field_size'];
    $field_max          = $_REQUEST['field_max'];
    $field_default      = $_REQUEST['field_default'];
    $field_required     = $_REQUEST['field_required'];
    $multi_position     = $_REQUEST['field_option_position'];
    $name_position      = $_REQUEST['field_position'];
    $field_order        = $_REQUEST['field_order'];
    
    $goUser = $_REQUEST['goUser'];
    $ip_address = $astDB->escape($_REQUEST['hostname']);
    $log_user = $astDB->escape($_REQUEST['log_user']);
    $log_group = $astDB->escape($_REQUEST['log_group']);
 
    
    $vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|';
    
    if ( (strlen($field_label)<1) or (strlen($field_name)<2) or (strlen($field_size)<1) ) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
        //$apiresults = array("result" => "ERROR: You must enter a field label, field name and field size  - ".$list_id." | ".$field_label." | ".$field_name." | ".$field_size);
    }else{
        
        $counterquery = "SELECT * from vicidial_lists_fields where list_id='$list_id' and field_label='$field_label';";
        $counterresult = $astDB->rawQuery($counterquery);
		$field_sql=''; $field_cost='';
        if($astDB->getRowCount() > 0){
            $err_msg = error_handle("41004", "");
            $apiresults = array("code" => "41004", "result" => $err_msg);
            $apiresults = array("result" => "ERROR: Field already exists for this list - ".$list_id." | ".$field_label);
        }else{
            $tableName = "custom_".$list_id;
            #$tableCheck="SHOW TABLES LIKE '$tableName'";
            $tableCheck="DESC $tableName;";
            $tableCheckResult = $astDB->rawQuery($tableCheck);
            //$countTable = mysqli_num_rows($tableCheckResult);
            
/*            if (!$tableCheckResult) {
                $field_sql .= "CREATE TABLE custom_$list_id (lead_id INT(9) UNSIGNED PRIMARY KEY NOT NULL);";	
                $rslt = mysqli_query($link, $field_sql);		
            } */
            
            if (!$tableCheckResult) {
                $field_sql .= "CREATE TABLE custom_$list_id (lead_id INT(9) UNSIGNED PRIMARY KEY NOT NULL, $field_label ";		
            }else{
                $field_sql .= "ALTER TABLE custom_$list_id ADD $field_label ";
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
                        $field_options_ENUM .= "'".str_replace(" ","_",$field_options_value_array[0]."',");
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
                        $field_options_ENUM .= "'".str_replace(" ","_",$field_options_value_array[0]."',");
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
            
            if ( (!empty($field_default) ) and ($field_type!='AREA') and ($field_type!='DATE') and ($field_type!='TIME') ){
                $field_sql .= "DEFAULT '$field_default'";
            }
            
            if ( empty($field_default) ) {
                $field_sql .="";  
            }
            
            if (!$tableCheckResult){
                $field_sql .= ");";
            }else{
                $field_sql .= ";";
            }
            
            if ( ($field_type=='DISPLAY') || ($field_type=='SCRIPT') || (preg_match("/\|$field_label\|/",$vicidial_list_fields)) ){
                //do nothing
            }else{
                $stmtCUSTOM="$field_sql";
                $rslt = $astDB->rawQuery($stmtCUSTOM);
            }

            $data_cf = array(
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
                'list_id'           => $list_id, 
                'multi_position'    => $multi_position, 
                'name_position'     => $name_position, 
                'field_order'       => $field_order
            );
            $insertCF = $astDB->insert('vicidial_lists_fields', $data_cf);
            $insertQuery = $astDB->getLastQuery();
            
            if($astDB->getInsertId()){
                $log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added a New Custom Field $field_label on List ID $list_id", $log_group, $insertQuery);
               
                $apiresults = array("result" => "success");
            }else{
                $apiresults = array("result" => "Error: Failed to add custom field.", "query" => $insertQuery);
            }
        }
    }
?>
