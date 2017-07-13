<?php
####################################################
#### Name: goChatSave.php                       ####
#### Type: API for Chat History                 ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

if (isset($_GET['old'])) { $old = $goDB->escape($_GET['old']); }
    else if (isset($_POST['old'])) { $old = $goDB->escape($_POST['old']); }
if (isset($_GET['managers'])) { $managers = $goDB->escape($_GET['managers']); }
    else if (isset($_POST['managers'])) { $managers = $goDB->escape($_POST['managers']); }
if (isset($_GET['type'])) { $type = $goDB->escape($_GET['type']); }
    else if (isset($_POST['type'])) { $type = $goDB->escape($_POST['type']); }
if (isset($_GET['text'])) { $post = $_GET['text']; }
    else if (isset($_POST['text'])) { $post = $_POST['text']; }

// Adds new message
if (isset($type) && $type == 'sendNewMessage') {
    $date = date("Y-m-d h:i:s");

	$astDB->where('user', $goUser);
	$rslt = $astDB->getOne('vicidial_users');
    $userId = $rslt['user_id'];
    $fullName = $rslt['full_name'];

    $session = uniqid();
    foreach($managers as $manager) {
        //$stmt = "INSERT INTO manager_chat SET `date`='$date',`from`='$userId',`to`='$manager', `text`='".$goDB->escape($post)."',`seen`=0, `session`='$session';";
		$insertData = array(
			'date' => $date,
			'from' => $userId,
			'to' => $manager,
			'text' => $goDB->escape($post),
			'seen' => 0,
			'session' => $session
		);
        $goDB->insert('manager_chat', $insertData);
    }

	$APIResult = array( "result" => "success", "code" => 200, "type" => $type, "data" => "<div class='agent-message'><b>Me ($fullName):</b> <div class='agent-message-text'>$post</div></div>" );
}


// Gets messages on page load
if (isset($type) && $type == 'getMessages') {
	$astDB->where('user', $goUser);
	$rslt = $astDB->getOne('vicidial_users');
    $userId = $rslt['user_id'];

    $where = " (`from`='$userId' OR `to`='$userId') GROUP BY `session`";
    if($managers !== "") {
        $where = "";
        foreach ($managers as $managerId) {
            $where .= "((`from`='$userId' AND `to`='$managerId') OR (`from`='$managerId' AND `to`='$userId')) OR ";
        }
        $where = trim($where, " OR ");
    }

    $query = "SELECT `from`, `date`, `text`, `fullname` FROM manager_chat INNER JOIN users ON manager_chat.`from`=users.`userid` WHERE $where ORDER BY manager_chat.`date` DESC LIMIT 15 OFFSET 0";
	$result = $goDB->rawQuery($query);

    $query = "UPDATE manager_chat SET `seen`=1 WHERE (`from`='$userId' OR `to`='$userId') AND `seen`=0";
    $urslt = $goDB->rawQuery($query);

    $messages = [];
    foreach ($result as $row) {
        $messages[] = $row;
    }

    $APIResult = array( "result" => "success", "code" => 200, "data" => $messages );
}

// Gets new messages
if (isset($type) && $type == 'getNewMessages') {
    //$stmt = "SELECT * FROM vicidial_users where user='$agent'";
	$astDB->where('user', $goUser);
	$rslt = $astDB->getOne('vicidial_users');
    $userId = $rslt['user_id'];

    $query = "SELECT `from`, `date`, `text`, `fullname` FROM manager_chat INNER JOIN users ON manager_chat.`from`=users.`userid` WHERE `to`='$userId' AND `seen`=0 ORDER BY manager_chat.`date` DESC";
    $result = $goDB->rawQuery($query);

    $messages = [];
    foreach ($result as $row) {
        $messages[] = $row;
    }

    //$query = "UPDATE manager_chat SET `seen`=1 WHERE `to`='$userId' AND `seen`=0";
	$goDB->where('to', $userId);
	$goDB->where('seen', 0);
    $goDB->update('manager_chat', array('seen' => 1));
	
    $APIResult = array( "result" => "success", "code" => 200, "type" => $type, "data" => $messages );
}

// Gets old messages
if(isset($type) && $type == 'getOldMessages') {
    //$stmt = "SELECT * FROM vicidial_users where user='$agent'";
	$astDB->where('user', $goUser);
	$rslt = $astDB->getOne('vicidial_users');
    $userId = $rslt['user_id'];

    $limit = 15;
    $offset = 15 + $old;

    $where = " (`from`='$userId' OR `to`='$userId') GROUP BY `session`";
    if(!is_null($managers) && $managers !== "") {
        $where = "";
        foreach ($managers as $managerId) {
            $where .= "((`from`='$userId' AND `to`='$managerId') OR (`from`='$managerId' AND `to`='$userId')) OR ";
        }
        $where = trim($where, " OR ");
    }

    $query = "SELECT `from`, `date`, `text`, `fullname` FROM manager_chat
				INNER JOIN users ON manager_chat.`from`=users.`userid`
				WHERE $where ORDER BY manager_chat.`date` DESC LIMIT $limit OFFSET $offset";
	$result = $goDB->rawQuery($query);

    $messages = [];
    foreach ($result as $row) {
        $messages[] = $row;
    }

    $APIResult = array( "result" => "success", "code" => 200, "type" => $type, "data" => $messages );
}

// Gets active managers
if(isset($type) && $type == 'getActiveManagers') {
    $managers = [];
    $stmt = "SELECT * FROM users WHERE (user_group='MANAGERS' OR user_group='LOCATION_MANAGERS') AND last_seen_date>'" . date('Y-m-d H:i:s', strtotime('-6 seconds')) . "'";
	$result = $goDB->rawQuery($stmt);
    foreach ($result as $row) {
        $managers[] = $row['userid'];
    }
	
    $APIResult = array( "result" => "success", "code" => 200, "type" => $type, "data" => $messages );
}
?>