<?php
    ####################################################
    #### Name: getAgentsOnline.php                  ####
    #### Type: API to get total agents online       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jeremiah Sebastian Samatra       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once ("goFunctions.php");

###Database Time
	$queryTime = "SELECT NOW() as dbtime, UNIX_TIMESTAMP(NOW()) as u_dbtime;";
	$rsltTime =  mysqli_query($link,$queryTime);
	$tresults = mysqli_fetch_assoc($rsltTime);

###PHP Time
	$phpTime =  date('Y-m-d H:i:s');

	
    
	$query = "SELECT server_id,server_description,server_ip,active,sysload,channels_total,cpu_idle_percent,disk_usage from servers order by server_id;";
	$rsltv = mysqli_query($link,$query);
	$countResult = mysqli_num_rows($rsltv);
	//    $fresults = mysqli_fetch_assoc($rsltv);
	/*
	$disk_ary = explode('|',$fresults['disk_usage']);
        $disk_ary_ct = count($disk_ary);
        $k=0;
        while ($k < $disk_ary_ct)
        {
                $disk_ary[$k] = preg_replace("/^\d* /","",$disk_ary[$k]);
                if ($k<1) {$disk = "$disk_ary[$k]";}
                else
                {
                        if ($disk_ary[$k] > $disk) {$disk = "$disk_ary[$k]";}
                }
                $k++;
        }
	$serverip = $fresults['server_ip'];
        $queryCount = "SELECT last_update as s_time,UNIX_TIMESTAMP(last_update) as u_time from server_updater where server_ip='$serverip';";
                $rsltc = mysqli_query($link,$queryCount);
                $countResultCheck = mysqli_num_rows($rsltc);
	    	$sresults = mysqli_fetch_assoc($rsltc);

        if ($countResultCheck > 0)
        {
                $fresults['s_time'] = $sresults['s_time'];
                //$u_time = $query->row()->u_time + 5;
        } else {
                $fresults['s_time'] = "TIME SYNC";
                //$u_time = 0;
        }
	*/
	
	if($countResult > 0){
		$i = 0;
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataServerID[] = $fresults['server_id'];
                $dataServerIP[] = $fresults['server_ip'];
                $dataActive[] = $fresults['active'];
                $dataSysload[] = $fresults['sysload'];
                $dataChannels[] = $fresults['channels_total'];

                $disk_ary = explode('|', $fresults['disk_usage']);
                $disk_ary_ct = count($disk_ary);
                $k = 0;

                while($k < $disk_ary_ct){
                        $disk_ary[$k] = preg_replace("/^\d* /","",$disk_ary[$k]);
                        if($k<1) {$disk = "$disk_ary[$k]";}
                        else{
                                if($disk_ary[$k] > $disk) {$disk = "$disk_ary[$k]";}
                        }
                        $k++;
                }

                $serverip = $fresults['server_ip'];
                $queryCount = "SELECT last_update as s_time,UNIX_TIMESTAMP(last_update) as u_time from server_updater where server_ip='$serverip';";
                $rsltc = mysqli_query($link,$queryCount);
                $countResultCheck = mysqli_num_rows($rsltc);
                $sresults = mysqli_fetch_assoc($rsltc);

                if ($countResultCheck > 0){
                        $fresults['s_time'] = $sresults['s_time'];
                        //$u_time = $query->row()->u_time + 5;
                } else {
                        $fresults['s_time'] = "TIME SYNC";
                        //$u_time = 0;
                }
		
                $fresults['disk_usage'] = $disk;
                $fresults['db_time'] = $tresults["dbtime"];
                $fresults['php_time'] = $phpTime;
                $cpu = 100 - $fresults['cpu_idle_percent'];
                //$fresults['sysload'] = $results["sysload"]." - ". $cpu."%";
		
		$dataSystemtime[] = $fresults['s_time'];
                $dataDiskusage[] = $fresults['disk_usage'];
                $dataCPU[] = $cpu;

                $apiresults = array("result" => "success","server_id" => $dataServerID, "server_ip" => $dataServerIP, "active" => $dataActive, "sysload" => $dataSysload, "channel" => $dataChannels, "disk_usage" => $dataDiskusage, "cpu" => $dataCPU, "systemtime" => $dataSystemtime, "phptime" => $phpTime ,"dbtime" => $tresults["dbtime"] );
				//$i++;
		}
		//$apiresults = array("result" => "success", "servers" => $servers );
	}else{
		$apiresults = array("result" => "Error: No data to show");
	}
/*
fresults['disk_usage'] = $disk;	
	$fresults['db_time'] = $tresults["dbtime"];
	$fresults['php_time'] = $phpTime;
        $fresults['cpu'] = 100 - $fresults['cpu_idle_percent'];
        //$fresults['sysload'] = $results["sysload"]." - ". $cpu."%";
	
	    
	    $apiresults = array_merge( array( "result" => "success" ), $fresults );
	*/
?>
