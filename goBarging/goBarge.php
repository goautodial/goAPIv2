<?php
 /**
 * @file 		goBarge.php
 * @brief 		API for Barging
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad <chris@goautodial.com>
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


### POST or GET Variables
$goUserID = $astDB->escape($_REQUEST['user_id']);
$goConfExten = $astDB->escape($_REQUEST['conf_exten']);
$goServerIP = $astDB->escape($_REQUEST['server_ip']);
$goPhone = $astDB->escape($_REQUEST['phone']);
$type = $astDB->escape($_REQUEST['type']);

if($goUserID == "") {  array("result" => "Error: No UserID"); }
if($goConfExten == "") {  array("result" => "Error: No Conference Room"); }
if($goServerIP == "") {  array("result" => "Error: No Server IP"); }
if($goPhone == "") {  array("result" => "Error: No Phone"); }
if($type == "") {  array("result" => "Error: No Type"); }


$StarTtime = date("U");
$NOW_TIME = date("Y-m-d H:i:s");
//$query = "SELECT count(*) AS cnt from vicidial_conferences where conf_exten='$goConfExten' and server_ip='$goServerIP'";
$astDB->where('conf_exten', $goConfExten);
$astDB->where('server_ip', $goServerIP);
$rsltv = $astDB->get('vicidial_conferences');
$session_exist = $astDB->getRowCount();

if ($session_exist > 0) {
	//$query2 = "SELECT count(*) AS cnt from phones where login='$goPhone'";
	$astDB->where('login', $goPhone);
	$rsltv2 = $astDB->get('phones');
	$phone_exist = $astDB->getRowCount();

	if ($phone_exist > 0) {
		//$query3 = "SELECT dialplan_number,server_ip,outbound_cid from phones where login='$goPhone'";
		$astDB->where('login', $goPhone);
		$fresults3 = $astDB->getOne('phones', 'dialplan_number,server_ip,outbound_cid');
	}

	$S='*';
	$D_s_ip = explode('.', $goServerIP);
	if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
	if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
	if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
	if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
	if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
	if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
	if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
	if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
	$monitor_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S";

	$GADuser = sprintf("%08s", $goUserID);
	while (strlen($GADuser) > 8) {$GADuser = substr("$GADuser", 0, -1);}
	$BMquery = "BM$StarTtime$GADuser";

	if ( (preg_match('/LISTEN/',$type)) or (strlen($type)<1) ) {$type = '0';}
	if (preg_match('/BARGE/',$type)) {$type = '';}
	if (preg_match('/HIJACK/',$type)) {$type = '';}

	### insert a new lead in the system with this phone number
	$query4 = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','".$fresults3['server_ip']."','','Originate','$BMquery','Channel: Local/$monitor_dialstring$type$goConfExten@default','Context; default','Exten: ".$fresults3['dialplan_number']."','Priority: 1','Callerid: \"VC Blind Monitor\" <".$fresults3['outbound_cid'].">','','','','','')";

	$rsltv4 = $astDB->rawQuery($query4);

	$apiresults = array("result" => "success");
}

?>
