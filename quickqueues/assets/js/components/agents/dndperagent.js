var dnd_per_agent = new Vue({
    el: '#dnd_per_agent',
    data () {
        return {
            agent: {},

            date_gt: "",
            date_lt: "",

            dnds_per_agent: "",

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
                        console.log(this.agent);
                    })
                .finally(() => this.agent_loading = false)
        },

        get_dnd_by_agent_id: function() {

            axios.post(api_url+'Dnd/get_dnd_by_agent_id/'+agent_id, this.form_data)
                .then(
                    response => {
                        this.dnds_per_agent = response.data;
                        console.log(this.dnds_per_agent)
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
            //this.chart_event_distrib.destroy();

            this.update_form_data();
            this.load_data();
        },


        load_data: function() {
            this.get_dnd_by_agent_id();
            //this.get_total_stats();
            //this.get_agent_stats();
            //this.get_hourly_stats();
            //this.get_daily_stats();
            // this.get_category_stats();
        },

        export_stats: function() {
            location.href = app_url+'/export/overview_new?date_gt='+$('#date_gt').val()+'&date_lt='+$('#date_lt').val();
        },

    },

    mounted () {

    },

    created () {
        //this.get_total_stats();
        this.get_agent_data();
        this.get_dnd_by_agent_id();

        // this.get_hourly_stats();
        // this.get_daily_stats();
        // this.get_category_stats();
    },

    computed: {

    },

});

$('#date_gt').datetimepicker({format: 'Y-m-d H:i:00', defaultTime: '00:00:00'});
$('#date_lt').datetimepicker({format: 'Y-m-d H:i:00', defaultTime: '23:59:59'});
