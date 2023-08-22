var crm_link = new Vue({
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

            future_call: {},

            crm_link: "http://service.trm/index.php?r=tickets",

            crm_open: 'no',
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
                        var num;
                        this.current_call = response.data.data;
                        if (this.current_call[0]) {
                            this.call_in_progress = this.current_call[0];
                            if (this.crm_open == 'no') {
                                console.log("Generating new crm link");
                                this.crm_open = "yes";

                                if (this.call_in_progress.ConnectedLineNum.startsWith(' ')) {
                                    this.call_in_progress.ConnectedLineNum = this.call_in_progress.ConnectedLineNum.substring(1);
                                }

                                if (!dids.includes(this.call_in_progress.ConnectedLineNum)) {
                                    num = this.call_in_progress['ConnectedLineNum'];
                                } else {
                                    num = this.call_in_progress['CallerIDNum'];
                                }
                                // Clean the number
                                if (num.startsWith(' ')) {
                                    num = num.substring(1);
                                }
                                if (num.startsWith('9955')) {
                                    num = num.substring(4);
                                }
                                if (num.startsWith('99532')) {
                                    num = num.substring(5);
                                }
                                if (num.startsWith('32')) {
                                    num = num.substring(2);
                                }
                                if (num.startsWith('032')) {
                                    num = num.substring(3);
                                }

                                this.crm_link = this.crm_link+"&Initial%5Bclient_phone%5D="+num
                            } else {
                                console.log("Nothing to do, as crm link generated");
                            }
                            this.loaded_call = 1;

                            if (this.call_in_progress.Linkedid != this.future_call.uniqueid) {
                                this.future_call = {};
                                this.future_call.uniqueid = this.call_in_progress.Linkedid;
                            }
                        } else {
                            if (this.crm_open == 'yes') {
                                console.log("Setting crm open to no");
                                this.crm_open = 'no';
                                this.crm_open = "http://service.trm/index.php?r=tickets";
                            }
                        }
                        if (this.loaded_call == 1) {

                            this.get_calls();
                        }
                        console.log(this.crm_link);

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

        },

        save_current_call: function () {
            f = new FormData();
            f.append('status', this.future_call.status);
            f.append('category_id', this.future_call.category_id);
            f.append('comment', this.future_call.comment);
            f.append('priority', this.future_call.priority);
            f.append('curator_id', this.future_call.curator_id);
            f.append('subcategory_id', this.future_call.subcategory_id);
            f.append('uniqueid', this.future_call.uniqueid);

            axios.post(api_url+'recording/create_future_event', f);
            send_notif(lang['call_update_success'], 'success');
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

$('#nav_crm_link').addClass('active')
