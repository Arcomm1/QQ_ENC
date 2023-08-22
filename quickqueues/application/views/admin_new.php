<div class="container-lg mt-3" id="start">
    <div class="row">
        <div class="col">
            <div class="row mb-2">
                <div class="col">
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
                            <button class="nav-link" id="queues-tab" data-coreui-toggle="tab" data-coreui-target="#queues" type="button" role="tab" aria-controls="queues" aria-selected="false">{{ lang['queues'] }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hours-tab" data-coreui-toggle="tab" data-coreui-target="#hours" type="button" role="tab" aria-controls="hours" aria-selected="false">{{ lang['hours'] }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="days-tab" data-coreui-toggle="tab" data-coreui-target="#days" type="button" role="tab" aria-controls="days" aria-selected="false">{{ lang['days'] }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="categories-tab" data-coreui-toggle="tab" data-coreui-target="#categories" type="button" role="tab" aria-controls="categories" aria-selected="false">{{ lang['categories'] }}</button>
                        </li>
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
                                            <i class="cil-bookmark text-info mr-2"></i>
                                            {{ lang['calls_unique'] }}
                                        </span>
                                        <span>{{ total_stats.calls_unique }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-check text-success mr-4"></i>
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?event_type=ANSWERED&date_gt='+date_gt+'&date_lt='+date_lt">{{ lang['calls_answered'] }}</a>
                                        </span>
                                        <span>{{ total_stats.calls_answered }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-delete text-warning mr-2"></i>
                                            <a class="text-decoration-none link-dark link-dark" :href="app_url+'/recordings?event_type=UNANSWERED&date_gt='+date_gt+'&date_lt='+date_lt">{{ lang['calls_unanswered'] }}</a>
                                        </span>
                                        <span>{{ total_stats.calls_unanswered }}</span>
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
                                            <a class="text-decoration-none link-dark" :href="app_url+'/recordings?called_back=yes&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('called_back'); ?></a>
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
                                            {{ lang['calls_outgoing']+' ('+lang['unanswered']+')' }}
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
                                            <i class="cil-av-timer text-success mr-4"></i>
                                            {{ lang['call_time']+' ('+lang['avg']+')' }}
                                        </span>
                                        <span>{{ call_time_avg }}</span>
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
                                            <i class="cil-list-filter text-primary mr-4"></i>
                                            {{ lang['calls_waiting']+' ('+lang['avg']+')' }}
                                        </span>
                                        <span>{{ Math.ceil(total_stats.origposition_avg) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="cil-list-filter text-danger mr-4"></i>
                                            {{ lang['calls_waiting']+' ('+lang['max']+')' }}
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
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ lang['agent'] }}</th>
                                            <th scope="col">{{ lang['calls_answered'] }}</th>
                                            <th scope="col">{{ lang['calls_outgoing'] }}</th>
                                            <th scope="col">{{ lang['calls_missed'] }}</th>
                                            <th scope="col">{{ lang['call_time'] }}</th>
                                            <th scope="col">{{ lang['pause_time'] }}</th>

                                        </tr>
                                    </thead>
                                    <tbody id="overview_agents">
                                        <tr v-for="a in agent_stats">
                                            <td v-if="a.display_name">{{ a.display_name }}</td>
                                            <td v-if="a.display_name">{{ a.calls_answered }}</td>
                                            <td v-if="a.display_name">{{ a.calls_outgoing }}</td>
                                            <td v-if="a.display_name">{{ a.calls_missed }}</td>

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
                                            <th scope="col">{{ lang['calls_outgoing'] }}</th>
                                            <th scope="col">{{ lang['calls_unanswered'] }}</th>
                                            <th scope="col">{{ lang['call_time'] }}</th>
                                            <th scope="col">{{ lang['hold_time'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="overview_queues">
                                        <tr v-for="q in queue_stats">
                                            <td v-if="q.display_name">{{ q.display_name }}</td>
                                            <td v-if="q.display_name">{{ q.calls_answered }}</td>
                                            <td v-if="q.display_name">{{ q.calls_outgoing }}</td>
                                            <td v-if="q.display_name">{{ q.calls_unanswered }}</td>
                                            <td v-if="q.display_name">{{ sec_to_time(q.total_calltime) }}</td>
                                            <td v-if="q.display_name">{{ sec_to_time(q.total_holdtime) }}</td>
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
                                            <th scope="col">{{ lang['calls_outgoing'] }}</th>
                                            <th scope="col">{{ lang['calls_unanswered'] }}</th>
                                            <th scope="col">{{ lang['call_time'] }}</th>
                                            <th scope="col">{{ lang['hold_time'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="overview_hours">
                                        <tr v-for="h in hourly_stats">
                                            <td>{{ h.hour }}</td>
                                            <td>{{ h.calls_answered }}</td>
                                            <td>{{ h.calls_outgoing }}</td>
                                            <td>{{ h.calls_unanswered }}</td>
                                            <td>{{ sec_to_time(h.total_calltime) }}</td>
                                            <td>{{ sec_to_time(h.total_holdtime) }}</td>
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
                                            <th scope="col">{{ lang['calls_outgoing'] }}</th>
                                            <th scope="col">{{ lang['calls_unanswered'] }}</th>
                                            <th scope="col">{{ lang['call_time'] }}</th>
                                            <th scope="col">{{ lang['hold_time'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="overview_days">
                                        <tr v-for="d in daily_stats">
                                            <td>{{ d.day }}</td>
                                            <td>{{ d.calls_answered }}</td>
                                            <td>{{ d.calls_outgoing }}</td>
                                            <td>{{ d.calls_unanswered }}</td>
                                            <td>{{ d.total_calltime }}</td>
                                            <td>{{ d.total_holdtime }}</td>
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
                                <?php
                                    $all_parent_subjects=$this->Call_subjects_model->get_called_subjects();
                                    foreach($all_parent_subjects as $parent_subject){
                                        $subject_id=$parent_subject['id'];
                                        echo "<div class='w100'>";
                                            echo "<a href='#'>";
                                                echo "<div class='btn  mt-1 parent_subject div_inline'id=".$subject_id.">";
                                                    $parent_subject_id=$parent_subject['id'];
                                                    echo $parent_subject_id;
                                                    //echo $parent_subject['title'];
                                                echo "</div>";
                                            echo "</a>";

                                            /* if($parent_subject['visible']=='1'){
                                                echo "<div class='div_inline div_right div_w_30 trash_main_subject visible1' id=".$subject_id.">";
                                                    echo "<a href='#' >Hide</a>";
                                                echo "</div>";
                                            }
                                            elseif($parent_subject['visible']=='0'){
                                                echo "<div class='div_inline div_right div_w_30 trash_main_subject visible0' id=".$subject_id.">";
                                                    echo "<a href='#' style='color:red;'>Show</a>";
                                                echo "</div>";
                                            } */
                                           /*  echo "<div class='div_inline div_right div_w_30 edit_parent_subject' id=".$subject_id."
                                                    data-toggle='modal' data-target='#edit_subjects_modal'>";
                                                echo "<a href='#'>Edit</a>";
                                            echo "</div>"; */
                                        echo "</div>";
                                    }
                                ?>
                                <!-- <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width:40%">{{ lang['call_category'] }}</th>
                                            <th scope="col">{{ lang['amount'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="overview_categories">
                                        <tr v-for="c in category_stats">
                                            <td>{{ c.name }}</td>
                                            <td>{{ c.count }}</td>
                                        </tr>
                                    </tbody>
                                </table> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
