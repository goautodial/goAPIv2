<?php
   ####################################################
   #### Name: goGetVoiceFilesList.php              ####
   #### Description: API to view Audiofile         ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("goFunctions.php");
 
	### POST or GET Variables
        $audiofile = $_REQUEST['audiofile'];
        if($audiofile == null) {
                $apiresults = array("result" => "Error: Set a value for Audiofile");
        } else {

        $stmt = "SELECT count(*) as countuser from vicidial_users where user='$user' and user_level > 6;";
	$rsltv = mysqli_query($link, $stmt);
        //$rslt = $this->asteriskDB->query($stmt);
	$allowed_user = mysqli_num_rows($rsltv); 
        if ($allowed_user < 1) {
                $result = 'ERROR';
                $result_reason = "sounds_list USER DOES NOT HAVE PERMISSION TO VIEW SOUNDS LIST";
                $apiresults = array("result" => "Error".$result_reason);
        } else {

                $server_name = getenv("SERVER_NAME");
                $server_port = getenv("SERVER_PORT");
                if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
                  else {$HTTPprotocol = 'http://';}
                $admDIR = "$HTTPprotocol$server_name:$server_port";

                #############################################
                ##### START SYSTEM_SETTINGS LOOKUP #####
                $stmtOne = "SELECT use_non_latin,sounds_central_control_active,sounds_web_server,sounds_web_directory FROM system_settings;";
                $rslt = mysqli_query($link, $stmtOne);
                $ss_conf_ct = mysqli_num_rows($rslt);

                if ($ss_conf_ct > 0) {

                        while($fresults = mysqli_fetch_array($rslt, MYSQLI_ASSOC)){
                                $non_latin[] = $fresults['use_non_latin'];
                                $sounds_central_control_active[] = $fresults['sounds_central_control_active'];
                                $sounds_web_server[] = $fresults['sounds_web_server'];
                                $sounds_web_directory[] = $fresults['sounds_web_directory'];
                        }


                                  }
                ##### END SETTINGS LOOKUP #####
                ###########################################

                if ($sounds_central_control_active < 1) {
                        $result = 'ERROR';
                        $result_reason = "sounds_list CENTRAL SOUND CONTROL IS NOT ACTIVE";
                        $apiresults = array("result" => "Error: ".$result_reason);
                                  } else {
                        $i=0;
                        $filename_sort=$MT;
                        #$dirpath = "$WeBServeRRooT/$sounds_web_directory";
                        $dirpath = "/var/lib/asterisk/sounds";
                        $dh = opendir($dirpath);

                       // if ($DB>0) {echo "DEBUG: sounds_list variables - $dirpath|$stage|$format\n";}
                        while (false !== ($file = readdir($dh)))
                                {
                                # Do not list subdirectories
                                if ( (!is_dir("$dirpath/$file")) and (preg_match('/\.(wav|mp3)$/', $file)) )
                                        {
                                        if ((!is_null($search) && strlen($search) > 0)) {^M
                                             if (!preg_match("/$search/", $file))^M
                                                  continue;^M
                                        }
                                        if (file_exists("$dirpath/$file"))
                                                {
                                                //      'sample_prompt','date',30;
                                                $stage = "date";

                                                $file_names[$i] = $file;
                                                $file_namesPROMPT[$i] = preg_replace("/\.wav$|\.gsm$|\.mp3$/","",$file);
                                                $file_epoch[$i] = filemtime("$dirpath/$file");
                                                $file_dates[$i] = date ("Y-m-d H:i:s", filemtime("$dirpath/$file"));
                                                $file_sizes[$i] = filesize("$dirpath/$file");
                                                $file_sizesPAD[$i] = sprintf("[%020s]\n",filesize("$dirpath/$file"));
                                                if (eregi('date',$stage)) {$file_sort[$i] = $file_epoch[$i] . "----------" . $i;}
                                                if (eregi('name',$stage)) {$file_sort[$i] = $file_names[$i] . "----------" . $i;}
                                                if (eregi('size',$stage)) {$file_sort[$i] = $file_sizesPAD[$i] . "----------" . $i;}

                                                $i++;
                                                }
                                        }

                                }
                        closedir($dh);

                        if (eregi('date',$stage)) {rsort($file_sort);}
                        if (eregi('name',$stage)) {sort($file_sort);}
                        if (eregi('size',$stage)) {rsort($file_sort);}

                        sleep(1);

                        $k=0;
                        $sf=0;
                        while($k < $i)
                                {

                                $file_split = explode('----------',$file_sort[$k]);
                                $m = $file_split[1];
                                $NOWsize = filesize("$dirpath/$file_names[$m]");
		//		if($file_names == $audiofile){
					$apiresults = array("result" => "success", "file_name" => $file_names, "file_date" => $file_dates, "file_size" => $file_sizes, "file_poch" => $file_epoch);
                  //              } else {
		//		$apiresults = array("result" => "Error: Audiofile doesn't exist.");
		//		}
			$k++;
                                }
}

                        }
                }
?>
