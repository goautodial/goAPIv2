<?php
    #######################################################
    #### Name: goGetAllLists.php	               ####
    #### Description: API to get all Lists             ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }

    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
	$ul = "WHERE user_group='$groupId'";  
    }

 	  $query = "SELECT list_id,list_name,list_description,(SELECT count(*) as tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) as tally,active,list_lastcalldate,campaign_id,reset_time from vicidial_lists $wherecampaigns order by list_id LIMIT $limit";
   	  $rsltv = mysqli_query($link, $query);
        	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){

			$dataListId[] =  $fresults['list_id'];
			$dataListName[] =  $fresults['list_name'];
			$dataActive[] =  $fresults['active'];
			$dataListLastcallDate[] =  $fresults['list_lastcalldate'];
			$dataTally[] =  $fresults['tally'];
			$dataCampaignId[] =  $fresults['campaign_id'];

				$apiresults = array( "result" => "success", "list_id" => $dataListId, "list_name" => $dataListName, "active" => $dataActive, "list_lastcalldate" => $dataListLastcallDate, "tally" => $dataTally, "campaign_id" => $dataCampaignId);
	}
?>
