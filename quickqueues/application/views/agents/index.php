<div class="container-lg mt-3" id="agent_overview">
    <div class="row">
        <div class="col">
            <div class="card border-top-primary border-primary border-top-3">
                <div class="card-body">
                    <h4 class="card-title mb-3"><?php echo lang('agents'); ?></h4>
                    <!-- Ordering -->
                    <div class="table-responsive">
                    <table class="table agents_table">
                        <thead class="table-light fw-semibold">
                            <tr>
                                <th scope="col"><?php echo lang('agent'); ?></th>
                                <th scope="col"><?php echo lang('calls_answered'); ?></th>
								<th scope="col"><?php echo lang('local_calls'); ?></th>
                                <th scope="col"><?php echo lang('incoming_talk_time_sum'); ?></th>
                                <th scope="col"><?php echo lang('ringnoanswer'); ?></th>
                                <th scope="col"><?php echo lang('calls_outgoing_answered'); ?></th>
                                <th scope="col"><?php echo lang('outgoing_talk_time_sum'); ?></th>
                                <th scope="col"><?php echo lang('calls_outgoing_failed'); ?></th>
                                <th scope="col"><?php echo lang('dnd'); ?></th>
                                <!-- <th scope="col"></th> -->
                                <th scope="col"><?php echo lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="monitoring-agents-dashboard-table-body">
                          <tr v-for="agent in agents" v-if="agent && agent.display_name">
                                <td style="width: 300px;">
								  <div style="display:flex; gap: 10px;">
									  <div style="width: 150px">
										  <div>
											  <span v-if="!agent.isEditing" :key="agent.display_name" @click="startEditing(agent)" style="cursor: pointer; text-decoration: underline; color: rgba(44, 56, 74, 0.95);">{{ agent.display_name }}</span>
											  <?php if ($this->data->logged_in_user->role === 'admin'): ?>
											  <input class="form-control"v-else v-model="agent.editedDisplayName" @keyup.enter="updateDisplayName(agent)" />
											  <?php endif; ?>
										  </div>
										  <div class="small text-medium-emphasis">
											  <span>
												  <span>{{ agent.extension }}</span>
											  </span>
											  {{ " | "+agent.last_call }}
										  </div>
									  </div>
									  <div style="width: 150px">
										  <?php if ($this->data->logged_in_user->role === 'admin'): ?>
										  <button class="btn" v-if="agent.isEditing" @click="cancelEditing(agent)">Cancel</button>
										  <?php endif; ?>
									  </div>
								  </div>
                                </td>
                                <td>
                                    {{ agent.calls_answered }}
                                </td>
                                <td>
                                    {{ agent.calls_total_local }}
                                </td>								
                                <td>                        
                                <td>
                                    {{ sec_to_time(agent.incoming_total_calltime) }}
                                </td>
                                <td>
                                    {{ agent.calls_missed }}
                                </td>
                                <td>
                                    {{ agent.calls_outgoing_answered }}
                                </td>
                                <td>
                                    {{ sec_to_time(agent.outgoing_total_calltime) }}
                                </td>
                                <td>
                                    {{ agent.calls_outgoing_unanswered }}
                                </td>
                                <td>
                                    {{ sec_to_time(agent.total_pausetime) }}
                                <td>
                                <!-- <td>
                                    <span v-if="agent.dnd_status_pushed == 'on'" style="color:red">
                                        {{ agent.dnd_status_pushed }} - {{ agent.dnd_subject_title_pushed }}
                                        <div>
                                            {{ agent.dnd_duration_pushed }}
                                        </div>
                                    </span>
                                    <span v-else>
                                        {{ agent.dnd_status_pushed}}
                                    </span>
                                </td> -->
                                <td>
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-ghost-success" v-bind:href="'agents/dndperagent/'+agent.agent_id"><i class="cil-media-pause"></i></a>
                                        <a class="btn btn-ghost-info" v-bind:href="'agents/stats/'+agent.agent_id"><i class="cil-bar-chart"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <h4 class="card-title mb-3"><?php echo lang('mobile_forward'); ?></h4>
                   <div class="table-responsive">
                    <table class="table agents_table">
                        <tbody class="monitoring-agents-dashboard-table-body">
                          <tr v-for="agent in agents_missing" v-if="agent && agent.display_name">
                                <td style="width: 300px;">
								  <div style="display:flex; gap: 10px;">
									  <div style="width: 150px">
										  <div>
											  <span v-if="!agent.isEditing" :key="agent.display_name" @click="startEditing(agent)" style="cursor: pointer; text-decoration: underline; color: rgba(44, 56, 74, 0.95);">{{ agent.display_name }}</span>
											  <?php if ($this->data->logged_in_user->role === 'admin'): ?>
											  <input class="form-control"v-else v-model="agent.editedDisplayName" @keyup.enter="updateDisplayName(agent)" />
											  <?php endif; ?>
										  </div>
										  <div class="small text-medium-emphasis">
											  <span>
												  <span>{{ agent.extension }}</span>
											  </span>
											  {{ " | "+agent.last_call }}
										  </div>
									  </div>
									  <div style="width: 150px">
										  <?php if ($this->data->logged_in_user->role === 'admin'): ?>
										  <button class="btn" v-if="agent.isEditing" @click="cancelEditing(agent)">Cancel</button>
										  <?php endif; ?>
									  </div>
								  </div>
                                </td>
                                <td>
                                    {{ agent.calls_answered }}
                                </td>
                                <td>
                                    {{ agent.calls_total_local }}
                                </td>								
                                <td>                        
                                <td>
                                    {{ sec_to_time(agent.incoming_total_calltime) }}
                                </td>
                                <td>
                                    {{ agent.calls_missed }}
                                </td>
                                <td>
                                    {{ agent.calls_outgoing_answered }}
                                </td>
                                <td>
                                    {{ sec_to_time(agent.outgoing_total_calltime) }}
                                </td>
                                <td>
                                    {{ agent.calls_outgoing_unanswered }}
                                </td>
                                <td>
                                    {{ sec_to_time(agent.total_pausetime) }}
                                <td>
                                <!-- <td>
                                    <span v-if="agent.dnd_status_pushed == 'on'" style="color:red">
                                        {{ agent.dnd_status_pushed }} - {{ agent.dnd_subject_title_pushed }}
                                        <div>
                                            {{ agent.dnd_duration_pushed }}
                                        </div>
                                    </span>
                                    <span v-else>
                                        {{ agent.dnd_status_pushed}}
                                    </span>
                                </td> -->
                                <td>
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-ghost-success" v-bind:href="'agents/dndperagent/'+agent.agent_id"><i class="cil-media-pause"></i></a>
                                        <a class="btn btn-ghost-info" v-bind:href="'agents/stats/'+agent.agent_id"><i class="cil-bar-chart"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>					
                    <!--End Of Ordering-->
                </div>
            </div>
        </div>
    </div>
</div>

