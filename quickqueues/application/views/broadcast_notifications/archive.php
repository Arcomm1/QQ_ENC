<div class="row" id="broadcast_notitications">
    <div class="col-md-12">
        <div class="card border-info">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><?php echo lang('broadcast_notifs'); ?></h4>
                <div class="btn-group">
                    <a class="btn btn-info" href="<?php echo site_url('broadcast_notifications/index'); ?>"><?php echo lang('active'); ?></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table table-hover table-sm" id="tbl-bcasts">
                        <thead>
                            <tr class="table-primary">
                                <th scope="col"><?php echo lang('name'); ?></th>
                                <th scope="col"><?php echo lang('description'); ?></th>
                                <th scope="col"><?php echo lang('date'); ?></th>
                                <th scope="col"><?php echo lang('author'); ?></th>
                                <th scope="col" width="20%"><?php echo lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="create_bcast" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('create_new_cat'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input v-model="name" type="text" class="form-control mb-2" id="name" name="name" placeholder="<?php echo lang('enter_name'); ?>">
<textarea v-model="description" type="text" class="form-control mb-2" id="description" name="description" placeholder="<?php echo lang('enter_text'); ?>"></textarea>
                            <button v-if="name.length > 1" @click="save_bcast()" data-dismiss="modal" class="btn btn-info btn-block"><?php echo lang('save'); ?></button>
                            <button v-else disabled class="btn btn-info btn-block"><?php echo lang('save'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>
