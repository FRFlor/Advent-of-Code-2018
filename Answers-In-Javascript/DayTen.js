const fs = require('fs');

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

        this.minX =  Math.min(...this.stars.map( star => star.position.x )) - 5;
        this.minY =  Math.min(...this.stars.map( star => star.position.y )) - 5;

        for (let i = 0; i < this.stars.length; i++){
            this.stars[i].position.x -= this.minX;
            this.stars[i].position.y -= this.minY;
        }

        this.maxX =  Math.max(...this.stars.map( star => star.position.x )) + 5;
        this.maxY =  Math.max(...this.stars.map( star => star.position.y )) + 5;
    }

    update() {
        this.seconds++;
        for(let i = 0; i < this.stars.length; i ++) {
            this.stars[i].update();
        }

        this.minX =  Math.min(...this.stars.map( star => star.position.x )) - 5;
        this.minY =  Math.min(...this.stars.map( star => star.position.y )) - 5;

        for (let i = 0; i < this.stars.length; i++){
            this.stars[i].position.x -= this.minX;
            this.stars[i].position.y -= this.minY;
        }

        this.maxX =  Math.max(...this.stars.map( star => star.position.x )) + 5;
        this.maxY =  Math.max(...this.stars.map( star => star.position.y )) + 5;
    }

    render() {
        if (this.maxX > 80 || this.maxY > 80) {
            return;
        }
        fs.writeFileSync('temp.txt', '');
        for (let y = 0; y < this.maxY; y++) {
            let line = "";
            for (let x = 0; x < this.maxX; x++) {
                let star = this.stars.find( star => star.position.x === x && star.position.y === y);
                line += (star === undefined) ? '.' : '#';
            }
            fs.writeFileSync('temp.txt', line + '\n', {flag: 'a'});
        }
    }

    firstStar() {
        while(true) {
            this.render();
            this.update();
        }

        return  "";
    }

    secondStar() {
        return "";
    }
}



const Support = require('./Support.js');
Support.Timer(() => {
    let dayEight = new DayTen(require('./DayTenInput'));
    console.log('First Star: ' + dayEight.firstStar());
    console.log('Second Star: ' + dayEight.secondStar());
});
