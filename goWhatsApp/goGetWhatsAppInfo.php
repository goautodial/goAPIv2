<?php
/**
 * @file        goGetWhatsAppInfo.php
 * @brief       API to get WhatsApp details 
 * @copyright   Copyright (c) 2020 GOautodial Inc.
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
**/

    include_once ("goAPI.php");
    
	if (empty ($goUser) || is_null ($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty ($goPass) || is_null ($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty ($log_user) || is_null ($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($_REQUEST['whats_id']) && empty($_REQUEST['whats_id'])) {
		$apiresults 									= array(
			"result" 										=> "Error: ID Not Defined."
		);
    } else {
		$messages 										= $goDB->getOne('go_whatsapp_message');
		$ack 											= $goDB->getOne('go_whatsapp_ack');

		$apiresults 									= array(
			"result" 										=> "success",
			"message" 										=> $messages, 
			"ack" 											=> $ack
		);	
    }

?>
