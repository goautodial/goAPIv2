<?php
if ($_GET['dev'] == 'test') {
    $lead_id = "12313";
    $lead_id = preg_replace("/[^0-9]/","",$lead_id);
    var_dump($lead_id);
}
?>