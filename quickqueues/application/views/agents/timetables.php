<div id="agent_timetables">
    <div class="row mb-2">
        <div class="col">
            <div class="card border-warning">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex justify-content-between align-items-center">
                            <span class="strong">{{ lang['agents'] }}</span>
                            <div class="btn-group">
				                <?php if ($config->app_track_agent_session_time == 'yes') { ?> 
                                <a href="<?php echo site_url('agents/index'); ?>" class="btn btn-primary pull-right">{{ lang['overview'] }}</a>
                                <?php } ?>
                                <a href="<?php echo site_url('agents/detailed_stats'); ?>" class="btn btn-info pull-right">{{ lang['stats'] }}</a>
                                <a href="<?php echo site_url('export/agent_historical_stats'); ?>" class="btn btn-success pull-right">{{ lang['compare'] }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php echo form_open(false, array('method' => 'get')); ?>
                    <div class="row">
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
                                            foreach ($user_agents as $a) {
                                                if ($this->input->get('agent_id') == $a->id) {
                                                    echo "<option selected value='".$a->id."'>".$a->extension." - ".$a->display_name."</option>";
                                                } else {
                                                    echo "<option value='".$a->id."'>".$a->extension." - ".$a->display_name."</option>";
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
                            <div class="form-group">
                                <label class="control-label sr-only"><?php echo lang('event'); ?></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang('event'); ?></span>
                                        </div>
                                        <select name="event_type" id="event_type" class="form-control">
                                            <option value=""></option>
                                            <?php
                                            foreach (array('SESSION', 'PAUSE') as $e) {
                                                if ($this->input->get('event_type') == $e) {
                                                    echo "<option selected value='".$e."'>".lang($e)."</option>";
                                                } else {
                                                    echo "<option value='".$e."'>".lang($e)."</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary"><?php echo lang('search'); ?></button>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                    <div class="row">
                        <div class="col">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr class="table-primary">
                                        <th scope="col"><?php echo lang('date'); ?></th>
                                        <th scope="col"><?php echo lang('event'); ?></th>
                                        <th scope="col"><?php echo lang('agent'); ?></th>
                                        <th scope="col"><?php echo lang('time'); ?></th>
                                    </tr>
                                </thead>
                                <?php foreach ($events as $e) { ?>
                                    <tr>
                                        <td><?php echo lang($e->event_type); ?></td>
                                        <td><?php echo $e->date; ?></td>
                                        <td><?php echo $agents[$e->agent_id]; ?></td>
                                        <td>
                                        <?php if ($e->event_type == 'STOPSESSION') { ?> 
                                            <?php echo sec_to_time($e->sessiontime); ?>
                                        <?php } ?>
                                        <?php if ($e->event_type == 'STOPPAUSE') { ?> 
                                            <?php echo sec_to_time($e->pausetime); ?>
                                        <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
