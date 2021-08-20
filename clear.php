<?php
session_start();

$json = file_get_contents('coordinates/input_coords/lastDeletingBots.json');
$deletedTime = json_decode($json,true);
if(($_SESSION['userID']-$deletedTime)/3600 > 6){
    $files = glob('coordinates/input_coords/positionsBots/*'); // get all file names
    foreach($files as $file){ // iterate files
        if(is_file($file)) {
            unlink($file); // delete file
            file_put_contents('coordinates/input_coords/lastDeletingBots.json', json_encode($_SESSION['userID']));
        }
    }
}