<?php
   #####################################################
   #### Name: goCheckCampaign.php	                ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Alexander Jim H. Abenoja        ####
   #### License: AGPLv2                             ####
   #####################################################
    ### POST or GET Variables
    $campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
    
    // Check exisiting status
    if(!empty($_REQUEST['status'])){
        $status = mysqli_real_escape_string($link, $_REQUEST['status']);
            
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

        if($rsltvCheck1 > 0) {
            $apiresults = array("result" => "fail", "status" => "Campaign already exist.");
        }else{
            $apiresults = array("result" => "success");
        }
    }
?>
