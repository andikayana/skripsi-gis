@extends('layouts.maps')
@section('title','Peta')
@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .panel {
            /*position: absolute;*/
            width: 20%;
            z-index: 100;
            /*height: 100%;*/
            height: 100%;
            float: left;
            overflow: auto;
            background: white;
        }
        @media print {
            .panel {
                float: none;
                width: auto;
            }
        }
        .hide-panel {
            left: -20%;
        }
        .show-panel {
            left: 0px;
        }
        #toggle-button {
            position: absolute;
            background: white;
            border: 1px solid black;
            cursor: pointer;
        }
        .show-panel #toggle-button {
            right: 0px;
        }
        .hide-panel #toggle-button {
            left: 280px;
        }
        #map-holder {
            position: absolute;
            height:100%;
        }

        .show-panel+#map-holder {
            width: 80%;
            left: 20%;
        }

        .hide-panel+#map-holder {
            width: 100%;
            left: 0%;
        }
        #map {
            width: 100%;
            height: 100%;
        }
    </style>
@endsection

@section('content')
    <div id="panel" class="panel show-panel">
        <div id="toggle-button">Hide</div>

    </div>
    <div id="map-holder">
        <div id="map"></div>
    </div>
    <div id="fab_distribution" class="fixed-action-btn" >
        <div class="row">
            <div class="card-panel yellow" style="height:60px;">
                <div class="card-content">
                    <span class="black-text">{{$sekolah->nama}}</span>
                </div>
            </div>
        </div>
    </div>
    @include('tambah')
    <script>
        $(document).ready(function() {
            $('#toggle-button').click(function() {
                if($('#panel').hasClass('show-panel')) {
                    $('#panel').removeClass('show-panel').addClass('hide-panel');
                    $(this).text('Show');
                }
                else {
                    $('#panel').removeClass('hide-panel').addClass('show-panel');
                    $(this).text('Hide');
                }
            });
        });
        var map,infoWindow,directionsService,directionsDisplay;
        function initMap() {
            var options={
                center: {
                    lat: -6.4178, lng: 106.8297
                },
                zoom: 12,
                disableDefaultUI: true
            };
                directionsService = new google.maps.DirectionsService;
                directionsDisplay = new google.maps.DirectionsRenderer;
            var elementById = document.getElementById('map');
            map = new google.maps.Map(elementById,options);

//            var infoWindow = new google.maps.InfoWindow({map: map});
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    console.log("lat: "+lokasi['latitude']+" long: "+lokasi['longitude']);
                    console.log(pos);
                    directionsDisplay.setMap(map);
                    directionsDisplay.setPanel(document.getElementById('panel'));
                    calculateAndDisplayRoute(directionsService, directionsDisplay,pos.lat,pos.lng);
                    map.setCenter(pos);
                }, function() {
                    handleLocationError(true, infoWindow, map.getCenter());
                });
            } else {
                // Browser doesn't support Geolocation
                handleLocationError(false, infoWindow, map.getCenter());
            }

            google.maps.event.addListener(map, "click", function(event) {
                if (infoWindow) {
                    infoWindow.close();
                }
                infoWindow = new google.maps.InfoWindow({map: map});
                var elat = event.latLng.lat();
                var elng = event.latLng.lng();
                var pos = {
                    lat: elat,
                    lng: elng
                };
                infoWindow.setPosition(pos);
                infoWindow.setContent('<button id="mulai" class="waves-effect white-text waves-light center green btn-flat" onclick="coba('+elat+','+elng+');">mulai dari sini</button>');
            });
        }
        function coba(lat,lon) {
            infoWindow.close();
            calculateAndDisplayRoute(directionsService, directionsDisplay,lat,lon);
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay,alat,alon) {

            var awal={
                    lat: parseFloat(alat),
                    lng: parseFloat(alon)
                },
                tujuan={
                    lat: parseFloat(lokasi['latitude']),
                    lng: parseFloat(lokasi['longitude'])
                };
            directionsService.route({
                origin: awal,
                destination: tujuan,
                avoidTolls: true,
                travelMode: 'DRIVING'
            }, function(response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }
        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ?
                    'Error: The Geolocation service failed.' :
                    'Error: Your browser doesn\'t support geolocation.');
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyABwmMBc53s_XnRUtcoFihOoUUMjURgGS4&callback=initMap"
            async defer></script>
    </body>
@endsection