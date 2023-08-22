var switchboard = new Vue({
    el: '#switchboard',
    data () {
        return {
            extension_states: [],
            extension_states_loading: true,
            extension_states_error: false,
            exts: [],

            devices: {},
            devices_loading: true,
            devices_error: false,

            state_class_map: {
                0: 'bg-success',
                1: 'bg-danger',
                2: 'bg-danger',
                4: 'bg-secondary',
                8: 'bg-warning',
                16: 'bg-warning'
            },
        }
    },

    methods: {
        get_extension_states: function () {
            this.extension_states_loading = true,

            axios.post(api_url+'misc/get_extension_states/')
                .then(response => {
                    this.extension_states_loading = false;
                    this.extension_states = [];
                    for (e in response.data.data) {
                        if (response.data.data[e].Context == 'from-internal') {
                            if (response.data.data[e].Status == '4') {
                                this.extension_states.push([response.data.data[e].Exten.substring(10), response.data.data[e]]);
                                this.exts.push(response.data.data[e].Exten.substring(10));
                                console.log("unavailable");
                                console.log(this.exts);
                            }
                        }
                    }
                    for (e in response.data.data) {
                        if (response.data.data[e].Context == 'ext-local') {
                            if (this.exts.includes(response.data.data[e].Exten) === false) {
                                console.log("other");
                                this.extension_states.push([response.data.data[e].Exten, response.data.data[e]]);
                                console.log(this.exts);
                            }
                        }
                    }
                    this.extension_states.sort();
                    console.log(this.extension_states);

                })
                .finally(() => this.extension_states_error = false, this.exts = [])

        },


        get_devices: function () {
            this.devices_loading = true,
            axios.get(api_url+'misc/get_devices')
                .then(response => {
                    this.devices_loading = false;
                    this.devices = response.data.data;
                })
                .finally(() => this.devices_error = false)
        },

        show_exts: function () {
            this.get_extension_states();
        },

        refresh_states: function () {
            setInterval( () => this.get_extension_states(), 3000);
        },
    },

    mounted () {
        this.get_devices();
    },

    created () {
        this.show_exts();
        // this.get_extension_states();
        this.refresh_states();
    },

});


