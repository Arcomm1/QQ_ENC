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

            loaded_call: 0,
            call_in_progress: {},
            future_call: {},

            calls: {},
            calls_loading: true,
            calls_error: false,

            contact: {},
            contact_loading: true,
            contact_error: false,

            future_call: {},
        }
    },

    methods: {
        get_agent_data: function () {
            axios.post(api_url+'agent/get/'+agent_id, {})
                .then(
                    response => {
                        this.agent = response.data.data;
                        this.selected_queue_id = this.agent.primary_queue_id;
                    })
                .finally(() => this.agent_loading = false)
        },

        get_realtime_status: function () {
            axios.post(api_url+'agent/get_realtime_status/'+agent_id)
                .then(
                    response => {
                        this.realtime_status = response.data.data;
                    })
                .finally(() => this.realtime_status_loading = false)
        },

        get_current_call: function () {
            axios.post(api_url+'agent/get_current_call/'+agent_id)
                .then(
                    response => {
                        this.current_call = response.data.data;
                        if (this.current_call[0]) {
                            this.loaded_call = 1;
                            this.call_in_progress = this.current_call[0];
                            if (this.call_in_progress.Linkedid != this.future_call.uniqueid) {
                                this.future_call = {};
                                this.future_call.uniqueid = this.call_in_progress.Linkedid;
                            }
                        }
                        if (this.loaded_call == 1) {
                            this.get_calls();
                        }

                    })
                .finally(() => this.current_call_loading = false)
        },


        get_calls: function () {
            var num;
            if (!dids.includes(this.call_in_progress.ConnectedLineNum)) {
                num = this.call_in_progress['ConnectedLineNum'];
            } else {
                num = this.call_in_progress['CallerIDNum'];
            }

            axios.post(api_url+'recording/get_by_number/'+num.replace(/\D/g,'')+'/25',)
                .then(
                    response => {
                        this.calls = response.data.data
                    })
                .finally(() => this.calls_loading = false);

            axios.post(api_url+'crocobet_contact/get_by_number/'+num.replace(/\D/g,''))
                .then(
                    response => {
                        this.contact = response.data.data;
                    })
                .finally(() => this.contact_loading = false)

        },

        save_current_call: function () {

            console.log(this.future_call);
            console.log(this.future_call.category_id);

            future_call_form = new FormData();
            future_call_form.append('status', this.future_call.status);
            future_call_form.append('category_id', this.future_call.category_id);
            future_call_form.append('comment', this.future_call.comment);
            future_call_form.append('priority', this.future_call.priority);
            future_call_form.append('curator_id', this.future_call.curator_id);
            future_call_form.append('subcategory_id', this.future_call.subcategory_id);
            future_call_form.append('uniqueid', this.future_call.uniqueid);

            future_call_form.append('service_id', this.future_call.service_id);
            future_call_form.append('service_product_id', this.future_call.service_product_id);
            future_call_form.append('service_product_type_id', this.future_call.service_product_type_id);
            future_call_form.append('service_product_subtype_id', this.future_call.service_product_subtype_id);

            console.log(future_call_form)


            axios.post(api_url+'recording/create_future_event', future_call_form);
            send_notif(lang['call_update_success'], 'success');
        },

        load_service_products: function (service_id) {
            axios.get(api_url+'config/get_service_products/'+service_id)
                .then(response => {
                    $('#service_product_id').empty();
                    $('#service_product_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#service_product_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },


        load_service_product_types: function (service_product_id) {
            axios.get(api_url+'config/get_service_product_types/'+service_product_id)
                .then(response => {
                    $('#service_product_type_id').empty();
                    $('#service_product_type_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#service_product_type_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },


        load_service_product_subtypes: function (service_product_type_id) {
            axios.get(api_url+'config/get_service_product_subtypes/'+service_product_type_id)
                .then(response => {
                    $('#service_product_subtype_id').empty();
                    $('#service_product_subtype_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#service_product_subtype_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },


        refresh_realtime_data: function () {
            setInterval( () => this.get_agent_data(), 60000);
            setInterval( () => this.get_realtime_status(), 2000);
            setInterval( () => this.get_current_call(), 2000);
        },
    },

    created () {
        this.get_agent_data();
        this.get_realtime_status()
        this.get_current_call()
    },

    mounted () {
        this.refresh_realtime_data();
    },

});

$('#nav_workspace').addClass('active')
