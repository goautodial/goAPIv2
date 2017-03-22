<?php
    #######################################################
    #### Name: goGetUsersList.php 	               ####
    #### Description: API to get all User Lists        ####
    #### Version: 4.0                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2017      ####
    #### Written by: Demian Lizandro A. Biscocho       ####
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
	
	// getting all users
	$query = "
                SELECT userid, name, fullname, phone, email, avatar, user_group, role, status 
                FROM users 
                WHERE userid 
                NOT IN ('VDAD','VDCL','goAPI','goautodial') 
                AND (role != '4' AND role <= '$user_level') $ul 
                ORDER BY userid 
                ASC
                ";
                
	$rsltv = mysqli_query($linkgo, $query);
        $countResult = mysqli_num_rows($rsltv);
 		
        if($countResult > 0) {
            $data = array();
            while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                array_push($data, $fresults);
                $apiresults = array("result" => "success", "data" => $data);
            }
	} else {
		$apiresults = array("result" => "Error: No data to show. $user", "test" => go_get_groupid($user));
	}

?>
