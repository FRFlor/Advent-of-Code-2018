const STEP_NAME_INDEX = 36;
const DEPENDENCY_NAME_INDEX = 5;
const ASCII_A = 65;
const BASE_WORK = 60 + 1 - ASCII_A;
const NUMBER_OF_WORKERS = 5;

class Step {
    constructor(name) {
        this.name = name;
        this.dependencies = [];
        this.workRemaining = BASE_WORK + name.charCodeAt(0);
        this.underWork = false;
    }

    isDoable() {
        if (this.workRemaining === 0 || this.underWork) {
            return false;
        }

        // If there's any dependency that needs to be done... this step is not doable
        return this.dependencies.find(dependency => dependency.workRemaining > 0) === undefined;
    }
}

class Steps {
    constructor() {
        this.data = [];
    }

    registerStep(stepName, dependencyName) {
        let baseStep = this.data.find(step => step.name === stepName);
        if (baseStep === undefined) {
            baseStep = new Step(stepName);
            this.data.push(baseStep);
        }

        let dependencyStep = this.data.find(step => step.name === dependencyName);
        if (dependencyStep === undefined) {
            dependencyStep = new Step(dependencyName);
            this.data.push(dependencyStep);
        }

        baseStep.dependencies.push(dependencyStep);
    }

    isComplete() {
        return this.data.find(step => step.workRemaining > 0) === undefined;
    }

    availableSteps() {
        return this.data.filter(step => step.isDoable()).sort((a, b) => {
            return ( a.name > b.name ) ? 1 : -1;
        });
    }
}


class Worker {
    constructor() {
        this.workingOn = null;
    }

    assignTo(step) {
        this.workingOn = step;
        step.underWork = true;
    }

    work() {
        if (this.workingOn !== null) {
            this.workingOn.workRemaining--;
            if (this.workingOn.workRemaining === 0) {
                this.workingOn.underWork = false;
                this.workingOn = null;
            }
        }
    }
}


class WorkForce {
    constructor(numberOfWorkers) {
        this.workers = Array.from(Array(numberOfWorkers)).map(_ => new Worker());
    }

    availableWorkers() {
        return this.workers.filter(worker => worker.workingOn === null);
    }

    assignWorkers(stepsAvailable) {
        let availableWorkers = this.availableWorkers();
        let stepIndex = 0;
        let workerIndex = 0;
        while (availableWorkers[workerIndex] && stepsAvailable[stepIndex]) {
            let nextStep = stepsAvailable[stepIndex++];
            let nextWorker = availableWorkers[workerIndex++];
            nextWorker.assignTo(nextStep);
        }
    }

    //  Work for a second
    tick() {
        this.workers.forEach(worker => worker.work());
    }

}


class DaySeven {
    constructor() {
        this.input = require('./DaySevenInput');
        this.steps = null;
        this.stepsBuilt = [];
    }

    firstStar() {
        this.getStepsDependencies();
        let executionOrder = '';

        while (!this.steps.isComplete()) {
            let nextStep = this.steps.availableSteps()[0];

            executionOrder += nextStep.name;
            nextStep.workRemaining = 0;
        }

        return executionOrder;
    }

    secondStar() {
        this.getStepsDependencies();
        this.workForce = new WorkForce(NUMBER_OF_WORKERS);

        let secondsElapsed = 0;
        while (!this.steps.isComplete()) {
            let availableSteps = this.steps.availableSteps();
            this.workForce.assignWorkers(availableSteps);
            this.workForce.tick();
            secondsElapsed++;
        }

        return secondsElapsed;
    }

    getStepsDependencies() {
        this.steps = new Steps();

        this.input.forEach((instruction) => {
            let stepName = instruction[STEP_NAME_INDEX];
            let dependencyName = instruction[DEPENDENCY_NAME_INDEX];

            this.steps.registerStep(stepName, dependencyName);
        });
    }
}

const Support  = require('./Support.js');
Support.Timer(() => {
    let daySeven = new DaySeven();
    console.log("First Star: " + daySeven.firstStar());
    console.log("Second Star: " + daySeven.secondStar());
});


