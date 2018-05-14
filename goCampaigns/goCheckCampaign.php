<?php
/**
 * @file    goCheckCampaign.php
 * @brief     API to check if campaign already exists
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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
   #####################################################
   #### Name: goCheckCampaign.php	                ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Alexander Jim H. Abenoja        ####
   #### License: AGPLv2                             ####
   #####################################################
    ### POST or GET Variables
    $campaign_id = $astDB->escape($_REQUEST['campaign_id']);
    
    // Check exisiting status
    if(!empty($_REQUEST['status'])){
        $status = $astDB->escape($_REQUEST['status']);
            
        $rsltvCheck3 = 0;
        
        if($campaign_id == "ALL"){
            $astDB->where('status', $status);
            $rsltvCheck3 = $astDB->get('vicidial_campaign_statuses', null, 'status');
        }

            $astDB->where('status', $status);
            $rsltvCheck2 = $astDB->get('vicidial_statuses', null, 'status');

            $astDB->where('status', $status);
            $astDB->where('campaign_id', $campaign_id);
            $rsltvCheck1 = $astDB->get('vicidial_campaign_statuses', null, 'status');
                
        if($rsltvCheck1 || $rsltvCheck2 || $rsltvCheck3) {
            $apiresults = array("result" => "fail", "status" => "There are 1 or more statuses with that specific input.");
        }else{
            $apiresults = array("result" => "success");
        }
    }else{
        $astDB->where('campaign_id', $campaign_id);
        $rsltvCheck1 = $astDB->get('vicidial_campaigns', null, 'campaign_id');

        if($rsltvCheck1) {
            $apiresults = array("result" => "fail", "status" => "Campaign already exist.");
        }else{
            $apiresults = array("result" => "success");
        }
    }
?>
