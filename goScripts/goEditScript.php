<?php
   ####################################################
   #### Name: goEditScript.php                 		####
   #### Description: API to edit specific Script	####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
        $script_id = mysqli_real_escape_string($link, $_REQUEST['script_id']);
        $script_name = mysqli_real_escape_string($link, $_REQUEST['script_name']);
        $script_comments = mysqli_real_escape_string($link, $_REQUEST['script_comments']);
        $script_text = $_REQUEST['script_text'];
        $user_group = mysqli_real_escape_string($link, $_REQUEST['user_group']);
        $active = $_REQUEST['active'];
        $ip_address = $_REQUEST['hostname'];
        $goUser = $_REQUEST['goUser'];
		
        $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
        $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

    ### Default values
    $defActive = array("Y","N");

    ### ERROR CHECKING ...
        if($script_id == null) {
                $apiresults = array("result" => "Error: Please try again.");
        } else {
				if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$script_name) && $script_name != null){
						$apiresults = array("result" => "Error: Special characters found in script name");
				} else {
						### Check value compare to default values
						if(!in_array($active,$defActive) && $active != null) {
								$apiresults = array("result" => "Error: Default value for active is Y or N only.");
						} else {
				
						$groupId = go_get_groupid($goUser);
		
						if (!checkIfTenant($groupId)) {
								$ul = "";
						} else {
								$ul = "AND user_group='$groupId'";
						}
		
						$queryCheck = "SELECT * from vicidial_scripts WHERE script_id='$script_id' $ul;";
						
						$sqlCheck = mysqli_query($link, $queryCheck);
								while($fresults = mysqli_fetch_array($sqlCheck, MYSQLI_ASSOC)){
										$datascript_id = $fresults['script_id'];
										$datascript_name = $fresults['script_name'];
										$datascript_comments = $fresults['script_comments'];
										$datascript_text = $fresults['script_text'];
										$dataactive = $fresults['active'];
										$datauser_group = $fresults['user_group'];
								}
						$countVM = mysqli_num_rows($sqlCheck);
						
						if($countVM > 0) {
								if($script_id == null){ $script_id = $datascript_id; }
								if($script_name == null){ $script_name = $datascript_name;}
								if($script_comments == null){ $script_comments = $datascript_comments;}
								if($script_text == null){ $script_text = $datascript_text;}
								if($active == null){ $active =  $dataactive; }
								if($user_group == null){ $user_group =  $datauser_group; }
				
								$queryVM ="UPDATE vicidial_scripts
										SET
										script_name = '$script_name',
										script_comments = '$script_comments',
										active = '$active',
										script_text = '$script_text',
										user_group = '$user_group'
										WHERE script_id = '$script_id';";
						
								$rsltv1 = mysqli_query($link, $queryVM);
						
									if($rsltv1 == false){
										$apiresults = array("result" => "Error: Try updating Script Again");
									} else {
										$apiresults = array("result" => "success");
						
						### Admin logs
										//$SQLdate = date("Y-m-d H:i:s");
										//
										//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Voicemail box: $voicemail_id','UPDATE `vicidial_scripts` SET script_id = $script_id,  script_name = $script_name,  script_comments = $script_comments,  active = $active,  script_text =$script_text WHERE script_id = $script_id');";
										//$rsltvLog = mysqli_query($linkgo, $queryLog);
										$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified Script ID: $script_id", $log_group, $queryVM);
									}
									   
						} else {
								$apiresults = array("result" => "Error: Script doesn't exist");
						}
						
						}
				}
		}
?>
