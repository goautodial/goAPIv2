<?php
 /**
 * @file 		goGetAgentsPerformanceForMD.php
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

    $campaign_id = $astDB->escape($_REQUEST['campaign_id']);
    $location = $astDB->escape($_REQUEST['location']);
    $user_id = $astDB->escape($_REQUEST['user_id']);

    $date = date("Y-m-d");
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
    	$locationQuery = "AND gc.location_id='$location'";
    }

    if($user_id == "all"){
        $userQuery = "";
    }else{
        $userQuery = "AND val.user='$user_id'";
    }
    // Getting Campaign Today Stats
	    $CampTodayStatsstatuses = getStatuses($astDB, $campaign_id);
	    // Sales Today
	    $querySalesToday = "SELECT DISTINCT val.agent_log_id, val.*, vicidial_users.user_id FROM vicidial_log vl,vicidial_agent_log val INNER JOIN vicidial_users ON vicidial_users.user=val.user WHERE vl.uniqueid=val.uniqueid  AND val.status IN ('$CampTodayStatsstatuses') AND {$queryDate} {$campaignQuery} {$userQuery};";
	    $resultSalesToday = $astDB->rawQuery($querySalesToday);
	    // $salesToday = sizeof($resultSalesToday);
	    $salesToday = $astDB->getRowCount();
	    $dataCampaignStatsToday['salesToday'] = $salesToday;
        // $dataCampaignStatsToday['querysalesToday'] = $querySalesToday;

	    // Hours Today
	    $queryHoursToday = "SELECT DISTINCT val.agent_log_id, val.*, vicidial_users.user_id FROM vicidial_agent_log val INNER JOIN vicidial_users ON vicidial_users.user=val.user WHERE {$queryDate} {$campaignQuery} {$userQuery};";
	    $resultHoursToday = $astDB->rawQuery($queryHoursToday);
	    $hoursToday = 0;
        foreach ($resultHoursToday as $fresultsHours){
        	$hoursToday += $fresultsHours['pause_sec'];
            $hoursToday += $fresultsHours['wait_sec'];
            $hoursToday += $fresultsHours['talk_sec'];
            $hoursToday += $fresultsHours['dispo_sec'];
            // $hoursToday += $fresultsHours['dead_sec'];
        }
        $hoursToday = round($hoursToday / 3600, 4);
        $salesHourToday = round($salesToday / $hoursToday, 1);
        $dataCampaignStatsToday['hoursToday'] = $hoursToday;
        $dataCampaignStatsToday['salesHourToday'] = $salesHourToday;
        // $dataCampaignStatsToday['queryhoursToday'] = $queryHoursToday;

	    // Conversion Today
	    $queryConversionToday = "SELECT DISTINCT val.agent_log_id, val.*, vicidial_users.user_id FROM vicidial_log vl,vicidial_agent_log val INNER JOIN vicidial_users ON vicidial_users.user=val.user WHERE vl.uniqueid=val.uniqueid AND val.status IN ('$CampTodayStatsstatuses', 'QualR', 'QUANS') AND {$queryDate} {$campaignQuery} {$userQuery};";
        $resultConvertionToday = $astDB->rawQuery($queryConversionToday);
        $countConvertionToday = $astDB->getRowCount();
        $conversionToday = 0;
        // if(sizeof($resultConvertionToday) > 0)
        if($countConvertionToday > 0){
            // $conversionToday = $salesToday / sizeof($resultConvertionToday) * 100;
            $conversionToday = $salesToday / $countConvertionToday * 100;
        }
        // $contactsHourToday = round(sizeof($resultConvertionToday) / $hoursToday, 1);
        $contactsHourToday = round($countConvertionToday / $hoursToday, 1);
        $dataCampaignStatsToday['conversionToday'] = $conversionToday;
        $dataCampaignStatsToday['contactsHourToday'] = $contactsHourToday;
        // $dataCampaignStatsToday['queryconversionToday'] = $queryConversionToday;
	// End of Campaign Today Stats

    // Getting Campaign Stats
        $CampaignStatsstatuses = getStatuses($astDB, $campaign_id);
        // Sales
        $querySales = "SELECT DISTINCT val.agent_log_id, val.*, vu.user_id FROM `asteriskV4`.`vicidial_log` vl, `asteriskV4`.`vicidial_agent_log` val INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user = val.user LEFT JOIN `goautodialV4`.`go_campaigns` gc ON gc.campaign_id=val.campaign_id WHERE vl.uniqueid=val.uniqueid AND val.status IN ('$CampaignStatsstatuses') {$campaignQuery} {$locationQuery} {$userQuery};";
        $resultSales = $astDB->rawQuery($querySales);
        $salesCampaign = $astDB->getRowCount();
	    $dataCampaignStats['salesCampaign'] = $salesCampaign;
        $dataCampaignStats['querysalesCampaign'] = $querySales;
        // Hours
        if($campaign_id == "all"){
            $campaignQueryHours = "1";
        }else{
            $campaignQueryHours = "val.campaign_id='$campaign_id'";
        }
        $queryHours = "SELECT DISTINCT val.agent_log_id, val.*, vu.user_id FROM `asteriskV4`.`vicidial_agent_log` val INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user = val.user LEFT JOIN `goautodialV4`.`go_campaigns` gc ON gc.campaign_id=val.campaign_id WHERE {$campaignQueryHours} {$locationQuery} {$userQuery};";
        $resultHours = $astDB->rawQuery($queryHours);
        $hoursCampaign = 0;
        foreach ($resultHours as $fresultsHoursCampaign){
            $hoursCampaign += $fresultsHoursCampaign['pause_sec'];
            $hoursCampaign += $fresultsHoursCampaign['wait_sec'];
            $hoursCampaign += $fresultsHoursCampaign['talk_sec'];
            $hoursCampaign += $fresultsHoursCampaign['dispo_sec'];
            // $hoursCampaign += $fresultsHoursCampaign['dead_sec'];
        }

        $hoursCampaign = round($hoursCampaign / 3600, 2);
        $salesHourCampaign = round($salesCampaign / $hoursCampaign, 1);
        $dataCampaignStats['hoursCampaign'] = $hoursCampaign;
        $dataCampaignStats['salesHourCampaign'] = $salesHourCampaign;
        $dataCampaignStats['queryhoursCampaign'] = $queryHours;

        // Conversion
        $queryConversion = "SELECT DISTINCT val.agent_log_id, val.*, vu.user_id FROM `asteriskV4`.`vicidial_log` vl, `asteriskV4`.`vicidial_agent_log` val INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user = val.user LEFT JOIN `goautodialV4`.`go_campaigns` gc ON gc.campaign_id=val.campaign_id WHERE vl.uniqueid=val.uniqueid  AND val.status IN ('$CampaignStatsstatuses', 'QualR', 'QUANS') {$campaignQuery} {$locationQuery} {$userQuery};";
        $resultConversion = $astDB->rawQuery($queryConversion);
     	$countConvertionCampaign = $astDB->getRowCount();
        $conversionCampaign = 0;
        if($countConvertionCampaign > 0){
            // $conversionCampaign = $salesCampaign / sizeof($countConvertionCampaign) * 100;
            $conversionCampaign = $salesCampaign / $countConvertionCampaign * 100;
        }
        // $contactsHourCampaign = round(sizeof($countConvertionCampaign) / $hoursCampaign, 1);
        $contactsHourCampaign = round($countConvertionCampaign / $hoursCampaign, 1);
        $dataCampaignStats['conversionCampaign'] = $conversionCampaign;
        $dataCampaignStats['contactsHourCampaign'] = $contactsHourCampaign;
        $dataCampaignStats['queryconversionCampaign'] = $queryConversion;
    // End of Camapaign stats

    // Getting Campaign Stats Total
        $CampaignTotalStatsstatuses = getStatuses($astDB);
    	// Sales Total
        $querySalesTotal = "SELECT DISTINCT val.agent_log_id, val.*, vu.user_id FROM `asteriskV4`.`vicidial_log` vl, `asteriskV4`.`vicidial_agent_log` val INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user = val.user LEFT JOIN `goautodialV4`.`go_campaigns` gc ON gc.campaign_id=val.campaign_id WHERE vl.uniqueid=val.uniqueid AND val.status IN ('$CampaignTotalStatsstatuses') AND {$dateQueryTotal} {$locationQuery} {$userQuery};";
        $resultSalesTotal = $astDB->rawQuery($querySalesTotal);
        $salesTotal = $astDB->getRowCount();
	    $dataCampaignStatsTotal['salesTotal'] = $salesTotal;
        // $dataCampaignStatsTotal['querysalesTotal'] = $querySalesTotal;

	    // Hours Total
	    $queryHoursTotal = "SELECT DISTINCT val.agent_log_id, pause_sec,wait_sec,talk_sec,dispo_sec,dead_sec, vu.user_id FROM `asteriskV4`.`vicidial_agent_log` val INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user = val.user LEFT JOIN `goautodialV4`.`go_campaigns` gc ON gc.campaign_id=val.campaign_id WHERE {$dateHoursQueryTotal} {$locationQuery} {$userQuery};";
	    $resultHoursTotal = $astDB->rawQuery($queryHoursTotal);
	    $hoursTotal = 0;
        foreach ($resultHoursTotal as $fresultsHoursTotal){
        	$hoursTotal += $fresultsHoursTotal['pause_sec'];
            $hoursTotal += $fresultsHoursTotal['wait_sec'];
            $hoursTotal += $fresultsHoursTotal['talk_sec'];
            $hoursTotal += $fresultsHoursTotal['dispo_sec'];
            // $hoursTotal += $fresultsHoursTotal['dead_sec'];
        }
        $hoursTotal = round($hoursTotal / 3600, 4);
        $salesHourTotal = round($salesTotal / $hoursTotal, 1);
        $dataCampaignStatsTotal['hoursTotal'] = $hoursTotal;
        $dataCampaignStatsTotal['salesHourTotal'] = $salesHourTotal;
        // $dataCampaignStatsTotal['queryhoursTotal'] = $queryHoursTotal;

	    // Conversion Total
	    $queryConversionTotal = "SELECT DISTINCT val.agent_log_id, val.*, vu.user_id FROM `asteriskV4`.`vicidial_log` vl, `asteriskV4`.`vicidial_agent_log` val INNER JOIN `asteriskV4`.`vicidial_users` vu ON vu.user = val.user LEFT JOIN `goautodialV4`.`go_campaigns` gc ON gc.campaign_id=val.campaign_id WHERE vl.uniqueid=val.uniqueid AND val.status IN ('$CampaignTotalStatsstatuses', 'QualR', 'QUANS') AND {$dateQueryTotal} {$locationQuery} {$userQuery};";
	    $resultConversionTotal = $astDB->rawQuery($queryConversionTotal);
	    $countConvertionTotal = $astDB->getRowCount();
        $conversionTotal = 0;
        // if(sizeof($resultConversionTotal) > 0)
        if($countConvertionTotal > 0){
            // $conversionTotal = $salesTotal / sizeof($countConvertionTotal) * 100;
            $conversionTotal = $salesTotal / $countConvertionTotal * 100;
        }
        // $contactsHourTotal = round(sizeof($countConvertionTotal) / $hoursTotal, 1);
        $contactsHourTotal = round($countConvertionTotal / $hoursTotal, 1);
        $dataCampaignStatsTotal['conversionTotal'] = $conversionTotal;
        $dataCampaignStatsTotal['contactsHourTotal'] = $contactsHourTotal;
        // $dataCampaignStatsTotal['queryconversionTotal'] = $queryConversionTotal;
    // End of Campaign Stats Total


    $apiresults = array(
    	"result" => "success", 
    	"campaignStatsToday" => $dataCampaignStatsToday,
    	"campaignStats" => $dataCampaignStats,
    	"campaignStatsTotal" => $dataCampaignStatsTotal
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