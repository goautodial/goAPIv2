<?php
/**
 * @file        goGetInboundReport.php
 * @brief       API reports for inbound report
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 * @author		John Ezra Gois
 * @author		Demian Lizandro A. Biscocho
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
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

    include_once("goAPI.php");

    $fromDate 										= $astDB->escape($_REQUEST['fromDate']);
    $toDate 										= $astDB->escape($_REQUEST['toDate']);
    $campaignID 									= $astDB->escape($_REQUEST['campaignID']);
    $dispo_stats 									= $astDB->escape($_REQUEST['statuses']);
    
    if (empty($fromDate)) {
        $fromDate 									= date("Y-m-d")." 00:00:00";
    }
    if (empty($toDate)) {
        $toDate 									= date("Y-m-d")." 23:59:59";
    }

    if (empty($log_user) || is_null($log_user)) {
        $apiresults 								= array(
            "result" 									=> "Error: Session User Not Defined."
        );
    } elseif ( empty($campaignID) || is_null($campaignID) ) {
        $err_msg 									= error_handle("40001");
        $apiresults 								= array(
                "code" 									=> "40001",
                "result" 								=> $err_msg
 		);
    } elseif (empty($fromDate) && empty($toDate)) {
	    $fromDate 									= date("Y-m-d") . " 00:00:00";
	    $toDate 									= date("Y-m-d") . " 23:59:59";
    	//die($fromDate." - ".$toDate);                                                                 => $err_msg
    } else {
	    // set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
	    // every time we need to filter out requests
	    $tenant 									=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;

	    if ($tenant) {
	            $astDB->where("user_group", $log_group);
	    } else {
            if (strtoupper($log_group) != 'ADMIN') {
                if ($user_level < 9) {
                    $astDB->where("user_group", $log_group);
                }
            }
	    }

		// check if MariaDB slave server available
		$rslt										= $goDB
			->where('setting', 'slave_db_ip')
			->where('context', 'creamy')
			->getOne('settings', 'value');
		$slaveDBip 									= $rslt['value'];
		
		if (!empty($slaveDBip)) {
			$astDB 									= new MySQLiDB($slaveDBip, $VARDB_user, $VARDB_pass, $VARDB_database);

			if (!$astDB) {
				echo "Error: Unable to connect to MariaDB slave server." . PHP_EOL;
				echo "Debugging Error: " . $astDB->getLastError() . PHP_EOL;
				exit;
				//die('MySQL connect ERROR: ' . mysqli_error('mysqli'));
			}			
		}
		
		if ($dispo_stats != NULL) {
			$ul 									= " AND status = '$dispo_stats' ";
		} else {
			$ul 									= "";
		}

		$inbound_report_query 						= "
		    SELECT * FROM vicidial_closer_log
		    WHERE campaign_id = '$campaignID' $ul
		    AND date_format(call_date, '%Y-%m-%d %H:%i:%s') BETWEEN '$fromDate' AND '$toDate'
		";

		$query 										= $astDB->rawQuery($inbound_report_query);
		$TOPsorted_output 							= "";
		$number 									= 1;

		foreach ($query as $row) {
			$TOPsorted_output[] 					.= '<tr>';
			$TOPsorted_output[] 					.= '<td nowrap>'.$number.'</td>';

		    $date 									= strtotime($row['call_date']);
			$date 									= date("Y-m-d", $date);
			$TOPsorted_output[] 					.= '<td nowrap>'.$date.'</td>';

		    $TOPsorted_output[] 					.= '<td nowrap>'.$row['user'].'</td>';
		    $TOPsorted_output[] 					.= '<td nowrap>'.$row['phone_number'].'</td>';

			//$time = strtotime($row['call_date']);
			$time 									= $row['end_epoch'] + $row['start_epoch'];
			$time 									= date("h:i:s", $time);
			$TOPsorted_output[] 					.= '<td nowrap>'.$time.'</td>';
			$TOPsorted_output[] 					.= '<td nowrap style="padding-left:40px;">'.$row['length_in_sec'].'</td>';
			$TOPsorted_output[] 					.= '<td nowrap>'.$row['status'].'</td>';
			$TOPsorted_output[] 					.= '</tr>';
			$number++;
		}

		$apiresults 								= array(
		    "result" 									=> "success",
		    "inbound_query" 							=> $inbound_report_query,
		    "query" 									=> $query,
		    "TOPsorted_output" 							=> $TOPsorted_output
		);

		return $apiresults;
	}
?>
