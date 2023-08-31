var agent_stats = new Vue({
    el: '#agent_stats',
    data () {
        return {
			overall:{},
            agent: {},
            agent_loading: true,
            agent_error: false,

            total_stats: {},
            total_stats_loading: true,
            total_stats_error: false,

            hourly_stats: {},
            hourly_stats_loading: true,
            hourly_stats_error: false,

            daily_stats: {},
            daily_stats_loading: true,
            daily_stats_error: false,

            date_gt: "",
            date_lt: "",

            form_data: new FormData,
        }
    },

    methods: {
        get_agent_data: function() {
            this.agent_loading = true;
            axios.post(api_url+'agent/get/'+agent_id, {})
                .then(
                    response => {
                        this.agent = response.data.data;
                    })
                .finally(() => this.agent_loading = false)
        },

        get_total_stats: function() 
		{
			axios.post(api_url+'queue/get_total_stats_for_start/',this.form_data)
            .then(response => {
                    this.overall = response.data.data;
            });
			
            axios.post(api_url+'agent/get_stats_for_agent_stats/'+agent_id, this.form_data)
                .then(response => {
                    this.total_stats_loading = false;
                    this.total_stats = response.data.data;
                    console.log(this.total_stats);
                    ctx_event_distrib = document.getElementById("canvas_event_distrib").getContext('2d');
                    this.chart_event_distrib = new Chart(ctx_event_distrib, {
                        type: 'bar',
                        data: {
                            labels: [
                                lang['calls_answered'],
                                lang['calls_outgoing']+' ('+lang['unanswered']+')',
                                lang['calls_outgoing']+' ('+lang['answered']+')'
                            ],
                            datasets: [{
                                label: lang['calls'],
                                backgroundColor: [
                                    get_colors('green'),
                                    get_colors('red'),
                                    get_colors('teal'),
                                ],
                                data: [
                                    response.data.data.calls_answered,
                                    this.calls_outgoing_unanswered,
                                    response.data.data.calls_outgoing_answered,
                                ]
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }
                        }
                    });
                });
        },

        get_hourly_stats: function() {
            axios.post(api_url+'agent/get_hourly_stats_for_queue_stats/'+agent_id,this.form_data)
                .then(response => {
                    this.hourly_stats_loading = false;
                    this.hourly_stats = response.data.data;
                });
        },

        get_daily_stats: function() {
            axios.post(api_url+'agent/get_daily_stats_for_queue_stats/'+agent_id,this.form_data)
                .then(response => {
                    this.daily_stats_loading = false;
                    this.daily_stats = response.data.data;
                });
        },

        update_form_data: function() {
            f = new FormData();
            f.append('date_gt', $('#date_gt').val());
            f.append('date_lt', $('#date_lt').val());
            this.date_gt = $('#date_gt').val();
            this.date_lt = $('#date_lt').val();

            this.form_data = f;
        },

        refresh: function() {
            this.chart_event_distrib.destroy();
            //this.chart_agent_distrib.destroy(); ??? error

            this.update_form_data();
            this.load_data();
        },


        load_data: function() {
            this.get_total_stats();
            this.get_agent_stats();
            this.get_hourly_stats();
            this.get_daily_stats();
            // this.get_category_stats();
        },

        export_stats: function() {
            location.href = app_url+'/export/overview_new?date_gt='+$('#date_gt').val()+'&date_lt='+$('#date_lt').val();
        },

    },

    mounted () {

    },

    created () {
        this.get_total_stats();
        this.get_agent_data();

        // this.get_hourly_stats();
        // this.get_daily_stats();
        // this.get_category_stats();
		console.log("THIS",this);
    },

    computed: {
        calls_answered_both_directions: function() {
            return parseInt(this.total_stats.calls_answered ) + parseInt(this.total_stats.calls_outgoing_answered)
        },

        call_time_avg:  function() {
            return sec_to_min(Math.floor(this.total_stats.total_calltime / this.calls_answered_both_directions));
        },

        total_holdtime: function() {
            return parseInt(this.total_stats.total_holdtime ) + parseInt(this.total_stats.total_waittime)
        },

        calls_total: function() 
		{
			let overallCalls = (parseInt(this.overall.calls_answered ) + parseInt(this.overall.calls_unanswered)) +
							   (parseInt(this.overall.calls_outgoing_answered) + parseInt(this.overall.calls_outgoing_unanswered));
							   
		    let agentCalls	 =  parseInt(this.total_stats.calls_answered) + parseInt(this.total_stats.calls_outgoing);
			
		    let percent = agentCalls > 0 && overallCalls > 0 ? ((agentCalls / overallCalls) * 100).toFixed(2)+'%' : '0%';
						  
			return agentCalls + ' (' + percent + ')'; 
        },
		calls_answered: function()
		{
			let overallCalls = parseInt(this.overall.calls_answered );
							   
		    let agentCalls	 = parseInt(this.total_stats.calls_answered);
			
		    let percent = agentCalls > 0 && overallCalls > 0 ? ((agentCalls / overallCalls) * 100).toFixed(2)+'%' : '0%';
						  
			return agentCalls + ' (' + percent + ')'; 
		},
		calls_missed: function()
		{
			let overallCalls = parseInt(this.overall.calls_unanswered );
							   
		    let agentCalls	 = parseInt(this.total_stats.calls_unanswered);
			
		    let percent = agentCalls > 0 && overallCalls > 0 ? ((agentCalls / overallCalls) * 100).toFixed(2)+'%' : '0%';
						  
			return agentCalls + ' (' + percent + ')'; 
		},
		
		all_incoming_calls:function()
		{
			return parseInt(this.total_stats.calls_answered) + parseInt(this.total_stats.calls_missed);
		},
		
		hold_time_avg: function()
		{
			return sec_to_min(Math.floor(this.total_holdtime / this.all_incoming_calls));
		},
		
		hold_time_max: function()
		{
			return sec_to_min(this.total_stats.max_ringtime_answered);
		},
		
        calls_outgoing_unanswered: function() {
            return parseInt(this.total_stats.calls_outgoing) - parseInt(this.total_stats.calls_outgoing_answered)
        },

        ring_time_avg: function() {
            return sec_to_min(Math.floor(this.total_ringtime / this.calls_total));
        },

        /*--- SLA Hold Time --- */
        sla_count_less_than_10_percent: function() {
            if (this.total_stats.sla_count_total > 0) {
                let less_than_10 = (this.total_stats.sla_count_less_than_10 / this.total_stats.sla_count_total) * 100;
                return less_than_10.toFixed(2)+'%';
            } else {
                return '0%';
            }
        },

        sla_count_between_10_20_percent: function() {
            if (this.total_stats.sla_count_total > 0) {
                let between_10_20 = (this.total_stats.sla_count_between_10_20 / this.total_stats.sla_count_total) * 100;
                return between_10_20.toFixed(2)+'%';
            } else {
                return '0%';
            }
        },

        sla_count_grate_than_10_percent: function() {
            if (this.total_stats.sla_count_total > 0) {
                let grate_than_10 = (this.total_stats.sla_count_grate_than_10 / this.total_stats.sla_count_total) * 100;
                return grate_than_10.toFixed(2)+'%';
            } else {
                return '0%';
            }
        },
        /* --- End Of SLA Hold Time --- */


        /* --- Incoming Total And AVG Calltime --- */
        incoming_total_calltime_count: function() {
            if (this.total_stats.incomig_total_calltime_count > 0) {
                return sec_to_time(this.total_stats.incomig_total_calltime);
            } else {
                return '0';
            }
        },

        incoming_total_calltime_avg: function() {
            if (this.total_stats.incomig_total_calltime_count > 0) {
                return sec_to_time(Math.floor(this.total_stats.incomig_total_calltime / this.total_stats.incomig_total_calltime_count));
            } else {
                return '0';
            }
        },
		incoming_total_calltime_max:function(){
		
			if (this.total_stats.incomig_max_calltime > 0) {
                return sec_to_time(this.total_stats.incomig_max_calltime);
            } else {
                return '0';
            }
		},
        /* --- End Of Incoming Total And AVG Calltime --- */

        /* --- Outgoing Total And AVG Calltime --- */
        outgoing_total_calltime_count: function() {
            if (this.total_stats.outgoing_total_calltime_count > 0) {
                return sec_to_time(this.total_stats.outgoing_total_calltime);
            } else {
                return '0';
            }
        },

        outgoing_total_calltime_avg: function() {
            if (this.total_stats.outgoing_total_calltime_count > 0) {
                return sec_to_time(Math.floor(this.total_stats.outgoing_total_calltime / this.total_stats.outgoing_total_calltime_count));
            } else {
                return '0';
            }
        },
		outgoing_total_calltime_max:function() {
			if (this.total_stats.outgoing_max_calltime > 0) {
                return sec_to_time(this.total_stats.outgoing_max_calltime);
            } else {
                return '0';
            }
		},
        /* --- End Of Outgoing Total And AVG Calltime --- */
    },

});

$('#date_gt').datetimepicker({format: 'Y-m-d H:i:00', defaultTime: '00:00:00'});
$('#date_lt').datetimepicker({format: 'Y-m-d H:i:00', defaultTime: '23:59:59'});
