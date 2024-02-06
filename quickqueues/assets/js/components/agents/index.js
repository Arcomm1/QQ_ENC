Vue.config.devtools = true;

var agent_overview = new Vue({

	el: '#agent_overview',
	data () {
		return {
			agents              : {},
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
	  get_overview: function () 
		{
			this.agents_loading = true;
			axios.get(api_url + 'agent/get_stats_for_start/')
			.then(response => 
				{
					if (typeof (response.data.data) == 'object') 
					{
						const agentsData        = response.data.data;
						const reorganizedAgents = {};
						Object.keys(agentsData).forEach(agentId => 
							{
								const agent                  = agentsData[agentId];
								const extension              = parseInt(agent.extension);
								reorganizedAgents[extension] = agent;
						});

						this.agents = reorganizedAgents;

						// DnD Status for Agents
						Object.keys(this.agents).forEach(agentId => 
						{
							axios.get(api_url + 'Dnd/get_agent_dnd_status/' + agentId)
								.then(agentResponse => 
									{
										const agentInfo = agentResponse.data;
										this.$set(this.agents[agentId], 'dnd_status_pushed', agentInfo.dnd_status);
										this.$set(this.agents[agentId], 'dnd_duration_pushed', agentInfo.dnd_duration);
										this.$set(this.agents[agentId], 'dnd_subject_title_pushed', agentInfo.dnd_subject_title);
								})
								.catch(error => 
								{
										console.error('Error fetching data:', error);
								});
						});
					}
			})
			.finally(() => 
			{
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



