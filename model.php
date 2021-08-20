<?php
include "config.php";
session_start();

?>
<!DOCTYPE html>
<html lang="<?= $lang['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Lukas Korciciak">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['model'] ?> | Simulation of Vehicle</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/model.css">
    <link rel="icon" type="image/png" href="favicon.png">

    <script src="lib/babylon/babylon.js"></script>
    <script src="lib/babylon/babylon.objFileLoader.js"></script>
    <script src="lib/babylon/babylonjs.loaders.min.js"></script>
    <script src="lib/babylon/babylon.gui.min.js"></script>
    <script src="lib/graph/plotly-latest.min.js"></script>
</head>
<body>
<canvas id="renderCanvas" touch-action="none"></canvas>
<div id="graph">
    <div class="menu-section">
        <a href="index.php" class="btn btn-dark"><?= $lang['home'] ?></a>
        <a href="record.php" class="btn btn-dark"><?= $lang['record'] ?></a>
        <a href="javascript:void(0)" id="stopModel" class="btn btn-dark"><?= $lang['stop'] ?></a>
        <a href="javascript:void(0)" id="restartModel" class="btn btn-dark d-none"><?= $lang['restart'] ?></a>
    </div>
    <div id="carSensors">
        <div id="car">
            <div id="front-sensor"></div>
            <div id="left-front-sensor"></div>
            <div id="left-rear-sensor"></div>
            <?php switch ($_GET['choice']) {
                case"car":
                    ?><img src="img/pickup-senzory.png" alt="auto" class="senzor-image"> <?php
                    break;
                case"bus":
                    ?><img src="img/bus-senzory.png" alt="autobus" class="senzor-image" id="bus-image"> <?php
                    break;
                case"truck":
                    ?><img src="img/truck-senzory.jpg" alt="truck" class="senzor-image"> <?php
                    break;
            } ?>
            <div id="right-front-sensor"></div>
            <div id="right-rear-sensor"></div>
            <div id="rear-sensor"></div>
        </div>
    </div>
    <h6><?= $lang['graph1'] ?></h6>
    <div id="carPosition" class="graph-section"></div>
    <h6><?= $lang['graph2'] ?></h6>
    <div id="carSpeed" class="graph-section"></div>
    <h6><?= $lang['graph3'] ?></h6>
    <div id="botPosition" class="graph-section"></div>
    <div class="lang-section">
        <a href="model.php?choice=<?= $_SESSION['select'] ?>&lang=en"><img src="img/en.png" class="img-fluid pr-3"
                                                                           alt="EN"></a>
        <a href="model.php?choice=<?= $_SESSION['select'] ?>&lang=sk"><img src="img/sk.png" class="img-fluid" alt="SK"></a>
    </div>
</div>

<script src="script.js"></script>
<script src="gamepad.js"></script>
<script src="Sensors.js"></script>
<script src="CarSensors.js"></script>
<script src="Esentials.js"></script>
<script src="Bots.js"></script>
<script src="scene.js"></script>

</body>
</html>