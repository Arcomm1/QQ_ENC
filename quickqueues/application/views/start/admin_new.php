<div class="container-lg mt-3" id="start">
    <div class="row">
        <div class="col">
            <div class="row mb-2">
                <div class="col">
                    <div class="input-group">
                        <input class="form-control" autocomplete="off" tupe="text" id="date_gt" name="date_gt" 
                            placeholder="<?php echo lang('date_gt'); ?>" value="<?php echo $this->input->get('date_gt'); ?>">
                        <input class="form-control" autocomplete="off" tupe="text" id="date_lt" name="date_lt" 
                            placeholder="<?php echo lang('date_lt'); ?>" value="<?php echo $this->input->get('date_gt'); ?>">
                        <div class="btn-group">
                            <button id="predefined_periods" type="button" class="btn btn-primary dropdown-toggle" 
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu" aria-labelledby="predefined_periods" x-placement="bottom-start" 
                                style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 36px, 0px);">

                                <a @click="change_interval('today')" class="dropdown-item" href="javascript:;"><?php echo lang('today'); ?></a>
                                <a @click="change_interval('yday')" class="dropdown-item" href="javascript:;"><?php echo lang('yesterday'); ?></a>
                                <a @click="change_interval('tweek')" class="dropdown-item" href="javascript:;"><?php echo lang('this_week'); ?></a>
                                <a @click="change_interval('tmonth')" class="dropdown-item" href="javascript:;"><?php echo lang('this_month'); ?></a>
                                <a @click="change_interval('l7day')" class="dropdown-item" href="javascript:;"><?php echo lang('last_7_days'); ?></a>
                                <a @click="change_interval('l14day')" class="dropdown-item" href="javascript:;"><?php echo lang('last_14_days'); ?></a>
                                <a @click="change_interval('l30day')" class="dropdown-item" href="javascript:;"><?php echo lang('last_30_days') ;?></a>

                            </div>
                            <!-- <button id='refresh_subject'  class="btn btn-primary" type="button"><?php echo lang('refresh') ?></button> -->
                            <button @click='refresh' class="btn btn-primary" type="button"><?php echo lang('refresh') ?></button>
                            <button @click="export_stats" type="button" class="btn btn-primary"><i class="cil cil-cloud-download"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <ul class="nav nav-pills" id="start_overview" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-coreui-toggle="tab" data-coreui-target="#overview"
                                type="button" role="tab" aria-controls="overview" aria-selected="true">
                                {{ lang['system_overview'] }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="agents-tab" data-coreui-toggle="tab" data-coreui-target="#agents" 
                                type="button" role="tab" aria-controls="agents" aria-selected="false">
                                {{ lang['agents'] }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="queues-tab" data-coreui-toggle="tab" data-coreui-target="#queues" 
                                type="button" role="tab" aria-controls="queues" aria-selected="false">
                                {{ lang['queues'] }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hours-tab" data-coreui-toggle="tab" data-coreui-target="#hours" 
                                type="button" role="tab" aria-controls="hours" aria-selected="false">
                                {{ lang['hours'] }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="days-tab" data-coreui-toggle="tab" data-coreui-target="#days" 
                                type="button" role="tab" aria-controls="days" aria-selected="false">
                                {{ lang['days'] }}
                            </button>
                        </li>
                        <?php if ($this->data->config->app_call_categories == 'yes') { ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="categories-tab" data-coreui-toggle="tab" data-coreui-target="#categories" 
                                type="button" role="tab" aria-controls="categories" aria-selected="false">
                                {{ lang['categories'] }}
                            </button>
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
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?date_gt='+date_gt+'&date_lt='+date_lt">
                                                {{ lang['calls_total'] }}
                                            </a>
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
                                            <a class="text-decoration-none link-dark" 
                                                :href="app_url+'/recordings?event_type=ANSWERED&date_gt='+date_gt+'&date_lt='+date_lt">
                                                {{ lang['start_menu_calls_answered'] }}
                                            </a>
                                        </span>
                                        <span>{{ total_stats.calls_answered }}</span>
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
                                        <span>{{ hold_time_max }}</span>
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
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?event_type=UNANSWERED&calls_without_service=yes&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_without_service'); ?></a>
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
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?event_type=OUT_UNANSWERED&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_outgoing_failed'); ?></a>
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
                                        <span>{{ incoming_total_calltime_max }}</span>
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
                                        <span>{{ total_stats.origposition_max }}</span>
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
                                <table class="table">
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
                                            <td v-if="a.display_name">{{ a.calls_outgoing_answered }}</td>
                                            <td v-if="a.display_name">{{ sec_to_time(a.outgoing_total_calltime) }}</td>
                                            <td v-if="a.display_name">{{ a.calls_outgoing_unanswered }}</td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="queues" role="tabpanel" aria-labelledby="queues-tab">
                    <div class="row mt-2">
                        <div class="col">
                            <div class="card border-top-success border-success border-top-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                        <th scope="col" style="width:40%">{{ lang['queue'] }}</th>
                                            <th scope="col">{{ lang['calls_answered'] }}</th>
                                            <th scope="col">{{ lang['incoming_talk_time_sum'] }}</th>
                                            <th scope="col">{{ lang['calls_missed'] }}</th>
                                            <th scope="col">{{ lang['calls_outgoing_answered'] }}</th>
                                            <th scope="col">{{ lang['outgoing_talk_time_sum'] }}</th>
                                            <th scope="col">{{ lang['calls_outgoing_failed'] }}</th>
                                            <th scope="col">{{ lang['hold_time'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="overview_queues">
                                        <tr v-for="q in queue_stats">
                                        <td v-if="q.display_name">{{ q.display_name }}</td>
                                            <td v-if="q.display_name">{{ q.calls_answered }}</td>
                                            <td v-if="q.display_name">{{ sec_to_time(q.incoming_total_calltime) }}</td>
                                            <td v-if="q.display_name">{{ q.calls_missed }}</td>
                                            <td v-if="q.display_name">{{ q.calls_outgoing_answered }}</td>
                                            <td v-if="q.display_name">{{ sec_to_time(q.outgoing_total_calltime) }}</td>
                                            <td v-if="q.display_name">{{ q.calls_outgoing_unanswered }}</td>
                                            <td v-if="q.display_name">{{ sec_to_time(q.avg_holdtime) }}</td>
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
                <div class="tab-pane fade" id="categories" role="tabpanel" aria-labelledby="categories-tab">
                    <div class="row mt-2">
                        <div class="col">
                            <div class="card border-top-primary border-primary border-top-3">
                                <!-- Here subject table needed -->
                                <input type="hidden" name="subject_family_input" id="subject_family_input">
                                <input type="hidden" name="subject_sub_family_input" id="subject_sub_family_input">
                                <?php
                                    echo "<table style='width:100%'>";
                                        echo "<tr style='vertical-align:top;'>";
                                            echo "<td>";
                                                echo "<div class='p-2' style='border:1px solid black; width:260px;min-height:300px;border-radius:5px;'>";
                                                    echo "<div style='display:inline-block'>Main Subject</div>";
                                                        echo "<hr>";
                                                        echo "<div id='subject_col_1'>";
                                                            /* Placeholder For Col 1 */
                                                        echo "</div>";
                                                echo "</div>";
                                            echo "</td>";
                                            echo "<td>";
                                                echo "<div class='p-2' style='border:1px solid black; width:260px;min-height:300px;border-radius:5px;'>";
                                                    echo "<div style='display:inline-block' id='col_2_title'></div>";
                                                        echo "<hr>";
                                                        echo "<div id='subject_col_2'>";
                                                            /* Placeholder For Col 2 */
                                                        echo "</div>";
                                                echo "</div>";
                                            echo "</td>";
                                            echo "<td>";
                                                echo "<div class='p-2' style='border:1px solid black; width:260px;min-height:300px;border-radius:5px;'>";
                                                    echo "<div style='display:inline-block' id='col_3_title'></div>";
                                                        echo "<hr>";
                                                        echo "<div id='subject_col_3'>";
                                                            /* Placeholder For Col 3 */
                                                        echo "</div>";
                                                echo "</div>";
                                            echo "</td>";
                                            echo "<td>";
                                                echo "<div class='p-2' style='border:1px solid black; width:260px;min-height:300px;border-radius:5px;'>";
                                                    echo "<div style='display:inline-block' id='col_4_title'></div>";
                                                        echo "<hr>";
                                                        echo "<div id='subject_col_4'>";
                                                            /* Placeholder For Col 4 */
                                                        echo "</div>";
                                                echo "</div>";
                                            echo "</td>";
                                        echo "</tr>";
                                    echo "</table>";
                                ?>
                            <div id="subject_div"><!-- Placeholder For Subjects --></div>
                            </div>
                            <div class="p-2">
                                <button @click="export_category" type="button" class="btn btn-primary">{{ lang['category_export']}} <i class="cil cil-cloud-download"></i></button>

                                <button  id="cat-refresh-button" type="button" class="btn btn-warning">{{ lang['category_refresh'] }}</button>
                            </div>
                            <!--<div class="p-2">
                                <button @click="show_category" type="button" class="btn btn-primary">Show</i></button>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    ul{
        list-style-type:none;

        padding-left:20px;
    }
</style>
<script>

$(document).ready(function() {
//  Display Filtered Subjects Items

    $('#categories-tab, #cat-refresh-button').click(get_subjects);
    $(document).on('click', '.parent_subject', get_child_1);
    $(document).on('click', '.child_1_subject', get_child_2);
    $(document).on('click', '.child_2_subject', get_child_3);

    function get_date_lt_gt(){
        date_gt='';
        date_lt='';

        date_obj = new Date();
        curr_year=date_obj.getFullYear();
        curr_month=date_obj.getMonth()+1;
        curr_day=date_obj.getDate();

        curr_hours=date_obj.getHours();
        curr_minutes=date_obj.getMinutes();
        curr_seconds=date_obj.getSeconds();

        if(curr_minutes<10){
            curr_minutes='0'+curr_minutes;
        }
        if(curr_seconds<10){
            curr_seconds='0'+curr_seconds;
        }

        curr_date=curr_year+'-'+curr_month+'-'+curr_day+' '+curr_hours+':'+curr_minutes+':'+curr_seconds;
        curr_day=curr_year+'-'+curr_month+'-'+curr_day+' 00:00:00';


        if($('#date_gt').val().length>0){
            date_gt=$('#date_gt').val();
        }
        else{
            date_gt=curr_day;
        }
        if($('#date_lt').val().length>0){
            date_lt=$('#date_lt').val();
        }
        else{
            date_lt=curr_date;
        }

        return data_lt_gt_array=[date_gt, date_lt];
    }

    function get_subjects(){
        $('#col_2_title').text('');
        $('#col_3_title').text('');
        $('#col_4_title').text('');

        $('#subject_col_2').text('');
        $('#subject_col_3').text('');
        $('#subject_col_4').text('');

        date_lt_gt_array=get_date_lt_gt();
        $.post('<?php echo base_url() ?>index.php/Call_subjects_statistics/get_parent_subjects',
            {
                date_gt: date_lt_gt_array[0],
                date_lt: date_lt_gt_array[1],
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    var subject_id=dataResult.parent_subjects_id;
                    var subject_title=dataResult.parent_subjects_title;
                    var subject_count=dataResult.parent_subjects_count;
                    var subjects_html='';

                    for(var i=0; i<subject_id.length; i++){
                        subjects_html+="<ul>";
                            subjects_html+="<li id='"+subject_id[i]+"'>";
                                subjects_html+="<button type='button'  class='btn parent_subject' id='"+subject_id[i]+"'>";
                                subjects_html+=subject_title[i]+" ("+subject_count[i]+") "+"</button>";
                            subjects_html+="</li>";
                        subjects_html+="</ul>";
                    }

                    $('#subject_col_1').html(subjects_html);
                }
                else{
                    alert('Request Status Error!');
				}
            }
        );
    }
//Display Child 1 subjects
    function get_child_1(){
        var child_id_1=this.id;

        var title_for_col_2=$(this).text();
        //get_date_lt_gt();
        $('#col_2_title').text('');
        $('#col_3_title').text('');
        $('#col_4_title').text('');

        $('#subject_col_2').text('');
        $('#subject_col_3').text('');
        $('#subject_col_4').text('');

        $('#col_2_title').text(title_for_col_2);


        $('#subject_family_input').val(child_id_1);

        date_lt_gt_array=get_date_lt_gt();
        $.post('<?php echo base_url() ?>index.php/Call_subjects_statistics/get_child_1_subjects',
            {
                parent_id: child_id_1,
                date_gt: date_lt_gt_array[0],
                date_lt: date_lt_gt_array[1],
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    var child_1_id=dataResult.child_1_subject;
                    var child_1_title=dataResult.child_1_subject;
                    var child_1_parent_id=dataResult.child_1_subject;
                    var child_1_count=dataResult.child_1_count;
                    var child_2_count=dataResult.child_2_count;

                    child_1_html="";
                    for(var i=0; i<child_1_title.length; i++){
                        ch_1_id='b_'+child_1_id[i]['id'];
                        ch_1_title=child_1_title[i]['title'];
                        ch_1_parent_id=child_1_parent_id[i]['parent_id'];
                        if(child_1_count[i]>0){
                            child_1_html+="<ul><li id='li_1_"+ch_1_id+"'><button type='button' class='btn child_1_subject' id='"+ch_1_id+"'>";
                            child_1_html+=ch_1_title;
                            child_1_html+=" (" + child_1_count[i] + ")";
                           /* if(child_2_count[i]>0) {
                                child_1_html+=" -> (" + child_2_count[i] + ")";
                            }*/
                            child_1_html+="</button></li></ul>";

                            //open_list(ch_1_parent_id, ch_1_id, ch_1_title, child_2_count[i]);
                        }
                    }
                    $('#subject_col_2').html(child_1_html);
                }
                else{
                    alert('Request Status Error!');
				}
            }
        );
    }
//Display child 2 subjects
    function get_child_2(){
        var child_id_2=this.id;

        $('#col_3_title').text('');
        $('#col_4_title').text('');

        $('#subject_col_3').text('');
        $('#subject_col_4').text('');

        var title_for_col_3=$(this).text();
        $('#col_3_title').text(title_for_col_3);

        main_subject_id= $('#subject_family_input').val();
        child_id_2_for_request=child_id_2.substring(2);
        //alert(child_id_2_for_reques);
        $('#subject_sub_family_input').val(child_id_2_for_request);

        date_lt_gt_array=get_date_lt_gt();

        $.post('<?php echo base_url() ?>index.php/Call_subjects_statistics/get_child_2_subjects',
            {
                main_subject_id: main_subject_id,
                parent_id: child_id_2_for_request,
                date_gt: date_lt_gt_array[0],
                date_lt: date_lt_gt_array[1],
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    var child_2_id=dataResult.child_2_subject;
                    var child_2_title=dataResult.child_2_subject;
                    var child_2_parent_id=dataResult.child_2_subject;
                    var child_2_count=dataResult.child_2_count;
                    var child_3_count=dataResult.child_3_count;

                    child_2_html="";
                    for(var i=0; i<child_2_title.length; i++){
                        ch_2_id='c_'+child_2_id[i]['id'];
                        ch_2_title=child_2_title[i]['title'];
                        ch_2_parent_id='b_'+child_2_parent_id[i]['parent_id'];
                        if(child_2_count[i]>0) {
                            child_2_html += "<ul><li id='li_2_" + ch_2_id + "'><button type='button' class='btn child_2_subject' id='" + ch_2_id + "'>";
                            child_2_html += ch_2_title;
                            child_2_html += " (" + child_2_count[i] + ")";
                            /*if (child_3_count[i] > 0) {
                                child_2_html += " -> (" + child_3_count[i] + ")";
                            }*/
                            child_2_html += "</button></li></ul>";
                        }
                        //if(child_3_count[i]>0){
                            //child_2_html+="<ul><li id='li_2_"+ch_2_id+"'><button type='button' class='btn child_2_subject' id='"+ch_2_id+"' >"+ch_2_title+" ( "+child_3_count[i]+" ) </button></li></ul>";
                            /* child_id_2='li_1_'+child_id_2;
                            open_list('li_1_'+child_id_2, ch_2_id, ch_2_title, child_3_count[i]); */
                        //}
                    }
                    $('#subject_col_3').html(child_2_html);
                }
                else{
                    alert('Request Status Error!');
				}
            }
        );
    }

    //Display child 3 subjects
    function get_child_3(){
        var child_id_3=this.id;

        $('#col_4_title').text('');

        $('#subject_col_4').text('');

        var title_for_col_4=$(this).text();
        $('#col_4_title').text(title_for_col_4);

        main_subject_id= $('#subject_family_input').val();
        main_sub_subject_id= $('#subject_sub_family_input').val();
        child_id_3_for_reques=child_id_3.substring(2);
        //alert(child_id_3);
        date_lt_gt_array=get_date_lt_gt();

        $.post('<?php echo base_url() ?>index.php/Call_subjects_statistics/get_child_3_subjects',
            {
                main_subject_id: main_subject_id,
                main_sub_subject_id: main_sub_subject_id,
                parent_id: child_id_3_for_reques,
                date_gt: date_lt_gt_array[0],
                date_lt: date_lt_gt_array[1],
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    var child_3_id=dataResult.child_3_subject;
                    var child_3_title=dataResult.child_3_subject;
                    //var child_2_parent_id=dataResult.child_2_subject;
                    var child_4_count=dataResult.child_4_count;
                    //console.log(child_4_count);
                    child_3_html="";
                    for(var i=0; i<child_3_title.length; i++){
                        ch_3_id='c_'+child_3_id[i]['id'];
                        ch_3_title=child_3_title[i]['title'];
                        //ch_2_parent_id='b_'+child_2_parent_id[i]['parent_id'];
                        //if(child_4_count[i]>0){
                        if(child_4_count[i]>0) {
                            child_3_html += "<ul><li id='li_3_" + ch_3_id + "'><button type='button' class='btn child_3_subject' id='" + ch_3_id + "' >";
                            child_3_html += ch_3_title;
                            child_3_html += " (" + child_4_count[i] + ")";
                            child_3_html += "</button></li></ul>";
                        }
                            /* child_id_3='li_2_'+child_id_3;
                            open_list(child_id_3, ch_3_id, ch_3_title, child_4_count[i]); */
                        //}
                    }
                    $('#subject_col_4').html(child_3_html);
                }
                else{
                    alert('Request Status Error!');
				}
            }
        );
        return false;

    }

    /* Append Function */
    function open_list(target_li, child_id, child_title, child_count){
       $('#'+target_li).append("<ul><li id='li_2_"+child_id+"'><button type='button' class='btn child_2_subject' id='"+child_id+"' >"+child_title+" ( "+child_count+" ) </button></li></ul>");
    }

});
</script>
