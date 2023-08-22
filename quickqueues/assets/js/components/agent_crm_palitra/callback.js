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

        refresh_realtime_data: function() {
            setInterval( () => this.get_agent_data(), 60000);
            setInterval( () => this.get_queue_stats(), 60000);
            setInterval( () => this.get_realtime_status(), 5000);
            setInterval( () => this.get_current_call(), 5000);
            setInterval( () => this.get_queue_realtime_data(), 5000);
            setInterval( () => this.get_queue_agent_stats(), 5000);
            setInterval( () => this.get_queue_agent_realtime_data(), 5000);
        },
    },

    created () {
        this.get_agent_data();
        this.get_queue_agent_realtime_data();

    },

    mounted () {
        this.refresh_realtime_data();
        $('#tbl-calls').DataTable({
            searching: true,
            lengthChange: false,
            info: false,
            pageLength: 20,
            sortable: false,
            ordering: false
        });
    },

});

$('#nav_callback_queue').addClass('active')
