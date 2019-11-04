<?php
/**
 * @file        goGetAreacodeInfo.php
 * @brief       API to get the information of an areacode
 * @copyright   Copyright (C) 2019 GOautodial Inc.
 * @author      Thom Bernarth Patacsil
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

  $astDB->where('campaign_id', $campaign_id);
  $astDB->where('areacode', $areacode);

  $cols = array(
		'campaign_id',
		'areacode',
		'outbound_cid',
		'active',
		'cid_description',
		'call_count_today'
	);

  $result = $astDB->getOne('vicidial_campaign_cid_areacodes', null, $cols);
  
	  $dataCampID   = $result['campaign_id'];
	  $dataAreacode = $result['areacode'];
	  $dataOutboundCID = $result['outbound_cid'];
	  $dataActive = $result['active'];
	  $dataDescription = $result['cid_description'];
	  $dataCallCountToday = $result['call_count_today'];
  
  $apiresults = array(
    "result"        => "success",
    "campaign_id"        => $dataCampID,
    "areacode"		=> $dataAreacode,
    "outbound_cid"	=> $dataOutboundCID,
    "active"		=> $dataActive,
    "cid_description"	=> $dataDescription,
    "call_count_today"	=> $dataCallCountToday
  );
?>
