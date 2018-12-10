const MAXIMUM_EXPECTED_FONT_SIZE = 50;

class Star {
    constructor(x, y, vx, vy) {
        this.position = { x: parseInt(x), y: parseInt(y) };
        this.speed = { x: parseInt(vx), y: parseInt(vy)};
    }

    update(){
        this.position.x += this.speed.x;
        this.position.y += this.speed.y;
    }
}

class DayTen {
    constructor(input) {
        this.stars = new Array(input.length);
        this.seconds = 0;
        for (let i = 0; i < input.length; i++) {
            const m = input[i].match(/position=<\s*(\-?\d+),\s*(\-?\d+)> velocity=<\s*(\-?\d+),\s*(\-?\d+)>/);
            this.stars[i] = new Star(m[1]-1, m[2]-1, m[3], m[4]);
        }

        this.normalizeStars();
    }

    normalizeStars() {
        this.minX =  Math.min(...this.stars.map( star => star.position.x ));
        this.minY =  Math.min(...this.stars.map( star => star.position.y ));

        for (let i = 0; i < this.stars.length; i++){
            this.stars[i].position.x -= this.minX;
            this.stars[i].position.y -= this.minY;
        }

        this.minX = 0;
        this.minY = 0;
        this.maxX =  Math.max(...this.stars.map( star => star.position.x )) + 1;
        this.maxY =  Math.max(...this.stars.map( star => star.position.y )) + 1;
    }

    update() {
        this.seconds++;
        for(let i = 0; i < this.stars.length; i ++) {
            this.stars[i].update();
        }

        this.normalizeStars();
    }

    // The message guaranteed to not be ready if there are rogue stars still.
    // A rogue star being a star that is alone in its row.
    isMessageReady() {
        for (let i = 0; i < this.stars.length; i++) {
            let star = this.stars[i];
            if (this.stars.filter( matches => matches.position.y === star.position.y).length === 1) {
                return false;
            }
        }

        return true;
    }

    render() {
        let result = "\n";
        for (let y = 0; y < this.maxY; y++) {
            let line = "";
            for (let x = 0; x < this.maxX; x++) {
                let star = this.stars.find( star => star.position.x === x && star.position.y === y);
                line += (star === undefined) ? '.' : '#';
            }
            result += line + "\n";
        }

        return result;
    }

    firstStar() {
        do {
            this.update();
        } while (this.maxY > MAXIMUM_EXPECTED_FONT_SIZE || ! this.isMessageReady());
        return this.render();
    }

    secondStar() {
        return this.seconds;
    }
}



const Support = require('./Support.js');
Support.Timer(() => {
    let dayEight = new DayTen(require('./DayTenInput'));
    console.log('First Star: ' + dayEight.firstStar());
    console.log('Second Star: ' + dayEight.secondStar());
});
