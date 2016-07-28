<?php
   ####################################################
   #### Name: goEditCampaign.php                   ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jerico James Milo              ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
//menu_id, menu_name, user_group, menu_prompt, menu_timeout, menu_timeout_prompt, menu_invalid_prompt, menu_repeat, menu_time_check, call_time_id, track_in_vdac, custom_dialplan_entry, tracking_group 
    ### POST or GET Variables
    $menu_id = $_REQUEST['menu_id'];
    $menu_name = $_REQUEST['menu_name'];
    $user_group = $_REQUEST['user_group'];
    $menu_prompt = $_REQUEST['menu_prompt'];
    $menu_timeout = $_REQUEST['menu_timeout'];
    $menu_timeout_prompt = $_REQUEST['menu_timeout_prompt'];
    $menu_invalid_prompt = $_REQUEST['menu_invalid_prompt'];
    $menu_repeat = $_REQUEST['menu_repeat'];
    $menu_time_check = $_REQUEST['menu_time_check'];
    $call_time_id = $_REQUEST['call_time_id'];
    $track_in_vdac = $_REQUEST['track_in_vdac'];
    $custom_dialplan_entry = $_REQUEST['custom_dialplan_entry'];
    $tracking_group = $_REQUEST['tracking_group'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
    $values = $_REQUEST['items'];
   
    ### Default values 
	$defmenu_time_check = array('0','1');
	$deftrack_in_vdac = array('0','1');


####################

        if($menu_id == null || strlen($menu_id) < 4) {
                $apiresults = array("result" => "Error: Set a value for Menu ID not less than 4 characters.");
        } else {
        if($menu_name == null) {
                $apiresults = array("result" => "Error: Set a value for menu_name.");
        } else {
        if($user_group == null) {
                $apiresults = array("result" => "Error: Set a value for user_group.");
        } else {
        if($menu_timeout == null) {
                $apiresults = array("result" => "Error: Set a value for menu_timeout.");
        } else {
        if($menu_repeat == null) {
                $apiresults = array("result" => "Error: Set a value for menu_repeat.");
        } else {
 //       if($menu_time_check == null) {
 //               $apiresults = array("result" => "Error: Set a value for menu_time_check.");
 //       } else {
//        if($call_time_id == null) {
//                $apiresults = array("result" => "Error: Set a value for call_time_id.");
//        } else {
  //      if($track_in_vdac == null) {
   //             $apiresults = array("result" => "Error: Set a value for track_in_vdac.");
     //   } else {
        if($tracking_group == null) {
                $apiresults = array("result" => "Error: Set a value for tracking_group.");
        } else {


        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_id)){
                $apiresults = array("result" => "Error: Special characters found in menu_id");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_name)){
                $apiresults = array("result" => "Error: Special characters found in menu_name");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_timeout)){
                $apiresults = array("result" => "Error: Special characters found in menu_timeout");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_repeat)){
                $apiresults = array("result" => "Error: Special characters found in menu_repeat");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $tracking_group)){
                $apiresults = array("result" => "Error: Special characters found in tracking_group");
        } else {






        if($user_group == null) {
                $apiresults = array("result" => "Error: Set a value for user_group.");
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




  /*              if($menu_time_check < 0 || $menu_time_check > 1) {
                        $apiresults = array("result" => "Error: menu_time_check Value should be 0 or 1");
                } else {
                if($track_in_vdac < 0 || $track_in_vdac > 1) {
                        $apiresults = array("result" => "Error: track_in_vdac Value should be 0 or 1");
                } else {
*/
                  $stmt = "SELECT value FROM vicidial_override_ids where id_table='vicidial_call_menu' and active='1';";
                  $query = mysqli_query($link, $stmt);
                  $voi_ct = mysqli_num_rows($query);

                  if ($voi_ct > 0) {
                          //$row = $query->row(); 
                          //$menu_id = ($row->value + 1);
                          $stmt = "UPDATE vicidial_override_ids SET value='$menu_id' where id_table='vicidial_call_menu' and active='1';";
                          $result = mysqli_query($link, $stmt);
                  }
                  $stmtCheck = "SELECT menu_id from vicidial_call_menu where menu_id='$menu_id';";
                  $queryCheck =  mysqli_query($link, $stmtCheck);
                  $row = mysqli_num_rows($queryCheck);
                  
                  if ($row > 0) {
			   $apiresults = array("result" => "Error: CALL MENU NOT ADDED - there is already a CALL MENU in the system with this ID");
                           //$message = "CALL MENU NOT ADDED - there is already a CALL MENU in the system with this ID\n";
                  } else {


				$queryAddIVR = "INSERT INTO vicidial_call_menu (menu_id, menu_name, user_group, menu_prompt, menu_timeout, menu_timeout_prompt, menu_invalid_prompt, menu_repeat, menu_time_check, call_time_id, track_in_vdac, custom_dialplan_entry, tracking_group) values('$menu_id', '$menu_name', '$user_group', '$menu_prompt', '$menu_timeout', '$menu_timeout_prompt', '$menu_invalid_prompt', '$menu_repeat', '$menu_time_check', '24hours', '$track_in_vdac', '$custom_dialplan_entry', '$tracking_group');";

                                $resultQueryAdd = mysqli_query($link, $queryAddIVR);

				$resultQueryAddCheck = "SELECT menu_id from vicidial_call_menu where menu_id='$menu_id';";
				$resultCheck = mysqli_query($link, $resultQueryAddCheck);
				$countAdd = mysqli_num_rows($resultCheck);
                               // $apiresults = array("result" => "success");


                           # set default entry in vicidial_callmenu_options by Franco Hora 
			   $queryDef = "INSERT INTO vicidial_call_menu_options (menu_id,option_value,option_description,option_route,option_route_value) values('$menu_id','TIMEOUT','Hangup','HANGUP','vm-goodbye');";
                          $resultQueryDef = mysqli_query($link, $queryDef); 
			   
			  $queryCount = "SELECT menu_id from vicidial_call_menu_options where menu_id='$menu_id';";
			  $resultCount = mysqli_query($link, $queryCount);
                  	  $countOpt = mysqli_num_rows($resultCount);

                  if ($countOpt > 0 && $countAdd > 0) {

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New IVR $menu_id','INSERT INTO  vicidial_call_menu SET (menu_name, menu_id, menu_prompt, menu_timeout, menu_timeout_prompt, menu_invalid_prompt, menu_repeat, menu_time_check, call_time_id, track_in_vdac, tracking_group, user_group) VALUES ($menu_name, $menu_id, $menu_prompt, $menu_timeout, $menu_timeout_prompt, $menu_invalid_prompt, $menu_repeat, $menu_time_check, $call_time_id, $track_in_vdac, $tracking_group, $user_group);');";

                                        $rsltvLog = mysqli_query($linkgo, $queryLog);

			   $apiresults = array("result" => "success");
                  } else {
                           $apiresults = array("result" => "CALL MENU NOT ADDED - there is already a CALL MENU in the system with this ID\n"); 
		  }
                  }
//}}


} else {
	$apiresults = array("result" => "Error: INVALID USER GROUP");
}
}}}}}
}}}}}

}
}










###################3
?>
