<div class="container-lg mt-3" id="agent_stats">
    <div class="row">
        <div class="col">
            <div class="row mb-2">
                <div class="col">
                    <div class="card border-top-info border-info border-top-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
									<?php
									// Assuming you have access to the $this->db object in your view. If not, this logic should ideally be in your controller.

									$agent_id = $this->uri->segment(3); // Assuming '3' is the correct segment number for the agent_id in your URL structure.

									// Check if $agent_id is numeric
									if (!is_numeric($agent_id)) {
										redirect(site_url('agents')); // Redirect if not numeric
									}

									// Initialize variables to store agent information
									$agentDisplayName = null;
									$agentFound = false;

									// Attempt to fetch the agent from the active agents table
									$queryActive = $this->db->select('name, display_name')->from('qq_agents')->where('id', $agent_id)->get();
									if ($queryActive && $queryActive->num_rows() > 0) {
										$agentFound = true;
										$agentDisplayName = $queryActive->row()->display_name;
										
										// Check for Mobile Forwarding
										if (preg_match("/Local\/(.+?)@from-queue\/n/", $queryActive->row()->name, $matches)) {
											if ($queryActive->row()->name != $queryActive->row()->display_name){
												$agentDisplayName = $matches[1]."-".$queryActive->row()->display_name;
											}
											else {
												$agentDisplayName = $matches[1];
											}
										}										
									}

									// If not found in active agents, attempt to fetch the agent from the archived agents table
									if (!$agentFound) {
										$queryArchived = $this->db->select('name, display_name, last_call')->from('qq_agents_archived')->where('agent_id', $agent_id)->get();
										if ($queryArchived && $queryArchived->num_rows() > 0) {
											$agentFound = true;
											$agentDisplayName = $queryArchived->row()->display_name;
											$agentLastCall = $queryArchived->row()->last_call;
											
											// Check for Mobile Forwarding
											if (preg_match("/Local\/(.+?)@from-queue\/n/", $queryArchived->row()->name, $matches)) {
												if ($queryArchived->row()->name != $queryArchived->row()->display_name && $matches[1] != $queryArchived->row()->display_name){
													$agentDisplayName = $matches[1]."-".$queryArchived->row()->display_name;
												}
												else {
													$agentDisplayName = $matches[1];
												}
											}											
										}
									}

									// Check if the agent was found in either table
									if (!$agentFound) {
										// Agent not found in both tables, redirect
										redirect(site_url('agents'));
									}
									
									?>
									<h4 class="card-title"><?php echo lang('agent') . ": " . $agentDisplayName; ?></h4>
                                    <div class="small text-medium-emphasis mb-3"><?php echo lang('stats'); ?></div>
                                </div>
                                <div class="btn-toolbar d-none d-md-block" role="toolbar">
                                    <div class="btn-group btn-group-toggle mx-3" data-coreui-toggle="buttons">
                                        <a class="btn btn-success" href="<?php echo site_url('agents/'); ?>"><i class="cil-media-step-backward"></i> <?php echo lang('back'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group">
                                <input class="form-control" autocomplete="off" tupe="text" id="date_gt" name="date_gt" placeholder="<?php echo lang('date_gt'); ?>" value="<?php echo $this->input->get('date_gt'); ?>">
                                <input class="form-control" autocomplete="off" tupe="text" id="date_lt" name="date_lt" placeholder="<?php echo lang('date_lt'); ?>" value="<?php echo $this->input->get('date_gt'); ?>">
                                <div class="btn-group">
                                    <button id="predefined_periods" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                    <div class="dropdown-menu" aria-labelledby="predefined_periods" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 36px, 0px);">
                                        <a @click="change_interval('today')" class="dropdown-item" href="javascript:;"><?php echo lang('today'); ?></a>
                                        <a @click="change_interval('yday')" class="dropdown-item" href="javascript:;"><?php echo lang('yesterday'); ?></a>
                                        <a @click="change_interval('tweek')" class="dropdown-item" href="javascript:;"><?php echo lang('this_week'); ?></a>
                                        <a @click="change_interval('tmonth')" class="dropdown-item" href="javascript:;"><?php echo lang('this_month'); ?></a>
                                        <a @click="change_interval('l7day')" class="dropdown-item" href="javascript:;"><?php echo lang('last_7_days'); ?></a>
                                        <a @click="change_interval('l14day')" class="dropdown-item" href="javascript:;"><?php echo lang('last_14_days'); ?></a>
                                        <a @click="change_interval('l30day')" class="dropdown-item" href="javascript:;"><?php echo lang('last_30_days') ;?></a>
                                    </div>
                                    <button @click="refresh" class="btn btn-primary" type="button"><?php echo lang('refresh') ?></button>
                                    <button @click="export_stats" type="button" class="btn btn-primary"><i class="cil cil-cloud-download"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <div class="row">
                <div class="col">
                    <ul class="nav nav-pills" id="start_overview" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-coreui-toggle="tab" data-coreui-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">{{ lang['system_overview'] }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hours-tab" data-coreui-toggle="tab" data-coreui-target="#hours" type="button" role="tab" aria-controls="hours" aria-selected="false">{{ lang['hours'] }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="days-tab" data-coreui-toggle="tab" data-coreui-target="#days" type="button" role="tab" aria-controls="days" aria-selected="false">{{ lang['days'] }}</button>
                        </li>
                        <?php if ($this->data->config->app_call_categories == 'yes') { ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="categories-tab" data-coreui-toggle="tab" data-coreui-target="#categories" type="button" role="tab" aria-controls="categories" aria-selected="false">{{ lang['categories'] }}</button>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="tab-content" id="start_tabs">
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <div class="row mt-2">
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="card border-top-dark border-dark border-top-3">
                                <ul class="list-group">
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<span>
											<i class="cil-list-rich text-primary mr-2"></i>
											<span><?php echo lang('last_call'); ?></span>
										</span>
										<!-- Conditionally display $agentLastCall if it exists -->
										<?php if (!empty($agentLastCall)): ?>
											<span><?php echo $agentLastCall; ?></span>
										<?php else: ?>
											<!-- Display some default text or leave blank if $agentLastCall does not exist -->
											<span>{{ agent.last_call }}</span>
										<?php endif; ?>
									</li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-list-rich text-primary mr-2"></i>
											<a class="text-decoration-none link-dark" :href="app_url+'/recordings?date_gt='+date_gt+'&date_lt='+date_lt+'&agent_id=' + <?php echo is_object($agent) ? $agent->id : '0'; ?>">{{ lang['calls_total'] }}</a>
                                        </span>
                                        <span>{{ calls_total }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-list-rich text-primary mr-2"></i>
                                           {{ lang['local_calls'] }}
                                        </span>
                                        <span>{{ total_stats.calls_total_local }}</span>
                                    </li>									
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-check text-success mr-4"></i>
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?event_type=ANSWERED&date_gt='+date_gt+'&date_lt='+date_lt+'&agent_id='+<?php echo is_object($agent) ? $agent->id : '0'; ?>">{{ lang['start_menu_calls_answered'] }}</a>
                                        </span>
                                        <span>{{ calls_answered }}</span>
                                    </li>
                                    <!-- SLA Hold Time-->
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-watch text-success mr-2"></i>
                                             {{ lang['start_menu_sla_less_than_or_equal_to_10'] }}
                                        </span>
                                        <span>{{ total_stats.sla_count_less_than_or_equal_to_10 + ' (' + sla_count_less_than_or_equal_to_10_percent + ')' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-watch text-info mr-2"></i>
                                            {{ lang['start_menu_sla_greater_than_10_less_then_or_equal_to_20'] }}
                                        </span>
                                        <span>{{ total_stats.sla_count_greater_than_10_and_less_than_or_equal_to_20 + ' ('+ sla_count_greater_than_10_and_less_than_or_equal_to_20_percent +')' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-watch text-danger mr-2"></i>
                                             {{ lang['start_menu_sla_greater_than_20'] }}
                                        </span>
                                        <span>{{ total_stats.sla_count_greater_than_20 + ' ('+ sla_count_greater_than_20_percent +')' }}</span>
                                    </li>
									
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-av-timer text-warning mr-4"></i>
                                            {{ lang['ring_time']+' ('+lang['avg']+')' }}
                                        </span>
                                        <span>{{ring_time_avg }}</span>
                                    </li>
									<li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-av-timer text-warning mr-4"></i>
                                            {{ lang['ring_time']+' ('+lang['max']+')' }}
                                        </span>
                                        <span>{{ ring_time_max }}</span>
                                    </li>
									
                                    <!-- End Of SLA Hold Time -->
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-delete text-warning mr-2"></i>
                                            <a class="text-decoration-none link-dark link-dark" :href="app_url+'/recordings?event_type=RINGNOANSWER&date_gt='+date_gt+'&date_lt='+date_lt+'&agent_id='+<?php echo is_object($agent) ? $agent->id : '0'; ?>">{{ lang['ringnoanswer'] }}</a>
                                        </span>
                                        <span>{{ total_stats.calls_missed }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-left text-success mr-4"></i>
                                            {{ lang['incoming_talk_time_sum_overview'] }}
                                        </span>
                                        <span>{{ incoming_total_calltime_count }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-left text-success mr-4"></i>
                                            {{ lang['incoming_talk_time_avg'] }}
                                        </span>
                                        <span>{{ incoming_total_calltime_avg }}</span>
                                    </li>
                                    
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-left text-success mr-4"></i>
                                            {{ lang['incoming_talk_time_max'] }}
                                        </span>
                                        <span>{{ incoming_total_calltime_max }}</span>
                                    </li>
									
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-right text-success mr-4"></i>
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?event_type=OUT_ANSWERED&date_gt='+date_gt+'&date_lt='+date_lt+'&agent_id='+<?php echo isset($agent) && is_object($agent) ? $agent->id : '0'; ?>"><?php echo lang('calls_outgoing_answered'); ?></a>
                                        </span>
                                        <span>{{ total_stats.calls_outgoing_answered }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-right text-danger mr-4"></i>
                                            {{ lang['calls_outgoing_failed'] }}
                                        </span>
                                        <span>{{ calls_outgoing_unanswered }}</span>
                                    </li>
									
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-right text-primary mr-4"></i>
                                            {{ lang['outgoing_talk_time_sum_overview'] }}
                                        </span>
                                        <span>{{ outgoing_total_calltime_count }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-right text-primary mr-4"></i>
                                            {{ lang['outgoing_talk_time_avg'] }}
                                        </span>
                                        <span>{{ outgoing_total_calltime_avg }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-right text-primary mr-4"></i>
                                            {{ lang['outgoing_talk_time_max'] }}
                                        </span>
                                        <span>{{ outgoing_total_calltime_max }}</span>
                                    </li>
                                    <!--<li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-av-timer text-success mr-4"></i>
                                            {{ lang['call_time']+' ('+lang['avg']+')' }}
                                        </span>
                                        <span>{{ call_time_avg }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-av-timer text-warning mr-4"></i>
                                            {{ lang['ring_time']+' ('+lang['avg']+')' }}
                                        </span>
                                        <span>{{ ring_time_avg }}</span>
                                    </li>-->
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                            <div class="card border-top-primary border-dark border-top-3">
                                <div class="row">
                                    <div class="col">
                                        <div class="c-chart-wrapper">
                                            <canvas id="canvas_event_distrib" ></canvas>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col">
                                        <div class="c-chart-wrapper">
                                            <canvas id="canvas_agent_distrib" ></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="hours" role="tabpanel" aria-labelledby="hours-tab">
                    <div class="row mt-2">
                        <div class="col">
                            <div class="card border-top-info border-info border-top-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width:40%">{{ lang['hour'] }}</th>
                                            <th scope="col">{{ lang['calls_answered'] }}</th>
                                            <th scope="col">{{ lang['incoming_talk_time_sum'] }}</th>
                                            <th scope="col">{{ lang['ringnoanswer'] }}</th>
                                            <th scope="col">{{ lang['calls_outgoing_answered'] }}</th>
                                            <th scope="col">{{ lang['outgoing_talk_time_sum'] }}</th>
                                            <th scope="col">{{ lang['calls_outgoing_failed'] }}</th>
                                            <th scope="col">{{ lang['hold_time'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="overview_hours">
                                        <tr v-for="h in hourly_stats">
                                            <td>{{ h.hour }}</td>
                                            <td>{{ h.calls_answered }}</td>
                                            <td>{{ sec_to_time(h.incoming_total_calltime) }}</td>
                                            <td>{{ h.calls_missed }}</td>
                                            <td>{{ h.calls_outgoing_answered }}</td>
                                            <td>{{ sec_to_time(h.outgoing_total_calltime) }}</td>
                                            <td>{{ h.calls_outgoing_unanswered }}</td>
                                            <td>{{ sec_to_time(h.avg_holdtime) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="days" role="tabpanel" aria-labelledby="days-tab">
                    <div class="row mt-2">
                        <div class="col">
                            <div class="card border-top-primary border-primary border-top-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width:40%">{{ lang['day'] }}</th>
                                            <th scope="col">{{ lang['calls_answered'] }}</th>
                                            <th scope="col">{{ lang['incoming_talk_time_sum'] }}</th>
                                            <th scope="col">{{ lang['ringnoanswer'] }}</th>
                                            <th scope="col">{{ lang['calls_outgoing_answered'] }}</th>
                                            <th scope="col">{{ lang['outgoing_talk_time_sum'] }}</th>
                                            <th scope="col">{{ lang['calls_outgoing_failed'] }}</th>
                                            <th scope="col">{{ lang['hold_time'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="overview_days">
                                        <tr v-for="d in daily_stats">
                                            <td>{{ d.day }}</td>
                                            <td>{{ d.calls_answered }}</td>
                                            <td>{{ sec_to_time(d.incoming_total_calltime) }}</td>
                                            <td>{{ d.calls_missed }}</td>
                                            <td>{{ d.calls_outgoing_answered }}</td>
                                            <td>{{ sec_to_time(d.outgoing_total_calltime) }}</td>
                                            <td>{{ d.calls_outgoing_unanswered }}</td>
                                            <td>{{ d.avg_holdtime }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
