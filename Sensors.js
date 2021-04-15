class Sensors {
    groundSensors = [];
    frontSensors = [];
    backSensors = [];
    sideSensors = [];

    constructor(groundSensors, frontSensors, backSensors, sideSensors) {
        this.frontSensors = frontSensors;
        this.backSensors = backSensors;
        this.groundSensors = groundSensors;
        this.sideSensors = sideSensors;
    }

    setDistanceFrontSensors(distance) {
        this.frontSensors.forEach(function (element) {
           element.length =  (distance / 10) >= 2 ? distance / 10 : 2;
        });
    }

    /*translateSenzors(vector) {
        this.frontSensors.forEach(function (element) {
            element.origin = element.origin.add(vector);
            element.direction = element.direction.add(vector);
        });

        this.backSensors.forEach(function (element) {
            element.origin = element.origin.add(vector);
            element.direction = element.direction.add(vector);
        });

        this.sideSensors.forEach(function (element) {
            element.origin = element.origin.add(vector);
            element.direction = element.direction.add(vector);
        });

        this.groundSensors.forEach(function (element) {
            element.origin = element.origin.add(vector);
            element.direction = element.direction.add(vector);
        });
    }*/
}