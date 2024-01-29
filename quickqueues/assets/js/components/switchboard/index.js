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
			this.devices_loading = true;
			axios.get(api_url + 'misc/get_devices')
				.then(response => {
					console.log("Response JSON:", JSON.stringify(response, null, 2));
					this.devices_loading = false;
					let truncatedDevices = {};
					for (let key in response.data.data) {
						if (response.data.data.hasOwnProperty(key)) {
							let deviceString = response.data.data[key];
							// Truncate the string to 18 characters
							truncatedDevices[key] = deviceString.length > 15 ? deviceString.substring(0, 15) + '...' : deviceString;
						}
					}
					this.devices = truncatedDevices;
				})
				.catch(error => {
					// Handle error here if necessary
					console.error("Error fetching devices:", error);
				})
				.finally(() => this.devices_error = false);
		},


        show_exts: function () {
            this.get_extension_states();
        },
		
        show_exts: function (which) {
            $('.btn').removeClass('active');
            $('#exts_'+which).addClass('active');
            this.filter = which;
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
        this.show_exts('all');
        // this.get_extension_states();
        this.refresh_states();
    },

});
