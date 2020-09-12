module.exports = {
    computed: {
        application() {
            return window.Application;
        },

        whoami() {
            return this.application.user;
        },

        roles() {
            if (this.whoami && this.whoami.roles.length) {
                return this.whoami.roles;
            }

            return [];
        },

        isAdmin() {
            return this.hasRole('admin');
        },

        isUser() {
            return this.hasRole('user');
        },

        isLab() {
            return this.hasRole('lab');
        },

        isDent() {
            return this.hasRole('dent');
        },

        isCrmUser() {
            return this.hasRole('crm-user');
        }
    },

    methods: {
        hasRole(role) {
            return !! _.find(this.roles, {name: role});
        },

        hasRoles(roles, requireAll = false) {
            let authorized = false;

            for(let i=0; i<roles.length; i++) {
                authorized = this.hasRole(roles[i]);

                if (!requireAll && authorized) {
                    return true;
                }
            }

            return authorized;
        }
    }
};