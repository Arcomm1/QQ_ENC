var monitoring_dashboard = new Vue({

    el: '#monitoring_dashboard',
    data () {
        return {
            freepbx_agents             : {},
            freepbx_agents_loading     : true,
     
            basic_stats                : false,
            basic_stats_loading        : true,

            realtime_data              : {},
            realtime_data_loading      : true,

            agent_stats                : {},
            agent_stats_loading        : true,

            agent_statuses             : {},
            agent_statuses_loading     : true,

            agent_current_calls        : {},
            agent_current_calls_loading: true,

            overal                     :{},
            overal_loading             : true,

            agents_free                : 0,
            agents_busy                : 0,
            agents_on_call             : 0,
            agents_unavailable         : 0,
            total_callers              : 0,

            callDurations              : {},

            queueStats                 : {},
            queueStats_loading         : false,
            totalCallsByQueue          : {},
            form_data                  : new FormData,
			
			uniqueAgentsArray: [],
        }
    },

    methods: {
        get_basic_stats: function() 
        {
            axios.get(api_url+'queue/get_basic_stats_for_today/' + queue_id)
                .then(response => {
                    this.basic_stats         = response.data.data;
                    this.basic_stats_loading = false;
                });
        },

        get_queue_stats: function() 
        {
            this.queueStats_loading = true;
            axios
              .get(api_url + 'queue/get_stats_by_queue/', this.form_data)
              .then((response) => {
                this.queueStats = response.data.data;
      
                // Calculate total calls for each queue
                const totalCallsByQueue = {};
      
                for (const queueId in this.queueStats) 
                {
                  if (this.queueStats.hasOwnProperty(queueId)) 
                  {
                    const queueData  = this.queueStats[queueId];
                    const totalCalls =
                      queueData.calls_answered +
                      queueData.calls_unanswered +
                      queueData.calls_outgoing;
                    totalCallsByQueue[queueId] = totalCalls;
                  }
                }
      
                // Assign the total calls by queue to a variable or update  component's state
                this.totalCallsByQueue = totalCallsByQueue;
      
                // Debugging: log the calculated totals
      
                this.queueStats_loading = false;
              });
          },
        

        get_freepbx_agents: function() 
        {
            axios.get(api_url+'queue/get_freepbx_agents/' + queue_id)
                .then(response => {
                    this.freepbx_agents         = response.data.data;
                    this.freepbx_agents_loading = false;
            });
        },
		
		/////////////////////////////////////
		get_combined_data: function() {
			axios.get(api_url + 'agent/get_cached_realtime_all/' + queue_id) // Adjust the endpoint as necessary
			.then(response => {
				// Split the combined string into individual JSON strings
				let combinedData = response;

				// Directly access data from the response
				let agentStatusesData = response.data.agent_statuses; // Correctly access `queue` data
				let realtimeData = response.data.queue; // Assuming `queue_stats` holds what you refer to as realtimeData
				let agentCurrentCalls = response.data.all_calls; // Correctly access `status`
				let agentStats = response.data.queue_stats; // Correctly access `extensions`

				// Process agent statuses
				if (typeof(agentStatusesData) == 'object') {
					this.agent_statuses = agentStatusesData;
					this.agent_statuses_loading = false;
					// Initialize counters
					this.agents_busy = 0;
					this.agents_on_call = 0;
					this.agents_free = 0;
					this.agents_unavailable = 0;
					// Count agents based on status
					for (let fa in this.freepbx_agents) {
						let status = this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'];
						if (status == 'Idle') {
							this.agents_free++;
						} else if (status == 'Unavailable') {
							this.agents_unavailable++;
						} else if (status == 'InUse') {
							this.agents_on_call++;
						} else if (status == 'Busy') {
							this.agents_busy++;
						}
					}
				}

				// Process realtime queue data
				this.realtime_data = realtimeData;
				this.realtime_data_loading = false;
				this.total_callers = 0;
				this.uniqueAgentsArray = this.processQueueData(realtimeData);
				for (let queue in realtimeData) {
					this.total_callers += Object.keys(realtimeData[queue]['callers']).length;
				}

				// Process current calls
				this.agent_current_calls = agentCurrentCalls;
				this.agent_current_calls_loading = false;

				// Process agent stats
				this.agent_stats = agentStats;
				this.agent_stats_loading = false;
			});
		},
		////////////////////////////////////
       
		isAgentOnCall(agentExtension) {
			// Safely check if agentExtension exists and has a StatusText property
			return this.agent_statuses[agentExtension] && this.agent_statuses[agentExtension]['StatusText'] === 'InUse';
		},

		updateCallDuration: function(agentExtension) {
			// Retrieve call durations from local storage
			this.callDurations = JSON.parse(localStorage.getItem('callDurations')) || {};

			if (this.isAgentOnCall(agentExtension)) {
				if (!this.callDurations[agentExtension]) {
					this.callDurations[agentExtension] = 0;
				}
				this.callDurations[agentExtension]++;
			}
			else {
				this.callDurations[agentExtension] = 0;
			}

			// Save the updated call durations back to local storage
			localStorage.setItem('callDurations', JSON.stringify(this.callDurations));
		},

        queueIsOverloaded: function(queue) 
        {
            const callers = queue.callers;
            
            if (callers && Object.keys(callers).length > Number.parseInt(window.globalSettings.call_overload)) 
            {
                return true;
            }
            return false;
        },

        queueIdContainsCallback(queueId) 
        {
            return queueId.toLowerCase().includes('callback');
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

    computed: 
    {	  
        total_callers: function() 
        {
            a = 0;
            for (queue in this.realtime_data) 
            {
                // a = a + Object.keys(this.realtime_data[queue]['callers'].length);
            }
            return a;
        },
    },

    created () 
    {
        $('#nav_queues').addClass('active text-primary');

		//This must be executes first, as it draws the page
		this.get_basic_stats();
		this.get_freepbx_agents();
		this.get_queue_stats();	


		function getRandomDelay(min, max) {
			return Math.floor(Math.random() * (max - min + 1)) + min;
		}

		// Recursive setTimeout for each function

		const scheduleBasicStats = () => {
			this.get_basic_stats();
			setTimeout(scheduleBasicStats, 60000 + getRandomDelay(500, 2000));
		};

		const scheduleFreePBXAgents = () => {
			this.get_freepbx_agents();
			setTimeout(scheduleFreePBXAgents, 3000 + getRandomDelay(500, 2000));
		};

		const schedulecombined_data = () => {
			this.get_combined_data();
            setTimeout(schedulecombined_data, 2000); // Keeping this interval constant as it's a frequent update
		};

		const scheduleQueueStats = () => {
			this.get_queue_stats();
			setTimeout(scheduleQueueStats, 5000 + getRandomDelay(500, 2000));
		};

		// Updating call duration with a fixed interval
		const updateCallDuration = () => {
			for (const key in this.agent_statuses) {
				this.updateCallDuration(key);
			}
			setTimeout(updateCallDuration, 1000); // Keeping this interval constant as it's a frequent update
		};

		// Initial calls to start the recursive scheduling;
			scheduleBasicStats();
			scheduleFreePBXAgents();
			schedulecombined_data();
			scheduleQueueStats();
			updateCallDuration();
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
