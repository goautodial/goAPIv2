<?php
if ($_GET['dev'] == 'test') {
    //$lead_id = "12313";
    //$lead_id = preg_replace("/[^0-9]/","",$lead_id);
    //var_dump($lead_id);
    $test1 = strtotime("2016-04-12 13:38:54");
    $test2 = strtotime("2016-04-12 13:39:03");
    $test3 = $test2 - $test1;
    var_dump($test1, $test2, $test3);
}
?>