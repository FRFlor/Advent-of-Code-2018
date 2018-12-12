class Pot {
    constructor(contents, number = null) {
        this.prev = null;
        this.next = null;
        this.contents = contents;
        this.number = number;
    }

    attachToRightOf(pot) {
        this.prev = pot;
        pot.next = this;
        this.number = pot.number + 1;
    }

    attachToLeftOf(pot) {
        this.next = pot;
        pot.prev = this;
        this.number = pot.number - 1;
    }

    neighborContents(shift) {
        try {
            switch (shift) {
                case 1:
                    return this.next.contents;
                case 2:
                    return this.next.next.contents;
                case -1:
                    return this.prev.contents;
                case -2:
                    return this.prev.prev.contents;
            }
        }
        catch (e) {
            return '.';
        }

    }

    getState() {
        return ( this.neighborContents(-2) + this.neighborContents(-1) + this.contents +
            this.neighborContents(1) + this.neighborContents(2) );
    }
}

class DayTwelve {
    constructor(input) {
        this.rules = [];
        for (let i = 1; i < input.length; i++) {
            const m = input[i].match(/(.{5}) => (.)/);
            this.rules.push({
                state: m[1],
                effect: m[2],
            });
        }
        this.firstGenerationString = input[0].split('');

        this.buildFirstGeneration();
    }

    buildFirstGeneration() {
        this.start = null;
        this.end = null;

        let pot = new Pot(this.firstGenerationString[0], 0);
        this.start = pot;
        let previousPot = pot;

        for (let i = 1; i < this.firstGenerationString.length; i++) {
            pot = new Pot(this.firstGenerationString[i]);
            pot.attachToRightOf(previousPot);
            previousPot = pot;
        }

        this.end = pot;

        this.generation = 0;
        this.padGeneration();
    }

    padGeneration() {
        let pot = null;

        while (!( this.start.contents === '.' && this.start.next.contents === '.' )) {
            pot = new Pot('.');
            pot.attachToLeftOf(this.start);
            this.start = pot;
        }

        while (!( this.end.contents === '.' && this.end.prev.contents === '.' )) {
            pot = new Pot('.');
            pot.attachToRightOf(this.end);
            this.end = pot;
        }
    }

    getNewValue(pot) {
        let rule = this.rules.find(rule => rule.state === pot.getState());

        if (rule === undefined) {
            return '.';
        }

        return rule.effect;
    }

    printPots() {
        let pot = this.start;
        let str = pot.contents;
        while (pot.next !== null) {
            pot = pot.next;
            str += pot.contents;
        }

        return str;
    }

    update() {
        let newGenPot = new Pot(this.getNewValue(this.start), this.start.number);
        let newStart = newGenPot;

        let oldGenPot = this.start;
        let newGenPot_l = newGenPot;
        let newGenPot_r = null;
        while (oldGenPot.next !== null) {
            oldGenPot = oldGenPot.next;
            newGenPot_r = new Pot(this.getNewValue(oldGenPot));
            newGenPot_r.attachToRightOf(newGenPot_l);

            newGenPot_l = newGenPot_r;
        }

        this.generation++;
        this.start = newStart;
        this.end = newGenPot_r;
        this.padGeneration();
    }

    totalValue() {
        let pot = this.start;

        let count = ( pot.contents === '#' ) ? pot.number : 0;
        while (pot.next !== null) {
            pot = pot.next;
            count += ( pot.contents === '#' ) ? pot.number : 0;
        }

        return count;
    }

    firstStar() {
        do {
            this.update();
        } while (this.generation < 20);

        return this.totalValue();
    }

    secondStar() {
        this.buildFirstGeneration();

        let delta0 = Infinity;
        let deltaf = Infinity;
        let stabilityCount = 0;
        let v0 = null;
        let vf = this.totalValue();
        do {
            this.update();
            v0 = vf;
            vf = this.totalValue();
            delta0 = deltaf;
            deltaf = vf - v0;
            if (deltaf === delta0) {
                stabilityCount++;
            }
            else {
                stabilityCount = 0;
            }
        } while (stabilityCount < 50);

        return ( 50E9 - this.generation ) * deltaf + vf;
    }
}

const Support = require('./Support.js');
Support.Timer(() => {
    let question = new DayTwelve(require('./DayTwelveInput'));

    console.log('First Star: ' + question.firstStar());
    console.log('Second Star: ' + question.secondStar());
});
