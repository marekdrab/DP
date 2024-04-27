class Bots {
    busVehicles = [];
    truckVehicles = [];

    roadWidth = 0;
    roadHeight = 0;

    /**
     * Create bots
     * @param botsMeshes BotsMeshesStruct
     * @param botsStruct BotsStruct
     */
    CreateBots(botsMeshes, botsStruct) {
        //struktura bots bude: Bus : 1 , Car : 2, ...

        if (botsStruct.Bus > 0) {
            // console.log(botsStruct)
            this.createBus(botsMeshes.Bus, botsStruct.Bus)
        }

        if(botsStruct.Truck > 0) {
            //tu si doplni metodu na vytvorenie napr kamionu
        }

    }

    startBusMoving(botIndex) {
        var speedBot
        switch (botIndex) {
            case 0:
                speedBot = localStorage.getItem('dataset-0')/100
                break
            case 1:
                speedBot = localStorage.getItem('dataset-1')/100
                break
            case 2:
                speedBot = localStorage.getItem('dataset-2')/100
                break
            case 3:
                speedBot = localStorage.getItem('dataset-3')/100
                break
            default:
                speedBot = this.busVehicles[botIndex].speedCar
        }

        if (this.busVehicles[botIndex].position.x > -this.roadWidth && this.busVehicles[botIndex].position.x < this.roadWidth && this.busVehicles[botIndex].position.z > 0) {

            if (this.busVehicles[botIndex].busVehiclesCorrection === true) {
                this.busVehicles[botIndex].rotate(BABYLON.Axis.Y, -(this.busVehicles[botIndex].sumAngle + 2 * Math.PI), BABYLON.Space.LOCAL);
                this.busVehicles[botIndex].position.x = this.roadWidth;
                this.busVehicles[botIndex].position.z = this.roadHeight;

                this.busVehicles[botIndex].sumAngle = 0;
                this.busVehicles[botIndex].busVehiclesCorrection = false;
            }


            this.busVehicles[botIndex].translate(BABYLON.Axis.X, -speedBot, BABYLON.Space.LOCAL);
            this.busVehicles[botIndex].position.x = Math.round(this.busVehicles[botIndex].position.x * 100) / 100;
            this.busVehicles[botIndex].position.z = Math.round(this.busVehicles[botIndex].position.z * 100) / 100;

            this.busVehicles[botIndex].turnInterval = 0;

        } else if (this.busVehicles[botIndex].position.x <= -this.roadWidth || this.busVehicles[botIndex].position.x >= this.roadWidth) {
            this.busVehicles[botIndex].busVehiclesCorrection = true;
            const epsilon = 0.01;

            if (this.busVehicles[botIndex].turnInterval % 2 === 0) {
                // console.log('angle', -this.busVehicles[botIndex].turnAngle)
                this.busVehicles[botIndex].rotate(BABYLON.Axis.Y, -this.busVehicles[botIndex].turnAngle, BABYLON.Space.LOCAL);
                this.busVehicles[botIndex].sumAngle += -this.busVehicles[botIndex].turnAngle;
            }
            this.busVehicles[botIndex].translate(BABYLON.Axis.X, -speedBot, BABYLON.Space.LOCAL);

            speedBot = speedBot.toFixed(1);
            var turnInterval = Number((1 - speedBot) + 0.2)
            // turnInterval.toFixed(1)
            // console.log(botIndex, speedBot, turnInterval)

            this.busVehicles[botIndex].speedBotSum += (1 - speedBot) + 0.2;
            this.busVehicles[botIndex].turnInterval += 0.5;
            // this.busVehicles[botIndex].turnInterval += turnInterval;
            // this.busVehicles[botIndex].turnInterval = Number(this.busVehicles[botIndex].turnInterval.toFixed())
            // console.log(botIndex, this.busVehicles[botIndex].turnInterval)

        } else if (this.busVehicles[botIndex].position.x > -this.roadWidth && this.busVehicles[botIndex].position.x < this.roadWidth && this.busVehicles[botIndex].position.z < 0) {
            if (this.busVehicles[botIndex].busVehiclesCorrection === true) {
                this.busVehicles[botIndex].rotate(BABYLON.Axis.Y, -(this.busVehicles[botIndex].sumAngle + Math.PI), BABYLON.Space.LOCAL);
                this.busVehicles[botIndex].position.x = -this.roadWidth;
                this.busVehicles[botIndex].position.z = -this.roadHeight;

                this.busVehicles[botIndex].busVehiclesCorrection = false;
            }

            this.busVehicles[botIndex].translate(BABYLON.Axis.X, -speedBot, BABYLON.Space.LOCAL);
            this.busVehicles[botIndex].position.x = Math.round(this.busVehicles[botIndex].position.x * 100) / 100;
            this.busVehicles[botIndex].position.z = Math.round(this.busVehicles[botIndex].position.z * 100) / 100;

            this.busVehicles[botIndex].turnInterval = 0;
        }

        // console.log(botIndex,speedBot)

        var botWheels = this.busVehicles[botIndex].getChildMeshes();
        for (var i = 0; i < botWheels.length; i++) {
            botWheels[i].rotate(BABYLON.Axis.Y, Math.PI / 2, BABYLON.Space.LOCAL);
        }
    }

    createBus(busMeshes, numberOfBuses) {
        // console.log(numberOfBuses)
        var botBody = busMeshes["bus"];
        var botRightFrontWheel = busMeshes["wheelA"];
        botRightFrontWheel.rotate(BABYLON.Axis.X, Math.PI / 2, BABYLON.Space.WORLD);
        var botRightRareWheel = botRightFrontWheel.createInstance();

        botRightFrontWheel.parent = botBody;
        botRightRareWheel.parent = botBody;

        botRightFrontWheel.position = new BABYLON.Vector3(-12.2, 0, 4);
        botRightRareWheel.position = new BABYLON.Vector3(12.2, 0, 4);

        var botLeftFrontWheel = busMeshes["wheelB"];
        botLeftFrontWheel.rotate(BABYLON.Axis.X, Math.PI / 2, BABYLON.Space.WORLD);

        var botLeftRareWheel = botLeftFrontWheel.createInstance();

        botLeftFrontWheel.parent = botBody;
        botLeftRareWheel.parent = botBody;

        botLeftFrontWheel.position = new BABYLON.Vector3(-12.2, 0, -5);
        botLeftRareWheel.position = new BABYLON.Vector3(12.2, 0, -5);

        botBody.position = new BABYLON.Vector3(0, 0.5, 300);

        for (var i = 0; i < numberOfBuses; i++) { //botsStartPosition.length
            // console.log(i)
            var cloneBot = botBody.clone();
            cloneBot.name = "Autobus" + (1 + i).toString();
            cloneBot.position.x = botsStartPosition[i].x;
            cloneBot.position.z = botsStartPosition[i].z;
            cloneBot.rotation.y = botsStartPosition[i].r;

            cloneBot.busVehiclesCorrection = Math.abs(botsStartPosition[i].z) !== 300;
            cloneBot.sumAngle = botsStartPosition[i].r;
            cloneBot.turnInterval = 0;
            cloneBot.turnAngle = Math.PI / 680;
            cloneBot.speedCar = 0.8;
            cloneBot.speedBotSum = 0;

            this.busVehicles[i] = cloneBot;
        }

        botBody.setEnabled(false);
    }
}

//tieto triedy si mozes doplnit ako chces (volby vozidla) ale musis si prisposobit aj zostatok kodu k tomu. Metoda startBusMoving sa vola z scene.js

class BotsStruct {
    Bus = 0;
    Truck = 0;
}

class BotsMeshesStruct {
    Bus = {};
    Truck = {};
}
