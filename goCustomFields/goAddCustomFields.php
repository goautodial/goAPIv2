<?php
   #####################################################
   #### Name: goCheckCampaign.php	                ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Noel Umandap                    ####
   #### License: AGPLv2                             ####
   #####################################################
    
    include_once ("../goFunctions.php");
//    error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors', 1);
    $list_id            = mysqli_real_escape_string($link, $_REQUEST['list_id']);
    $field_label        = str_replace(" ","_",trim($_REQUEST['field_label']));
    $field_name         = $_REQUEST['field_name'];
    $field_description  = $_REQUEST['field_description'];
    $field_rank         = $_REQUEST['field_rank'];
    $field_help         = $_REQUEST['field_help'];
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
    $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
    $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
 
    
    $vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|';
    
    if ( (strlen($field_label)<1) or (strlen($field_name)<2) or (strlen($field_size)<1) ) {
        $apiresults = array("result" => "ERROR: You must enter a field label, field name and field size  - ".$list_id." | ".$field_label." | ".$field_name." | ".$field_size);
    }else{
        
        $counterquery = "SELECT count(*) as countchecking from vicidial_lists_fields where list_id='$list_id' and field_label='$field_label';";
        $counterresult = mysqli_query($link, $counterquery);

        if(!$counterresult){
            $apiresults = array("result" => "ERROR: Field already exists for this list - ".$list_id." | ".$field_label);
        }else{
            $tableName = "custom_".$list_id;
            #$tableCheck="SHOW TABLES LIKE '$tableName'";
            $tableCheck="DESC $tableName;";
            $tableCheckResult = mysqli_query($link, $tableCheck);
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
                if($fieldcatch == "") {
                    $field_sql .= "default '$field_default'";
                } else {
                    $field_sql .= "default $fieldcatch";
                }
            }
            
            if ( empty($field_default) ) {
                $field_sql .="";  
            }
            
            if (!$tableCheckResult){
                $field_sql .= ");";
            }else{
                $field_sql .= ";";
            }
            
            //if ( ($field_type=='DISPLAY') or ($field_type=='SCRIPT') or (preg_match("/\|$field_label\|/",$vicidial_list_fields)) ){
            //    //do nothing
            //}else{
            //    if (strlen($copy_option) < 3){
            //        $stmtCUSTOM="$field_sql";
            //        //die($stmtCUSTOM);
            //        $rslt = mysqli_query($link, $stmtCUSTOM);
            //    }
            //}
           
            $stmtCUSTOM="$field_sql";
            $rslt = mysqli_query($link, $stmtCUSTOM);
            
            $insert = "INSERT INTO vicidial_lists_fields
                        set field_label='$field_label',
                            field_name='$field_name',
                            field_description='$field_description',
                            field_rank='$field_rank',
                            field_help='$field_help',
                            field_type='$field_type',
                            field_options='$field_options',
                            field_size='$field_size',
                            field_max='$field_max',
                            field_default='$field_default',
                            field_required='$field_required',
                            field_cost='$field_cost',
                            list_id='$list_id',
                            multi_position='$multi_position',
                            name_position='$name_position',
                            field_order='$field_order';";
            $insertrslt = mysqli_query($link, $insert);
            $countResultInsert = mysqli_num_rows($insertrslt);

            if($insertrslt){
                //$SQLdate = date("Y-m-d H:i:s");
                //$queryLog = "INSERT INTO go_action_logs
                //                (user,ip_address,event_date,action,details,db_query)
                //            values('$goUser','$ip_address','$SQLdate','ADD','Added New Custom Field $field_label on list $list_id','');";
                //$rsltvLog = mysqli_query($linkgo, $queryLog);
                $log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Custom Field $field_label on List ID $list_id", $log_group, $insert);
               
                $apiresults = array("result" => "success", "gg" => $stmtCUSTOM);
            }else{
                $apiresults = array("result" => "Error: Failed to add custom field.");
            }
        }
    }
?>
