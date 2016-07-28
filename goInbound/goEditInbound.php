<?php
   ####################################################
   #### Name: goEditInbound.php                    ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jerico James Milo              ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("goFunctions.php");
    ### POST or GET Variables
        $group_id = $_REQUEST['group_id'];
        $group_name = $_REQUEST['group_name'];
        $group_color = $_REQUEST['group_color'];
        $active = $_REQUEST['active'];
        $web_form_address = $_REQUEST['web_form_address'];
        $next_agent_call = $_REQUEST['next_agent_call'];
        $fronter_display = $_REQUEST['fronter_display'];
        $ingroup_script = $_REQUEST['ingroup_script'];
        //$get_call_launch = $_REQUEST['get_call_launch'];
        //$ignore_list_script_override = $_REQUEST['ignore_list_script_override'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
	$queue_priority = $_REQUEST['queue_priority'];
	//$values = $_REQUEST['items'];
 //group_id, group_name, group_color, active, web_form_address, next_agent_call, fronter_display, ingroup_script, queue_priority

    ### Default values 
    $defActive = array("Y","N");
    $deffronter_display = array("Y","N");
    $defget_call_launch = array('NONE','SCRIPT','WEBFORM','WEBFORMTWO','FORM','EMAIL');
    $defnext_agent_call = array('fewest_calls_campaign','longest_wait_time','ring_all','random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank','fewest_calls');

####################################


/* start lists */

        if($group_id == null) {
                $apiresults = array("result" => "Error: Set a value for Inbound ID.");
        } else {

                    if ( (strlen($group_name) < 2 && $group_name != null)  || (strlen($group_color) < 2 && $group_color != null) ) {
                         $apiresults = array("result" => "<br>GROUP NOT ADDED - Please go back and look at the data you entered\n <br>Group name and group color must be at least 2 characters in length\n");
                     } else {

                if($queue_priority < -99 || $queue_priority > 99) {
                        $apiresults = array("result" => "Error: queue_priority Value should be in between -99 and 99");
                } else {

        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_id)){
                $apiresults = array("result" => "Error: Special characters found in group_id");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name)){
                $apiresults = array("result" => "Error: Special characters found in group_name");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_color)){
                $apiresults = array("result" => "Error: Special characters found in group_color");
        } else {

         if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {
                if(!in_array($fronter_display,$deffronter_display) && $fronter_display != null) {
                        $apiresults = array("result" => "Error: Default value for fronter_display is Y or N only.");
                } else {
                if(!in_array($get_call_launch,$defget_call_launch) && $get_call_launch != null) {
                        $apiresults = array("result" => "Error: Default value for get_call_launch is NONE, SCRIPT, WEBFORM, WEBFORMTWO, FORM or EMAIL only.");
                } else {
                if(!in_array($next_agent_call,$defnext_agent_call) && $next_agent_call != null) {
                        $apiresults = array("result" => "Error: Default value for next_agent_call is fewest_calls_campaign, longest_wait_time, ring_all, random, oldest_call_start, oldest_call_finish, overall_user_level, inbound_group_rank, campaign_rank or fewest_calls only.");
                } else {


                  $stmtCheck = "SELECT group_id from vicidial_inbound_groups where group_id='$group_id';";
                  $queryCheck =  mysqli_query($link, $stmtCheck);
                  $row = mysqli_num_rows($queryCheck);
                                                while($fresults = mysqli_fetch_array($queryCheck, MYSQLI_ASSOC)){
                                        $datagroup_id = $fresults['group_id'];
                                        $datagroup_name = $fresults['group_name'];
                                        $datagroup_color = $fresults['group_color'];
                                        $dataactive = $fresults['active'];
                                        $dataweb_form_address = $fresults['web_form_address'];
                                        $datanext_agent_call = $fresults['next_agent_call'];
                                        $datafronter_display = $fresults['fronter_display'];
                                        $dataingroup_script = $fresults['ingroup_script'];
                                        $dataqueue_priority = $fresults['queue_priority'];
	
                                        
                                }
//group_id, group_name, group_color, active, web_form_address, next_agent_call, fronter_display, ingroup_script, queue_priority
                  if ($row <= 0) {
                          $apiresults = array("result" => "GROUP NOT MODIFIED - Inbound doesn't exist");
                  } else {

                        /*        $items = $values;
                                foreach (explode("&",$items) as $item)
                                {
                                        list($var,$val) = explode("=",$item,2);
                                        if (strlen($val) > 0)
                                        {

                                                if ($var!="group_id")
                                                        $itemSQL .= "$var='".str_replace('+',' ',mysql_real_escape_string($val))."', ";

                                                if ($var=="group_id")
                                                        $groupid_data="$val";

                                        }
                                }
                                $itemSQL = rtrim($itemSQL,', ');
                                */
			if($datagroup_id != NULL){
			
			  if($group_name == null){ $group_name = $datagroup_name;}
			  if($group_color == null){ $group_color = $datagroup_color;}
			  if($active == null){$active = $dataactive;}
			  if($web_form_address == null){$web_form_address = $dataweb_form_address;}
			  if($next_agent_call == null){$next_agent_call = $datanext_agent_call;}
			  if($fronter_display == null){$fronter_display = $datafronter_display;}
			  if($ingroup_script == null){$ingroup_script = $dataingroup_script;}
			  if($queue_priority == null){$queue_priority = $dataqueue_priority;}
                                $query = "UPDATE vicidial_inbound_groups SET group_id = '$group_id', group_name = '$group_name', group_color = '$group_color', active = '$active', web_form_address = '$web_form_address' , next_agent_call = '$next_agent_call', fronter_display = '$fronter_display', ingroup_script = '$ingroup_script', queue_priority = '$queue_priority' WHERE group_id='$datagroup_id';";
                                $resultQuery = mysqli_query($link, $query);

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','MODIFY IN_GROUP $group_id','UPDATE vicidial_inbound_groups SET group_id=$group_id, group_name=$group_name, group_color=$group_color, active=$active, web_form_address=$web_form_address, next_agent_call=$next_agent_call, fronter_display=$fronter_display, ingroup_script=$ingroup_script, queue_priority=$queue_priority WHERE group_id=$groupid_data;');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);



                                $apiresults = array("result" => "success");
				}
			else {
				$apiresults = array("result" => "Error: Failed to modified the Group ID");
				}

}}}}}}}		}        
}
}}
#################################
?>
