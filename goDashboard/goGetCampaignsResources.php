<?php
    ####################################################
    #### Name: goGetCampaignsResources.php          ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################

	$groupId = go_get_groupid($session_user, $astDB);
	
        if (checkIfTenant($groupId, $goDB)) {
            $ul = "";
        } else {
            $stringv = go_getall_allowed_campaigns($groupId, $astDB);
			if($stringv !== "'ALLCAMPAIGNS'")
				$ul = " and vl.campaign_id IN ($stringv) ";
			else
				$ul = "";
        }
    
        #$query = "CALL get_HopperLeadsWarning()";
        $query = "SELECT COUNT(vh.campaign_id) as mycnt, vl.campaign_id, vl.campaign_name,vl.local_call_time, vl.user_group FROM vicidial_hopper as vh RIGHT OUTER JOIN vicidial_campaigns as vl ON (vl.campaign_id=vh.campaign_id) RIGHT OUTER JOIN vicidial_call_times as vct ON (call_time_id=local_call_time) where vl.active='Y' $ul AND ct_default_start BETWEEN 'SELECT NOW ();' AND ct_default_stop > 'SELECT NOW ();' GROUP BY vl.campaign_id HAVING COUNT(vh.campaign_id) < '100' ORDER BY mycnt DESC , campaign_id ASC LIMIT 1000";
        $rsltv = $astDB->rawQuery($query);
        $countResult = $astDB->getRowCount();
        //echo "<pre>";
        //var_dump($rsltv);   
                    
        if($countResult > 0) {
            $data = array();
                foreach ($rsltv as $fresults){
                    array_push($data, $fresults);
                }
                $apiresults = array("result" => "success", "data" => $data, "query" => $query);
        }

?>
