<?php
    ####################################################
    #### Name: getViewUsers.php                     ####
    #### Type: API to get all users                 ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Waren Ipac Briones             ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include "goFunctions.php";
    
    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = go_getall_allowed_users($groupId);
        $stringv .= "'j'";
        $ul = "AND user_level  AND (user  OR full_name ) AND user_group = '$groupId' AND user_group != 'ADMIN'";
    }

    $query ="SELECT user,full_name,user_level,active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ul ORDER BY user ASC";

   $rsltv = mysqli_query($link,$query);
   $array = array();
   while($fresults = mysqli_fetch_array($rsltv, MYSQL_ASSOC)){
        $array[] = $fresults['user'];
        $array[] = $fresults['full_name'];
        $array[] = $fresults['user_level']; 
        $array[] = $fresults['active'];
        $apiresults = array_merge( array( "result" => "success" ), $array );
}
?>
