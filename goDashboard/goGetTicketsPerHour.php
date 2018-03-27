<?php
 /**
 * @file 		goGetTicketsPerHour.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Demian Lizandro A. Biscocho  <demian@goautodial.com>
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

    $groupId = go_get_groupid($goUser, $astDB);

    if (!checkIfTenant($groupId, $goDB)) {
        $ul = "";
    } else {
        $stringv = go_getall_allowed_campaigns($goUser, $astDB);
        $ul = " and campaign_id IN ('$stringv') ";
    }

    $query_date =  date('Y-m-d');

    //tickets
    $ostquery = "SELECT date_format(created, '%Y-%m-%d') as tdate,sum(if(date_format(created,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(created,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(created,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(created,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(created,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(created,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(created,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(created,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(created,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(created,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(created,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(created,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(created,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(created,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(created,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(created,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(created,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(created,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(created,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(created,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(created,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(created,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(created,'%H') = 23, 1, 0)) as 'Hour23',sum(if(date_format(created,'%H') = 24, 1, 0)) as 'Hour24' from ost_ticket WHERE date_format(created, '%Y-%m-%d') = CURDATE()";
    $fresults = $ostDB->rawQuery($ostquery);
    //$fresults = mysqli_fetch_assoc($rsltv);
	
    if ($fresults == NULL) {
        $fresults = array();
    }
    
    $apiresults = array_merge( array( "result" => "success" ), $fresults);
    
?>
