SERIAL_NUMBER = 3214;
GRID_SIZE = 300;

class Cell {
    constructor(x , y) {
        this.rackId = (x+1) + 10;

        this.powerLevel = this.rackId * (y+1) + SERIAL_NUMBER;
        this.powerLevel *= this.rackId;
        let str = this.powerLevel.toString();
        this.powerLevel = (parseInt(str.slice(-3,-2)) | 0) - 5;
    }
}

class DayEleven {
    constructor() {
        this.grid = Array.from(new Array(GRID_SIZE)).map(_ => Array.from(new Array(GRID_SIZE)));
        for (let x = 0; x < GRID_SIZE; x++) {
            for (let y = 0; y < GRID_SIZE; y++) {
                this.grid[x][y] = new Cell(x, y);
            }
        }
    }

    getMaxPowerSquare(squareSize) {
        let powers = [];
        let coords = [];
        for (let x = 0; x < GRID_SIZE-(squareSize-1); x++) {
            for (let y = 0; y < GRID_SIZE-(squareSize-1); y++) {
                let power = 0;
                for (let dx = 0; dx < squareSize; dx++) {
                    for (let dy = 0; dy < squareSize; dy++) {
                        power += this.grid[x+dx][y+dy].powerLevel;
                    }
                }
                powers.push(power);
                coords.push({x,y});
            }
        }

        let maxPower =  Math.max(...powers);
        let i = powers.indexOf(maxPower);
        let position = coords[i];

        return { power: maxPower, x: position.x+1, y: position.y+1};
    }

    firstStar() {
        let response = this.getMaxPowerSquare(3);

        return  `${response.x},${response.y}`;
    }

    secondStar() {
        let maxPowers = [];
        let squaresData = [];
        for (let squareSize = 2; squareSize < 50; squareSize++) {
            let response = this.getMaxPowerSquare(squareSize);
            maxPowers.push(response.power);
            squaresData.push({x: response.x, y: response.y, size: squareSize});
        }
        let indexOfMaximum = maxPowers.indexOf(Math.max(...maxPowers));
        let response = squaresData[indexOfMaximum];

        return `${response.x},${response.y},${response.size}`;
    }
}


const Support = require('./Support.js');
Support.Timer(() => {
    let question = new DayEleven(require('./DebugInput'));
    console.log('First Star: ' + question.firstStar());
    console.log('Second Star: ' + question.secondStar());
});
