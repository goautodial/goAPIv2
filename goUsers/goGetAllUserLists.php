<?php
    #######################################################
    #### Name: goGetAllUserLists.php 	               ####
    #### Description: API to get all User Lists        ####
    #### Version: 4.0                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    include_once ("../licensed-conf.php");
	
	$user = $goUser;
	if (isset($_REQUEST["user"]) && strlen($_REQUEST["user"]) > 0) {
		$user = $_REQUEST["user"];
	}
	
	// get user_level
	$query_userlevel_sql = "SELECT user_level,user_group FROM vicidial_users WHERE user = '$user' LIMIT 1";
	$rsltv_userlevel = mysqli_query($link, $query_userlevel_sql);
	$fetch_user_level = mysqli_fetch_array($rsltv_userlevel);
	$user_level = $fetch_user_level["user_level"];
	$groupId = $fetch_user_level["user_group"];
    
    if (!checkIfTenant($groupId)) {
        $ul='';
        if ($groupId != 'ADMIN') {
			if ($user_level > 8) {
				$uQuery = "SELECT tenant_id FROM go_multi_tenant;";
				$uRslt = mysqli_query($linkgo, $uQuery);
				if (mysqli_num_rows($uRslt) > 0) {
					$ul = "AND user_group NOT IN (";
					$uListGroups = "";
					while($uResults = mysqli_fetch_array($uRslt, MYSQLI_ASSOC)) {
						$uListGroups = "'{$uResults['tenant_id']}',";
					}
					$ul .= rtrim($uListGroups, ',');
					$ul .= ")";
				}
			} else {
				$ul = "AND user_group='$groupId'";
			}
        }
    } else { 
        $ul = "AND user_group='$groupId'";  
    }
    if ($groupId != 'ADMIN') {
        $notAdminSQL = "AND user_group != 'ADMIN'";
    }
	
	// getting agent count
	$getLastCount = "SELECT user FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL', 'goAPI') AND user_level != '4' ORDER BY user ASC";
	$queryCount = mysqli_query($link, $getLastCount);
	$max = mysqli_num_rows($queryCount);
		
		// condition
		for($i=0; $i < $max; $i++){
			$userRow = mysqli_fetch_array($queryCount);
			if(preg_match("/^agent/i", $userRow['user'])){
				$get_last = preg_replace("/^agent/i", "", $userRow['user']);
				$last_num[] = intval($get_last);
			}
		}
		// return data
		$get_last = max($last_num);
		$agent_num = $get_last + 1;
		
	// getting phone login count
	$getLastPhoneLogin = "SELECT phone_login FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL', 'goAPI') AND user_level != '4' AND phone_login != '' $notAdminSQL ORDER BY phone_login DESC";
	$queryPhoneLoginCount = mysqli_query($link, $getLastPhoneLogin);
	$max_phonelogins = mysqli_num_rows($queryPhoneLoginCount);
	
		// condition
		if($max_phonelogins > 0){
			while($get_last_phonelogin = mysqli_fetch_array($queryPhoneLoginCount)){
				if(preg_match("/^Agent/i", $get_last_phonelogin['phone_login'])){
					$get_last_count = preg_replace("/^Agent/i", "", $get_last_phonelogin['phone_login']);
					$last_pl[] = intval($get_last_count);
				}else{
					$get_last_count = $get_last_phonelogin['phone_login'];
					$last_pl[] = intval($get_last_count);
				}
			}
			
			// return data
			$phonelogin_num = max($last_pl);
			$phonelogin_num = $phonelogin_num + 1;
			
		}else{
			// return data
			$phonelogin_num = "0000001";
		}

	// getting all users
	#	$query = "SELECT user_id, user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ul $notAdminSQL ORDER BY user ASC;";
	$query = "SELECT user_id, user, full_name, user_level, user_group, phone_login, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL','goAPI','goautodial') AND (user_level != '4' AND user_level <= '$user_level') $ul ORDER BY user_id ASC";
	$rsltv = mysqli_query($link, $query);
        $countResult = mysqli_num_rows($rsltv);
        
        $querygo = "SELECT userid, avatar FROM users ORDER BY userid ASC";
        $rsltvgo = mysqli_query($linkgo, $querygo);
        $countResultgo = mysqli_num_rows($rsltvgo);
        
        if($countResultgo > 0) {
            $datago = array();
            while($fresultsgo = mysqli_fetch_array($rsltvgo, MYSQLI_ASSOC)){
                array_push($datago, $fresultsgo);
                $dataUserIDgo[] = $fresultsgo['userid'];
                $dataAvatar[] = $fresultsgo['avatar'];
            }
        }               
		
        // condition
 		
        if($countResult > 0) {
            $data = array();
            while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                array_push($data, $fresults);
                $dataUserID[] = $fresults['user_id'];
                $dataUser[] = $fresults['user'];
                $dataFullName[] = $fresults['full_name'];
                $dataUserLevel[] = $fresults['user_level'];
                $dataUserGroup[] = $fresults['user_group'];
                $dataPhone[] = $fresults['phone_login'];
                $dataActive[]	= $fresults['active'];                
                
                //$apiresults = array("result" => "success", "data" => $data);
            }
            $apiresults = array("result" => "success", "user_id" => $dataUserID,"user_group" => $dataUserGroup, "user" => $dataUser, "full_name" => $dataFullName, "user_level" => $dataUserLevel, "phone_login" => $dataPhone, "active" => $dataActive, "last_count" => $agent_num, "last_phone_login" => $phonelogin_num, "avatar" => $dataAvatar, "useridgo" => $dataUserIDgo, "licensedSeats" => $config["licensedSeats"]);
	} else {
		$apiresults = array("result" => "Error: No data to show. $user", "test" => go_get_groupid($user));
	}

?>
