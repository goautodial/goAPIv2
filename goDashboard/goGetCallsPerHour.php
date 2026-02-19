<?php
 /**
 * @file 		goGetCallsPerHour.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (c) 2026 GOautodial Inc.
 * @author      Demian Lizandro Biscocho
 * @author     	Jeremiah Sebastian Samatra
 * @author     	Chris Lomuntad
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

include_once ("goAPI.php");

$allowed_campaigns									= allowed_campaigns($log_group, $goDB, $astDB);
$ul                                                 = "";
$uli                                                = "";

// ERROR CHECKING
if (empty($goUser) || is_null($goUser)) {
    $apiresults 									= array(
        "result" 										=> "Error: goAPI User Not Defined."
    );
} elseif (empty($goPass) || is_null($goPass)) {
    $apiresults 									= array(
        "result" 										=> "Error: goAPI Password Not Defined."
    );
} elseif (empty($log_user) || is_null($log_user)) {
    $apiresults 									= array(
        "result" 										=> "Error: Session User Not Defined."
    );
} else {
    // check if goUser and goPass are valid
    $fresults										= $astDB
        ->where("user", $goUser)
        ->where("pass_hash", $goPass)
        ->getOne("vicidial_users", "user,user_level");

    $goapiaccess									= $astDB->getRowCount();
    $userlevel										= $fresults["user_level"];

    if ($goapiaccess > 0 && $userlevel > 7) {
        if (is_array($allowed_campaigns)) {
            if (strtoupper($log_group) !== 'ADMIN') {
                //$allowed_campaigns	= allowed_campaigns($log_group, $goDB, $astDB);
                $stringv = implode(",", $allowed_campaigns);
                $ul = "and campaign_id IN ($stringv)";

                $getIngroups                        = $astDB->where('user_group', $log_group)
                    //->orWhere("user_group", "---ALL---")
                    ->get('vicidial_inbound_groups', NULL, array('group_id'));

                $ingroups                           = array();
                foreach ($getIngroups as $fresults) {
                    $ingroups[]                     = $fresults['group_id'];
                }

                $stringvi                           = implode(",", $ingroups);
                $uli                                = "and campaign_id IN ('$stringvi')";

            }

            //inbound
            $query = "SELECT date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23',sum(if(date_format(call_date,'%H') = 24, 1, 0)) as 'Hour24' from vicidial_closer_log WHERE date_format(call_date, '%Y-%m-%d') = CURDATE() $uli GROUP BY cdate;";
            $fresults = $astDB->rawQuery($query);
            //$fresults = mysqli_fetch_assoc($rsltv);

            if ($fresults == NULL) {
                $fresults                           = array();
            }

            //dropped
            $queryDropped = "select date_format(call_date, '%Y-%m-%d') as cdated,sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1d',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2d',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3d',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4d',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5d',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6d',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7d',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8d',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9d',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10d',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11d',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12d',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13d',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14d',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15d',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16d',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17d',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18d',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19d',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20d',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21d',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22d',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23d',sum(if(date_format(call_date,'%H') = 24, 1, 0)) as 'Hour24d' from vicidial_log WHERE status IN ('DROP', 'IVRXFR') AND date_format(call_date, '%Y-%m-%d') = CURDATE() $ul GROUP BY cdated;";
            $dresults = $astDB->rawQuery($queryDropped);
            //$dresults = mysqli_fetch_assoc($rsltd);

            if ($dresults == NULL) {
                $dresults                           = array();
            }

            //outbound
            $queryOut = "select date_format(call_date, '%Y-%m-%d') as cdateo,sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1o',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2o',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3o',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4o',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5o',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6o',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7o',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8o',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9o',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10o',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11o',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12o',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13o',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14o',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15o',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16o',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17o',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18o',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19o',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20o',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21o',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22o',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23o',sum(if(date_format(call_date,'%H') = 24, 1, 0)) as 'Hour24o' from vicidial_log WHERE date_format(call_date, '%Y-%m-%d') = CURDATE() $ul GROUP BY cdateo";
            $oresults = $astDB->rawQuery($queryOut);
            //$oresults = mysqli_fetch_assoc($rsltOut);

            if ($oresults == NULL) {
                $oresults                           = array();
            }

            $apiresults = array_merge( array( "result" => "success" ), $fresults, $dresults, $oresults);
        }
    } else {
        $err_msg 									= error_handle("10001");
        $apiresults 								= array(
            "code" 										=> "10001",
            "result" 									=> $err_msg
        );
    }
}

?>
