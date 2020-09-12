import Todo from './Todo.template.html';


export default {
    template: Todo,
    props: [''],

    data () {
        return {
            sitename: 'Todo Liste Verwaltung',
            listWithWaiting: [],
            listWithoutWaiting: [],
            editWithoutWaiting: false,
            editWithWaiting: false,

            todoWithWaiting: {
                id: '',
                title: '',
                is_queued: 1
            },
            todoWithoutWaiting: {
                id: '',
                title: '',
                is_queued: 0
            },
            // whoami: false,

            //scroll
            scrolling: false
        };
    },


    created()
    {
        $('#title').html(this.sitename);
        $('title').text(this.sitename + ' | Padento.de');
        // this.whoAmI();
    },

    ready()
    {
        this.fetch();
        window.scrollTo(0, 1000);
    },

    methods: {
        fetch()
        {
            this.$http.get('/api/todo').then(function (response) {
                this.listWithWaiting = response.data.withWaiting;
                this.listWithoutWaiting = response.data.withoutWaiting;

            });
        },

        whoAmI: function() {
            this.$http.get('/api/whoami')
                .then(response => {
                    this.whoami = response.data;
                });
        },

        create(todo){
            this.$http.post('/api/todo', todo)
                .then(response => {
                    this.todoWithoutWaiting.title = '';
                    this.todoWithWaiting.title = '';
                    this.editWithoutWaiting = false;
                    this.editWithWaiting = false;
                    this.fetch();
                });
        },


        update(todo){
            this.$http.patch('/api/todo/' + todo.id, todo)
                .then(response => {
                    this.todoWithoutWaiting.title = '';
                    this.todoWithWaiting.title = '';
                    this.editWithoutWaiting = false;
                    this.editWithWaiting = false;
                    this.fetch();
                });
        },

        fillFormEdit(todo)
        {
            if (todo.is_queued) {
                this.todoWithWaiting.id = todo.id;
                this.todoWithWaiting.title = todo.title;
                this.editWithWaiting = true;
                this.$el.querySelector('input.form-control.WithWaiting').focus();
            }
            else {
                this.todoWithoutWaiting.id = todo.id;
                this.todoWithoutWaiting.title = todo.title;
                this.editWithoutWaiting = true;
                this.$el.querySelector('input.form-control.WithoutWaiting').focus();
            }

        },

        delete(id)
        {
            if(!confirm('Willst du es wirklich löschen?')) {
                return false;
            }

            this.$http.delete('/api/todo/' + id)
                .then(response => {
                    this.fetch();
                });
        },

        sortWithWaitingTasks(task) {
            let tasks = this.listWithWaiting;

            tasks.splice(task.newIndex, 0, tasks.splice(task.oldIndex, 1)[0]);

            let ids = _.map(tasks, 'id');

            let form = {
                ids: ids
            };

            this.$http.post(`/api/todo/sortWithWaiting`, form)
                .then(response => {
                    this.listWithWaiting = _.orderBy(response.data, 'order', 'asc');
                });
        },

        sortWithoutWaitingTasks(task) {
            let tasks = this.listWithoutWaiting;

            tasks.splice(task.newIndex, 0, tasks.splice(task.oldIndex, 1)[0]);

            let ids = _.map(tasks, 'id');

            let form = {
                ids: ids
            };

            this.$http.post(`/api/todo/sortWithoutWaiting`, form)
                .then(response => {
                    this.listWithoutWaiting = _.orderBy(response.data, 'order', 'asc');
                });
        },

        handleTaskMove (/**Event*/evt, /**Event*/originalEvent) {
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
};
