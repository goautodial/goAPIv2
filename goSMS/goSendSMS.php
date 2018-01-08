<?php
   ##################################################
   ### Name: goSendSMS.php                        ###
   ### Description: API to send sms 	          ###
   ### Version: 0.9                               ###
   ### Copyright: GOAutoDial Ltd. (c) 2011-2015   ###
   ### Written by: Noel Umandap     			  ###
   ### License: AGPLv2                            ###
   ##################################################
    //ini_set('display_errors', 'on');
    //error_reporting(E_ALL);

	$datenow = date('Y-m-d H:i:s');

	$user_id = $_REQUEST['user_id'];
	$log_user = $_REQUEST['log_user'];
	$log_group = $_REQUEST['log_group'];

	$arr_post_body = array(
        "message_type" 	=> "SEND",
        "mobile_number" => mysqli_real_escape_string($link, $_REQUEST['phone_number']),
        "shortcode" 	=> "29290462886",
        "message_id" 	=> gen_random(),
        "message" 		=> mysqli_real_escape_string($link, urlencode($_REQUEST['message'])),
        "client_id" 	=> "fdd645b7328431d8f21a6301de52b42fe4b06514f47fcaae0e70790e3fb8cd8d",
        "secret_key" 	=> "649dc271effa414ebfa2c3d473024cf4362f994b1d42c1a4b92d088f8a4c1bec"
    );

    $query_string = "";
    foreach($arr_post_body as $key => $frow)
    {
        $query_string .= '&'.$key.'='.$frow;
    }

    $URL = "https://post.chikka.com/smsapi/request";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_POST, count($arr_post_body));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if(strpos($response, '200') && strpos($response, 'ACCEPTED')){
        $data_insert = array(
            'message_type'  => $arr_post_body["message_type"], 
            'mobile_number' => $arr_post_body["mobile_number"], 
            'shortcode'     => $arr_post_body["shortcode"], 
            'message_id'    => $arr_post_body["message_id"], 
            'message'       => $arr_post_body["message"], 
            'client_id'     => $arr_post_body["client_id"], 
            'secret_key'    => $arr_post_body["secret_key"], 
            'created'       => $datenow, 
            'status'        => 1, 
            'is_deleted'    => 0, 
            'user_id'       => $user_id
        );
        $queryGoSMS = $goDB->insert('go_sms', $data_insert);

    	if($queryGoSMS) {
    		$apiresults = array("result" => "success");
    	}else{
    		$apiresults = array("result" => "error");
    	}
    }else{
    	$apiresults = array("result" => "error", "error_message" => $response);
    }
	

	function gen_random($length=32){
		$final_rand='';
		for($i=0;$i< $length;$i++){
		    $final_rand .= rand(0,9);

		}

		return $final_rand;
	}

?>