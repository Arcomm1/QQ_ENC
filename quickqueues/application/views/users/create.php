<div class="container-lg mt-3" id="create_user">
    <div class="col-md-12">
        <div class="card border-top-danger border-danger border-top-3">
            <div class="card-header">
                <div class="row">
                    <div class="col d-flex justify-content-between">
                        <h4><?php echo lang('create_user'); ?></h4>
                        <a class="btn btn-danger float-right" href="<?php echo site_url('users'); ?>"><?php echo lang('back'); ?></a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php echo form_open(false, array('id' => 'form_create_user')); ?>
                    <fieldset class="mb-4">
                        <div class="row">
                            <div class="col">
                                <div class="form-group" style="margin-bottom: 40px;">
                                    <label for="username"><?php echo lang('username'); ?></label>
                                    <input tabindex="1" v-model="username" type="text" class="form-control" id="username" name="name" aria-describedby="username_status" placeholder="<?php echo lang('enter_username'); ?>">
                                    <small v-if="username_err" id="username_status" class="form-text text-danger">{{ username_err }}</small>
                                    <small v-else="username_err" id="username_status"><?php echo lang('desc_username'); ?></small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="username"><?php echo lang('password'); ?></label>
                                    <input tabindex="4" v-model="password" type="password" class="form-control" id="password" name="pwd" aria-describedby="password" placeholder="<?php echo lang('enter_password'); ?>">
                                    <small v-if="password_err" id="password_status" class="form-text text-danger">{{ password_err }}</small>
                                    <small v-else="password_err" id="password_status" ><?php echo lang('desc_password'); ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group" style="margin-bottom: 30px;">
                                    <label for="display_name"><?php echo lang('display_name'); ?></label>
                                    <input tabindex="2" v-model="display_name" type="text" class="form-control" id="display_name" name="display_name" aria-describedby="display_name" placeholder="<?php echo lang('enter_display_name'); ?>">
                                    <small v-if="displayname_err" id="displayname_status" class="form-text text-danger">{{ displayname_err }}</small>
                                    <small v-else="displayname_err" id="displayname_status" ><?php echo lang('desc_display_name'); ?></small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="username"><?php echo lang('confirm_password'); ?></label>
                                    <input tabindex="5" v-model="password_confirm" type="password" class="form-control" id="password_confirm" aria-describedby="password_confirm" placeholder="<?php echo lang('confirm_password'); ?>">
                                    <small v-if="password_confirm_err" id="password_confirm_status" class="form-text text-danger">{{ password_confirm_err }}</small>
                                    <small v-else="password_confirm_err" id="password_confirm_status" ><?php echo lang('desc_confirm_password'); ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group" style="margin-bottom: 30px;">
                                    <label for="email"><?php echo lang('email'); ?></label>
                                    <input tabindex="3" v-model="email" type="email" class="form-control" id="email" name="email" aria-describedby="email" placeholder="<?php echo lang('enter_email'); ?>">
                                    <small v-if="email_err" id="email_status" class="form-text text-danger">{{ email_err }}</small>
                                    <small v-else id="email" ><?php echo lang('desc_email'); ?></small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="username"><?php echo lang('role'); ?></label>
                                    <select tabindex="6" v-model="role" class="form-control" id="role" name="role" aria-describedby="role"></select>
                                    <small v-if="password_confirm_err" id="password_confirm_status" >{{ role_err }}</small>
                                    <small v-else="password_confirm_err" id="password_confirm_status" ><?php echo lang('desc_role'); ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="associated_agent_id"><?php echo lang('agent'); ?></label>
                                    <select v-model="associated_agent_id" id="associated_agent_id" name="associated_agent_id" class="form-control">
                                    <?php foreach ($user_agents as $a) { ?>
                                        <option value="<?php echo $a->id; ?>"><?php echo $a->extension." - ".$a->display_name; ?></option>
                                    <?php } ?>
                                    </select>
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
