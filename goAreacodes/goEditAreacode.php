<?php
/**
 * @file        goEditAreacode.php
 * @brief       API to modify an areacode
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <noelumandap@goautodial.com>
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

  $campaign_id = $_REQUEST['campaign_id'];
  $areacode = $_REQUEST['areacode'];

  $outbound_cid = $astDB->escape($_REQUEST['outbound_cid']);
  $active = $_REQUEST['active'];
  $cid_description = $astDB->escape($_REQUEST['cid_description']);

	if (empty($goUser) || is_null($goUser)) {
                $apiresults                                                                     = array(
                        "result"                                                                                => "Error: goAPI User Not Defined."
                );
        } elseif (empty($goPass) || is_null($goPass)) {
                $apiresults                                                                     = array(
                        "result"                                                                                => "Error: goAPI Password Not Defined."
                );
        } elseif (empty($log_user) || is_null($log_user)) {
                $apiresults                                                                     = array(
                        "result"                                                                                => "Error: Session User Not Defined."
                );
        } elseif (empty($campaign_id) || is_null($campaign_id)) {
                $apiresults                                                                     = array(
                        "result"                                                                                => "Error: Campaign ID not Defined."
                );
        } elseif (empty($areacode) || is_null($areacode)) {
                $apiresults                                                                     = array(
                        "result"                                                                                => "Error: Areacode not Defined."
                );
        } else {

		$astDB->where('campaign_id', $campaign_id);
		$astDB->where('areacode', $areacode);

		$cols = array(
			'outbound_cid',
			'active',
			'cid_description',
		);

		$result = $astDB->getOne('vicidial_campaign_cid_areacodes', null, $cols);
  
		$dataOutboundCID = $result['outbound_cid'];
	  	$dataActive = $result['active'];
		$dataDescription = $result['cid_description'];

		if(!isset($outbound_cid)){
			$outbound_cid = $dataOutboundCID;
		}

		if(!isset($cid_description)){
			$cid_description = $dataDescription;
		}
  
		$data = array(
			"outbound_cid"		=> $outbound_cid,
			"active"		=> $active,
			"cid_description"	=> $cid_description
		);

		$astDB->where('campaign_id', $campaign_id);
		$astDB->where('areacode', $areacode);
		$update = $astDB->update('vicidial_campaign_cid_areacodes', $data);

		if($update) {
   			$log_id = log_action( $goDB, 'MODIFY', $log_user, $log_ip, "Updated Areacode: $areacode for campaign: $campaign_id", $log_group, $astDB->getLastQuery());

    			$apiresults = array(
				"result" => "success"
			);
		} else {
			$apiresults = array(
	                	"result" => "error",
				"error" => $astDB->getLastError()
		        );
		}
	}
	return $apiresults;

	
?>
