<?php
   ####################################################
   #### Name: goAddList.php                        ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("goFunctions.php");
 
	### POST or GET Variables
    /*$audiofiles = $_REQUEST['files']*/;
    $stage = $_REQUEST['stage'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);


    ### Default values 
    
    ### Check campaign_id if its null or empty
$audiofile_name=$_FILES["files"]['name'];
$WeBServeRRooT = '/var/lib/asterisk';
$sounds_web_directory = 'sounds';
$audiofile=$_FILES["files"];
$audiofile_orig = $_FILES['files']['name'];
$audiofile_dir = $_FILES['files']['tmp_name'];
$server_name = getenv("SERVER_NAME");
$web_server_ip = getenv("SERVER_ADDR");

             
//        if($stage == null) {
  //              $apiresults = array("result" => "Error: Set a value for stage.");
    //    } else {

            if ($stage == "upload") {
                //$audiofile
                $explodefile = explode(".",strtolower($audiofile_orig));
                        $groupId = go_get_groupid($goUser);
                        $prefix = (checkIfTenant($groupId)) ? "go_".$groupId."_" : "go_";
                
                if (preg_match("/\.(wav|mp3)$/i",$audiofile_orig)) {
                        
                $audiofile_dir = preg_replace("/ /",'\ ',$audiofile_dir);
                $audiofile_dir = preg_replace("/@/",'\@',$audiofile_dir);
                $audiofile_name = preg_replace("/ /",'',"$prefix".$audiofile_name);
                $audiofile_name = preg_replace("/@/",'',$audiofile_name);
                
               copy($audiofile_dir, "$WeBServeRRooT/$sounds_web_directory/$audiofile_name");
						chmod("$WeBServeRRooT/$sounds_web_directory/$audiofile_name", 0766);

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','UPLOAD','Uploaded a WAV file: $audiofile_name','');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
				$log_id = log_action($linkgo, 'UPLOAD', $log_user, $ip_address, "Uploaded New Voice File: $audiofile_name", $log_group);

                $stmt = "SELECT server_id,server_ip,active,server_description FROM servers";
                $servers = mysqli_query($link, $stmt);
                while($fresults = mysqli_fetch_array($servers, MYSQLI_ASSOC)){
                                        $server_ip[] = $fresults['server_ip'];
                                        $active[] = $fresults['active'];
                                        $server_description[] = $fresults['server_description'];
                                

                    #if(!preg_match('/dialer/i',$server_description) && $active == "Y"){
                    if($active == "Y"){
                        exec('/usr/share/goautodial/goautodialc.pl "rsync -avz -e \"ssh -p222\" /var/lib/asterisk/sounds/'.$audiofile_name.' root@'.$server_ip.':/var/lib/asterisk/sounds"');
                    }                                           

                }
                
                if ($web_server_ip == "162.216.5.169") {
                    exec('/usr/share/goautodial/goautodialc.pl "rsync -avz -e \"ssh -p222\" /var/lib/asterisk/sounds/'.$audiofile_name.' root@162.216.5.164:/var/lib/asterisk/sounds"');
                }
                
                //$this->commonhelper->auditadmin('UPLOAD',"Uploaded a WAV file: $audiofile_name");
						$apiresults = array("result" => "success");
						$stmtUpdate = "UPDATE servers SET sounds_update='Y';";
						$rsltUpdate = mysqli_query($link, $stmtUpdate);

                } else {
                        //$data['uploadfail'] = "{$this->lang->line("go_file_type_wav")}";
						$apiresults = array("result" => "Error: Upload Failed.");

                }
            }
        
//}

?>
