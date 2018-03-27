<?php
 /**
 * @file 		goChatSave.php
 * @brief 		API for Manager Chat
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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
    
    $rslt = $goDB->insert('go_chat_history', $insertData);
    
    $APIResult = array( "result" => "success", "chat_id" => $goDB->getInsertId() );
}
?>