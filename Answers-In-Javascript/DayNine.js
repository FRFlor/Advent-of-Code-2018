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
}


class CircleGame {
    constructor(playerCount) {
        this.players = new Players(playerCount);
        this.currentMarble = new Marble(0);

        // It's a circle after all... even though it only has one element... LOL
        this.currentMarble.nextMarble = this.currentMarble;
        this.currentMarble.previousMarble = this.currentMarble;
    }

    linkMarble(newMarble) {
        const marbleToLeft = this.currentMarble.nextMarble;
        const marbleToRight = marbleToLeft.nextMarble;

        newMarble.previousMarble = marbleToLeft;
        newMarble.nextMarble = marbleToRight;
        marbleToLeft.nextMarble = newMarble;
        marbleToRight.previousMarble = newMarble;
    }

    unlinkMarble(targetForDeletion) {
        const marbleToLeft = targetForDeletion.previousMarble;
        const marbleToRight = targetForDeletion.nextMarble;
        marbleToLeft.nextMarble = marbleToRight;
        marbleToRight.previousMarble = marbleToLeft;
    }

    playMarble(newMarble) {
        if (newMarble.number % 23 === 0) {
            this.players.addScoreToCurrentPlayer(newMarble.number);
            // Delete the marble 7 positions to the left of the current marble
            // Add its value to player
            let targetForDeletion = this.currentMarble;
            for (let i = 0; i < 7; i++) {
                targetForDeletion = targetForDeletion.previousMarble;
            }
            this.players.addScoreToCurrentPlayer(targetForDeletion.number);
            this.unlinkMarble(targetForDeletion);
            this.currentMarble = targetForDeletion.nextMarble;
        } else {
            this.linkMarble(newMarble);
            this.currentMarble = newMarble;
        }

        this.players.nextTurn();
    }

    getScore() {
        return Math.max(...this.players.score);
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
            circle.playMarble(new Marble(i));
        }

        return circle.getScore();
    }

    secondStar() {
        let circle = new CircleGame(this.playerCount);
        for (let i = 1; i <= this.marblesCount * 100; i++) {
            circle.playMarble(new Marble(i));
        }

        return circle.getScore();
    }
}


const Support = require('./Support.js');
Support.Timer(() => {
    let dayEight = new DayNine(require('./DayNineInput'));
    console.log('First Star: ' + dayEight.firstStar());
    console.log('Second Star: ' + dayEight.secondStar());
});
