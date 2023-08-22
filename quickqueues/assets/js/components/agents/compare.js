var agent_stats = new Vue({

    el: '#agent_stats',
    data () {
        return {
            total_stats: {},
            total_stats_loading: true,
            total_stats_error: false,

            date_gt: "",
            date_lt: "",
        }
    },

    methods: {

        get_total_stats: function () {
            f = new FormData();
            f.append('date_gt', $('#date_gt').val());
            f.append('date_lt', $('#date_lt').val());
            this.total_stats_loading = true;
            // TODO obsolete
            axios.post(api_url+'agent/get_total_stats/', f)
                .then(response => {
                    this.total_stats = response.data.data;

                    labels = [];
                    dataset_answered = [];
                    dataset_missed = [];
                    dataset_outgoing_internal = [];
                    dataset_outgoing_external = [];

                    dataset_call_time = [];
                    dataset_ring_time = [];
                    dataset_pause_time = [];

                    dataset_answered_avg = [];
                    dataset_missed_avg = [];
                    dataset_outgoing_internal_avg = [];
                    dataset_outgoing_external_avg = [];

                    dataset_call_time_avg = [];
                    dataset_ring_time_avg = [];
                    dataset_pause_time_avg = [];


                    for (a in this.total_stats) {
                        labels.push(this.total_stats[a].data.display_name);
                        dataset_answered.push(this.total_stats[a].calls_answered);
                        dataset_missed.push(this.total_stats[a].calls_missed);
                        dataset_outgoing_internal.push(this.total_stats[a].calls_outgoing_internal);
                        dataset_outgoing_external.push(this.total_stats[a].calls_outgoing_external);
                        dataset_call_time.push(this.total_stats[a].call_time);
                        dataset_ring_time.push(this.total_stats[a].ring_time);
                        dataset_pause_time.push(this.total_stats[a].pause_time);

                        dataset_answered_avg.push(~~Math.floor(this.total_stats[a].calls_answered/this.total_stats[a].days_with_calls));
                        dataset_missed_avg.push(~~Math.floor(this.total_stats[a].calls_missed/this.total_stats[a].days_with_calls));
                        dataset_outgoing_internal_avg.push(~~Math.floor(this.total_stats[a].calls_outgoing_internal/this.total_stats[a].days_with_calls));
                        dataset_outgoing_external_avg.push(~~Math.floor(this.total_stats[a].calls_outgoing_external/this.total_stats[a].days_with_calls));
                        dataset_call_time_avg.push(Math.floor(this.total_stats[a].call_time/this.total_stats[a].days_with_calls));
                        dataset_ring_time_avg.push(Math.floor(this.total_stats[a].ring_time/this.total_stats[a].days_with_calls));
                        dataset_pause_time_avg.push(Math.floor(this.total_stats[a].pause_time/this.total_stats[a].days_with_calls));
                    }

                    ctx_call_distrib = document.getElementById("ctx_call_distrib").getContext('2d');
                    this.chart_call_distrib = new Chart(ctx_call_distrib, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['calls_answered'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_answered,
                                },
                                {
                                    label: lang['missed'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_missed,
                                },
                                {
                                    label: lang['calls_outgoing']+' ('+lang['external']+')',
                                    backgroundColor: get_colors('primary'),
                                    data: dataset_outgoing_external,
                                },
                                {
                                    label: lang['calls_outgoing']+' ('+lang['internal']+')',
                                    backgroundColor: get_colors('info'),
                                    data: dataset_outgoing_internal,
                                },
                            ]
                        },
                        options: {
                            title: {
                                display: true,
                                text: lang['call_distrib_by_agent']
                            },
                            scales: {
                                xAxes: [{
                                    stacked: false,
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        min: 0,
                                        autoSkip: false
                                    }
                                }]
                            }
                        }
                    });

                    ctx_time_distrib = document.getElementById("ctx_time_distrib").getContext('2d');
                    this.chart_time_distrib = new Chart(ctx_time_distrib, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['call_time'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_call_time,
                                },
                                {
                                    label: lang['ring_time'],
                                    backgroundColor: get_colors('warning'),
                                    data: dataset_ring_time,
                                },
                                {
                                    label: lang['pause_time'],
                                    backgroundColor: get_colors('danger'),
                                    data: dataset_pause_time,
                                },
                            ]
                        },
                        options: {
                            title: {
                                display: true,
                                text: lang['call_distrib_by_time']
                            },
                            scales: {
                                xAxes: [{
                                    stacked: false,
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        min: 0,
                                        autoSkip: false
                                    }
                                }]
                            }
                        }
                    });

                    ctx_call_distrib_avg = document.getElementById("ctx_call_distrib_avg").getContext('2d');
                    this.chart_call_distrib_avg = new Chart(ctx_call_distrib_avg, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['calls_answered'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_answered_avg,
                                },
                                {
                                    label: lang['missed'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_missed_avg,
                                },
                                {
                                    label: lang['calls_outgoing']+' ('+lang['external']+')',
                                    backgroundColor: get_colors('primary'),
                                    data: dataset_outgoing_external_avg,
                                },
                                {
                                    label: lang['calls_outgoing']+' ('+lang['internal']+')',
                                    backgroundColor: get_colors('info'),
                                    data: dataset_outgoing_internal_avg,
                                },
                            ]
                        },
                        options: {
                            title: {
                                display: true,
                                text: lang['call_distrib_by_agent']
                            },
                            scales: {
                                xAxes: [{
                                    stacked: false,
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        min: 0,
                                        autoSkip: false
                                    }
                                }]
                            }
                        }
                    });

                    ctx_time_distrib_avg = document.getElementById("ctx_time_distrib_avg").getContext('2d');
                    this.chart_time_distrib_avg = new Chart(ctx_time_distrib_avg, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['call_time'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_call_time_avg,
                                },
                                {
                                    label: lang['ring_time'],
                                    backgroundColor: get_colors('warning'),
                                    data: dataset_ring_time_avg,
                                },
                                {
                                    label: lang['pause_time'],
                                    backgroundColor: get_colors('danger'),
                                    data: dataset_pause_time_avg,
                                },
                            ]
                        },
                        options: {
                            title: {
                                display: true,
                                text: lang['call_distrib_by_time']
                            },
                            scales: {
                                xAxes: [{
                                    stacked: false,
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        min: 0,
                                        autoSkip: false
                                    }
                                }]
                            }
                        }
                    });
                    this.total_stats_loading = false;
                })
        },

        change_interval: function(interval) {
            switch(interval) {
                case 'today':
                    $('#date_gt').val(moment().startOf('day').format('YYYY-MM-DD 00:00:00'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
                    this.interval = 1;
                    break;

                case 'yday':
                    $('#date_gt').val(moment().subtract(1, 'days').startOf('day').format('YYYY-MM-DD 00:00:00'));
                    $('#date_lt').val(moment().subtract(1, 'days').endOf('day').format('YYYY-MM-DD 23:59:59'));
                    this.interval = 1;
                    break;

                case 'tweek':
                    $('#date_gt').val(moment().startOf('isoWeek').format('YYYY-MM-DD 00:00:00'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
                    this.interval = 1;
                    break;

                case 'tmonth':
                    $('#date_gt').val(moment().startOf('month').format('YYYY-MM-DD 00:00:00'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
                    this.interval = 1;
                    break;

                case 'l7day':
                    $('#date_gt').val(moment().subtract(7, 'days').startOf('day').format('YYYY-MM-DD 00:00:00'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
                    this.interval = 7;
                    break;

                case 'l14day':
                    $('#date_gt').val(moment().subtract(14, 'days').startOf('day').format('YYYY-MM-DD 00:00:00'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
                    this.interval = 14;
                    break;

                case 'l30day':
                    $('#date_gt').val(moment().subtract(30, 'days').startOf('day').format('YYYY-MM-DD 00:00:00'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
                    this.interval = 30;
                    break;

                default:
                    $('#date_gt').val(moment().startOf('day').format('YYYY-MM-DD 00:00:00'));
                    $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
                    this.interval = 1;
                    break;
            }
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

        refresh: function () {
            this.get_total_stats();
        },

        export_stats: function() {
            location.href = app_url+'/export/agent_compare?date_gt='+$('#date_gt').val()+'&date_lt='+$('#date_lt').val();
        },


    },

    created () {
        this.get_total_stats();
    }


});

$('#date_gt').datetimepicker({format: 'Y-m-d 00:00:00'});
$('#date_lt').datetimepicker({format: 'Y-m-d 23:59:59'});
