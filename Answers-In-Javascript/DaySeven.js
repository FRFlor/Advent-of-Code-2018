const STEP_NAME_INDEX = 36;
const DEPENDENCY_NAME_INDEX = 5;


class Step {
    constructor(name) {
        this.name = name;
        this.dependencies = [];
        this.done = false;
    }

    isDoable() {
        if (this.done) {
            return false;
        }

        // If there's any dependency that needs to be done... this step is not doable
        return this.dependencies.find(dependency => !dependency.done) === undefined;
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
        return this.data.find(step => !step.done) === undefined;
    }

    availableSteps() {
        return this.data.reduce((availableSteps, step) => {
            if (step.isDoable()) {
                availableSteps.push(step);
                return availableSteps;
            }

            return availableSteps;
        }, []);
    }
}


class DaySeven {
    constructor() {
        this.input = require('./DaySevenInput');
        this.steps = new Steps();
        this.stepsBuilt = [];
    }

    firstStar() {
        this.getStepsDependencies();
        let executionOrder = '';

        while (!this.steps.isComplete()) {
            let availableSteps = this.steps.availableSteps();
            let nextStep = availableSteps.sort((a, b) => {
                return ( a.name > b.name ) ? 1 : -1;
            })[0];

            executionOrder += nextStep.name;
            nextStep.done = true;
        }

        return executionOrder;
    }

    getStepsDependencies() {
        this.input.forEach((instruction) => {
            let stepName = instruction[STEP_NAME_INDEX];
            let dependencyName = instruction[DEPENDENCY_NAME_INDEX];

            this.steps.registerStep(stepName, dependencyName);
        });
    }
}


const {performance} = require('perf_hooks');
var t0 = performance.now();

console.log(( new DaySeven() ).firstStar());

var t1 = performance.now();
console.log('Executed in ' + ( t1 - t0 ) + ' milliseconds.');
