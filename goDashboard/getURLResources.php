<?php
    ####################################################
    #### Name: getAllPhones.php                     ####
    #### Type: API to get all phones                ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Waren Ipac Briones             ####
    #### License: AGPLv2                            ####
    ####################################################

if (file_exists("/etc/goautodial.conf")) {
        $conf_path = "/etc/goautodial.conf";
} elseif (file_exists("{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf")) {
        $conf_path = "{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf";
} else {
        die ("ERROR: 'goautodial.conf' file not found.");
}
 
    include "goFunctions.php";
    
    $groupId = go_get_groupid();
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $ul = "AND user_group='$groupId'";
    }




//$settings_path = "".$_SERVER['DOCUMENT_ROOT']."/goautodial.conf";
//include($settings_path);
//$data['url_resources'] = "https://{$_SERVER['HTTP_HOST']}/agent/agent.php";
//$data['Y'] = "kam01hv.goautodial.com";
//$data['N'] = $_SERVER['SERVER_ADDR'];





/*
                $VARKAMAILIO = $this->config->item('VARKAMAILIO');
                $VARSERVTYPE = $this->config->item('VARSERVTYPE');
                $data['VARKAMAILIO'] = $VARKAMAILIO;
                $data['VARSERVTYPE'] = $VARSERVTYPE;

*/




   $query = "select count(user) as num_seats from vicidial_users where user_level < '4' and user NOT IN ('VDAD','VDCL') $ul";

    $rsltv = mysqli_query($link,$query);
    $fresults = mysql_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
