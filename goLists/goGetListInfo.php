<?php
    #######################################################
    #### Name: goGetListInfo.php	               ####
    #### Description: API to get specific List	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jermiah Sebastian Samatra         ####
    #### License: AGPLv2                               ####
    #######################################################
    include "goFunctions.php";
    
    ### POST or GET Variables
    $list_id = $_REQUEST['list_id'];
    
	if($list_id == null) { 
		$apiresults = array("result" => "Error: Set a value for List ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE list_id='$list_id'";
    		} else { 
			$ul = "WHERE list_id='$list_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT list_id,list_name,list_description,(SELECT count(*) as tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) as tally,active,list_lastcalldate,campaign_id,reset_time from vicidial_lists $ul order by list_id LIMIT 1";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			        $dataListId[] =  $fresults['list_id'];
			        $dataListName[] =  $fresults['list_name'];
					 $dataDesc[] =  $fresults['list_description'];
			        $dataActive[] =  $fresults['active'];
			        $dataListLastcallDate[] =  $fresults['list_lastcalldate'];
			        $dataTally[] =  $fresults['tally'];
			        $dataCampaignId[] =  $fresults['campaign_id'];

				        $apiresults = array( "result" => "success", "list_id" => $dataListId, "list_name" => $dataListName, "list_desc" => $dataDesc, "active" => $dataActive, "list_lastcalldate" => $dataListLastcallDate, "tally" => $dataTally, "campaign_id" => $dataCampaignId);
			}
		} else {
			$apiresults = array("result" => "Error: List doesn't exist.");
		}
	}
?>
