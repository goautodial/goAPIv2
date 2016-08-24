<?php
####################################################
#### Name: goCheckIfLoggedIn.php                ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$astDB->where('sess_agent_user', $goUser);
$astDB->where('sess_agent_status', 'INUSE');
$rslt = $astDB->getOne('go_agent_sessions');
$is_logged_in = $astDB->getRowCount();

$APIResult = array( "result" => "success", "logged_in" => $is_logged_in, "message" => "You have been logged out from the dialer." );
?>