<?php
   ####################################################
   #### Name: goEditMOH.php	                   ####
   #### Description: API to edit specific MOH      ####
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
        $active = $_REQUEST['active'];
        $random = $_REQUEST['random'];
        $values = $_REQUEST['item'];
	$filename = $_REQUEST['filename'];
	$ranks = $_REQUEST['rank'];   
    ### Default values 
    $defActive = array("Y","N");
    $defRandom = array("N","Y");
	
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

    ### ERROR CHECKING ...
      if($moh_id == null) { 
                $apiresults = array("result" => "Error: Set a value for MOH ID."); 
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $moh_name)){
                $apiresults = array("result" => "Error: Special characters found in moh_name");
        } else {
                if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {
                if(!in_array($random,$defRandom) && $random != null) {
                        $apiresults = array("result" => "Error: Default value for random is Y or N only.");
                } else {
		
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "WHERE user_group='$user_group'";
			$ulMoh = "AND moh_id='$moh_id'";
                } else {
                        $ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
			$ulMoh = "AND moh_id='$moh_id' AND user_group='$groupId'";
                }


                $queryMoh = "SELECT moh_id, moh_name, active, random, user_group FROM vicidial_music_on_hold WHERE remove='N' $ulMoh ORDER BY moh_id LIMIT 1;";
                $rsltvMoh = mysqli_query($link, $queryMoh);
                				while($fresults = mysqli_fetch_array($rsltvMoh, MYSQLI_ASSOC)){
					$datamoh_id = $fresults['moh_id'];
					$datamoh_name = $fresults['moh_name'];
					$dataactive = $fresults['active'];
					$datarandom = $fresults['random'];
					$datauser_group = $fresults['user_group'];
				  
				}
                $countMoh = mysqli_num_rows($rsltvMoh);

                if($countMoh > 0) {
		

		if($user_group !== null){	
                $queryCheck = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
                $resultCheck = mysqli_query($link, $queryCheck);
                $countResult = mysqli_num_rows($resultCheck);

                if($countResult == 0 && $filename == NULL) {
		$apiresults = array("result" => "Error: User Group doesn't exist.");
		}}
/*
                                        $items = $values;
                                        $affected_rows = 0;
                                        $itemSQL = "UPDATE vicidial_music_on_hold SET ";
                                        foreach (explode("&",$items) as $item)
                                        {
                                                $itemX = explode("=",$item);

                                                if ($itemX[0]=="moh_id"){
                                                        $moh_id = $itemX[1];}

                                                if ($itemX[0]=="moh_name" || $itemX[0]=="active" || $itemX[0]=="user_group" || $itemX[0]=="random")
                                                {
                                                        if (strlen($itemX[1])>0)
                                                        {
                                                                $itemSQL .= $itemX[0]."='".str_replace("+"," ",$itemX[1])."',";
                                                        }
                                                } else {
                                                        if (($itemX[0]!="moh_id" && $itemX[0]!="filename") && strlen($itemX[1])>0)
                                                        {
                                                                $fileSQL = "UPDATE vicidial_music_on_hold_files SET rank='".$itemX[1]."' WHERE moh_id='$moh_id' AND filename='".$itemX[0]."'";
								$newQuery = mysqli_query($link, $fileSQL);
								$affected_rows++;
                                                        }

                                                        if ($itemX[0]=="filename" && strlen($itemX[1])>0)
                                                        {

     						           $groupId = go_get_groupid($goUser);
                					   if (!checkIfTenant($groupId)) {
                					        $ul = "WHERE filename='$filename'";
                					   } else {
                					        $ul = "WHERE filename='$filename' AND user_group='$groupId'";
                					   }
						                $queryExist = "SELECT filename FROM vicidial_music_on_hold_files $ul ORDER BY user_group LIMIT 1;";
                						$rsltv = mysqli_query($link, $query);
                						$countResult = mysqli_num_rows($rsltv);

                						if($countResult == 0) {
								                $apiresults = array("result" => "Error: Audio File Doesn't exits");
								} else {
                                                                $query = "SELECT count(*) as cnt FROM vicidial_music_on_hold_files WHERE moh_id='$moh_id';";
								$rsltv = mysqli_query($link, $query);
								$rows = mysqli_num_rows($rsltv);
                                                                $ranks = $rows + 1;
                                                                $newFileSQL = "INSERT INTO vicidial_music_on_hold_files SET filename='".$itemX[1]."',rank='$ranks',moh_id='$moh_id';";
                                                                $newQuery = mysqli_query($link, $newFileSQL);
								$affected_rows++;
								}
                                                        }
                                                }
                                        }
                                        $itemSQL = rtrim($itemSQL,",");
                                        $itemSQL .= " WHERE moh_id='$moh_id';";
                                        $newQuery1 = $itemSQL;
                                        
					$rsltv1 = mysqli_query($link, $newQuery1);
*/


			if($filename != null){
			$queryFiles = "INSERT INTO vicidial_music_on_hold_files SET filename='$filename', rank='$rank', moh_id='$moh_id';";
			$rsltvFILES = mysqli_query($link, $queryFiles);
			}
			if($moh_name == null){$moh_name = $datamoh_name;}
			if($active == null){$active = $dataactive;}
			if($user_group == null){$user_group = $datauser_group;}
			if($random == null){$random = $datarandom;}

                        $queryMOH = "UPDATE vicidial_music_on_hold SET moh_name='$moh_name', active='$active', user_group='$user_group', random='$random' WHERE moh_id='$moh_id';";
                        $rsltv1 = mysqli_query($link, $queryMOH);
                        

					if($rsltv1 == false){
						$apiresults = array("result" => "Error: Try updating Moh Again");
					} else {
						$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified Music On-Hold: $moh_id", $log_group, $queryMOH);
						
						$apiresults = array("result" => "success");
					$affected_rows++;
                                        if ($affected_rows)
                                        {
                                                $newQuery2 = "UPDATE servers SET rebuild_conf_files='Y',rebuild_music_on_hold='Y',sounds_update='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y';";
						$rsltv2 = mysqli_query($link, $newQuery2);
                                                $apiresults = array("result" => "success");
                                        } else {
                                                $apiresults = array("result" => "Error: Try updating Moh Again");
                                        }
					}

                                       
			} else {
				$apiresults = array("result" => "Error: MOH doesn't exist");
				}
}}}
}
?>
