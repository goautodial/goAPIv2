<?php
####################################################
#### Name: goGetInboundGroups.php               ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
###################################################

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