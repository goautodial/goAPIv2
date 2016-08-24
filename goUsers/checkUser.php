<?php
   #####################################################
   #### Name: goCheckUser.php	                    ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Alexander Jim H. Abenoja        ####
   #### License: AGPLv2                             ####
   #####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
        $user = mysqli_real_escape_string($link, $_REQUEST['user']);
        $phone_login = mysqli_real_escape_string($link, $_REQUEST['phone_login']);

        $response = array();
        $validate = 0;
        
        // User Duplicate Check
        $queryUserCheck = "SELECT user FROM vicidial_users WHERE user = '$user';";
        $rsltvCheck1 = mysqli_query($link, $queryUserCheck);
        $countCheckResult1 = mysqli_num_rows($rsltvCheck1);
        if($countCheckResult1 > 0) {
            $validate = $validate + 1;
            $response = array_push("user" => "There are 1 or more users with that User ID.");
        }else{
            $validate = $validate - 1;
        }
        
        // Phone Login Check optional when not null
        if($phone_login != NULL){
            $queryPhoneCheck = "SELECT user FROM vicidial_users WHERE phone_login = '$phone_login';";
            $rsltvCheck2 = mysqli_query($link, $queryNameCheck);
            $countCheckResult2 = mysqli_num_rows($rsltvCheck2);
            if($countCheckResult2 > 0) {
                $validate = $validate + 1;
                $response = array_push("phone login" => "There are 1 or more users with that Phone Login.");
            }else{
                $validate = $validate - 1;
            }
        }
        
        if($validate <= 0){
            $apiresults = array("result" => "success");
        }else{
            $apiresults = array_merge("result" => "fail", "data" => $response);
        }
?>
