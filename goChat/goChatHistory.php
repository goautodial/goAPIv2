<?php
####################################################
#### Name: goChatHistory.php                    ####
#### Type: API for Chat History                 ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

if (isset($_GET['goRecipient'])) { $recipient = $astDB->escape($_GET['goRecipient']); }
    else if (isset($_POST['goRecipient'])) { $recipient = $astDB->escape($_POST['goRecipient']); }


if (isset($recipient) && $recipient !== '') {
	$astDB->where('sender', $recipient);
	$astDB->where('recipient', $recipient);
    $rslt = $astDB->get('go_chat_history');
    
    $APIResult = array( "result" => "success", "data" => $rslt );
}
?>