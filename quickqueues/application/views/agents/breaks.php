<div class="container-lg mt-3" id="agent_breaks">
    <div class="row">
        <div class="col">
            <div class="row mb-2">
                <div class="col">
                    <div class="card border-top-info border-info border-top-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                   <!--  <h4 class="card-title">
                                        <?php echo lang('agent').": "?>
                                        {{ agent.display_name }}
                                    </h4> -->

                                    <div class="small text-medium-emphasis mb-3"><?php echo lang('stats'); ?></div>
                                </div>
                                <div class="btn-toolbar d-none d-md-block" role="toolbar">
                                    <div class="btn-group btn-group-toggle mx-3" data-coreui-toggle="buttons">
                                        <a class="btn btn-success" href="<?php echo site_url('agents/'); ?>"><i class="cil-media-step-backward"></i> <?php echo lang('back'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group">
                                <input class="form-control" autocomplete="off" type="text" id="date_gt" name="date_gt" placeholder="<?php echo lang('date_gt'); ?>" value="<?php echo $this->input->get('date_gt'); ?>">
                                <input class="form-control" autocomplete="off" type="text" id="date_lt" name="date_lt" placeholder="<?php echo lang('date_lt'); ?>" value="<?php echo $this->input->get('date_gt'); ?>">
                                <div class="btn-group">
                                    <button id="predefined_periods" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                    <div class="dropdown-menu" aria-labelledby="predefined_periods" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 36px, 0px);">
                                        <a @click="change_interval('today')" class="dropdown-item" href="javascript:;"><?php echo lang('today'); ?></a>
                                        <a @click="change_interval('yday')" class="dropdown-item" href="javascript:;"><?php echo lang('yesterday'); ?></a>
                                        <a @click="change_interval('tweek')" class="dropdown-item" href="javascript:;"><?php echo lang('this_week'); ?></a>
                                        <a @click="change_interval('tmonth')" class="dropdown-item" href="javascript:;"><?php echo lang('this_month'); ?></a>
                                        <a @click="change_interval('l7day')" class="dropdown-item" href="javascript:;"><?php echo lang('last_7_days'); ?></a>
                                        <a @click="change_interval('l14day')" class="dropdown-item" href="javascript:;"><?php echo lang('last_14_days'); ?></a>
                                        <a @click="change_interval('l30day')" class="dropdown-item" href="javascript:;"><?php echo lang('last_30_days') ;?></a>
                                    </div>
                                    <button @click="refresh" class="btn btn-primary" type="button"><?php echo lang('refresh') ?></button>
                                    <button @click="export_stats" type="button" class="btn btn-primary"><i class="cil cil-cloud-download"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <div class="tab-content" id="start_tabs">
                <div>
                    <div class="row mt-2">
                        <div class="col">
                            <div class="card border-top-primary border-primary border-top-3">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th scope="col"><?php echo lang('agent').": "?></th>
                                        <th scope="col">{{ lang['dnd_reason'] }}</th>
                                        <th scope="col">{{ lang['dnd_start_time'] }}</th>
                                        <th scope="col">{{ lang['dnd_end_time'] }}</th>
                                        <th scope="col">hh:mm</th>
                                    </tr>
                                    <tr v-for="agent in agents">
                                        <td scope="col">{{ agent.display_name }}</td>
                                        <td scope="col" >{{ brake_subjects[parseInt(agent.title)].title }}</td>
                                        <td scope="col">{{ agent.dnd_started_at }}</td>
                                        <td scope="col">{{ agent.dnd_ended_at }}</td>
                                        <td scope="col" >{{ agent.date_diff }} </td>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
