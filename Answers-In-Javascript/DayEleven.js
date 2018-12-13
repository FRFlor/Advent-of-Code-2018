SERIAL_NUMBER = 3214;
GRID_SIZE = 300;


class Cell {
    constructor(x, y) {
        this.rackId = ( x + 1 ) + 10;

        this.powerLevel = this.rackId * ( y + 1 ) + SERIAL_NUMBER;
        this.powerLevel *= this.rackId;
        let str = this.powerLevel.toString();
        this.powerLevel = ( parseInt(str.slice(-3, -2)) | 0 ) - 5;
    }
}

// Curious to know what's going on here? https://en.wikipedia.org/wiki/Summed-area_table
class SummedPowerLevelGrid {
    constructor(grid) {
        this.grid = grid;

        this.sumTable = new Array(GRID_SIZE);
        for (let i = 0; i < GRID_SIZE; i++) {
            this.sumTable[i] = new Array(GRID_SIZE);
        }

        for (let y = 0; y < GRID_SIZE; y++) {
            for (let x = 0; x < GRID_SIZE; x++) {
                let p1 /* x, y-1 */ = ( y - 1 ) < 0 ? 0 : this.sumTable[x][y - 1];
                let p2 /* x -1, y */ = ( x - 1 ) < 0 ? 0 : this.sumTable[x - 1][y];
                let p3 /* x-1, y-1 */ = ( ( x - 1 ) < 0 || ( y - 1 ) < 0 ) ? 0 : this.sumTable[x - 1][y - 1];

                this.sumTable[x][y] = this.grid[x][y].powerLevel + p1 + p2 - p3;
            }
        }
    }

    getPowerArea(x0, y0, xf, yf) {
        let p1 = ( x0 - 1 < 0 || y0 - 1 < 0 ) ? 0 : this.sumTable[x0 - 1][y0 - 1];
        let p2 = ( y0 - 1 < 0 ) ? 0 : this.sumTable[xf][y0 - 1];
        let p3 = ( x0 - 1 < 0 ) ? 0 : this.sumTable[x0 - 1][yf];

        return this.sumTable[xf][yf] + p1 - p2 - p3;
    };

    getMaxPowerAreaByAreaSize(areaSize) {
        let powers = [];
        let coords = [];
        for (let y = 0; y < GRID_SIZE - ( areaSize - 1 ); y++) {
            for (let x = 0; x < GRID_SIZE - ( areaSize - 1 ); x++) {
                powers.push(this.getPowerArea(x, y, x + areaSize - 1, y + areaSize - 1));
                coords.push({x, y});
            }
        }
        let maxPower = Math.max(...powers);
        let maxPowerIndex = powers.indexOf(maxPower);
        let maxPowerCoord = coords[maxPowerIndex];

        return {power: maxPower, x: maxPowerCoord.x + 1, y: maxPowerCoord.y + 1};
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

        this.sumTable = new SummedPowerLevelGrid(this.grid);
    }

    firstStar() {
        let response = this.sumTable.getMaxPowerAreaByAreaSize(3);

        return `${response.x},${response.y}`;
    }

    secondStar() {
        let maxPowers = [];
        let squaresData = [];
        for (let squareSize = 2; squareSize < GRID_SIZE; squareSize++) {
            let response = this.sumTable.getMaxPowerAreaByAreaSize(squareSize);
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
    let question = new DayEleven();
    console.log('First Star: ' + question.firstStar());
    console.log('Second Star: ' + question.secondStar());
});
