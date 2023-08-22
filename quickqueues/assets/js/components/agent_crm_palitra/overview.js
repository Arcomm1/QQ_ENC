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

            queue_stats: {},
            queue_stats_loading: true,
            queue_stats_error: false,

            agents: {},
            agents_loading: true,
            agents_error: false,

            bcasts: {},
            bcasts_loading: true,
            bcasts_error: false,

            agent_extens: [],

            agents_free: 0,
            agents_busy: 0,
            agents_on_call: 0,

            queue_realtime: {},
            queue_realtime_loading: true,
            queue_realtime_error: false,

            gage_answered: false,
            gage_unanswered: false,
            gage_outgoing: false,
            gage_waiting: false,

            waiting: 0,
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
            axios.post(api_url+'agent/get_realtime_status/'+agent_id)
                .then(
                    response => {
                        this.realtime_status = response.data.data;
                    })
                .finally(() => this.realtime_status_loading = false)
        },

        get_current_call: function() {
            axios.post(api_url+'agent/get_current_call/'+agent_id)
                .then(
                    response => {
                        this.current_call = response.data.data;
                    })
                .finally(() => this.current_call_loading = false)
        },

        get_queue_stats: function () {
            this.stats_loading = true,

            axios.post(api_url+'queue/get_stats/'+queue_id)
                .then(response => {
                    this.queue_stats_loading = false;
                    this.queue_stats = response.data.data;

                    this.gage_answered.refresh(response.data.data.calls_answered,response.data.data.calls_total,0);
                    this.gage_unanswered.refresh(response.data.data.calls_unanswered,response.data.data.calls_total,0);
                    this.gage_outgoing.refresh(response.data.data.calls_outgoing,response.data.data.calls_total,0);

                })
                .finally(() => this.queue_stats_error = false)
        },


        get_bcasts: function () {
            this.bcasts_loading = true,

            axios.post(api_url+'broadcast_notification/get_all')
                .then(response => {
                    this.bcasts_loading = false;
                    this.bcasts = response.data.data;
                })
                .finally(() => this.bcasts_error = false)
        },


        get_agents: function () {
            this.agents_loading = true;
            axios.get(api_url+'queue/get_agent_overview/'+queue_id)
                .then(response => {
                    this.agents = response.data.data;
                })
                .then(() => {
                    this.agents_loading = false;
                    this.agents_busy = 0;
                    this.agents_on_call = 0;
                    this.agents_free = 0;
                    this.agent_extens = [];
                    for (a in this.agents) {
                        if (this.agents[a].realtime.Status == 2) {
                            this.agents_busy++;
                        }
                        if (this.agents[a].realtime.Status == 1) {
                            this.agents_on_call++;
                        }
                        if (this.agents[a].realtime.Status == 0) {
                            this.agents_free++;
                        }

                        if (this.agents[a].realtime.Status != 2) {
                            this.agent_extens.push([this.agents[a].data.extension, this.agents[a]]);
                        }
                    }
                    this.agent_extens.sort();
                }
            );
        },

        get_queue_realtime: function () {
            this.queue_realtime_loading = true;
            axios.get(api_url+'queue/get_realtime_data/'+queue_id)
                .then(response => {
                    this.queue_realtime = response.data.data;
                    this.waiting = 0;
                    for (c in this.queue_realtime.callers) {
                        this.waiting = this.waiting + 1;
                    }
                    this.gage_waiting.refresh(this.waiting,20,0);

                })
                .then(this.queue_realtime_loading = false);
        },

        go_fullscreen: function () {
            $('#monitoring_body').css('padding-top', '20px');
            $('#monitoring_nav').hide();
            $('#footer').hide();
        },

        leave_fullscreen: function () {
            setTimeout(function () {},1000);
            $('#monitoring_body').css('padding-top', '120px');
            $('#monitoring_nav').show();
            $('#footer').show();
        },

        refresh_stats: function () {
            setInterval( () => this.get_queue_stats(), 60000);
            setInterval( () => this.get_bcasts(), 60000);

            setInterval( () => this.get_agents(), 2000);
            setInterval( () => this.get_queue_realtime(), 2000);

            setInterval( () => this.get_realtime_status(), 2000);
            setInterval( () => this.get_current_call(), 2000);
        },
    },

    mounted () {
        this.gage_answered = new JustGage({
            id: "answered",
            value: 100,
            min: 0,
            max: 100,
            title: lang['calls_answered'],
            levelColors: [get_colors('success')]
        });

        this.gage_unanswered = new JustGage({
            id: "unanswered",
            value: 100,
            min: 0,
            max: 100,
            title: lang['calls_unanswered'],
            levelColors: [get_colors('danger')],
        });

        this.gage_outgoing = new JustGage({
            id: "outgoing",
            value: 100,
            min: 0,
            max: 100,
            title: lang['calls_outgoing'],
            levelColors: [get_colors('info')]
        });

        this.gage_waiting = new JustGage({
            id: "waiting",
            value: 1,
            min: 0,
            max: 1,
            title: lang['calls_waiting'],
            levelColors: [get_colors('warning')]
        });

        this.get_agents();
        this.get_queue_realtime();
    },

    created () {
        this.get_bcasts();
        this.get_queue_stats();
        this.refresh_stats();
        this.get_agent_data();
        // setTimeout(()=>{this.go_fullscreen()},3000);
        // $('#monitoring').on("focusin", this.leave_fullscreen());
    },

});


$(document).keyup(function(e) {
     if (e.key === "Escape") {
        monitoring.leave_fullscreen();
    }
});
