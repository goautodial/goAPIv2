<?php
 /**
 * @file        goGetScriptInfo.php
 * @brief       API for Dial Status
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
    $agent = get_settings('user', $astDB, $goUser);
    $script_id = $astDB->escape($_REQUEST["script_id"]); 

    if($script_id == null) {
            $apiresults = array("result" => "Error: Set a value for Script ID.");
    } else {
        $groupId = go_get_groupid($goUser);

        if (!checkIfTenant($groupId)) {
            //do nothing
        } else { 
            $astDB->where('user_group', $agent->user_group);  
        }
        $astDB->where('script_id', $script_id);
        $script = $astDB->getOne('vicidial_scripts', null, 'script_id, script_name, script_comments, active, user_group, script_text');
		
	    if($script){
            foreach($script as $fresults)
                $apiresults = array(
                    "result" => "success", 
                    "script_id" => $fresults['script_id'], 
                    "script_name" => $fresults['script_name'], 
                    "script_comments" => $fresults['script_comments'], 
                    "active" => $fresults['active'], 
                    "user_group" => $fresults['user_group'], 
                    "script_text" => $fresults['script_text']
                );
            }
        } else {
            $apiresults = array("result" => "Error: Script does not exist.");
    	}
    }
?>
