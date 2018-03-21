<?php
    #######################################################
    #### Name: goGetMainTableData.php 	               ####
    #### Description: API to get Main Table data       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Noel Umandap					   ####
    #### License: AGPLv2                               ####
    #######################################################
    
    $campaign_id = $astDB->escape($_REQUEST['campaign_id']);
    $location = $astDB->escape($_REQUEST['location']);
    $user_id = $astDB->escape($_REQUEST['user_id']);

    $statuses = getStatuses($astDB, $campaign_id);

    // Get All Users from vicidial_agent_log
    $queryGetUsers = "SELECT DISTINCT(vu.user_id), vu.user, vu.full_name as agent_name, loc.description as location, vla.status 
                    FROM `$VARDB_database`.`vicidial_agent_log` val 
                    LEFT JOIN `$VARDB_database`.`vicidial_users` vu ON vu.user = val.user 
                    LEFT JOIN `$VARDB_database`.`vicidial_live_agents` vla ON vla.agent_log_id = val.agent_log_id  
                    LEFT JOIN `$VARDBgo_database`.`go_campaigns` gc ON gc.campaign_id=val.campaign_id 
                    LEFT JOIN `$VARDBgo_database`.`locations` loc ON loc.id=gc.location_id;";
    $resultUsers = $astDB->rawQuery($queryGetUsers);
    // $count = mysqli_num_rows($resultUsers);
    foreach ($resultUsers as $fresultsUsers){
        $data['user_id'] = $fresultsUsers['user_id'];
        $data['user'] = $fresultsUsers['user'];
        $data['agent'] = $fresultsUsers['agent_name'];
        $data['loc'] = $fresultsUsers['location'];
        $data['status'] = $fresultsUsers['status'];

        $data['table_data'] = [];
        $user = $fresultsUsers['user'];

        if($campaign_id == "all"){
            $campaignQuery = "";
            $campaignCallbackQuery = "";
            $hoursCampaignQuery = "";
        }else{
            $campaignQuery = "AND val.campaign_id='$campaign_id'";
            $campaignCallbackQuery = "WHERE vicidial_callbacks.campaign_id='$campaign_id'";
            $hoursCampaignQuery = "AND val.campaign_id='$campaign_id'";
        }

        $data['table_data']['contacts'] = 0;
        $data['table_data']['contact_hours'] = 0;
        $data['table_data']['sales'] = 0;
        $data['table_data']['sales_hours'] = 0;
        $data['table_data']['wrong'] = 0;
        $data['table_data']['notQualified'] = 0;
        $data['table_data']['hours'] = 0;
        $data['table_data']['calls'] = 0;
        $data['table_data']['call_hours'] = 0;
        $data['table_data']['callbacks'] = 0;
        $data['table_data']['refusals'] = 0;
        $data['table_data']['leads'] = 0;
        $data['table_data']['conversion'] = 0;

        $date =  date('Y-m-d');
        $dateHoursQuery = "event_time > '$date 00:00:00' AND event_time < '$date 23:59:59'";

        // Contacts
        $queryContacts = "SELECT DISTINCT val.agent_log_id, val.* 
            FROM vicidial_log vl,vicidial_agent_log val 
            WHERE vl.uniqueid=val.uniqueid 
            AND val.status IN ('$statuses', 'QualR', 'QUANS') 
            AND val.user = '$user' 
            AND $dateHoursQuery $campaignQuery;";
        $resultContacts = $astDB->rawQuery($queryContacts);
        foreach ($resultContacts as $fresultsContacts){
            $data['table_data']['contacts'] = $fresultsContacts;
        }

        // Sales
        $querySales = "SELECT DISTINCT val.agent_log_id, val.* 
            FROM vicidial_log vl,vicidial_agent_log val
            WHERE vl.uniqueid=val.uniqueid 
            AND val.status IN ('$statuses') 
            AND val.user = '$user' 
            AND $dateHoursQuery $campaignQuery;";
        $resultSales = $astDB->rawQuery($querySales);
        foreach ($resultSales as $fresultsSales){
            $data['table_data']['sales'] = $fresultsSales;
        }

        // Wrong
        $queryWrong = "SELECT DISTINCT val.agent_log_id, val.* 
            FROM vicidial_log vl,vicidial_agent_log val
            WHERE vl.uniqueid=val.uniqueid 
            AND val.status='WN' 
            AND val.user = '$user' 
            AND $dateHoursQuery $campaignQuery;";
        $resultWrong = $astDB->rawQuery($queryWrong);
        foreach ($resultWrong as $fresultsWrong){
            $data['table_data']['wrong'] = $fresultsWrong;
        }

        // Not Qualified
        $queryNotQualified = "SELECT DISTINCT val.agent_log_id, val.* 
            FROM vicidial_log vl,vicidial_agent_log val
            INNER JOIN vicidial_statuses ON val.status=vicidial_statuses.status
            WHERE vl.uniqueid=val.uniqueid 
            AND vicidial_statuses.status_name LIKE '%not qualified%' 
            AND val.user = '$user' 
            AND $dateHoursQuery $campaignQuery;";
        $resultNotQualified = $astDB->rawQuery($queryNotQualified);
        foreach ($resultNotQualified as $fresultsNotQualified){
            $data['table_data']['not_qualified'] = $fresultsNotQualified;
        }

        // Hours
        $queryHours = "SELECT val.agent_log_id, (sum(pause_sec)+sum(wait_sec)+sum(talk_sec)+sum(dispo_sec))/3600 as hours 
            FROM vicidial_agent_log val 
            WHERE $dateHoursQuery $hoursCampaignQuery 
            AND val.user = '$user' 
            GROUP BY agent_log_id;";
        $resultHours = $astDB->rawQuery($queryHours);
        foreach ($resultHours as $fresultsHours){
            $data['table_data']['hours'] = $fresultsHours['hours'];
        }

        // Calls
        $queryCalls = "SELECT COUNT(DISTINCT val.agent_log_id) as count 
            FROM vicidial_agent_log val
            WHERE val.lead_id IS NOT NULL $hoursCampaignQuery 
            AND $dateHoursQuery 
            AND val.user = '$user' 
            GROUP BY val.user;";
        $resultCalls = $astDB->rawQuery($queryCalls);
        foreach ($resultCalls as $fresultsCalls){
            $data['table_data']['calls'] = $fresultsCalls['count'];
        }

        // Call Backs
        $queryCallBacks = "SELECT vicidial_callbacks.* 
            FROM vicidial_callbacks
            $campaignCallbackQuery AND vicidial_callbacks.user = '$user';";
        $resultCallBacks = $astDB->rawQuery($queryCallBacks);
        foreach ($resultCallBacks as $fresultsCallBacks){
            $data['table_data']['callbacks'] = $fresultsCallBacks;
        }

        // Refusals
        $queryRefusals = "SELECT DISTINCT val.agent_log_id, val.* 
            FROM vicidial_log vl,vicidial_agent_log val
            WHERE vl.uniqueid=val.uniqueid 
            AND val.status='QualR' 
            AND val.user = '$user' 
            AND $dateHoursQuery $campaignQuery;";
        $resultRefusals = $astDB->rawQuery($queryRefusals);
        foreach ($resultRefusals as $fresultsRefusals){
            $data['table_data']['refusals'] = $fresultsRefusals;
        }

        // Leads
        $queryLeads = "SELECT DISTINCT val.agent_log_id, val.* 
            FROM vicidial_log vl,vicidial_agent_log val
            WHERE vl.uniqueid=val.uniqueid 
            AND val.status='LeaS' 
            AND val.user = '$user' 
            AND $dateHoursQuery $campaignQuery;";
        $resultLeads = $astDB->rawQuery($queryLeads);
        foreach ($resultLeads as $fresultsLeads){
            $data['table_data']['leads'] = $fresultsLeads;
        }
        $data['table_data']['contact_hours'] = round($data['table_data']['contacts'] / $data['table_data']['hours'], 2);
        $data['table_data']['call_hours'] = round($data['table_data']['calls'] / $data['table_data']['hours'], 2);
        $data['table_data']['sales_hours'] = round($data['table_data']['sales'] / $data['table_data']['hours'], 2);
        $data['table_data']['conversion'] = round(($data['table_data']['sales'] / $data['table_data']['contacts']) * 100, 2);
        $dataMainTable[] = $data;
    }

    $apiresults = array(
        "result" => "success",
        "data"  => $dataMainTable
    );

    function getStatuses($dbase, $campaign_id="all"){
        if($campaign_id == "all"){
            //$queryStatuses = "SELECT status FROM vicidial_statuses WHERE sale='Y';";
            $dbase->where('sale', 'Y');
            $resultStatuses = $dbase->get('vicidial_statuses', null, 'status');
            foreach ($resultStatuses as $fresultsStatuses){
                $status = $fresultsStatuses['status'];
                $sstatuses[$status] = $fresultsStatuses['status'];
            }
            $sstatuses = implode("','", $sstatuses);
            //$queryCStatuses = "SELECT status FROM vicidial_campaign_statuses WHERE sale='Y';";
            $dbase->where('sale', 'Y');
            $resultCStatuses = $dbase->get('vicidial_campaign_statuses', null, 'status');
            foreach ($resultCStatuses as $fresultsCStatuses){
                $status = $fresultsCStatuses['status'];
                $cstatuses[$status] = $fresultsCStatuses['status'];
            }
        }else{
            $queryStatuses = "SELECT status 
                FROM vicidial_statuses 
                WHERE sale='Y' 
                AND cam_category IN (SELECT vicidial_campaign_definitions.id FROM vicidial_campaign_definitions, vicidial_campaigns WHERE vicidial_campaigns.campaign_def = vicidial_campaign_definitions.code AND vicidial_campaigns.campaign_id = '$campaign_id');";
            $resultStatuses = $dbase->rawQuery($queryStatuses);
            foreach ($resultStatuses as $fresultsStatuses){
                $status = $fresultsStatuses['status'];
                $sstatuses[$status] = $fresultsStatuses['status'];
            }
            $sstatuses = implode("','", $sstatuses);
            $queryCStatuses = "SELECT vicidial_campaign_statuses.status FROM vicidial_campaign_statuses WHERE vicidial_campaign_statuses.sale='Y' AND vicidial_campaign_statuses.campaign_id = '$campaign_id';";
            $resultCStatuses = $dbase->rawQuery($queryCStatuses);
            foreach ($resultCStatuses as $fresultsCStatuses){
                $status = $fresultsCStatuses['status'];
                $cstatuses[$status] = $fresultsCStatuses['status'];
            }
        }
        $cstatuses = implode("','", $cstatuses);
        if(strlen($sstatuses) > 0 && strlen($cstatuses) > 0)
        {
            $statuses = "{$sstatuses}','{$cstatuses}";
        }
        else
        {
            $statuses = (strlen($sstatuses) > 0 && strlen($cstatuses) < 1) ? $sstatuses : $cstatuses;
        }

        return $statuses;
    }
?>