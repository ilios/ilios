var system = require('system');

if (system.args.length !== 2) {
    console.log('Usage: run-jasmine.js URL');
    phantom.exit(1);
}

// Check for console message indicating jasmine is finished running
var doneRegEx = /^\d+ specs, (\d+) failure/;

var page = require('webpage').create();

// Route "console.log()" calls from within the Page context
// to the main Phantom context (i.e. current "this")

page.onConsoleMessage = function (msg) {
    system.stdout.write(msg);
    var match = doneRegEx.exec(msg);
    if (match) {
        phantom.exit(match[1]);
    }
};

system.stdout.writeLine("");

page.open(system.args[1], function (status){
    if (status !== "success") {
        console.log("Couldn't load the page");
    }
    system.stdout.writeLine("");
});

