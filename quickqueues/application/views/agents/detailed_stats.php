<div id="agent_stats">
    <div class="row mb-2">
        <div class="col">
            <div class="card border-info">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex justify-content-between align-items-center">
                            <?php echo lang('total_stats'); ?>
                            <div class="form-inline">
                                <div class="form-group">
                                    <span v-if="total_stats_loading" class="mr-3">
                                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                                            <span class="sr-only"><?php echo lang('loading'); ?></span>
                                        </div>
                                    </span>
                                    <input type="text" id="date_gt" name="date_gt" class="form-control mb-2 mr-2" placeholder="<?php echo lang('start_date'); ?>">
                                    <input type="text" id="date_lt" name="date_lt" class="form-control mb-2 mr-2" placeholder="<?php echo lang('end_date'); ?>">
                                    <div class="btn-group mb-2" role="group" aria-label="Button group with nested dropdown">
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
                                            <button @click="export_stats" type="button" class="btn btn-primary"><i class="fas fa-file-export"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <table class="table table-striped table-sm table-hover">
                                <thead>
                                    <tr class="table-primary">
                                        <th>{{ lang['agent'] }}</th>
                                        <th>{{ lang['calls_answered'] }}</th>
                                        <?php if ($config->app_track_ringnoanswer != 'no') { ?>
                                        <th>{{ lang['calls_missed'] }}</th>
                                        <?php } ?>
                                        <?php if ($config->app_track_outgoing != 'no') { ?>
                                        <th>{{ lang['calls_outgoing']+' ('+lang['internal']+')' }}</th>
                                        <th>{{ lang['calls_outgoing']+' ('+lang['external']+')' }}</th>
                                        <?php } ?>
                                        <th>{{ lang['call_time'] }}</th>
                                        <th>{{ lang['ring_time'] }}</th>
                                        <?php if ($config->app_track_agent_pause_time == 'yes') { ?>
                                        <th>{{ lang['pause_time'] }}</th>
                                        <?php } ?>
                                        <th>{{ lang['work_start'] }}</th>
                                        <th>{{ lang['work_end'] }}</th>
                                        <th>{{ lang['work_days'] }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="a in total_stats">
                                        <td>{{ a.data.display_name }}</td>
                                        <td>{{ a.calls_answered }}</td>
                                        <?php if ($config->app_track_ringnoanswer != 'no') { ?>
                                        <td>{{ a.calls_missed }}</td>
                                        <?php } ?>
                                        <?php if ($config->app_track_ringnoanswer != 'no') { ?>
                                        <td>{{ a.calls_outgoing_internal }}</td>
                                        <td>{{ a.calls_outgoing_external }}</td>
                                        <?php } ?>
                                        <td>{{ sec_to_time(a.call_time) }}</td>
                                        <td>{{ sec_to_time(a.ring_time) }}</td>
                                        <?php if ($config->app_track_agent_pause_time == 'yes') { ?>
                                        <td>{{ sec_to_time(a.pause_time) }}</td>
                                        <?php } ?>
                                        <td v-if="a.work_start">{{ a.work_start.date }}</td><td v-else></td>
                                        <td v-if="a.work_end">{{ a.work_end.date }}</td><td v-else></td>
                                        <td>{{ a.days_with_calls }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col">
                            <canvas id="ctx_call_distrib"></canvas>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <canvas id="ctx_time_distrib"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <div class="card border-info">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <?php echo lang('avg_stats'); ?>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <table class="table table-striped table-sm table-hover">
                                <thead>
                                    <tr class="table-primary">
                                        <th>{{ lang['agent'] }}</th>
                                        <th>{{ lang['calls_answered'] }}</th>
                                        <?php if ($config->app_track_ringnoanswer != 'no') { ?>
                                        <th>{{ lang['calls_missed'] }}</th>
                                        <?php } ?>
                                        <?php if ($config->app_track_outgoing != 'no') { ?>
                                        <th>{{ lang['calls_outgoing']+' ('+lang['internal']+')' }}</th>
                                        <th>{{ lang['calls_outgoing']+' ('+lang['external']+')' }}</th>
                                        <?php } ?>
                                        <th>{{ lang['call_time'] }}</th>
                                        <th>{{ lang['ring_time'] }}</th>
                                        <?php if ($config->app_track_agent_pause_time == 'yes') { ?>
                                        <th>{{ lang['pause_time'] }}</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="a in total_stats">
                                        <td>{{ a.data.display_name }}</td>
                                        <td>{{ ~~Math.floor(a.calls_answered / a.days_with_calls) }}</td>
                                        <?php if ($config->app_track_ringnoanswer != 'no') { ?>
                                        <td>{{ ~~Math.floor(a.calls_missed/a.days_with_calls) }}</td>
                                        <?php } ?>
                                        <?php if ($config->app_track_ringnoanswer != 'no') { ?>
                                        <td>{{ ~~Math.floor(a.calls_outgoing_internal/a.days_with_calls) }}</td>
                                        <td>{{ ~~Math.floor(a.calls_outgoing_external/a.days_with_calls) }}</td>
                                        <?php } ?>
                                        <td>{{ sec_to_time(Math.floor(a.call_time/a.days_with_calls)) }}</td>
                                        <td>{{ sec_to_time(Math.floor(a.ring_time/a.days_with_calls)) }}</td>
                                        <?php if ($config->app_track_agent_pause_time == 'yes') { ?>
                                        <td>{{ sec_to_time(Math.floor(a.pause_time/a.days_with_calls)) }}</td>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col">
                            <canvas id="ctx_call_distrib_avg"></canvas>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <canvas id="ctx_time_distrib_avg"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


</div>
