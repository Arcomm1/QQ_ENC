<div class="container-lg mt-3" id="queue_stats">
    <div class="row">
        <div class="col">
            <div class="row mb-2">
                <div class="col">
                    <div class="card border-top-info border-info border-top-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h4 class="card-title"><?php echo lang('queue').": ".$queue->display_name; ?></h4>
                                    <div class="small text-medium-emphasis mb-3"><?php echo lang('stats'); ?></div>
                                </div>
                                <div class="btn-toolbar d-none d-md-block" role="toolbar">
                                    <div class="btn-group btn-group-toggle mx-3" data-coreui-toggle="buttons">
                                        <a class="btn btn-success" href="<?php echo site_url('queues/'); ?>"><i class="cil-media-step-backward"></i> <?php echo lang('back'); ?></a>
                                        <a class="btn btn-info" href="<?php echo site_url('queues/realtime/'.$queue->id); ?>"><i class="cil-bar-chart"></i> <?php echo lang('realtime'); ?></a>
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
                            <button class="nav-link" id="agents-tab" data-coreui-toggle="tab" data-coreui-target="#agents" type="button" role="tab" aria-controls="agents" aria-selected="false">{{ lang['agents'] }}</button>
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
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?date_gt='+date_gt+'&date_lt='+date_lt">{{ lang['calls_total'] }}</a>
                                        </span>
                                        <span>{{ calls_total }}</span>
                                    </li>
                                   <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-list-rich text-primary mr-2"></i>
                                            <a class="text-decoration-none link-dark">{{ lang['calls_unique_in'] }}</a>
                                        </span>
                                        <span>{{ unique_incoming_calls }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-list-rich text-primary mr-2"></i>
                                            <a class="text-decoration-none link-dark">{{ lang['calls_unique_users'] }}</a>
                                        </span>
                                        <span>{{ unique_calls }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-check text-success mr-4"></i>
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?event_type=ANSWERED&date_gt='+date_gt+'&date_lt='+date_lt">{{ lang['start_menu_calls_answered'] }}</a>
                                        </span>
                                        <span>{{ calls_answered_percent }}</span>
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
                                    <!-- End Of SLA Hold Time -->
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-av-timer text-warning mr-4"></i>
                                            {{ lang['hold_time']+' ('+lang['max']+')' }}
                                        </span>
                                        <span>{{ sec_to_time(total_stats.max_holdtime) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-av-timer text-warning mr-4"></i>
                                            {{ lang['hold_time']+' ('+lang['avg']+')' }}
                                        </span>
                                        <span>{{ hold_time_avg }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-delete text-warning mr-2"></i>
                                            <a class="text-decoration-none link-dark link-dark" :href="app_url+'/recordings?event_type=UNANSWERED&date_gt='+date_gt+'&date_lt='+date_lt">{{ lang['start_menu_calls_unanswered'] }}</a>
                                        </span>
                                        <span>{{ calls_unanswered_percent }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-av-timer text-warning mr-4"></i>
                                            {{ lang['ata'] }}
                                        </span>
                                        <span>{{ ata_time_avg }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-delete text-danger mr-2"></i>
                                            <a class="text-decoration-none link-dark" :href="app_url+'/AdditionalRecordings?event_type=UNANSWERED&calls_without_service=yes&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_without_service'); ?></a>
                                        </span>
                                        <span>{{ total_stats.calls_without_service }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-check text-warning mr-4"></i>
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?answered_elsewhere=yes&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('answered_elsewhere'); ?></a>

                                        </span>
                                        <span>{{ total_stats.answered_elsewhere }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-reload text-success mr-4"></i>
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?called_back=yes&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('answered_aoutcall'); ?></a>
                                        </span>
                                        <span>{{ total_stats.called_back }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-right text-success mr-4"></i>
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?event_type=OUT_ANSWERED&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_outgoing_answered'); ?></a>
                                        </span>
                                        <span>{{ total_stats.calls_outgoing_answered }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-right text-danger mr-4"></i>
                                            {{ lang['calls_outgoing_failed'] }}
                                        </span>
                                        <span>{{ total_stats.calls_outgoing_unanswered }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-copy text-primary mr-4"></i>
                                            {{ lang['duplicate_calls'] }}
                                        </span>
                                        <span>{{ total_stats.calls_duplicate }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-lock-locked text-danger mr-4"></i>
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?event_type=INCOMINGOFFWORK &date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('start_menu_calls_offwork'); ?></a>
                                            <!-- {{ lang['calls_offwork'] }} -->
                                        </span>
                                        <!-- total -->
                                        <span>{{ total_stats.calls_offwork }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-left text-success mr-4"></i>
                                            {{ lang['incoming_talk_time_sum'] }}
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
                                        <span>{{ total_stats.incoming_total_calltime_count > 0 ? sec_to_time(Math.floor(total_stats.incoming_max_calltime)) : 0 }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-arrow-thick-right text-primary mr-4"></i>
                                            {{ lang['outgoing_talk_time_sum'] }}
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
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-list-filter text-primary mr-4"></i>
                                            {{ lang['start_menu_calls_waiting']+' ('+lang['avg']+')' }}
                                        </span>
                                        <span>{{ Math.ceil(total_stats.origposition_avg) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-list-filter text-danger mr-4"></i>
                                            {{ lang['start_menu_calls_waiting']+' ('+lang['max']+')' }}
                                        </span>
                                        <span>{{ total_stats.origposition_max == null ? 0 : total_stats.origposition_max }}</span>
                                    </li>
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

                <div class="tab-pane fade" id="agents" role="tabpanel" aria-labelledby="agents-tab">
                    <div class="row mt-2">
                        <div class="col">
                            <div class="card border-top-warning border-warning border-top-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                        <th scope="col" style="width:60%">{{ lang['agent'] }}</th>
                                            <th scope="col">{{ lang['calls_answered'] }}</th>
                                            <th scope="col">{{ lang['incoming_talk_time_sum'] }}</th>
                                            <th scope="col">{{ lang['calls_missed'] }}</th>
                                            <th scope="col">{{ lang['calls_outgoing_answered'] }}</th>
                                            <th scope="col">{{ lang['outgoing_talk_time_sum'] }}</th>
                                            <th scope="col">{{ lang['calls_outgoing_failed'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="overview_agents">
                                        <tr v-for="a in agent_stats">
                                        <td v-if="a.display_name">{{ a.display_name }}</td>
                                            <td v-if="a.display_name">{{ a.calls_answered }}</td>
                                            <td v-if="a.display_name">{{ sec_to_time(a.incoming_total_calltime) }}</td>
                                            <td v-if="a.display_name">{{ a.calls_missed }}</td>
                                            <td v-if="a.display_name">{{ a.calls_outgoing_answered}}</td>
                                            <td v-if="a.display_name">{{ sec_to_time(a.outgoing_total_calltime) }}</td>
                                            <td v-if="a.display_name">{{ a.calls_outgoing_unanswered}}</td>
                                        </tr>
                                    </tbody>
                                </table>
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
                                            <th scope="col">{{ lang['calls_missed'] }}</th>
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
                                            <td>{{ sec_to_time(h.outhoing_total_calltime) }}</td>
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
                                            <th scope="col">{{ lang['calls_missed'] }}</th>
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
                                            <td>{{ sec_to_time(d.outhoing_total_calltime) }}</td>
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
