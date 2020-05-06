<?php
 /**
 * @file 		index.php
 * @brief 		Index file - for testing codes
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad <chris@goautodial.com>
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
 
if ($_GET['dev'] == 'test') {
    //$lead_id = "12313";
    //$lead_id = preg_replace("/[^0-9]/","",$lead_id);
    //var_dump($lead_id);
    $test1 = strtotime("2016-04-12 13:38:54");
    $test2 = strtotime("2016-04-12 13:39:03");
    $test3 = $test2 - $test1;
    var_dump($test1, $test2, $test3);
}
?>