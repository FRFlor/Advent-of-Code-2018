class Star {
    constructor(x, y, vx, vy) {
        this.x0 = parseInt(x);
        this.y0 = parseInt(y);

        this.position = {x: this.x0, y: this.y0 };
        this.speed = {x: parseInt(vx), y: parseInt(vy)};
    }

    update(seconds) {
        this.position.x = this.x0 + this.speed.x * seconds;
        this.position.y = this.y0 + this.speed.y * seconds;
    }
}


class DayTen {
    constructor(input) {
        this.stars = new Array(input.length);
        this.area = null;
        this.seconds = 0;
        for (let i = 0; i < input.length; i++) {
            const m = input[i].match(/position=<(.+),(.+)> velocity=<(.+),(.+)>/);
            this.stars[i] = new Star(m[1] - 1, m[2] - 1, m[3], m[4]);
        }
    }

    update(newSeconds) {
        this.seconds = newSeconds;
        for (let i = 0; i < this.stars.length; i++) {
            this.stars[i].update(newSeconds);
        }

        this.calculateEdges();
        this.area = ( this.maxY - this.minY ) * ( this.maxX - this.minX );
    }

    calculateEdges() {
        let coordsX = this.stars.map(star => star.position.x);
        let coordsY = this.stars.map(star => star.position.y);

        this.minX = Math.min(...coordsX);
        this.minY = Math.min(...coordsY);
        this.maxX = Math.max(...coordsX);
        this.maxY = Math.max(...coordsY);
    }

    render() {
        this.calculateEdges();

        let result = '\n';
        for (let y = this.minY; y <= this.maxY; y++) {
            let line = '';
            for (let x = this.minX; x <= this.maxX; x++) {
                let star = this.stars.find(star => star.position.x === x && star.position.y === y);
                line += ( star === undefined ) ? '.' : '#';
            }
            result += line + '\n';
        }

        return result;
    }

    convergeToMessage(t0, tf) {
        let midpointT = Math.floor((t0 + tf)/2);
        this.update(midpointT);
        let midpointA = this.area;
        this.update(midpointT + 1);
        let nextPointA = this.area;
        let isDispersing = (nextPointA > midpointA);


        if (tf - t0 < 5) {
            // The change is small enough that it is okay to transverse one by one now
            let delta = isDispersing ? -1 : 1;
            let lastArea = midpointA;
            let newArea = nextPointA;
            do {
                lastArea = newArea;
                this.update(this.seconds + delta);
                newArea = this.area;
            } while (newArea < lastArea);

            return this.update(this.seconds - delta);
        }

        if (isDispersing) {
            return this.convergeToMessage(t0, midpointT);
        } else {
            return this.convergeToMessage(midpointT, tf);
        }
    }

    firstStar() {
        this.convergeToMessage(0, 1000000);

        return this.render();
    }

    secondStar() {
        return this.seconds;
    }
}


const Support = require('./Support.js');
Support.Timer(() => {
    let dayTen = new DayTen(require('./DayTenInput'));

    console.log('First Star: ' + dayTen.firstStar());
    console.log('Second Star: ' + dayTen.secondStar());
});
