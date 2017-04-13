<?php
    #######################################################
    #### Name: goGetLeads.php     	               ####
    #### Description: API to get Leads                 ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Warren Ipac Briones               ####
    #### Modified by: Alexander Jim Abenoja	       ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");

    function remove_empty($array) {
    	return array_filter($array, '_remove_empty_internal');
    }

    function _remove_empty_internal($value) {
  	return !empty($value) || $value === 0;
    }

    if (isset($_REQUEST["draw"])) { $draw = $_REQUEST["draw"]; }
        else { $draw = 1; }
    if (isset($_REQUEST["start"])) { $start = $_REQUEST["start"]; }
        else { $start = 0; }
    if (isset($_REQUEST["length"])) { $length = $_REQUEST["length"]; }
        //else { $length = 100; }    
    if (isset($_REQUEST["recordsTotal"])) { $recordsTotal = $_REQUEST["recordsTotal"]; }
    if (isset($_REQUEST["recordsFiltered"])) { $recordsFiltered = $_REQUEST["recordsFiltered"]; }    
    if (isset($_REQUEST['order'][0]['column'])) { $orderByColumnIndex = $_REQUEST['order'][0]['column']; }
        else { $orderByColumnIndex = 0; }     
    if (isset($_REQUEST['columns'][$orderByColumnIndex]['data'])) { $orderBy = $_REQUEST['columns'][$orderByColumnIndex]['data']; }
        else { $orderBy = "lead_id"; }
    if (isset($_REQUEST['order'][0]['dir'])) { $orderType = $_REQUEST['order'][0]['dir']; }
        else { $orderType = "DESC"; } 
    
    $userid = $_REQUEST["user_id"];    
    $goMyLimit = "LIMIT $start,$length";    

    $getAllowedCampaigns_query = "SELECT vicidial_users.user_group, vicidial_user_groups.allowed_campaigns FROM vicidial_users, vicidial_user_groups WHERE vicidial_users.user_group = vicidial_user_groups.user_group AND vicidial_users.user ='$userid'";  	
    $allowedCampaigns_result = mysqli_query($link, $getAllowedCampaigns_query);
    $allowedCampaignsFetch = mysqli_fetch_array($allowedCampaigns_result, MYSQLI_ASSOC);
    $allowedCampaigns = $allowedCampaignsFetch['allowed_campaigns'];
    
    
    // GET CUSTOMER LIST
    $customers_query = "SELECT lead_id FROM go_customers;";
    $exec_customers_query = mysqli_query($linkgo, $customers_query);
    $customers = array();
    while($fetch_customers = mysqli_fetch_array($exec_customers_query, MYSQLI_ASSOC)){
            $customers[] = $fetch_customers['lead_id'];
    }
            
    //if admin
    if(preg_match("/ALL-CAMPAIGNS/", $allowedCampaigns)){
        $queryx = sprintf("SELECT lead_id,list_id,first_name,middle_initial,last_name,phone_number,status,last_local_call_time FROM %s WHERE phone_number != '' ORDER BY %s %s limit %d , %d ", "vicidial_list" ,$orderBy,$orderType ,$start , $length);
        $returnRes = mysqli_query($link, $queryx);
        
        $queryY = "SELECT lead_id,list_id,first_name,middle_initial,last_name,phone_number,status,last_local_call_time FROM vicidial_list WHERE phone_number != '';";
        $returnReS = mysqli_query($link, $queryY);
        
        $recordsTotal = mysqli_num_rows($returnReS);
        $recordsFiltered = mysqli_num_rows($returnRes);
        
    } else { //if multiple allowed campaigns
        $multiple_campaigns = explode("-", $allowedCampaigns);
        $allowedCampaignsx = $multiple_campaigns[0];
        $allowedCampaignsx = explode(" ",$allowedCampaignsx);	
        $allowedCampaignsx = remove_empty($allowedCampaignsx);
        $allowedCampaignsx = implode("','", $allowedCampaignsx);
        $allowedCampaignsx = "'".$allowedCampaignsx."'";
        
        //get lists id from return campaigns
        $getListsID_query = "select list_id from vicidial_lists where campaign_id IN($allowedCampaignsx)";
        $listsID_result = mysqli_query($link, $getListsID_query);
        //$listsIDFetch = mysqli_fetch_array($listsID_result, MYSQLI_ASSOC);
        
        while($listsID_resultx = mysqli_fetch_array($listsID_result, MYSQLI_ASSOC)){
                $list_results .= $listsID_resultx['list_id']." ";
        }
        $fetchLists = explode(" ", $list_results);
        $fetchLists = remove_empty($fetchLists);
        $fetchLists = implode("','", $fetchLists);
        //$fetchLists = "'".$fetchLists."'";
        
        if($fetchLists != '')
                $additional_query = "AND list_id IN('".$fetchLists."')";
        else
                $additional_query = '';
        
        $queryx = sprintf("SELECT lead_id,list_id,first_name,middle_initial,last_name,phone_number,status,last_local_call_time FROM %s WHERE phone_number != '' ORDER BY %s %s limit %d , %d ", "vicidial_list" ,$orderBy,$orderType ,$start , $length);
        $returnRes = mysqli_query($link, $queryx);	
        
        $queryY = "SELECT lead_id,list_id,first_name,middle_initial,last_name,phone_number,status,last_local_call_time FROM vicidial_list WHERE phone_number != '';";
        $returnReS = mysqli_query($link, $queryY);
        
        $recordsTotal = mysqli_num_rows($returnReS);
        $recordsFiltered = mysqli_num_rows($returnRes);
    }
    
    $data = array();
    while($fresults = mysqli_fetch_array($returnRes)){
        array_push($data, $fresults);
    }
    if ($search_customers) {
            $apiresults = array("draw" => intval($draw), "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, "data" => $data, "query" => $queryx, "result" => "success");
    } else {
            $apiresults = array("draw" => intval($draw), "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, "data" => $data, "query" => $queryx, "result" => "success");            
    }
?>
