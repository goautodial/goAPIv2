<?php
/**
 * @file        goEditTemplate.php
 * @brief       API Template to modify
 * @copyright   Copyright (C) 2020 GOautodial Inc.
 * @author      Thom Bernarth Patacsil
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
  
  $id = $astDB->escape($_REQUEST['id']);
  $is_typing = $astDB->escape($_REQUEST['is_typing']);

	if (empty($goUser) || is_null($goUser)) {
		$apiresults                                                                     = array(
			"result"                                                                                => "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults                                                                     = array(
			"result"                                                                                => "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults                                                                     = array(
			"result"                                                                                => "Error: Session User Not Defined."
		);
	} elseif (empty($id) || is_null($id)) {
		$apiresults                                                                     = array(
			"result"                                                                                => "Error: ID not Defined."
		);
	} else {
		$data = array(
			"is_typing"		=> $is_typing
		);

		$astDB->where('id', $id);
		$update = $astDB->update('go_agent_chat_login_details', $data);

		if($update) {
			// $log_id = log_action( $goDB, 'MODIFY', $log_user, $log_ip, "Updated Request: $request for Id: $id", $log_group, $astDB->getLastQuery());

			$apiresults = array(
				"result" => "success"
			);
		} else {
			$apiresults = array(
				"result" => "error",
				"error" => $astDB->getLastError()
			);
		}
	}
	return $apiresults;

	
?>

