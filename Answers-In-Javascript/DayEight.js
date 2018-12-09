class Node {
    constructor(children, metaData) {
        this.children = children;
        this.metaData = metaData;
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
}

class DayEight {
    constructor() {
        this.nodeManager = new NodeManager(require('./DayEightInput'));
    }

    firstStar() {
        this.nodeManager.buildAllNodes();

        return this.nodeManager.nodes.reduce(function(totalMetadata, node) {
            const nodeTotalMetadata = node.metaData.reduce(function(nodeMetadataSum, metadata) {
                return nodeMetadataSum + metadata;
            }, 0);

            return totalMetadata + nodeTotalMetadata;
        }, 0)
    }

    secondStar() {
        return "";
    }
}



const Support  = require('./Support.js');
Support.Timer(() => {
    let dayEight = new DayEight();
    console.log("First Star: " + dayEight.firstStar());
    console.log("Second Star: " + dayEight.secondStar());
});
