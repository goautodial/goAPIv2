<?php
/**
 * @file        goGetSuggestedDIDs.php
 * @brief       API to get suggested DIDs
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <noelumandap@goautodial.com>
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
    $keyword = $_REQUEST['keyword'];

    $astDB->where('did_pattern', "$keyword%", 'like');
    $rsltv = $astDB->get('vicidial_inbound_dids', null, 'did_pattern')
    
    if($rsltv) {
        foreach($rsltv as $fresults){
            $dids[] = $fresults['did_pattern'];
        }
        
        $dataDID = "[";
        foreach($dids as $did){
            $dataDID .= '"'.$did.'",';
        }
        $dataDID = rtrim($dataDID, ",");
        $dataDID .= "]";
        
        $apiresults = array("result" => "success", "data" => $dataDID);
    } else {
        $apiresults = array("result" => "error");
    }
    
?>