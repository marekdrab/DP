<?php
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['select'])&& isset($_POST['bots'])){

    header('location: model.php?choice='.$_POST['select']);
}