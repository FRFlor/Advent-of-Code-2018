class Node {
    constructor(children, metadata) {
        this.children = children;
        this.metadata = metadata;
    }

    getMetadataSum() {
        return this.metadata.reduce( function (total, metaValue) {
            return total + metaValue;
        }, 0);
    }

    getValue() {
        if (this.children.length === 0) {
            return this.getMetadataSum();
        }

        let value = 0;
        for(let i = 0; i < this.metadata.length; i++) {
            const childIndex = this.metadata[i] - 1;
            const childValue = (this.children[childIndex] === undefined) ? 0 : this.children[childIndex].getValue();

            value += childValue;
        }

        return value;
    }
}

class NodeManager {
    constructor(instructionString) {
        this.instructions = instructionString.split(" ").map( numberString => parseInt(numberString));
        this.nodes = [];
        this.instructionPointer = 0;
    }

    buildAllNodes() {
        this.buildNode(0);
    }

    buildNode(startingIndex) {
        let numberOfChildren = this.instructions[startingIndex];
        let numberOfMetadata = this.instructions[startingIndex + 1];
        let children = [];
        let childStartIndex = startingIndex + 2;
        for(let i = 0; i < numberOfChildren; i++) {
            children.push(this.buildNode(childStartIndex));
            childStartIndex = this.instructionPointer;
        }

        let metadataStartIndex = (numberOfChildren > 0) ? this.instructionPointer : startingIndex + 2;
        let metaData = [];
        let i;
        for (i = metadataStartIndex; i < metadataStartIndex + numberOfMetadata; i++ ) {
            metaData.push(this.instructions[i]);
        }
        this.instructionPointer = i;
        const newNode = new Node(children, metaData);
        this.nodes.push(newNode);
        return newNode;
    }

    getRootNode() {
        return this.nodes[this.nodes.length-1];
    }
}

class DayEight {
    constructor() {
        this.nodeManager = new NodeManager(require('./DayEightInput'));
        this.nodeManager.buildAllNodes();
    }

    firstStar() {
        return this.nodeManager.nodes.reduce(function(totalMetadata, node) {
            return totalMetadata + node.getMetadataSum();
        }, 0)
    }

    secondStar() {
        return this.nodeManager.getRootNode().getValue();
    }
}



const Support  = require('./Support.js');
Support.Timer(() => {
    let dayEight = new DayEight();
    console.log("First Star: " + dayEight.firstStar());
    console.log("Second Star: " + dayEight.secondStar());
});
