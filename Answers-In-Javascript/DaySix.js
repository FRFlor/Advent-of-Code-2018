const SAFE_DISTANCE = 10000;


class DaySix {
    firstStar() {
        this.grid = new SpaceGrid(require('./DaySixInputs'));

        return this.grid.getLargestFiniteAreaSize();
    }

    secondStar() {
        return this.grid.getSafeAreaSize();
    }
}


class SpaceGrid {
    constructor(rawCoordinates) {
        let arrayX = rawCoordinates.map(el => el[0]);
        let arrayY = rawCoordinates.map(el => el[1]);

        this.trueMinX = Math.min(...arrayX);
        this.trueMaxX = Math.max(...arrayX);
        this.trueMinY = Math.min(...arrayY);
        this.trueMaxY = Math.max(...arrayY);

        this.maxX = this.trueMaxX - this.trueMinX;
        this.maxY = this.trueMaxY - this.trueMinY;
        this.coordinates = rawCoordinates.map(
            coordinate => [coordinate[0] - this.trueMinX, coordinate[1] - this.trueMinY]);

        this.infiniteAreaCoordinatesIds = [];
    }

    getSafeAreaSize() {
        let size = 0;
        for (let x = 0; x < this.maxX; x++) {
            for (let y = 0; y < this.maxY; y++) {
                let distance = this.coordinates.reduce( (distance, coordinate) => {
                    distance += Math.abs(coordinate[0] - x) + Math.abs(coordinate[1] - y);

                    return distance;
                }, 0);

                if (distance < SAFE_DISTANCE) {
                    size++;
                }
            }
        }

        return size;
    }

    isOnEdge(x, y) {
        return x === 0 || x === this.maxX || y === 0 || y === this.maxY;
    }

    getLargestFiniteAreaSize() {
        this.coordinates.forEach((coordinate, id) => {
            if (this.isOnEdge(coordinate[0], coordinate[1])) {
                this.infiniteAreaCoordinatesIds.push(id);
            }
        });

        // Get finite areas
        let coordinateAreas = [];
        for (let x = 0; x <= this.maxX; x++) {
            for (let y = 0; y <= this.maxY; y++) {
                let closestId = this.getClosestCoordinateToPoint(x, y);
                if (closestId !== -1) {
                    coordinateAreas.push(closestId);
                }
            }
        }

        return countOfMostFrequent(coordinateAreas);
    }

    isInfiniteAreaCoordinate(coordinateId) {
        return this.infiniteAreaCoordinatesIds.find(el => el === coordinateId) !== undefined;
    }

    getClosestCoordinateToPoint(x, y) {
        let minDistance = null;
        let closestCoordinateId = null;
        let count = null;

        for (let i = 0; i < this.coordinates.length; i++) {
            let coordinate = this.coordinates[i];

            let distance = Math.abs(coordinate[0] - x) + Math.abs(coordinate[1] - y);

            if (minDistance === distance) {
                count++;
            }

            if (minDistance === null || minDistance > distance) {
                minDistance = distance;
                closestCoordinateId = i;
                count = 1;
            }
        }

        if (count > 1 || this.isInfiniteAreaCoordinate(closestCoordinateId)) {
            return -1;
        }

        if (this.isOnEdge(x, y)) {
            this.infiniteAreaCoordinatesIds.push(closestCoordinateId);
            return -1;
        }

        return closestCoordinateId;
    }
}


function countOfMostFrequent(arr1) {
    var counts = {};
    var compare = 0;
    var mostFrequent;
    return ( function(array) {
        for (var i = 0, len = array.length; i < len; i++) {
            var word = array[i];

            if (counts[word] === undefined) {
                counts[word] = 1;
            }
            else {
                counts[word] = counts[word] + 1;
            }
            if (counts[word] > compare) {
                compare = counts[word];
                mostFrequent = arr1[i];
            }
        }
        return compare;
    } )(arr1);
}


const Support  = require('./Support.js');
Support.Timer(() => {
    let daySix = new DaySix();
    console.log("First Star: " + daySix.firstStar());
    console.log("Second Star: " + daySix.secondStar());
});
