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

            subcategories: {},

            customer_info: {},
            customer_api_token: "",
        }
    },

    methods: {
        vendoo_api_auth: function () {

        },

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
            axios.post(api_url+'agent/gorgia_get_last_call/'+agent_id)
                .then(
                    response => {
                        this.current_call = response.data.data;
                        if (this.current_call) {
                            this.loaded_call = 1;
                            this.call_in_progress = this.current_call;
                            this.future_call.uniqueid = this.current_call.uniqueid;

                            if (!this.future_call.comment) {
                                this.future_call.comment = this.current_call.comment;
                            }
                            if (!this.future_call.service_id) {
                                this.future_call.service_id = this.current_call.service_id;
                            }
                            if (!this.future_call.service_product_id) {
                                this.future_call.service_product_id = this.current_call.service_product_id;
                            }
                            if (!this.future_call.service_product_type_id) {
                                this.future_call.service_product_type_id = this.current_call.service_product_type_id;
                            }
                            if (!this.future_call.service_product_subtype_id) {
                                this.future_call.service_product_subtype_id = this.current_call.service_product_subtype_id;
                            }

                        }
                        if (this.loaded_call == 1) {
                            this.get_calls();
                        }

                        if ('Channel' in this.call_in_progress) {
                            if (this.call_in_progress.CallerIDNum.length > 4 && !dids.includes(this.call_in_progress.CallerIDNum)) {
                                this.call_in_progress.RealCaller = this.call_in_progress.CallerIDNum;
                            } else {
                                if (this.call_in_progress.ConnectedLineNum.length > 4 && !dids.includes(this.call_in_progress.ConnectedLineNum)) {
                                    this.call_in_progress.RealCaller = this.call_in_progress.CallerIDNum;
                                }
                            }
                        }
                    })
                .finally(() => this.current_call_loading = false)
        },

        get_calls: function () {


            axios.post(api_url+'recording/get_by_number/'+this.current_call.src,)
                .then(
                    response => {
                        this.calls = response.data.data
                    })
                .finally(() => this.calls_loading = false);

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

        load_subcategories: function (category_id) {
            axios.get(api_url+'config/get_call_subcategories/'+category_id)
                .then(response => {
                    $('#subcategory_id').empty();
                    for (c in response.data.data) {
                        $('#subcategory_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
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
        this.get_realtime_status();
        this.get_current_call();
    },

    mounted () {
        this.refresh_realtime_data();
    },

});

$('#nav_workspace').addClass('active')
