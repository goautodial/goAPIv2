<?php
    include_once("../goFunctions.php");
    
    $did = $_REQUEST['did'];
    
    $query = "SELECT
                didSetting.did_id,
                didSetting.did_pattern,
                didSetting.did_route,
                didSetting.group_id,
                didSetting.menu_id,
                didSetting.user,
                didSetting.voicemail_ext,
                groupSetting.group_color
            FROM vicidial_inbound_dids as didSetting
            LEFT JOIN vicidial_inbound_groups as groupSetting
            ON didSetting.group_id = groupSetting.group_id 
            WHERE did_pattern = '$did';";
   	$rsltv = mysqli_query($link, $query);
    $countResult = mysqli_num_rows($rsltv);
    
    if($countResult > 0) {
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
            $data['did_id']         = $fresults['did_id'];
            $data['did_pattern']    = $fresults['did_pattern'];
            $data['did_route']      = $fresults['did_route'];
            $data['group_id']       = $fresults['group_id'];
            $data['menu_id']        = $fresults['menu_id'];
            $data['user']           = $fresults['user'];
            $data['voicemail_ext']  = $fresults['voicemail_ext'];
            $data['group_color']    = $fresults['group_color'];
        }
        
        $apiresults = array("result" => "success", "data" => $data);
    } else {
        $apiresults = array("result" => "error");
    }
?>