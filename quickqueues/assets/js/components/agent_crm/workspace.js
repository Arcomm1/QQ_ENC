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

            future_call: {},

            customer_info: {},

            tickets: {},
            tickets_loading: true,
            tickets_error: false,

            dnd_subjects: {},
            dnd_subjects_select: '',
            agent_dnd_status: '',
            dnd_end_status: '',
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
            axios.post(api_url+'agent/vendoo_get_last_call/'+agent_id)
                .then(
                    response => {
                        this.current_call = response.data.data;
                        this.call_in_progress=response.data.current_call;

                        //this.call_in_progress_time=10;

                        //At this moment this statement is very suspicious (Zura)
                        if (this.current_call) {

                            this.loaded_call = 1;
                            this.call_in_progress = this.current_call;

                            this.future_call.uniqueid = this.current_call.Uniqueid;

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
                            this.get_tickets();
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
             //this.get_customer_info();
        },

        get_customer_info: function () {
            axios.post(api_url+'misc/get_customer_info/'+this.call_in_progress.src, {}) //Fake (Zura)
                .then(
                    response => {
                        this.customer_info = response.data.data;
                    })
        },

        get_calls: function () {
            var num;
            if (!dids.includes(this.call_in_progress.ConnectedLineNum)) {
                num = this.call_in_progress['ConnectedLineNum'];
            } else {
                num = this.call_in_progress['CallerIDNum'];
            }

            //axios.post(api_url+'recording/get_by_number/'+num.replace(/\D/g,'')+'/25',)
            axios.post(api_url+'recording/get_by_number/'+num)
                .then(
                    response => {
                        this.calls = response.data.data
                    })
                .finally(() => this.calls_loading = false);

        },

        get_tickets: function () {
            axios.post(api_url+'ticket/get_by_number/'+this.current_call.src,)
                .then(
                    response => {
                        this.tickets = response.data.data
                    })
                .finally(() => this.tickets_loading = false);

        },

        assign_call_to_ticket: function (ticket_id) {
            axios.post(api_url+'recording/add_to_ticket/'+this.current_call.uniqueid+'/'+ticket_id,)
                .then(
                    response => {
                        send_notif(response.data.message);
                    })
                .finally(() => this.tickets_loading = false);
        },

        save_current_call: function () {
            alert('work');
            console.log(this.future_call);
            console.log(this.future_call.category_id);

            future_call_form = new FormData();
            future_call_form.append('status', this.future_call.status);
            future_call_form.append('category_id', this.future_call.category_id);
            future_call_form.append('comment', this.future_call.comment);
            future_call_form.append('priority', this.future_call.priority);
            future_call_form.append('curator_id', this.future_call.curator_id);
            future_call_form.append('subcategory_id', this.future_call.subcategory_id);
            future_call_form.append('uniqueid', this.call_in_progress.uniqueid);

            future_call_form.append('service_id', this.future_call.service_id);
            future_call_form.append('service_product_id', this.future_call.service_product_id);
            future_call_form.append('service_product_type_id', this.future_call.service_product_type_id);
            future_call_form.append('service_product_subtype_id', this.future_call.service_product_subtype_id);

            console.log(future_call_form)

            console.log($('#subject_family').val());
            console.log($('#subject_family'));
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
            //setInterval( () => this.get_realtime_status(), 2000);
            setInterval( () => this.get_current_call(), 2000);
            setInterval( () => this.get_agent_dnd_status(), 60000);;
        },
         /* --- DND Methods ---*/
         load_dnd_subjects: function () {
            axios.get(api_url+'Dnd/get_dnd/')
                .then(response => {
                        this.dnd_subjects = (response.data);
                });
        },

        /* DnD Start*/
        async submit() {
            if(this.dnd_subjects_select == ''){
                alert('აირჩიეთ სტატუსი');
                return false;
            }
            axios.post(api_url+'Dnd/start_dnd/'+agent_id, {
                dnd_subjects_select: this.dnd_subjects_select,
            }).then(response => {
                this.agent_dnd_status = (response.data);
            });
        },
        async end_dnd() {
            dnd_record_id = document.getElementById('dnd_record_id').value;

            axios.post(api_url+'Dnd/end_dnd/', {
                dnd_record_id: dnd_record_id,
                agent_id: agent_id,
            }).then(response => {
                this.agent_dnd_status = (response.data);
            });
        },

        get_agent_dnd_status: function () {
            axios.get(api_url+'Dnd/get_agent_dnd_status/'+agent_id)
                .then(
                    response => {
                            this.agent_dnd_status = (response.data);
                            console.log(this.agent_dnd_status);
                        
                    });
        },
    },
    

    created () {
        this.get_agent_data();
        //this.get_realtime_status();
        this.get_current_call();
        this.load_dnd_subjects();
        this.get_agent_dnd_status();

    },

    mounted () {
        this.refresh_realtime_data();
    },

});

$('#nav_workspace').addClass('active')

/* Jquery zura */
$(document).ready(function() {
    function get_current_call_uniqueid(){
        var baseurl = window.location.origin+window.location.pathname;
        var url_array=baseurl.split("/");
        url_array.splice(url_array.length -2, 2);

        var controller_url=url_array.toString();
        controller_url=controller_url.replace(/,/g,'/');

        $.get(api_url+'agent/vendoo_get_last_call/'+agent_id,

        function(dataResult){
            var dataResult = JSON.parse(dataResult);
            if(dataResult.statusCode==200){
                uniqueid=dataResult['current_call']['uniqueid'];
                console.log(uniqueid);
            }
            else{
                alert('Request Status Error!');
            }
        });

    }
    get_current_call_uniqueid();
});
