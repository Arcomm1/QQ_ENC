<div class="container">
    <div class="row">
        <div class="col">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col">
                            <div class="alert alert-success d-flex justify-content-between">
                                <h1><i class="fas fa-phone"></i></h1>
                                <h3 v-if="!dids.includes(call_in_progress.ConnectedLineNum)">{{ call_in_progress.ConnectedLineNum }}</h3>
                                <h3 v-else>{{ call_in_progress['RealCaller'] }}</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="alert alert-info d-flex justify-content-between">
                                <h1><i class="fas fa-clock"></i></h1>
                                <h3>{{ sec_to_min(call_in_progress.Seconds) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <div class="form-group">
                                <textarea v-model="future_call.comment" id="comment" rows="5" class="form-control" placeholder="<?php echo lang('comment'); ?>"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <?php if ($config->app_call_statuses == 'yes') { ?>
                            <div class="col">
                                <label for="call_status">{{ lang['status'] }}</label>
                                <select v-model="future_call.status" class="form-control" id='call_status' name="call_status">
                                <?php foreach (qq_get_call_statuses() as $s) { ?>
                                    <option value="<?php echo $s; ?>"><?php echo lang($s); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <?php if ($config->app_call_priorities == 'yes') { ?>
                            <div class="col">
                                <label for="call_priority">{{ lang['priority'] }}</label>
                                <select v-model="future_call.priority" class="form-control" id='call_priority' name="call_priority">
                                <?php foreach (qq_get_call_priorities() as $p) { ?>
                                    <option value="<?php echo $p; ?>"><?php echo lang($p); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <?php if ($config->app_call_curators == 'yes') { ?>
                            <div class="col">
                                <label for="call_curator_id">{{ lang['curator'] }}</label>
                                <select v-model="future_call.curator_id" class="form-control" id='call_curator_id' name="call_curator_id">
                                <?php foreach ($users as $u) { ?>
                                    <option value="<?php echo $u->id; ?>"><?php echo $u->display_name; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <?php if ($config->app_call_categories == 'yes') { ?>
                            <div class="col">
                                <label for="category_id"><?php echo lang('call_category'); ?></label>
                                <select v-model="future_call.category_id" class="form-control mb-2" id="category_id" name="category_id">
                                    <option value=""></option>
                                    <?php foreach ($call_categories as $ct) { ?>
                                        <option value="<?php echo $ct->id; ?>"><?php echo $ct->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <?php if ($config->app_call_tags == 'yes') { ?>
                            <div class="col">
                                <label for="subcategory_id">{{ lang['call_tag'] }}</label>
                                <select v-model="future_call.subcategory_id" class="form-control" id='subcategory_id' name="subcategory_id">
                                <?php foreach ($call_tags as $ct) { ?>
                                    <option value="<?php echo $ct->id; ?>"><?php echo $ct->name; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="custom_1"><?php echo lang('custom_1'); ?></label>
                            <input v-model="future_call.custom_1" type="text" name="custom_1" id="custom_1" class="form-control"/>
                        </div>
                        <div class="col">
                            <label for="custom_2"><?php echo lang('custom_2'); ?></label>
                            <input v-model="future_call.custom_2" type="text" name="custom_2" id="custom_2" class="form-control"/>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="custom_3"><?php echo lang('custom_3'); ?></label>
                            <input v-model="future_call.custom_3" type="text" name="custom_3" id="custom_3" class="form-control"/>
                        </div>
                        <div class="col">
                            <label for="custom_4"><?php echo lang('custom_4'); ?></label>
                            <input v-model="future_call.custom_4" type="text" name="custom_4" id="custom_4" class="form-control"/>
                        </div>
                    </div>
                    <?php if ($config->app_service_module == 'yes') { ?>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="service_id"><?php echo lang('service'); ?></label>
                            <select v-model="future_call.service_id" onChange='agent_crm.load_service_products(this.value)' class="form-control mb-2" id="service_id" name="service_id">
                                <option value=""></option>
                                <?php foreach ($services as $s) { ?>
                                    <option value="<?php echo $s->id ;?>"><?php echo $s->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col">
                            <label for="service_product_id"><?php echo lang('product'); ?></label>
                            <select v-model="future_call.service_product_id" onChange='agent_crm.load_service_product_types(this.value)' class="form-control mb-2" id="service_product_id" name="service_product_id">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="service_product_type_id"><?php echo lang('service_type'); ?></label>
                            <select v-model="future_call.service_product_type_id" onChange='agent_crm.load_service_product_subtypes(this.value)' class="form-control mb-2" id="service_product_type_id" name="service_product_type_id">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="service_product_subtype_id"><?php echo lang('service_subtype'); ?></label>
                            <select v-model="future_call.service_product_subtype_id" class="form-control mb-2" id="service_product_subtype_id" name="service_product_subtype_id">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col d-flex justify-content-center">
                            <button @click="save_current_call()" id="save_calls" class="btn btn-success">{{ lang['save'] }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-primary">
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
                            <td>{{ c.src }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
