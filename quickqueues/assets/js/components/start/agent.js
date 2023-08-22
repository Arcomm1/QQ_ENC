var agent_dashboard = new Vue({
    el: '#agent_dashboard',
    data () {
        return {
            agent: {},
            agent_error: false,
            agent_loading: true,

            stats: {},
            stats_error: false,
            stats_loading: true,

            agents: {},
            agents_loading: true,
            agents_error: false,

            realtime_status: {},
            realtime_status_loading: true,
            realtime_status_error: false,

            current_call: {},
            current_call_loading: true,
            current_call_error: false,

            calls: {},
            calls_error: false,
            calls_loading: true,

            queues: {},
            queues_error: false,
            queues_loading: true,

            calls_table: {},

            new_comment: "",

            call_categories: {},
            new_category_id: "",

            selected_call: {},

            form_data: new FormData,
        }
    },

    methods: {
        get_realtime_status: function() {
            axios.post(api_url+'agent/get_realtime_status/'+agent_id, this.form_data)
                .then(
                    response => {
                        this.realtime_status = response.data.data;
                    })
                .finally(() => this.realtime_status_loading = false)
        },

        get_call_categories: function() {
            axios.get(api_url+'config/get_call_categories/')
                .then(
                    response => {
                        this.call_categories = response.data.data;
                    })
        },

        get_current_call: function() {
            axios.post(api_url+'agent/get_current_call/'+agent_id, this.form_data)
                .then(
                    response => {
                        this.current_call = response.data.data;
                    })
                .finally(() => this.current_call_loading = false)
        },

        get_agent_data: function() {
            axios.post(api_url+'agent/get/'+agent_id, {})
                .then(
                    response => {
                        this.agent = response.data.data;
                    })
                .finally(() => this.agent_loading = false)
        },

        get_stats: function() {
            axios.post(api_url+'agent/get_stats/'+agent_id, this.form_data)
                .then(
                    response => {
                        this.stats = response.data.data;
                    })
                .finally(() => this.stats_loading = false)
        },

        get_queues: function() {
            axios.post(api_url+'agent/get_queues/'+agent_id)
                .then(
                    response => {
                        this.queues = response.data.data;
                    })
                .finally(() => this.queues_loading = false)
        },

        get_calls: function() {
            f = new FormData();
            f.append('date_gt', $('#date_gt').val());
            f.append('date_lt', $('#date_lt').val());
            f.append('src', $('#src').val());
            f.append('dst', $('#dst').val());

            axios.post(api_url+'agent/get_dashboard_calls/'+agent_id, f)
                .then(
                    response => {
                        this.calls = response.data.data;
                        this.calls_table.clear().draw();
                        for (c in response.data.data) {
                            r = [];
                            r = [
                                response.data.data[c].src,
                                response.data.data[c].dst,
                                response.data.data[c].date,
                                sec_to_time(response.data.data[c].calltime),
                                '<a onClick="get_call_details('+response.data.data[c].id+')" class="text-primary mr-2" data-toggle="modal" data-target="#call_details"><i class="fas fa-info-circle"></i></a>',

                            ];
                            this.calls_table.row.add(r).draw()
                        }
                    })
                .finally(() => this.calls_loading = false)
        },

        comment_on_future_call: function(uniqueid) {
            axios.post(api_url+'recording/comment_on_future/'+uniqueid+'/'+this.new_comment, {});
            send_notif(lang['call_update_success'], 'success');
        },

        category_on_future_call: function(uniqueid) {
            axios.post(api_url+'recording/category_on_future/'+uniqueid+'/'+this.new_category_id, {});
            send_notif(lang['call_update_success'], 'success');
        },

        add_comment: function() {
            axios.post(api_url+'recording/comment/'+this.selected_call.id+'/'+$('#selected_call_comment').val(), {})
            send_notif(lang['call_update_success'], 'success');

        },

        add_category: function() {
            axios.post(api_url+'recording/category/'+this.selected_call.id+'/'+$('#selected_call_category_id').val(), {});
            send_notif(lang['call_update_success'], 'success');
        },

        get_agents:  function() {
            if (show_agents == 'yes') {
                axios.get(api_url+'queue/get_agent_realtime_data/'+this.agent.primary_queue_id)
                    .then(
                        response => {
                            this.agents = response.data.data;
                        })
                    .finally(() => this.agents_loading = false)
            }
        },

        load_data: function() {
            this.get_realtime_status();
            this.get_current_call();
            this.get_stats();
            this.get_queues();
            this.get_calls();
            this.get_agent_data();
        },

        refresh_realtime_data: function() {
            setInterval( () => this.get_agent_data(), 60000);
            setInterval( () => this.get_realtime_status(), 5000);
            setInterval( () => this.get_current_call(), 5000);
            setInterval( () => this.get_queues(), 5000);
            setInterval( () => this.get_agents(), 5000);
            setInterval( () => this.get_stats(), 60000);
            setInterval( () => this.get_calls(), 60000);
        },
    },

    mounted () {
        this.calls_table = $('#tbl-calls').DataTable({
            searching: false,
            lengthChange: false,
            info: false,
            pageLength: 10,
            sortable: false,
            ordering: false
        });
        this.load_data();
        this.refresh_realtime_data();
        this.get_call_categories();
    },

    created () {
        this.get_queues();
    }

});


function load_player(id) {
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
}


function get_call_details(id) {
    console.log(id);
    axios.get(api_url+'recording/get/'+id)
        .then(response => {agent_dashboard.$data.selected_call = response.data.data});
    if (can_listen == 'yes') {
        load_player(id);
    }
}


$('#call_details').on('hidden.bs.modal', function() {
    $("#jquery_jplayer_1").jPlayer("destroy");
});

$('#date_gt').datetimepicker({format: 'Y-m-d 00:00:00'});
$('#date_lt').datetimepicker({format: 'Y-m-d 23:59:59'});
