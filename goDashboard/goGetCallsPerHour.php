<?php
 /**
 * @file 		goGetCallsPerHour.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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

	$groupId = go_get_groupid($session_user, $astDB);

    if (checkIfTenant($groupId, $goDB)) {
        $ul = "";
    } else {
        $stringv = go_getall_allowed_campaigns($groupId, $astDB);
		if(!preg_match("/ALLCAMPAIGNS/", $stringv))
			$ul = " and campaign_id IN ($stringv) ";
		else
			$ul = "";
    }
    
	##### getting timezone ######
    $goDB->where('setting', 'timezone');
    $rslt = $goDB->getOne('settings', 'value');
    $tz = $rslt['value'];
	if (!empty($tz)) {
        ini_set('date.timezone', $tz);
        date_default_timezone_set($tz);
	}

    $query_date =  date('Y-m-d');

    ### Original Query for Inbound

    //inbound
    //$query = "SELECT date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21' from vicidial_closer_log WHERE date_format(call_date, '%Y-%m-%d') = '$query_date' $ul;";
    $query = "SELECT date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23',sum(if(date_format(call_date,'%H') = 24, 1, 0)) as 'Hour24' from vicidial_closer_log WHERE date_format(call_date, '%Y-%m-%d') = CURDATE() $ul GROUP BY cdate;";
    $fresults = $astDB->rawQuery($query);
    //$fresults = mysqli_fetch_assoc($rsltv);
	
    if ($fresults == NULL) {
        $fresults = array();
    }


    //dropped
    $queryDropped = "select date_format(call_date, '%Y-%m-%d') as cdated,sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1d',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2d',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3d',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4d',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5d',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6d',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7d',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8d',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9d',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10d',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11d',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12d',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13d',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14d',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15d',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16d',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17d',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18d',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19d',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20d',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21d',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22d',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23d',sum(if(date_format(call_date,'%H') = 24, 1, 0)) as 'Hour24d' from vicidial_closer_log WHERE term_reason='ABANDON' AND date_format(call_date, '%Y-%m-%d') = CURDATE() $ul GROUP BY cdated;";
    $dresults = $astDB->rawQuery($queryDropped);
    //$dresults = mysqli_fetch_assoc($rsltd);
    
    if ($dresults == NULL) {
        $dresults = array();
    }


    //outbound
    $queryOut = "select date_format(call_date, '%Y-%m-%d') as cdateo,sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1o',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2o',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3o',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4o',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5o',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6o',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7o',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8o',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9o',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10o',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11o',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12o',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13o',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14o',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15o',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16o',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17o',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18o',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19o',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20o',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21o',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22o',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23o',sum(if(date_format(call_date,'%H') = 24, 1, 0)) as 'Hour24o' from vicidial_log WHERE date_format(call_date, '%Y-%m-%d') = CURDATE() $ul GROUP BY cdateo";
    $oresults = $astDB->rawQuery($queryOut);
    //$oresults = mysqli_fetch_assoc($rsltOut);
    
    if ($oresults == NULL) {
        $oresults = array();
    }
    

    //$apiresults = array_merge( array( "result" => "success" ,"THISss" => $query ), $fresults, $dresults, $oresults);
    $apiresults = array_merge( array( "result" => "success" ), $fresults, $dresults, $oresults);
    //$apiresults = array( "result" => "success" , "inboundcph" => $fresults, "outboundcph" => $oresults, "droppedcph" => $dresults);
?>
