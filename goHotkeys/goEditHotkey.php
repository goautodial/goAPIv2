<?php
   ####################################################
   #### Name: goEditHotkey.php                      ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
   #### Written by: Noel Umandap                   ####
   #### License: AGPLv2                            ####
   ####################################################
  $campaign_id = $_REQUEST['campaign_id'];

  $astDB->where('campaign_id', $campaign_id);
  $astDB->orderBy('hotkey');
  $hotkeys = $astDB->get('vicidial_campaign_hotkeys', null, 'status,hotkey,status_name');
  
  foreach($hotkeys as $fresults){
	  $dataHotkey[]   = $fresults['hotkey'];
	}
  
  $apiresults = array(
    "result"        => "success",
    "hotkey"        => $dataHotkey
  );
?>