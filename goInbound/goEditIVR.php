<?php
   ####################################################
   #### Name: goEditCampaign.php                   ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
         $menu_name = $_REQUEST['menu_name'];
         $menu_prompt = $_REQUEST['menu_prompt'];
         $menu_timeout = $_REQUEST['menu_timeout'];
         $menu_timeout_prompt = $_REQUEST['menu_timeout_prompt'];
         $menu_invalid_prompt = $_REQUEST['menu_invalid_prompt'];
         $menu_repeat = $_REQUEST['menu_repeat'];
         $menu_time_check = $_REQUEST['menu_time_check'];
         $call_time_id = $_REQUEST['call_time_id'];
         $track_in_vdac = $_REQUEST['track_in_vdac'];
         $tracking_group = $_REQUEST['tracking_group'];
         $custom_dialplan_entry = $_REQUEST['custom_dialplan_entry'];
         $menu_id = $_REQUEST['menu_id'];

        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
	 $values = $_REQUEST['items'];
   //menu_name, menu_prompt, menu_timeout, menu_timeout_prompt, menu_invalid_prompt, menu_repeat, menu_time_check, call_time_id, track_in_vdac, tracking_group, custom_dialplan_entry, menu_id
    ### Default values 
    $defActive = array("Y","N");
    $defDialMethod = array("MANUAL","RATIO","ADAPT_HARD_LIMIT","ADAPT_TAPERED","ADAPT_AVERAGE","INBOUND_MAN"); 
    

#############################
        if($menu_id == null) {
                $apiresults = array("result" => "Error: Set a value for menu ID.");
        } else {

        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_name)){
                $apiresults = array("result" => "Error: Special characters found in menu_name");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬-]/', $menu_prompt)){
                $apiresults = array("result" => "Error: Special characters found in menu_prompt");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_timeout)){
                $apiresults = array("result" => "Error: Special characters found in menu_timeout");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬-]/', $menu_timeout_prompt)){
                $apiresults = array("result" => "Error: Special characters found in menu_timeout_prompt");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬-]/', $menu_invalid_prompt)){
                $apiresults = array("result" => "Error: Special characters found in menu_invalid_prompt");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_repeat)){
                $apiresults = array("result" => "Error: Special characters found in menu_repeat");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $call_time_id)){
                $apiresults = array("result" => "Error: Special characters found in call_time_id");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $tracking_group)){
                $apiresults = array("result" => "Error: Special characters found in tracking_group");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $custom_dialplan_entry)){
                $apiresults = array("result" => "Error: Special characters found in custom_dialplan_entry");
        } else {


                if($menu_time_check < 0 && $menu_time_check != null || $menu_time_check > 1 && $menu_time_check != null) {
                        $apiresults = array("result" => "Error: menu_time_check Value should be 0 or 1");
                } else {
                if($track_in_vdac < 0 && $track_in_vdac != null || $track_in_vdac > 1 && $track_in_vdac != null) {
                        $apiresults = array("result" => "Error: track_in_vdac Value should be 0 or 1");
                } else {


                  $stmtCheck = "SELECT menu_id from vicidial_call_menu where menu_id='$menu_id';";
                  $queryCheck =  mysqli_query($link, $stmtCheck);
                  $row = mysqli_num_rows($queryCheck);
                              while($fresults = mysqli_fetch_array($queryCheck, MYSQLI_ASSOC)){
                                        $datamenu_name = $fresults['menu_name'];
					$datamenu_prompt = $fresults['menu_prompt'];
					$datamenu_timeout = $fresults['menu_timeout'];
					$datamenu_timeout_prompt = $fresults['menu_timeout_prompt'];
					$datamenu_invalid_prompt = $fresults['menu_invalid_prompt'];
					$datamenu_repeat = $fresults['menu_repeat'];
					$datamenu_time_check = $fresults['menu_time_check'];
					$datacall_time_id = $fresults['call_time_id'];
					$datatrack_in_vdac = $fresults['track_in_vdac'];
					$datatracking_group = $fresults['tracking_group'];
					$datacustom_dialplan_entry = $fresults['custom_dialplan_entry'];
					$datamenu_id = $fresults['menu_id'];
                                        
                                }
                  if ($row <= 0) {
                           $apiresults = array("result" => "Error: CALL MENU NOT FOUND");
                           //$message = "CALL MENU NOT ADDED - there is already a CALL MENU in the system with this ID\n";
                  } else {

		if($datamenu_id != null){
			/*	 $items = $values;
                                foreach (explode("&",$items) as $item)
                                {
                                        list($var,$val) = explode("=",$item,2);
                                        if (strlen($val) > 0)
                                        {

                                                if ($var!="menu_id")
                                                        $itemSQL .= "$var='".str_replace('+',' ',mysql_real_escape_string($val))."', ";

                                                if ($var=="menu_id")
                                                        $callmenu_data="$val";

                                        }
                                }
                                $itemSQL = rtrim($itemSQL,', ');
                                */
                                if($menu_name == null){ $menu_name = $datamenu_name;}
                                if($menu_prompt == null){ $menu_prompt = $datamenu_prompt;}
                                if($menu_timeout == null){ $menu_timeout = $datamenu_timeout;}
                                if($menu_timeout_prompt == null) {$menu_timeout_prompt = $datamenu_timeout_prompt;}
                                if($menu_invalid_prompt == null){$menu_invalid_prompt = $datamenu_invalid_prompt;}
                                if($menu_repeat == null){ $menu_repeat = $datamenu_repeat;}
                                if($menu_time_check == null) { $menu_time_check = $datamenu_time_check;}
                                if($call_time_id == null){ $call_time_id = $datacall_time_id;}
                                if($track_in_vdac == null){ $track_in_vdac = $datatrack_in_vdac;}
                                if($tracking_group == null) {$tracking_group = $datatracking_group;}
                                if($custom_dialplan_entry == null) { $custom_dialplan_entry = $datacustom_dialplan_entry;}
                                $query = "UPDATE vicidial_call_menu SET menu_name = '$menu_name', menu_prompt = '$menu_prompt', menu_timeout = '$menu_timeout', menu_timeout_prompt = '$menu_timeout_prompt', menu_invalid_prompt = '$menu_invalid_prompt', menu_repeat = '$menu_repeat', menu_time_check = '$menu_time_check', call_time_id = '$call_time_id', track_in_vdac = '$track_in_vdac', tracking_group = '$tracking_group', custom_dialplan_entry = '$custom_dialplan_entry' WHERE menu_id='$menu_id';";
                                $resultQuery = mysqli_query($link, $query);


			//	$queryUpdate = "UPDATE vicidial_call_menu SET menu_name='$menu_name',  menu_prompt='$menu_prompt',  menu_timeout='$menu_timeout',  menu_timeout_prompt='$menu_timeout_prompt',  menu_invalid_prompt='$menu_invalid_prompt',  menu_repeat='$menu_repeat',  menu_time_check='$menu_time_check',  call_time_id='$call_time_id',  track_in_vdac='$track_in_vdac',  tracking_group='$tracking_group',  custom_dialplan_entry='$custom_dialplan_entry' WHERE menu_id='$menu_id';";


        ### Admin logs
//menu_name, menu_prompt, menu_timeout, menu_timeout_prompt, menu_invalid_prompt, menu_repeat, menu_time_check, call_time_id, track_in_vdac, tracking_group, custom_dialplan_entry, menu_id
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Call Nenu ID $menu_id','UPDATE vicidial_call_menu SET menu_name=$menu_name,  menu_prompt=$menu_prompt,  menu_timeout=$menu_timeout,  menu_timeout_prompt=$menu_timeout_prompt,  menu_invalid_prompt=$menu_invalid_prompt,  menu_repeat=$menu_repeat,  menu_time_check=$menu_time_check,  call_time_id=$call_time_id,  track_in_vdac=$track_in_vdac,  tracking_group=$tracking_group,  custom_dialplan_entry=$custom_dialplan_entry WHERE menu_id=$callmenu_data;')";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


					$apiresults = array("result" => "success");
				   } else {
					$apiresults = array("result" => "Error: Failed to update");
					}

				}
	}

}}}}}}}}}
}
}

#############################
?>
