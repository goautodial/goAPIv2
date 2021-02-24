<?php
 /**
 * @file                goCheckWebrtc.php
 * @brief               API to retrieve Webrtc Setting On (1) or Off (0)
 * @copyright   Copyright (C) GOautodial Inc.
 * @author              Alexander Abenoja  <alex@goautodial.com>
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

        $user_id = $goDB->escape($_REQUEST['user_id']);
        
        $goDB->where('setting', 'GO_agent_use_wss');
        $rslt = $goDB->getOne('settings', 'value');

        if($rslt){
                $webrtc = $rslt['value'];
                if ($webrtc > 0 && (!empty($user_id) && !is_null($user_id))) {
                        $goDB->where('userid', $user_id);
                        $rsltu = $goDB->getOne('users', 'enable_webrtc');
                        if ($rsltu['enable_webrtc'] > -1) {
                                $webrtc = $rsltu['enable_webrtc'];
                        }
                }
                
                $apiresults = array("result" => $webrtc);
        } else {
                $apiresults = array("result" => "Failed to get Result.");
        }
?>

