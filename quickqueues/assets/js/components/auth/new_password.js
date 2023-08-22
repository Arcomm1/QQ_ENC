var create_user = new Vue({

    el: '#new_password_div',

    data () {
        return {
            new_password: '',
            confirm_new_password:'',
            help_topic: false,
            can_submit: false,
        }
    },

    methods: {
        get_random_help_topic: function () {
            axios.get(api_url+'anonymous/misc/get_random_help_topic')
                .then(response => { this.help_topic = response.data.data; });
        },

        form_is_valid: function () {
            alert(this.newpassowrdinp.length)
            if (this.newpassowrdinp.length > 2) {
                alert('dd');
                document.getElementById("new_password_submit").disabled = false;
            } else {
                document.getElementById("new_password_submit").disabled = true;
            }
        }
    },

    created () {
        this.get_random_help_topic();
        setInterval( () => this.get_random_help_topic(), 5000);
    },

    watch: {
        newpasswordinp() {
            this.form_is_valid();
        },

    }

});

$(document).ready(function () {
    $('input').keyup( function(e){
        if( $('#new_password').val().length >3 && $('#confirm_new_password').val().length >3) {
            $('#new_password_submit').removeAttr('disabled');
        }
    });

    $('form').on('submit', function(e){
        var valid=true;
        if($('#new_password').val() != $('#confirm_new_password').val()){
            $('#msg_placeholder').html('Password And Confirmation Does not Match');
            valid=false;
        }

        if(!valid) {
            e.preventDefault();
        }
    });
});
