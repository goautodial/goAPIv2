<?php
    #######################################################
    #### Name: getLeadFilterInfo.php 	               ####
    #### Description: API to get specific lead filter       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    $voicemail_id = mysqli_real_escape_string($link, $_REQUEST['voicemail_id']);
   
if($voicemail_id == null) {
                $apiresults = array("result" => "Error: Set a value for Voicemail ID.");
} else {
                $groupId = go_get_groupid($goUser);

            if (!checkIfTenant($groupId)) {
                        $ul = "";
            } else {
                        $ul = "AND user_group='$groupId'";
               //    $addedSQL = "AND user_group='$groupId'";
                	$addedSQL = "";
			}
 
			$query = "SELECT voicemail_id,pass,fullname,email,active,messages,old_messages,delete_vm_after_email,user_group FROM vicidial_voicemail WHERE voicemail_id='$voicemail_id' $ul ORDER BY voicemail_id LIMIT 1;";
			$rsltv = mysqli_query($link, $query);
			$exist = mysqli_num_rows($rsltv);

			if($exist > 0){
						while($fresults = mysqli_fetch_array($rsltv)){									
						            $dataVoicemailID[] = $fresults['voicemail_id'];
						            $dataPassword[] = $fresults['pass'];
									$dataFullname[] = $fresults['fullname'];
									$dataEmail[] = $fresults['email'];
						            $dataActive[] = $fresults['active'];
						            $dataMessages[] = $fresults['messages'];
						            $dataOldMessages[] = $fresults['old_messages'];
						            $dataDeleteVMAfterEmail[] = $fresults['delete_vm_after_email'];
						            $dataUserGroup[] = $fresults['user_group'];
						        $apiresults = array("result" => "success", "voicemail_id" => $dataVoicemailID, "password"=> $dataPassword,"fullname" => $dataFullname, "email" => $dataEmail, "active" => $dataActive, "messages" => $dataMessages, "old_messages" => $dataOldMessages, "delete_vm_after_email" => $dataDeleteVMAfterEmail, "user_group" => $dataUserGroup);
						
						}
	        } else {

                $apiresults = array("result" => "Error: Lead Filter does not exist.");

        	}
}
?>
