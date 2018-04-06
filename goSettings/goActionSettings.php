<?php
 /**
 * @file 		goActionSettings.php
 * @brief 		API for Settings
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jericho James Milo  <james@goautodial.com>
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

	$seats_param = $astDB->escape($_REQUEST["seats"]);
	
	if(!empty($seats_param) && $seats_param > 0) {
		$seats = $seats_param;
	} else {
		$seats = 0;
	}

	//$query = "SELECT * FROM settings WHERE setting = 'GO_licensed_seats' LIMIT 1;";
	$goDB->where('setting', 'GO_licensed_seats');
	$rsltv = $goDB->getOne('settings');
	$exist = $goDB->getRowCount();

	if($exist <= 0){
		//$create_default_query = "INSERT INTO settings (setting, context, value) VALUES('GO_licensed_seats', 'module_licensedSeats', '$seats');";
		$insertData = array(
			'setting' => 'GO_licensed_seats',
			'context' => 'module_licensedSeats',
			'value' => $seats
		);
		$exec_create_default = $goDB->insert('settings', $insertData);

		if($exec_create_default) {
			if($seats > 0)
				$msg_seats = $seats;
			else
				$msg_seats = "Unlimited";
			
			$apiresults = array("result" => "success", "msg" => "Created ( $msg_seats ) Default Licensed Seats.");
		} else {
			$apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => mysqli_error($exec_create_default) );
		}
	} else {
		//$update_query = "UPDATE settings SET value = '$seats' WHERE setting = 'GO_licensed_seats';";
		$goDB->where('setting', 'GO_licensed_seats');
		$exec_update = $goDB->update('settings', array('value' => $seats));
		
		if($goDB->getRowCount()){
			if($seats > 0)
				$msg_seats = $seats;
			else
				$msg_seats = "Unlimited";

			$apiresults = array("result" => "success", "msg" => "Updated ( $msg_seats ) Licensed Seats.");
		} else {
			$apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => mysqli_error($exec_update) );
		}
	}
?>