//origin posledne cislo je poloha od auta
//local vektor ak chcem flipnut na opacnu stranu tak posledne cislo na -
const url = window.location.search;
const urlParams = new URLSearchParams(url);
const choice = urlParams.get('choice');
if(choice=='car'){
var zadVpravo = {
    direction: new BABYLON.Vector3(0, 0, 1),
    origin : new BABYLON.Vector3(3, 1, 3),
    name : "zadVpravo"
}

var predVpravo = {
    direction: new BABYLON.Vector3(0, 0, 1),
    origin : new BABYLON.Vector3(-11, 1, 3),
    name : "predVpravo"
}

var predVlavo = {
    direction: new BABYLON.Vector3(0, 0, -1),
    origin : new BABYLON.Vector3(-11, 1, -3),
    name : "predVlavo"
}

var zadVlavo = {
    direction: new BABYLON.Vector3(0, 0, -1),
    origin : new BABYLON.Vector3(3, 1, -3),
    name : "zadVlavo"
}

var rovnoVlavo = {
    direction: new BABYLON.Vector3(-3.5, 0, 0),
    origin : new BABYLON.Vector3(-12, 1, -2),
    name : "rovnoVlavo"
}

var rovnoVpravo = {
    direction: new BABYLON.Vector3(-3.5, 0, 0),
    origin : new BABYLON.Vector3(-12, 1, 2),
    name : "rovnoVpravo"
}

var rovnoZLava = {
    direction: new BABYLON.Vector3(-3.5, 0, 0.7),
    origin : new BABYLON.Vector3(-12, 1, -2),
    name : "rovnoZLava"
}

var rovnoZPrava = {
    direction: new BABYLON.Vector3(-3.5, 0, -0.7),
    origin : new BABYLON.Vector3(-12, 1, 2),
    name : "rovnoZPrava"
}

var doZaduVlavo = {
    direction: new BABYLON.Vector3(1, 0, 0),
    origin : new BABYLON.Vector3(4.2, 1, 2),
    name : "doZaduVlavo"
}

var doZaduVpravo = {
    direction: new BABYLON.Vector3(1, 0, 0),
    origin : new BABYLON.Vector3(4.2, 1, -2),
    name : "doZaduVpravo"
}

var doleZadVpravo = {
    direction: new BABYLON.Vector3(0, -1, -1),
    origin : new BABYLON.Vector3(3, 1, -3),
    name : "doleZadVpravo"
}

var doleZadVlavo = {
    direction: new BABYLON.Vector3(0, -1, 1),
    origin : new BABYLON.Vector3(3, 1, 3),
    name : "doleZadVlavo"
}

var dolePredVpravo = {
    direction: new BABYLON.Vector3(0, -1, 1),
    origin : new BABYLON.Vector3(-11, 1, 3),
    name : "dolePredVpravo"
}

var dolePredVlavo = {
    direction: new BABYLON.Vector3(0, -1, -1),
    origin : new BABYLON.Vector3(-11, 1, -3),
    name : "dolePredVlavo"
}}
if(choice=='truck'){
    var zadVpravo = {
        direction: new BABYLON.Vector3(0, 0, 1),
        origin : new BABYLON.Vector3(3, 1, 3),
        name : "zadVpravo"
    }

    var predVpravo = {
        direction: new BABYLON.Vector3(0, 0, 1),
        origin : new BABYLON.Vector3(-5, 1, 3),
        name : "predVpravo"
    }

    var predVlavo = {
        direction: new BABYLON.Vector3(0, 0, -1),
        origin : new BABYLON.Vector3(-5, 1, -3),
        name : "predVlavo"
    }

    var zadVlavo = {
        direction: new BABYLON.Vector3(0, 0, -1),
        origin : new BABYLON.Vector3(3, 1, -3),
        name : "zadVlavo"
    }

    var rovnoVlavo = {
        direction: new BABYLON.Vector3(-3.5, 0, 0),
        origin : new BABYLON.Vector3(-6, 1, -2),
        name : "rovnoVlavo"
    }

    var rovnoVpravo = {
        direction: new BABYLON.Vector3(-3.5, 0, 0),
        origin : new BABYLON.Vector3(-6, 1, 2),
        name : "rovnoVpravo"
    }

    var rovnoZLava = {
        direction: new BABYLON.Vector3(-3.5, 0, 0.7),
        origin : new BABYLON.Vector3(-6, 1, -2),
        name : "rovnoZLava"
    }

    var rovnoZPrava = {
        direction: new BABYLON.Vector3(-3.5, 0, -0.7),
        origin : new BABYLON.Vector3(-6, 1, 2),
        name : "rovnoZPrava"
    }

    var doZaduVlavo = {
        direction: new BABYLON.Vector3(1, 0, 0),
        origin : new BABYLON.Vector3(4.2, 1, 2),
        name : "doZaduVlavo"
    }

    var doZaduVpravo = {
        direction: new BABYLON.Vector3(1, 0, 0),
        origin : new BABYLON.Vector3(4.2, 1, -2),
        name : "doZaduVpravo"
    }

    var doleZadVpravo = {
        direction: new BABYLON.Vector3(0, -1, -1),
        origin : new BABYLON.Vector3(3, 1, -3),
        name : "doleZadVpravo"
    }

    var doleZadVlavo = {
        direction: new BABYLON.Vector3(0, -1, 1),
        origin : new BABYLON.Vector3(3, 1, 3),
        name : "doleZadVlavo"
    }

    var dolePredVpravo = {
        direction: new BABYLON.Vector3(0, -1, 1),
        origin : new BABYLON.Vector3(-5, 1, 3),
        name : "dolePredVpravo"
    }

    var dolePredVlavo = {
        direction: new BABYLON.Vector3(0, -1, -1),
        origin : new BABYLON.Vector3(-5, 1, -3),
        name : "dolePredVlavo"
    }
}
if(choice=='bus'){
    var zadVpravo = {
        direction: new BABYLON.Vector3(0, 0, 1),
        origin : new BABYLON.Vector3(24, 1, 5),
        name : "zadVpravo"
    }

    var predVpravo = {
        direction: new BABYLON.Vector3(0, 0, 1),
        origin : new BABYLON.Vector3(-23, 1, 5),
        name : "predVpravo"
    }

    var predVlavo = {
        direction: new BABYLON.Vector3(0, 0, -1),
        origin : new BABYLON.Vector3(-23, 1, -6),
        name : "predVlavo"
    }

    var zadVlavo = {
        direction: new BABYLON.Vector3(0, 0, -1),
        origin : new BABYLON.Vector3(24, 1, -6),
        name : "zadVlavo"
    }

    var rovnoVlavo = {
        direction: new BABYLON.Vector3(-3.5, 0, 0),
        origin : new BABYLON.Vector3(-24, 1, -4),
        name : "rovnoVlavo"
    }

    var rovnoVpravo = {
        direction: new BABYLON.Vector3(-3.5, 0, 0),
        origin : new BABYLON.Vector3(-24, 1, 3),
        name : "rovnoVpravo"
    }

    var rovnoZLava = {
        direction: new BABYLON.Vector3(-3.5, 0, 0.7),
        origin : new BABYLON.Vector3(-24, 1, -4),
        name : "rovnoZLava"
    }

    var rovnoZPrava = {
        direction: new BABYLON.Vector3(-3.5, 0, -0.7),
        origin : new BABYLON.Vector3(-24, 1, 3),
        name : "rovnoZPrava"
    }

    var doZaduVlavo = {
        direction: new BABYLON.Vector3(1, 0, 0),
        origin : new BABYLON.Vector3(25, 1, 3),
        name : "doZaduVlavo"
    }

    var doZaduVpravo = {
        direction: new BABYLON.Vector3(1, 0, 0),
        origin : new BABYLON.Vector3(25, 1, -4),
        name : "doZaduVpravo"
    }

    var doleZadVpravo = {
        direction: new BABYLON.Vector3(0, -1, -1),
        origin : new BABYLON.Vector3(24, 1, -5),
        name : "doleZadVpravo"
    }

    var doleZadVlavo = {
        direction: new BABYLON.Vector3(0, -1, 1),
        origin : new BABYLON.Vector3(24, 1, 5),
        name : "doleZadVlavo"
    }

    var dolePredVpravo = {
        direction: new BABYLON.Vector3(0, -1, 1),
        origin : new BABYLON.Vector3(-23, 1, 5),
        name : "dolePredVpravo"
    }

    var dolePredVlavo = {
        direction: new BABYLON.Vector3(0, -1, -1),
        origin : new BABYLON.Vector3(-23, 1, -6),
        name : "dolePredVlavo"
    }
}