<div class="row">
    <div class="col" id="agent_config">

        <div class="card border-danger">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="agent_nav" aria-expanded="false">{{ lang['agent']+' '+agent.display_name+': '+lang['preferences'] }}<span class="caret"></span></a>
                        <div class="dropdown-menu" aria-labelledby="download">
                            <a class="dropdown-item" href="<?php echo site_url('agents/stats/'.$agent_id); ?>"><?php echo lang('stats'); ?></a>
                            <a class="dropdown-item" href="<?php echo site_url('recordings?agent_id='.$agent_id); ?>"><?php echo lang('recordings'); ?></a>
                        </div>
                    </div>
                    <?php if (!$has_user) { ?>
                        <div class="col">
                            <a class="btn btn-outline-primary float-right" href="<?php echo site_url('users/create?from_agent='.$agent_id);?>"><?php echo lang('create_user'); ?></a>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col">
                        <dl>
                            <dt><?php echo lang('display_name'); ?></dt>
                            <dd><?php echo lang('desc_agent_display_name') ;?></dd>
                            <div class="input-group">
                                <input v-model="agent.display_name" type="text" id="display_name" name="display_name" class="form-control">
                                <button @click="set_item('display_name')" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </div>
                        </dl>
                    </div>
                </div>
                <div v-if="track_sessions == 'yes'" class="row mb-2">
                    <div class="col">
                        <dl>
                            <dt><?php echo lang('session_start_time'); ?></dt>
                            <dd><?php echo lang('desc_session_start_time') ;?></dd>
                            <div class="input-group">
                                <input v-model="agent_settings.agent_work_start_time['value']" type="text" id="agent_work_start_time" name="agent_work_start_time" class="form-control">
                                <button @click="set_item('agent_work_start_time')" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </div>
                        </dl>
                    </div>
                </div>
                <div v-if="track_sessions == 'yes'" class="row mb-2">
                    <div class="col">
                        <dl>
                            <dt><?php echo lang('session_end_time'); ?></dt>
                            <dd><?php echo lang('desc_session_end_time') ;?></dd>
                            <div class="input-group">
                                <input v-model="agent_settings.agent_work_end_time['value']" type="text" id="agent_work_end_time" name="agent_work_end_time" class="form-control">
                                <button @click="set_item('agent_work_end_time')" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </div>
                        </dl>
                    </div>
                </div>
                <div v-if="track_pauses == 'yes'" class="row mb-2">
                    <div class="col">
                        <dl>
                            <dt><?php echo lang('max_pause_time'); ?></dt>
                            <dd><?php echo lang('desc_max_pause_time') ;?></dd>
                            <div class="input-group">
                                <input v-model="agent_settings.agent_max_pause_time['value']" type="text" id="agent_max_pause_time" name="agent_work_end_time" class="form-control">
                                <button @click="set_item('agent_max_pause_time')" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </div>
                        </dl>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <dl>
                            <dt><?php echo lang('agent_call_restrictions'); ?></dt>
                            <dd><?php echo lang('desc_agent_call_restrictions') ;?></dd>
                            <div class="input-group">
                                <select v-model="agent_settings.agent_call_restrictions['value']" id="agent_call_restrictions" name="agent_call_restrictions" class="form-control">
                                    <option value="own"><?php echo lang('own_calls'); ?></option>
                                    <option value="queue"><?php echo lang('queue_calls'); ?></option>
                                    <option value="all"><?php echo lang('all'); ?></option>
                                </select>
                                <button @click="set_item('agent_call_restrictions')" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </div>
                        </dl>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <dl>
                            <dt><?php echo lang('agent_show_in_dashboard'); ?></dt>
                            <dd><?php echo lang('desc_agent_show_in_dashboard') ;?></dd>
                            <div class="input-group">
                                <select v-model="agent.show_in_dashboard" id="show_in_dashboard" name="show_in_dashboard" class="form-control">
                                    <option value="no"><?php echo lang('no'); ?></option>
                                    <option value="yes"><?php echo lang('yes'); ?></option>
                                </select>
                                <button @click="set_item('show_in_dashboard')" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
