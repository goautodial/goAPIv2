<?php
/**
 * @file        goGetAllDNC.php
 * @brief       API to get all DNC or to search for a specific one
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
	$search = $astDB->escape($_REQUEST['search']);
	
    //$query = "SELECT phone_number, campaign_id from vicidial_campaign_dnc WHERE phone_number LIKE '$search%' OR campaign_id LIKE '$search%' LIMIT 1000;";
	//$query = "SELECT * FROM (SELECT a.phone_number AS phone_number, '' AS campaign_id FROM vicidial_dnc a UNION SELECT b.phone_number AS phone_number, b.campaign_id AS campaign_id FROM vicidial_campaign_dnc b) searchdnc where phone_number LIKE '$search%' OR campaign_id LIKE '$search%' LIMIT 1000;";
	$rsltv = $astDB->rawQuery("SELECT * FROM (SELECT a.phone_number AS phone_number, '' AS campaign_id FROM vicidial_dnc a UNION SELECT b.phone_number AS phone_number, b.campaign_id AS campaign_id FROM vicidial_campaign_dnc b) searchdnc where phone_number LIKE '$search%' OR campaign_id LIKE '$search%' LIMIT 1000;");
	$countResult = $astDB->getRowCount();
    
    if($countResult > 0) {
		foreach ($rsltv as $fresults){
			$dataPhoneNumber[]       = $fresults['phone_number'];
			$dataCampaign[]       = $fresults['campaign_id'];
		}
		
		$apiresults = array(
			"result"            => "success",
			"phone_number"      => $dataPhoneNumber,
			"campaign"			=> $dataCampaign
		);
    }else{
        $apiresults = array("result" => "Error: No record found.");
    }
?>