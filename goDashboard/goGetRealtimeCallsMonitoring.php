<?php
    ####################################################
    #### Name: goGetRealtimeCallsMonitoring.php     ####
    #### Type: API to get total agents onCall       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jerico James Flores Milo       ####
    ####             Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    $groupId = go_get_groupid($goUser, $astDB);

    if (!checkIfTenant($groupId, $goDB)) {
        $ul = "";
    } else {
        $stringv = go_getall_allowed_campaigns($goUser, $astDB);
        $ul = " where campaign_id IN ('$stringv') ";
    }   
        
    //$query = "SELECT status,phone_number,call_type,UNIX_TIMESTAMP(call_time) as 'call_time',vac.campaign_id from vicidial_auto_calls as vac, vicidial_campaigns as vc, vicidial_inbound_groups as vig where (vac.campaign_id=vc.campaign_id OR vac.campaign_id=vig.group_id) $ul GROUP BY status,call_type,phone_number";
    $query = "SELECT status,phone_number,call_type,UNIX_TIMESTAMP(call_time) as 'call_time',vac.campaign_id from vicidial_auto_calls as vac, vicidial_campaigns as vc, vicidial_inbound_groups as vig $ul GROUP BY status,call_type,phone_number";
    $rsltv = $astDB->rawQuery($query);
    $countResult = $astDB->getRowCount();
    //echo "<pre>";
    //var_dump($rsltv);   
        
    if($countResult > 0) {
        $data = array();
        foreach ($rsltv as $fresults){       
            array_push($data, $fresults);
        }
        $apiresults = array("result" => "success", "data" => $data);
    } 
    
?>
