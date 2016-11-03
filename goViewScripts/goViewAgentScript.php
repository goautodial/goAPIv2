<?php
   ####################################################
   #### Name: goViewAgentScript.php                ####
   #### Description: API view script               ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jerico James Milo              ####
   #### License: AGPLv2                            ####
   ####################################################

include_once ("../goFunctions.php");


if (isset($_GET["lead_id"]))	{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))	{$lead_id=$_POST["lead_id"];}
if (isset($_GET["vendor_id"]))	{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))	{$vendor_id=$_POST["vendor_id"];}
	$vendor_lead_code = $vendor_id;
//if (isset($_GET["list_id"]))	{$list_id=$_GET["list_id"];}
//	elseif (isset($_POST["list_id"]))	{$list_id=$_POST["list_id"];}
if (isset($_GET["gmt_offset_now"]))	{$gmt_offset_now=$_GET["gmt_offset_now"];}
	elseif (isset($_POST["gmt_offset_now"]))	{$gmt_offset_now=$_POST["gmt_offset_now"];}
if (isset($_GET["phone_code"]))	{$phone_code=$_GET["phone_code"];}
	elseif (isset($_POST["phone_code"]))	{$phone_code=$_POST["phone_code"];}
if (isset($_GET["phone_number"]))	{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))	{$phone_number=$_POST["phone_number"];}
if (isset($_GET["title"]))	{$title=$_GET["title"];}
	elseif (isset($_POST["title"]))	{$title=$_POST["title"];}
if (isset($_GET["first_name"]))	{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))	{$first_name=$_POST["first_name"];}
if (isset($_GET["middle_initial"]))	{$middle_initial=$_GET["middle_initial"];}
	elseif (isset($_POST["middle_initial"]))	{$middle_initial=$_POST["middle_initial"];}
if (isset($_GET["last_name"]))	{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))	{$last_name=$_POST["last_name"];}
if (isset($_GET["address1"]))	{$address1=$_GET["address1"];}
	elseif (isset($_POST["address1"]))	{$address1=$_POST["address1"];}
if (isset($_GET["address2"]))	{$address2=$_GET["address2"];}
	elseif (isset($_POST["address2"]))	{$address2=$_POST["address2"];}
if (isset($_GET["address3"]))	{$address3=$_GET["address3"];}
	elseif (isset($_POST["address3"]))	{$address3=$_POST["address3"];}
if (isset($_GET["city"]))	{$city=$_GET["city"];}
	elseif (isset($_POST["city"]))	{$city=$_POST["city"];}
if (isset($_GET["state"]))	{$state=$_GET["state"];}
	elseif (isset($_POST["state"]))	{$state=$_POST["state"];}
if (isset($_GET["province"]))	{$province=$_GET["province"];}
	elseif (isset($_POST["province"]))	{$province=$_POST["province"];}
if (isset($_GET["postal_code"]))	{$postal_code=$_GET["postal_code"];}
	elseif (isset($_POST["postal_code"]))	{$postal_code=$_POST["postal_code"];}
if (isset($_GET["country_code"]))	{$country_code=$_GET["country_code"];}
	elseif (isset($_POST["country_code"]))	{$country_code=$_POST["country_code"];}
if (isset($_GET["gender"]))	{$gender=$_GET["gender"];}
	elseif (isset($_POST["gender"]))	{$gender=$_POST["gender"];}
if (isset($_GET["date_of_birth"]))	{$date_of_birth=$_GET["date_of_birth"];}
	elseif (isset($_POST["date_of_birth"]))	{$date_of_birth=$_POST["date_of_birth"];}
if (isset($_GET["alt_phone"]))	{$alt_phone=$_GET["alt_phone"];}
	elseif (isset($_POST["alt_phone"]))	{$alt_phone=$_POST["alt_phone"];}
if (isset($_GET["email"]))	{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))	{$email=$_POST["email"];}
if (isset($_GET["security_phrase"]))	{$security_phrase=$_GET["security_phrase"];}
	elseif (isset($_POST["security_phrase"]))	{$security_phrase=$_POST["security_phrase"];}
if (isset($_GET["comments"]))	{$comments=$_GET["comments"];}
	elseif (isset($_POST["comments"]))	{$comments=$_POST["comments"];}
if (isset($_GET["user"]))	{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))	{$user=$_POST["user"];}
if (isset($_GET["pass"]))	{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))	{$pass=$_POST["pass"];}
if (isset($_GET["campaign"]))	{$campaign=$_GET["campaign"];}
	elseif (isset($_POST["campaign"]))	{$campaign=$_POST["campaign"];}
if (isset($_GET["phone_login"]))	{$phone_login=$_GET["phone_login"];}
	elseif (isset($_POST["phone_login"]))	{$phone_login=$_POST["phone_login"];}
if (isset($_GET["original_phone_login"]))	{$original_phone_login=$_GET["original_phone_login"];}
	elseif (isset($_POST["original_phone_login"]))	{$original_phone_login=$_POST["original_phone_login"];}
if (isset($_GET["phone_pass"]))	{$phone_pass=$_GET["phone_pass"];}
	elseif (isset($_POST["phone_pass"]))	{$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["fronter"]))	{$fronter=$_GET["fronter"];}
	elseif (isset($_POST["fronter"]))	{$fronter=$_POST["fronter"];}
if (isset($_GET["closer"]))	{$closer=$_GET["closer"];}
	elseif (isset($_POST["closer"]))	{$closer=$_POST["closer"];}
if (isset($_GET["group"]))	{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))	{$group=$_POST["group"];}
if (isset($_GET["channel_group"]))	{$channel_group=$_GET["channel_group"];}
	elseif (isset($_POST["channel_group"]))	{$channel_group=$_POST["channel_group"];}
if (isset($_GET["SQLdate"]))	{$SQLdate=$_GET["SQLdate"];}
	elseif (isset($_POST["SQLdate"]))	{$SQLdate=$_POST["SQLdate"];}
if (isset($_GET["epoch"]))	{$epoch=$_GET["epoch"];}
	elseif (isset($_POST["epoch"]))	{$epoch=$_POST["epoch"];}
if (isset($_GET["uniqueid"]))	{$uniqueid=$_GET["uniqueid"];}
	elseif (isset($_POST["uniqueid"]))	{$uniqueid=$_POST["uniqueid"];}
if (isset($_GET["customer_zap_channel"]))	{$customer_zap_channel=$_GET["customer_zap_channel"];}
	elseif (isset($_POST["customer_zap_channel"]))	{$customer_zap_channel=$_POST["customer_zap_channel"];}
if (isset($_GET["customer_server_ip"]))	{$customer_server_ip=$_GET["customer_server_ip"];}
	elseif (isset($_POST["customer_server_ip"]))	{$customer_server_ip=$_POST["customer_server_ip"];}
if (isset($_GET["server_ip"]))	{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))	{$server_ip=$_POST["server_ip"];}
if (isset($_GET["SIPexten"]))	{$SIPexten=$_GET["SIPexten"];}
	elseif (isset($_POST["SIPexten"]))	{$SIPexten=$_POST["SIPexten"];}
if (isset($_GET["session_id"]))	{$session_id=$_GET["session_id"];}
	elseif (isset($_POST["session_id"]))	{$session_id=$_POST["session_id"];}
if (isset($_GET["phone"]))	{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))	{$phone=$_POST["phone"];}
if (isset($_GET["parked_by"]))	{$parked_by=$_GET["parked_by"];}
	elseif (isset($_POST["parked_by"]))	{$parked_by=$_POST["parked_by"];}
if (isset($_GET["dispo"]))	{$dispo=$_GET["dispo"];}
	elseif (isset($_POST["dispo"]))	{$dispo=$_POST["dispo"];}
if (isset($_GET["dialed_number"]))	{$dialed_number=$_GET["dialed_number"];}
	elseif (isset($_POST["dialed_number"]))	{$dialed_number=$_POST["dialed_number"];}
if (isset($_GET["dialed_label"]))	{$dialed_label=$_GET["dialed_label"];}
	elseif (isset($_POST["dialed_label"]))	{$dialed_label=$_POST["dialed_label"];}
if (isset($_GET["source_id"]))	{$source_id=$_GET["source_id"];}
	elseif (isset($_POST["source_id"]))	{$source_id=$_POST["source_id"];}
if (isset($_GET["rank"]))	{$rank=$_GET["rank"];}
	elseif (isset($_POST["rank"]))	{$rank=$_POST["rank"];}
if (isset($_GET["owner"]))	{$owner=$_GET["owner"];}
	elseif (isset($_POST["owner"]))	{$owner=$_POST["owner"];}
if (isset($_GET["camp_script"]))	{$camp_script=$_GET["camp_script"];}
	elseif (isset($_POST["camp_script"]))	{$camp_script=$_POST["camp_script"];}
if (isset($_GET["in_script"]))	{$in_script=$_GET["in_script"];}
	elseif (isset($_POST["in_script"]))	{$in_script=$_POST["in_script"];}
if (isset($_GET["script_width"]))	{$script_width=$_GET["script_width"];}
	elseif (isset($_POST["script_width"]))	{$script_width=$_POST["script_width"];}
if (isset($_GET["script_height"]))	{$script_height=$_GET["script_height"];}
	elseif (isset($_POST["script_height"]))	{$script_height=$_POST["script_height"];}
if (isset($_GET["fullname"]))	{$fullname=$_GET["fullname"];}
	elseif (isset($_POST["fullname"]))	{$fullname=$_POST["fullname"];}
if (isset($_GET["recording_filename"]))	{$recording_filename=$_GET["recording_filename"];}
	elseif (isset($_POST["recording_filename"]))	{$recording_filename=$_POST["recording_filename"];}
if (isset($_GET["recording_id"]))	{$recording_id=$_GET["recording_id"];}
	elseif (isset($_POST["recording_id"]))	{$recording_id=$_POST["recording_id"];}
if (isset($_GET["user_custom_one"]))	{$user_custom_one=$_GET["user_custom_one"];}
	elseif (isset($_POST["user_custom_one"]))	{$user_custom_one=$_POST["user_custom_one"];}
if (isset($_GET["user_custom_two"]))	{$user_custom_two=$_GET["user_custom_two"];}
	elseif (isset($_POST["user_custom_two"]))	{$user_custom_two=$_POST["user_custom_two"];}
if (isset($_GET["user_custom_three"]))	{$user_custom_three=$_GET["user_custom_three"];}
	elseif (isset($_POST["user_custom_three"]))	{$user_custom_three=$_POST["user_custom_three"];}
if (isset($_GET["user_custom_four"]))	{$user_custom_four=$_GET["user_custom_four"];}
	elseif (isset($_POST["user_custom_four"]))	{$user_custom_four=$_POST["user_custom_four"];}
if (isset($_GET["user_custom_five"]))	{$user_custom_five=$_GET["user_custom_five"];}
	elseif (isset($_POST["user_custom_five"]))	{$user_custom_five=$_POST["user_custom_five"];}
if (isset($_GET["preset_number_a"]))	{$preset_number_a=$_GET["preset_number_a"];}
	elseif (isset($_POST["preset_number_a"]))	{$preset_number_a=$_POST["preset_number_a"];}
if (isset($_GET["preset_number_b"]))	{$preset_number_b=$_GET["preset_number_b"];}
	elseif (isset($_POST["preset_number_b"]))	{$preset_number_b=$_POST["preset_number_b"];}
if (isset($_GET["preset_number_c"]))	{$preset_number_c=$_GET["preset_number_c"];}
	elseif (isset($_POST["preset_number_c"]))	{$preset_number_c=$_POST["preset_number_c"];}
if (isset($_GET["preset_number_d"]))	{$preset_number_d=$_GET["preset_number_d"];}
	elseif (isset($_POST["preset_number_d"]))	{$preset_number_d=$_POST["preset_number_d"];}
if (isset($_GET["preset_number_e"]))	{$preset_number_e=$_GET["preset_number_e"];}
	elseif (isset($_POST["preset_number_e"]))	{$preset_number_e=$_POST["preset_number_e"];}
if (isset($_GET["preset_number_f"]))	{$preset_number_f=$_GET["preset_number_f"];}
	elseif (isset($_POST["preset_number_f"]))	{$preset_number_f=$_POST["preset_number_f"];}
if (isset($_GET["preset_dtmf_a"]))	{$preset_dtmf_a=$_GET["preset_dtmf_a"];}
	elseif (isset($_POST["preset_dtmf_a"]))	{$preset_dtmf_a=$_POST["preset_dtmf_a"];}
if (isset($_GET["preset_dtmf_b"]))	{$preset_dtmf_b=$_GET["preset_dtmf_b"];}
	elseif (isset($_POST["preset_dtmf_b"]))	{$preset_dtmf_b=$_POST["preset_dtmf_b"];}
if (isset($_GET["did_id"]))				{$did_id=$_GET["did_id"];}
	elseif (isset($_POST["did_id"]))	{$did_id=$_POST["did_id"];}
if (isset($_GET["did_extension"]))			{$did_extension=$_GET["did_extension"];}
	elseif (isset($_POST["did_extension"]))	{$did_extension=$_POST["did_extension"];}
if (isset($_GET["did_pattern"]))			{$did_pattern=$_GET["did_pattern"];}
	elseif (isset($_POST["did_pattern"]))	{$did_pattern=$_POST["did_pattern"];}
if (isset($_GET["did_description"]))			{$did_description=$_GET["did_description"];}
	elseif (isset($_POST["did_description"]))	{$did_description=$_POST["did_description"];}
if (isset($_GET["closecallid"]))			{$closecallid=$_GET["closecallid"];}
	elseif (isset($_POST["closecallid"]))	{$closecallid=$_POST["closecallid"];}
if (isset($_GET["xfercallid"]))				{$xfercallid=$_GET["xfercallid"];}
	elseif (isset($_POST["xfercallid"]))	{$xfercallid=$_POST["xfercallid"];}
if (isset($_GET["agent_log_id"]))			{$agent_log_id=$_GET["agent_log_id"];}
	elseif (isset($_POST["agent_log_id"]))	{$agent_log_id=$_POST["agent_log_id"];}
if (isset($_GET["ScrollDIV"]))			{$ScrollDIV=$_GET["ScrollDIV"];}
	elseif (isset($_POST["ScrollDIV"]))	{$ScrollDIV=$_POST["ScrollDIV"];}
if (isset($_GET["ignore_list_script"]))				{$ignore_list_script=$_GET["ignore_list_script"];}
	elseif (isset($_POST["ignore_list_script"]))	{$ignore_list_script=$_POST["ignore_list_script"];}
if (isset($_GET["CF_uses_custom_fields"]))			{$CF_uses_custom_fields=$_GET["CF_uses_custom_fields"];}
	elseif (isset($_POST["CF_uses_custom_fields"]))	{$CF_uses_custom_fields=$_POST["CF_uses_custom_fields"];}
if (isset($_GET["entry_list_id"]))			{$entry_list_id=$_GET["entry_list_id"];}
	elseif (isset($_POST["entry_list_id"]))	{$entry_list_id=$_POST["entry_list_id"];}
if (isset($_GET["call_id"]))			{$call_id=$_GET["call_id"];}
	elseif (isset($_POST["call_id"]))	{$call_id=$_POST["call_id"];}
if (isset($_GET["user_group"]))				{$user_group=$_GET["user_group"];}
	elseif (isset($_POST["user_group"]))	{$user_group=$_POST["user_group"];}
if (isset($_GET["web_vars"]))			{$web_vars=$_GET["web_vars"];}
	elseif (isset($_POST["web_vars"]))	{$web_vars=$_POST["web_vars"];}


header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

$txt = '.txt';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$CIDdate = date("mdHis");
$ENTRYdate = date("YmdHis");
$MT[0]='';
$agents='@agents';
$script_height = ($script_height - 20);

$IFRAME=0;

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,timeclock_end_of_day,agentonly_callback_campaign_lock FROM system_settings;";
$rslt=mysqli_query($link, $stmt);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysqli_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysqli_fetch_row($rslt);
	$non_latin =							$row[0];
	$timeclock_end_of_day =					$row[1];
	$agentonly_callback_campaign_lock =		$row[2];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
	{
	$user=preg_replace("/[^-_0-9a-zA-Z]/","",$user);
	$pass=preg_replace("/[^\.-_0-9a-zA-Z]/","",$pass);
	$length_in_sec = preg_replace("/[^0-9]/","",$length_in_sec);
	$phone_code = preg_replace("/[^0-9]/","",$phone_code);
	$phone_number = preg_replace("/[^0-9]/","",$phone_number);
	}
else
	{
	$user = preg_replace("/\'|\"|\\\\|;/","",$user);
	$pass = preg_replace("/\'|\"|\\\\|;/","",$pass);
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

if (strlen($in_script) < 1)
	{$call_script = $camp_script;}
else
	{$call_script = $in_script;}

$ignore_list_script_override='N';
$stmt = "SELECT ignore_list_script_override FROM vicidial_inbound_groups where group_id='$group';";
$rslt=mysqli_query($link, $stmt);
if ($DB) {echo "$stmt\n";}
$ilso_ct = mysqli_num_rows($rslt);
if ($ilso_ct > 0)
	{
	$row=mysqli_fetch_row($rslt);
	$ignore_list_script_override =		$row[0];
	}
if ($ignore_list_script_override=='Y')
	{$ignore_list_script=1;}

if ($ignore_list_script < 1)
	{

//milo
//get list_id from lead_id
	$queryGetlist_id = "SELECT list_id FROM vicidial_list WHERE lead_id='$lead_id';";
	$qrslt=mysqli_query($link, $queryGetlist_id);
	$fetchlistid = mysqli_fetch_row($qrslt);
	$list_id = $fetchlistid[0];

	$stmt="SELECT agent_script_override from vicidial_lists where list_id='$list_id';";
	$rslt=mysqli_query($link, $stmt);
	$row=mysqli_fetch_row($rslt);
	$agent_script_override =		$row[0];
	if (strlen($agent_script_override) > 0)
		{$call_script = $agent_script_override;}
	}

//milo
//get script id
	$queryGetscript = "SELECT vc.campaign_script FROM vicidial_campaigns vc, vicidial_lists vl WHERE vc.campaign_id = vl.campaign_id AND vl.list_id='$list_id';";
	$gsrslt = mysqli_query($link, $queryGetscript);
	$fetchscriptid = mysqli_fetch_row($gsrslt);
	$call_script = $fetchscriptid[0];


$stmt="SELECT list_name,list_description from vicidial_lists where list_id='$list_id';";
$rslt=mysqli_query($link, $stmt);
$row=mysqli_fetch_row($rslt);
$list_name =			$row[0];
$list_description =		$row[1];

$stmt="SELECT script_name,script_text from vicidial_scripts where script_id='$call_script';";
$rslt=mysqli_query($link, $stmt);
$row=mysqli_fetch_row($rslt);
$script_name =		$row[0];
$script_text =		stripslashes($row[1]);

if (preg_match("/iframe\ssrc/i",$script_text))
	{
	$IFRAME=1;
	$lead_id = preg_replace('/\s/i','+',$lead_id);
	$vendor_id = preg_replace('/\s/i','+',$vendor_id);
	$vendor_lead_code = preg_replace('/\s/i','+',$vendor_lead_code);
	$list_id = preg_replace('/\s/i','+',$list_id);
	$list_name = preg_replace('/\s/i','+',$list_name);
	$list_description = preg_replace('/\s/i','+',$list_description);
	$gmt_offset_now = preg_replace('/\s/i','+',$gmt_offset_now);
	$phone_code = preg_replace('/\s/i','+',$phone_code);
	$phone_number = preg_replace('/\s/i','+',$phone_number);
	$title = preg_replace('/\s/i','+',$title);
	$first_name = preg_replace('/\s/i','+',$first_name);
	$middle_initial = preg_replace('/\s/i','+',$middle_initial);
	$last_name = preg_replace('/\s/i','+',$last_name);
	$address1 = preg_replace('/\s/i','+',$address1);
	$address2 = preg_replace('/\s/i','+',$address2);
	$address3 = preg_replace('/\s/i','+',$address3);
	$city = preg_replace('/\s/i','+',$city);
	$state = preg_replace('/\s/i','+',$state);
	$province = preg_replace('/\s/i','+',$province);
	$postal_code = preg_replace('/\s/i','+',$postal_code);
	$country_code = preg_replace('/\s/i','+',$country_code);
	$gender = preg_replace('/\s/i','+',$gender);
	$date_of_birth = preg_replace('/\s/i','+',$date_of_birth);
	$alt_phone = preg_replace('/\s/i','+',$alt_phone);
	$email = preg_replace('/\s/i','+',$email);
	$security_phrase = preg_replace('/\s/i','+',$security_phrase);
	$comments = preg_replace('/\s/i','+',$comments);
	$user = preg_replace('/\s/i','+',$user);
	$pass = preg_replace('/\s/i','+',$pass);
	$campaign = preg_replace('/\s/i','+',$campaign);
	$phone_login = preg_replace('/\s/i','+',$phone_login);
	$original_phone_login = preg_replace('/\s/i','+',$original_phone_login);
	$phone_pass = preg_replace('/\s/i','+',$phone_pass);
	$fronter = preg_replace('/\s/i','+',$fronter);
	$closer = preg_replace('/\s/i','+',$closer);
	$group = preg_replace('/\s/i','+',$group);
	$channel_group = preg_replace('/\s/i','+',$channel_group);
	$SQLdate = preg_replace('/\s/i','+',$SQLdate);
	$epoch = preg_replace('/\s/i','+',$epoch);
	$uniqueid = preg_replace('/\s/i','+',$uniqueid);
	$customer_zap_channel = preg_replace('/\s/i','+',$customer_zap_channel);
	$customer_server_ip = preg_replace('/\s/i','+',$customer_server_ip);
	$server_ip = preg_replace('/\s/i','+',$server_ip);
	$SIPexten = preg_replace('/\s/i','+',$SIPexten);
	$session_id = preg_replace('/\s/i','+',$session_id);
	$phone = preg_replace('/\s/i','+',$phone);
	$parked_by = preg_replace('/\s/i','+',$parked_by);
	$dispo = preg_replace('/\s/i','+',$dispo);
	$dialed_number = preg_replace('/\s/i','+',$dialed_number);
	$dialed_label = preg_replace('/\s/i','+',$dialed_label);
	$source_id = preg_replace('/\s/i','+',$source_id);
	$rank = preg_replace('/\s/i','+',$rank);
	$owner = preg_replace('/\s/i','+',$owner);
	$camp_script = preg_replace('/\s/i','+',$camp_script);
	$in_script = preg_replace('/\s/i','+',$in_script);
	$script_width = preg_replace('/\s/i','+',$script_width);
	$script_height = preg_replace('/\s/i','+',$script_height);
	$fullname = preg_replace('/\s/i','+',$fullname);
	$recording_filename = preg_replace('/\s/i','+',$recording_filename);
	$recording_id = preg_replace('/\s/i','+',$recording_id);
	$user_custom_one = preg_replace('/\s/i','+',$user_custom_one);
	$user_custom_two = preg_replace('/\s/i','+',$user_custom_two);
	$user_custom_three = preg_replace('/\s/i','+',$user_custom_three);
	$user_custom_four = preg_replace('/\s/i','+',$user_custom_four);
	$user_custom_five = preg_replace('/\s/i','+',$user_custom_five);
	$preset_number_a = preg_replace('/\s/i','+',$preset_number_a);
	$preset_number_b = preg_replace('/\s/i','+',$preset_number_b);
	$preset_number_c = preg_replace('/\s/i','+',$preset_number_c);
	$preset_number_d = preg_replace('/\s/i','+',$preset_number_d);
	$preset_number_e = preg_replace('/\s/i','+',$preset_number_e);
	$preset_number_f = preg_replace('/\s/i','+',$preset_number_f);
	$preset_dtmf_a = preg_replace('/\s/i','+',$preset_dtmf_a);
	$preset_dtmf_b = preg_replace('/\s/i','+',$preset_dtmf_b);
	$did_id = preg_replace('/\s/i','+',$did_id);
	$did_extension = preg_replace('/\s/i','+',$did_extension);
	$did_pattern = preg_replace('/\s/i','+',$did_pattern);
	$did_description = preg_replace('/\s/i','+',$did_description);
	$web_vars = preg_replace('/\s/i','+',$web_vars);
	}

$script_text = preg_replace('/--A--lead_id--B--/i',"$lead_id",$script_text);
$script_text = preg_replace('/--A--vendor_id--B--/i',"$vendor_id",$script_text);
$script_text = preg_replace('/--A--vendor_lead_code--B--/i',"$vendor_lead_code",$script_text);
$script_text = preg_replace('/--A--list_id--B--/i',"$list_id",$script_text);
$script_text = preg_replace('/--A--list_name--B--/i',"$list_name",$script_text);
$script_text = preg_replace('/--A--list_description--B--/i',"$list_description",$script_text);
$script_text = preg_replace('/--A--gmt_offset_now--B--/i',"$gmt_offset_now",$script_text);
$script_text = preg_replace('/--A--phone_code--B--/i',"$phone_code",$script_text);
$script_text = preg_replace('/--A--phone_number--B--/i',"$phone_number",$script_text);
$script_text = preg_replace('/--A--title--B--/i',"$title",$script_text);
$script_text = preg_replace('/--A--first_name--B--/i',"$first_name",$script_text);
$script_text = preg_replace('/--A--middle_initial--B--/i',"$middle_initial",$script_text);
$script_text = preg_replace('/--A--last_name--B--/i',"$last_name",$script_text);
$script_text = preg_replace('/--A--address1--B--/i',"$address1",$script_text);
$script_text = preg_replace('/--A--address2--B--/i',"$address2",$script_text);
$script_text = preg_replace('/--A--address3--B--/i',"$address3",$script_text);
$script_text = preg_replace('/--A--city--B--/i',"$city",$script_text);
$script_text = preg_replace('/--A--state--B--/i',"$state",$script_text);
$script_text = preg_replace('/--A--province--B--/i',"$province",$script_text);
$script_text = preg_replace('/--A--postal_code--B--/i',"$postal_code",$script_text);
$script_text = preg_replace('/--A--country_code--B--/i',"$country_code",$script_text);
$script_text = preg_replace('/--A--gender--B--/i',"$gender",$script_text);
$script_text = preg_replace('/--A--date_of_birth--B--/i',"$date_of_birth",$script_text);
$script_text = preg_replace('/--A--alt_phone--B--/i',"$alt_phone",$script_text);
$script_text = preg_replace('/--A--email--B--/i',"$email",$script_text);
$script_text = preg_replace('/--A--security_phrase--B--/i',"$security_phrase",$script_text);
$script_text = preg_replace('/--A--comments--B--/i',"$comments",$script_text);
$script_text = preg_replace('/--A--user--B--/i',"$user",$script_text);
$script_text = preg_replace('/--A--pass--B--/i',"$pass",$script_text);
$script_text = preg_replace('/--A--campaign--B--/i',"$campaign",$script_text);
$script_text = preg_replace('/--A--phone_login--B--/i',"$phone_login",$script_text);
$script_text = preg_replace('/--A--original_phone_login--B--/i',"$original_phone_login",$script_text);
$script_text = preg_replace('/--A--phone_pass--B--/i',"$phone_pass",$script_text);
$script_text = preg_replace('/--A--fronter--B--/i',"$fronter",$script_text);
$script_text = preg_replace('/--A--closer--B--/i',"$closer",$script_text);
$script_text = preg_replace('/--A--group--B--/i',"$group",$script_text);
$script_text = preg_replace('/--A--channel_group--B--/i',"$channel_group",$script_text);
$script_text = preg_replace('/--A--SQLdate--B--/i',"$SQLdate",$script_text);
$script_text = preg_replace('/--A--epoch--B--/i',"$epoch",$script_text);
$script_text = preg_replace('/--A--uniqueid--B--/i',"$uniqueid",$script_text);
$script_text = preg_replace('/--A--customer_zap_channel--B--/i',"$customer_zap_channel",$script_text);
$script_text = preg_replace('/--A--customer_server_ip--B--/i',"$customer_server_ip",$script_text);
$script_text = preg_replace('/--A--server_ip--B--/i',"$server_ip",$script_text);
$script_text = preg_replace('/--A--SIPexten--B--/i',"$SIPexten",$script_text);
$script_text = preg_replace('/--A--session_id--B--/i',"$session_id",$script_text);
$script_text = preg_replace('/--A--phone--B--/i',"$phone",$script_text);
$script_text = preg_replace('/--A--parked_by--B--/i',"$parked_by",$script_text);
$script_text = preg_replace('/--A--dispo--B--/i',"$dispo",$script_text);
$script_text = preg_replace('/--A--dialed_number--B--/i',"$dialed_number",$script_text);
$script_text = preg_replace('/--A--dialed_label--B--/i',"$dialed_label",$script_text);
$script_text = preg_replace('/--A--source_id--B--/i',"$source_id",$script_text);
$script_text = preg_replace('/--A--rank--B--/i',"$rank",$script_text);
$script_text = preg_replace('/--A--owner--B--/i',"$owner",$script_text);
$script_text = preg_replace('/--A--camp_script--B--/i',"$camp_script",$script_text);
$script_text = preg_replace('/--A--in_script--B--/i',"$in_script",$script_text);
$script_text = preg_replace('/--A--script_width--B--/i',"$script_width",$script_text);
$script_text = preg_replace('/--A--script_height--B--/i',"$script_height",$script_text);
$script_text = preg_replace('/--A--fullname--B--/i',"$fullname",$script_text);
$script_text = preg_replace('/--A--recording_filename--B--/i',"$recording_filename",$script_text);
$script_text = preg_replace('/--A--recording_id--B--/i',"$recording_id",$script_text);
$script_text = preg_replace('/--A--user_custom_one--B--/i',"$user_custom_one",$script_text);
$script_text = preg_replace('/--A--user_custom_two--B--/i',"$user_custom_two",$script_text);
$script_text = preg_replace('/--A--user_custom_three--B--/i',"$user_custom_three",$script_text);
$script_text = preg_replace('/--A--user_custom_four--B--/i',"$user_custom_four",$script_text);
$script_text = preg_replace('/--A--user_custom_five--B--/i',"$user_custom_five",$script_text);
$script_text = preg_replace('/--A--preset_number_a--B--/i',"$preset_number_a",$script_text);
$script_text = preg_replace('/--A--preset_number_b--B--/i',"$preset_number_b",$script_text);
$script_text = preg_replace('/--A--preset_number_c--B--/i',"$preset_number_c",$script_text);
$script_text = preg_replace('/--A--preset_number_d--B--/i',"$preset_number_d",$script_text);
$script_text = preg_replace('/--A--preset_number_e--B--/i',"$preset_number_e",$script_text);
$script_text = preg_replace('/--A--preset_number_f--B--/i',"$preset_number_f",$script_text);
$script_text = preg_replace('/--A--preset_dtmf_a--B--/i',"$preset_dtmf_a",$script_text);
$script_text = preg_replace('/--A--preset_dtmf_b--B--/i',"$preset_dtmf_b",$script_text);
$script_text = preg_replace('/--A--did_id--B--/i',"$did_id",$script_text);
$script_text = preg_replace('/--A--did_extension--B--/i',"$did_extension",$script_text);
$script_text = preg_replace('/--A--did_pattern--B--/i',"$did_pattern",$script_text);
$script_text = preg_replace('/--A--did_description--B--/i',"$did_description",$script_text);
$script_text = preg_replace('/--A--closecallid--B--/i',"$closecallid",$script_text);
$script_text = preg_replace('/--A--xfercallid--B--/i',"$xfercallid",$script_text);
$script_text = preg_replace('/--A--agent_log_id--B--/i',"$agent_log_id",$script_text);
$script_text = preg_replace('/--A--entry_list_id--B--/i',"$entry_list_id",$script_text);
$script_text = preg_replace('/--A--call_id--B--/i',"$call_id",$script_text);
$script_text = preg_replace('/--A--user_group--B--/i',"$user_group",$script_text);
$script_text = preg_replace('/--A--web_vars--B--/i',"$web_vars",$script_text);

if ($CF_uses_custom_fields=='Y')
	{
	### find the names of all custom fields, if any
	$stmt = "SELECT field_label,field_type FROM vicidial_lists_fields where list_id='$entry_list_id' and field_type NOT IN('SCRIPT','DISPLAY') and field_label NOT IN('vendor_lead_code','source_id','list_id','gmt_offset_now','called_since_last_reset','phone_code','phone_number','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','gender','date_of_birth','alt_phone','email','security_phrase','comments','called_count','last_local_call_time','rank','owner');";
	$rslt=mysqli_query($link, $stmt);
	if ($DB) {echo "$stmt\n";}
	$cffn_ct = mysqli_num_rows($rslt);
	$d=0;
	while ($cffn_ct > $d)
		{
		$row=mysqli_fetch_row($rslt);
		$field_name_id = $row[0];
		$field_name_tag = "--A--" . $field_name_id . "--B--";
		if (isset($_GET["$field_name_id"]))				{$form_field_value=$_GET["$field_name_id"];}
			elseif (isset($_POST["$field_name_id"]))	{$form_field_value=$_POST["$field_name_id"];}
		$script_text = preg_replace("/$field_name_tag/i","$form_field_value",$script_text);
			if ($DB) {echo "$d|$field_name_id|$field_name_tag|$form_field_value|<br>\n";}
		$d++;
		}
	}

$script_text = preg_replace("/\n/i","<BR>",$script_text);
$script_text = stripslashes($script_text);


//$goAgentScripts .=  "<!-- IFRAME$IFRAME -->\n";
//$goAgentScripts .=  "<!-- $script_id -->\n";
$goAgentScripts .=  "<TABLE WIDTH=100%><TR><TD>\n";
if ( ( ($IFRAME < 1) and ($ScrollDIV > 0) ) or (preg_match("/IGNORENOSCROLL/i",$script_text)) )
	{$goAgentScripts .=   "<div id=\"NewScriptContents\">";}
$goAgentScripts .=  "<center><B>$script_name</B><BR>\n";
$goAgentScripts .=  "$script_text\n";
if ( ( ($IFRAME < 1) and ($ScrollDIV > 0) ) or (preg_match("/IGNORENOSCROLL/i",$script_text)) )
	{$goAgentScripts .=  "</center></div>";}
$goAgentScripts .=  "</TD></TR></TABLE>\n";

//exit;


$apiresults = array("result" => "success", "gocampaignScript" => $goAgentScripts);


?>
