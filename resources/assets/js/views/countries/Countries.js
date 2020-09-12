import Countries from './Countries.template.html';

export default {
    template: Countries,

    props: [''],

    data() {
        return {
            sitename: 'Postleitzahlen',
            edit: false,
            list: [],
            zipCodes: [],
            search: '',
            form: {
                id: '',
                country_name: '',
                country_code: '',
                zip_code: '',
            },

            zipForm: {
                zip_code: '',
                country_id: ''
            },

            selectedCountry: {
                country_name: 'Deutschland',
                id: 1,
                country_code: 'DE',
            },
        };
    },

    created () {
        $('#title').html(this.sitename);
        $('#title').text(this.sitename + ' | Padento.de');
    },

    ready() {
        this.fetch();
        this.selectCountry(this.selectedCountry);
    },

    methods: {
        fetch() {
            this.$http.get('/api/countries')
                .then(({data}) => this.list = data);
        },

        selectCountry(country) {
            this.selectedCountry = country;
            this.search = '';
            this.zipCodes = [];
        },

        searchZipCodes() {
            this.$http.get('/api/zipcode', {query: this.search, country_id: this.selectedCountry.id})
                .then(({data}) => this.zipCodes = data);
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
            this.zipForm.country_id = this.selectedCountry.id;
            this.$http.post('/api/zipcode', this.zipForm)
                .then(response => {
                    this.zipForm.zip_code = '';
                    this.selectCountry(this.selectedCountry);
                });
        },

        deleteZipCode(id) {
            this.$http.delete('/api/zipcode/' + id)
                .then(response => this.selectCountry(this.selectedCountry));
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

        resetForm() {
            this.form.country_name = '';
            this.form.country_code = '';
        }
    }
}