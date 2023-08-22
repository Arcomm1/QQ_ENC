<div class="row justify-content-center" id="create_agent">
    <div class="col-md-12">
        <div class="card border-warning">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h4><?php echo lang('create_agent'); ?></h4>
                    </div>
                    <div class="col">
                        <a class="btn btn-danger float-right" href="<?php echo site_url('agents'); ?>"><?php echo lang('back'); ?></a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php echo form_open(false, array('id' => 'form_create_agent')); ?>
                    <fieldset>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="name"><?php echo lang('name'); ?></label>
                                    <input tabindex="1" v-model="name" type="text" class="form-control" id="name" name="name" aria-describedby="name_status" placeholder="<?php echo lang('enter_name'); ?>">
                                    <small v-if="name_err" id="name_status" class="form-text text-danger">{{ name_err }}</small>
                                    <small v-else="name_err" id="name_status" class="form-text text-muted"><?php echo lang('desc_agent_name'); ?></small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="extension"><?php echo lang('extension'); ?></label>
                                    <input tabindex="3" v-model="extension" type="extension" class="form-control" id="extension" name="extension" aria-describedby="extension" placeholder="<?php echo lang('enter_extension'); ?>">
                                    <small v-if="extension_err" id="extension_status" class="form-text text-danger">{{ extension_err }}</small>
                                    <small v-else id="extension_err" class="form-text text-muted"><?php echo lang('desc_extension'); ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="username"><?php echo lang('queue'); ?></label>
                                    <select tabindex="6" v-model="queue" class="form-control" id="queue" name="queue" aria-describedby="queue_status">
                                        <option value=""></option>
                                        <?php foreach ($queues as $q) { ?>
                                            <option value="<?php echo $q->id; ?>"><?php echo $q->display_name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <small v-if="queue_err" id="queue_status" class="form-text text-danger">{{ queue_err }}</small>
                                    <small v-else="queue_err" id="queue_status" class="form-text text-muted"><?php echo lang('desc_agent_queue'); ?></small>
                                </div>
                            </div>
                        </div>

                    </fieldset>
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <center>
                                <button v-if="can_submit" type="submit" class="btn btn-primary"><?php echo lang('save'); ?></button>
                                <button v-else disabled type="submit" class="btn btn-primary"><?php echo lang('save'); ?></button>
                            </center>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>