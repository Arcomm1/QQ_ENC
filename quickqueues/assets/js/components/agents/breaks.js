var agent_breaks = new Vue({
    el: '#agent_breaks',
    data () {
        return {
            agents: {},

            date_gt: "",
            date_lt: "",
            brake_subjects: [],

            form_data: new FormData,
        }
    },

    methods: {
        get_all_agents_data: function(){
            axios.post(api_url+'breaks/get_all_agents_breaks/', this.form_data)
            .then(
                response => {
                    this.agents = response.data;
                    console.log(this.agents);
                });
        },

        get_breaks_subjects: function() {
            axios.post(api_url+'breaks/get_breaks_subjects/')
            .then(
                response => {
                    this.brake_subjects = response.data;
                    console.log(this.brake_subjects);
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
            this.update_form_data();
            this.load_data();
        },


        load_data: function() {
            this.get_all_agents_data();
        },

        export_stats: function() {
            location.href = app_url+'/export/overview_new?date_gt='+$('#date_gt').val()+'&date_lt='+$('#date_lt').val();
        },

    },

    mounted () {
    },

    created () {
        this.get_all_agents_data();
        this.get_breaks_subjects();
    },

    computed: {

    },

});

$('#date_gt').datetimepicker({format: 'Y-m-d H:i:00', defaultTime: '00:00:00'});
$('#date_lt').datetimepicker({format: 'Y-m-d H:i:00', defaultTime: '23:59:59'});
