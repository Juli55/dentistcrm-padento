import Links from './Links.template.html';

export default {
    template: Links,

    components: {},

    data() {
        return {
            linksData: [],
            links: [],
            selectedParentLink: '',
            newLink: {},
            editLink: {},
            demoData: {},
            linksList: [],

            //link dragging
            scrolling: false
        };
    },

    created() {
        this.getLinks();
        this.getParentLinks();
        this.demoTest();
    },

    ready() {

    },

    methods: {
        demoTest() {
            this.$http.get('/api/links/prepareLinks').then(response => {
                this.demoData = response.data;
            });
        },
        getLinks() {
            this.data = {
                searchfor: this.search
            };
            this.$http.get('/api/links', this.data).then(response => {
                this.linksData = response.data;
                this.linksList = response.data;
            });
        },
        getParentLinks() {
            this.$http.get('/api/links/getParentLinks').then(response => {
                this.links = response.data;
            });
        },
        saveLink: function () {
            this.$http.post('/api/links', this.newLink).then(function (response) {
                this.getLinks();
                $('#newLink').modal('hide');
            });
        },
        updateLink: function () {
            this.$http.put('/api/links/' + this.editLink.id, this.editLink).then(function (response) {
                //window.location.reload();
                this.getLinks();
                $('#editLink').modal('hide');
            });
        },
        editLinkForm(link) {
            this.editLink = null;
            this.editLink = link;
            $('#editLink').modal('show');
        },
        deleteLink(id) {
            if (!confirm('Willst du es wirklich löschen?')) {
                return false;
            }
            this.$http.delete('/api/links/' + id).then(function (response) {
                this.getLinks();
                //window.location.reload();
            });
        },
        sortLinks(link) {
            let links = this.linksList;

            links.splice(link.newIndex, 0, links.splice(link.oldIndex, 1)[0]);

            let ids = _.map(links, 'id');

            let form = {
                ids: ids
            };

            this.$http.post(`/api/links/sort`, form)
                .then(response => {
                    this.linksList = _.orderBy(response.data, 'order', 'asc');
                });
        },

        handleLinkMove (/**Event*/evt, /**Event*/originalEvent) {
            if(evt.relatedRect.top < 100) {
              let self = this;
              if(!this.scrolling) {
                var container = $(".content");
                $(".content").animate({ scrollTop:  $(".content").scrollTop()-200}, 1000, function () {
                  self.scrolling = true
                }).promise().done(function () {
                  self.scrolling = false
                });
              }
            }
      	},
    },

    computed: {
        formReady: function () {
            return (
                this.newLink.title != null &&
                this.newLink.url != null
            );
        },
        formEditReady: function () {
            return (
                this.editLink.title != null &&
                this.editLink.url != null
            );
        },
    },

    watch: {
        search: function (value) {
            this.getLinks();
        }
    }
};
