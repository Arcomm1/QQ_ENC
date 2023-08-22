<div class="container">
<div class="row" id="agent_stats">
    <div class="col">
        <div class="card border-info">
            <div class="card-header">
                <div class="row">
                    <div class="col d-flex justify-content-end align-items-center">
                        <div class="form-inline">
                            <div class="form-group">
                                <input v-model="date_gt" type="text" id="date_gt" name="date_gt" class="form-control" placeringer="<?php echo lang('start_date'); ?>">
                                <input v-model="date_lt" type="text" id="date_lt" name="date_lt" class="form-control" placeringer="<?php echo lang('end_date'); ?>">
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <div class="btn-group" role="group">
                                        <button id="predefined_periods" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                        <div class="dropdown-menu" aria-labelledby="predefined_periods" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 36px, 0px);">
                                            <a @click="change_interval('today')" class="dropdown-item" href="#"><?php echo lang('today'); ?></a>
                                            <a @click="change_interval('yday')" class="dropdown-item" href="#"><?php echo lang('yesterday'); ?></a>
                                            <a @click="change_interval('tweek')" class="dropdown-item" href="#"><?php echo lang('this_week'); ?></a>
                                            <a @click="change_interval('tmonth')" class="dropdown-item" href="#"><?php echo lang('this_month'); ?></a>
                                            <a @click="change_interval('l7day')" class="dropdown-item" href="#"><?php echo lang('last_7_days'); ?></a>
                                            <a @click="change_interval('l14day')" class="dropdown-item" href="#"><?php echo lang('last_14_days'); ?></a>
                                            <a @click="change_interval('l30day')" class="dropdown-item" href="#"><?php echo lang('last_30_days') ;?></a>
                                        </div>
                                        <button @click="decrease_interval" type="button" class="btn btn-primary"><i class="fa fa-caret-left"></i></button>
                                        <button @click="increase_interval" type="button" class="btn btn-primary"><i class="fa fa-caret-right"></i></button>
                                        <button @click="refresh" type="button" class="btn btn-primary"><?php echo lang('refresh'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-4">
                        <div class="card border-success">
                            <div class="card-header"><?php echo lang('overview'); ?></div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-toggle-on mr-2"></i>
                                            <?php echo lang('status'); ?>
                                        </span>
                                        <span v-if="!realtime_status_loading">
                                        <span v-bind:class="'badge mr-2 badge-pill badge-'+agent_status_colors(realtime_status.Status)">
                                            &nbsp
                                        </span>
                                        </span>
                                        <span v-else class="badge badge-primary badge-pill">
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-phone mr-2"></i>
                                            <?php echo lang('last_call'); ?>
                                        </span>
                                        <span v-if="!agent_loading">
                                            {{ agent.last_call }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-asterisk mr-2"></i>
                                            <?php echo lang('current_call'); ?>
                                        </span>
                                        <span v-if="current_call[0] && realtime_status.Status == 1">
                                            <span v-if="current_call[0].Application.includes('AppQueue')">
                                                <i class="fas fa-arrow-down mr-1 text-info"></i>
                                            </span>
                                            <span v-else>
                                                <i class="fas fa-arrow-up mr-1 text-success"></i>
                                            </span>
                                            {{ current_call[0].ConnectedLineNum }}<br/>
                                            <small class="text-muted float-right">{{ sec_to_time(current_call[0].Seconds) }}</small>
                                        </span>
                                    </li>

                                    <li data-toggle="collapse" data-target="#answered_details" aria-expanded="false" aria-controls="answered_details" class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="far fa-check-circle mr-2 text-success"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?event_type=ANSWERED&date_gt='+date_gt+'&date_lt='+date_lt+'&agent_id='+agent_id"><?php echo lang('calls_answered'); ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.calls_answered+' ('+answered_percentage+'%)' }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="far fa-check-circle mr-2 text-success"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?event_type=ANSWERED&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_answered_within_10s'); ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.answered_10s+' ('+share_10s+'%)' }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li id="answered_details" class="collapse list-group-item">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?event_type=COMPLETECALLER&date_gt='+date_gt+'&date_lt='+date_lt+'&agent_id='+agent_id"><?php echo lang('COMPLETECALLER'); ?></a>
                                                <span>{{ stats.calls_completecaller }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?event_type=COMPLETEAGENT&date_gt='+date_gt+'&date_lt='+date_lt+'&agent_id='+agent_id"><?php echo lang('COMPLETEAGENT'); ?></a>
                                                <span> {{ stats.calls_completeagent }}  </span>
                                            </li>
                                        </ul>
                                    </li>


                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-minus-circle mr-2 text-danger"></i>
                                            <a class="text-primary" href="#" @click="load_missed_calls()"><?php echo lang('calls_missed'); ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.calls_missed+' ('+missed_percentage+'%)' }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>

                                    <?php if ($track_outgoing == 'yes') { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-chevron-up mr-2 text-info"></i>
                                            <a class="text-primary" href="<?php echo site_url('recordings/index?event_type=OUTGOING&agent_id='.$agent_id); ?>"><?php echo lang('calls_outgoing'); ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.calls_outgoing }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <?php } ?>

                                    <?php if ($track_pauses == 'yes') { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-pause mr-2 text-warning"></i>
                                            <?php echo lang('pause_time'); ?>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ sec_to_time(stats.pause_time) }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <?php } ?>

                                    <li data-toggle="collapse" data-target="#call_time_details" aria-expanded="false" aria-controls="call_time_details" class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="far fa-clock mr-2 text-success"></i>
                                            <?php echo lang('call_time'); ?>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ call_time_avg }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li id="call_time_details" class="collapse list-group-item">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><?php echo lang('total'); ?></span>
                                                <span>{{ sec_to_time(stats.total_calltime) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><?php echo lang('avg'); ?></span>
                                                <span> {{ call_time_avg }}  </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><?php echo lang('max'); ?></span>
                                                <span> {{ sec_to_time(stats.max_calltime) }}</span>
                                            </li>
                                        </ul>
                                    </li>
                                    <li data-toggle="collapse" data-target="#ring_time_details" aria-expanded="false" aria-controls="ring_time_details" class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-user-clock mr-2 text-warning"></i>
                                            <?php echo lang('ring_time'); ?>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ ring_time_avg }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li id="ring_time_details" class="collapse list-group-item">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><?php echo lang('total'); ?></span>
                                                <span>{{ sec_to_time(stats.total_ringtime) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><?php echo lang('avg'); ?></span>
                                                <span> {{ ring_time_avg }}  </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><?php echo lang('max'); ?></span>
                                                <span> {{ sec_to_time(stats.max_ringtime) }}</span>
                                            </li>
                                        </ul>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">

                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="card border-info">
                            <div class="card-header">
                                <center><?php echo lang('call_distrib_by_hour'); ?></center>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col">
                                        <canvas id="ctx_cause_distrib_by_hour"></canvas>
                                    </div>
                                    <div class="col">
                                        <canvas id="ctx_time_distrib_by_hour"></canvas>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr class="table-primary">
                                                    <th><?php echo lang('hour'); ?></th>
                                                    <th><?php echo lang('calls_answered'); ?></th>
                                                    <th><?php echo lang('calls_unanswered'); ?></th>
                                                    <th><?php echo lang('calls_outgoing'); ?></th>
                                                    <th><?php echo lang('call_time'); ?></th>
                                                    <th><?php echo lang('ring_time'); ?></th>
                                                    <th><?php echo lang('pause_time'); ?></th>
                                                </tr>
                                            </thead>
                                            <tr v-for="hour in ordered_hours">
                                                <td>
                                                    <strong>{{ hour+':00' }}</strong>
                                                </td>
                                                <td>
                                                    {{ stats_by_hour[hour].calls_answered }}
                                                </td>
                                                <td>
                                                    {{ stats_by_hour[hour].calls_unanswered }}
                                                </td>
                                                <td>
                                                    {{ stats_by_hour[hour].calls_outgoing }}
                                                </td>
                                                <td>
                                                    {{ sec_to_time(stats_by_hour[hour].call_time) }}
                                                </td>
                                                <td>
                                                    {{ sec_to_time(stats_by_hour[hour].ring_time) }}
                                                </td>
                                                <td>
                                                    {{ sec_to_time(stats_by_hour[hour].pause_time) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($config->app_track_agent_pause_time == 'yes') { ?>
                <div class="row mb-2">
                    <div class="col">
                        <div class="card border-primary">
                            <div class="card-header">
                                <center><?php echo lang('agent_pauses'); ?></center>
                            </div>
                            <div class="card-body">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr class="table-primary">
                                            <th><?php echo lang('date'); ?></th>
                                            <th><?php echo lang('activity'); ?></th>
                                            <th><?php echo lang('pausetime'); ?></th>
                                        </tr>
                                    </thead>
                                    <tr v-for="pause_event in pause_events">
                                        <td>
                                            <strong>{{ pause_event.date }}</strong>
                                        </td>
                                        <td>
                                            {{ pause_event.event_type }}
                                        </td>
                                        <td v-if="pause_event.pausetime > 0">
                                            {{ sec_to_time(pause_event.pausetime) }}
                                        </td>
                                        <td v-else></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="row mb-2">
                    <div class="col">
                        <div class="card border-primary">
                            <div class="card-header">
                                <center><?php echo lang('call_distrib_by_day'); ?></center>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col">
                                        <canvas id="ctx_cause_distrib_by_day"></canvas>
                                    </div>
                                    <div class="col">
                                        <canvas id="ctx_time_distrib_by_day"></canvas>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr class="table-primary">
                                                    <th><?php echo lang('day'); ?></th>
                                                    <th><?php echo lang('calls_answered'); ?></th>
                                                    <th><?php echo lang('calls_unanswered'); ?></th>
                                                    <th><?php echo lang('calls_outgoing'); ?></th>
                                                    <th><?php echo lang('call_time'); ?></th>
                                                    <th><?php echo lang('ring_time'); ?></th>
                                                    <th><?php echo lang('pause_time'); ?></th>
                                                </tr>
                                            </thead>
                                            <tr v-for="day in Object.keys(stats_by_day)">
                                                <td>
                                                    <strong>{{ day }}</strong>
                                                </td>
                                                <td>
                                                    {{ stats_by_day[day].calls_answered }}
                                                </td>
                                                <td>
                                                    {{ stats_by_day[day].calls_unanswered }}
                                                </td>
                                                <td>
                                                    {{ stats_by_day[day].calls_outgoing }}
                                                </td>
                                                <td>
                                                    {{ sec_to_time(stats_by_day[day].call_time) }}
                                                </td>
                                                <td>
                                                    {{ sec_to_time(stats_by_day[day].ring_time) }}
                                                </td>
                                                <td>
                                                    {{ sec_to_time(stats_by_day[day].pause_time) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="card border-primary">
                            <div class="card-header">
                                <center><?php echo lang('call_distrib_by_weekday'); ?></center>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col">
                                        <canvas id="ctx_cause_distrib_by_weekday"></canvas>
                                    </div>
                                    <div class="col">
                                        <canvas id="ctx_time_distrib_by_weekday"></canvas>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <table class="table table-hover table-sm">
                                            <tr class="table-primary">
                                                <th><?php echo lang('day'); ?></th>
                                                <th><?php echo lang('calls_answered'); ?></th>
                                                <th><?php echo lang('calls_unanswered'); ?></th>
                                                <th><?php echo lang('calls_outgoing'); ?></th>
                                                <th><?php echo lang('call_time'); ?></th>
                                                <th><?php echo lang('ring_time'); ?></th>
                                                <th><?php echo lang('pause_time'); ?></th>
                                            </tr>
                                            <tr v-for="day in Object.keys(stats_by_weekday)">
                                                <td>
                                                    <strong>{{ lang[day] }}</strong>
                                                </td>
                                                <td>
                                                    {{ stats_by_weekday[day].calls_answered }}
                                                </td>
                                                <td>
                                                    {{ stats_by_weekday[day].calls_unanswered }}
                                                </td>
                                                <td>
                                                    {{ stats_by_weekday[day].calls_outgoing }}
                                                </td>
                                                <td>
                                                    {{ sec_to_time(stats_by_weekday[day].call_time) }}
                                                </td>
                                                <td>
                                                    {{ sec_to_time(stats_by_weekday[day].ring_time) }}
                                                </td>
                                                <td>
                                                    {{ sec_to_time(stats_by_weekday[day].pause_time) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
