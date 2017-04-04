<?php
   ####################################################
   #### Name: goEditLeads.php                      ####
   #### Description: API to edit specific lead     ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
   #### Written by: Alexander Abenoja _m/          ####
   #### License: AGPLv2                            ####
   ####################################################

    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
        $goUser = $_REQUEST['goUser'];
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
        $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
        $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);

        $lead_id        = mysqli_real_escape_string($link, $_REQUEST['lead_id']);
        $first_name     = mysqli_real_escape_string($link, $_REQUEST['first_name']);
        $middle_initial = mysqli_real_escape_string($link, $_REQUEST['middle_initial']);
        $last_name      = mysqli_real_escape_string($link, $_REQUEST['last_name']);
        $gender         = mysqli_real_escape_string($link, $_REQUEST['gender']);
        $email          = mysqli_real_escape_string($link, $_REQUEST['email']);
        $phone_number   = mysqli_real_escape_string($link, $_REQUEST['phone_number']);
        $alt_phone      = mysqli_real_escape_string($link, $_REQUEST['alt_phone']);
        $address1       = mysqli_real_escape_string($link, $_REQUEST['address1']);
        $address2       = mysqli_real_escape_string($link, $_REQUEST['address2']);
        $address3       = mysqli_real_escape_string($link, $_REQUEST['address3']);
        $city           = mysqli_real_escape_string($link, $_REQUEST['city']);
        $province       = mysqli_real_escape_string($link, $_REQUEST['province']);
        $postal_code    = mysqli_real_escape_string($link, $_REQUEST['postal_code']);
        $country_code   = mysqli_real_escape_string($link, $_REQUEST['country_code']);
        $date_of_birth  = mysqli_real_escape_string($link, $_REQUEST['date_of_birth']);
        $title          = mysqli_real_escape_string($link, $_REQUEST['title']);
		$status         = mysqli_real_escape_string($link, $_REQUEST['status']);
        $is_customer    = mysqli_real_escape_string($link, $_REQUEST['is_customer']);
        $user_id        = mysqli_real_escape_string($link, $_REQUEST['user_id']);
        $avatar         = mysqli_real_escape_string($link, $_REQUEST['avatar']); //base64 encoded
        
        $query = "UPDATE vicidial_list
        SET first_name = '$first_name',
        middle_initial = '$middle_initial',
        last_name = '$last_name',
        gender = '$gender',
        email = '$email',
        phone_number = '$phone_number',
        alt_phone = '$alt_phone',
        address1 = '$address1',
        address2 = '$address2',
        address3 = '$address3',
        city = '$city',
        province = '$province',
        postal_code = '$postal_code',
        country_code = '$country_code',
        date_of_birth = '$date_of_birth',
        title = '$title',
		status = '$status'
        WHERE lead_id = '$lead_id';";
        
        $querygo = "SELECT '$lead_id'
        FROM go_customers
        WHERE lead_id ='$lead_id' 
        LIMIT 1;";                
        
        $updateQuery = mysqli_query($link, $query);
        $updateQuerygo = mysqli_query($linkgo, $querygo);
        
        if($updateQuery > 0){
            if ($is_customer) {                                              
                $rsltu = mysqli_query($link, "SELECT user_group FROM vicidial_users WHERE user_id='$user_id';");
                $fresults = mysqli_fetch_array($rsltu, MYSQLI_ASSOC);
                $user_group = $fresults['user_group'];
                
                $rsltg = mysqli_query($linkgo, "SELECT group_list_id FROM user_access_group WHERE user_group='$user_group';");
                $fresults = mysqli_fetch_array($rsltg, MYSQLI_ASSOC);
                $group_list_id = $fresults['group_list_id'];
                
                $countrsltgo = mysqli_num_rows($updateQuerygo);                
                
                if ($countrsltgo < 1) {
                
                    $querygo = "INSERT
                    INTO go_customers 
                    VALUES (null, '$lead_id', '$group_list_id', '$avatar') 
                    WHERE lead_id ='$lead_id';"; 
                    
                    $rsltgo = mysqli_query($linkgo, $querygo);
                    $fresultsgo = mysqli_fetch_array($rsltgo, MYSQLI_ASSOC);
                } else {
                
                    $querygo = "UPDATE go_customers 
                    SET avatar = '$avatar', group_list_id='$group_list_id'
                    WHERE lead_id ='$lead_id';";                    
            
                    $rsltgo = mysqli_query($linkgo, $querygo);
                    $fresultsgo = mysqli_fetch_array($rsltgo, MYSQLI_ASSOC);
                }
            } else {           
            
                $querygo = "INSERT
                INTO go_customers 
                VALUES (null, '$lead_id', '$group_list_id', '$avatar') 
                WHERE lead_id ='$lead_id';"; 
                
                $rsltgo = mysqli_query($linkgo, $querygo);
                $fresultsgo = mysqli_fetch_array($rsltgo, MYSQLI_ASSOC);
            }
            
            $log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified the Lead ID: $lead_id", $log_group, $query, $querygo);
            
            $apiresults = array("result" => "success");
        }else{
            $apiresults = array("result" => "Error: Failed to Update");
        }
    
?>
