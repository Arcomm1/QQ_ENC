Vue.config.devtools = true;

var agent_overview = new Vue({

	el: '#agent_overview',
	data () {
		return {
			agents              : {},
			agents_missing		: {},
			agents_loading      : true,
			agents_error        : false,
			refreshStatsInterval: null,
		}
	},
    
	methods: {
	startEditing(agent) 
	{
		this.$set(agent, 'isEditing', true);
        this.$set(agent, 'editedDisplayName', agent.display_name);
	},
	updateDisplayName(agent) 
	{
		const formData = new FormData();
    	formData.append('value', agent.editedDisplayName);
		axios.post(api_url + 'agent/update_agents/' + agent.agent_id, formData)
		.then(response => 
		{
			if (response.data.status === 'OK') 
			{
				agent.display_name = agent.editedDisplayName;
				this.$set(agent, 'isEditing', false);
			} 
			else 
			{
				console.error('Error updating display name:', response.data.message);
			}
		})
		.catch(error => 
		{
			console.error('Error updating display name:', error);
		});
	},
	
	cancelEditing(agent) 
	{
		this.$set(agent, 'isEditing', false);
        this.$set(agent, 'editedDisplayName', '');
	},
	
	get_overview: function() {
		this.agents_loading = true;
		axios.get(api_url + 'agent/get_stats_for_start/')
			.then(response => {
				if (typeof(response.data.data) == 'object') {
					const agentsData = response.data.data;
					const reorganizedAgents = {}; // Object for agents with valid extensions
					const reorganizedAgentsMissing = []; // Array for agents missing extensions

					Object.keys(agentsData).forEach((agentId, index) => {
						const agent = agentsData[agentId];

						// Check if agent.extension exists and is a valid number
						if (agent.extension && !isNaN(parseInt(agent.extension))) {
							const extensionKey = parseInt(agent.extension);
							reorganizedAgents[extensionKey] = agent; // Use extensionKey as the key
						} else {
							// Directly push agent into the missing array
							reorganizedAgentsMissing.push(agent);
						}
					});

					// Assign the structured data to Vue data properties
					this.agents = reorganizedAgents;
					this.agents_missing = reorganizedAgentsMissing;

					// Optional: Log the structured data for verification
					console.log(this.agents);
					console.log(this.agents_missing);
				}
			})
			.finally(() => {
				this.agents_loading = false;
			});
	},


	refresh_stats: function() 
    {
		this.refreshStatsInterval = setInterval(() => this.get_overview(), 60000);
	},	

	destroyed() 
	{
		clearInterval(this.refreshStatsInterval);
	},
  },

	mounted () 
	{
			this.get_overview();
	},

	created () 
	{
		$('#nav_agents').addClass('active text-primary');
		this.refresh_stats();
	}
});



