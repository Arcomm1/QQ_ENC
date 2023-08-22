<div class="row">
    <div class="col-md-12" id="queue_settings">

        <div class="card border-danger">
            <div class="card-header">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="queue_nav" aria-expanded="false">{{ lang['queue']+' '+queue.display_name+' ('+queue.name+'): '+lang['preferences'] }}<span class="caret"></span></a>
                <div class="dropdown-menu" aria-labelledby="download">
                    <a class="dropdown-item" href="<?php echo site_url('queues/realtime/'.$queue_id); ?>"><?php echo lang('realtime'); ?></a>
                    <a class="dropdown-item" href="<?php echo site_url('queues/stats/'.$queue_id); ?>"><?php echo lang('stats'); ?></a>
                    <a class="dropdown-item" href="<?php echo site_url('recordings?queue_id='.$queue_id); ?>"><?php echo lang('recordings'); ?></a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <dl>
                            <dt class="col-6"><?php echo lang('queue_name'); ?></dt>
                            <dd class="col-6"><?php echo lang('desc_queue_display_name') ;?></dd>
                            <div class="input-group col-12">
                                <input v-model="queue.display_name" type="text" id="display_name" name="display_name" class="form-control">
                                <button @click="set_item('display_name')" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </div>
                        </dl>
                        <dl>
                            <dt class="col-6"><?php echo lang('sla_calltime'); ?></dt>
                            <dd class="col-6"><?php echo lang('desc_sla_calltime') ;?></dd>
                            <div class="input-group col-12">
                                <input v-model="queue_config.queue_sla_call_time['value']" type="text" id="queue_sla_call_time" name="queue_sla_call_time" class="form-control">
                                <button @click="set_item('queue_sla_call_time')" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </div>
                        </dl>
                        <dl>
                            <dt class="col-6"><?php echo lang('sla_holdtime'); ?></dt>
                            <dd class="col-6"><?php echo lang('desc_sla_holdtime') ;?></dd>
                            <div class="input-group col-12">
                                <input v-model="queue_config.queue_sla_hold_time['value']" type="text" id="queue_sla_hold_time" name="queue_sla_hold_time" class="form-control">
                                <button @click="set_item('queue_sla_hold_time')" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </div>
                        </dl>
                        <dl>
                            <dt class="col-6"><?php echo lang('sla_overflow'); ?></dt>
                            <dd class="col-6"><?php echo lang('desc_sla_overflow') ;?></dd>
                            <div class="input-group col-12">
                                <input v-model="queue_config.queue_sla_overflow['value']" type="text" id="queue_sla_overflow" name="queue_sla_overflow" class="form-control">
                                <button @click="set_item('queue_sla_overflow')" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
