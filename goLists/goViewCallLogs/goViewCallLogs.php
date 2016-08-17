<?php
   ####################################################
   #### Name: goViewAgentScript.php                ####
   #### Description: API view script               ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jerico James Milo              ####
   #### License: AGPLv2                            ####
   ####################################################

include_once ("goFunctions.php");

$theNumber = $_REQUEST['goPhoneNumber'];


$query = "SELECT channel_group, number_dialed, length_in_sec,extension FROM call_log WHERE number_dialed LIKE '%$theNumber%';";

        $rslt=mysqli_query($link, $stmt);
        if ($DB) {echo "$stmt\n";}
        $cffn_ct = mysqli_num_rows($rslt);
        $d=0;
        while ($cffn_ct > $d)
                {
                $row=mysqli_fetch_row($rslt);
                $ChannelGroup = $row[0];
                $NumberDialed = $row[0];
                $LengthInSec  = $row[0];
                $Extension =   $row[0];
		
                $d++;
                }




$apiresults = array("result" => "success", "gocampaignScript" => $goAgentScripts);


?>
