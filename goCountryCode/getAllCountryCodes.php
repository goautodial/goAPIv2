<?php
    #######################################################
    #### Name: getAllCountryCodes.php	               ####
    #### Description: API to get all campaigns         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $query = "SELECT country_code, country FROM vicidial_phone_codes";
   	$rsltv = mysqli_query($link, $query);
    
    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataCountryCode[] = $fresults['country_code'];
       	$dataCountry[] = $fresults['country'];
   		$apiresults = array(
                        "result" => "success",
                        "country_code" => $dataCountryCode,
                        "country" => $dataCountry,
                    );
	}
?>