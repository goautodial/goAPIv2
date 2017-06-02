<?php
   ####################################################
   #### Name: goAddList.php                        ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
	// Parse conf
	if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/astguiclient.conf")) {
		$conf = parse_ini_file("/var/www/html/astguiclient.conf", true);
		$path_sounds = preg_replace("/>/", "", $conf['PATHsounds']);
		$path_sounds = preg_replace("/ /", "", $path_sounds);
	}
	### POST or GET Variables
    /*$audiofiles = $_REQUEST['files']*/;
    $stage = $_REQUEST['stage'];
	$groupId = go_get_groupid($session_user);
	$log_group = $groupId;
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
	
    ### Default values 
	$audiofile_name=$_FILES["files"]['name'];
	$WeBServeRRooT = '/var/lib/asterisk/';
	$sounds_web_directory = 'sounds';
	$audiofile=$_FILES["files"];
	$audiofile_orig = $_FILES['files']['name'];
	$audiofile_dir = $_FILES['files']['tmp_name'];
	$audiofile_size = $_FILES['files']['size'];
	$server_name = getenv("SERVER_NAME");
	$web_server_ip = getenv("SERVER_ADDR");
	
	if($path_sounds === "" || !isset($path_sounds) || $path_sounds === NULL)
		$path_sounds = $WeBServeRRooT.$sounds_web_directory;
		
	//die($path_sounds);
	
    if ($stage == "upload") {
		
		//$audiofile
		$explodefile = explode(".",strtolower($audiofile_orig));
		$prefix = (checkIfTenant($groupId)) ? "go_".$groupId."_" : "go_";
		
		if (preg_match("/\.(wav|mp3)$/i",$audiofile_orig)) {
			$audiofile_dir = preg_replace("/ /",'\ ',$audiofile_dir);
			$audiofile_dir = preg_replace("/@/",'\@',$audiofile_dir);
			$audiofile_name = preg_replace("/ /",'',"$prefix".$audiofile_name);
			$audiofile_name = preg_replace("/@/",'',$audiofile_name);
			
			$audio_filesize = formatSizeUnits($audiofile_size);
			
			$encoded_audio = base64_encode($audiofile_name);
			
			$get_sounds = "SELECT * FROM sounds WHERE goFilename = '$audiofile_name' AND goDirectory = '$path_sounds';";
			$exec_get_sounds = mysqli_query($linkgo, $get_sounds);
			$count_sounds = mysqli_num_rows($exec_get_sounds);
			
			if($count_sounds <= 0){
				copy($audiofile_dir, "$path_sounds/$audiofile_name");
				chmod("$path_sounds/$audiofile_name", 0644);
				if (file_exists("$path_sounds/$audiofile_name")) {
					$query_sounds = "INSERT INTO sounds(goFilename, goDirectory, goFileDate, goFilesize, uploaded_by) VALUES('$audiofile_name', '$path_sounds', NOW(), '$audio_filesize', '$session_user');";
					$exec_sounds = mysqli_query($linkgo, $query_sounds);
					
					//$query_go_sounds = "INSERT INTO go_sounds('type', 'data') VALUES('audio/mp3/wav', '$encoded_audio')";
					//$exec_go_sounds = mysqli_query($linkgo, $query_go_sounds);
					
					// Log
					$log_id = log_action($linkgo, 'UPLOAD', $log_user, $ip_address, "Uploaded New Voice File: $audiofile_name", $log_group);
					
					$stmt = "SELECT server_id,server_ip,active,server_description FROM servers";
					$servers = mysqli_query($link, $stmt);
					while($fresults = mysqli_fetch_array($servers, MYSQLI_ASSOC)){
						$server_ip[] = $fresults['server_ip'];
						$active[] = $fresults['active'];
						$server_description[] = $fresults['server_description'];
						#if(!preg_match('/dialer/i',$server_description) && $active == "Y"){
						if($active == "Y"){
							//exec('/usr/share/goautodial/goautodialc.pl "rsync -avz -e \"ssh -p222\" /var/lib/asterisk/sounds/'.$audiofile_name.' root@'.$server_ip.':/var/lib/asterisk/sounds"');
							exec('/usr/share/goautodial/goautodialc.pl "rsync -avz -e \"ssh -p222\" '.$path_sounds.'/'.$audiofile_name.' root@'.$server_ip.':'.$path_sounds.'"');
						}
					}
					if($exec_sounds){
						$apiresults = array("result" => "success");
						$stmtUpdate = "UPDATE servers SET sounds_update='Y';";
						$rsltUpdate = mysqli_query($link, $stmtUpdate);
					} else {
						//$data['uploadfail'] = "{$this->lang->line("go_file_type_wav")}";
						$apiresults = array("result" => "error");
					}
				}else{
					//$data['uploadfail'] = "{$this->lang->line("go_file_type_wav")}";
					$apiresults = array("result" => "error");
				}
				
			}else{
				$apiresults = array("result" => "exists");
			}
                //if ($web_server_ip == "162.216.5.169") {
                //    exec('/usr/share/goautodial/goautodialc.pl "rsync -avz -e \"ssh -p222\" /var/lib/asterisk/sounds/'.$audiofile_name.' root@162.216.5.164:/var/lib/asterisk/sounds"');
                //}
        }
	}
//}

?>
