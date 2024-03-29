var queue_realtime = new Vue({

    el: '#queue_realtime',
    data () {
        return {
			freepbx_agents: {},
            freepbx_agents_loading: true,
            basic_stats: false,
            basic_stats_loading: true,

			realtime_data: {
				callers: [] // Ensure callers is initialized as an array
			},
            realtime_data_loading: true,

            agent_stats: {},
            agent_stats_loading: true,

            agent_statuses: {},
            agent_statuses_loading: true,

            agent_current_calls: {},
            agent_current_calls_loading: true,
			
			callDurations: {},

            agents_free: 0,
            agents_busy: 0,
            agents_on_call: 0,
            agents_unavailable: 0,
			
			uniqueAgentsArray: [],
        }
    },

    methods: {
        get_basic_stats: function() {
            axios.get(api_url+'queue/get_basic_stats_for_today/'+queue_id)
                .then(response => {
                    //console.log(response.data.data, 'basic stats');
                    this.basic_stats = response.data.data;
                    this.basic_stats_loading = false;
                });
        },

        get_freepbx_agents: function() {
            axios.get(api_url+'queue/get_freepbx_agents/'+queue_id)
                .then(response => {
                    //console.log(response.data.data, 'freepbx agents');
                    this.freepbx_agents = response.data.data;
                    this.freepbx_agents_loading = false;
                });
        },

        get_agent_realtime_status: function() {
            axios.get(api_url+'agent/get_realtime_status_for_all_agents/')
                .then(response => {
                    if (typeof(response.data.data) == 'object') {
                        //console.log(response.data.data, 'agent_statuses');
                        this.agent_statuses = response.data.data;

                        this.agent_statuses_loading = false;
                    }
                })
                .then(() => {
                    this.agents_busy = 0;
                    this.agents_on_call = 0;
                    this.agents_free = 0;
                    this.agents_unavailable = 0;
                    for (fa in this.freepbx_agents) {
                        // console.log(fa);
                        //console.log(this.freepbx_agents[fa].extension);
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Idle') {
                            this.agents_free++;
                        }
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Unavailable') {
                            this.agents_unavailable++;
                        }
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'InUse') {
                            this.agents_on_call++;
                        }
                        if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Busy') {
                            this.agents_busy++;
                        }
                    }
                })
        },

        get_realtime_data: function() {
            axios.get(api_url+'queue/get_realtime_data/'+queue_id)
                .then(response => {
                    //console.log(response.data.data, 'realtime data');
                    this.realtime_data = response.data.data;
					this.uniqueAgentsArray = this.processQueueData(response.data);

					console.log(JSON.stringify(this.uniqueAgentsArray, null, 2));
					
                    this.realtime_data_loading = false;
                });
        },

        get_current_calls: function() {
            axios.get(api_url+'agent/get_current_calls_for_all_agents')
            .then(response => {
                if (typeof(response.data.data) == 'object') {
                    //console.log(response.data.data, 'current calls');
                    this.agent_current_calls = response.data.data;
                    this.agent_current_calls_loading = false;
                }
            })
        },

        get_agent_stats: function() {
            axios.post(api_url+'agent/get_stats_by_queue_id/'+queue_id,this.form_data)
            .then(response => {
                //console.log(response.data.data, 'agent stats');
                this.agent_stats_loading = false;
                this.agent_stats = response.data.data;
            });
        },
		
		copyToClipboard(text) {
			const input = document.createElement('textarea');
			input.value = text;
			document.body.appendChild(input);
			input.select();
			document.execCommand('copy');
			document.body.removeChild(input);

			// Optionally, you can show a message to indicate that the text has been copied.
			// Example: alert('Copied to clipboard: ' + text);
		},
		
		isAgentOnCall(agentExtension) {
			// Safely check if agentExtension exists and has a StatusText property
			return this.agent_statuses[agentExtension] && this.agent_statuses[agentExtension]['StatusText'] === 'InUse';
		},
		
		updateCallDuration: function(agentExtension) 
        {
			// Retrieve call durations from local storage
			this.callDurations = JSON.parse(localStorage.getItem('callDurations')) || {};

			if (this.isAgentOnCall(agentExtension)) {
				if (!this.callDurations[agentExtension]) 
                {
					this.callDurations[agentExtension] = 0;
				}
                
				this.callDurations[agentExtension]++;
			}
            else 
            {
                // Reset call duration to 0 when the agent is not on call
                this.callDurations[agentExtension] = 0;
            }

			// Save the updated call durations back to local storage
			localStorage.setItem('callDurations', JSON.stringify(this.callDurations));
		},
		
        processQueueData: function(data) {
            const agentsData = {};

            // Processing each queue
            for (const queueKey in data) {
                const queue = data[queueKey];
                if (queue.agents) {
                    for (const agentKey in queue.agents) {
                        const agent = queue.agents[agentKey];
                        const agentName = agent.Name;
                        const agentStatus = agent.Paused;

                        // Creating a unique key for each agent
                        if (!agentsData[agentName]) {
                            agentsData[agentName] = agentStatus;
                        }
                    }
                }
            }

            // Converting the unique agents data into the desired array format
            const uniqueAgentsArray = Object.keys(agentsData).map(name => ({
                Name: name,
                Paused: agentsData[name]
            }));

            return uniqueAgentsArray;
        },

		isAgentPaused(agentName) {
			const agent = this.uniqueAgentsArray.find(a => a.Name === agentName);
			return agent && agent.Paused === '1';
		},	
        		
		
    },

    mounted () {
    },

    created () {
        $('#nav_queues').addClass('active text-primary');

        this.get_basic_stats();
        this.get_freepbx_agents();
        this.get_agent_stats();
        this.get_realtime_data();

        setInterval( () => this.get_basic_stats(), 60000);
        setInterval( () => this.get_agent_stats(), 60000);
        setInterval( () => this.get_agent_realtime_status(), 2000);
        setInterval( () => this.get_realtime_data(), 2000);
        setInterval( () => this.get_current_calls(), 3000);
		
		// Updating call duration with a fixed interval
		const updateCallDuration = () => 
        {
			
			for (const key in this.agent_statuses) 
            {
				//alert(key);
				this.updateCallDuration(key);
			}
		};		
        setInterval(updateCallDuration, 1000)
		
    }

});
$(document).ready(function() {
    // Get the table body
    var tableBody = $('.monitoring-dashboard-table-body');

    // Function to sort the table rows
    function sortTable(columnIndex) {
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

            return cellA.localeCompare(cellB);
        });

        // Append the sorted rows back to the table body
        $.each(rows, function(index, row) {
            tableBody.append(row);
        });
    }

    // Function to automatically sort the table every 5 seconds
    function autoSortTable() {
        // Assuming you want to sort by the 'Status' column (index 1)
        sortTable(1);
    }

    // Initial sort on page load
    autoSortTable();

    // Call the autoSortTable function every 5 seconds
    setInterval(autoSortTable, 1500);
});
