var system_overview = new Vue({
    el: '#system_overview',
    data () {
        return {
            stats_by_queue: {},
            stats_by_queue_error: false,
            stats_by_queue_loading: true,

            stats_by_agent: {},
            stats_by_agent_error: false,
            stats_by_agent_loading: true,

            stats_by_category: {},
            stats_by_category_error: false,
            stats_by_category_loading: true,

            stats: {},
            stats_loading: true,
            stats_error: false,

            date_gt: "",
            date_lt: "",

            interval: 1,

            form_data: new FormData,
        }
    },

    methods: {
        get_stats_by_queue: function() {
            this.stats_by_queue_loading = true;
            axios.post(api_url+'queue/get_stats_by_queue/', this.form_data)
                .then(response => {
                    this.stats_by_queue = response.data.data;

                    labels = [];
                    dataset_answered_by_queue = [];
                    dataset_unanswered_by_queue = [];
                    dataset_outgoing_by_queue = [];
                    dataset_calltime_by_queue = [];
                    dataset_holdtime_by_queue = [];
                    $('#queue_details > tbody').empty();
                    a = 0;
                    u = 0;
                    o = 0;
                    for (q in response.data.data) {
                        labels.push(q);
                        dataset_answered_by_queue.push(response.data.data[q].calls_answered);
                        dataset_unanswered_by_queue.push(response.data.data[q].calls_unanswered);
                        dataset_outgoing_by_queue.push(response.data.data[q].calls_outgoing);
                        dataset_calltime_by_queue.push(response.data.data[q].call_time);
                        dataset_holdtime_by_queue.push(response.data.data[q].hold_time);
                        $('#queue_details').append("<tr><td>"+q+"</td><td>"+(response.data.data[q].calls_answered+response.data.data[q].calls_unanswered+response.data.data[q].calls_outgoing)+"</td><td>"+response.data.data[q].calls_answered+"</td><td>"+response.data.data[q].calls_unanswered+"</td><td>"+response.data.data[q].calls_outgoing+"</td></tr>");
                        a = a + response.data.data[q].calls_answered;
                        u = u + response.data.data[q].calls_unanswered;
                        o = o + response.data.data[q].calls_outgoing;
                        console.log(a)
                    }
                    $('#queue_details').append("<tr><td>"+lang['total']+"</td><td>"+(a+u+o)+"</td><td>"+a+"</td><td>"+u+"</td><td>"+o+"</td></tr>");


                    ctx_cause_distrib_by_queue = document.getElementById("ctx_cause_distrib_by_queue").getContext('2d');
                    this.chart_cause_distrib_by_queue = new Chart(ctx_cause_distrib_by_queue, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['calls_answered'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_answered_by_queue,
                                },
                                {
                                    label: lang['calls_unanswered'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_unanswered_by_queue,
                                },
                                {
                                    label: lang['calls_outgoing'],
                                    backgroundColor: get_colors('info'),
                                    data: dataset_outgoing_by_queue,
                                },
                            ]
                        },
                        options: {
                            title: {
                                display: true,
                                text: lang['call_distrib_by_queue']
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
                    this.stats_by_queue_loading = false;

                })
                .finally(() => this.stats_by_queue_loading = false)
        },

        get_stats_by_agent: function() {
            this.stats_by_agent_loading = true;
            axios.post(api_url+'agent/get_stats_by_agent/', this.form_data)
                .then(response => {
                    this.stats_by_agent = response.data.data;

                    labels = [];
                    dataset_answered_by_agent = [];
                    dataset_unanswered_by_agent = [];
                    dataset_outgoing_by_agent = [];
                    dataset_calltime_by_agent = [];
                    dataset_holdtime_by_agent = [];

                    $('#agent_details > tbody').empty();
                    for (a in response.data.data) {
                        labels.push(a);
                        dataset_answered_by_agent.push(response.data.data[a].calls_answered);
                        dataset_unanswered_by_agent.push(response.data.data[a].calls_unanswered);
                        dataset_outgoing_by_agent.push(response.data.data[a].calls_outgoing);
                        dataset_calltime_by_agent.push(response.data.data[a].call_time);
                        dataset_holdtime_by_agent.push(response.data.data[a].hold_time);
                        $('#agent_details').append("<tr><td>"+a+"</td><td>"+response.data.data[a].calls_answered+"</td><td>"+response.data.data[a].calls_unanswered+"</td><td>"+response.data.data[a].calls_outgoing+"</td></tr>");
                    }

                    ctx_cause_distrib_by_agent = document.getElementById("ctx_cause_distrib_by_agent").getContext('2d');
                    this.chart_cause_distrib_by_agent = new Chart(ctx_cause_distrib_by_agent, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['calls_answered'],
                                    backgroundColor: get_colors('green'),
                                    data: dataset_answered_by_agent,
                                },
                                {
                                    label: lang['calls_unanswered'],
                                    backgroundColor: get_colors('red'),
                                    data: dataset_unanswered_by_agent,
                                },
                                {
                                    label: lang['calls_outgoing'],
                                    backgroundColor: get_colors('info'),
                                    data: dataset_outgoing_by_agent,
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
                    this.stats_by_agent_loading = false;
                })
                .finally(() => this.stats_by_agent_loading = false)
        },


        get_stats_by_category: function() {
            if (app_call_categories == 'no') {
                return false;
            }
            this.stats_by_category_loading = true;
            axios.post(api_url+'recording/count_all_by_category/', this.form_data)
                .then(response => {
                    this.stats_by_category = response.data.data;

                    labels = [];
                    dataset_calls_by_category = [];

                    for (a in response.data.data) {
                        labels.push(a);
                        dataset_calls_by_category.push(response.data.data[a]);
                    }

                    ctx_call_distrib_by_category = document.getElementById("ctx_call_distrib_by_category").getContext('2d');
                    this.chart_call_distrib_by_category = new Chart(ctx_call_distrib_by_category, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: lang['calls'],
                                    backgroundColor: get_colors('primary'),
                                    data: dataset_calls_by_category,
                                },
                            ]
                        },
                        options: {
                            title: {
                                display: true,
                                text: lang['call_distrib_by_category']
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
                    this.stats_by_category_loading = false;
                })
                .finally(() => this.stats_by_category_loading = false)
        },


        get_total_stats: function() {
            this.stats_loading = true,

            axios.post(api_url+'queue/get_stats_total/', this.form_data)
                .then(response => {
                    this.stats_loading = false;
                    this.stats = response.data.data;

                    ctx_cause_distrib = document.getElementById("ctx_cause_distrib").getContext('2d');
                        this.chart_cause_distrib = new Chart(ctx_cause_distrib, {
                            type: 'pie',
                            data: {
                                labels: [lang['calls_answered'], lang['calls_unanswered'], lang['calls_outgoing']],
                                datasets: [{
                                    backgroundColor: [
                                        get_colors('green'),
                                        get_colors('red'),
                                        get_colors('info'),
                                    ],
                                    data: [
                                        response.data.data.calls_answered,
                                        response.data.data.calls_unanswered,
                                        (response.data.data.calls_outgoing_external + response.data.data.calls_outgoing_internal),
                                    ]
                                }]
                            },
                            options: {
                                legend: {
                                    position: 'right',
                                }
                            }
                        });
                        ctx_time_distrib = document.getElementById("ctx_time_distrib").getContext('2d');
                        this.chart_time_distrib = new Chart(ctx_time_distrib, {
                            type: 'pie',
                            data: {
                                labels: [lang['call_time'], lang['hold_time']],
                                datasets: [{
                                    backgroundColor: [
                                        get_colors('green'),
                                        get_colors('yellow'),
                                    ],
                                    data: [
                                        response.data.data.total_calltime,
                                        response.data.data.total_holdtime,
                                    ]
                                }]
                            },
                            options: {
                                legend: {
                                    position: 'right',
                                }
                            }
                        });
                        this.chart_outgoing_cause_distrib = new Chart(ctx_outgoing_cause_distrib, {
                            type: 'pie',
                            data: {
                                labels: [lang['calls_outgoing_answered'], lang['calls_outgoing_failed']],
                                datasets: [{
                                    backgroundColor: [
                                        get_colors('green'),
                                        get_colors('red'),
                                    ],
                                    data: [
                                        response.data.data.calls_outgoing_external_answered,
                                        response.data.data.calls_outgoing_external_failed,
                                    ]
                                }]
                            },
                            options: {
                                legend: {
                                    position: 'right',
                                }
                            }
                        });

                })
                .finally(() => this.stats_error = false)
        },


        refresh: function() {
            this.chart_cause_distrib_by_queue.destroy();
            this.chart_cause_distrib_by_agent.destroy();
            this.chart_cause_distrib.destroy();
            this.chart_time_distrib.destroy();

            this.update_form_data();
            this.load_data();
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

        export_stats: function() {
            location.href = app_url+'/export/overview?date_gt='+$('#date_gt').val()+'&date_lt='+$('#date_lt').val();
        },

        export_stats_new: function() {
            location.href = app_url+'/export/overview_new?date_gt='+$('#date_gt').val()+'&date_lt='+$('#date_lt').val();
        },

        export_unique_callers: function() {
            location.href = app_url+'/export/unique_callers?date_gt='+$('#date_gt').val()+'&date_lt='+$('#date_lt').val();
        },

        load_data: function() {
            this.get_stats_by_queue();
            this.get_stats_by_agent();
            this.get_stats_by_category();
            this.get_total_stats();
        },


        update_form_data: function() {
            f = new FormData();
            f.append('date_gt', $('#date_gt').val());
            f.append('date_lt', $('#date_lt').val());
            this.date_gt = $('#date_gt').val();
            this.date_lt = $('#date_lt').val();

            this.form_data = f;
        },

    },

    mounted () {
        this.get_stats_by_queue();
        this.get_stats_by_agent();
        this.get_stats_by_category();

    },

    created () {
        this.get_total_stats();
    },

    computed: {
        call_time_avg:  function() {
            return sec_to_time(Math.floor(this.stats.total_calltime / this.stats.calls_answered));
        },

        hold_time_avg: function() {
            return sec_to_time(Math.floor(this.stats.total_holdtime / this.stats.calls_answered));
        },

        answered_share:  function() {
            s = (this.stats.calls_answered / (this.stats.calls_unanswered + this.stats.calls_answered) * 100)
            if (app_round_to_hundredth == 'yes') {
                s = s.toFixed(2);
            } else {
                s = Math.floor(s);
            }
            if (Number.isNaN(s)) {
                return "0%";
            } else {
                return s+"%";
            }
        },

        unanswered_share:  function() {
            s = (this.stats.calls_unanswered / (this.stats.calls_unanswered + this.stats.calls_answered) * 100);
            if (app_round_to_hundredth == 'yes') {
                s = s.toFixed(2);
            } else {
                s = Math.floor(s);
            }
            if (Number.isNaN(s)) {
                return "0%";
            } else {
                return s+"%";
            }
        },

        ignore_abandon_share:  function() {
            s = (this.stats.calls_unanswered_ignored / (this.stats.calls_unanswered + this.stats.calls_answered) * 100);
            if (app_round_to_hundredth == 'yes') {
                s = s.toFixed(2);
            } else {
                s = Math.floor(s);
            }
            if (Number.isNaN(s)) {
                return "0%";
            } else {
                return s+"%";
            }
        },

        answered_elsewhere_share:  function() {
            s = (this.stats.answered_elsewhere / (this.stats.calls_unanswered + this.stats.calls_answered) * 100);
            if (app_round_to_hundredth == 'yes') {
                s = s.toFixed(2);
            } else {
                s = Math.floor(s);
            }
            if (Number.isNaN(s)) {
                return "0%";
            } else {
                return s+"%";
            }
        },

        calls_without_service_share:  function() {
            s = (this.stats.calls_without_service / (this.stats.calls_unanswered + this.stats.calls_answered) * 100);
            if (app_round_to_hundredth == 'yes') {
                s = s.toFixed(2);
            } else {
                s = Math.floor(s);
            }
            if (Number.isNaN(s)) {
                return "0%";
            } else {
                return s+"%";
            }
        },

        within_10s_share:  function() {
            s = (this.stats.answered_within_sla / this.stats.calls_answered * 100);
            if (app_round_to_hundredth == 'yes') {
                s = s.toFixed(2);
            } else {
                s = Math.floor(s);
            }
            if (Number.isNaN(s)) {
                return "0%";
            } else {
                return s+"%";
            }
        }
    },


});

$('#date_gt').datetimepicker({format: 'Y-m-d 00:00:00'});
$('#date_lt').datetimepicker({format: 'Y-m-d 23:59:59'});
