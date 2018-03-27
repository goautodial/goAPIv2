<?php
 /**
 * @file 	goAPI.php
 * @brief 	API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Warren Ipac Briones  <warren@goautodial.com>
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

//if (file_exists("/etc/goautodial.conf")) {
//        $conf_path = "/etc/goautodial.conf";
//} elseif (file_exists("{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf")) {
//        $conf_path = "{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf";
//} else {
//        die ("ERROR: 'goautodial.conf' file not found.");
//}
// 
//    include "goFunctions.php";
    
    $groupId = go_get_groupid($goUser, $astDB);
    
    if (!checkIfTenant($groupId, $goDB)) {
        //$ul='';
    } else { 
        //$ul = "AND user_group='$groupId'";
        $astDB->where('user_group', $groupId);
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




    //$query = "select count(user) as num_seats from vicidial_users where user_level < '4' and user NOT IN ('VDAD','VDCL') $ul";
    $astDB->where('user_level', '4', '<');
    $astDB->where('user', array('VDAD', 'VDCL'), 'not in');
    $fresults = $astDB->get('vicidial_users', null, 'COUNT(user) AS num_seats');
    //$fresults = mysql_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
?>
