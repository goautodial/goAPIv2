<?php
    ############################################################
    #### Name: goEditSMTP.php 			####
    #### Description: API to get SMTP Settings 			####
    #### Version: 4.0 			####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016 			####
    #### Written by: Alexander Jim H. Abenoja 			####
    #### License: AGPLv2 			####
    ############################################################
    
    include_once ("../goFunctions.php");
		
		$query = "SELECT * FROM smtp_settings LIMIT 1;";
		$rsltv = mysqli_query($linkgo, $query);
		$exist = mysqli_num_rows($rsltv);
		
		if($exist <= 1){
			
			$debug = mysqli_real_escape_string($linkgo, $_REQUEST['debug']); 	// if debug on... 0 = off, 1= client messages, 2 = client and server messages, 3 = timeout
			$timezone = mysqli_real_escape_string($linkgo, $_REQUEST['timezone']); 	// set date default timezone
			$ipv6_support = mysqli_real_escape_string($linkgo, $_REQUEST['ipv6_support']); 	// if your network does not support SMTP over IPv6... 0 = unsupported, 1 = supported
			$host = mysqli_real_escape_string($linkgo, $_REQUEST['host']); 	//Set the hostname of the mail server
			$port = mysqli_real_escape_string($linkgo, $_REQUEST['port']); 	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$smtp_security = mysqli_real_escape_string($linkgo, $_REQUEST['smtp_security']); 	//Set the encryption system to use - ssl (deprecated) or tls
			$smpt_auth = mysqli_real_escape_string($linkgo, $_REQUEST['smtp_auth']); 	//Whether to use SMTP authentication
			$username = mysqli_real_escape_string($linkgo, $_REQUEST['username']); 	//Username to use for SMTP authentication - use full email address for gmail
			$password = mysqli_real_escape_string($linkgo, $_REQUEST['password']); 	//Password to use for SMTP authentication
			
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
			$execute_update = mysqli_query($linkgo, $update_query);
			
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
