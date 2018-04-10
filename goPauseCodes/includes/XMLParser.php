<?php
####################################################
#### Name: XMLParser.php                        ####
#### Type: XML parser for Agent UI              ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

// creating object of SimpleXMLElement
$xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="'.$goCharset.'"?><goautodialapi version="'.$goVersion.'"></goautodialapi>');

// function defination to convert array to xml
function array_to_xml( $data, &$xml_data ) {
    foreach( $data as $key => $value ) {
        if( is_array($value) ) {
            if( is_numeric($key) ) {
                $key = "{$type}_{$key}"; // XML Naming Rules: Names cannot start with a number or punctuation character
            }
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $getKey = get_node_type($xml_data->getName());
            if ( is_numeric($key) ) {
                $newKey = (isset($getKey)) ? $getKey : 'item'; // XML Naming Rules: Names cannot start with a number or punctuation character
                $childNode = $xml_data->addChild("$newKey",htmlspecialchars("$value"));
                $childNode->addAttribute('id', $key);
            } else {
                $newKey = (isset($getKey)) ? $getKey : $key;
                $childNode = $xml_data->addChild("$newKey",htmlspecialchars("$value"));
                if ($getKey != null) {
                    $childNode->addAttribute('id', $key);
                }
            }
        }
    }
}

function get_node_type( $type ) {
    switch ($type) {
        case "campaigns":
        case "allowed_campaigns":
            $node = 'campaign';
            break;
        case "statuses":
            $node = 'status';
            break;
        case "xfer_groups":
        case "inbound_groups":
            $node = 'group';
            break;
        default:
            $node = null;
    }
    return $node;
}

function implode_recur( $separator, $arrayvar, $space='' ) {
    foreach ($arrayvar as $k => $av) {
        if (is_array ($av)) {
            $out .= "{$space}{$k}={\n";
            $newSpace = "{$space}    ";
            $out .= implode_recur($separator, $av, $newSpace); // Recursive array
            $out .= "{$space}}{$separator}\n";
        } else {
            $out .= "{$space}{$k}={$av}{$separator}\n";
        }
    }
    return $out;
}

function parse_xml( $xml ) {
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;
    $doc->loadXML($xml);
    
    header("Content-type: application/xml");
    echo $doc->saveXML();
}
?>