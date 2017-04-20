<?php
####################################################
#### Name: goEditList.php                       ####
#### Description: API to edit specific List     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
#### Written by: Jeremiah Sebastian V. Samatra  ####
#### License: AGPLv2                            ####
####################################################

include_once("../goFunctions.php");

### POST or GET Variables
$list_id = mysqli_real_escape_string($link, $_REQUEST['list_id']);
$list_name = mysqli_real_escape_string($link, $_REQUEST['list_name']);
$list_description = mysqli_real_escape_string($link, $_REQUEST['list_description']);
$campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
$active = mysqli_real_escape_string($link, strtoupper($_REQUEST['active']));
$reset_time = mysqli_real_escape_string($link, $_REQUEST['reset_time']);
$xferconf_a_number = mysqli_real_escape_string($link, $_REQUEST['xferconf_a_number']);
$xferconf_b_number = mysqli_real_escape_string($link, $_REQUEST['xferconf_b_number']);
$xferconf_c_number = mysqli_real_escape_string($link, $_REQUEST['xferconf_c_number']);
$xferconf_d_number = mysqli_real_escape_string($link, $_REQUEST['xferconf_d_number']);
$xferconf_e_number = mysqli_real_escape_string($link, $_REQUEST['xferconf_e_number']);
$agent_script_override = mysqli_real_escape_string($link, $_REQUEST['agent_script_override']);
$drop_inbound_group_override = mysqli_real_escape_string($link, $_REQUEST['drop_inbound_group_override']);
$campaign_cid_override = mysqli_real_escape_string($link, $_REQUEST['campaign_cid_override']);
$web_form_address = mysqli_real_escape_string($link, $_REQUEST['web_form_address']);
$reset_list = mysqli_real_escape_string($link, strtoupper($_REQUEST['reset_list']));
// $values = $_REQUEST['items'];
$ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
$goUser = mysqli_real_escape_string($link, $_REQUEST['goUser']);

$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

### Default values 
$defActive = array("Y","N");

$groupId = go_get_groupid($goUser);

if (!checkIfTenant($groupId)) {
	$ul = "WHERE campaign_id='$campaign_id'";
	$ulList = "WHERE list_id='$list_id'";
} else {
	$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
	$ulList = "WHERE list_id='$list_id' AND user_group='$groupId'";
}
####################################
if($list_id == null) {
	$apiresults = array("result" => "Error: Set a value for List ID.");
} elseif(!in_array($active,$defActive) && $active != null) {
	$apiresults = array("result" => "Error: Default value for active is Y or N only.");
} elseif(!in_array($reset_list,$defActive) && $reset_list != null) {
	$apiresults = array("result" => "Error: Default value for reset_list is Y or N only.");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $list_name)){
	$apiresults = array("result" => "Error: Special characters found in list_name");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $reset_time)) {
	$apiresults = array("result" => "Error: Special characters found in reset_time");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_a_number)) {
	$apiresults = array("result" => "Error: Special characters found in xferconf_a_number");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_b_number)) {
	$apiresults = array("result" => "Error: Special characters found in xferconf_b_number");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_c_number)) {
	$apiresults = array("result" => "Error: Special characters found in xferconf_c_number");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_d_number)) {
	$apiresults = array("result" => "Error: Special characters found in xferconf_d_number");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_e_number)) {
	$apiresults = array("result" => "Error: Special characters found in xferconf_e_number");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $agent_script_override)) {
	$apiresults = array("result" => "Error: Special characters found in agent_script_override");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $drop_inbound_group_override)) {
	$apiresults = array("result" => "Error: Special characters found in drop_inbound_group_override");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $campaign_cid_override)) {
	$apiresults = array("result" => "Error: Special characters found in campaign_cid_override");
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $web_form_address)) {
	$apiresults = array("result" => "Error: Special characters found in web_form_address");
} else {
	$queryreset = "UPDATE vicidial_list set called_since_last_reset='$reset_list' where list_id='$list_id';";
	$rsltvreset = mysqli_query($link, $queryreset);

	$query = "UPDATE vicidial_lists set list_name = '$list_name', list_description = '$list_description', campaign_id = '$campaign_id', active = '$active', reset_time='$reset_time', xferconf_a_number = '$xferconf_a_number', xferconf_b_number = '$xferconf_b_number', xferconf_c_number = '$xferconf_c_number', xferconf_d_number = '$xferconf_d_number', xferconf_e_number = '$xferconf_e_number',  agent_script_override = '$agent_script_override', drop_inbound_group_override = '$drop_inbound_group_override', campaign_cid_override = '$campaign_cid_override', web_form_address = '$web_form_address' WHERE list_id='$list_id';";
	$resultQuery = mysqli_query($link, $query);

	if($resultQuery == false){
		$apiresults = array("result" => "Error: Update failed, check your details.");
	} else {
		$SQLdate = date("Y-m-d H:i:s");

		//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','MODIFY List: $list_id','UPDATE vicidial_lists SET list_id=$list_id,list_name=$list_name,list_description=$list_description,campaign_id=$campaign_id,active=$active,reset_time=$reset_time, xferconf_a_number=$xferconf_a_number,xferconf_b_number=$xferconf_b_number,xferconf_c_number=$xferconf_c_number,xferconf_d_number=$xferconf_d_number,xferconf_e_number=$xferconf_e_number,agent_script_override=$agent_script_override,drop_inbound_group_override=$drop_inbound_group_override,campaign_cid_override=$campaign_cid_override,web_form_address=$web_form_address');";
		//$rsltvLog = mysqli_query($linkgo, $queryLog);
		$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified the List ID: $list_id", $log_group, $query);

		$querydate="UPDATE vicidial_lists SET list_changedate='$SQLdate' WHERE list_id='$listid_data';";
		$resultQueryDate = mysqli_query($link, $querydate);
		
		$queryresetback = "UPDATE vicidial_list set called_since_last_reset='N' where list_id='$list_id';";
		$rsltvresetback = mysqli_query($link, $queryresetback);
		
		$apiresults = array("result" => "success");
	}
}

?>
