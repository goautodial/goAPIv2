<?php
    ############################################################
    #### Name: goAddSMTPSettings.php 			####
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
		
		if($exist > 0){
			$apiresults = array("result" => "SMTP Setting already exists. Only one SMTP Setting is allowed. You can either delete and recreate or update the current SMTP Settings.");
		} else {
			
			$debug = mysqli_real_escape_string($_REQUEST['debug']); 	// if debug on... 0 = off, 1= client messages, 2 = client and server messages, 3 = timeout
			$timezone = mysqli_real_escape_string($_REQUEST['timezone']); 	// set date default timezone
			$ipv6_support = mysqli_real_escape_string($_REQUEST['ipv6_support']); 	// if your network does not support SMTP over IPv6... 0 = unsupported, 1 = supported
			$host = mysqli_real_escape_string($_REQUEST['host']); 	//Set the hostname of the mail server
			$port = mysqli_real_escape_string($_REQUEST['port']); 	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$smtp_security = mysqli_real_escape_string($_REQUEST['smtp_security']); 	//Set the encryption system to use - ssl (deprecated) or tls
			$smpt_auth = mysqli_real_escape_string($_REQUEST['smtp_auth']); 	//Whether to use SMTP authentication
			$username = mysqli_real_escape_string($_REQUEST['username']); 	//Username to use for SMTP authentication - use full email address for gmail
			$password = mysqli_real_escape_string($linkgo, $_REQUEST['password']); 	//Password to use for SMTP authentication
			
			$insert_query = "INSERT INTO smtp_settings(debug, timezone, ipv6_support, host, port, smtp_security, smtp_auth, username, password)
			VALUES('$debug','$timezone','$ipv6_support','$host','$port','$smtp_security','$smpt_auth','$username','$password');";
			$execute_insert = mysqli_query($linkgo, $insert_query);
			
			if($execute_insert){
				$apiresults = array("result" => "success", "query" => $insert_query);
			}else{
				$apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => $insert_query);
			}
			
		}
?>
