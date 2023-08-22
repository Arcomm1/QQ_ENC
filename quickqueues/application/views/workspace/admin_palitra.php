<div class="container" id="agent_crm">
    <div class="row mb-2">
        <div class="col">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-4">
                            <div class="row mb-2">
                                <div class="col">
                                    <div class="alert alert-success d-flex justify-content-between">
                                        <h1><i class="fas fa-phone"></i></h1>
                                        <h3>{{ call_in_progress['src'] }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <div class="alert alert-success d-flex justify-content-between">
                                        <h1><i class="fas fa-user"></i></h1>
                                        <h3>{{ contact['name'] }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <div class="alert alert-info d-flex justify-content-between">
                                        <h1><i class="fas fa-clock"></i></h1>
                                        <h3>{{ sec_to_min(call_in_progress.Seconds) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col d-flex justify-content-center">
                                    <a href="#" data-toggle="modal" data-target="#update_customer_modal" id="update_customer_name" class="mb-2">{{ lang['edit_customer_name'] }}</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col d-flex justify-content-center">
                                    <button @click="save_current_call()" id="save_calls" class="btn btn-primary">{{ lang['save'] }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row mb-2">
                                <div class="col">
                                    <div class="form-group">
                                        <textarea v-model="future_call.comment" id="comment" rows="5" class="form-control" placeholder="<?php echo lang('comment'); ?>"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="department"><?php echo lang('status'); ?></label>
                                        <select v-model="future_call.status" name="status" id="status" class="form-control">
                                            <option value=""></option>
                                            <?php
                                            foreach (qq_get_palitra_call_statuses() as $s) {
                                                echo "<option value='".$s."'>".lang($s)."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php if ($config->app_ticket_fields_in_calls == 'yes') { ?>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="department"><?php echo lang('department'); ?></label>
                                            <select v-model="future_call.ticket_department_id" onChange='agent_crm.load_categories(this.value)' name="department_id" id="department_id" class="form-control">
                                                <option value=""></option>
                                                <?php
                                                foreach ($departments as $d) {
                                                    if ($this->input->get('department_id') == $d->id) {
                                                        echo "<option selected value='".$d->id."'>".$d->name."</option>";
                                                    } else {
                                                        echo "<option value='".$d->id."'>".$d->name."</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="call_category"><?php echo lang('call_category'); ?></label>
                                            <select v-model="future_call.ticket_category_id" onChange='agent_crm.load_subcategories(this.value)' name="category_id" id="category_id" class="form-control">
                                                <option value=""></option>
                                                <?php foreach ($categories as $c) { ?>
                                                    <option value="<?php echo $c->id ;?>"><?php echo $c->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="call_subcategory"><?php echo lang('call_subcategory'); ?></label>
                                            <select v-model="future_call.ticket_subcategory_id" class="form-control" id="subcategory_id" name="subcategory_id">
                                                <option value=""></option>
                                                <?php foreach ($subcategories as $s) { ?>
                                                    <option value="<?php echo $s->id ;?>"><?php echo $s->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php if ($config->app_ticket_module == 'yes') { ?>
        <div class="col">
            <div class="card border-info">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">
                        <span class="strong">{{ lang['tickets'] }}</span>
                        <div class="btn-group">
                            <a v-bind:href="app_url+'/tickets/create/'+call_in_progress['uniqueid']" class="btn btn-info pull-right">{{ lang['create'] }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table v-if="tickets" class="table table-sm table-hover">
                        <tr class="table-primary">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr v-for="t in tickets">
                            <td><i class="fa fa-arrow-down"></i></td>
                            <td>{{ t.created_at }}</td>
                            <td>{{ t.description }}</td>
                            <td>{{ lang[t.status] }}</td>
                            <td v-bind:id="'ticket-'+t.id">
                                <a v-bind:href="app_url+'/tickets/edit/'+t.id+'/'+current_call.uniqueid"><i class="fa fa-edit"></i></a>
                                <a v-on:click="assign_call_to_ticket(t.id)" href="#"><i class="fa fa-plus"></i></a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="col">
            <div class="card border-success">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">
                        <span class="strong">{{ lang['calls'] }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <table v-if="calls" class="table table-sm table-hover">
                        <tr class=" table-primary">
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr v-for="c in calls">
                            <td><i class="fa fa-arrow-down"></i></td>
                            <td>{{ c.date }}</td>
                            <td>{{ c.dst }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="update_customer_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <label for="new_contact_name"><?php echo lang('name'); ?></label>
                            <input type="text" name="new_contact_name" id="new_contact_name" class="form-control mb-2">
                            <button v-model="new_contact_name" v-on:click="update_contact_name()" id="save_new_contact_name" class="btn btn-block btn-info"><?php echo lang('save'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



