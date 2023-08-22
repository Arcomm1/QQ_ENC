<div class="container-lg mt-3" id="edit_user">
    <div class="col-md-12">
        <div class="card border-warning">
            <div class="card-header">
                <div class="row">
                    <div class="col d-flex justify-content-between">
                        <h4>{{ lang['edit_user']+' '+display_name }}</h4>
                        <div class="btn-group float-right">
                            <?php if ($user_id != 1) { ?>
                            <button v-if="role == 'manager'" class="btn btn-info" data-toggle="modal" data-target="#manage_queues"><?php echo lang('manage_queues'); ?>{{ ' ('+num_user_queues+')' }}</button>
                            <button @click="de_activate()" :class="enabled == 'yes' ? 'btn btn-warning' : 'btn btn-success'">{{ enabled == 'yes' ? lang['deactivate_user'] : lang['activate_user'] }}</button>
                            <button @click="delete_user()" class="btn btn-danger"><?php echo lang('delete_user'); ?></button>
                            <?php } ?>
                            <a class="btn btn-primary float-right" href="<?php echo site_url('users'); ?>"><?php echo lang('back'); ?></a>
                            <a class="btn btn-success" href="<?php echo site_url('users/create'); ?>"><i class="cil-plus text-strong"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php echo form_open(); ?>
                    <fieldset>
                        <div class="row">
                            <div class="col">
                                <div class="form-group" style="margin-bottom: 40px;">
                                    <label for="username"><?php echo lang('username'); ?></label>
                                    <input tabindex="1" disabled v-model="username" type="text" class="form-control" id="username" name="name" aria-describedby="username_status" placeholder="<?php echo lang('enter_username'); ?>">
                                    <small v-if="username_err" id="username_status" class="form-text text-danger">{{ username_err }}</small>
                                    <small v-else="username_err" id="username_status" ><?php echo lang('desc_username'); ?></small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group" >
                                    <label for="username"><?php echo lang('password'); ?></label>
                                    <input tabindex="4" v-model="password" type="password" class="form-control" id="password" name="pwd" aria-describedby="password" placeholder="<?php echo lang('enter_password'); ?>">
                                    <small v-if="password_err" id="password_status" class="form-text text-danger">{{ password_err }}</small>
                                    <small v-else="password_err" id="password_status"><?php echo lang('desc_password'); ?></small>
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
                                    <small v-else="password_confirm_err" id="password_confirm_status"><?php echo lang('desc_confirm_password'); ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group" style="margin-bottom: 30px;">
                                    <label for="email"><?php echo lang('email'); ?></label>
                                    <input tabindex="3" v-model="email" type="email" class="form-control" id="email" name="email" aria-describedby="email" placeholder="<?php echo lang('enter_email'); ?>">
                                    <small v-if="email_err" id="email_status" class="form-text text-danger">{{ email_err }}</small>
                                    <small v-else id="email"><?php echo lang('desc_email'); ?></small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="username"><?php echo lang('role'); ?></label>
                                    <select v-if="num_user_queues == 0 & role == 'manager'" tabindex="6" v-model="role" class="form-control" id="role" name="role" aria-describedby="role"></select>
                                    <select v-else-if="num_user_agents == 0 & role == 'agent'" tabindex="6" v-model="role" class="form-control" id="role" name="role" aria-describedby="role"></select>
                                    <select v-else tabindex="6" v-model="role" disabled class="form-control" id="role" name="role" aria-describedby="role"></select>
                                    <small v-if="role_err" id="role_status" class="form-text text-danger">{{ role_err }}</small>
                                    <small v-else id="role_status" ><?php echo lang('desc_role'); ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
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
                        <div class="row mb-2">
                            <div class="col">
                                <div class="form-group">
                                    <label for="can_listen"><?php echo lang('can_listen'); ?></label>
                                    <select name="can_listen" id="can_listen" class="form-control">
                                        <?php foreach (array('yes', 'no', 'own') as $p) {
                                            if ($user->can_listen == $p) {
                                                echo "<option selected value='".$p."'>".lang($p)."</option>";
                                            } else {
                                                echo "<option value='".$p."'>".lang($p)."</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="can_download"><?php echo lang('can_download'); ?></label>
                                    <label for="can_download"><?php echo lang('can_download'); ?></label>
                                    <select name="can_download" id="can_download" class="form-control">
                                        <?php foreach (array('yes', 'no', 'own') as $p) {
                                            if ($user->can_download == $p) {
                                                echo "<option selected value='".$p."'>".lang($p)."</option>";
                                            } else {
                                                echo "<option value='".$p."'>".lang($p)."</option>";
                                            }
                                        }
                                        ?>
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
    <div class="modal fade" id="manage_queues" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('queues'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="add_queue"><?php echo lang('select_queue'); ?></label>
                            <div class="input-group">
                                <select v-model="queue_to_add" id="add_queue" class="form-control">
                                    <option value=""><?php echo lang('select'); ?></option>
                                    <?php foreach ($user_queues as $q) { ?>
                                        <option value="<?php echo $q->id; ?>"><?php echo $q->display_name; ?></option>
                                    <?php } ?>
                                </select>
                                <button v-if="queue_to_add != ''" @click="assign_queue()" class="btn btn-primary"><?php echo lang('assign'); ?></button>
                                <button v-else disabled @click="assign_queue()" class="btn btn-primary"><?php echo lang('assign'); ?></button>
                            </div>
                        </div>
                     </div>
                     <br/>
                     <div class="row">
                        <div class="col-md-12">
                            <div class="card border-secondary">
                                <div class="card-body">
                                    <h4><?php echo lang('assigned_queues'); ?></h4>
                                    <li v-for="q in user_queues" class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ q.display_name }}
                                        <button @click="unassign_queue(q.id)" class="btn btn-danger btn-sm">
                                            {{ lang['unassign'] }}
                                        </button>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="manage_agents" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('agents'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="add_agent"><?php echo lang('select_agent'); ?></label>
                            <div class="input-group">
                                <select v-model="agent_to_add" id="add_agent" class="form-control">
                                    <?php foreach ($user_agents as $a) { ?>
                                        <option value="<?php echo $a->id; ?>"><?php echo $a->extension." - ".$a->display_name; ?></option>
                                    <?php } ?>
                                </select>
                                <button v-if="agent_to_add != ''" @click="assign_agent()" class="btn btn-primary"><?php echo lang('assign'); ?></button>
                                <button v-else disabled @click="assign_agent()" class="btn btn-primary"><?php echo lang('assign'); ?></button>
                            </div>
                        </div>
                     </div>
                     <br/>
                     <div class="row">
                        <div class="col-md-12">
                            <div class="card border-secondary">
                                <div class="card-body">
                                    <h4><?php echo lang('assigned_agent'); ?></h4>
                                    <li v-for="a in user_agents" class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ a.extension+' - '+a.display_name }}
                                        <button @click="unassign_agent(a.id)" class="btn btn-danger btn-sm">
                                            {{ lang['unassign'] }}
                                        </button>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
