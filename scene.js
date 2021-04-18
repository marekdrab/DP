var canvas = document.getElementById("renderCanvas");
var engine = new BABYLON.Engine(canvas, true);

var forwardVector;
var rightVector;
var brake;
var gear;


/******* Create scene ******/

var createScene = function () {
    /****** Getting choice from URL *******/
    console.log(choice);


    var scene = new BABYLON.Scene(engine);

    var meshes = {};
    var botsMeshes = new BotsMeshesStruct();

    var isCarReady = false;
    var isRoadReady = false;
    var isBotReady = false;
    var isTruckReady = false;
    var isBusReady = false;
    var isTreeReady = false;


    var switchCam = true;

    /******* Cameras ******/
    // TODO: kamery pre kamion a bus - rozdelovat podla choice
    if (choice == 'car') {
        var camera = new BABYLON.ArcRotateCamera("camera1", 0, 0, 20, new BABYLON.Vector3(-2.5, 0, 0), scene);
        camera.setPosition(new BABYLON.Vector3(18.5, 5.5, 0));
        camera.upperBetaLimit = (Math.PI / 2);
        camera.lowerBetaLimit = (Math.PI / 4);
        camera.lowerRadiusLimit = 15;
        camera.upperRadiusLimit = 60;
        camera.attachControl(canvas, true);
        var camera2 = new BABYLON.UniversalCamera("camera2", new BABYLON.Vector3(-4, 3.2, -1.3), scene);
        camera2.setTarget(new BABYLON.Vector3(-5, 3.2, -1.3));

    } else if (choice == 'truck') {
        var camera = new BABYLON.ArcRotateCamera("camera1", 0, 0, 20, new BABYLON.Vector3(-2.5, 0, 0), scene);
        camera.setPosition(new BABYLON.Vector3(10, 5.5, 0));
        camera.upperBetaLimit = (Math.PI / 2);
        camera.lowerBetaLimit = (Math.PI / 4);
        camera.lowerRadiusLimit = 15;
        camera.upperRadiusLimit = 60;
        camera.attachControl(canvas, true);
        var camera2 = new BABYLON.UniversalCamera("camera2", new BABYLON.Vector3(-0.64, 0.5, -1.3), scene);
        camera2.setTarget(new BABYLON.Vector3(-0.65, 0.5, -1.3));
    } else if (choice == 'bus') {
        var camera = new BABYLON.ArcRotateCamera("camera1", 0, 0, 20, new BABYLON.Vector3(-2.5, 0, 0), scene);
        camera.setPosition(new BABYLON.Vector3(45, 15, 0));
        camera.upperBetaLimit = (Math.PI / 2);
        camera.lowerBetaLimit = (Math.PI / 4);
        camera.lowerRadiusLimit = 50;
        camera.upperRadiusLimit = 96;
        camera.attachControl(canvas, true);
        var camera2 = new BABYLON.UniversalCamera("camera2", new BABYLON.Vector3(-16, 5, -2.625), scene);
        camera2.setTarget(new BABYLON.Vector3(-17, 5, -2.625));
    }


    scene.activeCamera = camera;


    /******* Lights ******/

    CreateLights(scene);

    /* var light1 = new BABYLON.DirectionalLight("light1", new BABYLON.Vector3(1, 2, 0), scene);
     var light2 = new BABYLON.HemisphericLight("light2", new BABYLON.Vector3(0, 1, 0), scene);
     light2.intensity = 0.75;*/


    /******* Skybox ******/

    var skybox = CreateSkyBox(scene);


    /*var skybox = BABYLON.MeshBuilder.CreateBox("skyBox", {size: 5000.0}, scene);
    var skyboxMaterial = new BABYLON.StandardMaterial("skyBox", scene);
    skyboxMaterial.backFaceCulling = false;
    skyboxMaterial.disableLighting = true;
    skyboxMaterial.reflectionTexture = new BABYLON.CubeTexture("textures/skybox/bluecloud", scene);
    skyboxMaterial.reflectionTexture.coordinatesMode = BABYLON.Texture.SKYBOX_MODE;
    skyboxMaterial.diffuseColor = new BABYLON.Color3(0, 0, 0);
    skyboxMaterial.specularColor = new BABYLON.Color3(0, 0, 0);
    skybox.material = skyboxMaterial;*/


    /******* Loading assets ******/
        //TODO: spustat assets loading podla choice
    var assetsManager = new BABYLON.AssetsManager(scene);

    var treeTask = assetsManager.addMeshTask("", "", "meshes/three/", "Tree.obj");

    var roadTask = assetsManager.addMeshTask("", "", "meshes/road/", "road.babylon");
    var botTask = assetsManager.addMeshTask("", "", "meshes/bus/", "bus.babylon");
    if (choice == 'car') {
        var carTask = assetsManager.addMeshTask("", "", "meshes/car/", "dong-feng2.babylon");
    }
    //added truck
    if (choice == 'truck'){
        var truckTask = assetsManager.addMeshTask("", "", "meshes/truck/", "unimog_new17.babylon");
        var carTask = assetsManager.addMeshTask("", "", "meshes/car/", "dong-feng2.babylon");
    }
    //added bus
    if (choice == 'bus')
        var busTask = assetsManager.addMeshTask("", "", "meshes/bus/", "bus5.babylon");


    roadTask.onSuccess = function (task) {
        task.loadedMeshes.forEach(function (mesh) {
            meshes[mesh.id] = mesh;
        });
        isRoadReady = true;
        isAllMeshReady();
    };

    botTask.onSuccess = function (task) {
        task.loadedMeshes.forEach(function (mesh) {
            botsMeshes.Bus[mesh.id] = mesh;
        });
        isBotReady = true;
        isAllMeshReady();
    };


    if (choice == 'car') {
        carTask.onSuccess = function (task) {
            task.loadedMeshes.forEach(function (mesh) {
                meshes[mesh.id] = mesh;
            });
            isCarReady = true;
            isAllMeshReady();
        };
    }
    treeTask.onSuccess = function (task) {
        task.loadedMeshes.forEach(function (mesh) {
            meshes[mesh.id] = mesh;
        });
        isTreeReady = true;
        isAllMeshReady();
    };

    if (choice == 'truck') {
        //added truck
        carTask.onSuccess = function (task) {
            task.loadedMeshes.forEach(function (mesh) {
                meshes[mesh.id] = mesh;
            });
        }
        truckTask.onSuccess = function (task) {
            task.loadedMeshes.forEach(function (mesh) {
                meshes[mesh.id] = mesh;
            });
            isCarReady = true;
            isAllMeshReady();
        }

    }

    if (choice == 'bus') {
        //added bus
        busTask.onSuccess = function (task) {
            task.loadedMeshes.forEach(function (mesh) {
                meshes[mesh.id] = mesh;
            });
            isCarReady = true;
            isAllMeshReady();
        };
    }

    assetsManager.load();

//updated funnction with new truck
    function isAllMeshReady() {
        if (isRoadReady && isBotReady && isCarReady && isTreeReady) {
            renderScene();
        }
    }

    function renderScene() {


        /******* Ground ******/

        CreateGround(scene);

        /******* Landscape ******/

        var backgroundMeshes = CreateLandscape(scene, groundSizeWidth, groundSizeHeight);

        /******* Road ******/

        var roadWidth = 1000;   // width of road
        var roadHeight = 300;   // height of road

        CreateRoadAndCarStation(meshes, scene);

        /*
                var roadHigh = -1.3;

                var straightRoadRight = meshes["straight_road"];
                var straightRoadLeft = straightRoadRight.createInstance();

                straightRoadRight.position = new BABYLON.Vector3(0, roadHigh, 285);
                straightRoadLeft.position = new BABYLON.Vector3(0, roadHigh, -285);

                var roundRoadUp = meshes["round_road"];
                var roundRoadDown = roundRoadUp.createInstance();

                roundRoadUp.position = new BABYLON.Vector3(-1285, roadHigh, 0);
                roundRoadDown.rotation.y = Math.PI;
                roundRoadDown.position = new BABYLON.Vector3(1285, roadHigh, 0);


                /******* Car Station ******/

        /*   var carStationMat = new BABYLON.StandardMaterial("car_station", scene);
           carStationMat.diffuseTexture = new BABYLON.Texture("textures/road/car-station.jpg", scene);

           var carStation = BABYLON.MeshBuilder.CreateGround("car_station", {width: 45, height: 15}, scene);
           carStation.position = new BABYLON.Vector3(-10, roadHigh, 322.5);
           carStation.material = carStationMat; */

        //added temporary truck station
        /*var truckStationMat = new BABYLON.StandardMaterial("car_station", scene);
        truckStationMat.diffuseTexture = new BABYLON.Texture("textures/road/car-station.jpg", scene);

        var truckStation = BABYLON.MeshBuilder.CreateGround("car_station", {width: 45, height: 15}, scene);
        truckStation.position = new BABYLON.Vector3(-55, roadHigh, 322.5);
        truckStation.material = truckStationMat;*/

        //added temporary bus station
        /*var busStationMat = new BABYLON.StandardMaterial("car_station", scene);
        busStationMat.diffuseTexture = new BABYLON.Texture("textures/road/car-station.jpg", scene);

        var busStation = BABYLON.MeshBuilder.CreateGround("car_station", {width: 45, height: 15}, scene);
        busStation.position = new BABYLON.Vector3(-95, roadHigh, 322.5);
        busStation.material = busStationMat;*/

        /******* Trees ******/

        CreateThrees(scene, meshes);


        /******* Bot - mesh ******/

            //tmp set number of bots
        var botsStruct = new BotsStruct();
        botsStruct.Bus = botsStartPosition.length;


        var autobusy = new Bots();

        autobusy.roadWidth = roadWidth;
        autobusy.roadHeight = roadHeight;

        autobusy.CreateBots(botsMeshes, botsStruct);

        /******* Car ******/

        if (choice == 'car') {

            // Car Body
            var carBody = meshes["car"];

            camera.parent = carBody;
            camera2.parent = carBody;

            // Steering Wheel
            var pivotSW = new BABYLON.Mesh("pivotSW", scene);
            pivotSW.parent = carBody;
            pivotSW.rotate(BABYLON.Axis.Z, Math.PI / 12, BABYLON.Space.LOCAL);
            pivotSW.position = new BABYLON.Vector3(-6, 2.3, -1.5);

            var steering_wheel = meshes["steering_wheel"];
            steering_wheel.parent = pivotSW;

            // Wheels
            var wheelFL = meshes["pneu_left"];
            var wheelFR = meshes["pneu_right"];

            wheelFL.rotate(BABYLON.Axis.X, Math.PI / 2, BABYLON.Space.WORLD);
            wheelFR.rotate(BABYLON.Axis.X, Math.PI / 2, BABYLON.Space.WORLD);

            var pivotFL = new BABYLON.Mesh("pivotFL", scene);
            pivotFL.parent = carBody;
            pivotFL.position = new BABYLON.Vector3(-9.4, 0, -2.8);

            var pivotFR = new BABYLON.Mesh("pivotFR", scene);
            pivotFR.parent = carBody;
            pivotFR.position = new BABYLON.Vector3(-9.4, 0, 2.8);

            wheelFR.parent = pivotFR;
            wheelFR.position = new BABYLON.Vector3(0, 0, 0);

            var wheelRL = wheelFL.createInstance("wheelRL");
            wheelRL.parent = carBody;
            wheelRL.position = new BABYLON.Vector3(0, 0, -2.8);

            var wheelRR = wheelFR.createInstance("wheelRR");
            wheelRR.parent = carBody;
            wheelRR.position = new BABYLON.Vector3(0, 0, 2.8);

            wheelFL.parent = pivotFL;
            wheelFL.position = new BABYLON.Vector3(0, 0, 0);

            /*********   Sensors   **********/

            var senzoryHolder = [zadVpravo, predVpravo, predVlavo, zadVlavo];
            var senzoryFront = [rovnoVlavo, rovnoVpravo, rovnoZLava, rovnoZPrava];
            var senzoryBack = [doZaduVlavo, doZaduVpravo];
            var senzoryGround = [doleZadVpravo, doleZadVlavo, dolePredVlavo, dolePredVpravo];

            function createRayCastSensor(scene, carBody, localMeshDirection, localMeshOrigin, name) {
                var ray = new BABYLON.Ray();
                var rayHelper = new BABYLON.RayHelper(ray);
                var length = 5;
                ray.name = name;

                rayHelper.attachToMesh(carBody, localMeshDirection, localMeshOrigin, length);
                rayHelper.show(scene, new BABYLON.Color3(1, 0, 0));
                rayHelper._renderLine.alwaysSelectAsActiveMesh = true

                return ray;
            }

            function createAllSensors(senzoryHolder, scene, carBody) {
                var sensors = [];

                senzoryHolder.forEach(function (element) {
                    sensors.push(createRayCastSensor(scene, carBody, element.direction, element.origin, element.name));
                });

                return sensors;
            }

            var carSensors = new Sensors(createAllSensors(senzoryGround, scene, carBody),
                createAllSensors(senzoryFront, scene, carBody),
                createAllSensors(senzoryBack, scene, carBody),
                createAllSensors(senzoryHolder, scene, carBody)
            );

            // Car centre of rotation
            var pivot = new BABYLON.Mesh("pivot", scene); //current centre of rotation
            pivot.position = new BABYLON.Vector3(carStartPosition.x, 0, carStartPosition.z + 50);
            pivot.rotate(BABYLON.Axis.Y, carStartPosition.r, BABYLON.Space.WORLD);
            carBody.parent = pivot;
            carBody.position = new BABYLON.Vector3(0, 0, -50);
        }
        if (choice == 'truck') {


            /******** Truck **********/
                //truck body
            var carBody = meshes["body"]
            //truck cam
            camera.parent = carBody;
            camera2.parent = carBody;

            // Steering Wheel
            var pivotSW = new BABYLON.Mesh("pivotSW", scene);
            pivotSW.parent = carBody;
            pivotSW.rotate(BABYLON.Axis.Z, Math.PI / 12, BABYLON.Space.LOCAL);
            pivotSW.position = new BABYLON.Vector3(-1, 0.6, 0.75);
            pivotSW.scaling = new BABYLON.Vector3(0.25, 0.25, 0.25)

            var steering_wheel = meshes["steering_wheel"];
            steering_wheel.parent = pivotSW;

            //truck wheels
            var wheelFL = meshes["wheelFL"];
            var wheelFR = meshes["wheelFR"];

            wheelFL.rotate(BABYLON.Axis.X, Math.PI / 0.5, BABYLON.Space.WORLD);
            wheelFR.rotate(BABYLON.Axis.X, Math.PI / 0.5, BABYLON.Space.WORLD);

            var pivotFL = new BABYLON.Mesh("pivotFL", scene);
            pivotFL.parent = carBody;
            pivotFL.position = new BABYLON.Vector3(-1.725, 0.475, -0.95);

            var pivotFR = new BABYLON.Mesh("pivotFR", scene);
            pivotFR.parent = carBody;
            pivotFR.position = new BABYLON.Vector3(-1.725, 0.475, 0.95);

            wheelFR.parent = pivotFR;
            wheelFR.position = new BABYLON.Vector3(0, 0, 0);
            wheelFL.scaling = new BABYLON.Vector3(1, 0.75, 0.75)
            wheelFR.scaling = new BABYLON.Vector3(1, 0.75, 0.75)

            wheelFL.parent = pivotFL;
            wheelFL.position = new BABYLON.Vector3(0, 0, 0);

            var wheelRL = meshes["wheelRL"]
            var wheelRR = meshes["wheelRR"]

            wheelRL.rotate(BABYLON.Axis.X, Math.PI / 0.5, BABYLON.Space.WORLD);
            wheelRR.rotate(BABYLON.Axis.X, Math.PI / 0.5, BABYLON.Space.WORLD);

            var pivotRL = new BABYLON.Mesh("pivotRL", scene)
            pivotRL.parent = carBody;
            pivotRL.position = new BABYLON.Vector3(1.725, 0.475, -0.95);

            var pivotRR = new BABYLON.Mesh("pivotRR", scene)
            pivotRR.parent = carBody;
            pivotRR.position = new BABYLON.Vector3(1.725, 0.475, 0.95);

            wheelRL.parent = pivotRL;
            wheelRL.position = new BABYLON.Vector3(0, 0, 0);

            wheelRR.parent = pivotRR
            wheelRR.position = new BABYLON.Vector3(0, 0, 0);

            wheelRL.scaling = new BABYLON.Vector3(1, 0.75, 0.75)
            wheelRR.scaling = new BABYLON.Vector3(1, 0.75, 0.75)


            carBody.position = new BABYLON.Vector3(-55, -1, 322.5)

            /*********   Sensors   **********/

            var senzoryHolder = [zadVpravo, predVpravo, predVlavo, zadVlavo];
            var senzoryFront = [rovnoVlavo, rovnoVpravo, rovnoZLava, rovnoZPrava];
            var senzoryBack = [doZaduVlavo, doZaduVpravo];
            var senzoryGround = [doleZadVpravo, doleZadVlavo, dolePredVlavo, dolePredVpravo];

            function createRayCastSensor(scene, carBody, localMeshDirection, localMeshOrigin, name) {
                var ray = new BABYLON.Ray();
                var rayHelper = new BABYLON.RayHelper(ray);
                var length = 5;
                ray.name = name;

                rayHelper.attachToMesh(carBody, localMeshDirection, localMeshOrigin, length);
                rayHelper.show(scene, new BABYLON.Color3(1, 0, 0));
                rayHelper._renderLine.alwaysSelectAsActiveMesh = true

                return ray;
            }

            function createAllSensors(senzoryHolder, scene, carBody) {
                var sensors = [];

                senzoryHolder.forEach(function (element) {
                    sensors.push(createRayCastSensor(scene, carBody, element.direction, element.origin, element.name));
                });

                return sensors;
            }

            var carSensors = new Sensors(createAllSensors(senzoryGround, scene, carBody),
                createAllSensors(senzoryFront, scene, carBody),
                createAllSensors(senzoryBack, scene, carBody),
                createAllSensors(senzoryHolder, scene, carBody)
            );


            var pivot = new BABYLON.Mesh("pivot", scene); //current centre of rotation
            pivot.position = new BABYLON.Vector3(carStartPosition.x, 0, carStartPosition.z + 50);
            pivot.rotate(BABYLON.Axis.Y, carStartPosition.r, BABYLON.Space.WORLD);
            carBody.parent = pivot;
            carBody.position = new BABYLON.Vector3(0, -1, -50);

            //TODO: doplnit pivota, doplnit steering wheel, tocenie kolies, senzory

        }
        /******* Bus *******/

        if (choice == 'bus') {
            //TODO: komplet loading busu ako auta aj so senzormi
            var carBody = meshes["body"]

            camera.parent = carBody
            camera2.parent = carBody

            // Steering Wheel
            var pivotSW = new BABYLON.Mesh("pivotSW", scene);
            pivotSW.parent = carBody;
            pivotSW.rotate(BABYLON.Axis.Z, Math.PI / 12, BABYLON.Space.LOCAL);
            pivotSW.position = new BABYLON.Vector3(-20, 3, -3);

            var steering_wheel = meshes["steering_wheel"];
            steering_wheel.parent = pivotSW;

            var wheelFL = meshes["wheelRight"];
            var wheelFR = meshes["wheelLeft"];

            wheelFL.rotate(BABYLON.Axis.X, Math.PI / 2, BABYLON.Space.WORLD);
            wheelFR.rotate(BABYLON.Axis.X, Math.PI / 2, BABYLON.Space.WORLD);

            var pivotFL = new BABYLON.Mesh("pivotFL", scene);
            pivotFL.parent = carBody;
            pivotFL.position = new BABYLON.Vector3(-12.2, 0, -5);

            var pivotFR = new BABYLON.Mesh("pivotFR", scene);
            pivotFR.parent = carBody;
            pivotFR.position = new BABYLON.Vector3(-12.2, 0, 4);

            wheelFR.parent = pivotFR;
            wheelFR.position = new BABYLON.Vector3(0, 0, 0);
            wheelFL.parent = pivotFL;
            wheelFL.position = new BABYLON.Vector3(0, 0, 0);

            var wheelRL = wheelFL.createInstance();
            wheelRL.parent = carBody;
            wheelRL.position = new BABYLON.Vector3(12.2, 0, -5);

            var wheelRR = wheelFR.createInstance();
            wheelRR.parent = carBody;
            wheelRR.position = new BABYLON.Vector3(12.2, 0, 4);

            /*********   Sensors   **********/

            var senzoryHolder = [zadVpravo, predVpravo, predVlavo, zadVlavo];
            var senzoryFront = [rovnoVlavo, rovnoVpravo, rovnoZLava, rovnoZPrava];
            var senzoryBack = [doZaduVlavo, doZaduVpravo];
            var senzoryGround = [doleZadVpravo, doleZadVlavo, dolePredVlavo, dolePredVpravo];

            function createRayCastSensor(scene, carBody, localMeshDirection, localMeshOrigin, name) {
                var ray = new BABYLON.Ray();
                var rayHelper = new BABYLON.RayHelper(ray);
                var length = 5;
                ray.name = name;

                rayHelper.attachToMesh(carBody, localMeshDirection, localMeshOrigin, length);
                rayHelper.show(scene, new BABYLON.Color3(1, 0, 0));
                rayHelper._renderLine.alwaysSelectAsActiveMesh = true

                return ray;
            }

            function createAllSensors(senzoryHolder, scene, carBody) {
                var sensors = [];

                senzoryHolder.forEach(function (element) {
                    sensors.push(createRayCastSensor(scene, carBody, element.direction, element.origin, element.name));
                });

                return sensors;
            }

            var carSensors = new Sensors(createAllSensors(senzoryGround, scene, carBody),
                createAllSensors(senzoryFront, scene, carBody),
                createAllSensors(senzoryBack, scene, carBody),
                createAllSensors(senzoryHolder, scene, carBody)
            );


            // Car centre of rotation
            var pivot = new BABYLON.Mesh("pivot", scene); //current centre of rotation
            pivot.position = new BABYLON.Vector3(carStartPosition.x, 0, carStartPosition.z + 50);
            pivot.rotate(BABYLON.Axis.Y, carStartPosition.r, BABYLON.Space.WORLD);
            carBody.parent = pivot;
            carBody.position = new BABYLON.Vector3(0, 0, -50);
        }
        /******* Keyboard control ******/

        var map = {};
        scene.actionManager = new BABYLON.ActionManager(scene);

        scene.actionManager.registerAction(new BABYLON.ExecuteCodeAction(BABYLON.ActionManager.OnKeyDownTrigger, function (e) {
            map[e.sourceEvent.key] = e.sourceEvent.type === "keydown";
        }));

        scene.actionManager.registerAction(new BABYLON.ExecuteCodeAction(BABYLON.ActionManager.OnKeyUpTrigger, function (e) {
            map[e.sourceEvent.key] = e.sourceEvent.type === "keydown";

            if (e.sourceEvent.key === "v" || e.sourceEvent.key === "V") {
                if (switchCam) {
                    scene.activeCamera = camera2;
                } else {
                    scene.activeCamera = camera;
                }
                switchCam = !switchCam;
            }
            if (e.sourceEvent.key === "u" || e.sourceEvent.key === "U") {
                saveCoord();
            }
        }));


        /******* Text at the top of screen ******/

        const stackPanel = CreateUI();
        /*

                var advancedTexture = BABYLON.GUI.AdvancedDynamicTexture.CreateFullscreenUI("UI");

                var stackPanel = new BABYLON.GUI.StackPanel();
                stackPanel.background = new BABYLON.Color3(0, 0, 0);
                stackPanel.width = "100%";
                stackPanel.height = "50px";
                stackPanel.isVertical = false;
                stackPanel.alpha = 0.8;
                stackPanel.verticalAlignment = BABYLON.GUI.Control.VERTICAL_ALIGNMENT_TOP;
                advancedTexture.addControl(stackPanel);

                var fpsText = new BABYLON.GUI.TextBlock();
                fpsText.width = "200px";
                fpsText.color = "white";
                fpsText.fontSize = 20;
                stackPanel.addControl(fpsText);

                var speedCarText = new BABYLON.GUI.TextBlock();
                speedCarText.width = "200px";
                speedCarText.color = "white";
                speedCarText.fontSize = 20;
                speedCarText.horizontalAlignment = 1;
                stackPanel.addControl(speedCarText);*/


        /******* Variables and function for animation ******/

        var theta = 0;                      // rotation angle
        var turnTheta = Math.PI / 252;     // wheel turning per each frame
        var swTheta = Math.PI / 19;         // steering wheel turning per each frame

        var D = 0;      // distance translated per frame
        var R = 50;
        if(choice=='truck')
            R = 50// turning radius
        var NR;         // new turning radius
        var A = 5.6;    // distance between front and rear tyre
        var L = 9.4;    // distance between each tyre
        var r = 1.5;    // wheel radius
        if(choice=='truck'){
            A = 1.9
            L=3.45
        }
        var wheelRotation;  // wheel rotation
        var carRotation;    // car rotation when turning

        var F;          // frames per second

        var maxForwardSpeed = 80;       // maximal speed of car
        var maxBrakeSpeed = maxForwardSpeed / 50;   // maximal brake speed depending of maximal speed car

        var turnBorder = Math.PI / 6;   // maximal turning radius of front wheels


        function turnCar(turnTheta, swTheta) {
            theta += turnTheta;
            pivotFL.rotate(BABYLON.Axis.Y, turnTheta, BABYLON.Space.LOCAL);
            pivotFR.rotate(BABYLON.Axis.Y, turnTheta, BABYLON.Space.LOCAL);
            pivotSW.rotate(BABYLON.Axis.X, turnTheta + swTheta, BABYLON.Space.LOCAL);


            if (Math.abs(theta) > 0.00000001) {
                NR = A / 2 + L / Math.tan(theta);
                if(choice=='truck')
                NR = (A / 2 + L / Math.tan(theta))*2 ;

            } else {
                theta = 0;
                NR = 0;
            }
            if(choice=='truck'){
                if(theta>0) {
                    pivot.translate(BABYLON.Axis.Z, (NR - R) / 10000, BABYLON.Space.LOCAL);
                    carBody.translate(BABYLON.Axis.Z, (R - NR) / 10000, BABYLON.Space.LOCAL);
                }
                if(theta<0){
                    pivot.translate(BABYLON.Axis.Z, (NR - R) / 10000, BABYLON.Space.LOCAL);
                    carBody.translate(BABYLON.Axis.Z, (R - NR) / 10000, BABYLON.Space.LOCAL);
                }
            }else {
                pivot.translate(BABYLON.Axis.Z, NR - R, BABYLON.Space.LOCAL);
                carBody.translate(BABYLON.Axis.Z, R - NR, BABYLON.Space.LOCAL);
            }
            R = NR;
        }


        /******* Animation ******/
        var counter = 0;

        scene.registerBeforeRender(function () {
            F = engine.getFps();

            if (stopRender) {
                clearInterval(timer);
                engine.stopRenderLoop();
            }

            // rotate skybox
            skybox.rotate(BABYLON.Axis.Y, 0.0001, BABYLON.Space.LOCAL);

            // start moving bots on scene
            if (autobusy.busVehicles) {
                for (var i = 0; i < autobusy.busVehicles.length; i++) {
                    autobusy.startBusMoving(i);
                }
            }

            // check if car is in scene
            for (var i = 0; i < backgroundMeshes.length; i++) {
                if (carBody.intersectsMesh(backgroundMeshes[i], false)) {
                    D = -D;
                    break;
                }
            }

            // checking gamepad event
            updateGamepad(navigator.getGamepads()[0], D);

            if (gamepadApi.type === "joystick" || gamepadApi.type === "wheel") {
                if (gamepadApi.type === "joystick") {
                    rightVector = gamepadApi.axes[0] * turnBorder;
                    forwardVector = gamepadApi.axes[1] * maxForwardSpeed;
                    brake = gamepadApi.axes[2] * maxBrakeSpeed;
                    gear = gamepadApi.gear;

                } else if (gamepadApi.type === "wheel") {
                    rightVector = (parseFloat(gamepadApi.axes[0].toFixed(2))) * turnBorder;
                    forwardVector = (parseFloat(gamepadApi.axes[1].toFixed(2))) * maxForwardSpeed;
                    brake = (parseFloat(gamepadApi.axes[2].toFixed(2))) * maxBrakeSpeed;
                    gear = gamepadApi.gear;
                }

                if (gear === -1) {
                    forwardVector /= 4;
                }

                if (forwardVector > 0 && D * gear < forwardVector) {
                    D += gear;
                }

                if (brake > 0 && (Math.abs(D) - brake) >= 0) {
                    if (D >= 0) {
                        D -= brake;
                    } else {
                        D += brake;
                    }
                }

                if (rightVector < 0.0 && rightVector < theta) {
                    turnCar(-turnTheta, -swTheta);
                }

                if (rightVector <= 0.0 && rightVector > theta) {
                    turnCar(turnTheta, swTheta);
                }

                if (rightVector > 0.0 && rightVector > theta) {
                    turnCar(turnTheta, swTheta);
                }

                if (rightVector >= 0.0 && rightVector < theta) {
                    turnCar(-turnTheta, -swTheta);
                }

            } else {
                if ((map["w"] || map["W"]) && D < maxForwardSpeed) {
                    D += 1;
                }

                if ((map["s"] || map["S"]) && D > -maxForwardSpeed / 4) {
                    D -= 1;
                }

                if ((map["a"] || map["A"]) && -Math.PI / 6 < theta) {
                    turnCar(-turnTheta, -swTheta);
                }

                if ((map["d"] || map["D"]) && theta < Math.PI / 6) {
                    turnCar(turnTheta, swTheta);
                }

                if (!(map["a"] || map["A"]) && !(map["d"] || map["D"]) && theta !== 0) {
                    if (theta > 0) {
                        turnCar(-turnTheta, -swTheta);
                    } else if (theta < 0) {
                        turnCar(turnTheta, swTheta);
                    }
                }
            }

            if (!(map["w"] || map["W"]) && !(map["s"] || map["S"]) && D !== 0) {
                if (D > 0.05)
                    D -= 0.05;
                else if (D < -0.05)
                    D += 0.05;
                else
                    D = 0;
            }

            var distance = D / F;
            wheelRotation = D / (r * F);
            carRotation = D / (R * F);

            if (theta < 0 || theta > 0) {
                if(choice == 'truck'){
                    if(theta>0)
                    pivot.rotate(BABYLON.Axis.Y, carRotation, BABYLON.Space.WORLD);
                    else
                        pivot.rotate(BABYLON.Axis.Y, carRotation, BABYLON.Space.WORLD);
                    wheelFL.rotate(BABYLON.Axis.X, wheelRotation, BABYLON.Space.LOCAL);
                    wheelFR.rotate(BABYLON.Axis.X, wheelRotation, BABYLON.Space.LOCAL);
                    //wheelRL.rotate(BABYLON.Axis.Y, wheelRotation, BABYLON.Space.LOCAL);
                    //wheelRR.rotate(BABYLON.Axis.Y, wheelRotation, BABYLON.Space.LOCAL);
                }
                else{
                    pivot.rotate(BABYLON.Axis.Y, carRotation, BABYLON.Space.WORLD);
                    wheelFL.rotate(BABYLON.Axis.Y, wheelRotation, BABYLON.Space.LOCAL);
                    wheelFR.rotate(BABYLON.Axis.Y, wheelRotation, BABYLON.Space.LOCAL);
                    wheelRL.rotate(BABYLON.Axis.Y, wheelRotation, BABYLON.Space.LOCAL);
                    wheelRR.rotate(BABYLON.Axis.Y, wheelRotation, BABYLON.Space.LOCAL);
                }

            } else {

                pivot.translate(BABYLON.Axis.X, -distance, BABYLON.Space.LOCAL);

                if(choice == 'truck'){
                    wheelFL.rotate(BABYLON.Axis.X, -wheelRotation, BABYLON.Space.LOCAL);
                    wheelFR.rotate(BABYLON.Axis.X, -wheelRotation, BABYLON.Space.LOCAL);
                    wheelRL.rotate(BABYLON.Axis.X, -wheelRotation, BABYLON.Space.LOCAL);
                    wheelRR.rotate(BABYLON.Axis.X, -wheelRotation, BABYLON.Space.LOCAL);
                }
                else{
                    wheelFL.rotate(BABYLON.Axis.Y, wheelRotation, BABYLON.Space.LOCAL);
                    wheelFR.rotate(BABYLON.Axis.Y, wheelRotation, BABYLON.Space.LOCAL);
                    wheelRL.rotate(BABYLON.Axis.Y, wheelRotation, BABYLON.Space.LOCAL);
                    wheelRR.rotate(BABYLON.Axis.Y, wheelRotation, BABYLON.Space.LOCAL);
                }
            }
            if ((counter % 10) === 0) {

                carSensors.setDistanceFrontSensors(D);

                carSensors.sideSensors.forEach(function (element) {
                    const object = scene.pickWithRay(element);
                    if (object.hit) {
                        console.log(element.name + " zaznamenal " + object.pickedMesh.name);
                        D = 0; //pridanie zastavenia po naraze
                    }
                });

                carSensors.frontSensors.forEach(function (element) {
                    const object = scene.pickWithRay(element);
                    if (object.hit) {
                        console.log(element.name + " zaznamenal " + object.pickedMesh.name);
                        D = 0; //pridanie zastavenia po naraze

                    }
                });

                carSensors.backSensors.forEach(function (element) {
                    const object = scene.pickWithRay(element);
                    if (object.hit) {
                        console.log(element.name + " zaznamenal " + object.pickedMesh.name);
                        D = 0; //pridanie zastavenia po naraze

                    }
                });

                carSensors.groundSensors.forEach(function (element) {
                    const object = scene.pickWithRay(element);
                    /*if (object.pickedMesh.name == null) {
                        console.log(object.pickedMesh.toString());
                    }*/
                    if (object.hit && object.pickedMesh.name.toString() === "ground") {
                        console.log(element.name + " nezaznamenal cestu.");
                    }
                });

                counter = 1;
            } else {
                counter++;
            }

            // Update text info every frame
            stackPanel.fpsText.text = "FPS: " + F.toFixed(0);
            stackPanel.speedCarText.text = "Rýchlosť: " + Math.abs(D).toFixed(0) + " km/h";
        });

        // Set values to graphs
        var time = 0;

        var timer = setInterval(function () {

            var carPosition = wheelRL.getAbsolutePosition();
            var carPositionX = Math.round(carPosition.x * 100) / 100;
            var carPositionZ = (Math.round(carPosition.z * 100) / 100) + 2.8;

            traceCarPosition(carPositionX, carPositionZ);
            traceCarSpeed(D, time);
            traceBotsPosition(autobusy.busVehicles);
            time += 200;

        }, 200);

    }

    return scene;
};

var scene = createScene();

engine.runRenderLoop(function () {
    scene.render();
});

window.addEventListener("resize", function () {
    engine.resize();
});