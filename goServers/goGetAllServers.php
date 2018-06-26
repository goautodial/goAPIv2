<?php
/**
 * @file 		goGetAllServers.php
 * @brief 		API for Servers
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author     	Demian Lizandro A. Biscocho
 * @author     	Alexander Jim Abenoja
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

	include_once ("goAPI.php");
	
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	
	if (isset($_REQUEST['limit'])) {
			$limit = $astDB->escape($_REQUEST['limit']);
	} else { $limit = 50; }
    
	if (checkIfTenant($log_group, $goDB)) {
        //$astDB->where('user_group', $log_group);
    }

   	$cols = array("server_id", "server_description", "server_ip", "active", "asterisk_version", "max_vicidial_trunks", "local_gmt");;
	$astDB->orderBy('server_ip', 'desc');
   	$rsltv = $astDB->get('servers', $limit, $cols);
   	
	foreach ($rsltv as $fresults){
		$dataID[] = $fresults['server_id'];
		$dataDesc[] = $fresults['server_description'];
		$dataServerIP[] = $fresults['server_ip'];
		$dataActive[] = $fresults['active'];
		$dataAsterisk[] = $fresults['asterisk_version'];
		$dataTrunks[] = $fresults['max_vicidial_trunks'];
		$dataGMT[] = $fresults['local_gmt'];
	}
	
	$apiresults = array("result" => "success", "server_id" => $dataID, "server_description" => $dataDesc, "server_ip" => $dataServerIP, "active" => $dataActive, "asterisk_version" => $dataAsterisk, "max_vicidial_trunks" => $dataTrunks, "local_gmt" => $dataGMT);

?>
