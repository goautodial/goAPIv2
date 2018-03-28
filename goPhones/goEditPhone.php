<?php
/**
 * @file        goEditCampaign.php
 * @brief       API to edit specific Phone
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James Milo  <james@goautodial.com>
 * @author      Alexander Jim H. Abenoja  <alex@goautodial.com>
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
    
    include_once ("../goFunctions.php");
 
    // POST or GET Variables
    $extension = $_REQUEST['extension'];
    $server_ip = $_REQUEST['server_ip'];
    $pass =    $_REQUEST['pass'];
    $protocol = strtoupper($_REQUEST['protocol']);
    $dialplan_number = $_REQUEST['dialplan_number'];
    $voicemail_id = $_REQUEST['voicemail_id'];
    $status = $_REQUEST['status'];
    $active = strtoupper($_REQUEST['active']);
    $fullname = $_REQUEST['fullname'];
    $messages = $_REQUEST['messages'];
    $old_messages = $_REQUEST['old_messages'];
    $user_group = $_REQUEST['user_group'];
    $ip_address = $_REQUEST['hostname'];

    // Default values 
    $defActive = array("Y","N");
	$defProtocol = array('SIP','Zap','IAX2','EXTERNAL');
    $defStatus = array('ACTIVE','SUSPENDED','CLOSED','PENDING','ADMIN');

    //Error Checking Next
    if(!isset($session_user) || is_null($session_user)){
        $apiresults = array("result" => "Error: Session User Not Defined.");
    }elseif($extension == null) {
        $apiresults = array("result" => "Error: Set a value for Extension.");
    } elseif(!in_array($status,$defStatus) && $status != null) {
        $apiresults = array("result" => "Error: Default value for status is ACTIVE, SUSPENDED, CLOSED, PENDING, ADMIN only.");
    } elseif(!in_array($active,$defActive) && $active != null) {
        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
    } elseif(!in_array($protocol,$defProtocol) && $protocol != null) {
        $apiresults = array("result" => "Error: Default value for protocol is SIP, Zap, IAX2, EXTERNAL.");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $server_ip)){
        $apiresults = array("result" => "Error: Special characters found in server_ip");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $pass)){
        $apiresults = array("result" => "Error: Special characters found in password");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $dialplan_number)){
        $apiresults = array("result" => "Error: Special characters found in dialplan_number");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $voicemail_id)){
        $apiresults = array("result" => "Error: Special characters found in voicemail_id");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $status)){
        $apiresults = array("result" => "Error: Special characters found in status");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $fullname)){
        $apiresults = array("result" => "Error: Special characters found in fullname");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $messages)){
        $apiresults = array("result" => "Error: Special characters found in messages");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $old_messages)){
        $apiresults = array("result" => "Error: Special characters found in old_messages");
    } elseif(preg_match('/[\'^£$%&*()}{@*~?><>,|=_+¬-]/', $user_group)){
        $apiresults = array("result" => "Error: Special characters found in user_group");
    } else {
        
        $log_user = $session_user;
        $groupId = go_get_groupid($session_user, $astDB);
        
        if (!checkIfTenant($groupId)) {
            $astDB->where("extension", $extension);
            //$ul = "WHERE extension='$extension'";
        } else {
            $astDB->where("extension", $extension);
            $astDB->where("user_group", $groupId);
            //$ul = "WHERE extension='$extension' AND user_group='$groupId'";
        }

        $astDB->orderBy('extension', 'asc');
        $fresults = $astDB->getOne("phones", "extension,protocol,pass,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group");
        //$query = "SELECT extension,protocol,pass,server_ip,dialplan_number,voicemail_id,status,active,fullname,messages,old_messages,user_group FROM phones $ul ORDER BY extension LIMIT 1;";

    	$dataextension = $fresults['extension'];
    	$dataprotocol = $fresults['protocol'];
    	$datapass = $fresults['pass'];
    	$dataserver_ip = $fresults['server_ip'];
    	$datadialplan_number = $fresults['dialplan_number'];
    	$datavoicemail_id = $fresults['voicemail_id'];
    	$datastatus = $fresults['status'];
    	$dataactive = $fresults['active'];
    	$datafullname = $fresults['fullname'];
    	$datamessages = $fresults['messages'];
    	$dataold_messages = $fresults['old_messages'];
    	$datauser_group = $fresults['user_group'];

        $countResult = $astDB->count;

        if($countResult > 0) {
    		if($dataextension != null){
                if($server_ip ==  null){$server_ip = $dataserver_ip;} if($pass == null) {$pass = $datapass;} if($protocol == null){$protocol = $dataprotocol;} if($dialplan_number == null){$dialplan_number = $datadialplan_number;} if($voicemail_id == null){$voicemail_id = $datavoicemail_id;} if($status == null){$status = $datastatus;} if($active == null){$active = $dataactive;} if($fullname == null){$fullname = $datafullname;} if($messages == null) {$messages = $datamessages;} if($old_messages == null){$old_messages = $dataold_messages;} if($user_group == null){ $user_group = $datauser_group;}
                
                $data = Array(
                            "server_ip" => $server_ip,
                            "pass" => $pass,
                            "protocol" => $protocol,
                            "dialplan_number" => $dialplan_number,
                            "voicemail_id" => $voicemail_id,
                            "status" => $status,
                            "active" => $active,
                            "fullname" => $fullname,
                            "messages" => $messages,
                            "old_messages" => $old_messages,
                            "user_group" => $user_group
                        );
                $astDB->where("extension", $extension);
                $main_update = $astDB->update("phones", $data);
                
                $query = "UPDATE phones SET server_ip='$server_ip', pass='$pass', protocol='$protocol', dialplan_number='$dialplan_number', voicemail_id='$voicemail_id', status='$status', active='$active', fullname='$fullname', messages='$messages', old_messages='$old_messages', user_group='$user_group' WHERE extension='$extension';";
    			
                $dataUser = Array("phone_pass" => $passwd);
                $astDB->where("phone_login", $extension);
                $astDB->update("vicidial_users", $dataUser);
                //$queryNew = "UPDATE vicidial_users SET phone_pass='$passwd' WHERE phone_login='$extension';";

                rebuildconfQuery($astDB, $server_ip);
    			//$queryUpdate = "UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";

                // Admin logs
                	$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified Phone: $extension", $groupId, $query);

                if($main_update){
    				$apiresults = array("result" => "success");
                }else{
                    $apiresults = array("result" => "Error: Failed to Update");
                }
    		} else {
    			$apiresults = array("result" => "Error: Failed to update");
    		}

    	} else {
            $apiresults = array("result" => "Error: Phone doesn't  exist.");
    	}
    }
?>
