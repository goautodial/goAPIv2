<?php
   #####################################################
   #### Name: goCheckCampaign.php	                ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Alexander Jim H. Abenoja        ####
   #### License: AGPLv2                             ####
   #####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
        $campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
        $status = mysqli_real_escape_string($link, $_REQUEST['status']);
        
        // Check exisiting status
        if($status != NULL){
            $queryStatusCheck = "SELECT status FROM vicidial_campaign_statuses WHERE status = '$status' AND campaign_id = '$campaign_id';";
            $rsltvCheck1 = mysqli_query($link, $queryStatusCheck);
            $countCheckResult1 = mysqli_num_rows($rsltvCheck1);
                
                if($countCheckResult1 > 0) {
                    $apiresults = array("result" => "fail", "status" => "There are 1 or more statuses with that specific input.");
                }else{
                    $apiresults = array("result" => "success");
                }
        }
        
      
?>
