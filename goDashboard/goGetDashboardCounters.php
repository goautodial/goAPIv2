<?php
    #######################################################
    #### Name: goGetDashboardCounters.php 	           ####
    #### Description: API to get all Dashboard Counters####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Noel Umandap					   ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
    $location = mysqli_real_escape_string($link, $_REQUEST['location']);
    $user_id = mysqli_real_escape_string($link, $_REQUEST['user_id']);

    $date = date("Y-m-d");
	//$date = "2017-10-04";
    $queryDate = "event_time BETWEEN '$date 00:00:00' AND '$date 23:59:59'";
    $dateQueryTotal = "event_time > '$date'";
    $dateHoursQueryTotal = "event_time > '$date'";

    if($campaign_id == "all"){
    	$campaignNewLeads = "";
    	$campaignOldLeads = "";
    	$campaignQuery = "";
		$campaignStatuses = "";
    }else{
    	$campaignNewLeads = "AND list_id IN (SELECT list_id FROM vicidial_lists WHERE campaign_id ='$campaign_id')";
    	$campaignOldLeads = "AND l.list_id IN (SELECT list_id FROM vicidial_lists WHERE campaign_id ='$campaign_id')";
    	$campaignQuery = "AND val.campaign_id='$campaign_id'";
    	$campaignStatuses = "AND val.status IN (SELECT vicidial_campaign_statuses.status FROM vicidial_campaign_statuses WHERE vicidial_campaign_statuses.sale='Y' AND vicidial_campaign_statuses.campaign_id = '$campaign_id')";
    }

    if($location == "all"){
    	$locationQuery = "";
    }else{
    	$locationQuery = "AND gu.location_id='$location'";
    }

    // Getting New Leads Counter
	    $queryNewLeadsCount = "SELECT count(1) as cnt FROM vicidial_list WHERE status = 'NEW' {$campaignNewLeads};";
	    $resultNewLeadsCount = mysqli_query($link,$queryNewLeadsCount);
	    while($fresultsNewLeadsCount = mysqli_fetch_array($resultNewLeadsCount, MYSQLI_ASSOC)){
	    	$dataNewLeads['newLeadsCount'] = $fresultsNewLeadsCount['cnt'];
	    }

	    $queryNewLeadsTZMap = "SELECT count(1) as cnt, gmt_offset_now as gmt FROM vicidial_list WHERE status = 'NEW' {$campaignNewLeads} group by gmt_offset_now";
	    $resultNewLeadsTZMap = mysqli_query($link,$queryNewLeadsTZMap);
	    while($fresultsNewLeadsTZMap = mysqli_fetch_array($resultNewLeadsTZMap, MYSQLI_ASSOC)){
	    	$indexOfNewTZMap = getTimezoneNameByOffset($fresultsNewLeadsTZMap['gmt']);
	    	$dataNewLeads[$indexOfNewTZMap] = $fresultsNewLeadsTZMap['cnt'];
	    }
	// End of New Leads Counter

    // Getting Old Leads Counter
	    $queryOldLeadsCount = "SELECT count(*) as cnt 
	    			   	FROM vicidial_list l,
	    			        vicidial_lists ll,
	    					vicidial_campaigns vc
	    			  	WHERE l.list_id = ll.list_id
	    			    AND ll.campaign_id = vc.campaign_id
	    				AND	l.status != 'NEW' {$campaignOldLeads};";
	    $resultOldLeadsCount = mysqli_query($link,$queryOldLeadsCount);
	    while($fresultsOldLeadsCount = mysqli_fetch_array($resultOldLeadsCount, MYSQLI_ASSOC)){
	    	$dataOldLeads['oldLeadsCount'] = $fresultsOldLeadsCount['cnt'];
	    }

	    $queryOldLeadsTZMap = "SELECT count(*) as cnt, l.gmt_offset_now as gmt
	    			  	FROM  vicidial_list l,
			    			vicidial_lists ll,
			    			vicidial_campaigns vc
	    			  	WHERE l.list_id = ll.list_id
	    			    AND ll.campaign_id = vc.campaign_id
	    				AND	l.status != 'NEW' {$campaignOldLeads} GROUP BY l.gmt_offset_now;";
	    $resultOldLeadsTZMap = mysqli_query($link,$queryOldLeadsTZMap);
	    while($fresultsOldLeadsTZMap = mysqli_fetch_array($resultOldLeadsTZMap, MYSQLI_ASSOC)){
	    	$indexOfOldTZMap = getTimezoneNameByOffset($fresultsOldLeadsTZMap['gmt']);
	    	$dataOLdLeads[$indexOfOldTZMap] = $fresultsOldLeadsTZMap['cnt'];
	    }
	// End of Old Leads Counter

    if($location == "all"){
    	$locationQueryStats = "";
    	$locationQueryStatsShort = "";
    }else{
    	// AND gc.location_id='$location'
    	$locationQueryStats = "AND (gu.location_id='$location' OR vc.campaign_id NOT IN (SELECT campaign_id FROM `goautodialV4`.`go_campaigns`))";
    	// $locationQueryStatsShort = "AND val.user IN (SELECT vicidial_users.user FROM vicidial_users WHERE vicidial_users.user_id IN (SELECT gc.user_id FROM `goautodialV4`.`go_campaigns` gc WHERE gc.location_id = '$location';))";
    	$locationQueryStatsShort = "AND val.campaign_id IN (SELECT campaign_id FROM `goautodialV4`.`go_campaigns`)";
    }

    // Getting Data for Today Stats
    	// Calls
        $queryStatsTodayCalls = "SELECT COUNT(DISTINCT val.agent_log_id) as count, val.user FROM `asteriskV4`.`vicidial_agent_log` val LEFT OUTER JOIN `asteriskV4`.`vicidial_campaigns` vc ON ( val.campaign_id = vc.campaign_id ) WHERE 1 {$locationQueryStatsShort} {$campaignQuery} AND {$queryDate} AND val.lead_id IS NOT NULL;";
        $resultStatsTodayCalls = mysqli_query($link,$queryStatsTodayCalls);
        while($fresultsStatsTodayCalls = mysqli_fetch_array($resultStatsTodayCalls, MYSQLI_ASSOC)){
        	$StatsTodayCalls = $fresultsStatsTodayCalls['count'];
       	}
       	$callsST = $StatsTodayCalls;
       	$dataStatsToday['callsST'] = $callsST;
        // $dataStatsToday['querycallsST'] = $queryStatsTodayCalls;
        // Hours
        $queryStatsTodayHours = "SELECT val.agent_log_id , (sum(pause_sec)+sum(wait_sec)+sum(talk_sec)+sum(dispo_sec))/3600 as hours FROM `asteriskV4`.`vicidial_agent_log` val LEFT OUTER JOIN `asteriskV4`.`vicidial_campaigns` vc ON ( val.campaign_id = vc.campaign_id ) WHERE 1 {$locationQueryStatsShort} {$campaignQuery} AND {$queryDate};";
        $resultStatsTodayHours = mysqli_query($link,$queryStatsTodayHours);
        $hoursST = 0; 
        $callsHourST = 0;
        while($fresultsStatsTodayHours = mysqli_fetch_array($resultStatsTodayHours, MYSQLI_ASSOC)){
            $hoursST += $fresultsStatsTodayHours['hours'];
        }

        $hoursST = round($hoursST, 2);
        $callsHourST = round($callsST / $hoursST, 2);
        $dataStatsToday['hoursST'] = $hoursST;
        $dataStatsToday['callsHourST'] = $callsHourST;
        // $dataStatsToday['queryhoursST'] = $queryStatsTodayHours;
        // Contact Hour & Contact
        $queryStatsTodayContactHour = "SELECT DISTINCT val.agent_log_id, val.*, val.user FROM  `asteriskV4`.`vicidial_log` vl,`asteriskV4`.`vicidial_agent_log` val LEFT OUTER JOIN `asteriskV4`.`vicidial_campaigns` vc ON ( val.campaign_id = vc.campaign_id ) WHERE 1 {$locationQueryStatsShort} {$campaignQuery} AND {$queryDate} AND val.status IN ('SALE','RESIDENCE','SUBSCRIBER','CORPORATE','STATUS', 'QualR', 'QUANS');";
        $resultStatsTodayContactHour = mysqli_query($link,$queryStatsTodayContactHour);
        $contactsHourST = 0;
        $contactST = mysqli_num_rows($resultStatsTodayContactHour);
        $contactsHourST = round($contactST / $hoursST, 2);
        $dataStatsToday['contactST'] = $contactST;
        $dataStatsToday['contactsHourST'] = $contactsHourST;
        // $dataStatsToday['querycontactST'] = $queryStatsTodayContactHour;
        // Sales
        $queryStatsTodaySales = "SELECT DISTINCT val.agent_log_id, val.*, val.user FROM `asteriskV4`.`vicidial_log` vl,`asteriskV4`.`vicidial_agent_log` val WHERE 1 {$locationQueryStatsShort} {$campaignQuery} AND {$queryDate} AND val.status IN ('SALE','RESIDENCE' ,'SUBSCRIBER','CORPORATE','STATUS');";
        $resultStatsTodaySales = mysqli_query($link,$queryStatsTodaySales);
        $salesST = mysqli_num_rows($resultStatsTodaySales);
        $salesHourST = round($salesST / $hoursST, 2);
        $conversionST = round(($salesST / $contactST) * 100, 2);
        $dataStatsToday['salesST'] = $salesST;
        $dataStatsToday['salesHourST'] = $salesHourST;
        $dataStatsToday['conversionST'] = $conversionST;
        // $dataStatsToday['querySalesST'] = $queryStatsTodaySales;
    // End of today stats

    if($location == "all"){
    	$locationQueryCampaignStats = "";
    }else{
    	$locationQueryCampaignStats = "AND gu.location_id='$location'";
   	}
    // Getting Data for Campaign Stats
    	$CampStatsstatuses = getStatuses($campaign_id);
        // Calls
        $queryStatsCalls = "SELECT COUNT(DISTINCT val.agent_log_id) as count, vu.user_id 
        	FROM `asteriskV4`.`vicidial_agent_log` val
            INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user=val.user
            INNER JOIN `asteriskV4`.`vicidial_campaigns` vc ON vc.campaign_id=val.campaign_id
            LEFT JOIN `goautodialV4`.`users` gu ON gu.name=vu.user
            WHERE val.lead_id IS NOT NULL {$locationQueryCampaignStats} {$campaignQuery} AND {$dateQueryTotal};";
        $resultStatsCalls = mysqli_query($link,$queryStatsCalls);
        while($fresultsStatsCalls = mysqli_fetch_array($resultStatsCalls, MYSQLI_ASSOC)){
        	$StatsCalls = $fresultsStatsCalls['count'];
       	}
       	$callsCST = $StatsCalls;
       	$dataStatsCampaign['callsCST'] = $callsCST;
        // $dataStatsCampaign['querycallsCST'] = $queryStatsCalls;
        // Hours
        $queryStatsHours = "SELECT DISTINCT val.agent_log_id, val.*, vu.user_id 
        	FROM `asteriskV4`.`vicidial_agent_log` val
            INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user=val.user
            INNER JOIN `asteriskV4`.`vicidial_campaigns` vc ON vc.campaign_id=val.campaign_id
            LEFT JOIN `goautodialV4`.`users` gu ON gu.name=vu.user
            WHERE {$dateQueryTotal} {$campaignQuery} {$locationQueryCampaignStats} 
            GROUP BY val.agent_log_id, val.campaign_id;";
        $resultStatsHours = mysqli_query($link,$queryStatsHours);
        $hoursCST = 0;
        $callsHourCST = 0;
        while($fresultsStatsHours = mysqli_fetch_array($resultStatsHours, MYSQLI_ASSOC)){
        	$hoursCST += $fresultsStatsHours['pause_sec'];
            $hoursCST += $fresultsStatsHours['wait_sec'];
            $hoursCST += $fresultsStatsHours['talk_sec'];
            $hoursCST += $fresultsStatsHours['dispo_sec'];
            // $hoursCST += $fresultsStatsHours['dead_sec'];
        }

        $hoursCST = round($hoursCST / 3600, 2);
        $callsHourCST = round($callsCST / $hoursCST, 2);
        $dataStatsCampaign['hoursCST'] = $hoursCST;
        $dataStatsCampaign['callsHourCST'] = $callsHourCST;
        // $dataStatsCampaign['queryhoursCST'] = $queryStatsHours;
        // Contact Hour
        $queryStatsContactHour = "SELECT DISTINCT val.agent_log_id, val.*, vu.user
        	FROM `asteriskV4`.`vicidial_agent_log` val
            INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user=val.user
            INNER JOIN `asteriskV4`.`vicidial_campaigns` vc ON vc.campaign_id=val.campaign_id
            LEFT JOIN `goautodialV4`.`users` gu ON gu.name=vu.user
            WHERE val.status IN ('$CampStatsstatuses', 'QualR', 'QUANS') {$locationQueryCampaignStats} {$campaignQuery} AND {$dateQueryTotal};";
        $resultStatsContactHour = mysqli_query($link,$queryStatsContactHour);
        $contactsHourCST = 0;
        $contactsCST = mysqli_num_rows($resultStatsContactHour);
        $contactsHourCST = round($contactsCST / $hoursCST, 2);
        $dataStatsCampaign['contactsCST'] = $contactsCST;
        $dataStatsCampaign['contactsHourCST'] = $contactsHourCST;
        // $dataStatsCampaign['querycontactsCST'] = $queryStatsContactHour;
        // Sales
        $queryStatsSales = "SELECT DISTINCT val.agent_log_id, val.*, vu.user
        	FROM `asteriskV4`.`vicidial_agent_log` val
            INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user=val.user
            INNER JOIN `asteriskV4`.`vicidial_campaigns` vc ON vc.campaign_id=val.campaign_id
            LEFT JOIN `goautodialV4`.`users` gu ON gu.name=vu.user
            WHERE val.status IN ('$CampStatsstatuses') {$locationQueryCampaignStats} {$campaignQuery} AND {$dateQueryTotal};";
        $resultStatsSales = mysqli_query($link,$queryStatsSales);
        $salesCST = mysqli_num_rows($resultStatsSales);
        $salesHourCST = round($sales / $hoursCST, 2);
        $conversionCST = round($salesCST / $contactsCST * 100, 2);
        $dataStatsCampaign['salesCST'] = $salesCST;
        $dataStatsCampaign['salesHourCST'] = $salesHourCST;
        $dataStatsCampaign['conversionCST'] = $conversionCST;
        // $dataStatsCampaign['querysalesCST'] = $queryStatsSales;
    // End of campaign stats


    $apiresults = array(
    	"result" => "success", 
    	"newLeadsCounter" => $dataNewLeads,
    	"oldLeadsCounter" => $dataOldLeads,
    	"statsToday" => $dataStatsToday,
    	"statsCampaign" => $dataStatsCampaign
    );

    function getTimezoneNameByOffset($offset) {
    	if ($offset == -5) {
    		return "tz_eastern_time";
    	} else if ($offset == -6) {
    		return "tz_central_time";
    	} else if ($offset == -7) {
    		return "tz_mountain_time";
    	} else if ($offset == -8) {
    		return "tz_pacific_time";
    	} else if ($offset == -9) {
    		return "tz_alaska_time";
    	} else if ($offset == -10) {
    		return "tz_hawaii_time";
    	}
    }

    function getStatuses($campaign_id="all"){
        if($campaign_id == "all"){
            $queryStatuses = "SELECT status FROM vicidial_statuses WHERE sale='Y';";
            $resultStatuses = mysqli_query($link,$queryStatuses);
            while($fresultsStatuses = mysqli_fetch_array($resultStatuses, MYSQLI_ASSOC)){
                $status = $fresultsStatuses['status'];
                $sstatuses[$status] = $fresultsStatuses['status'];
            }
            $sstatuses = implode("','", $sstatuses);
            $queryCStatuses = "SELECT status FROM vicidial_campaign_statuses WHERE sale='Y';";
            $resultCStatuses = mysqli_query($link,$queryCStatuses);
            while($fresultsCStatuses = mysqli_fetch_array($resultCStatuses, MYSQLI_ASSOC)){
                $status = $fresultsCStatuses['status'];
                $cstatuses[$status] = $fresultsCStatuses['status'];
            }
        }else{
            $queryStatuses = "SELECT status 
                FROM vicidial_statuses 
                WHERE sale='Y' 
                AND cam_category IN (SELECT vicidial_campaign_definitions.id FROM vicidial_campaign_definitions, vicidial_campaigns WHERE vicidial_campaigns.campaign_def = vicidial_campaign_definitions.code AND vicidial_campaigns.campaign_id = '$campaign_id');";
            $resultStatuses = mysqli_query($link,$queryStatuses);
            while($fresultsStatuses = mysqli_fetch_array($resultStatuses, MYSQLI_ASSOC)){
                $status = $fresultsStatuses['status'];
                $sstatuses[$status] = $fresultsStatuses['status'];
            }
            $sstatuses = implode("','", $sstatuses);
            $queryCStatuses = "SELECT vicidial_campaign_statuses.status FROM vicidial_campaign_statuses WHERE vicidial_campaign_statuses.sale='Y' AND vicidial_campaign_statuses.campaign_id = '$campaign_id';";
            $resultCStatuses = mysqli_query($link,$queryCStatuses);
            while($fresultsCStatuses = mysqli_fetch_array($resultCStatuses, MYSQLI_ASSOC)){
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