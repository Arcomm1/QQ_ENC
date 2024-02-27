<script>
        window.globalSettings = <?php echo json_encode($this->data->global_settings); ?>;
</script>
<div class="container-lg mt-3" id="monitoring_dashboard">
    <div class="row mb-3">
        <div class="col">
            <div class="card border-top-info border-info border-top-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3 col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body p-0 d-flex align-items-center">
                                    <div class="bg-success text-white text-strong py-4 px-5 me-3">
                                        <svg class="icon icon-xxl">
                                            <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-check'); ?>"></use>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="fs-2 fw-semibold text-success">
                                            <span v-cloak v-if="!basic_stats_loading">
                                                {{ basic_stats.calls_answered }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-success" role="status">
                                                    <span class="visually-hidden"></span>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="text-medium-emphasis text-uppercase fw-semibold small"><?php echo lang('calls_answered'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body p-0 d-flex align-items-center">
                                    <div class="bg-warning text-white text-strong py-4 px-5 me-3">
                                        <svg class="icon icon-xxl">
                                            <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-bell-exclamation'); ?>"></use>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="fs-2 fw-semibold text-warning">
                                            <span v-cloak v-if="!basic_stats_loading">
                                                {{ basic_stats.calls_unanswered }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-warning" role="status">
                                                    <span class="visually-hidden"></span>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="text-medium-emphasis text-uppercase fw-semibold small"><?php echo lang('calls_unanswered'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body p-0 d-flex align-items-center">
                                    <div class="bg-danger text-white text-strong py-4 px-5 me-3">
                                        <svg class="icon icon-xxl">
                                            <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-x'); ?>"></use>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="fs-2 fw-semibold text-danger">
                                            <span v-cloak v-if="!basic_stats_loading">
                                                {{ basic_stats.calls_without_service }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-danger" role="status">
                                                    <span class="visually-hidden"></span>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="text-medium-emphasis text-uppercase fw-semibold small"><?php echo lang('calls_without_service'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body p-0 d-flex align-items-center">
                                    <div class="bg-info text-white text-strong py-4 px-5 me-3">
                                        <svg class="icon icon-xxl">
                                            <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-people'); ?>"></use>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="fs-2 fw-semibold text-info">{{ total_callers }}</div>
                                        <div class="text-medium-emphasis text-uppercase fw-semibold small"><?php echo lang('calls_waiting'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-12 col-xs-12 col-sm-12 mb-3 monitoring_dashboard_table">
            <div class="card border-top-primary border-primary border-top-3">
                <div class="card-body">
                    <div class="row agent-stat-numbers">
                        <div class="col">
                            <div class="border-start border-start-4 border-start-success px-3 mb-3"  style="text-align: center;"><small class="text-medium-emphasis"><?php echo lang('available'); ?></small>
                                <div class="fs-5 fw-semibold">{{ agents_free }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border-start border-start-4 border-start-info px-3 mb-3"  style="text-align: center;"><small class="text-medium-emphasis"><?php echo lang('on_call'); ?></small>
                                <div class="fs-5 fw-semibold">{{ agents_on_call }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border-start border-start-4 border-start-danger px-3 mb-3"  style="text-align: center;"><small class="text-medium-emphasis"><?php echo lang('paused'); ?></small> 
                                <div class="fs-5 fw-semibold">{{ agents_busy }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border-start border-start-4 border-start-dark px-3 mb-3"  style="text-align: center;"><small class="text-medium-emphasis"><?php echo lang('logged_out'); ?></small>
                                <div class="fs-5 fw-semibold">{{ agents_unavailable }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table class="table table-border mb-0">
                                    <thead class="table-light fw-semibold">
                                        <tr class="align-middle monitoring-table-head">
                                            <th scope="col" style="font-size: 0.85em; text-align: center;"><?php echo lang('agent'); ?></th>
                                            <th scope="col" style="font-size: 0.85em; text-align: center;"><?php echo lang('status'); ?></th>
                                            <th scope="col" style="font-size: 0.85em; text-align: center;"><?php echo lang('calls_answered'); ?></th>
                                            <th scope="col" style="font-size: 0.85em; text-align: center;"><?php echo lang('incoming_talk_time_sum'); ?></th>
                                            <th scope="col" style="font-size: 0.85em; text-align: center;"><?php echo lang('ringnoanswer'); ?></th>
                                            <th scope="col" style="font-size: 0.85em; text-align: center;"><?php echo lang('calls_outgoing_answered'); ?></th>
                                            <th scope="col" style="font-size: 0.85em; text-align: center;"><?php echo lang('outgoing_talk_time_sum'); ?></th>
                                            <th scope="col" style="font-size: 0.85em; text-align: center;"><?php echo lang('calls_outgoing_failed'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="monitoring-dashboard-table-body">
                                        <tr v-for="agent in freepbx_agents">
                                            <td  v-bind:id="'agent_status_'+agent.id"
                                                 v-if="agent_statuses[agent.extension]"
												 style="width: 500px;">
                                                <div>
                                                    <span>
                                                        <i v-if="agent_statuses[agent.extension]" v-bind:class="'cil-phone mr-3 text-white bg-' + agent_statuses[agent.extension].status_color"></i>
                                                        <i v-else class="cil-phone mr-3 text-dark"></i>
                                                    </span>
                                                    <a v-bind:href="'agents/stats/'+agent.id" class="ml-3 link-dark">{{ agent.display_name }}</a>
                                                    <span v-if="agent_current_calls[agent.extension]">
                                                        <i v-bind:class="'cil-chevron-double-'+agent_current_calls[agent.extension].direction+' mr-3 text-primary'"></i>
                                                        {{ agent_current_calls[agent.extension].second_party }}
                                                        <img src="<?php echo base_url('assets/img/copy.png'); ?>" width="20" height="20" alt="" @click="copyToClipboard(agent_current_calls[agent.extension].second_party)">
                                                    </span>
													<span v-if="sec_to_time(callDurations[agent.extension]) !== '00:00:00'">
														{{ sec_to_time(callDurations[agent.extension]) }}
													</span>
													<span v-if="isAgentPaused(agent.display_name)" class="paused-text">
														Queue Paused
													</span>                                                        
                                                    <span v-else></span> 
                                                </div>
                                                <div class="small text-medium-emphasis monitoring-dashboard-small-text">
                                                    <span>
                                                        <span>{{ agent.extension }}</span>
                                                    </span>
                                                    {{ " | "+agent.last_call }}
                                                </div>
                                            </td>

											<td style="text-align: center;">
												{{
													agent_statuses[agent.extension]
													? (
														agent_statuses[agent.extension].StatusText === 'Idle' ? 'Available' :
														agent_statuses[agent.extension].StatusText === 'Unavailable' ? 'Unavailable' :
														agent_statuses[agent.extension].StatusText === 'InUse' ? 'Active' :
														agent_statuses[agent.extension].StatusText === 'Busy' ? 'DND' :
														agent_statuses[agent.extension].StatusText === 'Ringing' ? 'Active Ringing' :
														agent_statuses[agent.extension].StatusText === 'Hold' ? 'Active OnHold' :                                                        
														agent_statuses[agent.extension].StatusText
													  )
													: 'Unavailable'
												}}
											</td>
                                            <td  v-if="agent_statuses[agent.extension]" style="text-align: center;">{{ agent_stats[agent.id]?.calls_answered }}</td>
                                            <td  v-if="agent_statuses[agent.extension]" style="text-align: center;">{{ sec_to_time(agent_stats[agent.id]?.incoming_total_calltime) }}</td>
                                            <td  v-if="agent_statuses[agent.extension]" style="text-align: center;">{{ agent_stats[agent.id]?.calls_missed }}</td>
                                            <td  v-if="agent_statuses[agent.extension]" style="text-align: center;">{{ agent_stats[agent.id]?.calls_outgoing_answered }}</td>
                                            <td  v-if="agent_statuses[agent.extension]" style="text-align: center;">{{ sec_to_time(agent_stats[agent.id]?.outgoing_total_calltime) }}</td>
                                            <td  v-if="agent_statuses[agent.extension]" style="text-align: center;">{{ agent_stats[agent.id]?.calls_outgoing_unanswered }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-top-danger border-danger border-top-3">
                <div class="card-body">
                    <div v-for="queueData in sortedQueueData" v-bind:style="{ 'border'
                        : queueIsOverloaded(queueData, ) ? '3px solid red' : '', 'border-radius': queueIsOverloaded(queueData) ? '5px' : '' }"  class="monitoring_dashboard_queue_cell">
                        <h5 class="card-title">{{ lang['queue'] + ': ' + queueData.queue + ' (' + queueData.queueId + ')' }}</h5>
                        <table class="table table-sm">
                            <thead class="table-light fw-semibold">
                                <tr class="align-middle">
                                    <th scope="col"></th>
                                    <th scope="col"><?php echo lang('number'); ?></th>
                                    <th scope="col"><?php echo lang('time'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="caller in queueData.callers">
                                    <td>{{ caller.Position }}</td>
                                    <td>{{ caller.CallerIDNum }}</td>
                                    <td>{{ sec_to_min(caller.Wait) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
#monitoring_dashboard {
    width: 100%;
    max-width: none;
}

.paused-text {
    display: block; /* This will put the text on a new line */
    color: red;     /* This will make the text red */
}
</style>
