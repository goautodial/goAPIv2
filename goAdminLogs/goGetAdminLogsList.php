<?php
#######################################################
#### Name: goGetAdminLogsList.php	               ####
#### Description: API to get all admin logs        ####
#### Version: 0.9                                  ####
#### Copyright: GOAutoDial Inc. (c) 2016           ####
#### Written by: Alexander Jim H. Abenoja          ####
#### Modified by: Christopher Lomuntad             ####
#### License: AGPLv2                               ####
#######################################################

if ($goUserGroup !== 'ADMIN') {
	$goDB->where('user_group', $goUserGroup);
}
if (strlen($limit) < 1 || ($limit < 1 && $limit > 1000)) {
	$limit = 1000;
}
$adminLogs = $goDB->get('go_action_logs', $limit);

foreach ($adminLogs as $log) {
	$result[] = $log;
}

$APIResult = array( "result" => "success", "data" => $adminLogs );
?>
