<?php
 /**
 * @file 		goGetWhatsappSettings.php
 * @brief 		API for Whatsapp
 * @copyright 	Copyright (C) 2020 GOautodial Inc.
 * @author		Thom Bernarth Patacsil
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

	//$query = "SELECT * FROM settings where context = 'module_GOautodialWhatsApp';";
	$cols = array(
		'setting',
		'context',
		'value'
	);
	$rsltv = $goDB->get('settings', null, $cols)
		->where('context', 'module_GOautodialWhatsApp');
	$exist = $goDB->getRowCount();
	
	if($exist > 0){
		$data = $rsltv;
		foreach($rsltv as $result){
			if($result['setting'] == 'GO_whatsapp_user')
				$user = $result['value'];
			if($result['setting'] == 'GO_whatsapp_token')
				$token = $result['value'];
			if($result['setting'] == 'GO_whatsapp_instance')
				$instance = $result['value'];
			if($result['setting'] == 'GO_whatsapp_host')
				$host = $result['value'];
			if($result['setting'] == 'GO_whatsapp_callback_url')
				$callback_url = $result['value'];
		}
		$apiresults = array("result" => "success", "user" => $user, "token" => $token, "instance" => $instance, "host" => $host, "callback_url" => $callback_url);
	} else {
		$apiresults = array("result" => "Whatsapp Setting doesn't exist. Please configure a valid Whatsapp Setting to continue.");
	}
?>