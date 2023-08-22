<div class="container">
<div id="recordings_search">
    <div class="row">
        <div class="col mb-2">
            <div class="card border-primary">
                <div class="card-body">
                    <?php echo form_open(false,array('method' => 'get')); ?>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('src'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('src'); ?></span>
                                        </div>
                                        <input type="text" class="form-control" id="src" name="src" value="<?php echo $this->input->get('src'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('dst'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('dst'); ?></span>
                                        </div>
                                        <input type="text" class="form-control" id="dst" name="dst" value="<?php echo $this->input->get('dst'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($config->app_track_transfers == 'yes') { ?>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('transferred_to'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('transferred_to'); ?></span>
                                        </div>
                                        <input type="text" class="form-control" id="transferred_to" name="transferred_to" value="<?php echo $this->input->get('transferred_to'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-1">
                            <select name="transferred" id="transferred" class="form-control">
                                <option value=""><?php echo lang('---'); ?></option>
                                <?php if ($this->input->get('transferred') == 'yes') { ?>
                                    <option selected value="yes"><?php echo lang('yes'); ?></option>
                                    <option value="no"><?php echo lang('no'); ?></option>
                                <?php } elseif ($this->input->get('transferred') == 'no') { ?>
                                    <option value="yes"><?php echo lang('yes'); ?></option>
                                    <option selected value="no"><?php echo lang('no'); ?></option>
                                <?php } else { ?>
                                    <option value="yes"><?php echo lang('yes'); ?></option>
                                    <option value="no"><?php echo lang('no'); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                        <?php if ($config->app_call_categories == 'yes') { ?>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('call_category'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('call_category'); ?></span>
                                        </div>
                                        <select id="search_category_id" name="search_category_id" class="form-control">
                                            <option value=""></option>
                                            <?php foreach ($call_categories as $ct) { ?>
                                                <?php if ($this->input->get('search_category_id') == $ct->id) { ?>
                                                    <option selected value="<?php echo $ct->id; ?>"><?php echo $ct->name; ?></option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $ct->id; ?>"><?php echo $ct->name; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('queue'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('queue'); ?></span>
                                        </div>
                                        <select name="queue_id" id="queue_id" class="form-control">
                                            <option value=""></option>
                                            <?php
                                            foreach ($select_queues as $id => $display_name) {
                                                if ($this->input->get('queue_id') == $id) {
                                                    echo "<option selected value='".$id."'>".$display_name."</option>";
                                                } else {
                                                    echo "<option value='".$id."'>".$display_name."</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('agent'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('agent'); ?></span>
                                        </div>
                                        <select name="agent_id" id="agent_id" class="form-control">
                                            <option value=""></option>
                                            <?php
                                            foreach ($select_agents as $id => $display_name) {
                                                if ($this->input->get('agent_id') == $id) {
                                                    echo "<option selected value='".$id."'>".$display_name."</option>";
                                                } else {
                                                    echo "<option value='".$id."'>".$display_name."</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('event'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('event'); ?></span>
                                        </div>
                                        <select name="event_type" id="event_type" class="form-control">
                                            <option value=""></option>
                                            <?php if ($this->input->get('event_type') == 'ANSWERED') { ?>
                                                <option selected value="ANSWERED"><?php echo lang('answered'); ?></option>
                                            <?php } else { ?>
                                                <option value="ANSWERED"><?php echo lang('answered'); ?></option>
                                            <?php } ?>
                                            <?php if ($this->input->get('event_type') == 'UNANSWERED') { ?>
                                                <option selected value="UNANSWERED"><?php echo lang('unanswered'); ?></option>
                                            <?php } else { ?>
                                                <option value="UNANSWERED"><?php echo lang('unanswered'); ?></option>
                                            <?php } ?>
                                            <?php if ($this->input->get('event_type') == 'OUTGOING_EXTERNAL') { ?>
                                                <option selected value="OUTGOING_EXTERNAL"><?php echo lang('outgoing')." (".lang('external').")"; ?></option>
                                            <?php } else { ?>
                                                <option value="OUTGOING_EXTERNAL"><?php echo lang('outgoing')." (".lang('external').")"; ?></option>
                                            <?php } ?>
                                            <?php if ($this->input->get('event_type') == 'OUTGOING_INTERNAL') { ?>
                                                <option selected value="OUTGOING_INTERNAL"><?php echo lang('outgoing')." (".lang('internal').")"; ?></option>
                                            <?php } else { ?>
                                                <option value="OUTGOING_INTERNAL"><?php echo lang('outgoing')." (".lang('internal').")"; ?></option>
                                            <?php } ?>
                                            <?php
                                            foreach ($interesting_events as $e) {
                                                if ($this->input->get('event_type') == $e->name) {
                                                    echo "<option selected value='".$e->name."'>".$e->name."</option>";
                                                } else {
                                                    echo "<option value='".$e->name."'>".$e->name."</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php if($track_called_back == 'yes') { ?>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('called_back'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('called_back'); ?></span>
                                        </div>
                                        <select name="called_back" id="called_back" class="form-control">
                                            <option value=""></option>
                                            <?php foreach (array('yes', 'no', 'nop', 'nah') as $cbs) { ?>
                                                <?php if ($this->input->get('called_back') == $cbs) { ?>
                                                    <option selected value="<?php echo $cbs; ?>"><?php echo lang("cb_".$cbs); ?></option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $cbs; ?>"><?php echo lang("cb_".$cbs); ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if ($track_duplicates > 0) { ?>
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><?php echo lang('duplicate'); ?></span>
                                </div>
                                <select name="duplicate" id="duplicate" class="form-control">
                                    <option value=""><?php echo lang('---'); ?></option>
                                    <?php if ($this->input->get('duplicate') == 'yes') { ?>
                                        <option selected value="yes"><?php echo lang('yes'); ?></option>
                                        <option value="no"><?php echo lang('no'); ?></option>
                                    <?php } elseif ($this->input->get('duplicate') == 'no') { ?>
                                        <option value="yes"><?php echo lang('yes'); ?></option>
                                        <option selected value="no"><?php echo lang('no'); ?></option>
                                    <?php } else { ?>
                                        <option value="yes"><?php echo lang('yes'); ?></option>
                                        <option value="no"><?php echo lang('no'); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <label class="control-label sr-only"><?php echo lang('comment'); ?></label>
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?php echo lang('comment'); ?></span>
                                    </div>
                                    <input type="text" class="form-control" id="search_comment" name="search_comment" value="<?php echo $this->input->get('comment'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('start_date'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('start_date'); ?></span>
                                        </div>
                                        <input type="text" class="form-control" id="date_gt" name="date_gt" value="<?php echo $this->input->get('date_gt'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('end_date'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('end_date'); ?></span>
                                        </div>
                                        <input type="text" class="form-control" id="date_lt" name="date_lt" value="<?php echo $this->input->get('date_lt'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="dropdown-menu" aria-labelledby="predefined_periods" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 36px, 0px);">
                                <a @click="change_interval('today')" class="dropdown-item" href="#"><?php echo lang('today'); ?></a>
                                <a @click="change_interval('yday')" class="dropdown-item" href="#"><?php echo lang('yesterday'); ?></a>
                                <a @click="change_interval('tweek')" class="dropdown-item" href="#"><?php echo lang('this_week'); ?></a>
                                <a @click="change_interval('tmonth')" class="dropdown-item" href="#"><?php echo lang('this_month'); ?></a>
                                <a @click="change_interval('l7day')" class="dropdown-item" href="#"><?php echo lang('last_7_days'); ?></a>
                                <a @click="change_interval('l14day')" class="dropdown-item" href="#"><?php echo lang('last_14_days'); ?></a>
                                <a @click="change_interval('l30day')" class="dropdown-item" href="#"><?php echo lang('last_30_days') ;?></a>
                            </div>
                            <span id="select_time_range" class="btn btn-info btn-block dropdown-toggle mb-2 mr-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang('time_range'); ?></span>
                        </div>
                    </div>
                    <!-- <?php if ($config->app_service_module == 'yes') { ?>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?php echo lang('service'); ?></span>
                                    </div>
                                    <select v-model="service_module.service_id" onChange='recordings_search.load_service_products(this.value)' class="form-control" id="service_id" name="service_id">
                                        <option value=""></option>
                                        <?php foreach ($services as $s) { ?>
                                            <option value="<?php echo $s->id ;?>"><?php echo $s->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?php echo lang('product'); ?></span>
                                    </div>
                                    <select v-model="service_module.service_product_id" onChange='recordings_search.load_service_product_types(this.value)' class="form-control" id="service_product_id" name="service_product_id">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?php echo lang('service_type'); ?></span>
                                    </div>
                                    <select v-model="service_module.service_product_type_id" onChange='recordings_search.load_service_product_subtypes(this.value)' class="form-control" id="service_product_type_id" name="service_product_type_id">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?php echo lang('service_subtype'); ?></span>
                                    </div>
                                    <select v-model="service_module.service_product_subtype_id" class="form-control" id="service_product_subtype_id" name="service_product_subtype_id">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?> -->
                    <div class="form-row">
                        <div class="col-3">
                            <label class="sr-only" for="calltime_gt"><?php echo lang('calltime_gt'); ?></label>
                            <input type="text" class="form-control mb-2 mr-2" id="calltime_gt" name="calltime_gt" value="<?php echo $this->input->get('calltime_gt'); ?>" placeholder="<?php echo lang('calltime_gt'); ?>">
                        </div>
                        <div class="col-3">
                            <label class="sr-only" for="calltime_lt"><?php echo lang('calltime_lt'); ?></label>
                            <input type="text" class="form-control mb-2 mr-2" id="calltime_lt" name="calltime_lt" value="<?php echo $this->input->get('calltime_lt'); ?>" placeholder="<?php echo lang('calltime_lt'); ?>">
                        </div>
                        <div class="col d-flex justify-content-end">
                            <div class="button-group">
                                <a class="btn btn-warning" href="<?php echo site_url('recordings?random=true&'.$this->input->server('QUERY_STRING'));?>"><?php echo lang('random'); ?></a>
                                <a class="btn btn-danger" href="<?php echo site_url('recordings');?>"><?php echo lang('reset'); ?></a>
                                <a class="btn btn-success" href="<?php echo site_url('export/recordings?'.$this->input->server('QUERY_STRING'));?>"><?php echo lang('export'); ?></a>
                                <button type="submit" class="btn btn-primary"><?php echo lang('search'); ?></button>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card border-success">
                <div class="card-header"><strong><?php echo lang('found')." ".$num_calls." ".lang('calls'); ?></strong></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id='tbl-calls'>
                            <thead>
                                <tr class="table-primary">
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"><?php echo lang('date'); ?></th>
                                    <th scope="col"><?php echo lang('agent'); ?></th>
                                    <th scope="col"><?php echo lang('queue'); ?></th>
                                    <th scope="col"><?php echo lang('src'); ?></th>
                                    <th scope="col"><?php echo lang('dst'); ?></th>
                                    <?php if ($config->app_contacts == 'yes') { ?>
                                        <th scope="col"><?php echo lang('contact'); ?></th>
                                    <?php } ?>
                                    <?php if ($config->app_track_transfers == 'yes') { ?>
                                    <th scope="col"><?php echo lang('transferred_to'); ?></th>
                                    <?php } ?>
                                    <th scope="col"><?php echo lang('call_time'); ?></th>
                                    <th scope="col"><?php echo lang('hold_time'); ?></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $i = $num_calls - $page; foreach ($calls as $c) { ?>
                                <tr scope="row">
                                <td><?php echo $i; ?></td>
                                <td>
                                <?php
                                if ( in_array($c->event_type, array('OUT_ANSWERED', 'OUT_FAILED', 'OUT_NOANSWER', 'OUT_ANSWERED')) ) {
                                    echo "<i class='fas fa-arrow-right mr-2 primary'></i>";
                                } else {
                                    echo "<i class='fas fa-arrow-left mr-2 primary'></i>";
                                }
                                if ( in_array($c->event_type, array('COMPLETECALLER', 'COMPLETEAGENT', 'OUT_ANSWERED')) ) {
                                    echo "<i class='fas text-success fa-check mr-2'></i>";
                                } else {
                                    echo "<i class='fas text-danger fa-times mr-2'></i>";
                                }
                                if ($c->duplicate == 'yes') {
                                    echo "<i class='far text-warning fa-copy mr-2' data-toggle='tooltip' data-placement='left' title='".lang('duplicate')."'></i>";
                                }
                                ?>
                                </td>
                                <td><?php echo $c->date; ?></td>
                                <td><?php echo (array_key_exists($c->agent_id, $agents)) ? $agents[$c->agent_id] : ""; ?></td>
                                <td><?php echo (array_key_exists($c->queue_id, $queues)) ? $queues[$c->queue_id] : ""; ?></td>
                                <td class="d-flex justify-content-between align-items-center">
                                    <a class="text-primary" href="<?php echo site_url('recordings?src='.$c->src); ?>"><?php echo $c->src; ?></a>
                                    <?php if ($c->answered_elsewhere) { ?>
                                        <span class="text-success"><i class="fas fa-exclamation-circle" data-toggle='tooltip' data-placement='left' title='<?php echo lang('answered_elsewhere'); ?>'></i></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a class="text-primary" href="<?php echo site_url('recordings?dst='.$c->dst); ?>"><?php echo $c->dst; ?></a>
                                </td>
                                <?php if ($config->app_contacts == 'yes') {
                                    if ( in_array($c->event_type, array('OUT_ANSWERED', 'OUT_FAILED', 'OUT_NOANSWER', 'OUT_ANSWERED')) ) { ?>
                                        <td><?php echo array_key_exists($c->dst, $contacts) ? $contacts[$c->dst] : ""; ?></td>
                                    <?php } else { ?>
                                        <td><?php echo array_key_exists($c->src, $contacts) ? $contacts[$c->src] : ""; ?></td>
                                    <?php }
                                } ?>
                                <?php if ($config->app_track_transfers == 'yes') { ?>
                                <td><?php echo $c->transferdst; ?></td>
                                <?php } ?>
                                <td><?php echo sec_to_min($c->calltime); ?></td>
                                <td><?php echo sec_to_min($c->holdtime + $c->waittime); ?></td>
                                <td>
                                    <a @click="load_player(<?php echo $c->id; ?>)" class="text-danger" data-toggle="modal" data-target="#play_recording"><i class="fas fa-play"></i></a>
                                    <a @click="get_events(<?php echo $c->uniqueid; ?>)" data-toggle="modal" data-target="#call_details"><i class='fas fa-info-circle'></i></a>

                                    <a @click="get_data(<?php echo $c->id; ?>)" data-toggle="modal" data-target="#call_manage"><i class='text-primary fas fa-list'></i></a>

                                    <?php if($track_called_back == 'yes') { if (in_array($c->event_type, array('ABANDON', 'EXITEMPTY', 'EXITWITHKEY', 'EXITWITHTIMEOUT'))) {?>
                                        <div class="dropdown-menu" aria-labelledby="predefined_periods" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 36px, 0px);">
                                            <a @click="toggle_called_back(<?php echo $c->id; ?>, 'yes')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('yes'); ?></a>
                                            <a @click="toggle_called_back(<?php echo $c->id; ?>, 'no')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('no'); ?></a>
                                            <a @click="toggle_called_back(<?php echo $c->id; ?>, 'nop')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('cb_nop'); ?></a>
                                            <a @click="toggle_called_back(<?php echo $c->id; ?>, 'nah')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('cb_nah'); ?></a>
                                        </div>
                                        <a id="called_back_<?php echo $c->id; ?>" class="<?php echo $called_back_styles[$c->called_back]; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-retweet"></i></a>
                                    <?php } } ?>
                                </td>
                                </tr>
                            <?php $i--; } ?>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center"><?php echo $pagination_links; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="call_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('call_details'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <small>
                        <table class="table table-hover table-bordered">
                            <th><?php echo lang('date'); ?></th>
                            <th><?php echo lang('event'); ?></th>
                            <th><?php echo lang('agent'); ?></th>
                            <th><?php echo lang('call_time'); ?></th>
                            <th><?php echo lang('hold_time'); ?></th>
                            <th><?php echo lang('ring_time'); ?></th>
                            <tr v-for="e in call_events">
                                <td>{{ e.date }}</td>
                                <td>{{ e.event_type }}</td>
                                <td v-if="e.agent_id > 0">{{ agents[e.agent_id].display_name }}</td>
                                <td v-else="e.agent_id > 0"></td>
                                <td>{{ sec_to_time(e.calltime) }}</td>
                                <td v-if="e.event_type == 'ABANDON'">{{ sec_to_time(e.waittime) }}</td>
                                <td v-else>{{ sec_to_time(e.holdtime) }}</td>
                                <td>{{ sec_to_time(e.ringtime) }}</td>
                            </tr>
                        </table>
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" class="close" data-dismiss="modal"><?php echo lang('close'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="play_recording" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('play_recording'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col d-flex justify-content-center">
                            <div id="jquery_jplayer_1" class="jp-jplayer"></div>
                            <div id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
                                <div class="jp-type-single">
                                    <div class="jp-gui jp-interface">
                                        <div class="jp-controls">
                                            <a class="jp-play"><i class="fa fa-play"></i></a>
                                            <a class="jp-pause"><i class="fa fa-pause"></i></a>
                                            <a class="jp-stop"><i class="fa fa-stop"></i></a>
                                        </div>
                                        <div class="jp-progress">
                                            <div class="jp-seek-bar">
                                                <div class="jp-play-bar"></div>
                                            </div>
                                        </div>
                                        <div class="jp-volume-controls">
                                            <a class="jp-mute"><i class="fa fa-volume-off"></i></a>
                                            <a class="jp-volume-max"><i class="fa fa-volume-up"></i></a>
                                            <div class="jp-volume-bar">
                                                <div class="jp-volume-bar-value"></div>
                                            </div>
                                        </div>
                                        <div class="jp-time-holder">
                                            <div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
                                            <div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="jp-no-solution">
                                        <span>Update Required</span>
                                        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" class="close" data-dismiss="modal"><?php echo lang('close'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="call_info" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('call_details'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label for="category_id"><?php echo lang('call_category'); ?></label>
                            <select v-model="call_data.category_id" class="form-control mb-2" id="category_id" name="category_id">
                                <option value=""></option>
                                <?php foreach ($call_categories as $ct) { ?>
                                    <option value="<?php echo $ct->id; ?>"><?php echo $ct->name; ?></option>
                                <?php } ?>
                            </select>
                            <button data-dismiss="modal" @click="add_category()" class="btn btn-block btn-info"><?php echo lang('add_category'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="call_manage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('call_details'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col d-flex justify-content-center">
                            <div id="jquery_jplayer_1" class="jp-jplayer"></div>
                            <div id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
                                <div class="jp-type-single">
                                    <div class="jp-gui jp-interface">
                                        <div class="jp-controls">
                                            <a class="jp-play"><i class="fa fa-play"></i></a>
                                            <a class="jp-pause"><i class="fa fa-pause"></i></a>
                                            <a class="jp-stop"><i class="fa fa-stop"></i></a>
                                        </div>
                                        <div class="jp-progress">
                                            <div class="jp-seek-bar">
                                                <div class="jp-play-bar"></div>
                                            </div>
                                        </div>
                                        <div class="jp-volume-controls">
                                            <a class="jp-mute"><i class="fa fa-volume-off"></i></a>
                                            <a class="jp-volume-max"><i class="fa fa-volume-up"></i></a>
                                            <div class="jp-volume-bar">
                                                <div class="jp-volume-bar-value"></div>
                                            </div>
                                        </div>
                                        <div class="jp-time-holder">
                                            <div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
                                            <div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="jp-no-solution">
                                        <span>Update Required</span>
                                        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <dl>
                                <dt>{{ call_data.src }}</dt>
                                <dd>{{ lang['src'] }}</dd>
                            </dl>
                        </div>
                        <div class="col">
                            <dl>
                                <dt>{{ call_data.dst }}</dt>
                                <dd>{{ lang['dst'] }}</dd>
                            </dl>
                        </div>
                        <div class="col">
                            <dl>
                                <dt>{{ call_data.date }}</dt>
                                <dd>{{ lang['date'] }}</dd>
                            </dl>
                        </div>
                        <div class="col">
                            <dl>
                                <dt>{{ sec_to_time(call_data.calltime) }}</dt>
                                <dd>{{ lang['call_time'] }}</dd>
                            </dl>
                        </div>
                        <div class="col">
                            <button class="btn btn-block btn-outline-info"><i class="fa fa-download mr-2"></i>{{ lang['download'] }}</button>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
<textarea v-model="call_data.comment" id="comment" class="form-control mb-2" placeholder="<?php echo lang('desc_add_comment'); ?>" rows="5">{{ call_data.comment }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <?php if ($config->app_call_statuses == 'yes') { ?>
                            <div class="col">
                                <label for="call_status">{{ lang['status'] }}</label>
                                <select v-model="call_data.status" class="form-control" id='call_status' name="call_status">
                                <?php foreach (qq_get_call_statuses() as $s) { ?>
                                    <option value="<?php echo $s; ?>"><?php echo lang($s); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <?php if ($config->app_call_priorities == 'yes') { ?>
                            <div class="col">
                                <label for="call_priority">{{ lang['priority'] }}</label>
                                <select v-model="call_data.priority" class="form-control" id='call_priority' name="call_priority">
                                <?php foreach (qq_get_call_priorities() as $p) { ?>
                                    <option value="<?php echo $p; ?>"><?php echo lang($p); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <?php if ($config->app_call_curators == 'yes') { ?>
                            <div class="col">
                                <label for="call_curator_id">{{ lang['curator'] }}</label>
                                <select v-model="call_data.curator_id" class="form-control" id='call_curator_id' name="call_curator_id">
                                <?php foreach ($users as $u) { ?>
                                    <option value="<?php echo $u->id; ?>"><?php echo $u->display_name; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <?php if ($config->app_call_categories == 'yes') { ?>
                            <div class="col">
                                <label for="category_id"><?php echo lang('call_category'); ?></label>
                                <select v-model="call_data.category_id" class="form-control mb-2" id="category_id" name="category_id">
                                    <option value=""></option>
                                    <?php foreach ($call_categories as $ct) { ?>
                                        <option value="<?php echo $ct->id; ?>"><?php echo $ct->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <?php if ($config->app_call_tags == 'yes') { ?>
                            <div class="col">
                                <label for="tag_id">{{ lang['call_tag'] }}</label>
                                <select v-model="call_data.tag_id" class="form-control" id='tag_id' name="tag_id">
                                <?php foreach ($call_tags as $ct) { ?>
                                    <option value="<?php echo $ct->id; ?>"><?php echo $ct->name; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="save_call_data()" type="button" class="btn btn-success"><?php echo lang('save'); ?></button>
                    <button type="button" class="btn btn-warning" class="close" data-dismiss="modal"><?php echo lang('cancel'); ?></button>
                    <button type="button" class="btn btn-danger" class="close" data-dismiss="modal"><?php echo lang('close'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="call_comment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('call_details'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-12">
<textarea v-model="call_data.comment" id="comment" class="form-control mb-2" placeholder="<?php echo lang('desc_add_comment'); ?>">{{ call_data.comment }}</textarea>
                        <button data-dismiss="modal" @click="add_comment()" class="btn btn-block btn-info"><?php echo lang('add_comment'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>