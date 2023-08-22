var agent_crm = new Vue({

    el: '#agent_crm',
    data () {
        return {
            agent: {},
            agent_loading: true,
            agent_error: false,

            stats: {},
            stats_loading: true,
            stats_error: false,

            realtime_status: {},
            realtime_status_loading: true,
            realtime_status_error: false,

            current_call: {},
            current_call_loading: true,
            current_call_error: false,

            missed_call_details: {},
            missed_call_details_loading: true,
            missed_call_details_error: false,

            stats_by_hour: {},
            stats_by_hour_loading: true,
            stats_by_hour_error: false,

            stats_by_day: {},
            stats_by_day_loading: true,
            stats_by_day_error: false,

            stats_by_weekday: {},
            stats_by_weekday_loading: true,
            stats_by_weekday_error: false,

            chart_cause_distrib_by_hour: false,
            chart_time_distrib_by_hour: false,

            chart_cause_distrib_by_day: false,
            chart_time_distrib_by_day: false,

            chart_cause_distrib_by_weekday: false,
            chart_time_distrib_by_weekday: false,

            form_data: new FormData,

            date_gt: "",
            date_lt: "",
            interval: 1,
        }
    },
    methods: {

        // Get agent information
        get_agent_data: function() {
            this.agent_loading = true;
            axios.post(api_url+'agent/get/'+agent_id, {})
                .then(
                    response => {
                        this.agent = response.data.data;
                    })
                .finally(() => this.agent_loading = false)
        },

        // Get agent information
        get_stats: function() {
            this.stats_loading = true;
            axios.post(api_url+'agent/get_stats/'+agent_id, this.form_data)
                .then(
                    response => {
                        this.stats = response.data.data;
                    })
                .finally(() => this.stats_loading = false)
        },

        // Get stats breakdown by hour for selected period
        get_agent_stats_by_hour: function() {
            axios.post(api_url+'agent/get_stats_by_hour/'+agent_id, this.form_data)
                .then(response => {
                    this.stats_by_hour = response.data.data;

                    labels = [];
                    dataset_answered_by_hour = [];
                    dataset_missed_by_hour = [];
                    dataset_outgoing_by_hour = [];
                    dataset_calltime_by_hour = [];
                    dataset_ringtime_by_hour = [];
                    dataset_pausetime_by_hour = [];


                    for (h in Object.keys(response.data.data).sort()) {
                        if (h < 10) {
                            h = '0'+h;
                        }
                        labels.push(h+':00');
                        dataset_answered_by_hour.push(response.data.data[h].calls_answered);
                        dataset_missed_by_hour.push(response.data.data[h].calls_missed);
                        dataset_outgoing_by_hour.push(response.data.data[h].calls_outgoing);
                        dataset_calltime_by_hour.push(Math.floor(response.data.data[h].call_time/60));
                        dataset_ringtime_by_hour.push(Math.floor(response.data.data[h].ring_time/60));
                        dataset_pausetime_by_hour.push(Math.floor(response.data.data[h].pause_time/60));

                    }

                    ctx_cause_distrib_by_hour = document.getElementById("ctx_cause_distrib_by_hour").getContext('2d');
                    this.chart_cause_distrib_by_hour = new Chart(ctx_cause_distrib_by_hour, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['calls_answered'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_answered_by_hour,
                                },
                                {
                                    label: lang['calls_unanswered'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_missed_by_hour,
                                },
                                {
                                    label: lang['calls_outgoing'],
                                    backgroundColor: get_colors('info'),
                                    data: dataset_outgoing_by_hour,
                                },
                            ]
                        }
                    });

                    ctx_time_distrib_by_hour = document.getElementById("ctx_time_distrib_by_hour").getContext('2d');
                    this.chart_time_distrib_by_hour = new Chart(ctx_time_distrib_by_hour, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['call_time'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_calltime_by_hour,
                                },
                                {
                                    label: lang['ring_time'],
                                    backgroundColor: get_colors('yellow'),
                                    data: dataset_ringtime_by_hour,
                                },
                                {
                                    label: lang['pause_time'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_pausetime_by_hour,
                                },
                            ]
                        },
                        options: {
                            scales: {
                                xAxes: [{
                                    stacked: true,
                                    ticks: {
                                        autoSkip: false
                                    }
                                }],
                                yAxes: [{
                                    stacked: true
                                }]
                            }
                        }
                    });


                })
                .finally(() => this.stats_by_hour_loading = false)
        },

        // Get stats breakdown by day for selected period
        get_agent_stats_by_day: function() {
            axios.post(api_url+'agent/get_stats_by_day/'+agent_id, this.form_data)
                .then(response => {
                    this.stats_by_day = response.data.data;

                    labels = [];
                    dataset_answered_by_day = [];
                    dataset_missed_by_day = [];
                    dataset_outgoing_by_day = [];
                    dataset_ringtime_by_day = [];
                    dataset_calltime_by_day = [];
                    dataset_pausetime_by_day = [];


                    for (d in response.data.data) {
                        labels.push(d);
                        dataset_answered_by_day.push(response.data.data[d].calls_answered);
                        dataset_missed_by_day.push(response.data.data[d].calls_missed);
                        dataset_outgoing_by_day.push(response.data.data[d].calls_outgoing);
                        dataset_calltime_by_day.push(Math.floor(response.data.data[d].call_time/60));
                        dataset_ringtime_by_day.push(Math.floor(response.data.data[d].ring_time/60));
                        dataset_pausetime_by_day.push(Math.floor(response.data.data[d].pause_time/60));
                    }

                    ctx_cause_distrib_by_day = document.getElementById("ctx_cause_distrib_by_day").getContext('2d');
                    this.chart_cause_distrib_by_day = new Chart(ctx_cause_distrib_by_day, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['calls_answered'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_answered_by_day,
                                },
                                {
                                    label: lang['calls_unanswered'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_missed_by_day,
                                },
                                {
                                    label: lang['calls_outgoing'],
                                    backgroundColor: get_colors('info'),
                                    data: dataset_outgoing_by_day,
                                },
                            ]
                        }
                    });

                    ctx_time_distrib_by_day = document.getElementById("ctx_time_distrib_by_day").getContext('2d');
                    this.chart_time_distrib_by_day = new Chart(ctx_time_distrib_by_day, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['call_time'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_calltime_by_day,
                                },
                                {
                                    label: lang['ring_time'],
                                    backgroundColor: get_colors('yellow'),
                                    data: dataset_ringtime_by_day,
                                },
                                {
                                    label: lang['pause_time'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_pausetime_by_day,
                                },
                            ]
                        },
                        options: {
                            scales: {
                                xAxes: [{
                                    stacked: true,
                                    ticks: {
                                        autoSkip: false
                                    }
                                }],
                                yAxes: [{
                                    stacked: true
                                }]
                            }
                        }
                    });

                })
                .finally(() => this.stats_by_day_loading = false)
        },

        // Get stats breakdown by day for selected period
        get_agent_stats_by_weekday: function() {
            axios.post(api_url+'agent/get_stats_by_weekday/'+agent_id, this.form_data)
                .then(response => {
                    this.stats_by_weekday = response.data.data;

                    labels = [];
                    dataset_answered_by_weekday = [];
                    dataset_missed_by_weekday = [];
                    dataset_outgoing_by_weekday = [];
                    dataset_ringtime_by_weekday = [];
                    dataset_calltime_by_weekday = [];
                    dataset_pausetime_by_weekday = [];


                    for (d in response.data.data) {
                        labels.push(lang[d]);
                        dataset_answered_by_weekday.push(response.data.data[d].calls_answered);
                        dataset_missed_by_weekday.push(response.data.data[d].calls_missed);
                        dataset_outgoing_by_weekday.push(response.data.data[d].calls_outgoing);
                        dataset_calltime_by_weekday.push(Math.floor(response.data.data[d].call_time/60));
                        dataset_ringtime_by_weekday.push(Math.floor(response.data.data[d].ring_time/60));
                        dataset_pausetime_by_weekday.push(Math.floor(response.data.data[d].pause_time/60));
                    }

                    ctx_cause_distrib_by_weekday = document.getElementById("ctx_cause_distrib_by_weekday").getContext('2d');
                    this.chart_cause_distrib_by_weekday = new Chart(ctx_cause_distrib_by_weekday, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['calls_answered'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_answered_by_weekday,
                                },
                                {
                                    label: lang['calls_unanswered'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_missed_by_weekday,
                                },
                                {
                                    label: lang['calls_outgoing'],
                                    backgroundColor: get_colors('info'),
                                    data: dataset_outgoing_by_weekday,
                                },
                            ]
                        }
                    });

                    ctx_time_distrib_by_weekday = document.getElementById("ctx_time_distrib_by_weekday").getContext('2d');
                    this.chart_time_distrib_by_weekday = new Chart(ctx_time_distrib_by_weekday, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['call_time'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_calltime_by_weekday,
                                },
                                {
                                    label: lang['ring_time'],
                                    backgroundColor: get_colors('yellow'),
                                    data: dataset_ringtime_by_weekday,
                                },
                                {
                                    label: lang['pause_time'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_pausetime_by_weekday,
                                },
                            ]
                        },
                        options: {
                            scales: {
                                xAxes: [{
                                    stacked: true,
                                    ticks: {
                                        autoSkip: false
                                    }
                                }],
                                yAxes: [{
                                    stacked: true
                                }]
                            }
                        }
                    });

                })
                .finally(() => this.stats_by_weekday_loading = false)
        },


        // Get realtime status of agent
        get_realtime_status: function() {
            axios.get(api_url+'agent/get_realtime_status/'+agent_id)
                .then(
                    response => {
                        this.realtime_status = response.data.data;
                    })
                .finally(() => this.realtime_status_loading = false)
        },

        // Get current calls of agent
        get_current_call: function() {
            axios.get(api_url+'agent/get_current_call/'+agent_id)
                .then(
                    response => {
                        this.current_call = response.data.data;
                    })
                .finally(() => this.current_call_loading = false)
        },

        load_missed_calls: function() {
            axios.post(api_url+'agent/get_missed_call_details/'+agent_id, this.form_data)
            .then(
                response => {
                    this.missed_call_details = response.data.data;
                })
            .finally(
                () => {
                    this.missed_call_details_loading = false;
                    var target_url = app_url+'/recordings?uniqueid=';
                    for (i in this.missed_call_details) {
                        target_url += this.missed_call_details[i].uniqueid+",";
                    }
                    target_url = target_url.substring(0, target_url.length - 1);
                    target_url += "&date_gt="+$('#date_gt').val()+"&date_lt="+$('#date_lt').val()
                    window.location.replace(target_url);
                }
            );
        },

        export_stats: function() {
            location.href = app_url+'/export/agent_stats/'+agent_id+'?date_gt='+$('#date_gt').val()+'&date_lt='+$('#date_lt').val();
        },

        decrease_interval: function() {
            if ($('#date_gt').val()) {
                $('#date_gt').val(moment($('#date_gt').val()).subtract(this.interval, 'days').format('YYYY-MM-DD 00:00:00'));
            } else {
                console.log("here");
                $('#date_gt').val(moment().startOf('day').subtract(this.interval, 'days').format('YYYY-MM-DD 00:00:00'));
            }
            if ($('#date_lt').val()) {
                $('#date_lt').val(moment($('#date_lt').val()).subtract(this.interval, 'days').format('YYYY-MM-DD 23:59:59'));
            } else {
                console.log("here");
                $('#date_lt').val(moment().startOf('day').subtract(this.interval, 'days').format('YYYY-MM-DD 23:59:59'));
            }
            this.refresh();
        },

        increase_interval: function() {
            if ($('#date_gt').val()) {
                $('#date_gt').val(moment($('#date_gt').val()).add(this.interval, 'days').format('YYYY-MM-DD 00:00:00'));
            } else {
                console.log("here");
                $('#date_gt').val(moment().startOf('day').add(this.interval, 'days').format('YYYY-MM-DD 00:00:00'));
            }
            if ($('#date_lt').val()) {
                $('#date_lt').val(moment($('#date_lt').val()).add(this.interval, 'days').format('YYYY-MM-DD 23:59:59'));
            } else {
                console.log("here");
                $('#date_lt').val(moment().startOf('day').add(this.interval, 'days').format('YYYY-MM-DD 23:59:59'));
            }
            this.refresh();
        },

        // Update time interval inputs
        change_interval: function(interval) {
            switch(interval) {
                case 'today':
                    $('#date_gt').val(moment().startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    this.interval = 1;
                    break;

                case 'yday':
                    $('#date_gt').val(moment().subtract(1, 'days').startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().subtract(1, 'days').endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    this.interval = 1;
                    break;

                case 'tweek':
                    $('#date_gt').val(moment().startOf('isoWeek').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    this.interval = 1;
                    break;

                case 'tmonth':
                    $('#date_gt').val(moment().startOf('month').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    this.interval = 1;
                    break;

                case 'l7day':
                    $('#date_gt').val(moment().subtract(7, 'days').startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    this.interval = 7;
                    break;

                case 'l14day':
                    $('#date_gt').val(moment().subtract(14, 'days').startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    this.interval = 14;
                    break;

                case 'l30day':
                    $('#date_gt').val(moment().subtract(30, 'days').startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    this.interval = 30;
                    break;

                default:
                    $('#date_gt').val(moment().startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    this.interval = 1;
                    break;
            }
        },

        // Refresh data
        refresh_realtime_data: function() {
            setInterval( () => this.get_agent_data(), 60000);
            setInterval( () => this.get_realtime_status(), 5000);
            setInterval( () => this.get_current_call(), 5000);
        },

        update_form_data: function() {
            f = new FormData();
            f.append('date_gt', $('#date_gt').val());
            f.append('date_lt', $('#date_lt').val());
            this.date_gt = $('#date_gt').val();
            this.date_lt = $('#date_lt').val();

            this.form_data = f;
        },

        load_data: function() {
            this.get_agent_data();
            this.get_realtime_status();
            this.get_current_call();
            this.get_stats();
            this.get_agent_stats_by_hour();
            this.get_agent_stats_by_day();
            this.get_agent_stats_by_weekday();

        },

        refresh: function() {
            this.chart_cause_distrib_by_hour.destroy();
            this.chart_time_distrib_by_hour.destroy();
            this.chart_cause_distrib_by_day.destroy();
            this.chart_cause_distrib_by_weekday.destroy();
            this.chart_time_distrib_by_day.destroy();
            this.chart_time_distrib_by_weekday.destroy();

            this.update_form_data();
            this.load_data();
        }

    },

    computed: {
        ordered_hours:  function() {
            return Object.keys(this.stats_by_hour).sort();
        },

        call_time_avg:  function() {
            return sec_to_time(Math.floor(this.stats.total_calltime / this.stats.calls_answered));
        },

        ring_time_avg:  function() {
            return sec_to_time(Math.floor(this.stats.total_ringtime / this.stats.calls_answered));
        },

        answered_percentage: function() {
            r = Math.floor((this.stats.calls_answered /this.stats.calls_answered_total) * 100);
            if (isNaN(r)) {
                return 0
            } else {
                return r;
            }
        },

        missed_percentage: function() {
            r = Math.floor((this.stats.calls_missed /this.stats.calls_missed_total) * 100);
            if (isNaN(r)) {
                return 0
            } else {
                return r;
            }
        },

        share_10s: function() {
            r = Math.floor((this.stats.answered_10s /this.stats.calls_answered) * 100);
            if (isNaN(r)) {
                return 0
            } else {
                return r;
            }
        }

    },

    mounted () {
        this.load_data();
        this.refresh_realtime_data();
    }


});

$('#date_gt').datetimepicker({format: 'Y-m-d 00:00:00'});
$('#date_lt').datetimepicker({format: 'Y-m-d 23:59:59'});
$('#nav_stats').addClass('active');
