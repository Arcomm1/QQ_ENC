var agent_crm = new Vue({

    el: '#agent_crm',
    data () {
        return {
            agent: {},
            agent_error: false,
            agent_loading: true,

            agents: {},
            agents_error: false,
            agents_loading: true,

            realtime_status: {},
            realtime_status_loading: true,
            realtime_status_error: false,

            current_call: {},
            current_call_loading: true,
            current_call_error: false,

            call_events: {},
            call_events_error: false,
            call_events_loading: true,

            call_data: {},
            call_data_error: false,
            call_data_loading: true,

            service_module: false,

            search_ticket_department_id: false,
            search_ticket_category_id: false,
            search_ticket_subcategory_id: false,
            call_ticket_department_id: false,
            call_ticket_category_id: false,
            call_ticket_subcategory_id: false,
            download_link: "#",
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

        start_session: function () {
            console.log("Stating session for agent "+agent_id);
            axios.post(api_url+'agent/start_session/'+agent_id, {})
                .then(
                    response => {
                        send_notif(response.data.message);
                        this.get_agent_data();
                    })
        },

        end_session: function () {
            console.log("Ending session for agent "+agent_id);
            axios.post(api_url+'agent/end_session/'+agent_id, {})
                .then(
                    response => {
                        send_notif(response.data.message);
                        this.get_agent_data();
                    })
        },

        start_pause: function () {
            axios.post(api_url+'agent/pause/'+agent_id, {})
                .then(
                    response => {
                        send_notif(response.data.message);
                        this.get_agent_data();
                    })
        },

        end_pause: function () {
            axios.post(api_url+'agent/unpause/'+agent_id, {})
                .then(
                    response => {
                        send_notif(response.data.message);
                        this.get_agent_data();
                    })
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

        get_agents: function () {
            axios.post(api_url+'agent/get_all/')
                .then(
                    response => {
                        this.agents = response.data.data;
                    })
                .finally(() => this.agent_loading = false)
        },

        toggle_called_back: function(id, called_back) {
            axios.get(api_url+'recording/called_back/'+id+'/'+called_back)
                .then(
                    response => {
                        if (response.data.status == 'OK') {
                            send_notif(lang['call_status_update_success'], 'success');
                            if (called_back == 'no') {
                                $('#called_back_'+id).removeClass('text-success').removeClass('text-danger').removeClass('text-info').removeClass('text-warning').addClass('text-danger');
                            }
                            if (called_back == 'yes') {
                                $('#called_back_'+id).removeClass('text-success').removeClass('text-danger').removeClass('text-info').removeClass('text-warning').addClass('text-success');
                            }
                            if (called_back == 'nop') {
                                $('#called_back_'+id).removeClass('text-success').removeClass('text-danger').removeClass('text-info').removeClass('text-warning').addClass('text-warning');
                            }
                            if (called_back == 'nah') {
                                $('#called_back_'+id).removeClass('text-success').removeClass('text-danger').removeClass('text-info').removeClass('text-warning').addClass('text-info');
                            }
                            setTimeout("location.reload(true);", 3000);
                        } else {
                            send_notif(lang['call_status_update_fail'], 'danger');
                        }
                    }
                )
        },

        load_player: function(id) {
            console.log(id);
            $(document).ready(function(){
                p = $("#jquery_jplayer_1").jPlayer({
                    ready: function () {
                    },
                    cssSelectorAncestor: "#jp_container_1",
                    swfPath: "/js",
                    supplied: "m4a, oga, wav",
                    useStateClassSkin: true,
                    autoBlur: false,
                    smoothPlayBar: true,
                    keyEnabled: true,
                    remainingDuration: false,
                    toggleDuration: false
                });
                p.jPlayer("setMedia", {wav: api_url+'recording/get_file/'+id})
            });
        },

        get_events: function (uniqueid){
            console.log(uniqueid);
            axios.post(api_url+'recording/get_events/'+uniqueid, {})
                .then(
                    response => {
                        this.call_events = response.data.data;
                        console.log(this.call_events);
                        for (e in this.call_events) {
                            console.log(this.call_events[e].id+' '+this.call_events[e].uniqueid+' '+this.call_events[e].date+' '+this.call_events[e].event_type);
                        }
                    })
                .finally(() => this.call_events_loading = false)
        },


        get_data: function (id) {
            this.call_data = {};
            this.call_data_loading = true;
            this.download_link = "#";
            axios.post(api_url+'recording/get/'+id, {})
                .then(
                    response => {
                        this.call_data = response.data.data;
                        this.download_link = api_url+'recording/get_file/'+this.call_data.id;
                        this.load_player(this.call_data.id)
                    })
                .finally(() => this.call_data_loading = false)
        },

        add_comment: function() {
            console.log(this.call_data.id+" - "+$('#comment').val());
            axios.post(api_url+'recording/comment/'+this.call_data.id+'/'+$('#comment').val(), {})
            send_notif(lang['call_update_success'], 'success');

        },

        add_category: function() {
            axios.post(api_url+'recording/category/'+this.call_data.id+'/'+this.call_data.category_id, {});
            send_notif(lang['call_update_success'], 'success');
        },

        load_call_service_products: function (service_id) {
            axios.get(api_url+'config/get_service_products/'+service_id)
                .then(response => {
                    $('#call_service_product_id').empty();
                    $('#call_service_product_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#call_service_product_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },


        load_call_service_product_types: function (service_product_id) {
            axios.get(api_url+'config/get_service_product_types/'+service_product_id)
                .then(response => {
                    $('#call_service_product_type_id').empty();
                    $('#call_service_product_type_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#call_service_product_type_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },


        load_call_service_product_subtypes: function (service_product_type_id) {
            axios.get(api_url+'config/get_service_product_subtypes/'+service_product_type_id)
                .then(response => {
                    $('#call_service_product_subtype_id').empty();
                    $('#call_service_product_subtype_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#call_service_product_subtype_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },


        save_call_data:  function (id) {
            f = new FormData();
            f.append('status', this.call_data.status);
            f.append('category_id', this.call_data.category_id);
            f.append('comment', this.call_data.comment);
            f.append('priority', this.call_data.priority);
            f.append('curator_id', this.call_data.curator_id);
            f.append('tag_id', this.call_data.tag_id);
            f.append('service_id', this.call_data.service_id);
            f.append('service_product_id', this.call_data.service_product_id);
            f.append('service_product_type_id', this.call_data.service_product_type_id);
            f.append('service_product_subtype_id', this.call_data.service_product_subtype_id);

            f.append('ticket_department_id', this.call_data.ticket_department_id);
            f.append('ticket_category_id', this.call_data.ticket_category_id);
            f.append('ticket_subcategory_id', this.call_data.ticket_subcategory_id);


            if ($('#comment').val()) {
                $('#comment_icon_'+this.call_data.id).removeClass('far').addClass('fas');
            } else {
                $('#comment_icon_'+this.call_data.id).removeClass('fas').addClass('far');
            }

            axios.post(api_url+'recording/update/'+this.call_data.id, f);
            send_notif(lang['call_update_success'], 'success');
        },

        // This is for call modal
        load_call_categories: function (department_id) {
            axios.get(api_url+'ticket/get_categories/'+department_id)
                .then(response => {
                    $('#call_ticket_category_id').empty();
                    $('#call_ticket_category_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#call_ticket_category_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },

        // This is for call modal
        load_call_subcategories: function (category_id) {
            axios.get(api_url+'ticket/get_subcategories/'+category_id)
                .then(response => {
                    $('#call_ticket_subcategory_id').empty();
                    $('#call_ticket_subcategory_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#call_ticket_subcategory_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },

        // This is for search form
        load_categories: function (department_id) {
            axios.get(api_url+'ticket/get_categories/'+department_id)
                .then(response => {
                    $('#search_ticket_category_id').empty();
                    $('#search_ticket_category_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#search_ticket_category_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },

        // This is for search form
        load_subcategories: function (category_id) {
            axios.get(api_url+'ticket/get_subcategories/'+category_id)
                .then(response => {
                    $('#search_ticket_subcategory_id').empty();
                    $('#search_ticket_subcategory_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#search_ticket_subcategory_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },

        // Update time interval inputs
        change_interval: function(interval) {
            switch(interval) {
                case 'today':
                    $('#date_gt').val(moment().startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    break;

                case 'yday':
                    $('#date_gt').val(moment().subtract(1, 'days').startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().subtract(1, 'days').endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    break;

                case 'tweek':
                    $('#date_gt').val(moment().startOf('isoWeek').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    break;

                case 'tmonth':
                    $('#date_gt').val(moment().startOf('month').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    break;

                case 'l7day':
                    $('#date_gt').val(moment().subtract(7, 'days').startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    break;

                case 'l14day':
                    $('#date_gt').val(moment().subtract(14, 'days').startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    break;

                case 'l30day':
                    $('#date_gt').val(moment().subtract(30, 'days').startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    break;

                default:
                    $('#date_gt').val(moment().startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    break;
            }
        },

        refresh_realtime_data: function() {
            setInterval( () => this.get_agent_data(), 60000);
            setInterval( () => this.get_realtime_status(), 5000);
            setInterval( () => this.get_current_call(), 5000);
        },
    },

    created () {
        this.get_agent_data();
        if (app_service_module == 'yes') {
            this.service_module = JSON.parse(service_module_params);

            this.load_call_service_products(this.service_module.service_id);
            this.load_call_service_product_types(this.service_module.service_product_id);
            this.load_call_service_product_subtypes(this.service_module.service_product_type_id);
        }
        console.log(this.agents);
    },

    mounted () {
        this.refresh_realtime_data();
        this.get_agents();
    },

});

$('#date_gt').datetimepicker({format: 'Y-m-d 00:00:00'});
$('#date_lt').datetimepicker({format: 'Y-m-d 23:59:59'});
$('#'+active_nav).addClass('active');
$('#play_recording').on('hidden.bs.modal', function() {
    console.log("destroying player");
    $("#jquery_jplayer_1").jPlayer("destroy");
})
