<?php
   ##################################################
   ### Name: goSendSMS.php                        ###
   ### Description: API to send sms 	          ###
   ### Version: 0.9                               ###
   ### Copyright: GOAutoDial Ltd. (c) 2011-2015   ###
   ### Written by: Noel Umandap     			  ###
   ### License: AGPLv2                            ###
   ##################################################
    
	include_once("../goFunctions.php");

	try
    {
        $message_type = $_POST["message_type"];
    }
    catch (Exception $e)
    {
        echo "Error 1";
        exit(0);
    }

    if (strtoupper($message_type) == "INCOMING"){
        try
        {
            $message = $_POST["message"];
            $mobile_number = $_POST["mobile_number"];
            $shortcode = $_POST["shortcode"];
            $timestamp = $_POST["timestamp"];
            $request_id = $_POST["request_id"];

            // echo "Accepted";
            // send reply
            $message = "We have received your text message. This is a reply message from GoAutoDial Inc.";
            $arr_post_body = array(
		        "message_type" 	=> "REPLY",
		        "mobile_number" => $mobile_number,
		        "shortcode" 	=> "29290462886",
		        "request_id" 	=> $request_id,
		        "message_id" 	=> gen_random(),
		        "message" 		=> $message,
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

            exit(0);
        }
        catch (Exception $e)
        {
            echo "Error 2";
            exit(0);
        }
    }else{
        echo "Error 3";
        exit(0);
    }

    function gen_random($length=32){
		$final_rand='';
		for($i=0;$i< $length;$i++){
		    $final_rand .= rand(0,9);

		}

		return $final_rand;
	}

?>