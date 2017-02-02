<?php
    #######################################################
    #### Name: goGetServerList.php                     ####
    #### Description: API to get all servers           ####
    #### Version: 4.0                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Alexander Jim H. Abenoja          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");

        $query = "SELECT server_id, server_description, server_ip, active, asterisk_version, max_vicidial_trunks, local_gmt FROM servers ORDER BY server_ip;";
  		$rsltv = mysqli_query($link,$query);

		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataID[] = $fresults['server_id'];
			$dataDesc[] = $fresults['server_description'];
			$dataServerIP[] = $fresults['server_ip'];
			$dataActive[] = $fresults['active'];
			$dataAsterisk[] = $fresults['asterisk_version'];
			$dataTrunks[] = $fresults['max_vicidial_trunks'];
			$dataGMT[] = $fresults['local_gmt'];
		}
		
		$apiresults = array("result" => "success", "server_id" => $dataID, "server_description" => $dataDesc, "server_ip" => $dataServerIP,
		"active" => $dataActive, "asterisk_version" => $dataAsterisk, "max_vicidial_trunks" => $dataTrunks, "local_gmt" => $dataGMT);

?>
