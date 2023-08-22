<div class="row">
    <div class="col" id="system_overview" data-step="2" data-intro="<?php echo lang('intro_start'); ?>"  data-position='left'>
        <div class="card border-primary">
            <div class="card-header">
                <div class="row">
                    <div class="col d-flex justify-content-between align-items-center" data-step="3" data-intro="<?php echo lang('intro_datepicker'); ?>"  data-position='left'>
                        <?php echo lang('system_overview'); ?>
                        <div class="form-inline">
                            <div class="form-group">
                                <input v-model="date_gt" autocomplete="off" type="text" id="date_gt" name="date_gt" class="form-control mb-2 mr-2" placeholder="<?php echo lang('start_date'); ?>">
                                <input v-model="date_lt" autocomplete="off" type="text" id="date_lt" name="date_lt" class="form-control mb-2 mr-2" placeholder="<?php echo lang('end_date'); ?>">
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
                                        <button @click="export_stats_new" type="button" class="btn btn-success"><i class="fas fa-file-export"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-4 col-xs-12">
                        <div class="card border-success mb-2">
                            <div class="card-body">
                                <ul class="list-group list-group-flush mb-2">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-list-ol mr-2 text-primary"></i>
                                            <a class="text-primary" href="<?php echo site_url('queues'); ?>"><?php echo lang('queues'); ?></a>
                                        </span>
                                        <span>
                                            <?php echo count($user_queues); ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-user mr-2 text-primary"></i>
                                            <a class="text-primary" href="<?php echo site_url('agents'); ?>"><?php echo lang('agents'); ?></a>
                                        </span>
                                        <span>
                                            <?php echo count($user_agents); ?>
                                        </span>
                                    </li>
                                    <li id="unique_details" class="collapse list-group-item">
                                        <ul class="list-group list-group-flush">
                                            <li v-for="n, d in stats.calls_unique_per_did" class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>{{ d }}</span>
                                                <span>{{ n }}</span>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-phone-volume mr-2 text-info"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_total'); ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.calls_total }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li data-toggle="collapse" data-target="#answered_details" aria-expanded="false" aria-controls="answered_details" class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="far fa-check-circle mr-2 text-success"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?event_type=ANSWERED&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_answered'); ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.calls_answered+' ('+answered_share+')' }}
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
                                                <a class="text-info" :href="app_url+'/recordings?event_type=COMPLETECALLER&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('COMPLETECALLER'); ?></a>
                                                <span>{{ stats.calls_completecaller }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?event_type=COMPLETEAGENT&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('COMPLETEAGENT'); ?></a>
                                                <span> {{ stats.calls_completeagent }}  </span>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="far fa-check-circle mr-2 text-success"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?event_type=ANSWERED&holdtime_lt=10&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_answered_within_10s')." (<".$this->data->config->queue_sla_hold_time." ".lang('seconds').")"; ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.answered_within_sla+' ('+within_10s_share+')' }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <?php if ($config->app_mark_answered_elsewhere > 0) { ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="far fa-check-circle mr-2 text-warning"></i>
                                                <a class="text-primary" :href="app_url+'/recordings?answered_elsewhere=yes&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('answered_elsewhere'); ?></a>
                                            </span>
                                            <span v-if="!stats_loading">
                                                {{ stats.answered_elsewhere+' ('+answered_elsewhere_share+')' }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-light spinner-border-sm" role="status">
                                                    <span class="sr-only"><?php echo lang('loading'); ?></span>
                                                </div>
                                            </span>
                                        </li>
                                    <?php } ?>
                                    <li data-toggle="collapse" data-target="#unanswered_details" aria-expanded="false" aria-controls="unanswered_details" class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-minus-circle mr-2 text-danger"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?event_type=UNANSWERED&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_unanswered'); ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.calls_unanswered+' ('+unanswered_share+')' }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>

                                    <?php if ($config->app_display_survey_scores == 'yes') { ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-star mr-2 text-info"></i>
                                                <a class="text-primary" :href="app_url+'/recordings?event_type=IVRABANDON&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('survey_result'); ?></a>
                                            </span>
                                            <span v-if="!stats_loading">
                                                {{ stats.avg_survey_score }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-light spinner-border-sm" role="status">
                                                    <span class="sr-only"><?php echo lang('loading'); ?></span>
                                                </div>
                                            </span>
                                        </li>
                                    <?php } ?>


                                    <?php if ($config->app_track_ivrabandon == 'yes') { ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-bed mr-2 text-warning"></i>
                                                <a class="text-primary" :href="app_url+'/recordings?event_type=IVRABANDON&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('IVRABANDON'); ?></a>
                                            </span>
                                            <span v-if="!stats_loading">
                                                {{ stats.calls_ivrabandon }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-light spinner-border-sm" role="status">
                                                    <span class="sr-only"><?php echo lang('loading'); ?></span>
                                                </div>
                                            </span>
                                        </li>
                                    <?php } ?>


                                    <li id="unanswered_details" class="collapse list-group-item">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?event_type=ABANDON&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('ABANDON'); ?></a>
                                                <span>{{ stats.calls_abandon }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?event_type=EXITEMPTY&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('EXITEMPTY'); ?></a>
                                                <span> {{ stats.calls_exitempty }}  </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?event_type=EXITWITHTIMEOUT&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('EXITWITHTIMEOUT'); ?></a>
                                                <span> {{ stats.calls_exitwithtimeout }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?event_type=EXITWITHKEY&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('EXITWITHKEY'); ?></a>
                                                <span> {{ stats.calls_exitwithkey }}</span>
                                            </li>
                                        </ul>
                                    </li>
                                    <?php if ($config->app_ignore_abandon > 0) { ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-minus-circle mr-2 text-warning"></i>
                                                <a class="text-primary" :href="app_url+'/recordings?event_type=UNANSWERED&waittime_lt=5&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_unanswered').' (<'.$config->app_ignore_abandon.' '.lang('seconds').')'; ?></a>
                                            </span>
                                            <span v-if="!stats_loading">
                                                {{ stats.calls_unanswered_ignored+' ('+ignore_abandon_share+')' }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-light spinner-border-sm" role="status">
                                                    <span class="sr-only"><?php echo lang('loading'); ?></span>
                                                </div>
                                            </span>
                                        </li>
                                    <?php } ?>
                                    <?php if ($config->app_mark_answered_elsewhere > 0) { ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-exclamation-triangle mr-2 text-danger"></i>
                                                <a class="text-primary" :href="app_url+'/recordings?event_type=UNANSWERED&calls_without_service=yes&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_without_service'); ?></a>
                                            </span>
                                            <span v-if="!stats_loading">
                                                {{ stats.calls_without_service+' ('+calls_without_service_share+')' }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-light spinner-border-sm" role="status">
                                                    <span class="sr-only"><?php echo lang('loading'); ?></span>
                                                </div>
                                            </span>
                                        </li>
                                    <?php } ?>
                                    <?php if ($config->app_track_incomingoffwork == 'yes') { ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-bed mr-2 text-warning"></i>
                                                <a class="text-primary" :href="app_url+'/recordings?event_type=INCOMINGOFFWORK&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_offwork'); ?></a>
                                            </span>
                                            <span v-if="!stats_loading">
                                                {{ stats.calls_offwork }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-light spinner-border-sm" role="status">
                                                    <span class="sr-only"><?php echo lang('loading'); ?></span>
                                                </div>
                                            </span>
                                        </li>
                                    <?php } ?>
                                    <?php if ($config->app_track_transfers == 'yes') { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-arrows-alt-h mr-2 text-primary"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?transferred=yes&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_transferred'); ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.calls_transferred }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <?php } ?>
                                    <?php if ($config->app_track_called_back_calls == 'yes') { ?>
                                    <li data-toggle="collapse" data-target="#called_back_details" aria-expanded="false" aria-controls="called_back_details" class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-redo mr-2 text-success"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?called_back=yes&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('called_back'); ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.called_back }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li id="called_back_details" class="collapse list-group-item">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?called_back=yes&event_type=ABANDON&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('ABANDON'); ?></a>
                                                <span> {{ stats.called_back_abandon+'/'+stats.called_back_abandon_nop+'/'+stats.called_back_abandon_nah }}  </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?called_back=yes&event_type=EXITEMPTY&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('EXITEMPTY'); ?></a>
                                                <span> {{ stats.called_back_exitempty+'/'+stats.called_back_exitempty_nop+'/'+stats.called_back_exitempty_nah }}  </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?called_back=yes&event_type=EXITWITHTIMEOUT&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('EXITWITHTIMEOUT'); ?></a>
                                                <span> {{ stats.called_back_exitwithtimeout+'/'+stats.called_back_exitwithtimeout_nop+'/'+stats.called_back_exitwithtimeout_nah }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a class="text-info" :href="app_url+'/recordings?called_back=yes&event_type=EXITWITHKEY&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('EXITWITHKEY'); ?></a>
                                                <span> {{ stats.called_back_exitwithkey+'/'+stats.called_back_exitwithkey_nop+'/'+stats.called_back_exitwithkey_nah }}</span>
                                            </li>
                                        </ul>
                                    </li>
                                    <?php } ?>
                                    <?php if ($config->app_track_outgoing != 'no') { ?>
                                    <li data-toggle="collapse" data-target="#outgoing_external_details" aria-expanded="false" aria-controls="outgoing_external_details" class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-chevron-up mr-2 text-info"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?event_type=OUTGOING_EXTERNAL&date_gt='+date_gt+'&date_lt='+date_lt">{{ lang['calls_outgoing']+' ('+lang['external']+')' }}</a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.calls_outgoing_external }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li id="outgoing_external_details" class="collapse list-group-item">
                                        <ul class="list-group list-group-flush">
                                            <li v-for="o, q in stats.calls_outgoing_per_queue" class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>{{ q }}</span>
                                                <span>{{ o }}</span>
                                            </li>
                                        </ul>
                                    </li>
                                    <li data-toggle="collapse" data-target="#outgoing_details_internal" aria-expanded="false" aria-controls="outgoing_details_internal" class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-chevron-up mr-2 text-info"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?event_type=OUTGOING_INTERNAL&date_gt='+date_gt+'&date_lt='+date_lt">{{ lang['calls_outgoing']+' ('+lang['internal']+')' }}</a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.calls_outgoing_internal }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li id="outgoing_details" class="collapse list-group-item">
                                        <ul class="list-group list-group-flush">
                                            <li v-for="o, q in stats.calls_outgoing_per_queue" class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>{{ q }}</span>
                                                <span>{{ o }}</span>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <span>
                                                <i class="fas fa-chevron-up mr-2 text-success"></i>
                                                <a class="text-primary" :href="app_url+'/export/unique_callers?date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_outgoing_answered'); ?></a>
                                            </span>
                                        </span>
                                        <span v-if="!stats_loading" >
                                            {{ stats.calls_outgoing_external_answered }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <span>
                                                <i class="fas fa-chevron-up mr-2 text-danger"></i>
                                                <a class="text-primary" :href="app_url+'/export/unique_callers?date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('calls_outgoing_failed'); ?></a>
                                            </span>
                                        </span>
                                        <span v-if="!stats_loading" >
                                            {{ stats.calls_outgoing_external_failed }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <?php } ?>
                                    <?php if ($config->app_track_duplicate_calls > '0') { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="far fa-copy mr-2 text-warning"></i>
                                            <a class="text-primary" :href="app_url+'/recordings?duplicate=yes&date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('duplicate_calls'); ?></a>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.calls_duplicate }}
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
                                                <span>
                                                    <a class="text-primary" :href="app_url+'/recordings?date_gt='+date_gt+'&date_lt='+date_lt+'&calltime='+stats.max_calltime"><?php echo lang('max'); ?></a>
                                                </span>
                                                <span> {{ sec_to_time(stats.max_calltime) }}</span>
                                            </li>
                                        </ul>
                                    </li>
                                    <li data-toggle="collapse" data-target="#hold_time_details" aria-expanded="false" aria-controls="hold_time_details" class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-user-clock mr-2 text-warning"></i>
                                            <?php echo lang('hold_time'); ?>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ hold_time_avg }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li id="hold_time_details" class="collapse list-group-item">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><?php echo lang('total'); ?></span>
                                                <span>{{ sec_to_time(stats.total_holdtime) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><?php echo lang('avg'); ?></span>
                                                <span> {{ hold_time_avg }}  </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <a class="text-primary" :href="app_url+'/recordings?date_gt='+date_gt+'&date_lt='+date_lt+'&holdtime='+stats.max_holdtime"><?php echo lang('max'); ?></a>
                                                </span>
                                                <span> {{ sec_to_time(stats.max_holdtime) }}</span>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <span>
                                                <i class="fas fa-id-badge mr-2 text-primary"></i>
                                                <a class="text-primary" :href="app_url+'/export/unique_callers?date_gt='+date_gt+'&date_lt='+date_lt"><?php echo lang('unique_callers'); ?></a>
                                            </span>
                                        </span>
                                        <span v-if="!stats_loading" >
                                            {{ stats.unique_callers }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <span>
                                                <i class="fas fa-sort-numeric-up mr-2 text-primary"></i>
                                                <?php echo lang('calls_waiting').' ('.lang('avg').')'; ?>
                                            </span>
                                        </span>
                                        <span v-if="!stats_loading" >
                                            {{ stats.origposition }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-sort-numeric-up mr-2 text-info"></i>
                                            <?php echo lang('calls_waiting').' ('.lang('max').')'; ?>
                                        </span>
                                        <span v-if="!stats_loading">
                                            {{ stats.origposition_max }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                </ul>



                            </div>
                        </div>
                        <?php if ($config->app_track_top_agents > 0) { ?>
                        <div class="card border-info">
                            <div class="card-header">
                                <strong>
                                    <?php echo lang('top_agents')." (".lang('last')." ".$config->app_track_top_agents." ".lang('hour').")"; ?>
                                </strong>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <th><?php echo lang('agent'); ?></th>
                                    <th><?php echo lang('calls'); ?></th>
                                    <?php foreach ($top_agents as $calls => $agent) { ?>
                                        <tr>
                                            <td><?php echo $agent->display_name; ?></td>
                                            <td><?php echo $calls; ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="col-md-8 col-xs-12">
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div v-if="stats_loading" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </div>
                                        <canvas id="ctx_cause_distrib"></canvas>
                                    </div>
                                    <div class="col-6">
                                        <div v-if="stats_loading" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </div>
                                        <canvas id="ctx_time_distrib"></canvas>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8">
                                        <div v-if="stats_loading" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </div>
                                        <canvas id="ctx_outgoing_cause_distrib"></canvas>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col mb-2">
                                        <div v-if="stats_by_queue_loading" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </div>
                                        <canvas id="ctx_cause_distrib_by_queue"></canvas>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col">
                                        <div v-if="stats_by_agent_loading" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </div>
                                        <canvas id="ctx_cause_distrib_by_agent"></canvas>
                                    </div>
                                </div>
                                <?php if ($config->app_call_categories == 'yes') { ?>
                                    <hr>
                                    <div class="row">
                                        <div class="col">
                                            <div v-if="stats_by_category_loading" class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only"><?php echo lang('loading'); ?></span>
                                                </div>
                                            </div>
                                            <canvas id="ctx_call_distrib_by_category"></canvas>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($config->app_show_details_in_dashboard == 'yes') { ?>
                    <div class="row">
                        <div class="col">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <table id="queue_details" class="table table-sm table-hover table-striped table-bordered">
                                                <thead>
                                                    <tr class="table-primary">
                                                        <th><?php echo lang('queue'); ?></th>
                                                        <th><?php echo lang('calls'); ?></th>
                                                        <th><?php echo lang('calls_answered'); ?></th>
                                                        <th><?php echo lang('calls_unanswered'); ?></th>
                                                        <th><?php echo lang('calls_outgoing'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <div class="col">
                                            <table id="agent_details" class="table table-sm table-hover table-striped table-bordered">
                                                <thead>
                                                    <tr class="table-primary">
                                                        <th><?php echo lang('agent'); ?></th>
                                                        <th><?php echo lang('calls_answered'); ?></th>
                                                        <th><?php echo lang('calls_unanswered'); ?></th>
                                                        <th><?php echo lang('calls_outgoing'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
