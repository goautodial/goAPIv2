<?php

 ##########################################################
 #### Name: goEditLeadRecyclingAPI.php                 ####
 #### Description: API to edit specific Lead Recycling ####
 #### Version: 0.9                                     ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015         ####
 #### Written by: Warren Ipac Briones                  ####
 #### License: AGPLv2                                  ####
 ##########################################################

         $url = "https://gadcs.goautodial.com/goAPI/goLeadRecycling/goAPI.php"; # URL to GoAutoDial API file
         $postfields["goUser"] = "admin"; #Username goes here. (required)
         $postfields["goPass"] = "kam0teque1234"; #Password goes here. (required)
         $postfields["goAction"] = "goEdit"; #action performed by the [[API:Functions]]
         $postfields["responsetype"] = "json"; #json (required)
         $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
         $postfields["leadRecCampID"] = $_GET['leadRecCampID'];
         $postfields["status"] = $_GET['status']; #VPause Code
         $postfields["attempt_delay"] = $_GET['attempt_delay']; #pause code name
         //$postfields["attempt_maximum"] = $_GET['attempt_maximum'];
         $postfields["active"] = $_GET['active']; #FNo, YES or HALF


         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
         $data = curl_exec($ch);
         curl_close($ch);
         $output = json_decode($data);

var_dump($data);
//      print_r($data);

        if ($output->result=="success") {
           # Result was OK!
                echo "Update Success";
         } else {
           # An error occured
                echo $output->result;
        }

?>
