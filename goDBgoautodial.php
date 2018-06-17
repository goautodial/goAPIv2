<?php
/**
 * @file        goDBgoautodial.php
 * @brief       Configurations to connect to goautodial DB
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
include_once ("includes/MySQLiDB.php");

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
        if (preg_match("/^PATHlogs/", $DBCline))
                {$PATHlogs = $DBCline;   $PATHlogs = preg_replace("/.*=/","",$PATHlogs);}
        if (preg_match("/^PATHweb/", $DBCline))
                {$WeBServeRRooT = $DBCline;   $WeBServeRRooT = preg_replace("/.*=/","",$WeBServeRRooT);}
        if (preg_match("/^VARserver_ip/", $DBCline))
                {$WEBserver_ip = $DBCline;   $WEBserver_ip = preg_replace("/.*=/","",$WEBserver_ip);}                
        if (preg_match("/^VARDBgo_server/", $DBCline))
                {$VARDBgo_server = $DBCline;   $VARDBgo_server = preg_replace("/.*=/","",$VARDBgo_server);}
        if (preg_match("/^VARDBgo_database/", $DBCline))
                {$VARDBgo_database = $DBCline;   $VARDBgo_database = preg_replace("/.*=/","",$VARDBgo_database);}
        if (preg_match("/^VARDBgo_user/", $DBCline))
                {$VARDBgo_user = $DBCline;   $VARDBgo_user = preg_replace("/.*=/","",$VARDBgo_user);}
        if (preg_match("/^VARDBgo_pass/", $DBCline))
                {$VARDBgo_pass = $DBCline;   $VARDBgo_pass = preg_replace("/.*=/","",$VARDBgo_pass);}
        if (preg_match("/^VARDBgo_port/", $DBCline))
                {$VARDBgo_port = $DBCline;   $VARDBgo_port = preg_replace("/.*=/","",$VARDBgo_port);}
        }
    }

$goDB = new MySQLiDB($VARDBgo_server, $VARDBgo_user, $VARDBgo_pass, $VARDBgo_database);

if (!$goDB)
    {
    echo "Error: Unable to connect to MySQL goautodial." . PHP_EOL;
    echo "Debugging Error: " . $goDB->getLastError() . PHP_EOL;
    exit;
    }
?>
