<?php
   ####################################################
   #### Name: goEditLead.php                       ####
   #### Description: API to edit specific lead     ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
   #### Written by: Alexander Abenoja _m/          ####
   #### License: AGPLv2                            ####
   ####################################################

    include_once ("../goFunctions.php");

    ### POST or GET Variables
         $menu_name = $_REQUEST['menu_name'];
         $menu_prompt = $_REQUEST['menu_prompt'];
         $menu_timeout = $_REQUEST['menu_timeout'];
         $menu_timeout_prompt = $_REQUEST['menu_timeout_prompt'];
         $menu_invalid_prompt = $_REQUEST['menu_invalid_prompt'];
         $menu_repeat = $_REQUEST['menu_repeat'];
         $menu_time_check = $_REQUEST['menu_time_check'];
         $call_time_id = $_REQUEST['call_time_id'];
         $track_in_vdac = $_REQUEST['track_in_vdac'];
         $tracking_group = $_REQUEST['tracking_group'];
         $custom_dialplan_entry = $_REQUEST['custom_dialplan_entry'];
         $menu_id = $_REQUEST['menu_id'];

        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
        $values = $_REQUEST['items'];

?>
