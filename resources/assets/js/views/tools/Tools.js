import Tools from './Tools.template.html';

export default {
    template: Tools,

    props: [''],

    data () {
        return {
            n: 0,
            sitename: 'Tools',
            results: {},
            showresults: false,
            showMap: false,
            map: null,
            map_settings: {
                center: {lat: 51.165691, lng: 10.451526},
                zoom: 8
            },
            circleSettings: [],
            labs: [],
            showMap: true,
            csvExportPage: 1,
            stop: false,
            exportStatus: 0,
            exportReady: false,
            downloadLink: '',
            countries: [],
        };
    },

    created () {
        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');
        this.getAllLabs();
        this.getCircleSettings();
        this.getCountries();
    },

    ready() {
        // this.initMap();
    },

    computed: {},

    methods: {
        getCountries(){
            this.$http.get('/api/countries')
                .then(response => {
                    this.countries = response.data;
                });
        },

        stopExport() {
            this.stop = true;
        },
        startExport() {

            var data = [];
            if (this.stop == true) return;

            this.$http.get('/api/patients/export?page=' + this.csvExportPage, data)
                .then(function (response) {
                    console.log(response.data);
                    if (this.csvExportPage < response.data.last_page) {
                        this.csvExportPage++;
                        this.exportStatus = Math.round(this.csvExportPage / response.data.last_page * 100);
                        console.log('Page ' + this.csvExportPage + ' of ' + response.data.last_page + '! :D');
                        this.startExport();
                    } else {
                        if (response.data.filename) {
                            this.downloadLink = '/api/patients/export/download/' + response.data.filename;
                            window.location = this.downloadLink;
                            console.log('ready');
                        }
                    }
                })
                .catch(function (response) {
                    console.log(response);
                });
        },

        getAllLabs () {
            this.$http.get('/api/labs', this.data).then(function (response) {
                this.$set('labs', response.data.data.data);
            }, function (error) {
                // console.
            });
        },
        getCircleSettings () {
            this.$http.get('/api/distribution/circlesettings', this.data).then(function (response) {
                this.$set('circleSettings', response.data);
            }, function (error) {
                // console.
            });
        },
        initMap() {
            this.map = new google.maps.Map(document.getElementById('map'), this.map_settings);
        },

        setMapCenter(location) {
            this.map.setCenter(location);
        },

        addMarker(location, lab) {
            var contentString = "<h4>" + lab.name + "</h4>" +
                "<p>Zip: " + lab.labmeta.zip + "</p>" +
                "<p>Contact Person: " + lab.labmeta.contact_person + "</p>" +
                "<p>Contact Number: " + lab.labmeta.tel + "</p>";

            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            var marker = new google.maps.Marker({
                position: location,
                map: this.map
            });

            marker.addListener('click', function () {
                infowindow.open(this.map, marker);
            });
        },

        setMarkers() {
            this.showMap = true;

            this.initMap();

            // this.results.labs.forEach(function(lab, index) {

            //   // var location = new google.maps.LatLng(parseFloat(lab.lab.lon), parseFloat(lab.lab.lat));
            //   var location = {lat: parseFloat(lab.lab.lon), lng: parseFloat(lab.lab.lat)};

            //   console.log(location);

            //   this.addMarker(location, lab);
            // }.bind(this));

            this.labs.forEach(function (lab, index) {
                // console.log(lab);
                // var location = new google.maps.LatLng(parseFloat(lab.lab.lon), parseFloat(lab.lab.lat));
                var location = {lat: parseFloat(lab.lon), lng: parseFloat(lab.lat)};

                // console.log(location);

                this.addMarker(location, lab);
            }.bind(this));

        },

        setCurrentZipMarker(zip, country) {
            this.$http.get('/api/distribution/lookup/' + zip + '/' + country).then(function (response) {
                var location = {lat: response.data.longitude, lng: response.data.latitude};

                var marker = new google.maps.Marker({
                    position: location,
                    map: this.map,
                    icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                });

                this.setMapCenter(location);

                var start = this.circleSettings.radius_start * 1;
                var inc = this.circleSettings.radius_inc * 1;
                var max = this.circleSettings.radius_max * 1;

                for (var i = start; i <= max; i += inc) {
                    this.drawCircle(location, i)
                }

            })
            ;
        },

        drawCircle(location, radius) {

            function shadeColor2(color, percent) {
                var f = parseInt(color.slice(1), 16), t = percent < 0 ? 0 : 255, p = percent < 0 ? percent * -1 : percent, R = f >> 16, G = f >> 8 & 0x00FF, B = f & 0x0000FF;
                return "#" + (0x1000000 + (Math.round((t - R) * p) + R) * 0x10000 + (Math.round((t - G) * p) + G) * 0x100 + (Math.round((t - B) * p) + B)).toString(16).slice(1);
            }

            new google.maps.Circle({
                strokeColor: shadeColor2('#FF0000', radius / 100),
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: shadeColor2('#FF0000', radius / 100),
                fillOpacity: 0.15,
                map: this.map,
                center: location,
                radius: radius * 1000   // in meters
            });
        },

        getLabs: function (zip, email, country) {
            var resource = this.$resource('/api/distribution{/zip}{/country}{/email}');
            resource.get({zip: zip, country: country, email: email}).then(function (response) {
                this.results = response.data;
                this.showresults = true;

                if (this.showMap == true) {
                    this.setCurrentZipMarker(zip, country);
                    this.setMarkers();
                }

            }.bind(this), function (response) {
                // console.log(response.data);
                $('#debug').addClass('active').find('.debugged-content').html(response.data);
            }.bind(this));
        }
    },

    filters: {
        round: function (distance) {
            var distance = Math.round(distance * 100) / 100;
            var distance = distance.toString().replace('.', ',');
            return distance + ' km';
        }
    }
};
