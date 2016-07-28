<?php
    ####################################################
    #### Name: goGetReports.php                     ####
    #### Description: API for reports               ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
    #### Written by: Jerico James Milo              ####
    #### License: AGPLv2                            ####
    ####################################################

        function go_getUsergroup($goUser ,$link){
            
            $query_userv = "SELECT user_group FROM vicidial_users WHERE user='$goUser'";
            $rsltv = mysqli_query($link, $query_userv);
            $check_resultv = mysqli_num_rows($rsltv);
            
            if ($check_resultv > 0) {
                $rowc=mysqli_fetch_array($rsltv, MYSQLI_ASSOC);
                $goUser_group = $rowc["user_group"];
                return $goUser_group;
            }
           
        }

        function remove_empty($array) {
    	   return array_filter($array, '_remove_empty_internal');
        }

        function _remove_empty_internal($value) {
            return !empty($value) || $value === 0;
        }
        
        function go_get_dates($d1, $d2)
        {
                $diff = explode("|", go_get_date_diff($d1, $d2));
                $days = $diff[2];

                for ($i=0;$i<=$days;$i++)
                {
                        $dateARY[$i] = $d1;
                        $d1 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($d1)) . " +1 day"));
                }
                return $dateARY;
        }

       
        function go_get_date_diff($d1, $d2)
        {
                $diff = abs(strtotime($d2) - strtotime($d1));

                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                //printf("%d years, %d months, %d days\n", $years, $months, $days);
                return "$years|$months|$days";
        }

        ##### reformat seconds into HH:MM:SS or MM:SS #####
        function go_sec_convert($sec,$precision)
                {
                $sec = round($sec,0);

                if ($sec < 1)
                        {
                        return "0:00";
                        }
                else
                        {
                        if ($sec < 3600) {$precision='M';}

                        if ($precision == 'H')
                                {
                                $Fhours_H =     ($sec / 3600);
                                $Fhours_H_int = floor($Fhours_H);
                                $Fhours_H_int = intval("$Fhours_H_int");
                                $Fhours_M = ($Fhours_H - $Fhours_H_int);
                                $Fhours_M = ($Fhours_M * 60);
                                $Fhours_M_int = floor($Fhours_M);
                                $Fhours_M_int = intval("$Fhours_M_int");
                                $Fhours_S = ($Fhours_M - $Fhours_M_int);
                                $Fhours_S = ($Fhours_S * 60);
                                $Fhours_S = round($Fhours_S, 0);
                                if ($Fhours_S < 10) {$Fhours_S = "0$Fhours_S";}
                                if ($Fhours_M_int < 10) {$Fhours_M_int = "0$Fhours_M_int";}
                                $Ftime = "$Fhours_H_int:$Fhours_M_int:$Fhours_S";
                                }
                        if ($precision == 'M')
                                {
                                $Fminutes_M = ($sec / 60);
                                $Fminutes_M_int = floor($Fminutes_M);
                                $Fminutes_M_int = intval("$Fminutes_M_int");
                                $Fminutes_S = ($Fminutes_M - $Fminutes_M_int);
                                $Fminutes_S = ($Fminutes_S * 60);
                                $Fminutes_S = round($Fminutes_S, 0);
                                if ($Fminutes_S < 10) {$Fminutes_S = "0$Fminutes_S";}
                                $Ftime = "$Fminutes_M_int:$Fminutes_S";
                                }
                        if ($precision == 'S')
                                {
                                $Ftime = $sec;
                                }
                        return "$Ftime";
                        }
                }

 
?>
