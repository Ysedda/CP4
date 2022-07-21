/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

require('bootstrap');
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');
require('leaflet/dist/leaflet.js');

let dataMap = JSON.parse(document.getElementById('map').getAttribute('data-map'));

var map = L.map('map').setView([47, 1.59], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 14,
    attribution: '© OpenStreetMap'
}).addTo(map);

var latlngs = dataMap.routes[0].geometry.coordinates;
let lats = [];
let longs = [];

for (let i = 0; i < latlngs.length; i++) {
    lats.push(latlngs[i][1]);
    longs.push(latlngs[i][0]);
}

let coords = [];
let arr = [];
console.log(lats);

for(let i = 0; i < latlngs.length; i++) {
    arr = [];
    arr[0] = lats[i];
    arr[1] = longs[i];
    coords.push(arr);
}
console.log(coords);

var polyline = L.polyline(coords, {color: 'black'}).addTo(map);

map.fitBounds(polyline.getBounds());

