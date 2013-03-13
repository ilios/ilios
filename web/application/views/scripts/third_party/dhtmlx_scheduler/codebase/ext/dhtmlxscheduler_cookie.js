/*
This software is allowed to use under GPL or you need to obtain Commercial or Enterise License
to use it in non-GPL project. Please contact sales@dhtmlx.com for details
*/
(function(){function h(b,a,c){var d=b+"="+c+(a?"; "+a:"");document.cookie=d}function i(b){var a=b+"=";if(document.cookie.length>0){var c=document.cookie.indexOf(a);if(c!=-1){c+=a.length;var d=document.cookie.indexOf(";",c);if(d==-1)d=document.cookie.length;return document.cookie.substring(c,d)}}return""}var g=!0;scheduler.attachEvent("onBeforeViewChange",function(b,a,c,d){if(g){g=!1;var e=i("scheduler_settings");if(e)return e=unescape(e).split("@"),e[0]=this.templates.xml_date(e[0]),window.setTimeout(function(){scheduler.setCurrentView(e[0],
e[1])},1),!1}var f=escape(this.templates.xml_format(d||a)+"@"+(c||b));h("scheduler_settings","expires=Sun, 31 Jan 9999 22:00:00 GMT",f);return!0});var f=scheduler._load;scheduler._load=function(){var b=arguments;if(!scheduler._date&&scheduler._load_mode){var a=this;window.setTimeout(function(){f.apply(a,b)},1)}else f.apply(this,b)}})();
