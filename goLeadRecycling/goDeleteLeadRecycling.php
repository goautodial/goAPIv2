<?php
  /***************************************************
  **** Name: goDeleteLeadRecycling.php            ****
  **** Description: API to delete Lead Recycling  ****
  **** Version: 4.0                               ****
  **** Copyright: GOAutoDial Ltd. (c) 2016-2017   ****
  **** Written by: Alexander Jim Abenoja          ****
  **** License: AGPLv2                            ****
  ****************************************************/
    include_once("../goFunctions.php");
    
    // POST or GET Variables
        $recycle_id = mysqli_real_escape_string($link, $_REQUEST['recycle_id']);
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
    
    // Check Voicemail ID if its null or empty
    if(empty($session_user) || empty($recycle_id)) {
		$err_msg = error_handle("40001", "recycle_id or session_user");
          $apiresults = array("code" => "40001", "result" => $err_msg);
	} else {
        $groupId = go_get_groupid($session_user);
        $get_campaign = mysqli_query($link, "SELECT campaign_id FROM recycle_id = '$recycle_id';");
        $fetch_campaign = mysqli_fetch_array($get_campaign);
        $campaign_id = $fetch_campaign['campaign_id'];
        $check_usergroup = go_check_usergroup_campaign($groupId, $campaign_id);
        $confirmed_exist = 0;
        if($check_usergroup > 0){
            $arr_id = explode(",",$recycle_id);

            for($i=0; $i<count($arr_id);$i++){
                $id = $arr_id[$i];
                $check = "SELECT recycle_id FROM vicidial_lead_recycle WHERE recycle_id = '$id';";
                $query_check = mysqli_query($link, $check);
                $num_check = mysqli_num_rows($query_check);
                $confirmed_exist = $confirmed_exist + $num_check;

                $deleteQuery = "DELETE FROM vicidial_lead_recycle WHERE recycle_id = '$id';"; 
                $deleteResult = mysqli_query($link,$deleteQuery);
                if($deleteResult)
                    $deleted_id[] = $id;

                $log_id = log_action($linkgo, 'DELETE', $session_user, $ip_address, "Deleted Lead Recycling ID: $d", $groupId, $deleteQuery);
            }
    		if((count($deleted_id) === count($arr_id)) && $confirmed_exist === count($deleted_id)){
                $imploded_ids = implode(",", $deleted_id);
                if(empty($imploded_ids))$imploded_ids=0;
    			$apiresults = array("result" => "success", "Deleted Lead Recycles:" => $imploded_ids);
    		} else {
                $imploded_ids = implode(",", $deleted_id);
                if(empty($imploded_ids))$imploded_ids=0;
    			$apiresults = array("result" => "Error: Some IDs are not deleted because they may not exist.");
    		}
        }else{
            $apiresults = array("result" => "Error: Current user ".$session_user." doesn't have enough permission to access this feature");
        }
	}//end
?>
