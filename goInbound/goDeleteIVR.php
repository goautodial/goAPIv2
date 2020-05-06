<?php
/**
 * @file        goDeleteIVR.php
 * @brief       API to delete specific IVR Menu
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian V. Samatra  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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

    // POST or GET Variables
    $menu_id 									= $astDB->escape($_REQUEST['menu_id']);
	
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
	} elseif (empty($menu_id) || is_null($menu_id)) {
        $apiresults 							= array(
			"result" 								=> "Error: Set a value for Menu ID."
		);
    } else {
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
		} else {
			if (strtoupper($log_group) != 'ADMIN') {
				if ($user_level > 8) {
					$astDB->where("user_group", $log_group);
				}
			}				
		}
			
		$astDB->where("menu_id", $menu_id);
		$astDB->where("menu_id", "defaultlog", "!=");
		$astDB->getOne("vicidial_call_menu");
		
		if ($astDB->count > 0) {
			$astDB->where("menu_id", $menu_id);
			$astDB->delete("vicidial_call_menu");
			
			$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted call menu ID: $menu_id", $log_group, $astDB->getLastQuery());
			
			$astDB->where("menu_id", $menu_id);
			$astDB->delete("vicidial_call_menu_options");			
			
			$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted call menu ID: $menu_id", $log_group, $astDB->getLastQuery());

			$apiresults 						= array(
				"result" 							=> "success"
			);			
		} else {
			$apiresults 						= array(
				"result" 							=> "Error: Call menu doesn't exist or insufficient rights."
			);
		}
	}
?>
