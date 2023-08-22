var agent_config = new Vue({

    el: '#agent_config',
    data () {
        return {
            agent: {},
            agent_loading: true,
            agent_error: false,

            agent_settings: {},
            agent_settings_loading: true,
            agent_settings_error: false,
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

        get_agent_settings: function() {
            this.agent_settings_loading = true;
            axios.post(api_url+'agent/get_settings/'+agent_id, {})
                .then(
                    response => {
                        this.agent_settings = response.data.data;
                    })
                .finally(() => this.agent_settings_loading = false)
        },

        set_item: function(item) {
            f = new FormData();

            if (item == 'display_name') {
                f.append(item, $('#'+item).val());
                axios.post(api_url+'agent/update/'+agent_id, f)
                    .then(response => {
                        console.log(response.data);
                        if (response.data.data == 0 || response.data.status == 'FAIL') {
                            send_notif(lang['agent_conf_update_fail'], 'danger');
                        } else {
                            send_notif(lang['agent_conf_update_ok'], 'success');
                        }
                    });

            } else if (item == 'show_in_dashboard') {
                f.append(item, $('#'+item).val());
                axios.post(api_url+'agent/update/'+agent_id, f)
                    .then(response => {
                        console.log(response.data);
                        if (response.data.data == 0 || response.data.status == 'FAIL') {
                            send_notif(lang['agent_conf_update_fail'], 'danger');
                        } else {
                            send_notif(lang['agent_conf_update_ok'], 'success');
                        }
                    });
            } else {
                f.append('value', $('#'+item).val());
                axios.post(api_url+'agent/update_settings/'+agent_id+'/'+item, f)
                    .then(response => {
                        console.log(response.data);
                        if (response.data.data == 0 || response.data.status == 'FAIL') {
                            send_notif(lang['agent_conf_update_fail'], 'danger');
                        } else {
                            send_notif(lang['agent_conf_update_ok'], 'success');
                        }
                    });
            }
        }

    },

    mounted () {
        this.get_agent_data();
        this.get_agent_settings();
    },
});
