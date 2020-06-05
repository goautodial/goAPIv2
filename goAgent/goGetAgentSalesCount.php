<?php
 /**
 * @file 		goGetAgentSalesCount.php
 * @brief 		API for Agent UI
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad <chris@goautodial.com>
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
 
if (isset($_GET['filter_date'])) { $filter_date = $astDB->escape($_GET['filter_date']); }
    else if (isset($_POST['filter_date'])) { $filter_date = $astDB->escape($_POST['filter_date']); }

$filter_date = (empty($filter_date) ? $NOW_DATE : $filter_date);

if ($goDB->has('go_sales_count')) {
    $closer_campaigns = go_getall_closer_campaigns($campaign, $astDB);
    $filter_campaigns = explode("','",trim($closer_campaigns, "'"));
    array_push($filter_campaigns, $campaign);
    
    $goDB->where('user', $goUser);
    //$goDB->where('campaign_id', $filter_campaigns, 'IN');
    $goDB->where('entry_date', $filter_date);
    $goDB->groupBy('user');
    $result = $goDB->getOne('go_sales_count', 'SUM(sales) AS sales, SUM(amount) AS amount');
    $s_cnt = $goDB->getRowCount();
    
    if ($s_cnt > 0) {
        $APIResult 							= array( 
            "result" 							=> "success", 
            "data" 							    => $result
        );
    } else {
        $APIResult 							= array( 
            "result" 							=> "error", 
            "message" 							=> "No result for user $goUser." 
        );
    }
} else {
    $APIResult 							= array( 
        "result" 							=> "error", 
        "message" 							=> "Table `go_sales_count` DOES NOT exist on the database." 
    );
}