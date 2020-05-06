<?php
 /**
 * @file 		goChatHistory.php
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


if (isset($_GET['user'])) { $user = $astDB->escape($_GET['user']); }
    else if (isset($_POST['user'])) { $user = $astDB->escape($_POST['user']); }
if (isset($_GET['goLimit'])) { $limit = $astDB->escape($_GET['goLimit']); }
    else if (isset($_POST['goLimit'])) { $limit = $astDB->escape($_POST['goLimit']); }

if (!is_numeric($limit) || $limit === '') { $limit = 50; }
if ((($user === '' || is_null($user)) && $goUser !== 'goAPI')) { $user = $goUser; }


if (isset($user) && $user !== '') {
	$astDB->where('sender', $user);
	$astDB->where('recipient', $user);
    $rslt = $astDB->get('go_chat_history', $limit);
    
    $APIResult = array( "result" => "success", "data" => $rslt );
} else {
	$APIResult = array( "result" => "error", "message" => "Field 'user' should not be empty." );
}
?>