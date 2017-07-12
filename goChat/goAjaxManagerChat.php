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


// Gets new messages
if (isset($goAction) && $goAction == 'getNewMessages') {
    $agent = $goUser;
    //$stmt = "SELECT * FROM vicidial_users where user='$agent'";
	$goDB->where('user', $agent);
	$rslt = $goDB->getOne('users');
    $userId = $rslt['user_id'];

    $query = "SELECT `from`, `date`, `text`, `fullname` FROM manager_chat
				INNER JOIN users ON manager_chat.`from`=users.`userid`
				WHERE `to`='$userId' AND `seen`=0 ORDER BY manager_chat.`date` DESC";
    $result = $goDB->rawQuery($query);

    $messages = [];
    foreach ($result as $row) {
        $messages[] = $row;
    }

    //$query = "UPDATE manager_chat SET `seen`=1 WHERE `to`='$userId' AND `seen`=0";
	$goDB->where('to', $userId);
	$goDB->where('seen', 0);
    $goDB->update('manager_chat', array('seen' => 1));
	
    echo json_encode($messages);
}

// Gets old messages
if(isset($goAction) && $goAction == 'getOldMessages') {
    $agent = $goUser;
    //$stmt = "SELECT * FROM vicidial_users where user='$agent'";
    $goDB->where('user', $agent);
	$rslt = $goDB->getOne('users');
    $userId = $rslt['user_id'];

    $limit = 15;
    $offset = 15 + $old;

    $where = " (`from`='$userId' OR `to`='$userId') GROUP BY `session`";
    if($managers !== "") {
        $where = "";
        foreach ($managers as $managerId) {
            $where .= "((`from`='$userId' AND `to`='$managerId') OR (`from`='$managerId' AND `to`='$userId')) OR ";
        }
        $where = trim($where, " OR ");
    }

    $query = "SELECT `from`, `date`, `text`, `fullname` FROM manager_chat
				INNER JOIN users ON manager_chat.`from`=users.`user_id`
				WHERE $where ORDER BY manager_chat.`date` DESC LIMIT $limit OFFSET $offset";
	$result = $goDB->rawQuery($query);

    $messages = [];
    foreach ($result as $row) {
        $messages[] = $row;
    }

    echo json_encode($messages);
}
?>