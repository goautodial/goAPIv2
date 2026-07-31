<?php
/**
 * @file        goDBkamailio.php
 * @brief       Configurations to connect to kamailio DB
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James Flores Milo  <jericojames@goautodial.com>
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
include_once (__DIR__ . "/includes/MySQLiDB.php");

if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/astguiclient.conf")) {
	$conf_path = "{$_SERVER['DOCUMENT_ROOT']}/astguiclient.conf";
} else {
	die ("ERROR: 'astguiclient.conf' file not found.");
}

if ( file_exists($conf_path) )
    {
    $DBCagc = file($conf_path);
    foreach ($DBCagc as $DBCline)
        {
        $DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/","",$DBCline);
        if (preg_match("/^PATHlogs/", (string) $DBCline))
                {$PATHlogs = $DBCline;   $PATHlogs = preg_replace("/.*=/","",(string) $PATHlogs);}
        if (preg_match("/^PATHweb/", (string) $DBCline))
                {$WeBServeRRooT = $DBCline;   $WeBServeRRooT = preg_replace("/.*=/","",(string) $WeBServeRRooT);}
        if (preg_match("/^VARserver_ip/", (string) $DBCline))
                {$WEBserver_ip = $DBCline;   $WEBserver_ip = preg_replace("/.*=/","",(string) $WEBserver_ip);}
                
        if (preg_match("/^VARDBgokam_server/", (string) $DBCline))
                {$VARDBgokam_server = $DBCline;   $VARDBgokam_server = preg_replace("/.*=/","",(string) $VARDBgokam_server);}
        if (preg_match("/^VARDBgokam_database/", (string) $DBCline))
                {$VARDBgokam_database = $DBCline;   $VARDBgokam_database = preg_replace("/.*=/","",(string) $VARDBgokam_database);}
        if (preg_match("/^VARDBgokam_user/", (string) $DBCline))
                {$VARDBgokam_user = $DBCline;   $VARDBgokam_user = preg_replace("/.*=/","",(string) $VARDBgokam_user);}
        if (preg_match("/^VARDBgokam_pass/", (string) $DBCline))
                {$VARDBgokam_pass = $DBCline;   $VARDBgokam_pass = preg_replace("/.*=/","",(string) $VARDBgokam_pass);}
        if (preg_match("/^VARDBgokam_port/", (string) $DBCline))
                {$VARDBgokam_port = $DBCline;   $VARDBgokam_port = preg_replace("/.*=/","",(string) $VARDBgokam_port);}
        }
    }

$kamDB = new MySQLiDB($VARDBgokam_server, $VARDBgokam_user, $VARDBgokam_pass, $VARDBgokam_database);

if (!$kamDB)
    {
    echo "Error: Unable to connect to MySQL goautodial." . PHP_EOL;
    echo "Debugging Error: " . $kamDB->getLastError() . PHP_EOL;
    exit;
    }
?>
