<?php
 /**
 * @file 		goGetSMTPActivation.php
 * @brief 		API for SMTP
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Alexander Abenoja  <alex@goautodial.com>
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

	//$query = "SELECT value FROM settings WHERE setting = 'enable_smtp' LIMIT 1;";
	$goDB->where('setting', 'enable_smtp');
	$rsltv = $goDB->getOne('settings', 'value');
	$exist = $goDB->getRowCount();
	
	if($exist > 0){
		$data = $rsltv;
		
		$apiresults = array("result" => "success", "data" => $data);
	} else {
		$apiresults = array("result" => "SMTP Setting doesn't exist. Please configure a valid SMTP Setting to continue.");
	}
?>