<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
function sendMsg($msg)
{
    echo 'data: ' . json_encode($msg) . "\n\n";
    ob_flush();
    flush();
}

session_start();
$bots = $_SESSION['bots'];
$json = file_get_contents('coordinates/input_coords/path.json');
$paths = json_decode($json, true);
$positions = $_SESSION['positions'];
do {
    $coords = array();

    for ($i = 0; $i < sizeof($positions); $i++) {
        if($positions[$i]<sizeof($paths)){
            array_push($coords, $paths[$positions[$i]++]);
        }
        else{
            $positions[$i]=0;
        }
    }
    sendMsg($coords);
    sleep(1);
} while (true);
