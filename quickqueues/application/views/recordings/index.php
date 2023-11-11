<div class="container-lg mt-3" id="recordings">
    <div class="row">
        <div class="col">
            <div class="card border-top-dark border-dark border-top-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <?php echo form_open(false,array('method' => 'get')); ?>
                            <div class="row">
                                <input type="hidden" name="subject_search_array" id="subject_search_array" value="">
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="src" name="src" placeholder="src" value="<?php echo $this->input->get('src'); ?>">
                                        <label for="src"><?php echo lang('src'); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="dst" name="dst" placeholder="dst" value="<?php echo $this->input->get('dst'); ?>">
                                        <label for="dst"><?php echo lang('dst'); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="queue_id" name="queue_id">
                                        <option selected value="0"><?php echo lang('select_queue'); ?></option>
                                        <?php foreach ($user_queues as $q) { ?>
                                            <?php if ($this->input->get('queue_id') == $q->id) { ?>
                                                <option selected value="<?php echo $q->id; ?>"><?php echo $q->display_name; ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $q->id; ?>"><?php echo $q->display_name; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        </select>
                                        <label for="queue_id"><?php echo lang('queue'); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="agent_id" name="agent_id">
                                        <option selected value="0"><?php echo lang('select_agent'); ?></option>
                                        <?php foreach ($user_agents as $a) { ?>
                                            <?php if ($this->input->get('agent_id') == $a->id) { ?>
                                                <option selected value="<?php echo $a->id; ?>"><?php echo $a->display_name; ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $a->id; ?>"><?php echo $a->display_name; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        </select>
                                        <label for="agent_id"><?php echo lang('agent'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="comment" name="comment" placeholder="comment" value="<?php echo $this->input->get('comment'); ?>">
                                        <label for="comment"><?php echo lang('comment'); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="event_type" name="event_type">
                                        <option selected value="0"><?php echo lang('event'); ?></option>
                                        <?php foreach ($interesting_events as $event_id => $event_name) { ?>
                                            <?php if ($this->input->get('event_type') == $event_id) { ?>
                                                <option selected value="<?php echo $event_id; ?>"><?php echo lang($event_name); ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $event_id; ?>"><?php echo lang($event_name); ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        </select>
                                        <label for="event_type"><?php echo lang('event'); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" autocomplete="off" tupe="text" id="date_gt" name="date_gt" placeholder="date_gt" value="<?php echo $this->input->get('date_gt'); ?>">
                                        <label for="date_gt"><?php echo lang('date_gt'); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" style="width:88%;display: inline-block" autocomplete="off" type="text" id="date_lt" name="date_lt" placeholder="date_lt" value="<?php echo $this->input->get('date_lt'); ?>">
                                        <label for="date_lt"><?php echo lang('date_lt'); ?></label>

                                    <div class="btn-group" style="display: inline-block;float:right">
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
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Search -->
                            <div class="row">
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <?php
                                        $all_parent_subjects=$this->Call_subjects_model->get_main_subjects();
                                        echo "<select name='search_parent_subjects' id='search_parent_subjects' class='form-control'>";
                                            echo "<option value=''>".lang('subject_title')."</option>";
                                            foreach($all_parent_subjects as $parent_subject){
                                                $subject_id=$parent_subject['id'];
                                                echo "<option value='".$subject_id."'>".$parent_subject['title']."</option>";

                                            }
                                        echo "</select>";
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <?php
                                            echo "<select name='search_child_1_subject' id='search_child_1_subject' class='form-control' >";
                                                echo "<option value=''>".lang('childd_1_suject')."</option>";
                                            echo "</select>";
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <?php
                                            echo "<select name='search_child_2_subject' id='search_child_2_subject' class='form-control' >";
                                                echo "<option value=''>".lang('childd_2_suject')."</option>";
                                            echo "</select>";
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <?php
                                            echo "<select name='search_child_3_subject' id='search_child_3_subject' class='form-control' >";
                                                echo "<option value=''>".lang('childd_3_suject')."</option>";
                                            echo "</select>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <!-- ////Endsearch//// -->
                            <div class="row">
                                <div class="col">
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-danger" href="<?php echo site_url('recordings');?>"><?php echo lang('reset'); ?></a>
                                        <a class="btn btn-success" href="<?php echo site_url('export/recordings?'.$this->input->server('QUERY_STRING'));?>"><?php echo lang('export'); ?></a>
                                        <button type="submit" class="btn btn-primary"><?php echo lang('search'); ?></button>
                                        <span class="btn btn-ghost"><?php echo lang('found')." $num_calls ".lang('calls'); ?></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <a class="btn btn-ghost text-success" href="<?php echo site_url('recordings?event_type=UNANSWERED&calls_without_service=yes&date_gt=&date_lt='); ?>">{{ lang['callback_queue'] }}</a>
                                </div>
                            </div>

                            <?php echo form_close(); ?>
                        </div>
                    </div>
                    <hr class="mt-3">
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table table-light fw-semibold">
                                        <tr>
                                            <th scope="col">&#35</th> <!-- New column for numeration -->
                                            <th scope="col"></th>
                                            <th scope="col"><?php echo lang('src'); ?></th>
                                            <th scope="col"><?php echo lang('dst'); ?></th>
                                            <th scope="col"><?php echo lang('queue'); ?></th>
                                            <th scope="col"><?php echo lang('date'); ?></th>
                                            <th scope="col" style="width:5%"><?php echo lang('call_time'); ?></th>
                                            <th scope="col" style="width:5%"><?php echo lang('actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $totalRowsBeforePage = ($this->pagination->cur_page - 1) * $this->pagination->per_page;
                                    $rowNumber           = $totalRowsBeforePage + 1;
                                    if ($totalRowsBeforePage < 0) 
                                    {
                                        $rowNumber = 1;
                                    }
                                    foreach ($calls as $c) {
                                        //print_r($c);
                                        ?>
                                    <tr class="table-row">
                                        <td scope="row"><?php echo $rowNumber; ?></td> <!-- Display row number -->
                                        <td scope="row">
                                            <?php if (in_array($c->event_type, array('COMPLETECALLER', 'COMPLETEAGENT', 'CONNECT'))) { ?>
                                                <i class="cil-arrow-thick-left text-success"></i>
                                            <?php } ?>
                                            <?php if ($c->event_type == 'ENTERQUEUE') { ?>
                                                <i class="cil-arrow-thick-left text-warning"></i>
                                            <?php } ?>
                                            <?php if (in_array($c->event_type, array('ABANDON', 'EXITWITHTIMEOUT', 'EXITEMPTY', 'EXITWITHKEY'))) { ?>
                                                <i class="cil-arrow-thick-left text-danger"></i>
                                            <?php } ?>
                                            <?php if ($c->event_type == 'OUT_ANSWERED') { ?>
                                                <i class="cil-arrow-thick-right text-success"></i>
                                            <?php } ?>
                                            <?php if (in_array($c->event_type, array('OUT_NOANSWER', 'OUT_BUSY', 'OUT_FAILED'))) { ?>
                                                <i class="cil-arrow-thick-right text-danger"></i>
                                            <?php } ?>
                                        </td>
                                        <td scope="row">
                                            <div scrPlaceholder><?php echo $c->src; ?></div>
                                            <?php if (in_array($c->event_type, array('OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY', 'OUT_FAILED'))) { ?>
                                            <div class="small text-medium-emphasis">
                                                <?php echo $agents[$c->agent_id]; ?>
                                            </div>
                                            <?php } ?>
                                        </td>
                                        <td scope="row">
                                            <div><?php echo $c->dst; ?></div>
                                            <?php if (in_array($c->event_type, array('COMPLETECALLER', 'COMPLETEAGENT', 'CONNECT'))) { ?>
                                            <div class="small text-medium-emphasis">
                                                <?php echo $agents[$c->agent_id]; ?>
                                            </div>
                                            <?php } ?>
                                        </td>
                                        <td scope="row"><?php echo $queues[$c->queue_id]; ?></td>
                                        <td scope="row">
                                            <div>
                                                <?php echo $c->date; ?>
                                            </div>
                                            <div class="small text-medium-emphasis">
                                                <?php echo lang($c->event_type); ?>
                                            </div>
                                        </td>
                                        <td scope="row">
                                            <div><?php echo sec_to_time($c->calltime); ?></div>
                                            <div class="small text-medium-emphasis">
                                                <!--<div><?php /*echo sec_to_time($c->holdtime); */?></div>-->
                                                <?php
                                                if ($c->event_type == 'ABANDON') {
                                                    echo "<div>".sec_to_time($c->waittime)."</div>";
                                                } else {
                                                    echo "<div>".sec_to_time($c->holdtime)."</div>";
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td scope="row" class="clickable-cell">
                                            <?php if ($logged_in_user->can_listen == 'yes') { ?>
                                                <a v-if="recordingsPopUp.audioFile !== ''" @click="load_player(<?php echo $c->id; ?>, <?php echo $rowNumber; ?>, '<?php echo $c->src; ?>', '<?php echo $c->dst; ?>')" data-coreui-toggle="modal" data-coreui-target="#play_recording" class="text-decoration-none"> <i class="cil-media-play text-success"></i></a>
                                            <?php } ?>
                                            <?php if ($logged_in_user->can_listen == 'own') { ?>
                                                <?php if ($logged_in_user->associated_agent_id == $c->agent_id) { ?>
                                                    <a v-if="recordingsPopUp.audioFile !== ''" @click="load_player(<?php echo $c->id; ?>, <?php echo $rowNumber; ?>,  '<?php echo $c->src; ?>', '<?php echo $c->dst; ?>')" data-coreui-toggle="modal" data-coreui-target="#play_recording" class="text-decoration-none"> <i class="cil-media-play text-success"></i></a>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($logged_in_user->can_download == 'yes') { ?>
                                                <a href="<?php echo site_url('api/recording/get_file/'.$c->id); ?>" class="text-decoration-none"><i class="cil-cloud-download text-info"></i></a>
                                            <?php } ?>
                                            <?php if ($logged_in_user->can_download == 'own') { ?>
                                                <?php if ($logged_in_user->associated_agent_id == $c->agent_id) { ?>
                                                    <a href="<?php echo site_url('api/recording/get_file/'.$c->id); ?>" class="text-decoration-none"><i class="cil-cloud-download text-info"></i></a>
                                                <?php } ?>
                                            <?php } ?>
                                            <i class="cil-comment-bubble text-warning modal_clear get_id" style="cursor:pointer; position:relative;" id="<?php echo $c->id ?>" data-toggle="modal" data-target="#call_subjects">
                                                <?php 
                                                        $comment = $this->Call_subjects_model->get_call_params($c->id);
                                                        $commentText = $comment['comment'];
                                                        $categoryID  = $comment['category_id'];
                                                        
                                                        if(strlen($commentText) > 0)
                                                        {
                                                            $inlineStyle = "pointer-events:none; position:absolute; font-size:14px; display:block; top:-3px; left:6px; font-weight: bold;";
                                                            echo '<i class="cil-check-alt text-success" style="'.$inlineStyle.'" ></i>';
                                                        }
                                                        if(strlen($categoryID) > 0)
                                                        {
                                                            $inlineStyle = "pointer-events:none; position:absolute; font-size:14px; display:block; top:5px; left:6px; font-weight: bold;";
                                                            echo '<i class="cil-check-alt text-info" style="'.$inlineStyle.'" ></i>';
                                                        }

                                                ?>
                                            </i>
                                            <a @click="get_events(<?php echo "'".$c->uniqueid."'"; ?>)" data-coreui-toggle="modal" data-coreui-target="#call_details" class="text-decoration-none"><i class="cil-list text-primary"></i></a>
                                            <?php if($config->app_track_called_back_calls == 'yes') { if (in_array($c->event_type, array('ABANDON', 'EXITEMPTY', 'EXITWITHKEY', 'EXITWITHTIMEOUT'))) {?>
                                                <div class="dropdown-menu" aria-labelledby="predefined_periods" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 36px, 0px);">
                                                    <a @click="toggle_called_back(<?php echo $c->id; ?>, 'yes')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('yes'); ?></a>
                                                    <a @click="toggle_called_back(<?php echo $c->id; ?>, 'no')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('no'); ?></a>
                                                    <a @click="toggle_called_back(<?php echo $c->id; ?>, 'nop')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('cb_nop'); ?></a>
                                                    <a @click="toggle_called_back(<?php echo $c->id; ?>, 'nah')" href="javascript:void(0)" class="dropdown-item" href="javascript:void(0)"><?php echo lang('cb_nah'); ?></a>

                                                </div>
                                                <a id="called_back_<?php echo $c->id; ?>" class="<?php echo $called_back_styles[$c->called_back]; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="display:none;"><i class="cil-reload"></i></a> <!-- called back icon, hidden temporarily -->
                                            <?php } } ?>
                                        </td>
                                    </tr>
                                    <?php 

                                    $rowNumber++; // Increment row number counter
                                } ?>
                                    <tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center"><?php echo $pagination_links; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="call_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('call_details'); ?></h5>
                    </button>
                </div>
                <div class="modal-body">
                    <small>
                        <table class="table table-hover table-bordered">
                            <th><?php echo lang('date'); ?></th>
                            <th><?php echo lang('event'); ?></th>
                            <th><?php echo lang('agent'); ?></th>
                            <th><?php echo lang('call_time'); ?></th>
                            <th><?php echo lang('hold_time'); ?></th>
                            <th><?php echo lang('ring_time'); ?></th>
                            <tr v-for="e in call_events">
                                <td>{{ e.date }}</td>
                                <td>{{ e.event_type }}</td>
                                <td v-if="e.agent_id > 0">{{ agents[e.agent_id].display_name }}</td>
                                <td v-else="e.agent_id > 0"></td>
                                <td>{{ sec_to_time(e.calltime) }}</td>
                                <td v-if="e.event_type == 'ABANDON'">{{ sec_to_time(e.waittime) }}</td>
                                <td v-else>{{ sec_to_time(e.holdtime) }}</td>
                                <td>{{ sec_to_time(e.ringtime) }}</td>
                            </tr>
                        </table>
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" class="close" data-coreui-dismiss="modal"><?php echo lang('close'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="play_recording" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="customRow">
                        <div class="customRowTitle">
                            <?php echo lang('play_recording'); ?><span> : #{{recordingsPopUp.name}}</span>
                        </div>
                        <div>
                            <?php echo lang('src'); ?> : {{ recordingsPopUp.from}}
                        </div>
                        <div>
                            <?php echo lang('dst'); ?> : {{ recordingsPopUp.to}}
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                        <audio id="qq_player" :src="recordingsPopUp.audioFile" type="audio/wav" controls></audio>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" class="close" data-coreui-dismiss="modal"><?php echo lang('close'); ?></button>
                </div>
            </div>
        </div>
    </div>
    
</div>
<?php

/* ---- Modal Add Subject---*/
    echo "<div class='modal fade' id='call_subjects' tabindex='-1' role='dialog' aria-labelledby='add_subjects_modal_Title' aria-hidden='true'>";
    echo "<div class='modal-dialog modal-dialog-centered modal-lg' role='document'>";
            echo "<div class='modal-content'>";
                echo "<div class='modal-header'>";
                    echo "<h5 class='modal-title' id='add_subjects_modal_LongTitle'>".lang('subjects')."</h5>";
                echo "</div>";
                echo "<div class='modal-body'>";
                    echo "<div style='display:inline-block; vertical-align:top;'>";
                        $all_parent_subjects=$this->Call_subjects_model->get_visible_subjects();
                            echo "<select name='parent_subjects' id='parent_subjects' class='form-control' style='width:220px;margin:0px 20px;'>";
                                echo "<option value=''>".lang('subject_title')."</option>";
                                foreach($all_parent_subjects as $parent_subject){
                                    $subject_id=$parent_subject['id'];
                                    echo "<option value='".$subject_id."'>".$parent_subject['title']."</option>";

                                }
                        echo "</select>";
                        echo "<select name='child_1_subject' id='child_1_subject' class='form-control' style='width:220px;margin:20px;'>";
                            echo "<option value=''>".lang('childd_1_suject')."</option>";
                        echo "</select>";

                        echo "<select name='child_2_subject' id='child_2_subject' class='form-control' style='width:220px;margin:20px;'>";
                            echo "<option value=''>".lang('childd_2_suject')."</option>";
                        echo "</select>";

                        echo "<select name='child_3_subject' id='child_3_subject' class='form-control' style='width:220px;margin:20px;'>";
                            echo "<option value=''>".lang('childd_3_suject')."</option>";
                        echo "</select>";
                    echo "</div>";
                    echo "<div style='display:inline-block;height:100%;width:300px'>";
                        echo "<textarea name='subject_comment' id='subject_comment' style='height:210px;width:480px;'  class='form-control' placeholder='".lang('comment')."'></textarea>";
                    echo "</div>";
                echo "</div>";
                echo "<div class='modal-footer'>";
                    echo "<input type='hidden' name='call_record_id' id='call_record_id'>";
                    echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>".lang('close')."</button>";
                    echo "<button type='button' name='add_subject_comment' id='add_subject_comment' class='btn btn-success'>".lang('assign')."</button>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
/* --- End Of Modal Add Subject--- */
?>
<script>
    $(document).ready(function() 
    {
        family_array=[];

        //Pass Reocrd Id And All Required Data To Modal Window
        $(document).on('click', '.get_id', function() {
            $('#call_record_id').val(this.id);
            $('#subject_comment').val('');
            var call_record_id=this.id;

            $.ajax({
                url: "<?php echo base_url() ?>index.php/Call_subjects/get_call_comment_params",
                type: "POST",
                data: {
                    type: "get_call_comment_params",
                    id: call_record_id,
                },
                cache: false,
                success: function(dataResult){
                    var dataResult = JSON.parse(dataResult);
                    var subject_comment_result=dataResult[0].comment;
                    var subject_family_result=dataResult[0].subject_family;
                    var parent_selected_id_array=[];
                    var id_to_find_childs_array=[];
                    //IF Result Is Not Empty
                    if(subject_family_result){
                        var family_array_result=subject_family_result.split('|');
                        var family_array_length=family_array_result.length-1;
                        var select_id_array=['child_1_subject', 'child_2_subject', 'child_3_subject'];
                        var table_counter=0;
                        for(var i=0; i<family_array_length; i++){
                            if(table_counter<3){
                                table_counter++;
                            }

                            id_to_find_childs_array[i]=family_array_result[i];//Id-s For Reques To Find Child <option> Group
                            parent_selected_id_array[i]=family_array_result[i];//Id-s Must Be Parents And Selected <option selected>
                            //console.log(id_to_find_childs_array[i]);

                            $.ajax({
                                url: "<?php echo base_url() ?>index.php/Call_subjects/get_parents_childs",
                                type: "POST",
                                async : false,
                                data: {
                                    type: "get_parents_childs",
                                    parent_child_id: id_to_find_childs_array[i],
                                    table_id: table_counter,
                                },
                                cache: false,
                                success: function(dataResult){

                                    var dataResult = JSON.parse(dataResult);
                                    var data_result_count = Object.keys(dataResult[0]).length;
                                    /* for(var j=0; j<data_result_count;j++){
                                        console.log(parent_selected_id_array[i]);
                                    } */
                                    items=dataResult[0];
                                        $.each(items, function (k, item) {
                                                $('#'+select_id_array[i]).append($('<option>', {
                                                    value: item.id,
                                                    text : item.title,
                                                }));
                                        });
                                },
                            });
                        }
                        $("#parent_subjects option[value="+parent_selected_id_array[0]+"]").attr('selected','selected');
                        $("#"+select_id_array[0]+" option[value="+parent_selected_id_array[1]+"]").attr('selected','selected');
                        $("#"+select_id_array[1]+" option[value="+parent_selected_id_array[2]+"]").attr('selected','selected');
                        $("#"+select_id_array[2]+" option[value="+parent_selected_id_array[3]+"]").attr('selected','selected');

                        $('#subject_comment').val(subject_comment_result);
                    }
                    $('#subject_comment').val(subject_comment_result);
                }
            });

        });

        //Collect  Child 1 Data
        $(document).on('change', '#parent_subjects', function() {
            $('#child_1_subject').find('option:not(:first)').remove();
            $('#child_2_subject').find('option:not(:first)').remove();
            $('#child_3_subject').find('option:not(:first)').remove();
            var parent_id=this.value;
            if(!parent_id){
                family_array[0]='';
                family_array[1]='';
                family_array[2]='';
                family_array[3]='';

                return false;
            }
            family_array[0]=parent_id+'|';


            $.ajax({
                url: "<?php echo base_url() ?>index.php/Call_subjects/get_child_1_subject_all",
                type: "POST",
                data: {
                    type: "display_child_1_subject",
                    parent_id: parent_id,
                },
                cache: false,
                success: function(dataResult){
                    var dataResult = JSON.parse(dataResult);
                    console.log(dataResult['child_1_result'][0].visible);
                    if(!dataResult['child_1_result']){
                        return false;
                    }
                    var items=dataResult['child_1_result'];
                    $.each(items, function (i, item) {
                        if(item.visible =='1') {
                            $('#child_1_subject').append($('<option>', {
                                value: item.id,
                                text: item.title
                            }));
                        }
                    });
                }
            });
        });

        //Collect  Child 2 Data
        $(document).on('change', '#child_1_subject', function() {
            $('#child_2_subject').find('option:not(:first)').remove();
            $('#child_3_subject').find('option:not(:first)').remove();
            var parent_id=this.value;

            if(!parent_id){
                family_array[1]='';
                family_array[2]='';
                family_array[3]='';
                return false;
            }
            family_array[1]=parent_id+'|';
            $.ajax({
                url: "<?php echo base_url() ?>index.php/Call_subjects/get_child_2_subject",
                type: "POST",
                data: {
                    type: "display_child_2_subject",
                    parent_id: parent_id,
                },
                cache: false,
                success: function(dataResult){
                    var dataResult = JSON.parse(dataResult);
                    //console.log(dataResult['child_2_result'][0].title);
                    if(!dataResult['child_2_result']){
                        return false;
                    }
                    var items=dataResult['child_2_result'];
                    $.each(items, function (i, item) {
                        if(item.visible =='1') {
                            $('#child_2_subject').append($('<option>', {
                                value: item.id,
                                text: item.title
                            }));
                        }
                    });
                }
            });
        });

        //Collect  Child 3 Data
        $(document).on('change', '#child_2_subject', function() {
            $('#child_3_subject').find('option:not(:first)').remove();
            var parent_id=this.value;
            if(!parent_id){
                family_array[2]='';
                family_array[3]='';
                return false;
            }
            family_array[2]=parent_id+'|';
            $.ajax({
                url: "<?php echo base_url() ?>index.php/Call_subjects/get_child_3_subject",
                type: "POST",
                data: {
                    type: "display_child_3_subject",
                    parent_id: parent_id,
                },
                cache: false,
                success: function(dataResult){
                    var dataResult = JSON.parse(dataResult);
                    //console.log(dataResult['child_3_result'][0].title);
                    if(!dataResult['child_3_result']){
                        return false;
                    }
                    var items=dataResult['child_3_result'];
                    $.each(items, function (i, item) {
                        if(item.visible =='1') {
                            $('#child_3_subject').append($('<option>', {
                                value: item.id,
                                text: item.title
                            }));
                        }
                    });
                }
            });
        });

        $(document).on('change', '#child_3_subject', function() {
            //$('#child_3_subject').find('option:not(:first)').remove();
            var parent_id=this.value;
            if(!parent_id){
                family_array[3]='';
                return false;
            }
            family_array[3]=parent_id+'|';
        });


         //Update(Save) Subjec Comment
         $(document).on('click', '#add_subject_comment', function() {
            var subject_comment=$('#subject_comment').val();

             //var subject_family=family_array[0]+family_array[1]+family_array[2]+family_array[3];
             //var subject_family = $('#parent_subjects' ).val()+'|'+$('#child_1_subject').val()+'|';

             var subject_family = '';

             if( $('#parent_subjects' ).val().length > 0){
                 subject_family+=$('#parent_subjects' ).val()+'|';
             }


             if( $('#child_1_subject' ).val().length > 0){
                 subject_family+=$('#child_1_subject' ).val()+'|';
             }

             if( $('#child_2_subject' ).val().length > 0){
                 subject_family+=$('#child_2_subject' ).val()+'|';
             }

             if( $('#child_3_subject' ).val().length > 0){
                 subject_family+=$('#child_3_subject' ).val()+'|';
             }

             //alert(subject_family);

            var call_record_id=$('#call_record_id').val();

            $.ajax({
                url: "<?php echo base_url() ?>index.php/Call_subjects/add_subject_comment",
                type: "POST",
                data: {
                    type: "add_subject_comment",
                    id: call_record_id,
                    subject_family: subject_family,
                    subject_comment: subject_comment,
                },
                cache: false,
                success: function(dataResult){
                    window.location.reload();
                }
            });
        });

    /* -------------------------- Search Selects ------------------------- */
    //Collect  Child 1 Data For Search
    $(document).on('change', '#search_parent_subjects', function() {
            $('#search_child_1_subject').find('option:not(:first)').remove();
            $('#search_child_2_subject').find('option:not(:first)').remove();
            $('#search_child_3_subject').find('option:not(:first)').remove();

            $('#subject_search_array').val();

            var parent_id=this.value;
            if(!parent_id){
                return false;
            }

            $.ajax({
                url: "<?php echo base_url() ?>index.php/Call_subjects/get_child_1_subject_all",
                type: "POST",
                data: {
                    type: "display_child_1_subject",
                    parent_id: parent_id,
                },
                cache: false,
                success: function(dataResult){
                    $('#subject_search_array').val(parent_id+'|');
                    var dataResult = JSON.parse(dataResult);
                    //console.log(dataResult['child_1_result'][0].title);
                    if(!dataResult['child_1_result']){
                        return false;
                    }
                    var items=dataResult['child_1_result'];
                    $.each(items, function (i, item) {
                        $('#search_child_1_subject').append($('<option>', {
                            value: item.id,
                            text : item.title
                        }));
                    });
                }
            });
        });

        //Collect  Child 2 Data For Search
        $(document).on('change', '#search_child_1_subject', function() {
            $('#search_child_2_subject').find('option:not(:first)').remove();
            $('#search_child_3_subject').find('option:not(:first)').remove();
            var parent_id=this.value;
            if(!parent_id){
                return false;
            }

            $.ajax({
                url: "<?php echo base_url() ?>index.php/Call_subjects/get_child_2_subject",
                type: "POST",
                data: {
                    type: "display_child_2_subject",
                    parent_id: parent_id,
                },
                cache: false,
                success: function(dataResult){
                    $('#subject_search_array').val($('#subject_search_array').val()+parent_id+'|');
                    var dataResult = JSON.parse(dataResult);
                    //console.log(dataResult['child_2_result'][0].title);
                    if(!dataResult['child_2_result']){
                        return false;
                    }
                    var items=dataResult['child_2_result'];
                    $.each(items, function (i, item) {
                        $('#search_child_2_subject').append($('<option>', {
                            value: item.id,
                            text : item.title
                        }));
                    });
                }
            });
        });

        //Collect  Child 3 Data For Search
        $(document).on('change', '#search_child_2_subject', function() {
            $('#csearch_hild_3_subject').find('option:not(:first)').remove();
            var parent_id=this.value;
            if(!parent_id){
                return false;
            }

            $.ajax({
                url: "<?php echo base_url() ?>index.php/Call_subjects/get_child_3_subject",
                type: "POST",
                data: {
                    type: "display_child_3_subject",
                    parent_id: parent_id,
                },
                cache: false,
                success: function(dataResult){
                    $('#subject_search_array').val($('#subject_search_array').val()+parent_id+'|');
                    var dataResult = JSON.parse(dataResult);
                    //console.log(dataResult['child_3_result'][0].title);
                    if(!dataResult['child_3_result']){
                        return false;
                    }
                    var items=dataResult['child_3_result'];
                    $.each(items, function (i, item) {
                        $('#search_child_3_subject').append($('<option>', {
                            value: item.id,
                            text : item.title
                        }));
                    });
                }
            });
        });

        //Fix Last Value For Search
        $(document).on('change', '#search_child_3_subject', function() {
            //$('#csearch_hild_3_subject').find('option:not(:first)').remove();
            var parent_id=this.value;
            if(!parent_id){
                return false;
            }
            $('#subject_search_array').val($('#subject_search_array').val()+parent_id+'|');
        });

        /* Reset Options */
        $('.modal_clear').click(function(){
            $('#parent_subjects').prop('selectedIndex', 0);
            $('#child_1_subject').find('option:not(:first)').remove();
            $('#child_2_subject').find('option:not(:first)').remove();
            $('#child_3_subject').find('option:not(:first)').remove();
        });

        //click event fot tabe row
        $(".table-row").click(function() 
        {
            $(".table-row").removeClass("table-active");
            $(".table-row").css("background-color", ""); // Reset background color for other rows
            $(this).addClass("table-active");
            $(this).css("background-color", "#c0c0c0"); // Set desired background color for the active row
        });

        // Click event for the document
        $(document).click(function(event) 
        {
            // Check if the clicked element is not within the table or the modal
            if (!$(event.target).closest(".table").length && !$(event.target).closest(".modal.fade").length) 
            {
                $(".table-row").removeClass("table-active");
                $(".table-row").css("background-color", ""); // Reset background color for all rows
            }
        });
        
    });
</script>
