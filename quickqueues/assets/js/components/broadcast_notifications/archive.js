var broadcast_notitications = new Vue({

    el: '#broadcast_notitications',
    data () {
        return {
            bcasts: {},
            bcasts_loading: true,
            bcasts_error: false,

            name: "",
            description: "",

            bcasts_table: {}
        }
    },


    methods: {
        get_bcasts: function() {
            axios.get(api_url+'broadcast_notification/get_deleted/')
                .then(response => {
                    this.bcasts = response.data.data;
                    this.bcasts_table.clear().draw();
                        for (c in response.data.data) {
                            b = "<a class='btn btn-warning btn-sm' href='"+api_url+"/broadcast_notification/restore/"+response.data.data[c].id+"'>"+lang['restore']+"</a>";
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
                    this.get_bcasts();
                })
        },

        set_color: function(color) {
            this.color = color;
            $('.btn-sm').html('&nbsp&nbsp');
            $('#color_'+color).html('<i class="fas fa-check"></i>');
        }

    },


    mounted () {
        this.bcasts_table = $('#tbl-bcasts').DataTable({
            searching: true,
            lengthChange: false,
            info: false,
            pageLength: 25,
            sortable: false,
            ordering: false
        });
        this.get_bcasts();
    }


});
