const fs = require('fs');
const NONE = -1;
const LEFT = 0;
const RIGHT = 1;
const STRAIGHT = 2;

let grid = [[]];
let cartGrid = [[]];

class Cart {
    constructor(char, x, y) {
        this.crashed = false;
        this.hasMovedThisTurn = false;
        this.x = x;
        this.y = y;
        this.char = char;  // < ^ > v
        this.lastIntersectionAction = NONE;
    }

    moveTo(x, y) {
        // There's a cart there!
        // CRASH!
        if (cartGrid[y][x]) {
            this.crashed = true;
            cartGrid[y][x].crashed = true;
        }

        cartGrid[this.y][this.x] = null;
        cartGrid[y][x] = this.crashed ? null : this;
        this.x = x;
        this.y = y;
        this.turn();

        return ! this.crashed;
    }

    move() {
        if (this.hasMovedThisTurn) {
            return true;
        }
        this.hasMovedThisTurn = true;

        let targetX = this.x;
        let targetY = this.y;
        switch (this.char) {
            case '>':
                targetX = this.x + 1;
                break;
            case '<':
                targetX = this.x - 1;
                break;
            case 'v':
                targetY = this.y + 1;
                break;
            case '^':
                targetY = this.y - 1;
                break;
        }

        return this.moveTo(targetX, targetY);
    }

    turnRight() {
        switch(this.char) {
            case '>':
                return this.char = 'v';
            case '<':
                return this.char = '^';
            case 'v':
                return this.char = '<';
            case '^':
                return this.char = '>';
        }
    }

    turnLeft() {
        switch(this.char) {
            case '>':
                return this.char = '^';
            case '<':
                return this.char = 'v';
            case 'v':
                return this.char = '>';
            case '^':
                return this.char = '<';
        }
    }

    turn() {
        let currentLocation =  grid[this.y][this.x];
        switch (currentLocation) {
            case '+':
                if (this.lastIntersectionAction === RIGHT || this.lastIntersectionAction === NONE) {
                    this.lastIntersectionAction = LEFT;
                    return this.turnLeft();
                }

                if (this.lastIntersectionAction === LEFT) {
                    this.lastIntersectionAction = STRAIGHT;
                    return;
                }

                if (this.lastIntersectionAction === STRAIGHT) {
                    this.lastIntersectionAction = RIGHT;
                    return this.turnRight();
                }
            case '\\':
                if (this.char === '>' || this.char === '<') return this.turnRight();
                if (this.char === '^' || this.char === 'v') return this.turnLeft();
            case '/':
                if (this.char === '>' || this.char === '<') return this.turnLeft();
                if (this.char === '^' || this.char === 'v') return this.turnRight();
            default:
                return;
        }
    }
}


class DayThirteen {
    constructor(input) {
        this.carts = [];
        this.parseMap(input);
    }

    parseMap(input) {
        cartGrid = [[]];
        grid = [[]];
        let x = 0;
        let y = 0;
        for (let i = 0; i < input.length; i++) {
            let charCode = input[i];
            let char = '?';

            if (charCode === 10) // Line Feed
            {
                grid.push([]);
                y++;
                x = 0;
                continue;
            }
            else if (charCode === 60 || charCode === 62) {
                char = '-';
                this.carts.push(new Cart(String.fromCharCode(charCode), x, y, grid));
            }
            else if (charCode === 94 || charCode === 118) {
                char = '|';
                this.carts.push(new Cart(String.fromCharCode(charCode), x, y, grid));
            }
            else {
                char = String.fromCharCode(charCode);
            }
            grid[y].push(char);
            x++;
        }

        grid.splice(grid.length - 1, 1); // Remove extra row at the end

        // Make all rows have the same amount of columns
        let colCount = Math.max(...grid.map(row => row.length));
        for (let i = 0; i < grid.length; i++) {
            while (grid[i].length < colCount) {
                grid[i].push(' ');
            }
        }

        cartGrid = new Array(grid.length);
        for (let i = 0; i < cartGrid.length; i++) {
            cartGrid[i] = new Array(grid[0].length);
            cartGrid[i].fill(null);
        }
        for (let i = 0; i < this.carts.length; i++) {
            let cart = this.carts[i];
            cartGrid[cart.y][cart.x] = cart;
        }
    }

    render() {
        let str = "";
        for (let y = 0; y < cartGrid.length; y++) {
            for (let x = 0; x < cartGrid[y].length; x++) {
                str += cartGrid[y][x] === null ? grid[y][x] : cartGrid[y][x].char;
            }
            str += "\n";
        }
        console.clear();
        console.log(str);
        return str;
    }

    firstStar() {
        let noCrashes = true;
        while(true) {
            for (let y = 0; y < cartGrid.length; y++) {
                for (let x = 0; x < cartGrid[y].length; x++) {
                    if (cartGrid[y][x] === null || cartGrid[y][x].hasMovedThisTurn) continue;

                    noCrashes = cartGrid[y][x].move();
                    if (! noCrashes) {
                        let crashedCar = this.carts.find( cart => cart.crashed);
                        return `${crashedCar.x},${crashedCar.y}`;
                    }
                }
            }
            this.carts.forEach(cart => cart.hasMovedThisTurn = false);
        }
    }

    secondStar() {
        let noCrashes = true;
        while(true) {
            for (let y = 0; y < cartGrid.length; y++) {
                for (let x = 0; x < cartGrid[y].length; x++) {
                    if (cartGrid[y][x] === null || cartGrid[y][x].hasMovedThisTurn) continue;
                    noCrashes = cartGrid[y][x].move();
                    if (! noCrashes) {
                        this.carts = this.carts.filter(cart => !cart.crashed);
                    }
                }
            }

            if (this.carts.length === 1){
                return `${this.carts[0].x},${this.carts[0].y}`
            }
            this.carts.forEach(cart => cart.hasMovedThisTurn = false);
        }
    }
}


const Support = require('./Support.js');
Support.Timer(() => {
    let debugInput = fs.readFileSync('./DayThirteenInput.txt');
    let question = new DayThirteen(debugInput);

    console.log('First Star: ' + question.firstStar());
    console.log('Second Star: ' + question.secondStar());
});
