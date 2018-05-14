<?php
/**
 * @file        goAddHotkey.php
 * @brief       API to add new hotkey
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

    $campaign_id    = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
    $hotkey         = mysqli_real_escape_string($link, $_REQUEST['hotkey']);
    $status         = mysqli_real_escape_string($link, $_REQUEST['status']);
    $status_name    = mysqli_real_escape_string($link, $_REQUEST['status_name']);
    $ip_address     = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
    $log_user       = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group      = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    $astDB->where('campaign_id', $campaign_id);
    $astDB->where('hotkey', $hotkey);
    $astDB->orwhere('status', $status);
    $hotkeys = $astDB->get('vicidial_campaign_hotkeys', null, '*');
    
    if(count($hotkeys) > 0) {
        $apiresults = array("result" => "duplicate");
    } else {
        $data_insert = array(
            'status'        => $status,
            'hotkey'        => $hotkey,
            'status_name'   => $status_name,
            'selectable'    => 'Y',
            'campaign_id'   => $campaign_id
        );
        $insertHotkey = $astDB->insert('vicidial_campaign_hotkeys', $data_insert);
        $insertQuery = $astDB->getLastQuery();
        
        if($insertHotkey) {
            $log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Hotkey $status on Campaign $campaign_id", $log_group, $insertQuery);
            
            $apiresults = array("result" => "success");
        } else {
            $apiresults = array("result" => "Error: Failed to add campaign hotkey.");
        }
    }
?>