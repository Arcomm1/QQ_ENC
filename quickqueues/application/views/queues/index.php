<div class="container-lg mt-3" id="queues">
    <div class="row">
        <div class="col">
            <div class="card border-top-primary border-primary border-top-3">
                <div class="card-body">
                    <h4 class="card-title mb-3"><?php echo lang('queues'); ?></h4>
                    <table class="table">
                        <thead class="table-light fw-semibold">
                            <tr>
                                <th scope="col"><?php echo lang('number'); ?></th>
                                <th scope="col"><?php echo lang('name'); ?></th>
                                <th scope="col"><?php echo lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_queues as $q) { ?>
                                <?php 
                                // Skip rows where $q->name contains "Callback" or "callback"
                                if (stripos($q->display_name, 'Callback') !== false || stripos($q->display_name, 'callback') !== false) {
                                    continue;
                                }
                            ?>
                            <tr>
                                <td scope="row"><?php echo $q->name; ?></td>
                                <td><?php echo $q->display_name; ?></td>
                                <td width='20%'>
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-ghost-success" href="<?php echo site_url('queues/realtime/'.$q->id); ?>"><i class="cil-clock"></i></a>
                                        <a class="btn btn-ghost-info" href="<?php echo site_url('queues/stats/'.$q->id); ?>"><i class="cil-bar-chart"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        <tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
