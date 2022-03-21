//jQuery
const $ = require('jquery');
global.$ = global.jQuery = $;

//Routing
const routes = require('./fos_js_routes.json');
global.Routing = require('../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min');
Routing.setRoutingData(routes);

//Bootstrap
require('bootstrap');