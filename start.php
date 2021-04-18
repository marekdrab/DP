<?php
$json = file_get_contents('coordinates/input_coords/path.json');
$paths = json_decode($json, true);
session_start();
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['select'])&& isset($_POST['bots'])){
    $_SESSION['select'] = $_POST['select'];
    $numbers = array();
    $bots = intval($_POST['bots']);
    $coord = array();
    for ($i = 0; $i < $bots; $i++){
        array_push($numbers,rand(0,sizeof($paths)));
    }
    for($i=0; $i<sizeof($numbers);$i++){
        array_push($coord,$paths[$numbers[$i]]);
    }
    file_put_contents('coordinates/input_coords/inputBots.json', json_encode($coord));
    //print_r($coord);
    header('location: model.php?choice='.$_POST['select']);
}