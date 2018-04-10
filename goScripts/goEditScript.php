<?php
   ####################################################
   #### Name: goEditScript.php                 		####
   #### Description: API to edit specific Script	####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
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
			            //do nothing
			        } else { 
			            $astDB->where('user_group', $agent->user_group);  
			        }

			        $astDB->where('script_id' , $script_id);
			        $script = $astDB->get('vicidial_scripts', null, '*');

					if($script) {
						foreach($script as $fresults){
							$datascript_id = $fresults['script_id'];
							$datascript_name = $fresults['script_name'];
							$datascript_comments = $fresults['script_comments'];
							$datascript_text = $fresults['script_text'];
							$dataactive = $fresults['active'];
							$datauser_group = $fresults['user_group'];
						}
						
						$data_update = array(
							'script_name' 		=> ($script_name == null) ? $datascript_name : $script_name,
							'script_comments' 	=> ($script_comments == null) ? $datascript_comments : $script_comments,
							'active' 			=> ($script_text == null) ? $datascript_text: $script_text,
							'script_text' 		=> ($active == null) ? $dataactive : $active,
							'user_group' 		=> ($user_group == null) ? $datauser_group : $user_group
						);
						$astDB->where('script_id', $script_id);
						$scriptUpdate = $astDB->update('vicidial_scripts', $data_update);
						$updateQuery = $astDB->getLastQuery();
					
						if($scriptUpdate){
							$apiresults = array("result" => "success");

							$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified Script ID: $script_id", $log_group, $updateQuery);
						} else {
							$apiresults = array("result" => "Error: Try updating Script Again");
						}		   
					} else {
						$apiresults = array("result" => "Error: Script doesn't exist");
					}
					
				}
		}
	}
?>
