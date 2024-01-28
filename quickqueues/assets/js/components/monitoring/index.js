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
        }
    },

    methods: {
        get_basic_stats: function() 
        {
            axios.get(api_url+'queue/get_basic_stats_for_today/')
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
            axios.get(api_url+'queue/get_freepbx_agents/')
                .then(response => {
                    this.freepbx_agents         = response.data.data;
                    this.freepbx_agents_loading = false;
            });
        },

        get_agent_realtime_status: function() 
        {
            axios.get(api_url+'agent/get_realtime_status_for_all_agents/')
                .then(response => 
                {
                    if (typeof(response.data.data) == 'object') 
                    {
                        this.agent_statuses = response.data.data;
                        this.agent_statuses_loading = false;
        
                        this.agents_busy        = 0;
                        this.agents_on_call     = 0;
                        this.agents_free        = 0;
                        this.agents_unavailable = 0;
        
                        for (let fa in this.freepbx_agents) 
                        {
                            if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Idle')
                            {
                                this.agents_free++;
                            }
                            if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Unavailable') 
                            {
                                this.agents_unavailable++;
                            }
                            if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'InUse') 
                            {
                                this.agents_on_call++;
                            }
                            if (this.agent_statuses[this.freepbx_agents[fa].extension]['StatusText'] == 'Busy') 
                            {
                                this.agents_busy++;
                            }
                        }
                    }
                });
        },
       
        isAgentOnCall(agentExtension) 
        {
            return this.agent_statuses[agentExtension]['StatusText'] === 'InUse';
        },

        updateCallDuration: function(agentExtension) 
        {
            if (this.isAgentOnCall(agentExtension)) 
            {
                if (!this.callDurations[agentExtension]) 
                {
                    this.callDurations[agentExtension] = 0;
                }
                this.callDurations[agentExtension]++;
            }
            else
            {
                this.callDurations[agentExtension] = 0;
            }
        },

        get_realtime_data: function() 
        {
            axios.get(api_url+'queue/get_realtime_data/')
                .then(response => {
 
                        this.realtime_data         = response.data.data;
                        this.realtime_data_loading = false;
                        this.total_callers         = 0;
                        for (queue in response.data.data) 
                        {
                            this.total_callers = this.total_callers + Object.keys(response.data.data[queue]['callers']).length;
                        }  
                    
                });
        },

        get_current_calls: function() 
        {
            axios.get(api_url+'agent/get_current_calls_for_all_agents')
            .then(response => {
                if (typeof(response.data.data) == 'object') 
                {
                    this.agent_current_calls         = response.data.data;
                    this.agent_current_calls_loading = false;

                }
            })
        },

        get_agent_stats: function() 
        {
            axios.post(api_url+'agent/get_stats_for_all_queues/',this.form_data)
            .then(response => {
                this.agent_stats_loading = false;
                this.agent_stats         = response.data.data;
            });
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
        
    },

    computed: 
    {
        sortedQueueData: function() 
        {
            const sortedQueueIds = Object.keys(this.totalCallsByQueue).sort((a, b) => 
            {
              return this.totalCallsByQueue[b] - this.totalCallsByQueue[a];
            });  
    
            const queuesArray = sortedQueueIds.map(queueId => 
            {
                if (this.queueIdContainsCallback(queueId)) 
                {
                    return null; // Skip this queue
                }
                let realtimeData = {};
                for (const key in this.realtime_data) 
                {
                    if (this.realtime_data.hasOwnProperty(key) && this.realtime_data[key].data.displayName === queueId) 
                    {
                    realtimeData = this.realtime_data[key];
                    }
                }
                const queueName = realtimeData.data ? realtimeData.data.Queue : "";
            
            
                return {
                    queueId: queueId,
                    totalCalls: this.totalCallsByQueue[queueId],
                    callers: realtimeData.callers || {},
                    queue: queueName,
                };
            });
            const filteredQueuesArray = queuesArray.filter(queue => queue !== null);
            return filteredQueuesArray;
          },
          

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
        $('#nav_monitoring').addClass('active text-primary');

		//This must be executes first, as it draws the page
		this.get_agent_realtime_status();
		this.get_basic_stats();
		this.get_freepbx_agents();
		this.get_agent_stats();
		this.get_realtime_data();
		this.get_queue_stats();	

        setInterval(() => this.get_queue_stats(), 5000);
        setInterval(() => this.get_basic_stats(), 60000);
        setInterval(() => this.get_agent_stats(), 6000);
        setInterval(() => this.get_freepbx_agents(), 3000);
        setInterval(() => this.get_agent_realtime_status(), 2000);
        setInterval(() => this.get_realtime_data(), 2000);
        setInterval(() => this.get_current_calls(), 3000);

        setInterval(() => 
        {
            for (const key in this.agent_statuses) 
            {
                this.updateCallDuration(key);
            }
        }, 1000);
        
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
    setInterval(autoSortTable, 1000);
});
