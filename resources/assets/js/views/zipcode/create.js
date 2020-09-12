import Countries from './Countries.template.html';

export default {
    template: Countries,

    props: [''],

    data() {
        return {
            edit: false,
            list: [],
            form: {
                id: '',
                country_name: '',
                country_code: '',
            },
            zipform: {
                id: '',
                zip_code: '',

            },
            selected: '',
        };
    },

    created () {
        $('#title').html(this.sitename);
        $('#title').text(this.sitename + ' | Padento.de');
    },

    ready() {
        this.fetch();
    },

    methods: {
        fetch() {
            this.$http.get('/api/countries')
                .then(({data}) => this.list = data);
        },

        create() {
            this.$http.post('/api/countries', this.form)
                .then(response => {
                    this.resetForm();
                    this.edit = false;
                    this.fetch();
                    $('#newcountry').modal('hide');
                });
        },


        createZipCode() {
            this.$http.post('/api/zipcode', this.form)
                .then(response => {
                    this.form.zip_code = '';
                    this.fetch();
                });
        },

        update(id) {
            this.$http.patch('/api/countries/' + id, this.form)
                .then(response => {
                    this.resetForm();
                    this.edit = false;
                    this.fetch();
                    $('#editcountry').modal('hide');
                });
        },

        show(id) {
            this.$http.get('/api/countries/' + id)
                .then(response => {
                    this.form.id = response.data.id;
                    this.form.country_name = response.data.country_name;
                    this.form.country_code = response.data.country_code;
                });

            this.$els.countriesinput.focus();
            this.edit = true;
        },

        delete(id) {
            this.$http.delete('/api/countries/' + id)
                .then(response => this.fetch());
        },
        resetForm(){
            this.form.country_name = '';
            this.form.country_code = '';
        }
    }
}