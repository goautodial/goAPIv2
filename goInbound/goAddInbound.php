<?php
   ####################################################
   #### Name: goAddInbound.php                     ####
   #### Description: API to edit specific ingroup  ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeemiah Sebastian V. Samatra   ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
	### POST or GET Variables
        $group_id = mysqli_real_escape_string($link, $_REQUEST['group_id']);
        $group_name = mysqli_real_escape_string($link, $_REQUEST['group_name']);
        $group_color = mysqli_real_escape_string($link,$_REQUEST['group_color']);
        $active =  mysqli_real_escape_string($link,$_REQUEST['active']);
        $web_form_address =  mysqli_real_escape_string($link,$_REQUEST['web_form_address']);
        $voicemail_ext =  mysqli_real_escape_string($link,$_REQUEST['voicemail_ext']);
        $next_agent_call =  mysqli_real_escape_string($link,$_REQUEST['next_agent_call']);
        $fronter_display =  mysqli_real_escape_string($link,$_REQUEST['fronter_display']);
        $ingroup_script =  mysqli_real_escape_string($link,$_REQUEST['ingroup_script']);
        $get_call_launch = $_REQUEST['get_call_launch'];
        $web_form_address_two = ""; //$_REQUEST['web_form_address_two'];
        $start_call_url = ""; //$_REQUEST['start_call_url'];
        $dispo_call_url = ""; //$_REQUEST['dispo_call_url'];
        $add_lead_url = ""; //$_REQUEST['add_lead_url'];
        $uniqueid_status_prefix = ""; // $_REQUEST['uniqueid_status_prefix'];
        $call_time_id = ""; // $_REQUEST['call_time_id'];
        $user_group = $_REQUEST['user_group'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
	//$values = $_REQUEST['items'];


    ### Default values 
    $defActive = array("Y","N");
    $deffronter_display = array("Y","N");
    $defget_call_launch = array('NONE','SCRIPT','WEBFORM','WEBFORMTWO','FORM','EMAIL'); 
    $defnext_agent_call = array('fewest_calls_campaign','longest_wait_time','ring_all','random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank','fewest_calls');



#####################

        if($group_id == null) {
                $apiresults = array("result" => "Error: Set a value for Group ID.");
        } else {
                if($web_form_address == null) {
                $apiresults = array("result" => "Error: Set a value for web_form_address.");
        } else {
                if($voicemail_ext == null) {
                $apiresults = array("result" => "Error: Set a value for voicemail_ext.");
        } else {
                if($ingroup_script == null) {
                $apiresults = array("result" => "Error: Set a value for ingroup_script.");
        } else {


        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_id)){
                $apiresults = array("result" => "Error: Special characters found in group_id");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name) || $group_name == null){
                $apiresults = array("result" => "Error: Special characters found in group_name and must not be empty");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_color) || $group_color == null){
                $apiresults = array("result" => "Error: Special characters found in group_color must not be empty");
        } else {

         if(!in_array($active,$defActive)) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {
                if(!in_array($fronter_display,$deffronter_display)) {
                        $apiresults = array("result" => "Error: Default value for fronter_display is Y or N only.");
                } else {
                if(!in_array($get_call_launch,$defget_call_launch)) {
                        $apiresults = array("result" => "Error: Default value for get_call_launch is NONE, SCRIPT, WEBFORM, WEBFORMTWO, FORM or EMAIL only.");
                } else {
                if(!in_array($next_agent_call,$defnext_agent_call)) {
                        $apiresults = array("result" => "Error: Default value for next_agent_call is fewest_calls_campaign, longest_wait_time, ring_all, random, oldest_call_start, oldest_call_finish, overall_user_level, inbound_group_rank, campaign_rank or fewest_calls only.");
                } else {
 
                $groupId = go_get_groupid($goUser);
                if (!checkIfTenant($groupId)) {
                        $ulug = "WHERE user_group='$user_group'";
                } else {
                        $ulug = "WHERE user_group='$user_group' AND user_group='$groupId'";
                }

                $query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ulug ORDER BY user_group LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
                $countResult = mysqli_num_rows($rsltv);

                if($countResult > 0) {

		

                  $stmt = "SELECT value FROM vicidial_override_ids where id_table='vicidial_inbound_groups' and active='1';";
                  $query = mysqli_query($link, $stmt);
                  $voi_ct = mysqli_num_rows($query);

                  if ($voi_ct > 0) {
                          //$row = $query->row(); 
                          //$menu_id = ($row->value + 1);
                          $stmt = "UPDATE vicidial_override_ids SET value='$group_id' where id_table='vicidial_inbound_groups' and active='1';";
                        $result = mysqli_query($link, $stmt);
                 }
                  $stmtCheck = "SELECT group_id from vicidial_inbound_groups where group_id='$group_id';";
                  $queryCheck =  mysqli_query($link, $stmtCheck);
                  $row = mysqli_num_rows($queryCheck);

                  if ($row > 0) {
                          $apiresults = array("result" => "GROUP NOT ADDED - there is already a Inbound in the system with this ID\n");
                  } else {


               $stmtMe="SELECT campaign_id from vicidial_campaigns where campaign_id='$group_id';";
                           $queryMe = mysqli_query($link, $stmtMe);
                           $count = mysqli_num_rows($queryMe);

                           if ($count > 0) {
                                        $apiresults = array("result" => "<br>GROUP NOT ADDED - there is already a campaign in the system with this ID\n");
                           } else {
                    if ( (strlen($group_id) < 2) || (strlen($group_name) < 2)  || (strlen($group_color) < 2) || (strlen($group_id) > 20) || (eregi(' ',$group_id)) or (eregi("\-",$group_id)) || (eregi("\+",$group_id)) ) {
                         $apiresults = array("result" => "<br>GROUP NOT ADDED - Please go back and look at the data you entered\n <br>Group ID must be between 2 and 20 characters in length and contain no ' -+'.\n <br>Group name and group color must be at least 2 characters in length\n");
                                        } else {
                                                 $stmtInsert = "INSERT INTO vicidial_inbound_groups (group_id,group_name,group_color,active,web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,web_form_address_two,start_call_url,dispo_call_url,add_lead_url,uniqueid_status_prefix,call_time_id,user_group) values('$group_id','$group_name','$group_color','$active','$web_form_address','$voicemail_ext','$next_agent_call','$fronter_display','$script_id','$get_call_launch','','','','','$accounts','24hours','$user_group');";
                                                 $query = mysqli_query($link, $stmtInsert);

                                $resultQueryAddCheck = "SELECT group_id from vicidial_inbound_groups where group_id='$group_id';";
                                $resultCheck = mysqli_query($link, $resultQueryAddCheck);
                                $countAdd = mysqli_num_rows($resultCheck);
                                                 

                                                 $stmtA="INSERT INTO vicidial_campaign_stats (campaign_id) values('$group_id');";
                                                 $query = mysqli_query($link, $stmtA);

                                $resultQueryAddCheck1 = "SELECT campaign_id from vicidial_campaign_stats where campaign_id='$group_id';";
                                $resultCheck1 = mysqli_query($link, $resultQueryAddCheck1);
                                $countAdd1 = mysqli_num_rows($resultCheck1);
$apiresults = array("result" => "success", "rerunnyayata" => "yehey");
		  if ($countAdd1 > 0 && $countAdd > 0) {

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New In-group $group_id','INSERT INTO vicidial_inbound_groups (group_id, group_name, group_color, active, web_form_address, voicemail_ext, next_agent_call, fronter_display, ingroup_script, get_call_launch, web_form_address_two, start_call_url,  dispo_call_url, add_lead_url, uniqueid_status_prefix, call_time_id, user_group) VALUES ($group_id, $group_name, $group_color, $active, $web_form_address, $voicemail_ext, $next_agent_call, $fronter_display, $ingroup_script, $get_call_launch, $web_form_address_two, $start_call_url,  $dispo_call_url, $add_lead_url, $uniqueid_status_prefix, $call_time_id, $user_group)');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);

                           $apiresults = array("result" => "success");
                  } else {
                           $apiresults = array("result" => "GROUP NOT ADDED - Check the name and value you type\n");
                  }
                                                 //$this->commonhelper->auditadmin('ADD',"Added New In-group ID: $group_id","$stmt\n$stmtA");
                                        }
                           }
                  }

} else  {
	$apiresults = array("result" => "INVALID User Group");
	}
}}}}
}
}
}
}}}
}


?>
