<?php
 /**
 * @file 		goEditWhatsappSettings.php
 * @brief 		API for Whatsapp
 * @copyright 	Copyright (C) 2020 GOautodial Inc.
 * @author     	Thom Bernarth Patacsil
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

	//$query = "SELECT * FROM smtp_settings LIMIT 1;";
	$rsltv = $goDB->getOne('settings')
		->where('settings', 'GO_whatsapp_user');
	$exist = $goDB->getRowCount();
	
	if($exist <= 1){
		$user = $astDB->escape($_REQUEST['GO_whatsapp_user']); 
		$token = $astDB->escape($_REQUEST['GO_whatsapp_token']); 
		$host = $astDB->escape($_REQUEST['GO_whatsapp_host']); 
		$instance = $astDB->escape($_REQUEST['GO_whatsapp_instance']); 	
		$callback_url = $astDB->escape($_REQUEST['GO_whatsapp_callback_url']); 

		$settings = array(
			'GO_whatsapp_user' => $user, 
			'GO_whatsapp_token' => $token, 
			'GO_whatsapp_host' => $host, 
			'GO_whatsapp_instance' => $instance, 
			'GO_whatsapp_callback_url' => $callback_url
		);
		
		foreach($settings as $setting => $key){
			$data = array(
				'value' => $key
			);
			$execute_update = $goDB->update('settings', $data)->where('setting', $setting);
		}

		if($execute_update){
			$apiresults = array("result" => "success");
		}else{
			$apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.");
		}
	} 
?>
