Vue.component('v-change-password', {
    data: function () {
        return {
            current_password: '',
            new_password: '',
            confirm_password: '',
            change_status: '',
        }
    },

    methods: {

        check_password: function () {

            if (this.current_password == '') {
                this.change_status = lang['incorrect_password'];
                $('#change_status').addClass('text-danger');
                return;
            }

            if (this.new_password == '' ||
                this.confirm_password == '' ||
                this.confirm_password != this.new_password
                ) {
                this.change_status = lang['password_mismatch'];
                $('#change_status').addClass('text-danger');
                return;
            }

            f = new FormData();

            f.append('user_id', user_id);
            f.append('password', this.current_password);

            axios.post(api_url+'user/check_password', f)
                .then(
                    response => {
                        console.log(response.data);
                        if (response.data.status == 'FAIL') {
                            this.change_status = lang['incorrect_password'];
                            $('#change_status').addClass('text-danger');
                            return;
                        } else {
                            this.update_password();
                        }
                    }
                )
        },

        update_password: function () {
            f = new FormData();
            f.append('password', this.new_password);
            axios.post(api_url+'user/update/'+user_id, f)
                .then(
                    response => {
                        console.log(response.data);
                        if (response.data.status == 'FAIL') {
                            this.change_status = lang['something_wrong'];
                            $('#change_status').addClass('text-danger');
                            return;
                        } else {
                            $('#change_password_modal').modal('hide');
                            send_notif(lang['password_change_success']);
                        }
                    }
                )
        }


    },

    template: `
    <div class="modal-body">
        <div class="form-group">
            <label for="current_password">{{ lang['current_password'] }}</label>
            <input v-model="current_password" id="current_password" type="password" name="current_password" class="form-control mb-2">
            <label for="new_password"><?php echo lang('new_password'); ?></label>
            <input v-model="new_password" id="new_password" type="password" name="new_password" class="form-control mb-2">
            <label for="confirm_password"><?php echo lang('confirm_password'); ?></label>
            <input v-model="confirm_password" id="confirm_password" type="password" name="confirm_password" class="form-control">
        </div>
        <hr>
        <span id="change_status">{{ change_status }}</span>
        <button @click="check_password" type="button" class="btn btn-success float-right mr-2">{{ lang['save'] }}</button>
        <button type="button" class="btn btn-danger float-right mr-2" data-dismiss="modal">{{ lang['cancel'] }}</button>
    </div>
    `
})


