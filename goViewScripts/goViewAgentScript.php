<?php
 /**
 * @file 		goViewAgentScript.php
 * @brief 		API for Viewing Scripts
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jericho James Milo  <james@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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

/** @var MySQLiDB $astDB */
/** @var MySQLiDB $goDB */
/** @var string $log_user */
/** @var string|false $log_group */
/** @var string $log_ip */

if (isset($_GET["lead_id"]))	{$lead_id=$astDB->escape($_GET["lead_id"]);}
	elseif (isset($_POST["lead_id"]))	{$lead_id=$astDB->escape($_POST["lead_id"]);}
	$vendor_id = isset($_GET["vendor_id"]) || isset($_POST["vendor_id"]) ? $_GET["vendor_id"] ?? $_POST["vendor_id"] : "";
	$vendor_lead_code = $vendor_id;

//if (isset($_GET["list_id"]))	{$list_id=$_GET["list_id"];}
//	elseif (isset($_POST["list_id"]))	{$list_id=$_POST["list_id"];}
if (isset($_GET["gmt_offset_now"]))	{$gmt_offset_now=$astDB->escape($_GET["gmt_offset_now"]);}
	elseif (isset($_POST["gmt_offset_now"]))	{$gmt_offset_now=$astDB->escape($_POST["gmt_offset_now"]);}
if (isset($_GET["phone_code"]))	{$phone_code=$astDB->escape($_GET["phone_code"]);}
	elseif (isset($_POST["phone_code"]))	{$phone_code=$astDB->escape($_POST["phone_code"]);}
if (isset($_GET["phone_number"]))	{$phone_number=$astDB->escape($_GET["phone_number"]);}
	elseif (isset($_POST["phone_number"]))	{$phone_number=$astDB->escape($_POST["phone_number"]);}
if (isset($_GET["title"]))	{$title=$astDB->escape($_GET["title"]);}
	elseif (isset($_POST["title"]))	{$title=$astDB->escape($_POST["title"]);}
if (isset($_GET["first_name"]))	{$first_name=$astDB->escape($_GET["first_name"]);}
	elseif (isset($_POST["first_name"]))	{$first_name=$astDB->escape($_POST["first_name"]);}
if (isset($_GET["middle_initial"]))	{$middle_initial=$astDB->escape($_GET["middle_initial"]);}
	elseif (isset($_POST["middle_initial"]))	{$middle_initial=$astDB->escape($_POST["middle_initial"]);}
if (isset($_GET["last_name"]))	{$last_name=$astDB->escape($_GET["last_name"]);}
	elseif (isset($_POST["last_name"]))	{$last_name=$astDB->escape($_POST["last_name"]);}
if (isset($_GET["address1"]))	{$address1=$astDB->escape($_GET["address1"]);}
	elseif (isset($_POST["address1"]))	{$address1=$astDB->escape($_POST["address1"]);}
if (isset($_GET["address2"]))	{$address2=$astDB->escape($_GET["address2"]);}
	elseif (isset($_POST["address2"]))	{$address2=$astDB->escape($_POST["address2"]);}
if (isset($_GET["address3"]))	{$address3=$astDB->escape($_GET["address3"]);}
	elseif (isset($_POST["address3"]))	{$address3=$astDB->escape($_POST["address3"]);}
if (isset($_GET["city"]))	{$city=$astDB->escape($_GET["city"]);}
	elseif (isset($_POST["city"]))	{$city=$astDB->escape($_POST["city"]);}
if (isset($_GET["state"]))	{$state=$astDB->escape($_GET["state"]);}
	elseif (isset($_POST["state"]))	{$state=$astDB->escape($_POST["state"]);}
if (isset($_GET["province"]))	{$province=$astDB->escape($_GET["province"]);}
	elseif (isset($_POST["province"]))	{$province=$astDB->escape($_POST["province"]);}
if (isset($_GET["postal_code"]))	{$postal_code=$astDB->escape($_GET["postal_code"]);}
	elseif (isset($_POST["postal_code"]))	{$postal_code=$astDB->escape($_POST["postal_code"]);}
if (isset($_GET["country_code"]))	{$country_code=$astDB->escape($_GET["country_code"]);}
	elseif (isset($_POST["country_code"]))	{$country_code=$astDB->escape($_POST["country_code"]);}
if (isset($_GET["gender"]))	{$gender=$astDB->escape($_GET["gender"]);}
	elseif (isset($_POST["gender"]))	{$gender=$astDB->escape($_POST["gender"]);}
if (isset($_GET["date_of_birth"]))	{$date_of_birth=$astDB->escape($_GET["date_of_birth"]);}
	elseif (isset($_POST["date_of_birth"]))	{$date_of_birth=$astDB->escape($_POST["date_of_birth"]);}
if (isset($_GET["alt_phone"]))	{$alt_phone=$astDB->escape($_GET["alt_phone"]);}
	elseif (isset($_POST["alt_phone"]))	{$alt_phone=$astDB->escape($_POST["alt_phone"]);}
if (isset($_GET["email"]))	{$email=$astDB->escape($_GET["email"]);}
	elseif (isset($_POST["email"]))	{$email=$astDB->escape($_POST["email"]);}
if (isset($_GET["security_phrase"]))	{$security_phrase=$astDB->escape($_GET["security_phrase"]);}
	elseif (isset($_POST["security_phrase"]))	{$security_phrase=$astDB->escape($_POST["security_phrase"]);}
if (isset($_GET["comments"]))	{$comments=$astDB->escape($_GET["comments"]);}
	elseif (isset($_POST["comments"]))	{$comments=$astDB->escape($_POST["comments"]);}
if (isset($_GET["user"]))	{$user=$astDB->escape($_GET["user"]);}
	elseif (isset($_POST["user"]))	{$user=$astDB->escape($_POST["user"]);}
if (isset($_GET["pass"]))	{$pass=$astDB->escape($_GET["pass"]);}
	elseif (isset($_POST["pass"]))	{$pass=$astDB->escape($_POST["pass"]);}
if (isset($_GET["campaign"]))	{$campaign=$astDB->escape($_GET["campaign"]);}
	elseif (isset($_POST["campaign"]))	{$campaign=$astDB->escape($_POST["campaign"]);}
if (isset($_GET["phone_login"]))	{$phone_login=$astDB->escape($_GET["phone_login"]);}
	elseif (isset($_POST["phone_login"]))	{$phone_login=$astDB->escape($_POST["phone_login"]);}
if (isset($_GET["original_phone_login"]))	{$original_phone_login=$astDB->escape($_GET["original_phone_login"]);}
	elseif (isset($_POST["original_phone_login"]))	{$original_phone_login=$astDB->escape($_POST["original_phone_login"]);}
if (isset($_GET["phone_pass"]))	{$phone_pass=$astDB->escape($_GET["phone_pass"]);}
	elseif (isset($_POST["phone_pass"]))	{$phone_pass=$astDB->escape($_POST["phone_pass"]);}
if (isset($_GET["fronter"]))	{$fronter=$astDB->escape($_GET["fronter"]);}
	elseif (isset($_POST["fronter"]))	{$fronter=$astDB->escape($_POST["fronter"]);}
if (isset($_GET["closer"]))	{$closer=$astDB->escape($_GET["closer"]);}
	elseif (isset($_POST["closer"]))	{$closer=$astDB->escape($_POST["closer"]);}
if (isset($_GET["group"]))	{$group=$astDB->escape($_GET["group"]);}
	elseif (isset($_POST["group"]))	{$group=$astDB->escape($_POST["group"]);}
if (isset($_GET["channel_group"]))	{$channel_group=$astDB->escape($_GET["channel_group"]);}
	elseif (isset($_POST["channel_group"]))	{$channel_group=$astDB->escape($_POST["channel_group"]);}
if (isset($_GET["SQLdate"]))	{$SQLdate=$astDB->escape($_GET["SQLdate"]);}
	elseif (isset($_POST["SQLdate"]))	{$SQLdate=$astDB->escape($_POST["SQLdate"]);}
if (isset($_GET["epoch"]))	{$epoch=$astDB->escape($_GET["epoch"]);}
	elseif (isset($_POST["epoch"]))	{$epoch=$astDB->escape($_POST["epoch"]);}
if (isset($_GET["uniqueid"]))	{$uniqueid=$astDB->escape($_GET["uniqueid"]);}
	elseif (isset($_POST["uniqueid"]))	{$uniqueid=$astDB->escape($_POST["uniqueid"]);}
if (isset($_GET["customer_zap_channel"]))	{$customer_zap_channel=$astDB->escape($_GET["customer_zap_channel"]);}
	elseif (isset($_POST["customer_zap_channel"]))	{$customer_zap_channel=$astDB->escape($_POST["customer_zap_channel"]);}
if (isset($_GET["customer_server_ip"]))	{$customer_server_ip=$astDB->escape($_GET["customer_server_ip"]);}
	elseif (isset($_POST["customer_server_ip"]))	{$customer_server_ip=$astDB->escape($_POST["customer_server_ip"]);}
if (isset($_GET["server_ip"]))	{$server_ip=$astDB->escape($_GET["server_ip"]);}
	elseif (isset($_POST["server_ip"]))	{$server_ip=$astDB->escape($_POST["server_ip"]);}
if (isset($_GET["SIPexten"]))	{$SIPexten=$astDB->escape($_GET["SIPexten"]);}
	elseif (isset($_POST["SIPexten"]))	{$SIPexten=$astDB->escape($_POST["SIPexten"]);}
if (isset($_GET["session_id"]))	{$session_id=$astDB->escape($_GET["session_id"]);}
	elseif (isset($_POST["session_id"]))	{$session_id=$astDB->escape($_POST["session_id"]);}
if (isset($_GET["phone"]))	{$phone=$astDB->escape($_GET["phone"]);}
	elseif (isset($_POST["phone"]))	{$phone=$astDB->escape($_POST["phone"]);}
if (isset($_GET["parked_by"]))	{$parked_by=$astDB->escape($_GET["parked_by"]);}
	elseif (isset($_POST["parked_by"]))	{$parked_by=$astDB->escape($_POST["parked_by"]);}
if (isset($_GET["dispo"]))	{$dispo=$astDB->escape($_GET["dispo"]);}
	elseif (isset($_POST["dispo"]))	{$dispo=$astDB->escape($_POST["dispo"]);}
if (isset($_GET["dialed_number"]))	{$dialed_number=$astDB->escape($_GET["dialed_number"]);}
	elseif (isset($_POST["dialed_number"]))	{$dialed_number=$astDB->escape($_POST["dialed_number"]);}
if (isset($_GET["dialed_label"]))	{$dialed_label=$astDB->escape($_GET["dialed_label"]);}
	elseif (isset($_POST["dialed_label"]))	{$dialed_label=$astDB->escape($_POST["dialed_label"]);}
if (isset($_GET["source_id"]))	{$source_id=$astDB->escape($_GET["source_id"]);}
	elseif (isset($_POST["source_id"]))	{$source_id=$astDB->escape($_POST["source_id"]);}
if (isset($_GET["rank"]))	{$rank=$astDB->escape($_GET["rank"]);}
	elseif (isset($_POST["rank"]))	{$rank=$astDB->escape($_POST["rank"]);}
if (isset($_GET["owner"]))	{$owner=$astDB->escape($_GET["owner"]);}
	elseif (isset($_POST["owner"]))	{$owner=$astDB->escape($_POST["owner"]);}
if (isset($_GET["camp_script"]))	{$camp_script=$astDB->escape($_GET["camp_script"]);}
	elseif (isset($_POST["camp_script"]))	{$camp_script=$astDB->escape($_POST["camp_script"]);}
if (isset($_GET["in_script"]))	{$in_script=$astDB->escape($_GET["in_script"]);}
	elseif (isset($_POST["in_script"]))	{$in_script=$astDB->escape($_POST["in_script"]);}
if (isset($_GET["script_width"]))	{$script_width=$astDB->escape($_GET["script_width"]);}
	elseif (isset($_POST["script_width"]))	{$script_width=$astDB->escape($_POST["script_width"]);}
if (isset($_GET["script_height"]))	{$script_height=$astDB->escape($_GET["script_height"]);}
	elseif (isset($_POST["script_height"]))	{$script_height=$astDB->escape($_POST["script_height"]);}
if (isset($_GET["fullname"]))	{$fullname=$astDB->escape($_GET["fullname"]);}
	elseif (isset($_POST["fullname"]))	{$fullname=$astDB->escape($_POST["fullname"]);}
if (isset($_GET["recording_filename"]))	{$recording_filename=$astDB->escape($_GET["recording_filename"]);}
	elseif (isset($_POST["recording_filename"]))	{$recording_filename=$astDB->escape($_POST["recording_filename"]);}
if (isset($_GET["recording_id"]))	{$recording_id=$astDB->escape($_GET["recording_id"]);}
	elseif (isset($_POST["recording_id"]))	{$recording_id=$astDB->escape($_POST["recording_id"]);}
if (isset($_GET["user_custom_one"]))	{$user_custom_one=$astDB->escape($_GET["user_custom_one"]);}
	elseif (isset($_POST["user_custom_one"]))	{$user_custom_one=$astDB->escape($_POST["user_custom_one"]);}
if (isset($_GET["user_custom_two"]))	{$user_custom_two=$astDB->escape($_GET["user_custom_two"]);}
	elseif (isset($_POST["user_custom_two"]))	{$user_custom_two=$astDB->escape($_POST["user_custom_two"]);}
if (isset($_GET["user_custom_three"]))	{$user_custom_three=$astDB->escape($_GET["user_custom_three"]);}
	elseif (isset($_POST["user_custom_three"]))	{$user_custom_three=$astDB->escape($_POST["user_custom_three"]);}
if (isset($_GET["user_custom_four"]))	{$user_custom_four=$astDB->escape($_GET["user_custom_four"]);}
	elseif (isset($_POST["user_custom_four"]))	{$user_custom_four=$astDB->escape($_POST["user_custom_four"]);}
if (isset($_GET["user_custom_five"]))	{$user_custom_five=$astDB->escape($_GET["user_custom_five"]);}
	elseif (isset($_POST["user_custom_five"]))	{$user_custom_five=$astDB->escape($_POST["user_custom_five"]);}
if (isset($_GET["preset_number_a"]))	{$preset_number_a=$astDB->escape($_GET["preset_number_a"]);}
	elseif (isset($_POST["preset_number_a"]))	{$preset_number_a=$astDB->escape($_POST["preset_number_a"]);}
if (isset($_GET["preset_number_b"]))	{$preset_number_b=$astDB->escape($_GET["preset_number_b"]);}
	elseif (isset($_POST["preset_number_b"]))	{$preset_number_b=$astDB->escape($_POST["preset_number_b"]);}
if (isset($_GET["preset_number_c"]))	{$preset_number_c=$astDB->escape($_GET["preset_number_c"]);}
	elseif (isset($_POST["preset_number_c"]))	{$preset_number_c=$astDB->escape($_POST["preset_number_c"]);}
if (isset($_GET["preset_number_d"]))	{$preset_number_d=$astDB->escape($_GET["preset_number_d"]);}
	elseif (isset($_POST["preset_number_d"]))	{$preset_number_d=$astDB->escape($_POST["preset_number_d"]);}
if (isset($_GET["preset_number_e"]))	{$preset_number_e=$astDB->escape($_GET["preset_number_e"]);}
	elseif (isset($_POST["preset_number_e"]))	{$preset_number_e=$astDB->escape($_POST["preset_number_e"]);}
if (isset($_GET["preset_number_f"]))	{$preset_number_f=$astDB->escape($_GET["preset_number_f"]);}
	elseif (isset($_POST["preset_number_f"]))	{$preset_number_f=$astDB->escape($_POST["preset_number_f"]);}
if (isset($_GET["preset_dtmf_a"]))	{$preset_dtmf_a=$astDB->escape($_GET["preset_dtmf_a"]);}
	elseif (isset($_POST["preset_dtmf_a"]))	{$preset_dtmf_a=$astDB->escape($_POST["preset_dtmf_a"]);}
if (isset($_GET["preset_dtmf_b"]))	{$preset_dtmf_b=$astDB->escape($_GET["preset_dtmf_b"]);}
	elseif (isset($_POST["preset_dtmf_b"]))	{$preset_dtmf_b=$astDB->escape($_POST["preset_dtmf_b"]);}
if (isset($_GET["did_id"]))				{$did_id=$astDB->escape($_GET["did_id"]);}
	elseif (isset($_POST["did_id"]))	{$did_id=$astDB->escape($_POST["did_id"]);}
if (isset($_GET["did_extension"]))			{$did_extension=$astDB->escape($_GET["did_extension"]);}
	elseif (isset($_POST["did_extension"]))	{$did_extension=$astDB->escape($_POST["did_extension"]);}
if (isset($_GET["did_pattern"]))			{$did_pattern=$astDB->escape($_GET["did_pattern"]);}
	elseif (isset($_POST["did_pattern"]))	{$did_pattern=$astDB->escape($_POST["did_pattern"]);}
if (isset($_GET["did_description"]))			{$did_description=$astDB->escape($_GET["did_description"]);}
	elseif (isset($_POST["did_description"]))	{$did_description=$astDB->escape($_POST["did_description"]);}
if (isset($_GET["closecallid"]))			{$closecallid=$astDB->escape($_GET["closecallid"]);}
	elseif (isset($_POST["closecallid"]))	{$closecallid=$astDB->escape($_POST["closecallid"]);}
if (isset($_GET["xfercallid"]))				{$xfercallid=$astDB->escape($_GET["xfercallid"]);}
	elseif (isset($_POST["xfercallid"]))	{$xfercallid=$astDB->escape($_POST["xfercallid"]);}
if (isset($_GET["agent_log_id"]))			{$agent_log_id=$astDB->escape($_GET["agent_log_id"]);}
	elseif (isset($_POST["agent_log_id"]))	{$agent_log_id=$astDB->escape($_POST["agent_log_id"]);}
if (isset($_GET["ScrollDIV"]))			{$ScrollDIV=$astDB->escape($_GET["ScrollDIV"]);}
	elseif (isset($_POST["ScrollDIV"]))	{$ScrollDIV=$astDB->escape($_POST["ScrollDIV"]);}
if (isset($_GET["ignore_list_script"]))				{$ignore_list_script=$astDB->escape($_GET["ignore_list_script"]);}
	elseif (isset($_POST["ignore_list_script"]))	{$ignore_list_script=$astDB->escape($_POST["ignore_list_script"]);}
if (isset($_GET["CF_uses_custom_fields"]))			{$CF_uses_custom_fields=$astDB->escape($_GET["CF_uses_custom_fields"]);}
	elseif (isset($_POST["CF_uses_custom_fields"]))	{$CF_uses_custom_fields=$astDB->escape($_POST["CF_uses_custom_fields"]);}
if (isset($_GET["entry_list_id"]))			{$entry_list_id=$astDB->escape($_GET["entry_list_id"]);}
	elseif (isset($_POST["entry_list_id"]))	{$entry_list_id=$astDB->escape($_POST["entry_list_id"]);}
if (isset($_GET["call_id"]))			{$call_id=$astDB->escape($_GET["call_id"]);}
	elseif (isset($_POST["call_id"]))	{$call_id=$astDB->escape($_POST["call_id"]);}
if (isset($_GET["user_group"]))				{$user_group=$astDB->escape($_GET["user_group"]);}
	elseif (isset($_POST["user_group"]))	{$user_group=$astDB->escape($_POST["user_group"]);}
if (isset($_GET["web_vars"]))			{$web_vars=$astDB->escape($_GET["web_vars"]);}
	elseif (isset($_POST["web_vars"]))	{$web_vars=$astDB->escape($_POST["web_vars"]);}

$goAgentScriptVars = [
	'lead_id', 'vendor_id', 'vendor_lead_code', 'gmt_offset_now', 'phone_code', 'phone_number', 'title', 'first_name', 'middle_initial', 'last_name',
	'address1', 'address2', 'address3', 'city', 'state', 'province', 'postal_code', 'country_code', 'gender', 'date_of_birth',
	'alt_phone', 'email', 'security_phrase', 'comments', 'user', 'pass', 'campaign', 'phone_login', 'original_phone_login', 'phone_pass',
	'fronter', 'closer', 'group', 'channel_group', 'SQLdate', 'epoch', 'uniqueid', 'customer_zap_channel', 'customer_server_ip', 'server_ip',
	'SIPexten', 'session_id', 'phone', 'parked_by', 'dispo', 'dialed_number', 'dialed_label', 'source_id', 'rank', 'owner',
	'camp_script', 'in_script', 'script_width', 'script_height', 'fullname', 'recording_filename', 'recording_id', 'user_custom_one',
	'user_custom_two', 'user_custom_three', 'user_custom_four', 'user_custom_five', 'preset_number_a', 'preset_number_b', 'preset_number_c',
	'preset_number_d', 'preset_number_e', 'preset_number_f', 'preset_dtmf_a', 'preset_dtmf_b', 'did_id', 'did_extension', 'did_pattern',
	'did_description', 'closecallid', 'xfercallid', 'agent_log_id', 'ScrollDIV', 'ignore_list_script', 'CF_uses_custom_fields', 'entry_list_id',
	'call_id', 'user_group', 'web_vars', 'length_in_sec', 'format', 'ACTION', 'query_date', 'list_id', 'list_name', 'list_description',
	'script_name', 'script_text', 'call_script', 'form_field_value'
];
foreach ($goAgentScriptVars as $goAgentScriptVar) {
	if (!isset($$goAgentScriptVar)) {
		$$goAgentScriptVar = '';
	}
}
$goAgentScripts = '';
$non_latin = 0;
$timeclock_end_of_day = '';
$agentonly_callback_campaign_lock = '';
$ScrollDIV = is_numeric($ScrollDIV) ? (int) $ScrollDIV : 0;
$ignore_list_script = is_numeric($ignore_list_script) ? (int) $ignore_list_script : 0;
$script_height = is_numeric($script_height) ? (int) $script_height : 0;
$CF_uses_custom_fields = (string) $CF_uses_custom_fields;

header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

$txt = '.txt';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$CIDdate = date("mdHis");
$ENTRYdate = date("YmdHis");
$MT = [''];
$agents='@agents';
$script_height -= 20;

$IFRAME=0;

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
//$stmt = "SELECT use_non_latin,timeclock_end_of_day,agentonly_callback_campaign_lock FROM system_settings;";
$rslt = $astDB->getOne('system_settings', 'use_non_latin,timeclock_end_of_day,agentonly_callback_campaign_lock');
$qm_conf_ct = $astDB->getRowCount();
if ($qm_conf_ct > 0 && is_array($rslt)) {
	$non_latin =							$rslt['use_non_latin'] ?? 0;
	$timeclock_end_of_day =					$rslt['timeclock_end_of_day'] ?? '';
	$agentonly_callback_campaign_lock =		$rslt['agentonly_callback_campaign_lock'] ?? '';
}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1) {
	$user = preg_replace("/[^-_0-9a-zA-Z]/","",(string) $user);
	$pass = preg_replace("/[^\.-_0-9a-zA-Z]/","",(string) $pass);
	$length_in_sec = preg_replace("/[^0-9]/","",(string) $length_in_sec);
	$phone_code = preg_replace("/[^0-9]/","",(string) $phone_code);
	$phone_number = preg_replace("/[^0-9]/","",(string) $phone_number);
} else {
	$user = preg_replace("/\'|\"|\\\\|;/","",(string) $user);
	$pass = preg_replace("/\'|\"|\\\\|;/","",(string) $pass);
}


# default optional vars if not set
if (!isset($format))   {$format="text";}
	if ($format == 'debug')	{$DB=1;}
if (!isset($ACTION))   {$ACTION="refresh";}
if (!isset($query_date)) {$query_date = $NOW_DATE;}

//$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
//if ($DB) {echo "|$stmt|\n";}
//if ($non_latin > 0) {$rslt=mysqli_query($link, "SET NAMES 'UTF8'");}
//$rslt=mysqli_query($link, $stmt);
//$row=mysqli_fetch_row($rslt);
//$auth=$row[0];

/*
$auth=0;
$auth_message = user_authorization($user,$pass,'',0,1,0);
if ($auth_message == 'GOOD')
    {$auth=1;}

if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0))
	{
	echo "Invalid Username/Password: |$user|$pass|$auth_message|\n";
	exit;
	}
else
	{
	# do nothing for now
	}
*/

if ($format=='debug')
	{
	/*echo "<html>\n";
	echo "<head>\n";
	echo "<!-- USER: $user   server_ip: $server_ip-->\n";
	echo "<title>Display Script";
	echo "</title>\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";*/
	}

if (strlen((string) $in_script) < 1)
	{$call_script = $camp_script;}
else
	{$call_script = $in_script;}

$ignore_list_script_override = 'N';
//$stmt = "SELECT ignore_list_script_override FROM vicidial_inbound_groups where group_id='$group';";
	$astDB->where('group_id', $group);
	$rslt = $astDB->getOne('vicidial_inbound_groups', 'ignore_list_script_override');
	$ilso_ct = $astDB->getRowCount();
	if ($ilso_ct > 0 && is_array($rslt)) {
		$ignore_list_script_override =		$rslt['ignore_list_script_override'] ?? 'N';
	}
if ($ignore_list_script_override == 'Y')
	{$ignore_list_script = 1;}

if ($ignore_list_script < 1) {
//milo
//get list_id from lead_id
	//$queryGetlist_id = "SELECT list_id FROM vicidial_list WHERE lead_id='$lead_id';";
    $astDB->where('lead_id', $lead_id);
	$qrslt = $astDB->getOne('vicidial_list', 'list_id');
	$list_id = is_array($qrslt) ? ($qrslt['list_id'] ?? $list_id) : $list_id;

	//$stmt="SELECT agent_script_override from vicidial_lists where list_id='$list_id';";
    $astDB->where('list_id', $list_id);
	$rslt = $astDB->getOne('vicidial_lists', 'agent_script_override');
	$agent_script_override =		is_array($rslt) ? ($rslt['agent_script_override'] ?? '') : '';
	if ((string) $agent_script_override !== '')
		{$call_script = $agent_script_override;}
}

//milo
//get script id
//$queryGetscript = "SELECT vc.campaign_script FROM vicidial_campaigns vc, vicidial_lists vl WHERE vc.campaign_id = vl.campaign_id AND vl.list_id='$list_id';";
$astDB->where('vl.list_id', $list_id);
$astDB->join('vicidial_campaigns vc', 'vc.campaign_id=vl.campaign_id', 'LEFT');
$gsrslt = $astDB->getOne('vicidial_lists vl', 'vc.campaign_script');
$campaign_script = is_array($gsrslt) ? ($gsrslt['campaign_script'] ?? '') : '';
if ((string) $campaign_script !== '') {
	$call_script = $campaign_script;
}


//$stmt="SELECT list_name,list_description from vicidial_lists where list_id='$list_id';";
$astDB->where('list_id', $list_id);
$rslt = $astDB->getOne('vicidial_lists', 'list_name,list_description');
$list_name =			is_array($rslt) ? ($rslt['list_name'] ?? '') : '';
$list_description =		is_array($rslt) ? ($rslt['list_description'] ?? '') : '';

//$stmt="SELECT script_name,script_text from vicidial_scripts where script_id='$call_script';";
$astDB->where('script_id', $call_script);
$rslt = $astDB->getOne('vicidial_scripts', 'script_name,script_text');
$script_name =		is_array($rslt) ? ($rslt['script_name'] ?? '') : '';
$script_text =		is_array($rslt) ? stripslashes((string) ($rslt['script_text'] ?? '')) : '';

if (preg_match("/iframe\ssrc/i",$script_text)) {
	$IFRAME = 1;
	$lead_id = preg_replace('/\s/i','+',(string) $lead_id);
	$vendor_id = preg_replace('/\s/i','+',(string) $vendor_id);
	$vendor_lead_code = preg_replace('/\s/i','+',(string) $vendor_lead_code);
	$list_id = preg_replace('/\s/i','+',(string) $list_id);
	$list_name = preg_replace('/\s/i','+',(string) $list_name);
	$list_description = preg_replace('/\s/i','+',(string) $list_description);
	$gmt_offset_now = preg_replace('/\s/i','+',(string) $gmt_offset_now);
	$phone_code = preg_replace('/\s/i','+',(string) $phone_code);
	$phone_number = preg_replace('/\s/i','+',(string) $phone_number);
	$title = preg_replace('/\s/i','+',(string) $title);
	$first_name = preg_replace('/\s/i','+',(string) $first_name);
	$middle_initial = preg_replace('/\s/i','+',(string) $middle_initial);
	$last_name = preg_replace('/\s/i','+',(string) $last_name);
	$address1 = preg_replace('/\s/i','+',(string) $address1);
	$address2 = preg_replace('/\s/i','+',(string) $address2);
	$address3 = preg_replace('/\s/i','+',(string) $address3);
	$city = preg_replace('/\s/i','+',(string) $city);
	$state = preg_replace('/\s/i','+',(string) $state);
	$province = preg_replace('/\s/i','+',(string) $province);
	$postal_code = preg_replace('/\s/i','+',(string) $postal_code);
	$country_code = preg_replace('/\s/i','+',(string) $country_code);
	$gender = preg_replace('/\s/i','+',(string) $gender);
	$date_of_birth = preg_replace('/\s/i','+',(string) $date_of_birth);
	$alt_phone = preg_replace('/\s/i','+',(string) $alt_phone);
	$email = preg_replace('/\s/i','+',(string) $email);
	$security_phrase = preg_replace('/\s/i','+',(string) $security_phrase);
	$comments = preg_replace('/\s/i','+',(string) $comments);
	$user = preg_replace('/\s/i','+',(string) $user);
	$pass = preg_replace('/\s/i','+',(string) $pass);
	$campaign = preg_replace('/\s/i','+',(string) $campaign);
	$phone_login = preg_replace('/\s/i','+',(string) $phone_login);
	$original_phone_login = preg_replace('/\s/i','+',(string) $original_phone_login);
	$phone_pass = preg_replace('/\s/i','+',(string) $phone_pass);
	$fronter = preg_replace('/\s/i','+',(string) $fronter);
	$closer = preg_replace('/\s/i','+',(string) $closer);
	$group = preg_replace('/\s/i','+',(string) $group);
	$channel_group = preg_replace('/\s/i','+',(string) $channel_group);
	$SQLdate = preg_replace('/\s/i','+',(string) $SQLdate);
	$epoch = preg_replace('/\s/i','+',(string) $epoch);
	$uniqueid = preg_replace('/\s/i','+',(string) $uniqueid);
	$customer_zap_channel = preg_replace('/\s/i','+',(string) $customer_zap_channel);
	$customer_server_ip = preg_replace('/\s/i','+',(string) $customer_server_ip);
	$server_ip = preg_replace('/\s/i','+',(string) $server_ip);
	$SIPexten = preg_replace('/\s/i','+',(string) $SIPexten);
	$session_id = preg_replace('/\s/i','+',(string) $session_id);
	$phone = preg_replace('/\s/i','+',(string) $phone);
	$parked_by = preg_replace('/\s/i','+',(string) $parked_by);
	$dispo = preg_replace('/\s/i','+',(string) $dispo);
	$dialed_number = preg_replace('/\s/i','+',(string) $dialed_number);
	$dialed_label = preg_replace('/\s/i','+',(string) $dialed_label);
	$source_id = preg_replace('/\s/i','+',(string) $source_id);
	$rank = preg_replace('/\s/i','+',(string) $rank);
	$owner = preg_replace('/\s/i','+',(string) $owner);
	$camp_script = preg_replace('/\s/i','+',(string) $camp_script);
	$in_script = preg_replace('/\s/i','+',(string) $in_script);
	$script_width = preg_replace('/\s/i','+',(string) $script_width);
	$script_height = preg_replace('/\s/i','+',(string) $script_height);
	$fullname = preg_replace('/\s/i','+',(string) $fullname);
	$recording_filename = preg_replace('/\s/i','+',(string) $recording_filename);
	$recording_id = preg_replace('/\s/i','+',(string) $recording_id);
	$user_custom_one = preg_replace('/\s/i','+',(string) $user_custom_one);
	$user_custom_two = preg_replace('/\s/i','+',(string) $user_custom_two);
	$user_custom_three = preg_replace('/\s/i','+',(string) $user_custom_three);
	$user_custom_four = preg_replace('/\s/i','+',(string) $user_custom_four);
	$user_custom_five = preg_replace('/\s/i','+',(string) $user_custom_five);
	$preset_number_a = preg_replace('/\s/i','+',(string) $preset_number_a);
	$preset_number_b = preg_replace('/\s/i','+',(string) $preset_number_b);
	$preset_number_c = preg_replace('/\s/i','+',(string) $preset_number_c);
	$preset_number_d = preg_replace('/\s/i','+',(string) $preset_number_d);
	$preset_number_e = preg_replace('/\s/i','+',(string) $preset_number_e);
	$preset_number_f = preg_replace('/\s/i','+',(string) $preset_number_f);
	$preset_dtmf_a = preg_replace('/\s/i','+',(string) $preset_dtmf_a);
	$preset_dtmf_b = preg_replace('/\s/i','+',(string) $preset_dtmf_b);
	$did_id = preg_replace('/\s/i','+',(string) $did_id);
	$did_extension = preg_replace('/\s/i','+',(string) $did_extension);
	$did_pattern = preg_replace('/\s/i','+',(string) $did_pattern);
	$did_description = preg_replace('/\s/i','+',(string) $did_description);
	$web_vars = preg_replace('/\s/i','+',(string) $web_vars);
}

$script_text = preg_replace('/--A--lead_id--B--/i',"$lead_id",$script_text);
$script_text = preg_replace('/--A--vendor_id--B--/i',"$vendor_id",(string) $script_text);
$script_text = preg_replace('/--A--vendor_lead_code--B--/i',"$vendor_lead_code",(string) $script_text);
$script_text = preg_replace('/--A--list_id--B--/i',"$list_id",(string) $script_text);
$script_text = preg_replace('/--A--list_name--B--/i',"$list_name",(string) $script_text);
$script_text = preg_replace('/--A--list_description--B--/i',"$list_description",(string) $script_text);
$script_text = preg_replace('/--A--gmt_offset_now--B--/i',"$gmt_offset_now",(string) $script_text);
$script_text = preg_replace('/--A--phone_code--B--/i',"$phone_code",(string) $script_text);
$script_text = preg_replace('/--A--phone_number--B--/i',"$phone_number",(string) $script_text);
$script_text = preg_replace('/--A--title--B--/i',"$title",(string) $script_text);
$script_text = preg_replace('/--A--first_name--B--/i',"$first_name",(string) $script_text);
$script_text = preg_replace('/--A--middle_initial--B--/i',"$middle_initial",(string) $script_text);
$script_text = preg_replace('/--A--last_name--B--/i',"$last_name",(string) $script_text);
$script_text = preg_replace('/--A--address1--B--/i',"$address1",(string) $script_text);
$script_text = preg_replace('/--A--address2--B--/i',"$address2",(string) $script_text);
$script_text = preg_replace('/--A--address3--B--/i',"$address3",(string) $script_text);
$script_text = preg_replace('/--A--city--B--/i',"$city",(string) $script_text);
$script_text = preg_replace('/--A--state--B--/i',"$state",(string) $script_text);
$script_text = preg_replace('/--A--province--B--/i',"$province",(string) $script_text);
$script_text = preg_replace('/--A--postal_code--B--/i',"$postal_code",(string) $script_text);
$script_text = preg_replace('/--A--country_code--B--/i',"$country_code",(string) $script_text);
$script_text = preg_replace('/--A--gender--B--/i',"$gender",(string) $script_text);
$script_text = preg_replace('/--A--date_of_birth--B--/i',"$date_of_birth",(string) $script_text);
$script_text = preg_replace('/--A--alt_phone--B--/i',"$alt_phone",(string) $script_text);
$script_text = preg_replace('/--A--email--B--/i',"$email",(string) $script_text);
$script_text = preg_replace('/--A--security_phrase--B--/i',"$security_phrase",(string) $script_text);
$script_text = preg_replace('/--A--comments--B--/i',"$comments",(string) $script_text);
$script_text = preg_replace('/--A--user--B--/i',"$user",(string) $script_text);
$script_text = preg_replace('/--A--pass--B--/i',"$pass",(string) $script_text);
$script_text = preg_replace('/--A--campaign--B--/i',"$campaign",(string) $script_text);
$script_text = preg_replace('/--A--phone_login--B--/i',"$phone_login",(string) $script_text);
$script_text = preg_replace('/--A--original_phone_login--B--/i',"$original_phone_login",(string) $script_text);
$script_text = preg_replace('/--A--phone_pass--B--/i',"$phone_pass",(string) $script_text);
$script_text = preg_replace('/--A--fronter--B--/i',"$fronter",(string) $script_text);
$script_text = preg_replace('/--A--closer--B--/i',"$closer",(string) $script_text);
$script_text = preg_replace('/--A--group--B--/i',"$group",(string) $script_text);
$script_text = preg_replace('/--A--channel_group--B--/i',"$channel_group",(string) $script_text);
$script_text = preg_replace('/--A--SQLdate--B--/i',"$SQLdate",(string) $script_text);
$script_text = preg_replace('/--A--epoch--B--/i',"$epoch",(string) $script_text);
$script_text = preg_replace('/--A--uniqueid--B--/i',"$uniqueid",(string) $script_text);
$script_text = preg_replace('/--A--customer_zap_channel--B--/i',"$customer_zap_channel",(string) $script_text);
$script_text = preg_replace('/--A--customer_server_ip--B--/i',"$customer_server_ip",(string) $script_text);
$script_text = preg_replace('/--A--server_ip--B--/i',"$server_ip",(string) $script_text);
$script_text = preg_replace('/--A--SIPexten--B--/i',"$SIPexten",(string) $script_text);
$script_text = preg_replace('/--A--session_id--B--/i',"$session_id",(string) $script_text);
$script_text = preg_replace('/--A--phone--B--/i',"$phone",(string) $script_text);
$script_text = preg_replace('/--A--parked_by--B--/i',"$parked_by",(string) $script_text);
$script_text = preg_replace('/--A--dispo--B--/i',"$dispo",(string) $script_text);
$script_text = preg_replace('/--A--dialed_number--B--/i',"$dialed_number",(string) $script_text);
$script_text = preg_replace('/--A--dialed_label--B--/i',"$dialed_label",(string) $script_text);
$script_text = preg_replace('/--A--source_id--B--/i',"$source_id",(string) $script_text);
$script_text = preg_replace('/--A--rank--B--/i',"$rank",(string) $script_text);
$script_text = preg_replace('/--A--owner--B--/i',"$owner",(string) $script_text);
$script_text = preg_replace('/--A--camp_script--B--/i',"$camp_script",(string) $script_text);
$script_text = preg_replace('/--A--in_script--B--/i',"$in_script",(string) $script_text);
$script_text = preg_replace('/--A--script_width--B--/i',"$script_width",(string) $script_text);
$script_text = preg_replace('/--A--script_height--B--/i',"$script_height",(string) $script_text);
$script_text = preg_replace('/--A--fullname--B--/i',"$fullname",(string) $script_text);
$script_text = preg_replace('/--A--recording_filename--B--/i',"$recording_filename",(string) $script_text);
$script_text = preg_replace('/--A--recording_id--B--/i',"$recording_id",(string) $script_text);
$script_text = preg_replace('/--A--user_custom_one--B--/i',"$user_custom_one",(string) $script_text);
$script_text = preg_replace('/--A--user_custom_two--B--/i',"$user_custom_two",(string) $script_text);
$script_text = preg_replace('/--A--user_custom_three--B--/i',"$user_custom_three",(string) $script_text);
$script_text = preg_replace('/--A--user_custom_four--B--/i',"$user_custom_four",(string) $script_text);
$script_text = preg_replace('/--A--user_custom_five--B--/i',"$user_custom_five",(string) $script_text);
$script_text = preg_replace('/--A--preset_number_a--B--/i',"$preset_number_a",(string) $script_text);
$script_text = preg_replace('/--A--preset_number_b--B--/i',"$preset_number_b",(string) $script_text);
$script_text = preg_replace('/--A--preset_number_c--B--/i',"$preset_number_c",(string) $script_text);
$script_text = preg_replace('/--A--preset_number_d--B--/i',"$preset_number_d",(string) $script_text);
$script_text = preg_replace('/--A--preset_number_e--B--/i',"$preset_number_e",(string) $script_text);
$script_text = preg_replace('/--A--preset_number_f--B--/i',"$preset_number_f",(string) $script_text);
$script_text = preg_replace('/--A--preset_dtmf_a--B--/i',"$preset_dtmf_a",(string) $script_text);
$script_text = preg_replace('/--A--preset_dtmf_b--B--/i',"$preset_dtmf_b",(string) $script_text);
$script_text = preg_replace('/--A--did_id--B--/i',"$did_id",(string) $script_text);
$script_text = preg_replace('/--A--did_extension--B--/i',"$did_extension",(string) $script_text);
$script_text = preg_replace('/--A--did_pattern--B--/i',"$did_pattern",(string) $script_text);
$script_text = preg_replace('/--A--did_description--B--/i',"$did_description",(string) $script_text);
$script_text = preg_replace('/--A--closecallid--B--/i',"$closecallid",(string) $script_text);
$script_text = preg_replace('/--A--xfercallid--B--/i',"$xfercallid",(string) $script_text);
$script_text = preg_replace('/--A--agent_log_id--B--/i',"$agent_log_id",(string) $script_text);
$script_text = preg_replace('/--A--entry_list_id--B--/i',"$entry_list_id",(string) $script_text);
$script_text = preg_replace('/--A--call_id--B--/i',"$call_id",(string) $script_text);
$script_text = preg_replace('/--A--user_group--B--/i',"$user_group",(string) $script_text);
$script_text = preg_replace('/--A--web_vars--B--/i',"$web_vars",(string) $script_text);

if ($CF_uses_custom_fields == 'Y') {
	### find the names of all custom fields, if any
	//$stmt = "SELECT field_label,field_type FROM vicidial_lists_fields where list_id='$entry_list_id' and field_type NOT IN('SCRIPT','DISPLAY') and field_label NOT IN('vendor_lead_code','source_id','list_id','gmt_offset_now','called_since_last_reset','phone_code','phone_number','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','gender','date_of_birth','alt_phone','email','security_phrase','comments','called_count','last_local_call_time','rank','owner');";
    $astDB->where('list_id', $entry_list_id);
    $astDB->where('field_type', ['SCRIPT', 'DISPLAY'], 'not in');
    $astDB->where('field_label', ['vendor_lead_code','source_id','list_id','gmt_offset_now','called_since_last_reset','phone_code','phone_number','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','gender','date_of_birth','alt_phone','email','security_phrase','comments','called_count','last_local_call_time','rank','owner'], 'not in');
	$rslt = $astDB->get('vicidial_lists_fields', null, 'field_label,field_type');
	$cffn_ct = $astDB->getRowCount();
	foreach ((is_array($rslt) ? $rslt : []) as $row) {
		$field_name_id = $row['field_label'] ?? '';
		$field_name_tag = "--A--" . $field_name_id . "--B--";
		$form_field_value = '';
		if (isset($_GET["$field_name_id"]))				{$form_field_value=$astDB->escape($_GET["$field_name_id"]);}
			elseif (isset($_POST["$field_name_id"]))	{$form_field_value=$astDB->escape($_POST["$field_name_id"]);}
		$script_text = preg_replace("/$field_name_tag/i","$form_field_value",(string) $script_text);
    }
}

$script_text = preg_replace("/\n/i","<BR>",(string) $script_text);
$script_text = stripslashes((string) $script_text);


//$goAgentScripts .=  "<!-- IFRAME$IFRAME -->\n";
//$goAgentScripts .=  "<!-- $script_id -->\n";
$goAgentScripts .=  "<TABLE WIDTH=100%><TR><TD>\n";
if ( $IFRAME < 1 && $ScrollDIV > 0 || preg_match("/IGNORENOSCROLL/i", (string) $script_text) )
	{$goAgentScripts .=   "<div id=\"NewScriptContents\">";}
$goAgentScripts .=  "<center><B>$script_name</B></center><BR>\n";
$goAgentScripts .=  "$script_text\n";
if ( $IFRAME < 1 && $ScrollDIV > 0 || preg_match("/IGNORENOSCROLL/i", (string) $script_text) )
	{$goAgentScripts .=  "</div>";}
$goAgentScripts .=  "</TD></TR></TABLE>\n";

//exit;

$apiresults = ["result" => "success", "gocampaignScript" => $goAgentScripts];
?>
