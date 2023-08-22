var agent_crm = new Vue({
    el: '#agent_crm',
    data () {
        return {
            agent: {},
            agent_error: false,
            agent_loading: true,

            realtime_status: {},
            realtime_status_loading: true,
            realtime_status_error: false,

            current_call: {},
            current_call_loading: true,
            current_call_error: false,

            extension_states: [],
            extension_states_loading: true,
            extension_states_error: false,

            filter: 'all',

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

        get_agent_data: function() {
            axios.post(api_url+'agent/get/'+agent_id, {})
                .then(
                    response => {
                        this.agent = response.data.data;
                        this.selected_queue_id = this.agent.primary_queue_id;
                    })
                .finally(() => this.agent_loading = false)
        },

        get_realtime_status: function() {
            axios.post(api_url+'agent/get_realtime_status/'+agent_id, this.form_data)
                .then(
                    response => {
                        this.realtime_status = response.data.data;
                    })
                .finally(() => this.realtime_status_loading = false)
        },

        get_current_call: function() {
            axios.post(api_url+'agent/get_current_call/'+agent_id, this.form_data)
                .then(
                    response => {
                        this.current_call = response.data.data;
                    })
                .finally(() => this.current_call_loading = false)
        },

        get_extension_states: function () {
            this.extension_states_loading = true,

            axios.post(api_url+'misc/get_extension_states/')
                .then(response => {
                    this.extension_states_loading = false;
                    this.extension_states = [];
                    for (e in response.data.data) {
                        if (response.data.data[e].Context == 'ext-local') {
                            if (this.filter == 'all') {
                                this.extension_states.push([response.data.data[e].Exten, response.data.data[e]]);
                            }

                            if (this.filter == 'free') {
                                if (response.data.data[e].Status == 0) {
                                    this.extension_states.push([response.data.data[e].Exten, response.data.data[e]]);
                                }
                            }

                            if (this.filter == 'on_call') {
                                if (response.data.data[e].Status == 1 || response.data.data[e].Status == 8 || response.data.data[e].Status == 16) {
                                    this.extension_states.push([response.data.data[e].Exten, response.data.data[e]]);
                                }
                            }

                            if (this.filter == 'busy') {
                                if (response.data.data[e].Status == 2) {
                                    this.extension_states.push([response.data.data[e].Exten, response.data.data[e]]);
                                }
                            }

                            if (this.filter == 'unavailable') {
                                if (response.data.data[e].Status == 4) {
                                    this.extension_states.push([response.data.data[e].Exten, response.data.data[e]]);
                                }
                            }
                        }
                    }
                    this.extension_states.sort();
                })
                .finally(() => this.extension_states_error = false);
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

        show_exts: function (which) {
            $('.btn').removeClass('active');
            $('#exts_'+which).addClass('active');
            this.filter = which;
            this.get_extension_states();
        },

        refresh_states: function () {
            setInterval( () => this.get_extension_states(), 3000);
            setInterval( () => this.get_agent_data(), 60000);
            setInterval( () => this.get_realtime_status(), 2000);
            setInterval( () => this.get_current_call(), 2000);
        },
    },

    mounted () {
        this.get_devices();
    },

    created () {
        this.show_exts('all');
        // this.get_extension_states();
        this.get_agent_data();
        this.get_realtime_status();
        this.get_current_call();
        this.refresh_states();
    },

});

$('#nav_switchboard').addClass('active');
