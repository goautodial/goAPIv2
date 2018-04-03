<?php
 /**
 * @file 		goCheckVoicemail.php
 * @brief 		API for Voicemails
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Alexander Abenoja  <alex@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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
$voicemail_id = $astDB->escape($_REQUEST['voicemail_id']);

//$queryCheck = "SELECT voicemail_id from vicidial_voicemail where voicemail_id='".$voicemail_id."';";
$astDB->where('voicemail_id', $voicemail_id);
$sqlCheck = $astDB->get('vicidial_voicemail');
$countCheck = $astDB->getRowCount();

if($countCheck <= 0) {
    $apiresults = array("result" => "success");
} else {
    $apiresults = array("result" => "Error: Add failed, Voicemail already already exist!");
}
?>