<?php
 /**
 * @file 		goAddWhatsappSettings.php
 * @brief 		API for Whatsapp
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

	//$query = "SELECT * FROM whatsapp_settings LIMIT 1;";
	$rsltv = $goDB->getOne('settings')
		->where('settings', 'GO_whatsapp_user');
	$exist = mysqli_num_rows($rsltv);
	
	if(!$exist){
		$user = $astDB->escape($_REQUEST['GO_whatsapp_user']); 
		$token = $astDB->escape($_REQUEST['GO_whatsapp_token']); 
		$host = $astDB->escape($_REQUEST['GO_whatsapp_host']); 
		$instance = $astDB->escape($_REQUEST['GO_whatsapp_instance']); 	
		$callback_url = $astDB->escape($_REQUEST['GO_whatsapp_callback_url']); 
		
		//$insert_query = "INSERT INTO settings(setting, context, value)
		//VALUES('GO_whatsapp_user', 'module_GOautodialWhatsApp', $user);";

		$settings = array(
			'GO_whatsapp_user' => $user, 
			'GO_whatsapp_token' => $token, 
			'GO_whatsapp_host' => $host, 
			'GO_whatsapp_instance' => $instance, 
			'GO_whatsapp_callback_url' => $callback_url
		);
		
		foreach($settings as $setting => $key){
			$insertData = array(
				'setting' => $setting,
				'context' => "module_GOautodialWhatsApp",
				'value' => $key
			);

			$execute_insert = $goDB->insert('settings', $insertData);
		}

		if($execute_insert){
			$apiresults = array("result" => "success", "query" => $goDB->getLastQuery());
		}else{
			$apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => $insertData);
		}
	} else {
		$apiresults = array("result" => "error", "msg" => "Whatsapp Settings already exists.");
	}
?>
