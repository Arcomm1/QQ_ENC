var recordings = new Vue({

    el: '#recordings',
    data () {
        return {
            agents             : {},
            agents_error       : {},
            agents_loading     : {},
            call_events        : {},
            call_events_error  : {},
            call_events_loading: {},
        }
    },

    methods: {
        load_player: function(id, rowNumber) 
        {
            
            var player                    = document.getElementById('qq_player');
            var rowNumberPlaceholder      = document.getElementById('row-number');
            rowNumberPlaceholder.innerText= " #" + rowNumber;
            
            player.src=api_url+'recording/get_file/'+id;
            player.load();
        },

        get_events: function (uniqueid)
        {
            console.log(uniqueid);
            axios.post(api_url+'recording/get_events/'+uniqueid, {})
                .then(
                    response => {
                        this.call_events = response.data.data;
                    })
                .finally(() => this.call_events_loading = false)
        },

        get_agents: function () 
        {
            axios.post(api_url+'agent/get_all/')
                .then(
                    response => {
                        this.agents = response.data.data;
                    })
                .finally(() => this.agent_loading = false)
        },

        toggle_called_back: function(id, called_back) {
            axios.get(api_url+'recording/called_back/'+id+'/'+called_back)
                .then(
                    response => {
                        if (response.data.status == 'OK') {
                            if (called_back == 'no') {
                                $('#called_back_'+id).removeClass('text-success').removeClass('text-danger').removeClass('text-info').removeClass('text-warning').addClass('text-danger');
                            }
                            if (called_back == 'yes') {
                                $('#called_back_'+id).removeClass('text-success').removeClass('text-danger').removeClass('text-info').removeClass('text-warning').addClass('text-success');
                            }
                            if (called_back == 'nop') {
                                $('#called_back_'+id).removeClass('text-success').removeClass('text-danger').removeClass('text-info').removeClass('text-warning').addClass('text-warning');
                            }
                            if (called_back == 'nah') {
                                $('#called_back_'+id).removeClass('text-success').removeClass('text-danger').removeClass('text-info').removeClass('text-warning').addClass('text-info');
                            }
                        }
                    }
                )
        },
    },

    mounted () {
        this.get_agents();
    },

    created () {
        $('#nav_recordings').addClass('active text-primary');
    }

});

$('#play_recording').on('hidden.coreui.modal', function() {
    var player = document.getElementById('qq_player');
    player.pause();
    player.removeAttribute('src'); // empty source
    player.load();
});

$('#date_gt').datetimepicker({format: 'Y-m-d H:i:00', defaultTime: '00:00:00'});
$('#date_lt').datetimepicker({format: 'Y-m-d H:i:00', defaultTime: '23:59:59'});
