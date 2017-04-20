<?php
    ############################################################
    #### Name: goGetSMTPSettings.php 			####
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
			$delete_query = "DELETE FROM smtp_settings;";
			$execute_delete = mysqli_query($linkgo, $delete_query);
			$apiresults = array("result" => "success");
		} else {
			$apiresults = array("result" => "No SMTP Setting exists. Please configure a valid SMTP Setting first.");
		}
?>
