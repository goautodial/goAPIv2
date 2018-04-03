<?php
 /**
 * @file 		goAddSMTPSettings.php
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

	//$query = "SELECT * FROM smtp_settings LIMIT 1;";
	$rsltv = $goDB->getOne('smtp_settings');
	$exist = mysqli_num_rows($rsltv);
	
	if($exist > 0){
		$apiresults = array("result" => "SMTP Setting already exists. Only one SMTP Setting is allowed. You can either delete and recreate or update the current SMTP Settings.");
	} else {
		
		$debug = $astDB->escape($_REQUEST['debug']); 	// if debug on... 0 = off, 1= client messages, 2 = client and server messages, 3 = timeout
		$timezone = $astDB->escape($_REQUEST['timezone']); 	// set date default timezone
		$ipv6_support = $astDB->escape($_REQUEST['ipv6_support']); 	// if your network does not support SMTP over IPv6... 0 = unsupported, 1 = supported
		$host = $astDB->escape($_REQUEST['host']); 	//Set the hostname of the mail server
		$port = $astDB->escape($_REQUEST['port']); 	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$smtp_security = $astDB->escape($_REQUEST['smtp_security']); 	//Set the encryption system to use - ssl (deprecated) or tls
		$smtp_auth = $astDB->escape($_REQUEST['smtp_auth']); 	//Whether to use SMTP authentication
		$username = $astDB->escape($_REQUEST['username']); 	//Username to use for SMTP authentication - use full email address for gmail
		$password = $astDB->escape($_REQUEST['password']); 	//Password to use for SMTP authentication
		
		//$insert_query = "INSERT INTO smtp_settings(debug, timezone, ipv6_support, host, port, smtp_security, smtp_auth, username, password)
		//VALUES('$debug','$timezone','$ipv6_support','$host','$port','$smtp_security','$smpt_auth','$username','$password');";
		$insertData = array(
			'debug' => $debug,
			'timezone' => $timezone,
			'ipv6_support' => $ipv6_support,
			'host' => $host,
			'port' => $port,
			'smtp_security' => $smtp_security,
			'smtp_auth' => $smtm_auth,
			'username' => $username,
			'password' => $password
		);
		$execute_insert = $goDB->insert('smtp_settings', $insertData);
		
		if($goDB->getInsertId() > 0){
			$apiresults = array("result" => "success", "query" => $goDB->getLastQuery());
		}else{
			$apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => $insert_query);
		}
	}
?>
