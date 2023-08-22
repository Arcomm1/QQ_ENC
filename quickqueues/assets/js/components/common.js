// https://stackoverflow.com/a/11486026
function sec_to_time(sec)
{
    if (sec == 0 || sec === undefined || sec === null || Number.isNaN(sec) || sec === Infinity) {
        return "00:00:00";
    }

    h = Math.floor(sec / 3600);
    m = Math.floor((sec / 60) % 60);
    s = sec % 60;

    if (h < 10)  { h = "0"+h; }
    if (m < 10)  { m = "0"+m; }
    if (s < 10)  { s = "0"+s; }

    return h+":"+m+":"+s;

    // return(sec-(sec%=60))/60+(9<sec?':':':0')+sec

}


// https://stackoverflow.com/a/11486026
function sec_to_min(sec)
{
    if (sec == 0 || sec === undefined || sec === null || Number.isNaN(sec) || sec === Infinity) {
        return "00:00";
    }

    m = Math.floor((sec / 60) % 60);
    s = sec % 60;

    if (m < 10)  { m = "0"+m; }
    if (s < 10)  { s = "0"+s; }

    return m+":"+s;

    // return(sec-(sec%=60))/60+(9<sec?':':':0')+sec

}


function get_colors(color) {
    colors = {
        blue: '#0d6efd',
        indigo: '#6610f2',
        purple: '#6f42c1',
        pink: '#d63384',
        red: '#dc3545',
        orange: '#fd7e14',
        yellow: '#ffc107',
        green: '#198754',
        teal: '#20c997',
        cyan: '#0dcaf0',
        white: '#ffffff',
        gray: '#999999',
        gray_dark: '#303030',
        primary: '#375a7f',
        secondary: '#444444',
        success: '#00bc8c',
        info: '#3498DB',
        warning: '#F39C12',
        danger: '#E74C3C',
        light: '#303030',
        dark: '#adb5bd',
    };
    if (color) {
        return colors[color];
    } else {
        return colors;
    }
}


function agent_status_colors(status) {
    colors = {
        '0': 'success',    // "Idle";
        '1': "info",       // "In Use";
        '2': "danger",    // "Busy";
        '4': "secondary",      // "Unavailable";
        '8': "warning",    // "Ringing"
        '16': "info",      // "On hold"
    }
    if (status) {
        return colors[status];
    } else {
        return colors;
    }
}


function send_notif(ntext, ntype) {
    if (ntype === false) {
        ntype = "danger";
    }

    $.notify({
        message: ntext
    },
    {
        type: ntype,
        placement: {
            from: "top",
            align: "right"
        },
    });
}


function change_interval(interval) {
    switch(interval) {
        case 'today':
            $('#date_gt').val(moment().startOf('day').format('YYYY-MM-DD 00:00:00'));
            $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
            break;

        case 'yday':
            $('#date_gt').val(moment().subtract(1, 'days').startOf('day').format('YYYY-MM-DD 00:00:00'));
            $('#date_lt').val(moment().subtract(1, 'days').endOf('day').format('YYYY-MM-DD 23:59:59'));
            break;

        case 'tweek':
            $('#date_gt').val(moment().startOf('isoWeek').format('YYYY-MM-DD 00:00:00'));
            $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
            break;

        case 'tmonth':
            $('#date_gt').val(moment().startOf('month').format('YYYY-MM-DD 00:00:00'));
            $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
            break;

        case 'l7day':
            $('#date_gt').val(moment().subtract(7, 'days').startOf('day').format('YYYY-MM-DD 00:00:00'));
            $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
            break;

        case 'l14day':
            $('#date_gt').val(moment().subtract(14, 'days').startOf('day').format('YYYY-MM-DD 00:00:00'));
            $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
            break;

        case 'l30day':
            $('#date_gt').val(moment().subtract(30, 'days').startOf('day').format('YYYY-MM-DD 00:00:00'));
            $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
            break;

        default:
            $('#date_gt').val(moment().startOf('day').format('YYYY-MM-DD 00:00:00'));
            $('#date_lt').val(moment().endOf('day').format('YYYY-MM-DD 23:59:59'));
            break;
    }
}

Vue.component('v-broadcast-notification', {
    data: function () {
        return {
            bcasts: {},
            color: 'danger'
        }
    },

    methods: {

        get_bcasts: function () {
            axios.get(api_url+'broadcast_notification/get_all/')
                .then(response => {
                    this.bcasts = response.data.data;
                    this.cycle_bcasts(this.bcasts);
                })
        },

        cycle_bcasts: function (b) {
            n = 0;
            setInterval(
                function () {
                    n = (n + 1) % Object.keys(b).length;
                    this.color = b[n].color;
                    console.log(this.color);
                }, 3000
            );
        },

    },

    created () {
        this.get_bcasts();
        //this.cycle_bcasts();
    },

    template: `
    <div id="v-bcast" class="alert alert-dismissible alert-warning alert-primary">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <h4 class="alert-heading">Warning!</h4>
      <p class="mb-0">Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
    </div>
    `
})



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
