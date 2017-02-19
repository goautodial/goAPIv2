<?php
    #######################################################
    #### Name: goGetLeadsInfo.php	               ####
    #### Description: API to get specific contact      ####
    #### Copyright: GOAutoDial Inc. (c) 2016           ####
    #### Written by: Alexander Abenoja                 ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $lead_id = $_REQUEST['lead_id'];
    
    $ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
    $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    ### Check user_id if its null or empty
    if($lead_id == null) { 
            $apiresults = array("result" => "Error: Set a value for Lead ID."); 
    } else { 
        $groupId = go_get_groupid($goUser);
    
        if (!checkIfTenant($groupId)) {
            $ul = "AND user='$user_id'";
        } else { 
            $ul = "AND user='$user_id' AND user_group='$groupId'";  
        }

        if ($groupId != 'ADMIN') {
            $notAdminSQL = "AND user_group != 'ADMIN'";
        }

        $query = "SELECT lead_id,list_id,first_name,middle_initial,last_name,email,phone_number,alt_phone,address1,address2,address3,city,state,province,postal_code,country_code,gender,status,user,date_of_birth FROM vicidial_list where lead_id='$lead_id'";
        $rsltv = mysqli_query($link, $query);
        $fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC);
        
        $is_customer = 0;
        if ($rsltv) {
            $rsltc = mysqli_query($linkgo, "SELECT * FROM go_customers WHERE lead_id='$lead_id' LIMIT 1;");
            $fresultsc = mysqli_fetch_array($rsltc, MYSQLI_ASSOC);
            $is_customer = mysqli_num_rows($rsltc);
        }

        $data = empty($fresultsc) ? $fresults : array_merge($fresults, $fresultsc) ;       
        $log_id = log_action($linkgo, 'VIEW', $log_user, $ip_address, "Viewed the lead info of Lead ID: $lead_id", $log_group);
        
        $apiresults = array("result" => "success", "data" => $data, "is_customer" => $is_customer);

    }
?>
