<?php
 /**
 * @file 		getAllCountryCodes.php
 * @brief 		API for Country Codes
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Noel Umandap  <noel@goautodial.com>
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