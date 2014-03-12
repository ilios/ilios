if (navigator.userAgent.indexOf("PhantomJS") !== -1) {
   var consoleReporter = jasmineRequire.ConsoleReporter()({
      print: function() {
        console.log.apply(console, arguments);
      }
   });

  jasmine.getEnv().addReporter(consoleReporter);
}
