<?php
 /**
 * @file 		goGetInboundGroups.php
 * @brief 		API for Agent UI
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad <chris@goautodial.com>
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

$astDB->where('campaign_id', $campaign);
$query = $astDB->getOne('vicidial_campaigns', 'campaign_allow_inbound,closer_campaigns');

if ($query['campaign_allow_inbound'] == 'Y') {
    $inb_groups = explode(" ", $query['closer_campaigns']);
    foreach ($inb_groups as $inb) {
        if ($inb != "" && $inb != '-') {
            $astDB->where('group_id', $inb);
            $inbQ = $astDB->getOne('vicidial_inbound_groups', 'group_name');
            if ($astDB->getRowCount() > 0) {
                $inbound_groups[$inb] = $inbQ['group_name'];
            }
        }
    }
}

if (count($inbound_groups)) {
    ksort($inbound_groups);
    $APIResult = array( "result" => "success", "data" => array("inbound_groups" => $inbound_groups) );
} else {
    $APIResult = array( "result" => "error", "message" => "No inbound groups assigned" );
}
?>