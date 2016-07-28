<?php
   ####################################################
   #### Name: goAddMOH.php                         ####
   #### Description: API to add new moh            ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
        $moh_id = $_REQUEST['moh_id'];
        $moh_name = $_REQUEST['moh_name'];
        $user_group = $_REQUEST['user_group'];
        $active = strtoupper($_REQUEST['active']);
	$random = strtoupper($_REQUEST['random']);
        $values = $_REQUEST['item'];
	$ip_address = $_REQUEST['hostname'];
	$goUser = $_REQUEST['goUser'];


    ### Default values 
    $defActive = array("Y","N");
    $defRandom = array("Y","N");


    ### ERROR CHECKING 
        if($moh_id == null || strlen($moh_id) < 3) {
                $apiresults = array("result" => "Error: Set a value for MOH ID not less than 3 characters.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $moh_name) || $moh_name == null){
                $apiresults = array("result" => "Error: Special characters found in moh_name and must not be empty");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $moh_id)){
                $apiresults = array("result" => "Error: Special characters found in moh_id");
        } else {

                if(!in_array($active,$defActive)) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {
                if(!in_array($random,$defRandom)) {
                        $apiresults = array("result" => "Error: Default value for random is Y or N only.");
                } else {

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "WHERE user_group='$user_group'";
                        $group_type = "Multi-tenant";
                } else {
                        $ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
                        $group_type = "Default";
                }

                $query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
                $rsltv = mysqli_query($link,$query);
                $countResult = mysqli_num_rows($rsltv);

                if($countResult > 0) {
                /*
                                        $items = $values;
                                        $itemSQL = "INSERT INTO vicidial_music_on_hold SET ";
                                        foreach (explode("&",$items) as $item)
                                        {
                                                $itemX = explode("=",$item);

                                                if ($itemX[0]=="moh_id")
                                                        $moh_id = $itemX[1];

                                                $itemSQL .= $itemX[0]."='".str_replace("+"," ",$itemX[1])."',";
                                        }
                                        $itemSQL = rtrim($itemSQL,",");
                                        */
                                        $newQuery = "INSERT INTO vicidial_music_on_hold SET moh_id = '$moh_id', moh_name = '$moh_name', user_group = '$user_group', active = '$active', random = '$random';";
					$rsltv = mysqli_query($link, $newQuery);
	      



	### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Music On Hold: $moh_id','INSERT INTO vicidial_music_on_hold (moh_id,moh_name,user_group,active,random) VALUES ($moh_id,$moh_name,$user_group,$active,$random)');";
                                        $rsltvLog = mysqli_query($linkgo,$queryLog);

				        if($rsltv == false){
						$apiresults = array("result" => "Error: Add failed, check your details");
					} else {

                                                $insertQuery = "INSERT INTO vicidial_music_on_hold_files SET filename='conf',rank='1',moh_id='$moh_id';";
						$insertResult = mysqli_query($link,$insertQuery);
                                                $updateQuery = "UPDATE servers SET rebuild_conf_files='Y',rebuild_music_on_hold='Y',sounds_update='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y';";
                                          $apiresults = array("result" => "success");
					}
                   } else {
                        $apiresults = array("result" => "Error: Invalid User Group");
		   }
                                        }
                                      
}
}
}
}

?>
