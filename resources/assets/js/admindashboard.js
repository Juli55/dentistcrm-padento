import Labs from './views/labs/Labs.js';
import LabSingle from './views/labs/LabSingle.js';
import LabImages from './views/labs/LabImage.js';
import LabSettings from './views/labs/LabSettings.js';
import NewLab from './views/labs/NewLab.js';

import ContactDashboard from './views/contacts/Contacts.js';
import DentistsDashboard from './views/dentists/DentistsDashboard.js';
import DentistSingle from './views/dentists/DentistSingle.js';
import NewDent from './views/dentists/NewDent.js';

import ContactSingle from './views/contacts/ContactSingle.js';

import SettingProperties from './views/admin/SettingProperties.js';
import Settings from './views/admin/Settings.js';
import SettingsUsers from './views/admin/SettingsUsers.js';
import NewUser from './views/admin/NewUser.js';
import SettingsEmail from './views/admin/SettingsEmail.js';
import SingleProperty from './views/admin/SingleProperty.js';
import SettingsUserEdit from './views/admin/SettingsUserEdit.js';
import AdminLogs from './views/admin/AdminLogs.js';

import Dashboard from './views/dashboard/Dashboard.js';

import Stats from './views/dashboard/Stats.js';


import DentistStats from './views/dashboard/DentistStats.js';


import Calendar from './views/labs/Calendar.js';
import MyDates from './views/labs/MyDates.js';
import DentistDates from './views/labs/DentistDates.js';
import GuidesPage from './views/pages/GuidesPage.js';
import DownloadsPage from './views/pages/DownloadsPage.js';
import PartnerPage from './views/pages/PartnerPage.js';
import Docs from './views/pages/DocsPage.js';
import Upgrades from './views/pages/Upgrades.js';

import Tools from './views/tools/Tools.js';
import Todo from './views/todo/Todo.js';
import Countries from './views/countries/Countries.js';
import DentistsContacts from './views/dentistscontacts/DentistsContacts.js';
import DentistContactSingle from './views/dentistscontacts/DentistContactSingle.js';

import LabUsers from './views/labUsers/LabUsers.js';
import Links from './views/links/Links.js';

import _ from 'lodash';

window._ = _;

import Sortable from 'vue-sortable';

Vue.config.silent = true;
Vue.config.devtools = true;
Vue.config.debug = false;

Vue.use(Sortable);

Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');

// Vue.config.silent = false;
// Vue.config.devtools = true;
// Vue.config.debug = true;

var Test = Vue.extend({
    template: '<p>This is test!</p>'
});
var LabMine = Vue.extend({
    template: '<p>This is my lab!</p>'
});

import Authorizer from './mixins/Authorizer';

Vue.mixin(Authorizer);

var App = Vue.extend({});

var router = new VueRouter({
    history: true
});

router.afterEach(function (transition) {
    if (transition.from.name == 'admin.contactSingle') {
        // console.log(transition.from.params.id);
        var data = {reset: 'yes', patient_id: transition.from.params.id};
        Vue.http.post('/api/contact/used', data).then(function (response) {
            console.log(response.data);
        }, function (response) {
            console.log(response.data);
            $('#debug').addClass('active').find('.debugged-content').html(response.data);
            Messenger().post({
                message: 'Fehler beim entsperren des Kontaktes',
                type: 'error',
                // showCloseButton: true
            });
        });
    }
});

Vue.directive('selectpicker', {
    twoWay: true,
    deep: true,

    bind: function () {

        $(this.el).selectpicker().on("change", function (e) {
            this.set($(this.el).val());
        }.bind(this));
    },
    update: function (value) {
        $(this.el).selectpicker('refresh').trigger("change");
    },
    unbind: function () {
        $(this.el).off().selectpicker('destroy');
    }
});

Vue.directive('fileinput', {
    twoWay: true,
    deep: true,

    bind: function () {
        $(this.el).fileinput();
    },
    update: function (value) {
        // $(this.el).selectpicker('refresh').trigger("change");
    },
    unbind: function () {
        // $(this.el).off().selectpicker('destroy');
    }
});

// Check this: http://www.giphy.com/gifs/3o7qEcaW9OI6cN4lfq
//<input data-input="labdate" v-datetimepicker="contact.labdate" v-model="contact.labdate" @blur="saveContact" @change="saveContact" @keyup.enter="saveContact">
Vue.directive('datetimepicker', {
    twoWay: true,
    deep: true,

    bind: function () {
        var self = this;
        $(this.el).datetimepicker({
            locale: 'de',
            sideBySide: true,
        }).on("dp.change", function (e) {
            // console.log($(this.el).val());
            // self.loadContact();
        }.bind(this));

    },
    update: function (value) {
        // this.set($(this.el).val());
        // $(this.el).val(value).trigger('change')

    },
    unbind: function () {
    }
});

Vue.directive('datepicker', {
    twoWay: true,
    deep: true,

    bind: function () {
        var self = this;
        $(this.el).datetimepicker({
            locale: 'de',
            format: 'DD.MM.YYYY'
        }).on("dp.change", function (e) {
            // console.log($(this.el).val());
            // self.loadContact();
        }.bind(this));

    },
    update: function (value) {
        // this.set($(this.el).val());
        // $(this.el).val(value).trigger('change')

    },
    unbind: function () {
    }
});

Vue.directive('test', {
    twoWay: true,
    deep: true,

    bind: function () {
        var self = this;
        // $()
    },
    update: function (value) {
        this.set($(this.el).val());
        $(this.el).val(value).trigger('change')

    },
    unbind: function () {
    }
});
router.beforeEach(function (transition) {
    // console.log(transition)
    if (transition.to.path === '/app/dashboard' && transition.to.router.app.whoami.lab.length && transition.to.router.app.whoami.lab[0].membership == 5) {
        transition.redirect({
            name: 'admin.dentistsContacts'
        })

    } else {
        transition.next()
    }
});

Vue.filter('date', value => {
    return moment.utc(value).local().format('DD.MM.YYYY')
});


router.map(
    {
        '/app': {
            component: {
                ready: function () {
                    this.$router.go({name: 'home', params: {}});
                }
            }
        },
        '/app/labore/': {
            name: 'admin.labs',
            component: Labs
        },
        '/app/zahnaerzte/': {
            name: 'admin.dentists',
            component: DentistsDashboard
        },
        '/app/dashboard/': {
            name: 'home',
            component: Dashboard
        },
        '/app/stats/': {
            name: 'stats',
            component: Stats
        },


        '/app/dentiststats/': {
            name: 'dentiststats',
            component: DentistStats
        },

        '/app/test/': {
            name: 'test',
            component: Test
        },
        '/app/termine/': {
            name: 'admin.calendar',
            component: Calendar
        },
        '/app/meine-termine': {
            name: 'my.calendar',
            component: MyDates
        },
        '/app/lab-users': {
            name: 'lab.users',
            component: LabUsers
        },
        '/app/zahnartze-termine': {
            name: 'my.dentistsCalendar',
            component: DentistDates
        },
        '/app/labore/:id': {
            name: 'admin.labSingle',
            component: LabSingle
        },
        '/app/labor-bilder/:id': {
            name: 'lab.edit.images',
            component: LabImages
        },
        '/app/labore/neues-labor/': {
            name: 'admin.newlab',
            component: NewLab
        },
        '/app/zahnaerzte/:id': {
            name: 'admin.dentistSingle',
            component: DentistSingle
        },
        '/app/zahnaerzte/neuer-zahnarzt': {
            name: 'admin.newdent',
            component: NewDent
        },
        '/app/kontakte/': {
            name: 'admin.contacts',
            component: ContactDashboard
        },
        '/app/kontakt/:id': {
            name: 'admin.contactSingle',
            component: ContactSingle
        },
        '/app/settings/': {
            name: 'admin.settings.index',
            component: Settings
        },
        '/app/settings/properties/': {
            name: 'admin.settings.properties',
            component: SettingProperties
        },
        '/app/settings/single-property/:id': {
            name: 'admin.settings.single-property',
            component: SingleProperty
        },
        '/app/settings/users/': {
            name: 'admin.settings.users',
            component: SettingsUsers
        },
        '/app/settings/users/:id/edit': {
            name: 'admin.settings.users.edit',
            component: SettingsUserEdit
        },
        '/app/neuer-nutzer/': {
            name: 'admin.newuser',
            component: NewUser
        },
        '/app/settings/mails/': {
            name: 'admin.settings.mails',
            component: SettingsEmail
        },
        '/mein-labor/': {
            name: 'lab.mine',
            component: LabMine
        },
        '/app/labsettings/': {
            name: 'lab.settings',
            component: LabSettings
        },
        '/app/logs/': {
            name: 'admin.logs',
            component: AdminLogs
        },
        '/app/guides/': {
            name: 'guides',
            component: GuidesPage
        },
        '/app/downloads/': {
            name: 'downloads',
            component: DownloadsPage
        },
        '/app/tools/': {
            name: 'tools',
            component: Tools
        },
        '/app/partner/': {
            name: 'partner',
            component: PartnerPage
        },
        '/app/dokumentation/': {
            name: 'docs',
            component: Docs
        },
        '/app/upgrades/': {
            name: 'upgrades',
            component: Upgrades
        },
        '/app/countries/': {
            name: 'countries',
            component: Countries
        },
        '/app/todo/': {
            name: 'todo',
            component: Todo
        },

        '/app/dentists/': {
            name: 'admin.dentistsContacts',
            component: DentistsContacts
        },

        '/app/dentist/:id': {
            name: 'admin.dentistContactSingle',
            component: DentistContactSingle
        },

        '/app/links/': {
            name: 'links',
            component: Links
        },
    }
);

router.start(App, '#app');

$(function () {
    $('body').tooltip({selector: '[data-toggle="tooltip"]'});
});

























































