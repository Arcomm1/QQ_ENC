<div class="container">

<div class="row">
    <div class="col">
        <div class="card border-info">
            <div class="card-header">
                <strong>
                    <?php echo lang('callback_queue')." ".lang('found').": ".count($calls) ?>
                </strong>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover" id="tbl-calls">
                    <thead>
                        <tr class="table-primary">
                            <td>{{ lang['src'] }}</td>
                            <td>{{ lang['queue'] }}</td>
                            <td>{{ lang['hold_time'] }}</td>
                            <td>{{ lang['date'] }}</td>
                            <td width="10%">{{ lang['actions'] }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($calls as $c) { ?>
                            <tr>
                                <td><?php echo $c->src; ?></td>
                                <td><?php echo (array_key_exists($c->queue_id, $queues)) ? $queues[$c->queue_id] : ""; ?></td>
                                <td><?php echo sec_to_time($c->holdtime); ?></td>
                                <td><?php echo $c->date; ?></td>
                                <td>
                                    <div class="dropdown-menu" aria-labelledby="predefined_periods" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 36px, 0px);">
                                        <a @click="toggle_called_back(<?php echo $c->id; ?>, 'yes')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('yes'); ?></a>
                                        <a @click="toggle_called_back(<?php echo $c->id; ?>, 'no')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('no'); ?></a>
                                        <a @click="toggle_called_back(<?php echo $c->id; ?>, 'nop')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('cb_nop'); ?></a>
                                        <a @click="toggle_called_back(<?php echo $c->id; ?>, 'nah')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('cb_nah'); ?></a>
                                    </div>
                                    <a id="called_back_<?php echo $c->id; ?>" class="<?php echo $called_back_styles[$c->called_back]; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-retweet"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
