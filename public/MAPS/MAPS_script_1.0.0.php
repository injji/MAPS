<?php
    if( !isset($_client_id) && !$_client_id ) die;
?>
'use strict';(function(e,d){let f=d.body||d.getElementsByTagName("body")[0],g=function(a,b){let c=d.createElement("script");c.type="text/javascript";a+=-1!=a.indexOf("?")?"&":"?";a+="client_id="+MAPSAPI.client_id+"&dc="+Math.round(+new Date/36E5);c.src=a;b&&(c.onload=function(){eval(b)});f.appendChild(c)};e=function(){if("function"==typeof MAPSAPI.getAgentService){let a=MAPSAPI.getAgentService();Array.isArray(a)&&a.forEach(function(b){g(b.src,b.trigger)})}};"object"===typeof MAPSAPI?(e(),console.log("MAPS Script v"+
MAPSAPI.version)):console.error("MAPS Script load failed.")})(window,document);
