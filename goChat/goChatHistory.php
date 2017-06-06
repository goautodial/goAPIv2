<?php
####################################################
#### Name: goChatHistory.php                    ####
#### Type: API for Chat History                 ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

if (isset($_GET['user'])) { $user = $astDB->escape($_GET['user']); }
    else if (isset($_POST['user'])) { $user = $astDB->escape($_POST['user']); }


if (isset($user) && $user !== '') {
	$astDB->where('sender', $user);
	$astDB->where('recipient', $user);
    $rslt = $astDB->get('go_chat_history');
    
    $APIResult = array( "result" => "success", "data" => $rslt );
} else {
	$APIResult = array( "result" => "error", "message" => "Field 'user' should not be empty." );
}
?>