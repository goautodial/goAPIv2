<?php
    #######################################################
    #### Name: getAllCountryCodes.php	               ####
    #### Description: API to get all campaigns         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    
    //$query = "SELECT country_code, country FROM vicidial_phone_codes";
   	$rsltv = $astDB->get('vicidial_phone_codes', null, 'country_code, country');
	$codeCnt = $astDB->getRowCount();
    
	if ($codeCnt > 0) {
		foreach ($rsltv as $fresults){
			$dataCountryCode[] = $fresults['country_code'];
			$dataCountry[] = $fresults['country'];
			$apiresults = array(
				"result" => "success",
				"country_code" => $dataCountryCode,
				"country" => $dataCountry,
			);
		}
	}
?>