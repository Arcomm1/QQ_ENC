var queue_settings = new Vue({

    el: '#queue_settings',
    data () {
        return {
            queue: {},
            queue_loading: true,
            queue_error: false,

            queue_config: {},
            queue_config_loading: true,
            queue_config_error: false,
        }
    },

    methods: {

        get_queue_data: function() {
            this.queue_loading = true;
            axios.post(api_url+'queue/get/'+queue_id, {})
                .then(
                    response => {
                        this.queue = response.data.data;
                    })
                .finally(() => this.queue_loading = false)
        },

        get_queue_config: function() {
            this.queue_config_loading = true;
            axios.post(api_url+'queue/get_config/'+queue_id, {})
                .then(
                    response => {
                        this.queue_config = response.data.data;
                    })
                .finally(() => this.queue_config_loading = false)
        },

        set_item: function(item) {
            console.log($('#'+item).val());
            console.log(item);
            f = new FormData();
            
            if (item == 'display_name') {
                f.append(item, $('#'+item).val());
                axios.post(api_url+'queue/update/'+queue_id, f)
                    .then(response => {
                        console.log(response.data);
                        if (response.data.data == 0 || response.data.status == 'FAIL') {
                            send_notif(lang['queue_conf_update_fail'], 'danger');
                        } else {
                            send_notif(lang['queue_conf_update_ok'], 'success');
                        }
                    });
            } else {
                f.append('value', $('#'+item).val());
                axios.post(api_url+'queue/set_config/'+queue_id+'/'+item, f)
                    .then(response => {
                        console.log(response.data);
                        if (response.data.data == 0 || response.data.status == 'FAIL') {
                            send_notif(lang['queue_conf_update_fail'], 'danger');
                        } else {
                            send_notif(lang['queue_conf_update_ok'], 'success');
                        }
                    });
            }
        }

    },

    mounted () {
        this.get_queue_data();
        this.get_queue_config();

    },
});