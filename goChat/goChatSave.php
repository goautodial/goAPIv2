<?php
####################################################
#### Name: goChatSave.php                       ####
#### Type: API for Chat History                 ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

if (isset($_GET['goSender'])) { $sender = $astDB->escape($_GET['goSender']); }
    else if (isset($_POST['goSender'])) { $sender = $astDB->escape($_POST['goSender']); }
if (isset($_GET['goRecipient'])) { $recipient = $astDB->escape($_GET['goRecipient']); }
    else if (isset($_POST['goRecipient'])) { $recipient = $astDB->escape($_POST['goRecipient']); }
if (isset($_GET['goMessage'])) { $message = $astDB->escape($_GET['goMessage']); }
    else if (isset($_POST['goMessage'])) { $message = $astDB->escape($_POST['goMessage']); }


if ((isset($sender) && $sender !== '') && (isset($recipient) && $recipient !== '') && (isset($message) && $message !== '')) {
	$insertData = array(
		'sender' => $sender,
		'recipient' => $recipient,
		'message' => $message,
		'entry_date' => date("Y-m-d H:i:s")
	);
    
    $rslt = $astDB->insert('go_chat_history', $insertData);
    
    $APIResult = array( "result" => "success", "chat_id" => $astDB->getInsertId() );
}
?>