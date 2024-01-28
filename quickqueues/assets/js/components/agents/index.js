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
    // Get the table body
    var tableBody = $('.monitoring-agents-dashboard-table-body');

    // Function to sort the table rows in descending order
    function sortTableDescending(columnIndex) {
        var rows = tableBody.find('tr').toArray();

        rows.sort(function(a, b) {
            var cellA, cellB;

            // Check if sorting based on 'Status' column
            if (columnIndex === 1) {
                // Get the text from the <td> element in the 'Status' column
                cellA = $(a).find('td:eq(1)').text();
                cellB = $(b).find('td:eq(1)').text();
            } else {
                // Get the text from the <td> element with Vue.js data
                cellA = $(a).find('td:eq(2)').text();
                cellB = $(b).find('td:eq(2)').text();
            }

            // Reverse the order of comparison for descending sorting
            return cellB.localeCompare(cellA);
        });

        // Append the sorted rows back to the table body
        $.each(rows, function(index, row) {
            tableBody.append(row);
        });
    }

    // Function to automatically sort the table in descending order every 5 seconds
    function autoSortTableDescending() {
        // Assuming you want to sort by the 'Status' column (index 1)
        sortTableDescending(1);
    }

    // Initial descending sort on page load
    autoSortTableDescending();

    // Call the autoSortTableDescending function every 5 seconds for descending sorting
    setInterval(autoSortTableDescending, 1000);
});


