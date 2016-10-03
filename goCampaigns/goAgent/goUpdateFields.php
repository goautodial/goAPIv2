<?php
####################################################
#### Name: goUpdateFields.php                   ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$agent = get_settings('user', $astDB, $goUser);

$user = $agent->user;
$user_group = $agent->user_group;
$phone_login = (isset($phone_login)) ? $phone_login : $agent->phone_login;
$phone_pass = (isset($phone_pass)) ? $phone_pass : $agent->phone_pass;

$is_logged_in = check_agent_login($astDB, $user);

if ($is_logged_in) {
	//$stmt="UPDATE vicidial_live_agents set external_update_fields='0',external_update_fields_data='' where user='$user';";
    $astDB->where('user', $user);
    $rslt = $astDB->update('vicidial_live_agents', array( 'external_update_fields' => '0', 'external_update_fields_data' => '' ));

	//$stmt="SELECT lead_id from vicidial_live_agents where user='$user';";
    $astDB->where('user', $user);
    $rslt = $astDB->get('vicidial_live_agents', null, 'lead_id');
	$vla_records = $astDB->getRowCount();
	if ($vla_records > 0) {
		$row = $rslt[0];
		if ($row['lead_id'] > 0) {$lead_id = $row['lead_id'];}
        
		//$stmt="SELECT count(comment_id) as comment_count FROM vicidial_comments where lead_id='$lead_id' and hidden is null";
        $astDB->where('lead_id', $lead_id);
        $astDB->where('hidden', null, 'is');
        $rslt = $astDB->get('vicidial_comments', null, 'count(comment_id) as comment_count');
        $row = $rslt[0];
        $lead_comment_count = trim("{$row['comment_count']}");

		##### grab the data from vicidial_list for the lead_id
		//$stmt="SELECT vendor_lead_code,source_id,gmt_offset_now,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,rank,owner FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
        $astDB->where('lead_id', $lead_id);
        $rslt = $astDB->getOne('vicidial_list', 'vendor_lead_code,source_id,gmt_offset_now,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,rank,owner');
		$list_lead_ct = $astDB->getRowCount();
		if ($list_lead_ct > 0) {
			$row = $rslt;
			$vendor_id		= trim("{$row['vendor_lead_code']}");
			$source_id		= trim("{$row['source_id']}");
			$gmt_offset_now	= trim("{$row['gmt_offset_now']}");
			$phone_code		= trim("{$row['phone_code']}");
			$phone_number	= trim("{$row['phone_number']}");
			$title			= trim("{$row['title']}");
			$first_name		= trim("{$row['first_name']}");
			$middle_initial	= trim("{$row['middle_initial']}");
			$last_name		= trim("{$row['last_name']}");
			$address1		= trim("{$row['address1']}");
			$address2		= trim("{$row['address2']}");
			$address3		= trim("{$row['address3']}");
			$city			= trim("{$row['city']}");
			$state			= trim("{$row['state']}");
			$province		= trim("{$row['province']}");
			$postal_code	= trim("{$row['postal_code']}");
			$country_code	= trim("{$row['country_code']}");
			$gender			= trim("{$row['gender']}");
			$date_of_birth	= trim("{$row['date_of_birth']}");
			$alt_phone		= trim("{$row['alt_phone']}");
			$email			= trim("{$row['email']}");
			$security		= trim("{$row['security_phrase']}");
			$comments		= stripslashes(trim("{$row['comments']}"));
			$rank			= trim("{$row['rank']}");
			$owner			= trim("{$row['owner']}");

			$comments = preg_replace("/\r/i", '', $comments);
			$comments = preg_replace("/\n/i", '!N', $comments);
 
            $areacode = substr($phone_number, 0, 3);
            //$stmt="SELECT country FROM vicidial_phone_codes where country_code='$phone_code' and areacode='$areacode' LIMIT 1;";
            $astDB->where('country_code', $phone_code);
            $astDB->where('areacode', $areacode);
            $rslt = $astDB->getOne('vicidial_phone_codes', 'country');
            $phone_code_ct = $astDB->getRowCount();
            if ($phone_code_ct > 0) {
                $converted_dial_code = trim("{$rslt['country']}");
            }

			$LeaD_InfO  = array(
                'status' => 'GOOD',
			    'vendor_id' => $vendor_id,
			    'source_id' => $source_id,
			    'gmt_offset' => $gmt_offset_now,
			    'phone_code' => $phone_code,
			    'phone_number' => $phone_number,
			    'title' => $title,
			    'first_name' => $first_name,
			    'middle_initial' => $middle_initial,
			    'last_name' => $last_name,
			    'address1' => $address1,
			    'address2' => $address2,
			    'address3' => $address3,
			    'city' => $city,
			    'state' => $state,
			    'province' => $province,
			    'postal_code' => $postal_code,
			    'country_code' => $country_code,
			    'gender' => $gender,
			    'date_of_birth' => $date_of_birth,
			    'alt_phone' => $alt_phone,
			    'email' => $email,
			    'security' => $security,
			    'comments' => $comments,
			    'rank' => $rank,
			    'owner' => $owner,
			    'lead_comment_count' => $lead_comment_count,
			    'converted_dial_code' => $converted_dial_code
            );

            $APIResult = array( "result" => "success", "data" => $LeaD_InfO );
		} else {
            $APIResult = array( "result" => "error", "message" => "No lead info in the system: $lead_id" );
		}
	} else {
        $APIResult = array( "result" => "error", "message" => "No lead active for agent $user" );
	}
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>