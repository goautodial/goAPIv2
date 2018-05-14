<?php
/**
 * @file        goGetStatusesWithCountCalledNCalled.php
 * @brief       API to get timezones with called and not called
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <noelumandap@goautodial.com>
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
    
    $list_id = $astDB->escape($_REQUEST['list_id']);
    // GROUP BY gmt_offset_now,called_since_last_reset
    $query = "SELECT
				gmt_offset_now,
				called_since_last_reset,
				count(*) as counttlist
			FROM vicidial_list
			WHERE list_id='$list_id'
			GROUP BY gmt_offset_now 
			ORDER BY gmt_offset_now,called_since_last_reset;";
	$rsltv = $astDB->rawQuery($query);
    
    foreach ($rsltv as $fresults) {
		$dataGMT[]                	=  $fresults['gmt_offset_now'];
        $dataCalledSinceLastReset[] =  $fresults['called_since_last_reset'];
        $dataCountTLists[]          =  $fresults['counttlist'];
	}
    $apiresults = array(
            "result"                    => "success",
            "gmt_offset_now"            => $dataGMT,
            "called_since_last_reset"   => $dataCalledSinceLastReset,
            "counttlist"                => $dataCountTLists
        );
?>