<?php
/**
 * @file        goEditHotkey.php
 * @brief       API to edit hotkey details
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <noelumandap@goautodial.com>
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

  $campaign_id = $_REQUEST['campaign_id'];

  $astDB->where('campaign_id', $campaign_id);
  $astDB->orderBy('hotkey');
  $hotkeys = $astDB->get('vicidial_campaign_hotkeys', null, 'status,hotkey,status_name');
  
  foreach($hotkeys as $fresults){
	  $dataHotkey[]   = $fresults['hotkey'];
	}
  
  $apiresults = array(
    "result"        => "success",
    "hotkey"        => $dataHotkey
  );
?>