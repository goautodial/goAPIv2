<?php
    #######################################################
    #### Name: goGetAllLists.php	               ####
    #### Description: API to get all Lists             ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");

    $groupId = go_get_groupid($goUser);
	$user_group = $_REQUEST['user_group'];
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
	$ul = "WHERE user_group='$groupId'";  
    }
	
	if (isset($user_group) && strlen($user_group) > 0) {
		$query = "SELECT TRIM(allowed_campaigns) AS allowed_camps FROM vicidial_user_groups WHERE user_group='$user_group';";
		$rsltv = mysqli_query($link, $query);
		$frslt = mysqli_fetch_assoc($rsltv);
		
		if (!preg_match("/ALL-CAMPAIGNS/", $frslt['allowed_camps'])) {
			$allowed_camps = explode(' ', $frslt['allowed_camps']);
			$allowed_campaigns = "";
			if (count($allowed_camps) > 0) {
				$allowed_campaigns = ($ul !== '') ? "AND campaign_id IN (" : "WHERE campaign_id IN (";
				foreach ($allowed_camps as $camp) {
					if ($camp !== "-") {
						$allowed_campaigns .= "'{$camp}',";
					}
				}
				$allowed_campaigns = rtrim($allowed_campaigns, ",");
				$allowed_campaigns .= ")";
			}
		}
	}

	$query = "SELECT list_id,list_name,list_description,(SELECT count(*) as tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) as tally,(SELECT count(*) as counter FROM vicidial_lists_fields WHERE list_id = vicidial_lists.list_id) as cf_count, active,list_lastcalldate,campaign_id,reset_time from vicidial_lists $allowed_campaigns order by list_id;";
	$rsltv = mysqli_query($link, $query);
	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataListId[] =  $fresults['list_id'];
		$dataListName[] =  $fresults['list_name'];
		$dataActive[] =  $fresults['active'];
		$dataListLastcallDate[] =  $fresults['list_lastcalldate'];
		$dataTally[] =  $fresults['tally'];
		$dataCFCount[] =  $fresults['cf_count'];
		$dataCampaignId[] =  $fresults['campaign_id'];
	}
	
	#get next list id
	$query2 = "SELECT list_id from vicidial_lists WHERE list_id NOT IN ('999', '998') order by list_id;";
	$rsltv2 = mysqli_query($link, $query2);
	while($fetch_lists = mysqli_fetch_array($rsltv2, MYSQLI_ASSOC)){
		$lists[] =  $fetch_lists['list_id'];
	}
	
	$max_list = max($lists);
	$min_list = min($lists);
	
	if($max_list >= 99999999){
		for($i=1;$i < $max_list;$i++){
			if(!in_array($i, $lists['list_id'])){
				$next_list = $i;
				$i = $max_list;
			}
		}
	}else{
		$next_list = $max_list + 1;
	}
	
	$apiresults = array("result" => "success","list_id" => $dataListId,"list_name" => $dataListName,"active" => $dataActive, "list_lastcalldate" => $dataListLastcallDate,"tally" => $dataTally,"cf_count" => $dataCFCount,"campaign_id" => $dataCampaignId, "next_listID" => $next_list);
?>
