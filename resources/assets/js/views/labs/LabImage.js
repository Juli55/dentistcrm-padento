import LabImageView from './lab-image.template.html';

export default {
    template: LabImageView,

    data() {
        return {
            lab_id: null,
            lab: [],
            images: {
                bilder: null,
                zert: null,
            },
            // whoami: [],
            bild1: false,
            bild2: false,
            bild3: false,
            bild4: false,
            bild5: false,
            bild6: false,
            zert1: false,
            zert2: false,
            zert3: false,
            zert4: false,
            zert5: false
        };
    },

    created: function() {
        let token = document.querySelector('#token').getAttribute('value');

        this.lab_id = this.$route.params.id;

        $.getJSON('/api/lab/' + this.lab_id, function(data) {
            this.lab = data;
            this.sitename = data.name;
            $('#title').html(this.sitename);
            $('title').html(this.sitename);
        }.bind(this));

        this.getLabImages();

        setTimeout(function() { $('#csrf-token').val(token); }, 500);
        setTimeout(function() { $('#csrf-token2').val(token); }, 500);
        setTimeout(function() { $('#kontaktfoto').fileinput(); }, 500);
        setTimeout(function() { $('#logo').fileinput(); }, 500);

        // this.whoAmI();
        this.checkImages();
    },

    methods: {

        getLabImages() {
            this.$http.get(`/api/labs/${this.lab_id}/images`)
                .then(response => {
                    this.images = response.data;

                    if (response.data.bilder) {
                        this.images.bilder = _.orderBy(response.data.bilder, 'order', 'asc');
                    }
                    if (response.data.zert) {
                        this.images.zert = _.orderBy(response.data.zert, 'order', 'asc');
                    }
                });
        },

        whoAmI: function() {
            this.$http.get('/api/whoami')
                .then(response => {
                    this.whoami = response.data;
                });
        },

        delete(id) {
            this.$http.delete(`/api/images/${id}`)
                .then(response => {
                    this.getLabImages();
                });
        },

        sortBilderImages(image) {
            this.sortImages(image, 'bilder');
        },

        sortZertImages(image) {
            this.sortImages(image, 'zert');
        },

        sortImages(image, type) {
            let images = this.images[type];

            images.splice(image.newIndex, 0, images.splice(image.oldIndex, 1)[0]);

            let ids = _.map(images, 'id');

            // console.log(ids);

            let form = {
                ids: ids
            };

            this.$http.post(`/api/images/sort`, form)
                .then(response => {
                    // location.reload();
                    // this.images[type] = response.data;
                });
        },

        checkImages: function() {
            let id = this.$route.params.id;
            $.ajax({
                url: '/img/laborbilder/bild1' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.bild1 = false;
                }.bind(this),
                success: function() {
                    this.bild1 = true;
                }.bind(this)
            });
            $.ajax({
                url: '/img/laborbilder/bild2' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.bild2 = false;
                }.bind(this),
                success: function() {
                    this.bild2 = true;
                }.bind(this)
            });
            $.ajax({
                url: '/img/laborbilder/bild3' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.bild3 = false;
                }.bind(this),
                success: function() {
                    this.bild3 = true;
                }.bind(this)
            });
            $.ajax({
                url: '/img/laborbilder/bild4' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.bild4 = false;
                }.bind(this),
                success: function() {
                    this.bild4 = true;
                }.bind(this)
            });
            $.ajax({
                url: '/img/laborbilder/bild5' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.bild5 = false;
                }.bind(this),
                success: function() {
                    this.bild5 = true;
                }.bind(this)
            });
            $.ajax({
                url: '/img/laborbilder/bild6' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.bild6 = false;
                }.bind(this),
                success: function() {
                    this.bild6 = true;
                }.bind(this)
            });
            $.ajax({
                url: '/img/zertifikate/zert1' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.zert1 = false;
                }.bind(this),
                success: function() {
                    this.zert1 = true;
                }.bind(this)
            });
            $.ajax({
                url: '/img/zertifikate/zert2' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.zert2 = false;
                }.bind(this),
                success: function() {
                    this.zert2 = true;
                }.bind(this)
            });
            $.ajax({
                url: '/img/zertifikate/zert3' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.zert3 = false;
                }.bind(this),
                success: function() {
                    this.zert3 = true;
                }.bind(this)
            });
            $.ajax({
                url: '/img/zertifikate/zert4' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.zert4 = false;
                }.bind(this),
                success: function() {
                    this.zert4 = true;
                }.bind(this)
            });
            $.ajax({
                url: '/img/zertifikate/zert5' + id + '_neu.jpg',
                type: 'HEAD',
                error: function() {
                    this.zert5 = false;
                }.bind(this),
                success: function() {
                    this.zert5 = true;
                }.bind(this)
            });
        },

        deleteImage: function(file, name) {
            console.log(file);
            this.$http.post('/api/removeFile', { 'file': file, 'name': name }).then(function(res) {
                console.log('success');
                console.log(res.data);
            }, function(res) {
                console.log('##### error #####');
                console.log(res.data);
            });
            location.reload();
        }
    }
};
