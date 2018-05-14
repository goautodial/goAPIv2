<?php
/**
 * @file    goUpdateCampaignDialStatus.php
 * @brief     API to update campaign dial status
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
    $goUser = $astDB->escape($_REQUEST['goUser']);
    $ip_address = $astDB->escape($_REQUEST['hostname']);
    $campaign_id  = $astDB->escape($_REQUEST['campaign_id']);
    $dial_statuses  = $astDB->escape($_REQUEST['dial_status']);
    $log_user = $astDB->escape($_REQUEST['log_user']);
    $log_group  = $astDB->escape($_REQUEST['log_group']);
    
    if($campaign_id != null) {
        $astDB->where('campaign_id', $campaign_id);
        $updateQuery = $astDB->update('vicidial_campaigns', array('dial_statuses' => $dial_status));

        $log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Updated Dial Statuses for Campaign ID: $campaign_id", $log_group, $updateQuery);
        
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Campaign doens't exist.");
    }
    
?>