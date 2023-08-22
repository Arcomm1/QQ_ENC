var agent_timetables = new Vue({

    el: '#agent_timetables',
    data () {
        return {

        }
    },

    methods: {

    },

    mounted () {
        $('#date_gt').datetimepicker({format: 'Y-m-d 00:00:00'});
        $('#date_lt').datetimepicker({format: 'Y-m-d 23:59:59'});
    },

    created () {
    }



});

