<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('goDBasterisk.php');
if (extension_loaded('mysqli')) { 
    echo 'extension mysqli is loaded'; //works 
}  
if($link->connect_error)
{
    die("$link->connect_errno: $link->connect_error");
}else{
	$groupId = $_REQUEST['group'];
        $allowed_users = "";
        $groupId = $link->real_escape_string($groupId);
        if (strtoupper($groupId) == 'ADMIN') {
                   $query = "select user as userg from vicidial_users";
        	   $res = $link->query($query);
		   while($row = $res->fetch_assoc()){
        	        $users[] = $row['userg'];
	           }
	} else {
		   $sql = "select user as userg from vicidial_users where user_group=?";
		   $users = fetch_assoc($link, $sql, "s", $groupId);
                   //$query = $link->prepare("select user as userg from vicidial_users where user_group=?");
		   //$query->bind_param("s", $groupId);
		   //$query->execute();
		   //while($query->fetch()){
		   //	$query->bind_result($users[]);
		   //}

	}
	
	var_dump($users);
	
}
function fetch_assoc($mysqli, $sql, $types = null, $parameters){ // types = string "s", parameters = array()
        $query = $mysqli->prepare($sql);
        $query->bind_param($types, $parameters);
        $query->execute();
	while($query->fetch()){
		$query->bind_result($result[]);
	}
        return $result;
    }
/*
function fetch_assoc($mysqli, $query)
    {
        $result = $mysqli->query($query);
        
	if($result){
            return $result->fetch_assoc();
        }else{
            // call the get_error function
            return $mysqli->get_error();
             //or:
            // return $this->get_error();
        }
    }
/*
function fetch_assoc_stmt($mysqli, $sql,$types = null,$params = null)
    {
        // create a prepared statement
        $stmt = mysqli->prepare($sql);

        // bind parameters for markers
        $stmt->bind_param($types, $parameter);

        // execute query 
        $stmt->execute();
/*
        while($stmt->fetch()) 
        { 
            return $parameters;  
        }

        // close statement
        //$stmt->close();
    }*/
?>
