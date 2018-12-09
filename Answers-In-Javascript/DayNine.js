class Marble {
    constructor(number) {
        this.number = number;
        this.nextMarble = null;
        this.previousMarble = null;
    }
}

class Players {
    constructor(playerCount) {
        this.score = Array.from(Array(playerCount).keys()).map(_ => 0);
        this.currentIndex = 0;
    }

    addScoreToCurrentPlayer(amount) {
        this.score[this.currentIndex] += amount;
    }

    nextTurn() {
        this.currentIndex++;
        if (this.currentIndex >= this.score.length) {
            this.currentIndex = 0;
        }
    }

    maxScore(){
        return Math.max(this.score);
    }
}

class CircleGame {
    constructor(playerCount) {
        this.players = new Players(playerCount);
        this.zeroMarble = new Marble(0);
        this.currentMarble = this.zeroMarble;
        this.currentMarble.nextMarble = this.currentMarble;
        this.currentMarble.previousMarble = this.currentMarble;
    }

    addMarble(newMarble) {
        if (newMarble.number % 23 === 0) {
            this.players.addScoreToCurrentPlayer(newMarble.number);
            let targetForDeletion = this.currentMarble;
            for (let i = 0; i < 7; i++) {
                targetForDeletion = targetForDeletion.previousMarble;
            }
            this.players.addScoreToCurrentPlayer(targetForDeletion.number);
            const marbleToLeft = targetForDeletion.previousMarble;
            const marbleToRight = targetForDeletion.nextMarble;

            marbleToLeft.nextMarble = marbleToRight;
            marbleToRight.previousMarble = marbleToLeft;

            this.currentMarble = marbleToRight;
            this.players.nextTurn();
            return;
        }

        const marbleToLeft = this.currentMarble.nextMarble;
        const marbleToRight = marbleToLeft.nextMarble;

        newMarble.previousMarble = marbleToLeft;
        newMarble.nextMarble = marbleToRight;
        marbleToLeft.nextMarble = newMarble;
        marbleToRight.previousMarble = newMarble;

        this.currentMarble = newMarble;
        this.players.nextTurn();
    }

    printCircle() {
        let currentMarble = this.zeroMarble;
        do {
            console.log(currentMarble.number);
            currentMarble = currentMarble.nextMarble;
        } while (currentMarble.number !== this.zeroMarble.number);
    }

    getScore(){
        return Math.max(...this.players.score)
    }
}


class DayNine {
    constructor(input) {
        const matches = input.match(/(\d+) players; last marble is worth (\d+) points/);
        this.playerCount = parseInt(matches[1]);
        this.marblesCount = parseInt(matches[2]);
    }

    firstStar() {
        let circle = new CircleGame(this.playerCount);
        for (let i = 1; i <= this.marblesCount; i++) {
            circle.addMarble(new Marble(i));
        }

        return circle.getScore();
    }

    secondStar() {
        let circle = new CircleGame(this.playerCount);
        for (let i = 1; i <= this.marblesCount*100; i++) {
            circle.addMarble(new Marble(i));
        }

        return circle.getScore();
    }
}


const Support = require('./Support.js');
Support.Timer(() => {
    let dayEight = new DayNine(require('./DayNineInput'));
    console.log('First Star: ' + dayEight.firstStar());
    console.log("Second Star: " + dayEight.secondStar());
});
