const FONT_SIZE = 10;

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

        this.minX =  Math.min(...this.stars.map( star => star.position.x )) - 1;
        this.minY =  Math.min(...this.stars.map( star => star.position.y )) - 1;

        for (let i = 0; i < this.stars.length; i++){
            this.stars[i].position.x -= this.minX;
            this.stars[i].position.y -= this.minY;
        }

        this.maxX =  Math.max(...this.stars.map( star => star.position.x )) + 1;
        this.maxY =  Math.max(...this.stars.map( star => star.position.y ));
    }

    update() {
        this.seconds++;
        for(let i = 0; i < this.stars.length; i ++) {
            this.stars[i].update();
        }

        this.minX =  Math.min(...this.stars.map( star => star.position.x )) -1;
        this.minY =  Math.min(...this.stars.map( star => star.position.y ));

        for (let i = 0; i < this.stars.length; i++){
            this.stars[i].position.x -= this.minX;
            this.stars[i].position.y -= this.minY;
        }

        this.maxX =  Math.max(...this.stars.map( star => star.position.x )) + 1;
        this.maxY =  Math.max(...this.stars.map( star => star.position.y )) + 1;
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
        } while (this.maxY > FONT_SIZE);
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
