const groundSizeWidth = 3000;
const groundSizeHeight = 1000;

function CreateThrees(scene, meshes) {

    var treeOrigo = meshes["g2"];
    var treeOrigo2 = meshes["g1"];


    for (var i = 0; i < 200; i++) {
        var tree = treeOrigo.createInstance();
        var tree2 = treeOrigo2.createInstance();
        var position = new BABYLON.Vector3(Math.random() * 2001 - 1000, 0, Math.random() * 501 - 250);
        tree.position = position;
        tree2.position = position;
        tree.name = "strom";
        tree2.name = "strom";
        tree.scaling = new BABYLON.Vector3(4, 4, 4);
        tree2.scaling = new BABYLON.Vector3(4, 4, 4);
    }

    for (var i = 0; i < 50; i++) {
        var tree = treeOrigo.createInstance();
        var tree2 = treeOrigo2.createInstance();
        var position = new BABYLON.Vector3(Math.random() * 2001 - 1000, 0, Math.random() * 151 + 340);
        tree.position = position;
        tree2.position = position;
        tree.name = "strom";
        tree2.name = "strom";
        tree.scaling = new BABYLON.Vector3(4, 4, 4);
        tree2.scaling = new BABYLON.Vector3(4, 4, 4);
    }

    for (var i = 0; i < 50; i++) {
        var tree = treeOrigo.createInstance();
        var tree2 = treeOrigo2.createInstance();
        var position = new BABYLON.Vector3(Math.random() * 2001 - 1000, 0, Math.random() * -151 + -320);
        tree.position = position;
        tree2.position = position;
        tree.name = "strom";
        tree2.name = "strom";
        tree.scaling = new BABYLON.Vector3(4, 4, 4);
        tree2.scaling = new BABYLON.Vector3(4, 4, 4);
    }
}

function CreateRoadAndCarStation(meshes, scene) {
    var roadHigh = -1.3;

    var straightRoadRight = meshes["straight_road"];
    var straightRoadLeft = straightRoadRight.createInstance();

    straightRoadLeft.name = "straight_road";
    straightRoadRight.position = new BABYLON.Vector3(0, roadHigh, 285);
    straightRoadLeft.position = new BABYLON.Vector3(0, roadHigh, -285);

    var roundRoadUp = meshes["round_road"];
    var roundRoadDown = roundRoadUp.createInstance();

    roundRoadDown.name = "round_road";
    roundRoadUp.position = new BABYLON.Vector3(-1285, roadHigh, 0);
    roundRoadDown.rotation.y = Math.PI;
    roundRoadDown.position = new BABYLON.Vector3(1285, roadHigh, 0);


    /******* Car Station ******/


    var carStationMat = new BABYLON.StandardMaterial("car_station", scene);
    carStationMat.diffuseTexture = new BABYLON.Texture("textures/road/car-station.jpg", scene);

    var carStation = BABYLON.MeshBuilder.CreateGround("car_station", {width: 60, height: 20}, scene);
    carStation.position = new BABYLON.Vector3(-10, roadHigh, 325);
    carStation.material = carStationMat;
}

function CreateSkyBox(scene) {
    var skybox = BABYLON.MeshBuilder.CreateBox("skyBox", {size: 5000.0}, scene);
    var skyboxMaterial = new BABYLON.StandardMaterial("skyBox", scene);
    skyboxMaterial.backFaceCulling = false;
    skyboxMaterial.disableLighting = true;
    skyboxMaterial.reflectionTexture = new BABYLON.CubeTexture("textures/skybox/bluecloud", scene);
    skyboxMaterial.reflectionTexture.coordinatesMode = BABYLON.Texture.SKYBOX_MODE;
    skyboxMaterial.diffuseColor = new BABYLON.Color3(0, 0, 0);
    skyboxMaterial.specularColor = new BABYLON.Color3(0, 0, 0);
    skybox.material = skyboxMaterial;

    return skybox;
}

function CreateLights(scene) {
    var light1 = new BABYLON.DirectionalLight("light1", new BABYLON.Vector3(1, 2, 0), scene);
    var light2 = new BABYLON.HemisphericLight("light2", new BABYLON.Vector3(0, 1, 0), scene);
    light2.intensity = 0.75;
}

function CreateLandscape(scene) {
    var planeMaterial = new BABYLON.StandardMaterial("bg_wall", scene);
    planeMaterial.diffuseTexture = new BABYLON.Texture("textures/mountain/mountain.png", scene);
    planeMaterial.emissiveTexture = new BABYLON.Texture("textures/mountain/mountain.png", scene);

    planeMaterial.diffuseTexture.hasAlpha = true;
    planeMaterial.emissiveTexture.hasAlpha = true;
    planeMaterial.diffuseTexture.wrapU = BABYLON.Texture.CLAMP_ADDRESSMODE;
    planeMaterial.diffuseTexture.wrapV = BABYLON.Texture.CLAMP_ADDRESSMODE;
    planeMaterial.emissiveTexture.wrapU = BABYLON.Texture.CLAMP_ADDRESSMODE;
    planeMaterial.emissiveTexture.wrapV = BABYLON.Texture.CLAMP_ADDRESSMODE;

    var planeMaterialInverse = new BABYLON.StandardMaterial("bg_wall", scene);
    planeMaterialInverse.diffuseTexture = new BABYLON.Texture("textures/mountain/mountain-inverse.png", scene);
    planeMaterialInverse.emissiveTexture = new BABYLON.Texture("textures/mountain/mountain-inverse.png", scene);

    planeMaterialInverse.diffuseTexture.hasAlpha = true;
    planeMaterialInverse.emissiveTexture.hasAlpha = true;
    planeMaterialInverse.diffuseTexture.wrapU = BABYLON.Texture.CLAMP_ADDRESSMODE;
    planeMaterialInverse.diffuseTexture.wrapV = BABYLON.Texture.CLAMP_ADDRESSMODE;
    planeMaterialInverse.emissiveTexture.wrapU = BABYLON.Texture.CLAMP_ADDRESSMODE;
    planeMaterialInverse.emissiveTexture.wrapV = BABYLON.Texture.CLAMP_ADDRESSMODE;

    var backgroundMeshes = [];

    function createBackgroundView(name, material, width, height, rotation, position) {
        var backgroundPlane = BABYLON.MeshBuilder.CreatePlane(name, {width: width, height: height}, scene);
        backgroundPlane.material = material;
        if (rotation !== 0) {
            backgroundPlane.rotation = rotation;
        }
        backgroundPlane.position = position;
        backgroundMeshes.push(backgroundPlane);
    }

    createBackgroundView("bg_front", planeMaterialInverse, 1000, 200, new BABYLON.Vector3(0, -Math.PI / 2, 0), new BABYLON.Vector3(-groundSizeWidth / 2, 0, 0));
    createBackgroundView("bg_back", planeMaterial, 1000, 200, new BABYLON.Vector3(0, Math.PI / 2, 0), new BABYLON.Vector3(groundSizeWidth / 2, 0, 0));
    createBackgroundView("bg_right_a", planeMaterialInverse, 1500, 200, 0, new BABYLON.Vector3(groundSizeWidth / 4, 0, groundSizeHeight / 2));
    createBackgroundView("bg_right_b", planeMaterial, 1500, 200, 0, new BABYLON.Vector3(-groundSizeWidth / 4, 0, groundSizeHeight / 2));
    createBackgroundView("bg_left_a", planeMaterialInverse, 1500, 200, new BABYLON.Vector3(0, Math.PI, 0), new BABYLON.Vector3(groundSizeWidth / 4, 0, -groundSizeHeight / 2));
    createBackgroundView("bg_left_b", planeMaterial, 1500, 200, new BABYLON.Vector3(0, Math.PI, 0), new BABYLON.Vector3(-groundSizeWidth / 4, 0, -groundSizeHeight / 2));

    var landscape = BABYLON.Mesh.MergeMeshes(backgroundMeshes, true, true, undefined, false, true);
    landscape.position.y = 98;

    return backgroundMeshes;
}

function CreateUI() {
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
    stackPanel.addControl(speedCarText);

    const panelText = {
        speedCarText: speedCarText,
        fpsText: fpsText
    };

    return panelText;
}

function CreateGround(scene) {
    var ground = BABYLON.MeshBuilder.CreateGround("ground", {
        width: groundSizeWidth,
        height: groundSizeHeight
    }, scene);
    var groundMaterial = new BABYLON.StandardMaterial("ground", scene);
    groundMaterial.diffuseColor = new BABYLON.Color3(0.698, 0.624, 0.529);
    ground.material = groundMaterial;
    ground.position = new BABYLON.Vector3(0, -1.4, 0);
}