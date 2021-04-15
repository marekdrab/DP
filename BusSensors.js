//origin posledne cislo je poloha od auta
//local vektor ak chcem flipnut na opacnu stranu tak posledne cislo na -

var zadVpravoBus = {
    direction: new BABYLON.Vector3(0, 0, 1),
    origin : new BABYLON.Vector3(3, 1, 3),
    name : "zadVpravo"
}

var predVpravoBus = {
    direction: new BABYLON.Vector3(0, 0, 1),
    origin : new BABYLON.Vector3(-11, 1, 3),
    name : "predVpravo"
}

var predVlavoBus = {
    direction: new BABYLON.Vector3(0, 0, -1),
    origin : new BABYLON.Vector3(-11, 1, -3),
    name : "predVlavo"
}

var zadVlavoBus = {
    direction: new BABYLON.Vector3(0, 0, -1),
    origin : new BABYLON.Vector3(3, 1, -3),
    name : "zadVpravo"
}

var rovnoVlavoBus = {
    direction: new BABYLON.Vector3(-3.5, 0, 0),
    origin : new BABYLON.Vector3(-12, 1, -2),
    name : "rovnoVlavo"
}

var rovnoVpravoBus = {
    direction: new BABYLON.Vector3(-3.5, 0, 0),
    origin : new BABYLON.Vector3(-12, 1, 2),
    name : "rovnoVpravo"
}

var rovnoZLavaBus = {
    direction: new BABYLON.Vector3(-3.5, 0, 0.7),
    origin : new BABYLON.Vector3(-12, 1, -2),
    name : "rovnoZLava"
}

var rovnoZPravaBus = {
    direction: new BABYLON.Vector3(-3.5, 0, -0.7),
    origin : new BABYLON.Vector3(-12, 1, 2),
    name : "rovnoZPrava"
}

var doZaduVlavoBus = {
    direction: new BABYLON.Vector3(1, 0, 0),
    origin : new BABYLON.Vector3(4.2, 1, 2),
    name : "doZaduVlavo"
}

var doZaduVpravoBus = {
    direction: new BABYLON.Vector3(1, 0, 0),
    origin : new BABYLON.Vector3(4.2, 1, -2),
    name : "doZaduVpravo"
}

var doleZadVpravoBus = {
    direction: new BABYLON.Vector3(0, -1, -1),
    origin : new BABYLON.Vector3(3, 1, -3),
    name : "doleZadVpravo"
}

var doleZadVlavoBus = {
    direction: new BABYLON.Vector3(0, -1, 1),
    origin : new BABYLON.Vector3(3, 1, 3),
    name : "doleZadVlavo"
}

var dolePredVpravoBus = {
    direction: new BABYLON.Vector3(0, -1, 1),
    origin : new BABYLON.Vector3(-11, 1, 3),
    name : "dolePredVpravo"
}

var dolePredVlavoBus = {
    direction: new BABYLON.Vector3(0, -1, -1),
    origin : new BABYLON.Vector3(-11, 1, -3),
    name : "dolePredVlavo"
}