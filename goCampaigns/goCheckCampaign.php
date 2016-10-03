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
        
        // Check exisiting status
        if(!empty($_REQUEST['status'])){
            $status = mysqli_real_escape_string($link, $_REQUEST['status']);
                
            $countCheckResult3 = 0;
            
            if($campaign_id == "ALL"){
                $queryStatusCheck2 = "SELECT status FROM vicidial_campaign_statuses WHERE status = '$status';";
                $rsltvCheck3 = mysqli_query($link, $queryStatusCheck2);
                $countCheckResult3 = mysqli_num_rows($rsltvCheck3);
            }
                
                $queryDefaultStatusCheck = "SELECT status FROM vicidial_statuses WHERE status = '$status';";
                $rsltvCheck2 = mysqli_query($link, $queryDefaultStatusCheck);
                $countCheckResult2 = mysqli_num_rows($rsltvCheck2);
                
                $queryStatusCheck = "SELECT status FROM vicidial_campaign_statuses WHERE status = '$status' AND campaign_id = '$campaign_id';";
                $rsltvCheck1 = mysqli_query($link, $queryStatusCheck);
                $countCheckResult1 = mysqli_num_rows($rsltvCheck1);
                    
            if($countCheckResult1 > 0 || $countCheckResult2 > 0 || $countCheckResult3 > 0) {
                $apiresults = array("result" => "fail", "status" => "There are 1 or more statuses with that specific input.");
            }else{
                $apiresults = array("result" => "success");
            }
        }else{
            
            $queryStatusCheck = "SELECT campaign_id FROM vicidial_campaigns WHERE campaign_id = '$campaign_id';";
            $rsltvCheck1 = mysqli_query($link, $queryStatusCheck);
            $countCheckResult1 = mysqli_num_rows($rsltvCheck1);
            if($countCheckResult1 > 0) {
                $apiresults = array("result" => "fail", "status" => "Campaign already exist.");
            }else{
                $apiresults = array("result" => "success");
            }
        }
        
      
?>
