var monitoring_dashboard = new Vue({

    el: '#monitoring_dashboard',
    data () {
        return {
            freepbx_agents             : {},
            freepbx_agents_loading     : true,
     
            basic_stats                : false,
            basic_stats_loading        : true,

            realtime_data              : {},
            realtime_data_loading      : true,

            agent_stats                : {},
            agent_stats_loading        : true,

            agent_statuses             : {},
            agent_statuses_loading     : true,

            agent_current_calls        : {},
            agent_current_calls_loading: true,

            overal                     :{},
            overal_loading             : true,

            agents_free                : 0,
            agents_busy                : 0,
            agents_on_call             : 0,
            agents_unavailable         : 0,
            total_callers              : 0,

            isAgentSpeaking            : false,
            callDuration               : 0,
        }
    },

    methods: {
        get_basic_stats: function() 
        {
            axios.get(api_url+'queue/get_basic_stats_for_today/')
                .then(response => {
                    this.basic_stats         = response.data.data;
                    this.basic_stats_loading = false;
                });
        },

        get_freepbx_agents: function() 
        {
            axios.get(api_url+'queue/get_freepbx_agents/')
                .then(response => {
                    this.freepbx_agents         = response.data.data;
                    this.freepbx_agents_loading = false;
            });
        },

        get_agent_realtime_status: function() 
        {
            axios.get(api_url+'agent/get_realtime_status_for_all_agents/')
                .then(response => {
                    if (typeof(response.data.data) == 'object') 
                    {

                        this.agent_statuses         = response.data.data;

                        this.agent_statuses_loading = false;
                    }
                })
                .then(() => {
                    this.agents_busy        = 0;
                    this.agents_on_call     = 0;
                    this.agents_free        = 0;
                    this.agents_unavailable = 0;
                    let anyAgentSpeaking    = false;
                    for (fa in this.freepbx_agents) 
                    {
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Idle') 
                        {
                            this.agents_free++;
                        }
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Unavailable') 
                        {
                            this.agents_unavailable++;
                        }
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'InUse') 
                        {

                            anyAgentSpeaking = true;
                            this.agents_on_call++;
                        }
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Busy') 
                        {
                            this.agents_busy++;
                        }
                    }
                    if (!anyAgentSpeaking) 
                    {
                        this.isAgentSpeaking = false;
                        this.callDuration = 0;
                    }
                    else 
                    {
                        this.isAgentSpeaking = true;
                    }
                })
        },

        get_realtime_data: function() 
        {
            axios.get(api_url+'queue/get_realtime_data/')
                .then(response => {
                    this.realtime_data         = response.data.data;
                    this.realtime_data_loading = false;
                    this.total_callers         = 0;
                    for (queue in response.data.data) 
                    {
                        this.total_callers = this.total_callers + Object.keys(response.data.data[queue]['callers']).length;
                    }
                });
        },

        get_current_calls: function() 
        {
            axios.get(api_url+'agent/get_current_calls_for_all_agents')
            .then(response => {
                if (typeof(response.data.data) == 'object') 
                {
                    this.agent_current_calls         = response.data.data;
                    this.agent_current_calls_loading = false;

                }
            })
        },

        get_agent_stats: function() 
        {
            axios.post(api_url+'agent/get_stats_for_all_queues/',this.form_data)
            .then(response => {
                this.agent_stats_loading = false;
                this.agent_stats = response.data.data;
            });
        }
    },

    computed: {
        total_callers: function() 
        {
            a = 0;
            for (queue in this.realtime_data) 
            {
                console.log(queue);
                // a = a + Object.keys(this.realtime_data[queue]['callers'].length);
            }
            return a;
        },
    },

    created () 
    {
        $('#nav_monitoring').addClass('active text-primary');

        this.get_basic_stats();
        this.get_freepbx_agents();
        this.get_agent_stats();
        this.get_realtime_data();
        
        

        setInterval(() => this.get_basic_stats(), 60000);
        setInterval(() => this.get_agent_stats(), 60000);
        setInterval(() => this.get_freepbx_agents(), 3000);
        setInterval(() => this.get_agent_realtime_status(), 2000);
        setInterval(() => this.get_realtime_data(), 2000);
        setInterval(() => this.get_current_calls(), 3000);

        setInterval(() => 
        {
            if (this.isAgentSpeaking)
            {
                this.callDuration++
            }
        }, 1000)
    }

});
