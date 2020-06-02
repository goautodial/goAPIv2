<?php
/**
 * @file        goGetSalesAgent.php
 * @brief       API for Sales Agent Report on Dashboard for Statewide
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author		Thom Bernarth D. Patacsil
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it AND/or modify
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

    include_once("goAPI.php");

    $fromDate = "";
    $toDate = "";
    $campaignID				= $astDB->escape($_REQUEST['campaign_id']);
    $campaignID				= (!empty($campaignID) ? $campaignID : 'ALL');
	
    if (empty($fromDate)) {
    	$fromDate			= date("Y-m-d");
    }
    
    if (empty($toDate)) {
    	$toDate 			= date("Y-m-d");
    }
		
	if (empty($log_user) || is_null($log_user)) {
		$apiresults = array(
			"result" => "Error: Session User Not Defined."
		);
	} elseif ( empty($campaignID) || is_null($campaignID) ) {
		$err_msg = error_handle("40001");
        	$apiresults = array(
			"code" => "40001",
			"result" => $err_msg
		);
	} else {            
		$cols = array(
			"user",
			"full_name",
			"sales as sale",
			"amount"
		);

		$sql_sales = $goDB->where('entry_date', array($fromDate, $toDate), 'BETWEEN')
				->where('amount', 0, '>')
				->get('go_sales_count', null, $cols);

		$apiresults = array(
			"result"			=> "success",
			"amount"			=> $sql_sales,
		);
			
		return $apiresults;
	}

?>
