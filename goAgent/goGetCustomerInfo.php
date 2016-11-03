<?php
####################################################
#### Name: goGetCustomerInfo.php                ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape($_GET['goLeadID']); }
    else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape($_POST['goLeadID']); }

$system_settings = get_settings('system', $astDB);

if (isset($lead_id) && $lead_id !== '') {
    $astDB->where('lead_id', $lead_id);
    $lead_info = $astDB->getOne('vicidial_list', 'lead_id,list_id,title,first_name,middle_initial,last_name,phone_number,alt_phone,email,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,status,user');
    $leadIDExist = $astDB->getRowCount();
    
    if ($system_settings->custom_fields_enabled > 0) {
        $astDB->where('lead_id', $lead_id);
        $rslt = $astDB->getOne('vicidial_list', 'list_id');
        $list_id = $rslt['list_id'];
        $custom_listid = "custom_{$list_id}";
        
        $astDB->has($custom_listid);
        $lastError = $astDB->getLastError();
        if (strlen($lastError) < 1) {
            $CFields = array();
            $rslt = $astDB->rawQuery("SHOW COLUMNS FROM $custom_listid;");
            foreach ($rslt as $key => $field) {
                if ($field['Field'] == 'lead_id') continue;
                $CFields[] = $field['Field'];
            }
            $CFields = implode(',', $CFields);
            
            $astDB->where('lead_id', $lead_id);
            $custom_info = $astDB->getOne($custom_listid, $CFields);
        }
    }

    if ($leadIDExist > 0) {
        $APIResult = array( "result" => "success", "lead_info" => $lead_info, 'custom_info' => $custom_info );
    } else {
        $APIResult = array( "result" => "error", "message" => "Lead ID '$lead_id' does NOT exist on the database" );
    }
} else {
    $APIResult = array( "result" => "error", "message" => "You did NOT specify a valid Lead ID" );
}
?>