<?php
 /**
 * @file 		goAPI.php
 * @brief 		API for Carriers
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Jim H. Abenoja  <alex@goautodial.com>
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
    $carrier_id = $astDB->escape($_REQUEST['carrier_id']);
    
    //$queryCheck = "SELECT carrier_id FROM vicidial_server_carriers WHERE carrier_id ='$carrier_id';";
    $astDB->where('carrier_id', $carrier_id);
    $rsltv = $astDB->get('vicidial_server_carriers');
    $countCheck = $astDB->getRowCount();
    
    if($countCheck > 0) {
        $apiresults = array("result" => "Error: Carrier already exist.");
    } else {
        $apiresults = array("result" => "success");
    }
?>