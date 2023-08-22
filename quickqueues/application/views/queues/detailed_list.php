<div class="row" id="queue_overview">
    <div class="col">
        <div v-for="queue in queues" class="row">
            <div class="col mb-2">
                <div class="card border-primary">
                    <div class="card-header">
                        <span class="mr-2">
                            {{ lang['queue']+' - '+queue['data']['display_name'] }}
                        </span>
                        <a :href="app_url+'/queues/realtime/'+queue['data']['id']" class="mr-2"><i class="fas fa-retweet"></i></a>
                        <a :href="app_url+'/queues/stats/'+queue['data']['id']" class="mr-2"><i class="fas fa-chart-bar"></i></a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center table-info">
                                        <?php echo lang('overview'); ?>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-fingerprint mr-2 text-info"></i>
                                            <a class="text-primary"><?php echo lang('calls_unique'); ?></a>
                                        </span>
                                        <span>
                                            {{ queue['stats']['unique'] }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-phone-volume mr-2 text-info"></i>
                                            <a class="text-primary" href="<?php echo site_url('recordings/index'); ?>"><?php echo lang('calls_total'); ?></a>
                                        </span>
                                        <span>
                                            {{ queue['stats']['total'] }}
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
                                            <a class="text-primary" href="<?php echo site_url('recordings/index'); ?>"><?php echo lang('calls_answered'); ?></a>
                                        </span>
                                        <span>
                                            {{ queue['stats']['answered'] }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-minus-circle mr-2 text-danger"></i>
                                            <a class="text-primary" href="<?php echo site_url('recordings/index'); ?>"><?php echo lang('calls_unanswered'); ?></a>
                                        </span>
                                        <span>
                                            {{ queue['stats']['unanswered'] }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-chevron-up mr-2 text-primary"></i>
                                            <a class="text-primary" href="<?php echo site_url('recordings/index'); ?>"><?php echo lang('calls_outgoing'); ?></a>
                                        </span>
                                        <span>
                                            {{ queue['stats']['outgoing'] }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <?php if ($track_transfers == 'yes') { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-arrows-alt-h mr-2 text-primary"></i>
                                            <a class="text-primary" href="<?php echo site_url('recordings/index'); ?>"><?php echo lang('calls_transferred'); ?></a>
                                        </span>
                                        <span>
                                            {{ queue['stats']['transfers'] }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <?php } ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-clock mr-2 text-success"></i>
                                            <a class="text-primary"><?php echo lang('call_time'); ?></a>
                                        </span>
                                        <span>
                                            {{ sec_to_time(queue['stats']['total_calltime']) }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-user-clock mr-2 text-warning"></i>
                                            <a class="text-primary"><?php echo lang('call_time'); ?></a>
                                        </span>
                                        <span>
                                            {{ sec_to_time(queue['stats']['total_holdtime']) }}
                                        </span>
                                        <span v-else>
                                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                                <span class="sr-only"><?php echo lang('loading'); ?></span>
                                            </div>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <table class="table">
                                    <tr class="table-info">
                                        <td colspan="4"><?php echo lang('agents'); ?></td>
                                    </tr>
                                    <tr v-for="agent in queue['agent_status']">
                                        <td>
                                            {{ agent[1]['extension']+' - '+agent[1]['display_name'] }}
                                        </td>
                                        <td>
                                            <span v-bind:class="'badge badge-pill badge-'+agent_status_colors(agent[0]['Status'])">
                                                &nbsp
                                            </span>
                                        </td>
                                        <td>{{ agent[1]['last_call'] }}</td>
                                        <td>
                                            <span v-if="agent[0]['Status'] == 1">
                                                {{ queue['agent_calls'][agent[0]['Exten']][0]['ConnectedLineNum']+' ('+sec_to_time(queue['agent_calls'][agent[0]['Exten']][0].Seconds)+')' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col">
                                <table v-if="Object.keys(queue['realtime']['callers']).length == 0" class="table">
                                    <tr class="table-info">
                                        <td><?php echo lang('agents'); ?></td>
                                    </tr>
                                    <tr><td><?php echo lang('no_callers'); ?></td></tr>
                                </table>
                                <table v-else class="table">
                                    <tr class="table-info">
                                        <td colspan="3"><?php echo lang('agents'); ?></td>
                                    </tr>
                                    <tr v-for="caller in queue['realtime']['callers']">
                                        <td>{{caller['Position']}}</td>
                                        <td>{{caller['CallerIDNum']}}</td>
                                        <td>{{sec_to_time(caller['Wait'])}}</td>
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
