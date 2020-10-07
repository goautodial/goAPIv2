<?php
/**
 * @file        goGetWhatsappUserInfo.php
 * @brief       API to get the user information
 * @copyright   Copyright (C) 2020 GOautodial Inc.
 * @author      Thom Bernarth Patacsil
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

  $userid = $goDB->escape($_REQUEST['userid']);

  $cols = array(
    'u.userid',
    'avatar',
    'name',
    'fullname',
    'phone',
    'whatsapp_current_session',
    'whatsapp_online',
    'last_activity',
    'is_typing'
	);

  
  $result = $goDB->join('go_agent_chat_login_details cld', 'cld.userid = u.userid', 'left')
    ->where('u.userid', $userid)
    ->getOne('users u', null, $cols);
  
    $dataUserid = $result['userid'];
    $dataAvatar = $result['avatar'];
    $dataName = $result['name'];
    $dataFullName = $result['fullname'];
    $dataPhone = $result['phone'];
    $dataCurrentSession = $result['whatsapp_current_session'];
    $dataOnline = $result['whatsapp_online'];
    $dataLastActivity = $result['last_activity'];
    $dataIsTyping = $result['is_typing'];
    
  
  $apiresults = array(
    "result"            => "success",
    "userid"            => $dataUserid,
    "avatar"            => $dataAvatar,
    "name"              => $dataName,
    "username"          => $dataFullName,
    "phone"             => $dataPhone,
    "current_session"   => $dataCurrentSession,
    "whatsapp_online"   => $dataOnline,
    "last_activity"     => $dataLastActivity,
    "is_typing"         => $dataIsTyping
  );

?>

