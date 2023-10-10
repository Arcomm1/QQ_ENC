var queue_realtime = new Vue({

    el: '#queue_realtime',
    data () {
        return {
            freepbx_agents: false,
            freepbx_agents_loading: true,
            basic_stats: false,
            basic_stats_loading: true,

            realtime_data: {},
            realtime_data_loading: true,

            agent_stats: {},
            agent_stats_loading: true,

            agent_statuses: {},
            agent_statuses_loading: true,

            agent_current_calls: {},
            agent_current_calls_loading: true,

            agents_free: 0,
            agents_busy: 0,
            agents_on_call: 0,
            agents_unavailable: 0,
        }
    },

    methods: {
        get_basic_stats: function() {
            axios.get(api_url+'queue/get_basic_stats_for_today/'+queue_id)
                .then(response => {
                    console.log(response.data.data, 'basic stats');
                    this.basic_stats = response.data.data;
                    this.basic_stats_loading = false;
                });
        },

        get_freepbx_agents: function() {
            axios.get(api_url+'queue/get_freepbx_agents/'+queue_id)
                .then(response => {
                    console.log(response.data.data, 'freepbx agents');
                    this.freepbx_agents = response.data.data;
                    this.freepbx_agents_loading = false;
                });
        },

        get_agent_realtime_status: function() {
            axios.get(api_url+'agent/get_realtime_status_for_all_agents/')
                .then(response => {
                    if (typeof(response.data.data) == 'object') {
                        console.log(response.data.data, 'agent_statuses');
                        this.agent_statuses = response.data.data;

                        this.agent_statuses_loading = false;
                    }
                })
                .then(() => {
                    this.agents_busy = 0;
                    this.agents_on_call = 0;
                    this.agents_free = 0;
                    this.agents_unavailable = 0;
                    for (fa in this.freepbx_agents) {
                        // console.log(fa);
                        console.log(this.freepbx_agents[fa].extension);
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Idle') {
                            this.agents_free++;
                        }
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Unavailable') {
                            this.agents_unavailable++;
                        }
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'InUse') {
                            this.agents_on_call++;
                        }
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Busy') {
                            this.agents_busy++;
                        }
                    }
                })
        },

        get_realtime_data: function() {
            axios.get(api_url+'queue/get_realtime_data/'+queue_id)
                .then(response => {
                    console.log(response.data.data, 'realtime data');
                    this.realtime_data = response.data.data;
                    this.realtime_data_loading = false;
                });
        },

        get_current_calls: function() {
            axios.get(api_url+'agent/get_current_calls_for_all_agents')
            .then(response => {
                if (typeof(response.data.data) == 'object') {
                    console.log(response.data.data, 'current calls');
                    this.agent_current_calls = response.data.data;
                    this.agent_current_calls_loading = false;
                }
            })
        },

        get_agent_stats: function() {
            axios.post(api_url+'agent/get_stats_by_queue_id/'+queue_id,this.form_data)
            .then(response => {
                console.log(response.data.data, 'agent stats');
                this.agent_stats_loading = false;
                this.agent_stats = response.data.data;
            });
        }
    },

    mounted () {
    },

    created () {
        $('#nav_queues').addClass('active text-primary');

        this.get_basic_stats();
        this.get_freepbx_agents();
        this.get_agent_stats();
        this.get_realtime_data();

        setInterval( () => this.get_basic_stats(), 60000);
        setInterval( () => this.get_agent_stats(), 60000);
        setInterval( () => this.get_agent_realtime_status(), 2000);
        setInterval( () => this.get_realtime_data(), 2000);

        setInterval( () => this.get_current_calls(), 3000);
    }

});
