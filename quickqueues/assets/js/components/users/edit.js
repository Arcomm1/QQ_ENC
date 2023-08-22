var edit_user = new Vue({

    el: '#edit_user',

    data () {
        return {
            username: "",
            display_name: "",
            email: "",
            username_err: "",
            displayname_err: "",
            email_err: "",
            password: "",
            associated_agent_id: "",
            password_confirm: "",
            password_err: "",
            password_confirm_err: "",
            role: "",
            role_err: "",
            emailre: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
            can_submit: true,
            enabled: "",
            roles: {},
            queue_to_add: false,
            user_queues: false,
            num_user_queues: 0,
            agent_to_add: false,
            user_agents: false,
            num_user_agents: 0,
            extension: "",
            extension_err: "",
        }
    },

    methods: {
        form_is_valid: function() {
            if (this.displayname_err        ||
                this.email_err              ||
                !this.display_name          ||
                !this.email                 ||
                this.password_err           ||
                this.password_confirm_err
                ) {
                this.can_submit = false;
            } else {
                if (this.password != this.password_confirm) {
                    this.can_submit = false;
                } else {
                    this.can_submit = true;
                }
            }
        },

        get_user: function() {
            axios.post(api_url+'user/get/'+user_id, {})
                .then(
                    response => {
                        if (response.data.status == "OK") {
                            this.username = response.data.data.name;
                            if (response.data.data.display_name) {
                                this.display_name = response.data.data.display_name;
                            }
                            if (response.data.data.email) {
                                this.email = response.data.data.email;
                            }
                            this.role = response.data.data.role;
                            this.enabled = response.data.data.enabled;
                            this.extension = response.data.data.extension;
                            this.associated_agent_id = response.data.data.associated_agent_id;

                        } else {
                            this.user_exists = false;
                        }

                    })
                .finally(() => this.agent_loading = false)
        },

        get_roles: function() {
            axios.get(api_url+'misc/get_roles/')
                .then(response => {
                    this.roles = response.data.data;
                    for (r in this.roles) {
                        $('#role').append(new Option(lang[this.roles[r]], this.roles[r]));
                    }
                })
        },

        delete_user: function() {
            if (confirm(lang['are_you_sure_delete_user'])) {
                axios.get(api_url+'user/delete/'+user_id)
                    .then(response => {
                        if (response.data.status == "OK") {
                            window.location.href = app_url+'/users/index';
                        } else {
                            send_notif(lang['user_delete_fail'], 'danger');
                        }
                    })
            }
        },

        de_activate: function() {
            if (this.enabled == 'yes') {
                action = 'deactivate';
            } else {
                action = 'activate';
            }
            if (confirm(lang['are_you_sure_deactivate_user'])) {
                axios.get(api_url+'user/de_activate/'+user_id)
                    .then(response => {
                        if (response.data.status == "OK") {
                            send_notif(lang['user_edit_success'], 'success');
                            this.get_user();
                        } else {
                            send_notif(lang['user_edit_fail'], 'danger');
                        }
                    })
            }
        },


        assign_queue: function() {
            axios.get(api_url+'user/assign_queue/'+user_id+'/'+this.queue_to_add)
                .then(response => {
                    if (response.data.status == "OK") {
                        this.get_queues();
                        send_notif(lang['user_queue_assign_success'], 'success');
                    } else {
                        send_notif(lang['user_queue_assign_fail'], 'danger');
                    }
                });
        },


        assign_agent: function() {
            axios.get(api_url+'user/assign_agent/'+user_id+'/'+this.agent_to_add)
                .then(response => {
                    if (response.data.status == "OK") {
                        this.get_agents();
                        send_notif(lang['user_agent_assign_success'], 'success');
                    } else {
                        send_notif(lang['user_agent_assign_fail'], 'danger');
                    }
                });
        },


        unassign_queue: function(queue_id) {
            axios.get(api_url+'user/unassign_queue/'+user_id+'/'+queue_id)
                .then(response => {
                    if (response.data.status == "OK") {
                        this.get_queues();
                        send_notif(lang['user_queue_unassign_success'], 'success');
                    } else {
                        send_notif(lang['user_queue_unassign_fail'], 'danger');
                    }
                });
        },

        unassign_agent: function(agent_id) {
            axios.get(api_url+'user/unassign_agent/'+user_id+'/'+agent_id)
                .then(response => {
                    if (response.data.status == "OK") {
                        this.get_agents();
                        send_notif(lang['user_agent_unassign_success'], 'success');
                    } else {
                        send_notif(lang['user_agent_unassign_fail'], 'danger');
                    }
                });
        },


        get_queues: function() {
            axios.get(api_url+'user/get_queues/'+user_id)
                .then(response => {
                    this.user_queues = response.data.data;
                    this.num_user_queues = Object.keys(response.data.data).length;
                });
        },


        get_agents: function() {
            axios.get(api_url+'user/get_agents/'+user_id)
                .then(response => {
                    this.user_agents = response.data.data;
                    this.num_user_agents = Object.keys(response.data.data).length;
                });
        }

    },

    watch: {

        display_name() {
            if (this.display_name.length < 6) {
                this.displayname_err = lang['display_name_short'];
            } else {
                this.displayname_err = "";
            }
            this.form_is_valid();
        },

        email() {
            if (this.email.length < 6) {
                this.email_err = lang['email_short'];
            } else {
                if (this.emailre.test(String(this.email).toLowerCase())) {
                    this.email_err = "";
                } else {
                    this.email_err = lang['email_invalid'];
                }
            }
            this.form_is_valid();
        },

        password() {
            if (this.password.length < 6 && this.password.length > 0) {
                this.password_err = lang['password_short'];
            } else {
                this.password_err = "";
            }
            this.form_is_valid();
        },

        password_confirm() {
            if (this.password_confirm != this.password) {
                this.password_confirm_err = lang['password_mismatch'];
            } else {
                this.password_confirm_err = "";
            }
            this.form_is_valid();
        },

        queue_to_add() {

        },

        agent_to_add() {

        },

    },

    mounted () {
        this.get_user();
        this.get_queues();
        this.get_agents();
    },

    created () {
        this.get_roles();
    }


})
