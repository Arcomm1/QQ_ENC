var broadcast_notitications = new Vue({

    el: '#broadcast_notitications',
    data () {
        return {
            bcasts: {},
            bcasts_loading: true,
            bcasts_error: false,

            bcast_name: '',
            bcast_description: '',
            bcast_id: '',

            name: "",
            description: "",

            bcasts_table: {}
        }
    },


    methods: {
        get_bcasts: function() {
            axios.get(api_url+'broadcast_notification/get_all/')
                .then(response => {
                    this.bcasts = response.data.data;
                    this.bcasts_table.clear().draw();
                        for (c in response.data.data) {
                            b = "<button v-click=\"get_bcast("+response.data.data[c].id+")\" class='btn btn-primary btn-sm' data-toggle='modal' data-target='#edit_bcast'>"+lang['edit']+"</button>"
                            b = b + "<a class='btn btn-danger btn-sm' href='"+api_url+"/broadcast_notification/delete/"+response.data.data[c].id+"'>"+lang['delete']+"</a>";
                            a = '';
                            if (response.data.data[c].creator_user_id) {
                                a = JSON.parse(users)[response.data.data[c].creator_user_id];
                            }
                            this.bcasts_table.row.add([
                                response.data.data[c].name,
                                response.data.data[c].description,
                                response.data.data[c].creation_date,
                                a,
                                b
                                ]).draw()
                        }
                })
        },

        get_bcast: function(id) {
            axios.get(api_url+'broadcast_notification/get/'+id)
                .then(response => {
                    this.bcast_name = response.data.data.name;
                    this.bcast_description = response.data.data.description;
                    this.bcast_id = id;
                })
        },

        save_bcast: function() {
            f = new FormData();
            f.append('name', this.name);
            f.append('description', this.description);


            axios.post(api_url+'broadcast_notification/create/', f)
                .then(response => {
                    if (response.data.status == 'OK') {
                        send_notif(response.data.message, 'success');
                        this.name = "";
                        this.description = "";
                    } else {
                        send_notif(response.data.message, 'danger');
                    }
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                })
        },

        update_bcast: function() {
            f = new FormData();
            f.append('name', this.bcast_name);
            f.append('description', this.bcast_description);

            axios.post(api_url+'broadcast_notification/update/'+this.bcast_id, f)
                .then(response => {
                    if (response.data.status == 'OK') {
                        send_notif(response.data.message, 'success');
                        this.bcast_name = "";
                        this.bcast_description = "";
                        this.bcast_id = "";
                    } else {
                        send_notif(response.data.message, 'danger');
                    }
                    // this.get_bcasts();
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                })
        },

        set_color: function(color) {
            this.color = color;
            $('.btn-sm').html('&nbsp&nbsp');
            $('#color_'+color).html('<i class="fas fa-check"></i>');
        }

    },


    mounted () {
        // this.bcasts_table = $('#tbl-bcasts').DataTable({
        //     searching: true,
        //     lengthChange: false,
        //     info: false,
        //     pageLength: 25,
        //     sortable: false,
        //     ordering: false
        // });
        // this.get_bcasts();
    }


});
