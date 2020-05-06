<?php
 /**
 * @file 		goAjaxManagerChat.php
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


if (isset($_GET['old'])) { $old = $goDB->escape($_GET['old']); }
    else if (isset($_POST['old'])) { $old = $goDB->escape($_POST['old']); }
if (isset($_GET['recipients'])) { $recipients = $_GET['recipients']; }
    else if (isset($_POST['recipients'])) { $recipients = $_POST['recipients']; }
if (isset($_GET['type'])) { $type = $goDB->escape($_GET['type']); }
    else if (isset($_POST['type'])) { $type = $goDB->escape($_POST['type']); }
if (isset($_GET['user'])) { $user = $goDB->escape($_GET['user']); }
    else if (isset($_POST['user'])) { $user = $goDB->escape($_POST['user']); }
if (isset($_GET['text'])) { $post = $_GET['text']; }
    else if (isset($_POST['text'])) { $post = $_POST['text']; }

// Adds new message
if (isset($type) && $type == 'sendNewMessage') {
    $date = date("Y-m-d h:i:s");

	$astDB->where('user', $user);
	$rslt = $astDB->getOne('vicidial_users');
    $userId = $rslt['user_id'];
    $fullName = $rslt['full_name'];
	$userGroup = $rslt['user_group'];

    $session = uniqid();
    foreach($recipients as $to) {
        //$stmt = "INSERT INTO manager_chat SET `date`='$date',`from`='$userId',`to`='$manager', `text`='".$goDB->escape($post)."',`seen`=0, `session`='$session';";
		$insertData = array(
			'date' => $date,
			'from' => $userId,
			'to' => $goDB->escape($to),
			'text' => $goDB->escape($post),
			'seen' => 0,
			'session' => $session
		);
        $goDB->insert('manager_chat', $insertData);
		$errorLog = $goDB->getLastError();
    }

	$thisClass = "agent-message";
	if (preg_match("/^(MANAGERS|LOCATION_MANAGERS|ADMIN)$/", $userGroup)) {
		$thisClass = "manager-message";
	}
	$APIResult = array( "result" => "success", "code" => 200, "type" => $type, "data" => "<div class='$thisClass col-md-12'><b>Me ($fullName):</b> <div class='{$thisClass}-text'>$post</div></div>", "error" => $errorLog );
}


// Gets messages on page load
if (isset($type) && $type == 'getMessages') {
	$astDB->where('user', $user);
	$rslt = $astDB->getOne('vicidial_users');
    $userId = $rslt['user_id'];

    $where = " (`from`='$userId' OR `to`='$userId') GROUP BY `session`";
    if($recipients !== "") {
        $where = "";
		if (count($recipients) > 0) {
			foreach ($recipients as $toId) {
				$where .= "((`from`='$userId' AND `to`='$toId') OR (`from`='$toId' AND `to`='$userId')) OR ";
			}
		}
        $where = trim($where, " OR ");
    }

    $query = "SELECT `from`, `date`, `text`, `fullname` FROM manager_chat INNER JOIN users ON manager_chat.`from`=users.`userid` WHERE $where ORDER BY manager_chat.`date` DESC LIMIT 15 OFFSET 0";
	$result = $goDB->rawQuery($query);

    $query = "UPDATE manager_chat SET `seen`=1 WHERE (`from`='$userId' OR `to`='$userId') AND `seen`=0";
    $urslt = $goDB->rawQuery($query);

    $messages = [];
	if ($result) {
		foreach ($result as $row) {
			$messages[] = $row;
		}
	}

    $APIResult = array( "result" => "success", "code" => 200, "type" => $type, "data" => $messages );
}

// Gets new messages
if (isset($type) && $type == 'getNewMessages') {
    //$stmt = "SELECT * FROM vicidial_users where user='$agent'";
	$astDB->where('user', $user);
	$rslt = $astDB->getOne('vicidial_users');
    $userId = $rslt['user_id'];

    $query = "SELECT `from`, `date`, `text`, `fullname` FROM manager_chat INNER JOIN users ON manager_chat.`from`=users.`userid` WHERE `to`='$userId' AND `seen`=0 ORDER BY manager_chat.`date` DESC";
    $result = $goDB->rawQuery($query);

    $messages = [];
	if ($result) {
		foreach ($result as $row) {
			$messages[] = $row;
		}
	}

    //$query = "UPDATE manager_chat SET `seen`=1 WHERE `to`='$userId' AND `seen`=0";
	$goDB->where('`to`', $userId);
	$goDB->where('`seen`', 0);
    $goDB->update('manager_chat', array('seen' => 1));
	$errorLog = $goDB->getLastError();
	
    $APIResult = array( "result" => "success", "code" => 200, "type" => $type, "data" => $messages, "error" => $errorLog );
}

// Gets old messages
if(isset($type) && $type == 'getOldMessages') {
    //$stmt = "SELECT * FROM vicidial_users where user='$agent'";
	$astDB->where('user', $user);
	$rslt = $astDB->getOne('vicidial_users');
    $userId = $rslt['user_id'];

    $limit = 15;
    $offset = 15 + $old;

    $where = " (`from`='$userId' OR `to`='$userId') GROUP BY `session`";
    if(!is_null($recipients) && $recipients !== "") {
        $where = "";
        foreach ($recipients as $toId) {
            $where .= "((`from`='$userId' AND `to`='$toId') OR (`from`='$toId' AND `to`='$userId')) OR ";
        }
        $where = trim($where, " OR ");
    }

    $query = "SELECT `from`, `date`, `text`, `fullname` FROM manager_chat
				INNER JOIN users ON manager_chat.`from`=users.`userid`
				WHERE $where ORDER BY manager_chat.`date` DESC LIMIT $limit OFFSET $offset";
	$result = $goDB->rawQuery($query);

    $messages = [];
	if ($result) {
		foreach ($result as $row) {
			$messages[] = $row;
		}
	}

    $APIResult = array( "result" => "success", "code" => 200, "type" => $type, "data" => $messages );
}

// Gets active managers
if(isset($type) && $type == 'getActiveManagers') {
    $managers = [];
    $stmt = "SELECT * FROM users WHERE (user_group='MANAGERS' OR user_group='LOCATION_MANAGERS') AND last_seen_date>'" . date('Y-m-d H:i:s', strtotime('-6 seconds')) . "'";
	$result = $goDB->rawQuery($stmt);
	
	if ($result) {
		foreach ($result as $row) {
			$managers[] = $row['userid'];
		}
	}
	
    $APIResult = array( "result" => "success", "code" => 200, "type" => $type, "data" => $managers );
}

// Update Last Seen
if (isset($type) && $type == 'updateSeen') {
	//UPDATE vicidial_users SET last_seen_date='" . date("Y-m-d H:i:s") ."' WHERE user='$user';
	$updateData = array(
		'last_seen_date' => date("Y-m-d H:i:s")
	);
	
	$goDB->where('name', $user);
	$goDB->update('users', $updateData);
	
    $APIResult = array( "result" => "success", "code" => 200, "type" => $type, "message" => "Last seen updated." );
}
?>