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
    
    $list_to   = mysqli_real_escape_string($link, $_REQUEST['list_to']);
    $list_from = mysqli_real_escape_string($link, $_REQUEST['list_from']);
    $copy_option = mysqli_real_escape_string($link, $_REQUEST['copy_option']);
    
    $goUser = mysqli_real_escape_string($link, $_REQUEST['goUser']);
    $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
    $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    $vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|';
    
    if($copy_option == "UPDATE"){
        $query = "SELECT
                    field_id,field_label,field_name,field_description,field_rank,field_help,field_type,
                    field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,
                    name_position,field_order
                FROM vicidial_lists_fields
                WHERE list_id='$list_from'
                ORDER BY field_rank,field_order,field_label;";
        $rsltv = mysqli_query($link, $query);
        
        $output = array();
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
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
            //$apiresults = array("result" => $fresults);
            $counterquery = "SELECT count(*) as countchecking from vicidial_lists_fields where list_id='$list_to' and field_label='$field_label';";
            $counterresult = mysqli_query($link, $counterquery);
            
            if($counterresult){
                $checkTable = "SHOW TABLES LIKE 'custom_$list_to'";
                $queryCheckTable = mysqli_query($link, $checkTable);
                
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
                    if($fieldcatch == "") {
                        $field_sql .= "default '$field_default'";
                    } else {
                        $field_sql .= "default $fieldcatch";
                    }
                }
                
                if ( empty($field_default) ) {
                    $field_sql .="";  
                }
                
                $field_sql .= ";";
                $stmtCUSTOM="$field_sql";
                $rslt = mysqli_query($link, $stmtCUSTOM);

                $update = "UPDATE vicidial_lists_fields
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
                                multi_position='$multi_position',
                                name_position='$name_position',
                                field_order='$field_order'
                            where
                                list_id='$list_to'
                            and field_id='$field_id';";
                $updaterslt = mysqli_query($link, $update);
                if($updaterslt){
                    //$SQLdate = date("Y-m-d H:i:s");
                    //$queryLog = "INSERT INTO go_action_logs
                    //                (user,ip_address,event_date,action,details,db_query)
                    //            values('$goUser','$ip_address','$SQLdate','ADD','Added New Custom Field $field_label on list $list_to','');";
                    //$rsltvLog = mysqli_query($linkgo, $queryLog);
                    $log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Custom Field $field_label on List ID $list_to", $log_group, $update);
                   
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
        $query = "SELECT
                    field_id,field_label,field_name,field_description,field_rank,field_help,field_type,
                    field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,
                    name_position,field_order
                FROM vicidial_lists_fields
                WHERE list_id='$list_from'
                ORDER BY field_rank,field_order,field_label;";
        $rsltv = mysqli_query($link, $query);
        
        $output = array();
        $output1 = array();
        $field_sql ='';
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
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
            //$apiresults = array("result" => $fresults);
            $counterquery = "SELECT count(*) as countchecking from vicidial_lists_fields where list_id='$list_to' and field_label='$field_label';";
            $counterresult = mysqli_query($link, $counterquery);
    
            if($counterresult){
                $tableName = "custom_".$list_to;
                $tableCheck="SHOW TABLES LIKE '$tableName'";
                $tableCheckResult = mysqli_query($link, $tableCheck);
                $tableExist = mysqli_num_rows($tableCheckResult);
                
                if ($tableExist < 1) {
                    $field_sql .= "CREATE TABLE custom_$list_to (lead_id INT(9) UNSIGNED PRIMARY KEY NOT NULL, $field_label ";      
                }else{
                    $field_sql .= "ALTER TABLE custom_$list_to ADD $field_label ";
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
                    if($fieldcatch == "") {
                        $field_sql .= "default '$field_default'";
                    } else {
                        $field_sql .= "default $fieldcatch";
                    }
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
                // $output1[] = $field_sql;
                $rslt = mysqli_query($link, $stmtCUSTOM);
                $field_sql = '';
                // $output[] = mysqli_error($link);
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
                                list_id='$list_to',
                                multi_position='$multi_position',
                                name_position='$name_position',
                                field_order='$field_order';";
                $insertrslt = mysqli_query($link, $insert);
                if($insertrslt){
                    $SQLdate = date("Y-m-d H:i:s");
                    //$queryLog = "INSERT INTO go_action_logs
                    //                (user,ip_address,event_date,action,details,db_query)
                    //            values('$goUser','$ip_address','$SQLdate','ADD','Added New Custom Field $field_label on list $list_to','');";
                    //$rsltvLog = mysqli_query($linkgo, $queryLog);
                    $log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Custom Field $field_label on List ID $list_to", $log_group, $insert);
                   
                    $output[] = "success";
                }else{
                    $output[] = "error";
                }
            }
        }
        // $apiresults = array("result" => "success", "query" => $output, "query1" => $output1);
        if(in_array("error", $output)){
            $apiresults = array("result" => "success", "data" => "some fields are detected as duplicate and skipped");
        }else{
            $apiresults = array("result" => "success", "query" => $field_sql);
        }
    }elseif($copy_option == "REPLACE"){
        //delete first existing
        $selectTable = "SHOW TABLES LIKE 'custom_$list_to'";
        $queryResult = mysqli_query($link, $selectTable);
        $countResult = mysqli_num_rows($queryResult);
        
        if($queryResult > 0){
            $deleteColumnTable = "ALTER TABLE `custom_$list_to`";
            $queryDelete = mysqli_query($link, $deleteColumnTable);
            
            $deleteAllColumn = "DELETE FROM vicidial_lists_fields
                            WHERE list_id='$list_to';";
            $query = mysqli_query($link, $deleteAllColumn);
            //$result = mysqli_num_rows($query);
            
            if($query){
                $query = "SELECT
                            field_id,field_label,field_name,field_description,field_rank,field_help,field_type,
                            field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,
                            name_position,field_order
                        FROM vicidial_lists_fields
                        WHERE list_id='$list_from'
                        ORDER BY field_rank,field_order,field_label;";
                $rsltv = mysqli_query($link, $query);
                
                $output = array();
                while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
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
                    $counterresult = mysqli_query($link, $counterquery);
            
                    if($counterresult){
                        $checkTable = "SHOW TABLES LIKE 'custom_$list_to'";
                        $queryCheckTable = mysqli_query($link, $checkTable);
                        
                        if($queryCheckTable->num_rows){
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
                            if($fieldcatch == "") {
                                $field_sql .= "default '$field_default'";
                            } else {
                                $field_sql .= "default $fieldcatch";
                            }
                        }
                        
                        if ( empty($field_default) ) {
                            $field_sql .="";  
                        }
                        
                        if ($queryCheckTable->num_rows) {
                            $field_sql .= ";";
                        } else {
                            $field_sql .= ");";
                        }
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
                                        list_id='$list_to',
                                        multi_position='$multi_position',
                                        name_position='$name_position',
                                        field_order='$field_order';";
                        $insertrslt = mysqli_query($link, $insert);
                        if($insertrslt){
                            //$SQLdate = date("Y-m-d H:i:s");
                            //$queryLog = "INSERT INTO go_action_logs
                            //                (user,ip_address,event_date,action,details,db_query)
                            //            values('$goUser','$ip_address','$SQLdate','ADD','Added New Custom Field $field_label on list $list_to','');";
                            //$rsltvLog = mysqli_query($linkgo, $queryLog);
                            $log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Custom Field $field_label on List ID $list_to", $log_group, $insert);
                           
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
