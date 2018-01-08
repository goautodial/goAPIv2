<?php
    $did = $_REQUEST['did'];

    $astDB->where('did_pattern', $did);
    $astDB->join('vicidial_inbound_groups groupSetting', 'didSetting.group_id = groupSetting.group_id ', 'left');
    $result = $astDB->get('vicidial_inbound_dids didSetting', null, 'didSetting.did_id,didSetting.did_pattern,didSetting.did_route,didSetting.group_id,didSetting.menu_id,didSetting.user,didSetting.voicemail_ext,groupSetting.group_color');

    foreach($result as $info){
        $data['did_id']         = $info['did_id'];
        $data['did_pattern']    = $info['did_pattern'];
        $data['did_route']      = $info['did_route'];
        $data['group_id']       = $info['group_id'];
        $data['menu_id']        = $info['menu_id'];
        $data['user']           = $info['user'];
        $data['voicemail_ext']  = $info['voicemail_ext'];
        $data['group_color']    = $info['group_color'];
    }
    
    if(count($data)) {
        $apiresults = array("result" => "success", "data" => $data);
    } else {
        $apiresults = array("result" => "error");
    }
?>