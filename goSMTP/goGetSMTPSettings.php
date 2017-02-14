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
		
		if($exist > 0){
			$data = mysqli_fetch_array($rsltv);
			
			$apiresults = array("result" => "success", "data" => $data);
		} else {
			$apiresults = array("result" => "SMTP Setting doesn't exist. Please configure a valid SMTP Setting to continue.");
		}
?>
