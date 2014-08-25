/**
 * Parse the HTML for config options in the form
 * <meta name="env-name" value="production">
 * and inject them into the application
 * Modified from from http://ember.zone/handling-environment-data-with-ember-js/
 */
Ember.Application.initializer({
    name: 'loadConfig',

    initialize: function( container, application ) {
        if(application.get('config') === undefined || application.get('config').constructor.toString() != 'Ember.Object') {
            throw new Ember.Error("The Application object must contain an 'config' property which is an Ember.Object.");
        }

        var envVars  = {};
        Ember.$("meta").each(function(){
            var envKey   = $(this).attr("name");
            var envValue = $(this).attr("value");
            //does the meta tag start with 'env-'?
            if (/^env\-/.test(envKey)) {
                //produce a camelized version of the env variable name, ignoring the initial 'env-'.
                var propertyName = Em.String.camelize(envKey.substring(4));
                //comma seperated gets turned into an array
                if(envValue.indexOf(",") != -1){
                    envValue = envValue.split(',');
                } else {
                    switch(envValue){
                        case '':
                            envValue = null;
                            break;
                        case 'true':
                            envValue = true;
                            break;
                        case 'false':
                            envValue = false;
                            break;
                    }
                }
                envVars[propertyName] = envValue;
              }
        });
        application.get("config").setProperties(envVars);
    }
});
