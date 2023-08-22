var create_user = new Vue({

    el: '#create_user',

    data () {
        return {
            username: JSON.parse(agent).extension,
            display_name: JSON.parse(agent).display_name,
            email: "",
            username_err: "",
            displayname_err: "",
            email_err: "",
            password: "",
            password_confirm: "",
            password_err: "",
            password_confirm_err: "",
            role: "",
            role_err: "",
            emailre: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
            can_submit: false,
            user_exists: true,
            extension: JSON.parse(agent).extension,
            extension_err: "",
        }
    },

    methods: {
        form_is_valid: function() {
            if (this.password_err           ||
                this.password_confirm_err   ||
                this.username_err           ||
                this.displayname_err        ||
                this.email_err              ||
                this.role_err               ||
                !this.username              ||
                !this.password              ||
                !this.display_name          ||
                !this.password_confirm      ||
                !this.email                 ||
                !this.role                  ||
                this.user_exists) {
                this.can_submit = false;
            } else {
                this.can_submit = true;
            }
        },

        get_roles: function() {
            axios.get(api_url+'misc/get_roles/')
                .then(response => {
                    this.roles = response.data.data;
                    for (r in this.roles) {
                        $('#role').append(new Option(lang[this.roles[r]], this.roles[r]));
                    }
                    if (from_agent) {
                        this.role = 'agent';
                    }
                })
        },

        check_user: function(username) {
            axios.post(api_url+'user/get_by_name/'+username, {})
                .then(
                    response => {
                        if (response.data.status == "OK") {
                            this.user_exists = true;
                        } else {
                            this.user_exists = false;
                        }
                    })
                .finally(() => this.agent_loading = false)
        },

        check_from_agent: function() {
            if (from_agent) {
                $('#form_create_user').append('<input type="hidden" name="from_agent_id" value="'+from_agent+'" />');
            }
        }

    },

    watch: {
        username() {
            if (this.username.length < 3) {
                this.username_err = lang['username_short'];
            } else {
                this.check_user(this.username);
                if (this.user_exists) {
                    this.username_err = lang['user_exists'];

                } else {
                    this.username_err = "";

                }
            }
            this.form_is_valid();
        },

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
            if (this.password.length < 6) {
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

        role() {
            if (this.role.length < 3) {
                this.role_err = lang['select_role'];
            } else {
                this.role_err = "";
            }
            this.form_is_valid();
        },

    },

    created () {
        this.check_user(this.username);
        this.get_roles();
        this.check_from_agent();
    }


})
