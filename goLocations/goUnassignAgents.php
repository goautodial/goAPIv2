<?php
 /**
 * @file 		goUnassignAgents.php
 * @brief 		API for Removing Agents from Locations
 * @copyright 	Copyright (C) GOautodial Inc.
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

if (isset($_GET['unassigned'])) { $unassigned = $_GET['unassigned']; }
    else if (isset($_POST['unassigned'])) { $unassigned = $_POST['unassigned']; }

if (isset($unassigned) && count($unassigned) > 0) {
	foreach ($unassigned as $unassign) {
		$astDB->where('id', $unassign);
		$astDB->delete('vicidial_campaign_agents');
	}
	
	$APIResult = array("result" => "success");
} else {
	$APIResult = array("result" => "error");
}
?>