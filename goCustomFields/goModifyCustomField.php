<?php
   #####################################################
   #### Name: goModifyCustomField.php               ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Noel Umandap                    ####
   #### License: AGPLv2                             ####
   #####################################################
    
    include_once ("../goFunctions.php");
    
    $list_id            = mysqli_real_escape_string($link, $_REQUEST['list_id']);
    $field_id           = $_REQUEST['field_id'];
    $field_label        = str_replace(" ","_",trim($_REQUEST['field_label']));
    $field_label_old    = str_replace(" ","_",trim($_REQUEST['field_label_old']));
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
    $multi_position     = $_REQUEST['multi_position'];
    $name_position      = $_REQUEST['name_position'];
    $field_order        = $_REQUEST['field_order'];
    
    $vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|';
    
    if($field_label_old != $field_label){
        $field_sql .= "ALTER TABLE custom_$list_id CHANGE $field_label_old $field_label ";
    }else{
        $field_sql .= "ALTER TABLE custom_$list_id MODIFY $field_label ";
    }
    
    $field_options_ENUM='';
	$field_cost=1;
    
    if ( ($field_type=='SELECT') or ($field_type=='RADIO') ) {
        $field_options_array = explode("\n",$field_options);
        $field_options_count = count($field_options_array);
        
        $te=0;
        
        while ($te < $field_options_count) {
            if (preg_match("/,/",$field_options_array[$te]))
                {
                $field_options_value_array = explode(",",$field_options_array[$te]);
                $field_options_ENUM .= str_replace(" ","_","'$field_options_value_array[0]',");
                }
            $te++;
        }
        
        $field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
        
        $field_cost = strlen($field_options_ENUM);
        if ($field_cost < 1) {$field_cost=1;};
        $field_sql .= "ENUM($field_options_ENUM) ";
    }
    
    if ( ($field_type=='MULTI') or ($field_type=='CHECKBOX') ) {
        $field_options_array = explode("\n",$field_options);
        $field_options_count = count($field_options_array);
        $te=0;
        while ($te < $field_options_count) {
            if (preg_match("/,/",$field_options_array[$te]))
                {
                $field_options_value_array = explode(",",$field_options_array[$te]);
                $field_options_ENUM .= str_replace(" ","_","'$field_options_value_array[0]',");
                }
            $te++;
        }
        $field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
        $field_cost = strlen($field_options_ENUM);
        $field_sql .= "VARCHAR($field_cost) ";
    }

    if ($field_type=='TEXT') {
        $field_sql .= "VARCHAR($field_max) ";
    }
    
    if ($field_type=='AREA') {
        $field_sql .= "TEXT ";
    }

    if ($field_type=='DATE') {
        $field_sql .= "DATE ";
    }

    if ($field_type=='TIME') {
        $field_sql .= "TIME ";
    }
    
    if ( (!empty($field_default) ) and ($field_type!='AREA') and ($field_type!='DATE') and ($field_type!='TIME') ){ 
        if(empty($field_default)) {
            $field_sql .=";";
        } else { 
            $field_sql .= "default '$field_default' ";
        }
    }
    
    if ( empty($field_default) ) {
        $field_sql .="";  
    }
    
    //if ( ($field_type=='DISPLAY') or ($field_type=='SCRIPT') or (preg_match("/\|$field_label\|/",$vicidial_list_fields)) ) {
    //    //  do nothing      
    //} else {
    //    $stmtCUSTOM="$field_sql";
    //    $rslt = mysqli_query($link, $stmtCUSTOM);
    //}
    
    $stmtCUSTOM="$field_sql";
    $rslt = mysqli_query($link, $stmtCUSTOM);
    
    $update = "UPDATE vicidial_lists_fields set
                field_label='$field_label',field_name='$field_name',field_description='$field_description',
                field_rank='$field_rank',field_help='$field_help',field_type='$field_type',field_options='$field_options',
                field_size='$field_size',field_max='$field_max',field_default='$field_default',field_required='$field_required',
                field_cost='$field_cost',multi_position='$multi_position',name_position='$name_position',field_order='$field_order'
                where list_id='$list_id' and field_id='$field_id';";
    $updaterslt = mysqli_query($link, $update);
    $countResultUpdate = mysqli_num_rows($updaterslt);
    
    if($updaterslt){
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: List or Field does not exist.");
    }
    
?>
