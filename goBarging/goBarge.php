<?php
    #######################################################
    #### Name: goBarge.php                                 ####
    #### Description: API to get all Users Online      ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jerico James F. Milo              ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");

    ### POST or GET Variables
    $goUserID = $_REQUEST['user_id'];
    $goConfExten = $_REQUEST['conf_exten'];
    $goServerIP = $_REQUEST['server_ip'];
    $goPhone = $_REQUEST['phone'];
    $type = $_REQUEST['type'];

    if($goUserID == "") {  array("result" => "Error: No UserID"); }
    if($goConfExten == "") {  array("result" => "Error: No Conference Room"); }
    if($goServerIP == "") {  array("result" => "Error: No Server IP"); }
    if($goPhone == "") {  array("result" => "Error: No Phone"); }
    if($type == "") {  array("result" => "Error: No Type"); }


            $StarTtime = date("U");
            $NOW_TIME = date("Y-m-d H:i:s");
            $query = "SELECT count(*) AS cnt from vicidial_conferences where conf_exten='$goConfExten' and server_ip='$goServerIP'";
    	    $rsltv = mysqli_query($link, $query);
	    $session_exist = mysqli_num_rows($rsltv);

            if ($session_exist > 0)
            {
                $query2 = "SELECT count(*) AS cnt from phones where login='$goPhone'";
    	    	$rsltv2 = mysqli_query($link, $query2);
	   	$fresults = mysqli_fetch_array($rsltv2, MYSQLI_ASSOC);
                $phone_exist = $fresults['cnt'];

                if ($phone_exist > 0)
                {
                    	$query3 = "SELECT dialplan_number,server_ip,outbound_cid from phones where login='$goPhone'";
    	    		$rsltv3 = mysqli_query($link, $query3);
	   		$fresults3 = mysqli_fetch_array($rsltv3, MYSQLI_ASSOC);

                }

                	$S='*';
                	$D_s_ip = explode('.', $server_ip);
                	if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
                	if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
                	if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
                	if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
                	if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
                	if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
                	if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
                	if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
                	$monitor_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S";

                	$GADuser = sprintf("%08s", $goUserID);
                	while (strlen($GADuser) > 8) {$GADuser = substr("$GADuser", 0, -1);}
                	$BMquery = "BM$StarTtime$GADuser";
                	
			// LISTEN = MONITOR
                	#5point3
                	#if ( (ereg('LISTEN',$type)) or (strlen($type)<1) ) {$type = '0';}
                	#if (ereg('BARGE',$type)) {$type = '';}
                	#if (ereg('HIJACK',$type)) {$type = '';}

                if ( (preg_match('/LISTEN/',$type)) or (strlen($type)<1) ) {$type = '0';}
                if (preg_match('/BARGE/',$type)) {$type = '';}
                if (preg_match('/HIJACK/',$type)) {$type = '';}

                ### insert a new lead in the system with this phone number
                $query4 = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','".$fresults3['server_ip']."','','Originate','$BMquery','Channel: Local/$monitor_dialstring$type$goConfExten@default','Context; default','Exten: ".$fresults3['dialplan_number']."','Priority: 1','Callerid: \"VC Blind Monitor\" <".$fresults3['outbound_cid'].">','','','','','')";
		
    	    	$rsltv4 = mysqli_query($link, $query4);

		$apiresults = array("result" => "success");

	}

?>
