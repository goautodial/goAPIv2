<?php
/**
 * @file        goGetStatusesWithCountCalledNCalled.php
 * @brief       API to get statuses with called and not called
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
    #######################################################
    #### Name: goGetListInfo.php	                   ####
    #### Description: API to get specific List	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2016-2017      ####
    #### Written by: NOEL UMANDAP                      ####
    #### License: AGPLv2                               ####
    #######################################################
    
    $list_id = $astDB->escape($_REQUEST['list_id']);
    // GROUP BY vicidial_list.status,vicidial_list.called_since_last_reset
    // $query = "SELECT
    //             vicidial_list.status as stats,
    //             vicidial_list.called_since_last_reset,
    //             count(*) as countvlists,
    //             vicidial_statuses.status_name
    //         FROM vicidial_list
    //         LEFT JOIN vicidial_statuses
    //         ON vicidial_list.status=vicidial_statuses.status
    //         WHERE vicidial_list.list_id='$list_id'
    //         GROUP BY vicidial_list.status, substr(vicidial_list.called_since_last_reset,1,1) 
    //         ORDER BY vicidial_list.status,vicidial_list.called_since_last_reset;";
    $query = "SELECT
                vicidial_list.status as stats,
                SUM(IF(substr(vicidial_list.called_since_last_reset,1,1)='Y',1,0)) AS 'is_called',
                SUM(IF(substr(vicidial_list.called_since_last_reset,1,1)='N',1,0)) AS 'not_called',
                count(*) as countvlists,
                vicidial_statuses.status_name
            FROM vicidial_list
            LEFT JOIN vicidial_statuses
            ON vicidial_list.status=vicidial_statuses.status
            WHERE vicidial_list.list_id='$list_id' 
            GROUP BY vicidial_list.status 
            ORDER BY vicidial_list.status,vicidial_list.called_since_last_reset;";
	$rsltv = $astDB->rawQuery($query);
    
    foreach ($rsltv as $fresults) {
		$dataStats[]		=  $fresults['stats'];
        $dataIsCalled[]		=  $fresults['is_called'];
        $dataNotCalled[]	=  $fresults['not_called'];
        $dataCountVLists[]	=  $fresults['countvlists'];
        $dataStatName[]		=  $fresults['status_name'];

		$apiresults = array(
			"result"		=> "success",
			"stats"			=> $dataStats,
            "is_called"		=> $dataIsCalled,
            "not_called"	=> $dataNotCalled,
            "countvlists"	=> $dataCountVLists,
            "status_name"	=> $dataStatName
            // "query" => $query
		);
	}
?>