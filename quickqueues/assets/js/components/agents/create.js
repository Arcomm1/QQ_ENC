var create_agent = new Vue({

    el: '#create_agent',
    
    data () {
        return {
            name: "",
            name_err: "",
            queue: "",
            queue_err: "",
            can_submit: false,
            extension: "",
            extension_err: "",
        }
    },

    methods: {
        form_is_valid: function() {
            if (
                this.name_err        ||
                this.queue_err       ||
                this.extension_err   ||
                !this.name           ||
                !this.queue          ||
                !this.extension
                ) {
                this.can_submit = false;
            } else {
                this.can_submit = true;
            }
        }

    },

    watch: {
        name() {
            if (this.name.length < 6) {
                this.name_err = lang['name_short'];
            } else {
                this.name_err = "";
            }
            this.form_is_valid();
        },

        queue() {
            if (!this.queue) {
                this.queue_err = lang['select_queue'];
            } else {
                this.queue_err = "";
            }
            this.form_is_valid();
        },

        extension() {
            if (this.extension.length < 3) {
                this.extension_err = lang['extension_short'];
            } else {
                this.extension_err = "";
            }
            this.form_is_valid();
        },

    },

})