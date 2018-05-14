<?php
/**
 * @file        goGetDIDSettings.php
 * @brief       API to get DID Settings for a DID
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
    $did = $_REQUEST['did'];

    $astDB->where('did_pattern', $did);
    $astDB->join('vicidial_inbound_groups groupSetting', 'didSetting.group_id = groupSetting.group_id ', 'left');
    $result = $astDB->get('vicidial_inbound_dids didSetting', null, 'didSetting.did_id,didSetting.did_pattern,didSetting.did_route,didSetting.group_id,didSetting.menu_id,didSetting.user,didSetting.voicemail_ext,groupSetting.group_color');

    foreach($result as $info){
        $data['did_id']         = $info['did_id'];
        $data['did_pattern']    = $info['did_pattern'];
        $data['did_route']      = $info['did_route'];
        $data['group_id']       = $info['group_id'];
        $data['menu_id']        = $info['menu_id'];
        $data['user']           = $info['user'];
        $data['voicemail_ext']  = $info['voicemail_ext'];
        $data['group_color']    = $info['group_color'];
    }
    
    if(count($data)) {
        $apiresults = array("result" => "success", "data" => $data);
    } else {
        $apiresults = array("result" => "error");
    }
?>