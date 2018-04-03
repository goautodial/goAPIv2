<?php
 /**
 * @file 		goAddVoiceFiles.php
 * @brief 		API for Voice Files
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// POST or GET Variables
/*$audiofiles = $_REQUEST['files']*/;
$stage = $astDB->escape($_REQUEST['stage']);
$groupId = go_get_groupid($session_user, $astDB);
$log_group = $groupId;
$ip_address = $astDB->escape($_REQUEST['hostname']);

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
	$prefix = (checkIfTenant($groupId, $goDB)) ? "go_".$groupId."_" : "go_";
	
	if (preg_match("/\.(wav|mp3)$/i",$audiofile_orig)) {
		$audiofile_dir = preg_replace("/ /",'\ ',$audiofile_dir);
		$audiofile_dir = preg_replace("/@/",'\@',$audiofile_dir);
		$audiofile_name = preg_replace("/ /",'',"$prefix".$audiofile_name);
		$audiofile_name = preg_replace("/@/",'',$audiofile_name);
		
		$audio_filesize = formatSizeUnits($audiofile_size);
		
		$encoded_audio = base64_encode($audiofile_name);
		
		//$get_sounds = "SELECT * FROM sounds WHERE goFilename = '$audiofile_name' AND goDirectory = '$path_sounds';";
		$goDB->where('goFIlename', $audiofile_name);
		$goDB->where('goDirectory', $path_sounds);
		$exec_get_sounds = $goDB->get('sounds');
		$count_sounds = $goDB->getRowCount();
		
		if($count_sounds <= 0){
			copy($audiofile_dir, "$path_sounds/$audiofile_name");
			chmod("$path_sounds/$audiofile_name", 0644);
			if (file_exists("$path_sounds/$audiofile_name")) {
				//$query_sounds = "INSERT INTO sounds(goFilename, goDirectory, goFileDate, goFilesize, uploaded_by) VALUES('$audiofile_name', '$path_sounds', NOW(), '$audio_filesize', '$session_user');";
				$insertData = array(
					'goFilename' => $audiofile_name,
					'goDirectory' => $path_sounds,
					'goFileDate' => 'NOW()',
					'goFilesize' => $audio_filesize,
					'uploaded_by' => $session_user
				);
				$exec_sounds = $goDB->insert('sounds', $insertData);
				
				//$query_go_sounds = "INSERT INTO go_sounds('type', 'data') VALUES('audio/mp3/wav', '$encoded_audio')";
				//$exec_go_sounds = mysqli_query($linkgo, $query_go_sounds);
				
				// Log
				$log_id = log_action($goDB, 'UPLOAD', $log_user, $ip_address, "Uploaded New Voice File: $audiofile_name", $log_group);
				
				//$stmt = "SELECT server_id,server_ip,active,server_description FROM servers";
				$servers = $astDB->get('servers');
				foreach ($servers as $fresults) {
					$server_ip = $fresults['server_ip'];
					$active = $fresults['active'];
					$server_description = $fresults['server_description'];
					#if(!preg_match('/dialer/i',$server_description) && $active == "Y"){
					if($active == "Y"){
						//exec('/usr/share/goautodial/goautodialc.pl "rsync -avz -e \"ssh -p222\" /var/lib/asterisk/sounds/'.$audiofile_name.' root@'.$server_ip.':/var/lib/asterisk/sounds"');
						exec('/usr/share/goautodial/goautodialc.pl "rsync -avz -e \"ssh -p222\" '.$path_sounds.'/'.$audiofile_name.' root@'.$server_ip.':'.$path_sounds.'"');
					}
				}
				if($exec_sounds) {
					$apiresults = array("result" => "success");
					//$stmtUpdate = "UPDATE servers SET sounds_update='Y';";
					$rsltUpdate = $astDB->update('servers', array('sounds_update' => 'Y'));
				} else {
					//$data['uploadfail'] = "{$this->lang->line("go_file_type_wav")}";
					$apiresults = array("result" => "error");
				}
			} else {
				//$data['uploadfail'] = "{$this->lang->line("go_file_type_wav")}";
				$apiresults = array("result" => "error");
			}
		} else {
			$apiresults = array("result" => "exists");
		}
		//if ($web_server_ip == "162.216.5.169") {
		//    exec('/usr/share/goautodial/goautodialc.pl "rsync -avz -e \"ssh -p222\" /var/lib/asterisk/sounds/'.$audiofile_name.' root@162.216.5.164:/var/lib/asterisk/sounds"');
		//}
	}
}
//}
?>