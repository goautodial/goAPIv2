<?php
  #######################################################
  #### Name: goGetUserInfo.php	                     ####
  #### Description: API to get specific user	     ####
  #### Version: 0.9                                  ####
  #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
  #### Written by: Jeremiah Sebastian Samatra        ####
  ####             Demian Lizandro Biscocho          ####
  #### License: AGPLv2                               ####
  #######################################################
    
 include_once ("goFunctions.php");

 ### POST or GET Variables
 $user_id = $_REQUEST['user_id'];

 ### Check user_id if its null or empty
 if($user_id == null) { 
        $apiresults = array("result" => "Error: Set a value for User ID."); 
 } else {
        $groupId = go_get_groupid($goUser);
 }

 if (!checkIfTenant($groupId)) {
        $ul = "AND user_id='$user_id'";
 } else { 
        $ul = "AND user_id='$user_id' AND user_group='$groupId'";  
 }

 $notAdminSQL = "AND user_level != '9'";
 $query = "SELECT user, full_name, user_level, user_group, active, email, voicemail_id, phone_login, phone_pass FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ul $notAdminSQL ORDER BY user ASC LIMIT 1;";
 $rsltv = mysqli_query($link, $query);
 $fresults = mysqli_fetch_assoc($rsltv);
 $apiresults = array("result" => "success", "data" => $fresults);
 

?>
