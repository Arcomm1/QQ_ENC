var edit_ticket = new Vue({

    el: '#edit_ticket',
    data () {
        return {
            ticket: {},
            comments: {},
            new_comment: '',
            department_id: false,
            category_id: false,
            subcategory_id: false,

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

            service_module: false
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

        get_ticket: function () {
            axios.get(api_url+'ticket/get/'+ticket_id)
                .then(response => {
                    this.ticket = response.data.data
                });
        },

        get_comments: function () {
            axios.get(api_url+'ticket/get_comments/'+ticket_id)
                .then(response => {
                    this.comments = response.data.data
                });
        },

        add_new_comment: function () {
            send_notif(lang['add_comment_success']);
            f = new FormData();
            f.append('comment', this.new_comment);
            f.append('author_id', author_id)
            axios.post(api_url+'ticket/add_comment/'+ticket_id, f)
                .then(response => {
                    send_notif(lang['add_comment_success']);
                    this.new_comment = '';
                    this.get_comments();
                });
        },

        load_categories: function (department_id) {
            axios.get(api_url+'ticket/get_categories/'+department_id)
                .then(response => {
                    $('#category_id').empty();
                    $('#category_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#category_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },

        load_subcategories: function (category_id) {
            axios.get(api_url+'ticket/get_subcategories/'+category_id)
                .then(response => {
                    $('#subcategory_id').empty();
                    $('#subcategory_id').append(new Option('', ''));
                    for (c in response.data.data) {
                        $('#subcategory_id').append(new Option(response.data.data[c].name, response.data.data[c].id));
                    }
                });
        },

        remove_call_from_ticket: function(call_id) {
            axios.get(api_url+'recording/remove_from_ticket/'+call_id)
                .then(response => {
                    send_notif(response.data.message);
                    $('#ticket-call-'+call_id).remove();
                });
        },

        load_player: function(id) {
            $(document).ready(function(){
                p = $("#jquery_jplayer_1").jPlayer({
                    ready: function () {
                    },
                    cssSelectorAncestor: "#jp_container_1",
                    swfPath: "/js",
                    supplied: "m4a, oga, wav",
                    solution: "html, flash",
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

    },

    mounted () {
        this.get_ticket();
        this.get_comments();

    }

});


$('#due_at').datetimepicker({format: 'Y-m-d H:i:00'});
