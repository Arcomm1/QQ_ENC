var start = new Vue({
    el: '#start',
    data () {
        return {
            total_stats: {},
            total_stats_loading: true,
            total_stats_error: false,

            agent_stats: {},
            agent_stats_loading: true,
            agent_stats_error: false,

            queue_stats: {},
            queue_stats_loading: true,
            queue_stats_error: false,

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
        get_total_stats: function() {
            axios.post(api_url+'queue/get_total_stats_for_start/',this.form_data)
                .then(response => {
                    this.total_stats_loading = false;
                    this.total_stats = response.data.data;
                    console.log(this.total_stats, 'Total stats for start');
                    if (this.total_stats.origposition_max === null) 
                    {
                        this.total_stats.origposition_max = 0;
                    }
                    ctx_event_distrib = document.getElementById("canvas_event_distrib").getContext('2d');
                    this.chart_event_distrib = new Chart(ctx_event_distrib, {
                        type: 'bar',
                        data: {
                            labels: [
                                lang['calls_answered'],
                                lang['calls_unanswered'],
                                lang['calls_outgoing']+' ('+lang['answered']+')',
                                lang['calls_outgoing']+' ('+lang['unanswered']+')'
                            ],
                            datasets: [{
                                label: lang['calls'],
                                backgroundColor: [
                                    get_colors('teal'),
                                    get_colors('red'),
                                    get_colors('teal'),
                                    get_colors('red'),
                                ],
                                data: [
                                    response.data.data.calls_answered,
                                    response.data.data.calls_unanswered,
                                    response.data.data.calls_outgoing_answered,
                                    response.data.data.calls_outgoing_unanswered,
                                ]
                            }]
                        },
                        options: {
                        }
                    });
                });
        },


        get_agent_stats: function() 
        {
            axios.post(api_url+'agent/get_stats_for_all_queues/',this.form_data)
                .then(response => {
                    this.agent_stats_loading = false;
                    this.agent_stats = response.data.data;
                    labels = [];
                    dataset_answered = [];
                    dataset_outgoing = [];
                    dataset_missed = [];

                    for (r in response.data.data) {
                        labels.push(response.data.data[r].display_name);
                        dataset_answered.push(response.data.data[r].calls_answered);
                        dataset_outgoing.push(response.data.data[r].calls_outgoing);
                        dataset_missed.push(response.data.data[r].calls_missed);

                    }
                    ctx_agent_distrib = document.getElementById("canvas_agent_distrib").getContext('2d');
                    this.chart_agent_distrib = new Chart(ctx_agent_distrib, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['calls_answered'],
                                    backgroundColor: get_colors('teal'),
                                    data: dataset_answered,
                                },
                                {
                                    label: lang['calls_outgoing'],
                                    backgroundColor: get_colors('blue'),
                                    data: dataset_outgoing,
                                },
                                {
                                    label: lang['calls_missed'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_missed,
                                },
                            ]
                        },
                        options: {
                            scaleShowValues: true,
                            scales: {
                                xAxes: [{
                                    ticks: {
                                        autoSkip: false
                                    }
                                }]
                            }
                        }
                    });
                });
        },


        get_queue_stats: function() {
            axios.post(api_url+'queue/get_stats_for_start/',this.form_data)
                .then(response => {
                    this.queue_stats_loading = false;
                    this.queue_stats = response.data.data;
                });
        },


        get_hourly_stats: function() {
            axios.post(api_url+'queue/get_hourly_stats_for_start/',this.form_data)
                .then(response => {
                    this.hourly_stats_loading = false;
                    this.hourly_stats = response.data.data;
                });
        },


        get_daily_stats: function() {
            axios.post(api_url+'queue/get_daily_stats_for_start/',this.form_data)
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
            this.chart_agent_distrib.destroy();

            this.update_form_data();
            this.load_data();
        },


        load_data: function() {
            this.get_total_stats();
            this.get_agent_stats();
            this.get_queue_stats();
            this.get_hourly_stats();
            this.get_daily_stats();
        },


        export_stats: function() {
            location.href = app_url+'/export/overview_new?date_gt='+$('#date_gt').val()+'&date_lt='+$('#date_lt').val();
        },

        export_category: function() {
            location.href = app_url+'/export/category_export?date_gt='+$('#date_gt').val()+'&date_lt='+$('#date_lt').val();;
        },

        /*show_category: function() {
            location.href = app_url+'/export/category_show';
        },*/
    },


    mounted () {

    },

    created () {
        this.get_total_stats();
        this.get_agent_stats();
        this.get_queue_stats();
        this.get_hourly_stats();
        this.get_daily_stats();
    },

    computed: {
        calls_answered_both_directions: function() {
            return parseInt(this.total_stats.calls_answered ) + parseInt(this.total_stats.calls_outgoing_answered)
        },
		
        calls_answered_percent: function() { 
            let percent = this.total_stats.calls_answered > 0 && this.all_incoming_calls > 0 ? 
						  ((this.total_stats.calls_answered / this.all_incoming_calls) * 100).toFixed(2)+'%' : '0%';
						  
			return this.total_stats.calls_answered + ' (' + percent + ')'; 
        },
		
		
        calls_unanswered_percent: function() { 
            let percent = this.total_stats.calls_unanswered > 0 && this.all_incoming_calls > 0 ? 
						  ((this.total_stats.calls_unanswered / this.all_incoming_calls) * 100).toFixed(2)+'%' : '0%';
						  
			return this.total_stats.calls_unanswered + ' (' + percent + ')'; 
        },

        unique_incoming_calls: function() {
            return parseInt(this.total_stats.unique_incoming_calls_answered) + parseInt(this.total_stats.unique_incoming_calls_unanswered)
        },

        unique_outgoing_calls: function() {
            return parseInt(this.total_stats.unique_outgoing_calls_answered ) + parseInt(this.total_stats.unique_outgoing_calls_unanswered)
        },

        unique_calls: function() {
            return parseInt(this.total_stats.unique_incoming_calls_answered )
        },

        all_incoming_calls: function() {
            return parseInt(this.total_stats.calls_answered ) + parseInt(this.total_stats.calls_unanswered)
        },

        call_time_avg:  function() {
            return sec_to_min(Math.floor(this.total_stats.total_calltime / this.calls_answered_both_directions));
        },

        total_holdtime: function() {
            return parseInt(this.total_stats.total_holdtime ) + parseInt(this.total_stats.total_waittime)
        },

        hold_time_max: function() {
            return sec_to_min(this.total_stats.max_waittime);
        },

        hold_time_avg: function() {
            return sec_to_min(Math.floor(this.total_stats.total_holdtime / this.total_stats.incoming_total_calltime_count));
        },

        calls_total: function() {
			console.log("THIS",this);
            return this.all_incoming_calls + parseInt(this.total_stats.calls_outgoing_answered) + parseInt(this.total_stats.calls_outgoing_unanswered);
        },

        /*--- SLA Hold Time --- */
        sla_count_less_than_or_equal_to_10_percent: function() {
            if (this.total_stats.sla_count_total > 0) {
                let less_than_or_equal_to_10 = (this.total_stats.sla_count_less_than_or_equal_to_10 / this.total_stats.sla_count_total) * 100;
                return less_than_or_equal_to_10.toFixed(2)+'%';
            } else {
                return '0%';
            }
        },

        sla_count_greater_than_10_and_less_than_or_equal_to_20_percent: function() {
            if (this.total_stats.sla_count_total > 0) {
                let greater_than_10_and_less_than_or_equal_to_20 = (this.total_stats.sla_count_greater_than_10_and_less_than_or_equal_to_20 / this.total_stats.sla_count_total) * 100;
                return greater_than_10_and_less_than_or_equal_to_20.toFixed(2)+'%';
            } else {
                return '0%';
            }
        },

        sla_count_greater_than_20_percent: function() {
            if (this.total_stats.sla_count_total > 0) {
                let greater_than_20 = (this.total_stats.sla_count_greater_than_20 / this.total_stats.sla_count_total) * 100;
                return greater_than_20.toFixed(2)+'%';
            } else {
                return '0%';
            }
        },

        // sla_count_greater_than_10_percent: function() {
        //     if (this.total_stats.sla_count_total > 0) {
        //         let greater_than_10 = (this.total_stats.sla_count_grate_than_10 / this.total_stats.sla_count_total) * 100;
        //         return greater_than_10.toFixed(2)+'%';
        //     } else {
        //         return '0%';
        //     }
        // },

        /* --- End Of SLA Hold Time --- */

        /* --- ATA Hold Time --- */
        ata_time_avg: function() {
            if (this.total_stats.ata_count_total > 0) {
                return sec_to_min(Math.floor(this.total_stats.ata_total_waittime / this.total_stats.ata_count_total));
            } else {
                return '0';
            }
        },
        /* --- End Of ATA Hold Time --- */

        /* --- Incoming Total And AVG Calltime --- */
        incoming_total_calltime_count: function() {
            if (this.total_stats.incoming_total_calltime_count > 0) {
                return sec_to_time(this.total_stats.incoming_total_calltime);
            } else {
                return '0';
            }
        },

        incoming_total_calltime_avg: function() {
            if (this.total_stats.incoming_total_calltime_count > 0) {
                return sec_to_time(Math.floor(this.total_stats.incoming_total_calltime / this.total_stats.incoming_total_calltime_count));
            } else {
                return '0';
            }
        },
		incoming_total_calltime_max:function(){
		
			if (this.total_stats.incoming_max_calltime > 0) {
                return sec_to_time(this.total_stats.incoming_max_calltime);
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

        without_service_share: function() {
            if (this.all_incoming_calls > 0) {
                return Math.floor((this.total_stats.calls_without_service / this.all_incoming_calls) * 100);
            } else {
                return '0%';
            }
        }
    },


});

$('#date_gt').datetimepicker({format: 'Y-m-d H:i:00', defaultTime: '00:00:00'});
$('#date_lt').datetimepicker({format: 'Y-m-d H:i:00', defaultTime: '23:59:59'});
