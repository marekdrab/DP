<?php
include "config.php";
session_start();
?>
<!DOCTYPE html>
<html lang="<?= $lang['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Marek Drab">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['model'] ?> | Simulation of Vehicle</title>

    <script src="lib/jquery/jquery.min.js"></script>
    <script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="icon" type="image/png" href="favicon.png">


</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-transparent-9 fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">Simulation of Vehicle</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><?= $lang['home'] ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="record.php"><?= $lang['record'] ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger font-weight-bold" href="choice.php"><?= $lang['start'] ?></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<header class="bg-custom py-5 mb-5">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-lg-12">
                <p class="h5 mt-5 mb-0"></p>
                <h1 class="display-4 font-weight-bold mb-3"><?= $lang['vehicle'] ?></h1>
                <p class="h5 font-weight-normal mb-4"><?= $lang['vehicle-sub'] ?></p>
            </div>
        </div>
    </div>
</header>
<div class="container">
    <form method="post" action="start.php">
        <div class="row">
            <div class="col-md-4 text-center">
                <input type="radio" id="control_01" name="select" value="car" required>
                <label for="control_01" id="label_car">
                    <p class="h5 mt-5 mb-5"><?= $lang['pickup'] ?></p>
                </label>
            </div>
            <div class="col-md-4 text-center">
                <input type="radio" id="control_02" name="select" value="truck" required>
                <label for="control_02" id="label_truck">
                    <p class="h5 mt-5 mb-5"><?= $lang['truck'] ?></p>
                </label>
            </div>
            <div class="col-md-4 text-center">
                <input type="radio" id="control_03" name="select" value="bus" required>
                <label for="control_03" id="label_bus">
                    <p class="h5 mt-5 mb-5"><?= $lang['bus'] ?></p>
                </label>
            </div>
        </div>
        <br>
        <div class="row justify-content-center">
            <div class="col-md-4 text-center">
                <h3><?= $lang['bot-count']?></h3>
                <input class="form-control mb-3 mt-3" type="number" id="bots" name="bots" min="1" max="10" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <button class="btn btn-danger" type="submit" id="startModel"><?= $lang['start']?></button>
            </div>
        </div>
    </form>
</div>
<footer class="py-4 bg-dark fixed-bottom">
    <div class="container">
        <div class="row align-items-center text-center text-white">
            <div class="col-md-4">
                <p><?= $lang['author'] ?><br>Bc. Lukáš Korčičiak, Marek Dráb</p>
                <p><?= $lang['supervisor'] ?><br>doc. Ing. Katarína Žáková, PhD.</p>
            </div>
            <div class="col-md-4">
                <img src="img/stu-fei.png" class="img-fluid" alt="STU FEI">
                <p class="mb-0"><?= $lang['stu'] ?></p>
                <p><?= $lang['fei'] ?></p>
            </div>
            <div class="col-md-4">
                <div class="pb-3">
                    <a href="index.php?lang=en"><img src="img/en.png" class="img-fluid pr-3" alt="EN"></a>
                    <a href="index.php?lang=sk"><img src="img/sk.png" class="img-fluid" alt="SK"></a>
                </div>
                <p>Copyright &copy; Simulation of Vehicle 2020</p>
            </div>
        </div>
    </div>
</footer>
<script src="script.js"></script>
</body>
</html>