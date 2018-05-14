<?php
 /**
 * @file    goAddPauseCode.php
 * @brief     API to add new Pause Code
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <noel@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
  ### POST or GET Variables
  $agent = get_settings('user', $astDB, $goUser);

  $camp = $_REQUEST['pauseCampID'];
  $pause_code = $_REQUEST['pause_code'];
  $pause_code_name = $_REQUEST['pause_code_name'];
  $billable = strtoupper($_REQUEST['billable']);

  $ip_address = $astDB->escape($_REQUEST['log_ip']);
  $log_user = $astDB->escape($_REQUEST['log_user']);
  $log_group = $astDB->escape($_REQUEST['log_group']);

  ### Default values 
  $defBill = array('NO','YES','HALF');

  ### ERROR CHECKING 
  if($camp == null || strlen($camp) < 3) {
    $apiresults = array("result" => "Error: Set a value for CAMP ID not less than 3 characters.");
  } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_code) || $pause_code == null) {
    $apiresults = array("result" => "Error: Special characters found in pause code and must not be empty");
  } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_code_name)) {
    $apiresults = array("result" => "Error: Special characters found in pause code name");
  } elseif(!in_array($billable,$defBill)) {
    $apiresults = array("result" => "Error: Default value for billable is No, Yes or half only.");
  } else {
    $astDB->where('campaign_id', $camp);
    $checkCampaign = $astDB->get('vicidial_campaigns', null, '*');

    if($checkCampaign){ 
      $astDB->where('campaign_id', $camp);
      $astDB->where('pause_code', $pause_code);
      $checkPC = $astDB->get('vicidial_pause_codes', null, '*');
      if($checkPC){ 
        $data_insert = array(
          'pause_code'      => $pause_code,
          'pause_code_name' => $pause_code_name,
          'campaign_id'     => $camp,
          'billable'        => $billable
        );
        $insertPauseCode = $astDB->insert('vicidial_pause_codes', $data_insert);
        $insertQuery = $astDB->getLastQuery();

        $log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Pause Code $pause_code under Campaign $camp", $log_group, $insertQuery);

        if($insertPauseCode){
          $apiresults = array("result" => "success");
        } else {
          $apiresults = array("result" => "Error: Add failed, check your details");
        }
      } else {
        $apiresults = array("result" => "Error: Add failed, Pause Code already exist!");
      }
    } else {
      $apiresults = array("result" => "Error: Add failed, Campaign ID does not exist!");
    }
  }

?>
