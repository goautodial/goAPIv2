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

    $campaign_id = $_REQUEST['campaign_id'];

	$query = "SELECT
			vicidial_lists.list_id,vicidial_lists.list_name,vicidial_lists.list_description,
			(SELECT count(*) as tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) as tally,
			(SELECT count(*) as counter FROM vicidial_lists_fields WHERE list_id = vicidial_lists.list_id) as cf_count,
			vicidial_lists.active,vicidial_lists.list_lastcalldate,vicidial_lists.campaign_id,vicidial_lists.reset_time,vicidial_lists.web_form_address,
			vicidial_lists.agent_script_override,vicidial_lists.campaign_cid_override,vicidial_lists.drop_inbound_group_override,
			vicidial_list.called_since_last_reset as reset_called_lead_status
		FROM vicidial_lists
		LEFT JOIN vicidial_list
		ON vicidial_lists.list_id=vicidial_list.list_id
		WHERE vicidial_lists.campaign_id = '$campaign_id'
		GROUP BY vicidial_lists.list_id
		ORDER BY vicidial_lists.list_id;";
	$rsltv = mysqli_query($link, $query);
	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataListId[] =  $fresults['list_id'];
		$dataListName[] =  $fresults['list_name'];
		$dataActive[] =  $fresults['active'];
		$dataListLastcallDate[] =  $fresults['list_lastcalldate'];
		$dataTally[] =  $fresults['tally'];
		$dataCFCount[] =  $fresults['cf_count'];
		$dataCampaignId[] =  $fresults['campaign_id'];
		$datareset_called_lead_status[] =  $fresults['reset_called_lead_status'];
		$dataweb_form_address[] =  $fresults['web_form_address'];
		$dataagent_script_override[] =  $fresults['agent_script_override'];
		$datacampaign_cid_override[] =  $fresults['campaign_cid_override'];
		$datadrop_inbound_group_override[] =  $fresults['drop_inbound_group_override'];
		$datareset_time[] = $fresults['reset_time'];
		$datalist_desc[] = $fresults['list_description'];
		
		$apiresults = array(
			"result" => "success",
			"list_id" => $dataListId,
			"list_name" => $dataListName,
			"active" => $dataActive,
			"list_lastcalldate" => $dataListLastcallDate,
			"tally" => $dataTally,
			"cf_count" => $dataCFCount,
			"campaign_id" => $dataCampaignId,
			"reset_called_lead_status" => $datareset_called_lead_status,
			"web_form_address" => $dataweb_form_address,
			"agent_script_override" => $dataagent_script_override,
			"campaign_cid_override" => $datacampaign_cid_override,
			"drop_inbound_group_override" => $datadrop_inbound_group_override,
			"reset_time" => $datareset_time,
			"list_description" => $datalist_desc
		);
	}
?>
