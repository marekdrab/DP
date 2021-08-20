<?php
session_start();
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

$bots = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));

$json = file_get_contents('coordinates/input_coords/path.json');
$paths = json_decode($json, true);
$positions = $_SESSION['positions'];
$coords = array();

for ($i = 0; $i < sizeof($positions); $i++) {
    $turnAngle = pi() / 589;
    //$turnAngle /= 2;
    if ($positions[$i] >= sizeof($paths)) {
        $positions[$i] = 0;
    }
    if (isset($_SESSION['coords'])) {
        //setted session
        var_dump($_SESSION['coords']);
        $x = $_SESSION['coords'][$i]['x'];
        $z = $_SESSION['coords'][$i]['z'];
        $r = $_SESSION['coords'][$i]['r'];
        $sumAngle = $_SESSION['sumAngle'];
    } else {
        //not setted session
        $x = $paths[$positions[$i]]['x'];
        $z = $paths[$positions[$i]]['z'];
        $r = $paths[$positions[$i]]['r'];
        $sumAngle = $r;
    }

    if($x > -1000 && $x < 1000 && $z > 0){
        $x-=1;
        $r=0;
        $sumAngle = pi()/2;
    }
    else if ($x <=-1000 || $x >= 1000){
        /*if($x == -1000 && $z==-300){
            $x+=1;
            $r=0;
        }
        else if($x == 1000 && $z==300){
            $x-=1;
            $r=0;
        }*/
        //prva zakruta druha polovica
        if($x <=-1000 && $z<=300){
            /*$z-=1;
            if($z<0){
                $x+=1;
            }
            else if ($z>=0){
                $x-=1;
            }*/
            $r = -$turnAngle;
            $sumAngle += $turnAngle;
            $x = -1000 - 300*sin($sumAngle);
            $z = 300*cos($sumAngle);

        }
        //druha polovica
        else if($x >=1000 && $z>=-300){
            /*$z+=1;
            if($z<0){
                $x+=1;
            }
            else if ($z>=0){
                $x-=1;
            }
            $r = -$turnAngle;
            $sumAngle += $turnAngle;*/
            $r = -$turnAngle;
            $sumAngle += $turnAngle;
            $x = 1000 + 300*sin($sumAngle);
            $z = 300*cos($sumAngle);
        }

    }
    else if($x > -1000 && $x < 1000 && $z < 0){
        $x+=1;
        $r=0;//-3.1415926535897682;
        $sumAngle = 3*(pi()/2);
    }

    $coords_new = array('x' => round($x), 'z' => round($z), 'r' => $r);

//    array_push($coords, $paths[$positions[$i]++]);
    array_push($coords,$coords_new);
    $_SESSION['positions'] = $positions;
    $_SESSION['sumAngle'] = $sumAngle;
}
$_SESSION['coords'] = $coords;
file_put_contents('coordinates/input_coords/positionsBots/positionsBots'.$_SESSION['userID'].'.json', json_encode($coords));
echo json_encode($coords);