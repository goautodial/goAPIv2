<?php
 /**
 * @file 		goGetDashboardCounters.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Noel Umandap  <noel@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

    global $astDB;

    $dataNewLeads = ['newLeadsCount' => 0];
    $dataOldLeads = ['oldLeadsCount' => 0];
    $dataStatsToday = [];
    $dataStatsCampaign = [];

    $campaign_id = $astDB->escape(($_REQUEST['campaign_id'] ?? ''));
    $location = $astDB->escape(($_REQUEST['location'] ?? ''));
    $user_id = $astDB->escape(($_REQUEST['user_id'] ?? ''));

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
    $resultNewLeadsCount = $astDB->rawQuery($queryNewLeadsCount);
    foreach ((is_array($resultNewLeadsCount) ? $resultNewLeadsCount : []) as $fresultsNewLeadsCount){
        $dataNewLeads['newLeadsCount'] = (int) ($fresultsNewLeadsCount['cnt'] ?? 0);
    }

    $queryNewLeadsTZMap = "SELECT count(1) as cnt, gmt_offset_now as gmt FROM vicidial_list WHERE status = 'NEW' {$campaignNewLeads} group by gmt_offset_now;";
    $resultNewLeadsTZMap = $astDB->rawQuery($queryNewLeadsTZMap);
    foreach ((is_array($resultNewLeadsTZMap) ? $resultNewLeadsTZMap : []) as $fresultsNewLeadsTZMap){
        $indexOfNewTZMap = getTimezoneNameByOffset($fresultsNewLeadsTZMap['gmt'] ?? null);
        if ($indexOfNewTZMap !== null) {
            $dataNewLeads[$indexOfNewTZMap] = (int) ($fresultsNewLeadsTZMap['cnt'] ?? 0);
        }
    }
    // End of New Leads Counter

    // Getting Old Leads Counter
    $queryOldLeadsCount = "SELECT count(*) as cnt
                    FROM vicidial_list l,
                        vicidial_lists ll,
                        vicidial_campaigns vc
                    WHERE l.list_id = ll.list_id
                    AND ll.campaign_id = vc.campaign_id
                    AND l.status != 'NEW' {$campaignOldLeads};";
    $resultOldLeadsCount = $astDB->rawQuery($queryOldLeadsCount);
    foreach ((is_array($resultOldLeadsCount) ? $resultOldLeadsCount : []) as $fresultsOldLeadsCount){
        $dataOldLeads['oldLeadsCount'] = (int) ($fresultsOldLeadsCount['cnt'] ?? 0);
    }

    $queryOldLeadsTZMap = "SELECT count(*) as cnt, l.gmt_offset_now as gmt
                    FROM  vicidial_list l,
                        vicidial_lists ll,
                        vicidial_campaigns vc
                    WHERE l.list_id = ll.list_id
                    AND ll.campaign_id = vc.campaign_id
                    AND l.status != 'NEW' {$campaignOldLeads} GROUP BY l.gmt_offset_now;";
    $resultOldLeadsTZMap = $astDB->rawQuery($queryOldLeadsTZMap);
    foreach ((is_array($resultOldLeadsTZMap) ? $resultOldLeadsTZMap : []) as $fresultsOldLeadsTZMap){
        $indexOfOldTZMap = getTimezoneNameByOffset($fresultsOldLeadsTZMap['gmt'] ?? null);
        if ($indexOfOldTZMap !== null) {
            $dataOldLeads[$indexOfOldTZMap] = (int) ($fresultsOldLeadsTZMap['cnt'] ?? 0);
        }
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
    $resultStatsTodayCalls = $astDB->rawQuery($queryStatsTodayCalls);
    $StatsTodayCalls = 0;
    foreach ((is_array($resultStatsTodayCalls) ? $resultStatsTodayCalls : []) as $fresultsStatsTodayCalls){
        $StatsTodayCalls = (int) ($fresultsStatsTodayCalls['count'] ?? 0);
    }
    $callsST = $StatsTodayCalls;
    $dataStatsToday['callsST'] = $callsST;
    // $dataStatsToday['querycallsST'] = $queryStatsTodayCalls;
    // Hours
    $queryStatsTodayHours = "SELECT val.agent_log_id , (sum(pause_sec)+sum(wait_sec)+sum(talk_sec)+sum(dispo_sec))/3600 as hours FROM `asteriskV4`.`vicidial_agent_log` val LEFT OUTER JOIN `asteriskV4`.`vicidial_campaigns` vc ON ( val.campaign_id = vc.campaign_id ) WHERE 1 {$locationQueryStatsShort} {$campaignQuery} AND {$queryDate};";
    $resultStatsTodayHours = $astDB->rawQuery($queryStatsTodayHours);
    $hoursST = 0;
    $callsHourST = 0;
    foreach ((is_array($resultStatsTodayHours) ? $resultStatsTodayHours : []) as $fresultsStatsTodayHours){
        $hoursST += (float) ($fresultsStatsTodayHours['hours'] ?? 0);
    }

    $hoursST = round($hoursST, 2);
    $callsHourST = ($hoursST > 0) ? round($callsST / $hoursST, 2) : 0;
    $dataStatsToday['hoursST'] = $hoursST;
    $dataStatsToday['callsHourST'] = $callsHourST;
    // $dataStatsToday['queryhoursST'] = $queryStatsTodayHours;
    // Contact Hour & Contact
    $queryStatsTodayContactHour = "SELECT DISTINCT val.agent_log_id, val.*, val.user FROM  `asteriskV4`.`vicidial_log` vl,`asteriskV4`.`vicidial_agent_log` val LEFT OUTER JOIN `asteriskV4`.`vicidial_campaigns` vc ON ( val.campaign_id = vc.campaign_id ) WHERE 1 {$locationQueryStatsShort} {$campaignQuery} AND {$queryDate} AND val.status IN ('SALE','RESIDENCE','SUBSCRIBER','CORPORATE','STATUS', 'QualR', 'QUANS');";
    $resultStatsTodayContactHour = $astDB->rawQuery($queryStatsTodayContactHour);
    $contactsHourST = 0;
    $contactST = $astDB->getRowCount();
    $contactsHourST = ($hoursST > 0) ? round($contactST / $hoursST, 2) : 0;
    $dataStatsToday['contactST'] = $contactST;
    $dataStatsToday['contactsHourST'] = $contactsHourST;
    // $dataStatsToday['querycontactST'] = $queryStatsTodayContactHour;
    // Sales
    $queryStatsTodaySales = "SELECT DISTINCT val.agent_log_id, val.*, val.user FROM `asteriskV4`.`vicidial_log` vl,`asteriskV4`.`vicidial_agent_log` val WHERE 1 {$locationQueryStatsShort} {$campaignQuery} AND {$queryDate} AND val.status IN ('SALE','RESIDENCE' ,'SUBSCRIBER','CORPORATE','STATUS');";
    $resultStatsTodaySales = $astDB->rawQuery($queryStatsTodaySales);
    $salesST = $astDB->getRowCount();
    $salesHourST = ($hoursST > 0) ? round($salesST / $hoursST, 2) : 0;
    $conversionST = ($contactST > 0) ? round(($salesST / $contactST) * 100, 2) : 0;
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
        $CampStatsstatuses = getStatuses($astDB, $campaign_id);
        // Calls
        $queryStatsCalls = "SELECT COUNT(DISTINCT val.agent_log_id) as count, vu.user_id
            FROM `asteriskV4`.`vicidial_agent_log` val
            INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user=val.user
            INNER JOIN `asteriskV4`.`vicidial_campaigns` vc ON vc.campaign_id=val.campaign_id
            LEFT JOIN `goautodialV4`.`users` gu ON gu.name=vu.user
            WHERE val.lead_id IS NOT NULL {$locationQueryCampaignStats} {$campaignQuery} AND {$dateQueryTotal};";
        $resultStatsCalls = $astDB->rawQuery($queryStatsCalls);
        $StatsCalls = 0;
        foreach ((is_array($resultStatsCalls) ? $resultStatsCalls : []) as $fresultsStatsCalls){
            $StatsCalls = (int) ($fresultsStatsCalls['count'] ?? 0);
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
        $resultStatsHours = $astDB->rawQuery($queryStatsHours);
        $hoursCST = 0;
        $callsHourCST = 0;
        foreach ((is_array($resultStatsHours) ? $resultStatsHours : []) as $fresultsStatsHours){
            $hoursCST += (int) ($fresultsStatsHours['pause_sec'] ?? 0);
            $hoursCST += (int) ($fresultsStatsHours['wait_sec'] ?? 0);
            $hoursCST += (int) ($fresultsStatsHours['talk_sec'] ?? 0);
            $hoursCST += (int) ($fresultsStatsHours['dispo_sec'] ?? 0);
            // $hoursCST += $fresultsStatsHours['dead_sec'];
        }

        $hoursCST = round($hoursCST / 3600, 2);
        $callsHourCST = ($hoursCST > 0) ? round($callsCST / $hoursCST, 2) : 0;
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
        $resultStatsContactHour = $astDB->rawQuery($queryStatsContactHour);
        $contactsHourCST = 0;
        $contactsCST = $astDB->getRowCount();
        $contactsHourCST = ($hoursCST > 0) ? round($contactsCST / $hoursCST, 2) : 0;
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
        $resultStatsSales = $astDB->rawQuery($queryStatsSales);
        $salesCST = $astDB->getRowCount();
        $salesHourCST = ($hoursCST > 0) ? round($salesCST / $hoursCST, 2) : 0;
        $conversionCST = ($contactsCST > 0) ? round($salesCST / $contactsCST * 100, 2) : 0;
        $dataStatsCampaign['salesCST'] = $salesCST;
        $dataStatsCampaign['salesHourCST'] = $salesHourCST;
        $dataStatsCampaign['conversionCST'] = $conversionCST;
        // $dataStatsCampaign['querysalesCST'] = $queryStatsSales;
    // End of campaign stats


    $apiresults = [
        "result" => "success",
        "newLeadsCounter" => $dataNewLeads,
        "oldLeadsCounter" => $dataOldLeads,
        "statsToday" => $dataStatsToday,
        "statsCampaign" => $dataStatsCampaign
    ];

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
        return null;
    }

    function getStatuses($dbase, $campaign_id="all"){
        $sstatuses = [];
        $cstatuses = [];

        if($campaign_id == "all"){
            $dbase->where('sale', 'Y');
            $resultStatuses = $dbase->get('vicidial_statuses', null, 'status');
            foreach ((is_array($resultStatuses) ? $resultStatuses : []) as $fresultsStatuses){
                $status = $fresultsStatuses['status'] ?? '';
                if ($status !== '') {
                    $sstatuses[$status] = $status;
                }
            }

            $dbase->where('sale', 'Y');
            $resultCStatuses = $dbase->get('vicidial_campaign_statuses', null, 'status');
            foreach ((is_array($resultCStatuses) ? $resultCStatuses : []) as $fresultsCStatuses){
                $status = $fresultsCStatuses['status'] ?? '';
                if ($status !== '') {
                    $cstatuses[$status] = $status;
                }
            }
        }else{
            $safeCampaign = $dbase->escape($campaign_id);
            $queryStatuses = "SELECT status
                FROM vicidial_statuses
                WHERE sale='Y'
                AND cam_category IN (SELECT vicidial_campaign_definitions.id FROM vicidial_campaign_definitions, vicidial_campaigns WHERE vicidial_campaigns.campaign_def = vicidial_campaign_definitions.code AND vicidial_campaigns.campaign_id = '$safeCampaign');";
            $resultStatuses = $dbase->rawQuery($queryStatuses);
            foreach ((is_array($resultStatuses) ? $resultStatuses : []) as $fresultsStatuses){
                $status = $fresultsStatuses['status'] ?? '';
                if ($status !== '') {
                    $sstatuses[$status] = $status;
                }
            }

            $queryCStatuses = "SELECT vicidial_campaign_statuses.status FROM vicidial_campaign_statuses WHERE vicidial_campaign_statuses.sale='Y' AND vicidial_campaign_statuses.campaign_id = '$safeCampaign';";
            $resultCStatuses = $dbase->rawQuery($queryCStatuses);
            foreach ((is_array($resultCStatuses) ? $resultCStatuses : []) as $fresultsCStatuses){
                $status = $fresultsCStatuses['status'] ?? '';
                if ($status !== '') {
                    $cstatuses[$status] = $status;
                }
            }
        }

        $sstatuses = implode("','", $sstatuses);
        $cstatuses = implode("','", $cstatuses);
        return ($cstatuses !== '') ? "{$sstatuses}','{$cstatuses}" : $sstatuses;
    }

?>
