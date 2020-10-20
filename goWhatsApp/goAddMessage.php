<?php
/**
 * @file        goGetWhatsAppInfo.php
 * @brief       API to get WhatsApp details 
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author      Thom Bernarth Patacsil
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
**/

    include_once ("goAPI.php");

    $token 												= $goDB->escape($_REQUEST["token"]);
    $instance 											= $goDB->escape($_REQUEST["instance"]);
    $user 												= $goDB->escape($_REQUEST['user']);
    $whatsapp_type 										= $goDB->escape($_REQUEST['whatsapp_type']);
    
    if (empty ($token) || is_null ($token)) {
        $apiresults 									= array(
            "result" 										=> "Error: Token Not Defined."
        );
    } elseif (empty ($instance) || is_null ($instance)) {
        $apiresults 									= array(
            "result" 										=> "Error: Instance Not Defined."
        );
    } elseif (empty ($user) || is_null ($user)) {
        $apiresults 									= array(
            "result" 										=> "Error: User Not Defined."
        );
    } elseif (empty ($whatsapp_type) || is_null ($whatsapp_type)) {
        $apiresults 									= array(
            "result"									 	=> "Error: WhatsApp Type Not Defined."
        );
    } else {
		// Go Whatsapp Validation
        $validate 										= $goDB
			->where('value', array($user, $token, $instance), "IN")
			->where('context', "module_GOautodialWhatsApp")
			->get('settings', null);

        $counter 										= $goDB->count;

        if ($counter != 3) {
            $apiresults 								= array(
                "result" 									=> "Error: Validation Failed."
            );
            
            return $apiresults;
        }

        $messageid 										= $goDB->escape($_REQUEST["id"]);
        $instanceId	 									= $instance;

        if ($whatsapp_type == 'message') {
            $body 										= (!isset($_REQUEST["body"]) ? '' : $goDB->escape($_REQUEST["body"]));
            $fromMe 									= (!isset($_REQUEST["fromMe"]) ? 0 : $goDB->escape($_REQUEST["fromMe"]));
            $self 										= (!isset($_REQUEST["self"]) ? 0 : $goDB->escape($_REQUEST["self"]));
            $isForwarded 								= (!isset($_REQUEST["isForwarded"]) ? 0 : $goDB->escape($_REQUEST["isForwarded"]));
            $author			 							= (!isset($_REQUEST["author"]) ? '' : $goDB->escape($_REQUEST["author"]));
            $time 										= (!isset($_REQUEST["time"]) ? 0 : $goDB->escape($_REQUEST["time"]));
            $chatId 									= (!isset($_REQUEST["chatId"]) ? '' : $goDB->escape($_REQUEST["chatId"]));
            $messageNumber 								= (!isset($_REQUEST["messageNumber"]) ? 0 : $goDB->escape($_REQUEST["messageNumber"]));
            $type 										= (!isset($_REQUEST["type"]) ? '' : $goDB->escape($_REQUEST["type"])); 
            $senderName 								= (!isset($_REQUEST["senderName"]) ? '' : $goDB->escape($_REQUEST["senderName"]));
            $caption 									= (!isset($_REQUEST["caption"]) ? '' : $goDB->escape($_REQUEST["caption"]));
            $quotedMsgBody 								= (!isset($_REQUEST["quotedMsgBody"]) ? '' : $goDB->escape($_REQUEST["quotedMsgBody"]));
            $quotedMsgId 								= (!isset($_REQUEST["quotedMsgId"]) ? '' : $goDB->escape($_REQUEST["quotedMsgId"]));
            $chatName 									= (!isset($_REQUEST["chatName"]) ? '' : $goDB->escape($_REQUEST["chatName"]));
        }

        if ($whatsapp_type == 'ack') {
            $queueNumber 								= (!isset($_REQUEST["queueNumber"]) ? 0 : $goDB->escape($_REQUEST["queueNumber"]));
            $chatId 									= (!isset($_REQUEST["chatId"]) ? '' : $goDB->escape($_REQUEST['chatId']));
            $status 									= (!isset($_REQUEST["status"]) ? '' : $goDB->escape($_REQUEST['status']));
        }

        if ($messageid != '') {
            if ($whatsapp_type == 'message') {
                $data_message 							= array(
                    'messageid' 							=> $messageid,
                    'body' 									=> $body,
                    'fromMe' 								=> $fromMe,
                    'self' 									=> $self,
                    'isForwarded' 							=> $isForwarded,
                    'author' 								=> $author,
                    'time' 									=> $time,
                    'chatId' 								=> $chatId,
                    'messageNumber' 						=> $messageNumber,
                    'type' 									=> $type,
                    'senderName' 							=> $senderName,
                    'caption' 								=> $caption,
                    'quotedMsgBody' 						=> $quotedMsgBody,
                    'quotedMsgId' 							=> $quotedMsgId,
                    'chatName' 								=> $chatName,
                    'instanceId' 							=> $instanceId
                );

                $insert_message 						= $goDB->insert('go_whatsapp_message', $data_message); 
                
                if ($insert_message) {
                    $apiresults 						= array(
                        "result" 							=> "success",
                        "message" 							=> $insert_message
                    );
                } else {
                    $apiresults 						= array(
                        "result" 							=> "error",
                        "error" 							=> $goDB->getLastError()
                    );    
                }
            } elseif($whatsapp_type == 'ack') {
                $data_ack 								= array(
                    'messageid' 							=> $messageid,
                    'queueNumber' 							=> $queueNumber,
                    'chatId' 								=> $chatId,
                    'status' 								=> $status,
                    'instanceId' 							=> $instanceId
                );
                
                $insert_ack 							= $goDB->insert('go_whatsapp_ack', $data_ack);
                
                if ($insert_ack) {
                    $apiresults 						= array(
                        "result" 							=> "success",
                        "ack" 								=> $insert_ack
                    );
                } else {
                    $apiresults 						= array(
                        "result" 							=> "error",
                        "error" 							=> $goDB->getLastError()
                    );    
                }
            } else {
                $apiresults 							= array(
                    "result" 								=> "Invalid Argument for WhatsApp Type"
                );
            }
        } else {
            $apiresults 								= array(
                "result" 									=> "error",
                "message" 									=> "ID is Empty"
            );
        }        
    }

?>
