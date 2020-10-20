<?php
/**
 * @file        goGetUnreadMessages.php
 * @brief       API to get the number of Unread messages
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

  $sender_userid = $goDB->escape($_REQUEST['userid']);
  $receiver_userid = $goDB->escape($_REQUEST['to_user_id']);

  $cols = array(
    'count(sender_userid) as count'
  );
  
  $result = $goDB->where("sender_userid", $sender_userid)
    ->where("reciever_userid", $receiver_userid)
    ->where("status", 1)
    ->getOne('go_agent_chat', $cols);

  $count = $result['count'];

  $apiresults = array(
    "result"            => "success",
    "countdata"            => $count
  );
?>

