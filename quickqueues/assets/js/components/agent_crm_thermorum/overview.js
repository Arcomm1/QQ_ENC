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

            queue_realtime: {},
            queue_realtime_loading: true,
            queue_realtime_error: false,
            queue_realtime_ongoing: 0,

            queue_agents: {},
            queue_agents_loading: true,
            queue_agents_error: false,

            queue_agents_realtime: {},
            queue_agents_realtime_loading: true,
            queue_agents_realtime_error: false,

            queue_stats: {},
            queue_stats_loading: true,
            queue_stats_error: false,

            calls_ongoing: 0,

            selected_queue_id: false,
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

        get_queue_realtime_data: function() {
            if (this.selected_queue_id === false) {
                this.selected_queue_id = primary_queue_id;
            }
            axios.get(api_url+'queue/get_realtime_data/'+this.selected_queue_id)
                .then(response => (this.queue_realtime = response.data.data))
                .finally(response => (this.queue_realtime_loading = false))
        },

        get_queue_agent_stats: function() {
            if (this.selected_queue_id === false) {
                this.selected_queue_id = primary_queue_id
            }
            axios.get(api_url+'queue/get_agent_stats/'+this.selected_queue_id)
                .then(response => (this.queue_agents = response.data.data))
                .finally(response => (this.queue_agents_loading = false))
        },

        get_queue_agent_realtime_data: function() {
            if (this.selected_queue_id === false) {
                this.selected_queue_id = primary_queue_id
            }
            axios.get(api_url+'queue/get_agent_realtime_data/'+this.selected_queue_id)
                .then(response => (this.queue_agents_realtime = response.data.data))
                .finally(response => (this.queue_agents_realtime_loading = false))
        },

        get_queue_stats: function() {

            if (this.selected_queue_id === false) {
                this.selected_queue_id = primary_queue_id;
            }
            axios.post(api_url+'queue/get_stats/'+this.selected_queue_id, {})
                .then(
                    response => (
                        this.queue_stats = response.data.data
                    )
                )
                .finally(() => this.queue_stats_loading = false)
        },

        refresh_realtime_data: function() {
            setInterval( () => this.get_agent_data(), 60000);
            setInterval( () => this.get_queue_stats(), 60000);
            setInterval( () => this.get_realtime_status(), 2000);
            setInterval( () => this.get_current_call(), 2000);
            setInterval( () => this.get_queue_realtime_data(), 2000);
            setInterval( () => this.get_queue_agent_stats(), 2000);
            setInterval( () => this.get_queue_agent_realtime_data(), 2000);
        },

    },

    // mounted () {
    //     this.refresh_realtime_data();
    // },

    created () {
        this.get_agent_data();
        this.get_queue_stats();
        this.get_queue_agent_stats();
        this.get_queue_agent_stats();
        this.get_queue_agent_realtime_data();

    },

    mounted () {
        this.refresh_realtime_data();
    },

    computed: {
        queue_agent_answered_percentage: function () {
            p = {};
            for (a in this.queue_agents) {
                p[a] = Math.floor(this.queue_agents[a].stats.answered / this.queue_stats.calls_answered * 100)
            }
            return p;
        },
        queue_agent_avg_calltime: function () {
            p = {};
            for (a in this.queue_agents) {
                if (this.queue_agents[a].stats.total_calltime > 0) {
                    p[a] = sec_to_time(Math.floor(this.queue_agents[a].stats.total_calltime / this.queue_agents[a].stats.answered))
                } else {
                    p[a] = '00:00';
                }

            }
            return p;
        },

        ongoing_calls: function () {
            p = 0;
            for (a in this.queue_agents_realtime) {
                if (this.queue_agents_realtime[a].status.Status == 1) {
                    console.log(this.queue_agents_realtime[a].status.Status);
                    p = p + 1;
                }
            }
            return p;
        }
    }


});


$('#nav_overview').addClass('active')
