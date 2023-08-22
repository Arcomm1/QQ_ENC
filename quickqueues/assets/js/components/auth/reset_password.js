var create_user = new Vue({

    el: '#reset_password',

    data () {
        return {
            username: "",
            help_topic: false,
            can_submit: false,
        }
    },

    methods: {
        get_random_help_topic: function () {
            axios.get(api_url+'anonymous/misc/get_random_help_topic')
                .then(response => { this.help_topic = response.data.data; });
        },

        form_is_valid: function () {
            if (this.username.length > 2) {
                document.getElementById("reset_password_submit").disabled = false;
            } else {
                document.getElementById("reset_password_submit").disabled = true;
            }
        }
    },

    created () {
        this.get_random_help_topic();
        setInterval( () => this.get_random_help_topic(), 5000);
    },

    watch: {
        username() {
            this.form_is_valid();
        },

    }

});
