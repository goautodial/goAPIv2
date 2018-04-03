<?php
 /**
 * @file 		goEditSMTP.php
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
	$exist = $goDB->getRowCount();
	
	if($exist <= 1){
		$debug = $goDB->escape($_REQUEST['debug']); 	// if debug on... 0 = off, 1= client messages, 2 = client and server messages, 3 = timeout
		$timezone = $goDB->escape($_REQUEST['timezone']); 	// set date default timezone
		$ipv6_support = $goDB->escape($_REQUEST['ipv6_support']); 	// if your network does not support SMTP over IPv6... 0 = unsupported, 1 = supported
		$host = $goDB->escape($_REQUEST['host']); 	//Set the hostname of the mail server
		$port = $goDB->escape($_REQUEST['port']); 	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$smtp_security = $goDB->escape($_REQUEST['smtp_security']); 	//Set the encryption system to use - ssl (deprecated) or tls
		$smpt_auth = $goDB->escape($_REQUEST['smtp_auth']); 	//Whether to use SMTP authentication
		$username = $goDB->escape($_REQUEST['username']); 	//Username to use for SMTP authentication - use full email address for gmail
		$password = $goDB->escape($_REQUEST['password']); 	//Password to use for SMTP authentication
		
		$password = encrypt_decrypt('encrypt', $password);
		
		if($password != NULL || $password != "")
			$password_sql = "password = '$password'";
		else
			$password_sql = "";
		
		$update_query = "UPDATE smtp_settings
		SET
		debug = '$debug',
		ipv6_support = '$ipv6_support',
		host = '$host',
		port = '$port',
		smtp_security = '$smtp_security',
		smtp_auth = '$smpt_auth',
		username = '$username',
		$password_sql;";
		$execute_update = $goDB->rawQuery($update_query);
		
		if($execute_update){
			$apiresults = array("result" => "success", "query" => $update_query);
		}else{
			$apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => $insert_query);
		}
	} else {
		$apiresults = array("result" => "SMTP Setting already exists. Only one SMTP Setting is allowed. You can either delete and recreate or update the current SMTP Settings.");
	}
	
	function encrypt_decrypt($action, $string) {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
?>
