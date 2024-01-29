Vue.config.devtools = true;

var agent_overview = new Vue({

    el: '#agent_overview',
    data () {
        return {
            agents: {},
            agents_loading: true,
            agents_error: false,

            agent_statuses: {},
            agent_statuses_loading: true,

            agent_current_calls: {},
            agent_current_calls_loading: true,

            agents_free: 0,
            agents_busy: 0,
            agents_on_call: 0,
            agents_unavailable: 0,

            queues: {},
            queues_loading: true,
            queues_error: false,

            chanspy_status: false,
        }
    },

    methods: {
        get_overview: function() {
            this.agents_loading = true;
            axios.get(api_url+'agent/get_stats_for_start/')
                .then(response => {
                    if (typeof(response.data.data) == 'object') {
                        
                        this.agents = response.data.data;
                       
                         /* DnD Status for Agents */
                         let agent_id = '';
                         for (const agentId in this.agents) {
 
                             agent_id = this.agents[agentId].agent_id;
 
                             axios.get(api_url + 'Dnd/get_agent_dnd_status/' + agent_id)
                                 .then(agentResponse => {
                                     this.agentInfo = agentResponse.data;
                                     this.$set(this.agents[agentId], 'dnd_status_pushed', this.agentInfo.dnd_status);
                                     this.$set(this.agents[agentId], 'dnd_duration_pushed', this.agentInfo.dnd_duration);
                                     this.$set(this.agents[agentId], 'dnd_subject_title_pushed', this.agentInfo.dnd_subject_title);
                                 })
                         }
                    }
                })
        },

        get_realtime_status: function() {
            axios.get(api_url+'agent/get_realtime_status_for_all_agents/')
                .then(response => {
                    if (typeof(response.data.data) == 'object') {

                        this.agent_statuses = response.data.data;

                        this.agent_statuses_loading = false;
                    }
                })
                .then(() => {
                    this.agents_busy = 0;
                    this.agents_on_call = 0;
                    this.agents_free = 0;
                    this.agents_unavailable = 0;

                    for (s in this.agent_statuses) {
                        if (this.agent_statuses[s]['StatusText'] == 'Idle') {
                            this.agents_free++;
                        }
                        if (this.agent_statuses[s]['StatusText'] == 'Unavailable') {
                            this.agents_unavailable++;
                        }
                        if (this.agent_statuses[s]['StatusText'] == 'InUse') {
                            this.agents_on_call++;
                        }
                        if (this.agent_statuses[s]['StatusText'] == 'Busy') {
                            this.agents_busy++;
                        }
                    }
                });
        },

        get_current_calls: function() {
            axios.get(api_url+'agent/get_current_calls_for_all_agents')
            .then(response => {
                if (typeof(response.data.data) == 'object') 
                {
                    this.agent_current_calls = response.data.data;
                    this.agent_current_calls_loading = false;
                }
            })
        },

        refresh_stats: function() {
            setInterval( () => this.get_overview(), 60000);
            setInterval( () => this.get_queues(), 10000);
            setInterval( () => this.get_realtime_status(), 5000);
            setInterval( () => this.get_current_calls(), 5000);
        },

        get_queues: function() {
            this.queues_loading = true;
            axios.get(api_url+'queue/get_realtime_data/')
                .then(response => {
                    this.queues = response.data.data;
                })
                .then(this.queues_loading = false);
        }
    },

    mounted () {
        this.get_overview();
        this.get_queues();
        this.get_realtime_status();
    },

    created () {
        $('#nav_agents').addClass('active text-primary');
        this.refresh_stats();
    }



});
$(document).ready(function() {
    var tableBody = $('.monitoring-agents-dashboard-table-body');

    function sortTableDescending(columnIndex) {
        var rows = tableBody.find('tr').toArray();

        rows.sort(function(a, b) {
            // Parse the numbers from the cells
            var cellA = parseInt($(a).find('td').eq(columnIndex).text());
            var cellB = parseInt($(b).find('td').eq(columnIndex).text());

            // Compare the numbers for descending order
            return cellB - cellA; // For ascending, use cellA - cellB
        });

        // Append sorted rows back to the table body
        rows.forEach(function(row) {
            tableBody.append(row);
        });
    }

    // Sort the table by the 'calls_answered' column in descending order on page load
    sortTableDescending(1); // Change the index if 'calls_answered' is in a different column

    // Optionally, keep sorting every second (might be excessive)
    setInterval(function() { sortTableDescending(1); }, 1000);
});


