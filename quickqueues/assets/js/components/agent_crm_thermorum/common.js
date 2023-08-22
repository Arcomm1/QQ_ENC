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
        <button @click="check_password" type="button" class="btn btn-success float-right mr-2">Save changes</button>
        <button type="button" class="btn btn-danger float-right mr-2" data-dismiss="modal">Close</button>
    </div>
    `
});


var cdr_lookup = new Vue({

    el: '#cdr_lookup',
    data () {
        return {
            loading: true
        }
    },

    methods: {
        perform_cdr_lookup: function() {
            $('#cdr_lookup_result').html('')
            axios.get(api_url+'misc/cdr_lookup/'+$('#cdr_lookup_number').val()+"/"+$('#cdr_lookup_hour').val())
                .then(response => {
                    $('#cdr_lookup_result').html("<div class='alert alert-info'>"+lang['result_not_found']+"</div>").html(
                        "<table class='table table-sm table-hover table-bordered'><tr><td>"+response.data.data.src+"</td><td>"+response.data.data.calldate+"</td></tr></table>"
                    );
                })
                .then(this.loading = false);

        }
    }
});


