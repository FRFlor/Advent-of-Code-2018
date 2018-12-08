module.exports = {
    Timer: (callBack) => {
        const {performance} = require('perf_hooks');
        var t0 = performance.now();

        callBack();

        var t1 = performance.now();
        console.log('Executed in ' + ( t1 - t0 ) + ' milliseconds.');
    }
};



