<?php
####################################################
#### Name: goGetLabels.php                      ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

if (isset($_GET['goLabelID'])) { $label_id = $astDB->escape($_GET['goLabelID']); }
    else if (isset($_POST['goLabelID'])) { $label_id = $astDB->escape($_POST['goLabelID']); }
if (isset($_GET['goTableName'])) { $table_name = $astDB->escape($_GET['goTableName']); }
    else if (isset($_POST['goTableName'])) { $table_name = $astDB->escape($_POST['goTableName']); }


if (!preg_match("/system_settings|vicidial_screen_labels/", $table_name)) {
    $APIResult = array( "result" => "error", "message" => "Getting label info from '{$table_name}' NOT allowed." );
} else {
	$astDB->where('campaign_id', $campaign);
	$rslt = $astDB->getOne('vicidial_campaigns', 'disable_alter_custphone');
	$disable_alter_custphone = $rslt['disable_alter_custphone'];
	
	if ($table_name == 'vicidial_screen_labels') {
        $astDB->where('label_id', $label_id);
        $astDB->where('active', 'Y');
    }
    
    $rslt = $astDB->getOne($table_name, 'label_title,label_first_name,label_middle_initial,label_last_name,label_address1,label_address2,label_address3,label_city,label_state,label_province,label_postal_code,label_vendor_lead_code,label_gender,label_phone_number,label_phone_code,label_alt_phone,label_security_phrase,label_email,label_comments');
    
    $APIResult = array( "result" => "success", "data" => array( "disable_alter_custphone" => $disable_alter_custphone, "labels" => $rslt ) );
}
?>