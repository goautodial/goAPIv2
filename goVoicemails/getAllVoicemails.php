<?php
    #######################################################
    #### Name: getAllLeadFilters.php 	               ####
    #### Description: API to get all lead filter       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### Modified by: Alexander Jim H. Abenoja         ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");

/*
$voicemail_id = $_REQUEST["voicemail_id"];
$active = $_REQUEST["active"];
$pass = $_REQUEST["pass"];
$fullname = $_REQUEST["fullname"];
$messages = $_REQUEST["messages"];
$old_messages = $_REQUEST["old_messages"];
$emial = $_REQUEST["email"];
$delete_vm_after_email = $_REQUEST["delete_vm_after_email"];
$voicemail_timezone = $_REQUEST["voicemail_timezone"];
$voicemail_options = $_REQUEST["voicemail_options"];
$user_group = $_REQUEST["user_group"];
*/
### voicemail_id, active enum('N','Y'), pass, fullname, messages, old_messages, email, delete_vm_after_email enum('N','Y'), voicemail_timezone, voicemail_options, user_group, voicemail_greeting

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                $query = "SELECT voicemail_id,fullname,active,messages,old_messages,delete_vm_after_email,user_group FROM vicidial_voicemail $ul $addedSQL ORDER BY voicemail_id;";
   		$rsltv = mysqli_query($link, $query);
		$countRsltv = mysqli_num_rows($rsltv);
		
	if($countRsltv > 0){
		while($fresults = mysqli_fetch_array($rsltv)){
			$dataVoicemailID[] = $fresults['voicemail_id'];
       			$dataFullname[] = $fresults['fullname'];
       			$dataActive[] = $fresults['active'];
       			$dataMessages[] = $fresults['messages'];
       			$dataOldMessages[] = $fresults['old_messages'];
       			$dataDeleteVMAfterEmail[] = $fresults['delete_vm_after_email'];
       			$dataUserGroup[] = $fresults['user_group'];
 	  		$apiresults = array("result" => "success", "voicemail_id" => $dataVoicemailID, "fullname" => $dataFullname, "active" => $dataActive, "messages" => $dataMessages, "old_messages" => $dataOldMessages, "delete_vm_after_email" => $dataDeleteVMAfterEmail, "user_group" => $dataUserGroup);
		}
	}else{
		$apiresults = array("result" => "Empty");
	}

?>
