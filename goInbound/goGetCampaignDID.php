<?php
 /**
 * @file        goGetCampaignDIDs.php
 * @brief       API to get all DID under a certain campaign
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <noel@goautodial.com>
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

    include_once("goAPI.php");
    
    $campaign_id = $_REQUEST['campaign_id'];
    
    if(empty($campaign_id)){
        $apiresults = array("result" => "Error: Set a value for campaign_id.");
    }else{
        $cols = Array("did_id", "did_pattern", "did_description", "did_actived", "did_route");
        $astDB->where("campaign_id", $campaign_id);
        $query =  $astDB->get("vicidial_inbound_dids", NULL, $cols);

        //$query = "SELECT did_id,did_pattern,did_description,did_active,did_route FROM vicidial_inbound_dids WHERE campaign_id = '$campaign_id' ORDER BY did_pattern";
        
        foreach($query as $fresults){
            $dataDidID[]            = $fresults['did_id'];
            $dataDidPattern[]       =  $fresults['did_pattern'];
            $dataDidDescription[]   =  $fresults['did_description'];
            $dataActive[]           =  $fresults['did_active'];
            $dataDidRoute[]         =  $fresults['did_route'];
        }
        $apiresults = array(
                        "result" => "success",
                        "did_id" => $dataDidID,
                        "did_pattern" => $dataDidPattern,
                        "did_description" => $dataDidDescription,
                        "active" => $dataActive,
                        "did_route" => $dataDidRoute
                    );
    }

    
?>