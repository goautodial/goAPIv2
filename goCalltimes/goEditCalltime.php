<?php
/**
 * @file        goEditCalltime.php
 * @brief       API to edit Call Time Details
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim H. Abenoja <alex@goautodial.com>
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
**/

    include_once("../goFunctions.php");
 
    // POST or GET Variables
        $call_time_id = $_REQUEST['call_time_id'];
        $call_time_name = $_REQUEST['call_time_name'];
        $call_time_comments = $_REQUEST['call_time_comments'];
        $ct_default_start = $_REQUEST['ct_default_start'];
        $ct_default_stop = $_REQUEST['ct_default_stop'];
        $ct_sunday_start = $_REQUEST['ct_sunday_start'];
        $ct_sunday_stop = $_REQUEST['ct_sunday_stop'];
        $ct_monday_start= $_REQUEST['ct_monday_start'];
        $ct_monday_stop= $_REQUEST['ct_monday_stop'];
        $ct_tuesday_start = $_REQUEST['ct_tuesday_start'];
        $ct_tuesday_stop = $_REQUEST['ct_tuesday_stop'];
        $ct_wednesday_start = $_REQUEST['ct_wednesday_start'];
        $ct_wednesday_stop = $_REQUEST['ct_wednesday_stop'];
        $ct_thursday_start = $_REQUEST['ct_thursday_start'];
        $ct_thursday_stop = $_REQUEST['ct_thursday_stop'];
        $ct_friday_start = $_REQUEST['ct_friday_start'];
        $ct_friday_stop = $_REQUEST['ct_friday_stop'];
        $ct_saturday_start = $_REQUEST['ct_saturday_start'];
        $ct_saturday_stop = $_REQUEST['ct_saturday_stop'];
        $default_audio = $_REQUEST['default_audio'];
        $sunday_audio = $_REQUEST['sunday_audio'];
        $monday_audio = $_REQUEST['monday_audio'];
        $tuesday_audio = $_REQUEST['tuesday_audio'];
        $wednesday_audio = $_REQUEST['wednesday_audio'];
        $thursday_audio = $_REQUEST['thursday_audio'];
        $friday_audio = $_REQUEST['friday_audio'];
        $saturday_audio = $_REQUEST['saturday_audio'];
		
		if($_REQUEST['user_group'] == "ALL"){
			$user_group = "---ALL---";
		}else{
			$user_group = $_REQUEST['user_group'];
		}
		
		$ip_address = $_REQUEST['hostname'];

    // ERROR CHECKING 
        if(empty($call_time_id) || strlen($call_time_id) < 3) {
            $apiresults = array("result" => "Error: Set a value for Call Time ID not less than 3 characters.");
        }elseif(preg_match('/[\'^Â£$%&*()}{@#~?><>,|=_+Â-]/',$call_time_name) || empty($call_time_name)){
            $apiresults = array("result" => "Error: Special characters found in call time name and must not be empty");
        }elseif(preg_match('/[\'^Â£$%&*()}{@#~?><>,|=_+Â]/',$call_time_id)){
            $apiresults = array("result" => "Error: Special characters found in call time ID");
        }elseif(preg_match('/[\'^Â£$%&*()}{@#~?><>,|=_+Â-]/',$call_time_comments)){
            $apiresults = array("result" => "Error: Special characters found in call time comments");
        }elseif(!is_numeric($ct_default_start) && $ct_default_start != null){
            $apiresults = array("result" => "Error: ct_default_start must be a number or combination of number");
        }elseif(!is_numeric($ct_default_stop) && $ct_default_stop != null){
            $apiresults = array("result" => "Error: ct_default_stop must be a number or combination of number");
        }elseif(!is_numeric($ct_sunday_start) && $ct_sunday_start != null){
            $apiresults = array("result" => "Error: ct_sunday_start must be a number or combination of number");
        }elseif(!is_numeric($ct_sunday_stop) && $ct_sunday_stop != null){
            $apiresults = array("result" => "Error: ct_sunday_stop must be a number or combination of number");
        }elseif(!is_numeric($ct_monday_start) && $ct_monday_start != null){
            $apiresults = array("result" => "Error: ct_monday_start must be a number or combination of number");
        }elseif(!is_numeric($ct_monday_stop) && $ct_monday_stop != null){
            $apiresults = array("result" => "Error: ct_monday_stop must be a number or combination of number");
        }elseif(!is_numeric($ct_tuesday_start) && $ct_tuesday_start != null){
            $apiresults = array("result" => "Error: ct_tuesday_start must be a number or combination of number");
        }elseif(!is_numeric($ct_tuesday_stop) && $ct_tuesday_stop != null){
            $apiresults = array("result" => "Error: ct_tuesday_stop must be a number or combination of number");
        }elseif(!is_numeric($ct_wednesday_start) && $ct_wednesday_start != null){
            $apiresults = array("result" => "Error: ct_wednesday_start must be a number or combination of number");
        }elseif(!is_numeric($ct_wednesday_stop) && $ct_wednesday_stop != null){
            $apiresults = array("result" => "Error: ct_wednesday_stop must be a number or combination of number");
        }elseif(!is_numeric($ct_thursday_start) && $ct_thursday_start != null){
            $apiresults = array("result" => "Error: ct_thursday_start must be a number or combination of number");
        }elseif(!is_numeric($ct_thursday_stop) && $ct_thursday_stop != null){
            $apiresults = array("result" => "Error: ct_thursday_stop must be a number or combination of number");
        }elseif(!is_numeric($ct_friday_start) && $ct_friday_start != null){
            $apiresults = array("result" => "Error: ct_friday_start must be a number or combination of number");
        }elseif(!is_numeric($ct_friday_stop) && $ct_friday_stop != null){
            $apiresults = array("result" => "Error: ct_friday_stop must be a number or combination of number");
        }elseif(!is_numeric($ct_saturday_start) && $ct_saturday_start != null){
            $apiresults = array("result" => "Error: ct_saturday_start must be a number or combination of number");
        }elseif(!is_numeric($ct_saturday_stop) && $ct_saturday_stop != null){
            $apiresults = array("result" => "Error: ct_saturday_stop must be a number or combination of number");
        }else{
            
            $groupId = go_get_groupid($session_user, $astDB);
            
            if (checkIfTenant($groupId, $goDB)) {
                $astDB->where("user_group", $groupId);
                //$ul = "AND user_group='$groupId'";
            }

            $astDB->where("user_group", $user_group);
            $astDB->getOne("vicidial_user_groups", "user_group");
            //$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups WHERE user_group='$user_group' $ul ORDER BY user_group LIMIT 1;";
            
            $countResult = $astDB->count;
			
			if($user_group == "---ALL---"){ // temporary
				$countResult = 1;
			}
			
            if($countResult > 0) {

                $astDB->where("call_time_id", $call_time_id);
                $origData = $astDB->getOne("vicidial_call_times");
                //$queryCheck = "SELECT call_time_id from vicidial_call_times where call_time_id='$call_time_id';";
                
                if($astDB->count > 0){
                    if(is_null($call_time_name)){$call_time_name = $origData["call_time_name"];} 
                    if(is_null($call_time_comments)){$call_time_comments = $origData["call_time_comments"];} 
                    if(is_null($user_group)){$user_group = $origData["user_group"];}
                    if(is_null($ct_default_start)){$ct_default_start = $origData["ct_default_start"];}
                    if(is_null($ct_default_stop)){$ct_default_stop = $origData["ct_default_stop"];}
                    if(is_null($ct_sunday_start)){$ct_sunday_start = $origData["ct_sunday_start"];}
                    if(is_null($ct_sunday_stop)){$ct_sunday_stop = $origData["ct_sunday_stop"];}
                    if(is_null($ct_monday_start)){$ct_monday_start = $origData["ct_monday_start"];}
                    if(is_null($ct_monday_stop)){$ct_monday_stop = $origData["ct_monday_stop"];}
                    if(is_null($ct_tuesday_start)){$ct_tuesday_start = $origData["ct_tuesday_start"];}
                    if(is_null($ct_tuesday_stop)){$ct_tuesday_stop = $origData["ct_tuesday_stop"];}
                    if(is_null($ct_wednesday_start)){$ct_wednesday_start = $origData["ct_wednesday_start"];}
                    if(is_null($ct_wednesday_stop)){$ct_wednesday_stop = $origData["ct_wednesday_stop"];}
                    if(is_null($ct_thursday_start)){$ct_thursday_start = $origData["ct_thursday_start"];}
                    if(is_null($ct_thursday_stop)){$ct_thursday_stop = $origData["ct_thursday_stop"];}
                    if(is_null($ct_friday_start)){$ct_friday_start = $origData["ct_friday_start"];}
                    if(is_null($ct_friday_stop)){$ct_friday_stop = $origData["ct_friday_stop"];}
                    if(is_null($ct_saturday_start)){$ct_saturday_start = $origData["ct_saturday_start"];}
                    if(is_null($ct_saturday_stop)){$ct_saturday_stop = $origData["ct_saturday_stop"];}
                    if(is_null($default_audio)){$default_audio = $origData["default_afterhours_filename_override"];}
                    if(is_null($sunday_audio)){$sunday_audio = $origData["sunday_afterhours_filename_override"];}
                    if(is_null($monday_audio)){$monday_audio = $origData["monday_afterhours_filename_override"];}
                    if(is_null($tuesday_audio)){$tuesday_audio = $origData["tuesday_afterhours_filename_override"];}
                    if(is_null($wednesday_audio)){$wednesday_audio = $origData["wednesday_afterhours_filename_override"];}
                    if(is_null($thursday_audio)){$thursday_audio = $origData["thursday_afterhours_filename_override"];}
                    if(is_null($friday_audio)){$friday_audio = $origData["friday_afterhours_filename_override"];}
                    if(is_null($saturday_audio)){$saturday_audio = $origData["saturday_afterhours_filename_override"];}

                    $data = Array(
                                "call_time_name" => $call_time_name,
                                "call_time_comments" => $call_time_comments,
                                "user_group" => $user_group,
                                "ct_default_start" => $ct_default_start,
                                "ct_default_stop" => $ct_default_stop,
                                "ct_sunday_start" => $ct_sunday_start,
                                "ct_sunday_stop" => $ct_sunday_stop,
                                "ct_monday_start" => $ct_monday_start,
                                "ct_monday_stop" => $ct_monday_stop,
                                "ct_tuesday_start" => $ct_tuesday_start,
                                "ct_tuesday_stop" => $ct_tuesday_stop,
                                "ct_wednesday_start" => $ct_wednesday_start,
                                "ct_wednesday_stop" => $ct_wednesday_stop,
                                "ct_thursday_start" => $ct_thursday_start,
                                "ct_thursday_stop" => $ct_thursday_stop,
                                "ct_friday_start" => $ct_friday_start,
                                "ct_friday_stop" => $ct_friday_stop,
                                "ct_saturday_start" => $ct_saturday_start,
                                "ct_saturday_stop" => $ct_saturday_stop,
                                "default_afterhours_filename_override" => $default_audio,
                                "sunday_afterhours_filename_override" => $sunday_audio,
                                "monday_afterhours_filename_override" => $monday_audio,
                                "tuesday_afterhours_filename_override" => $tuesday_audio,
                                "wednesday_afterhours_filename_override" => $wednesday_audio,
                                "thursday_afterhours_filename_override" => $thursday_audio,
                                "friday_afterhours_filename_override" => $friday_audio,
                                "saturday_afterhours_filename_override" => $saturday_audio
                            );
                    $astDB->where("call_time_id", $call_time_id);
                    $updateQuery = $astDB->update("vicidial_call_times", $data);
                    /*
                    $newQuery = "
                    UPDATE vicidial_call_times
                    SET
                    call_time_name = '$call_time_name',
                    call_time_comments = '$call_time_comments',
                    user_group = '$user_group',
                    ct_default_start = '$ct_default_start',
                    ct_default_stop = '$ct_default_stop',
                    ct_sunday_start = '$ct_sunday_start',
                    ct_sunday_stop = '$ct_sunday_stop',
                    ct_monday_start = '$ct_monday_start',
                    ct_monday_stop = '$ct_monday_stop',
                    ct_tuesday_start = '$ct_tuesday_start',
                    ct_tuesday_stop = '$ct_tuesday_stop',
                    ct_wednesday_start = '$ct_wednesday_start',
                    ct_wednesday_stop = '$ct_wednesday_stop',
                    ct_thursday_start = '$ct_thursday_start',
                    ct_thursday_stop = '$ct_thursday_stop',
                    ct_friday_start = '$ct_friday_start',
                    ct_friday_stop = '$ct_friday_stop',
                    ct_saturday_start = '$ct_saturday_start',
                    ct_saturday_stop = '$ct_saturday_stop',
					default_afterhours_filename_override = '$default_audio',
					sunday_afterhours_filename_override = '$sunday_audio',
					monday_afterhours_filename_override = '$monday_audio',
					tuesday_afterhours_filename_override = '$tuesday_audio',
					wednesday_afterhours_filename_override = '$wednesday_audio',
					thursday_afterhours_filename_override = '$thursday_audio',
					friday_afterhours_filename_override = '$friday_audio',
					saturday_afterhours_filename_override = '$saturday_audio' 
                    WHERE call_time_id = '$call_time_id'";
					*/
                    
                    if(!$updateQuery){
                        $apiresults = array("result" => "Error: Edit failed, check your details");
                    } else {
                        $log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Updated Call Time ID: $call_time_id", $groupId);
                        $apiresults = array("result" => "success");
                    }
                } else {
                    $apiresults = array("result" => "Error: Edit failed, Call Time doesn't exist!");
                }
            } else {
                $apiresults = array("result" => "Error: Invalid User Group");
            }
        }

?>
