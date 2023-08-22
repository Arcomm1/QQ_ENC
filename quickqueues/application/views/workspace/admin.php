<!-- Updated At 19.02.2023-->
<br><br>
<div class="container" id="agent_crm">
    <div class="row">
        <div class="col-4">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col">
                            <div class="alert alert-success d-flex justify-content-between">
                                <h1><i class="fas fa-phone"></i></h1>
                                <h3>{{ call_in_progress['src'] }}</h3>
                            </div>
                           <!-- <div class="alert alert-info d-flex justify-content-between">
                                <h1><i class="fas fa-clock"></i></h1>
                                <h3>{{ sec_to_min(call_in_progress.Seconds) }}</h3>
                            </div>-->
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <div class="form-group">
                                <textarea v-model="future_call.comment" id="comment" rows="5" class="form-control" placeholder="<?php echo lang('comment'); ?>"></textarea>
                                <!-- <input type="hidden" name="subject_family" id="subject_family"> -->
                                <div id="subjects_visual"></div>
                            </div>
                        </div>
                    </div>
                    <!--/// We Do not need This Category Division wright now ///-->

                    <!--<div class="row mb-2">
                        <?php /*if ($config->app_call_categories == 'yes') { */?>
                            <div class="col">
                                <label for="category_id"><?php /*echo lang('call_category'); */?></label>
                                <?php /*if ($config->app_call_subcategories == 'yes') { */?>
                                <select v-model="future_call.category_id" onChange='agent_crm.load_subcategories(this.value)' class="form-control mb-2" id="category_id" name="category_id">
                                <?php /*} else { */?>
                                <select v-model="future_call.category_id" class="form-control mb-2" id="category_id" name="category_id">
                                <?php /*} */?>
                                    <option value=""></option>
                                    <?php /*foreach ($call_categories as $ct) { */?>
                                        <option value="<?php /*echo $ct->id; */?>"><?php /*echo $ct->name; */?></option>
                                    <?php /*} */?>
                                </select>
                            </div>
                        <?php /*} */?>
                    </div>-->

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
                            <!-- <button @click="save_current_call()" id="save_calls" class="btn btn-success">{{ lang['save'] }}</button> -->
                            <input type="hidden" name="live_uniqueid" id="live_unicueid" v-model= call_in_progress['uniqueid']>
                            <button id="save_calls" class="btn btn-success">{{ lang['save'] }}</button>&nbsp;&nbsp;
                            <button id="test_reset" class="btn btn-success" onclick="reset_subjects();">{{ lang['reset']}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <?php if($config->app_ticket_module == 'yes') { ?>
            <div class="row mb-2">
                <div class="col">
                    <div class="card border-primary">
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
            </div>
            <?php } ?>
            <div class="row">
                <div class="col">
                    <!-- //// Subjects Division //// zura-->
                    <div>
                        <?php
                            echo "<div>";
                            echo "<table>";
                            echo "<tr style='vertical-align:top;'>";
                                echo "<td>";
                                    echo "<div class='p-2' style='border:1px solid black; width:175px;min-height:300px;border-radius:5px;'>";
                                        echo "<div style='display:inline-block'>".lang('main_subjects')."</div>";
                                        echo "<hr>";
                                        $all_parent_subjects=$this->Call_subjects_model->get_visible_subjects();
                                            foreach($all_parent_subjects as $parent_subject){
                                                $subject_id=$parent_subject['id'];
                                                echo "<div class='w100'>";
                                                    //echo "<a href='#'>";
                                                        echo "<div class='btn btn-light mt-1 parent_subject div_inline'
                                                        id=".$subject_id.">".$parent_subject['title']."</div>";
                                                    //echo "</a>";
                                                echo "</div>";
                                            }
                                    echo "</div>";
                                echo "</td>";
                                echo "<td>";
                                /* Child 1 */
                                    echo "<div class='p-2' style='border:1px solid black; width:175px;min-height:300px;border-radius:5px;'>";
                                        echo "<div style='display:inline-block' id='subject_child_1'>&nbsp;</div>";
                                        echo "<hr>";
                                        echo "<input type='hidden' name='subject_child_1_parent_id' id='subject_child_1_parent_id'>";
                                        /* Child 1 div Dinamyc Content */
                                        echo "<div id='child_1_items_list'></div>";
                                    echo "</div>";
                            echo "</td>";
                            echo "<td>";
                            /* Child 2 */
                                echo "<div class='p-2' style='border:1px solid black; width:175px;min-height:300px;border-radius:5px;'>";
                                    echo "<div style='display:inline-block' id='subject_child_2'>&nbsp;</div>";
                                    echo "<hr>";
                                    echo "<input type='hidden' name='subject_child_2_parent_id' id='subject_child_2_parent_id'>";
                                    /* Child 2 div Dinamyc Content */
                                    echo "<div id='child_2_items_list'></div>";
                                echo "</div>";
                            echo "</td>";
                            echo "<td>";
                            /* Child 3 */
                                    echo "<div class='p-2' style='border:1px solid black; width:175px;min-height:300px;border-radius:5px;'>";
                                    echo "<div style='display:inline-block' id='subject_child_3'>&nbsp;</div>";
                                    echo "<hr>";
                                    echo "<input type='hidden' name='subject_child_3_parent_id' id='subject_child_3_parent_id'>";
                                    /* Child 3 div Dinamyc Content */
                                    echo "<div id='child_3_items_list'></div>";
                                echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                            echo "</table>";

                            //Include JS
                            $this->load->view('workspace/subject_js/child_1_subjects_js');
                            $this->load->view('workspace/subject_js/child_2_subjects_js');
                            $this->load->view('workspace/subject_js/child_3_subjects_js');

                            ?>
                        </div>
                        <!-- //// End Of Subjects Division zura //// -->
                    <!--Call List, Future function-->
                    <!--
                    <hr>
                        <div class="card border-primary">
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
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
<br><br>
    <!-- ////////////// DND Mode On/Off //////////////// -->
<div>
    <h5><?php echo lang('dnd_pause_mode'); ?></h5>
</div>
<div class="row">
    <div class="col-11">
        <div class="card border-primary p-2">
            <form action="api/Dnd/start_dnd" @submit.prevent="submit">
                <input type="hidden" id="dnd_record_id" :value="agent_dnd_status.id">
                <table>
                    <tr v-if="agent_dnd_status != 'empty'">
                        <td  v-if="agent_dnd_status.dnd_status == 'off'" style="width: 250px;">
                            <select  v-model="dnd_subjects_select" style="font-size:13px;">
                                <option value="">--- <?php echo lang('dnd_status'); ?>  ---</option>
                                <option v-for="dnd in dnd_subjects" :value="dnd.id">{{ dnd.title }}</option>
                            </select>
                        </td>
                        <td v-if="agent_dnd_status.dnd_status == 'off'" style="width: 250px;">
                            <button type="submit" class="btn btn-success" style="font-size:13px;width:150px;"><?php echo lang('dnd_start'); ?></button>
                        </td>
                        <td  v-if="agent_dnd_status.dnd_status == 'on'" style="width: 250px;font-size:13px;font-weight: bold">
                            <?php echo lang('dnd_status'); ?>: {{ agent_dnd_status.dnd_subject_title}}
                        </td>
                        <td v-if="agent_dnd_status.dnd_status == 'on'"  style="width: 250px;">
                            <button type="button" @click="end_dnd();" class="btn btn-danger" style="font-size:13px;width:150px;"><?php echo lang('dnd_end'); ?></button>
                        </td>
                        <td v-if="agent_dnd_status.dnd_status == 'on'">
                            <span style="font-size:13px;font-weight: bold;"><?php echo lang('dnd_duration'); ?></span>
                            <span class="text-danger"> <small>(hh:mm)</small> {{ agent_dnd_status.dnd_duration}}</span>
                        </td>
                    </tr>
                    <tr v-else>
                        <td style="width: 250px;">
                            <select  v-model="dnd_subjects_select" style="font-size:13px;">
                                <option value="">--- <?php echo lang('dnd_status'); ?>  ---</option>
                                <option v-for="dnd in dnd_subjects" :value="dnd.id">{{ dnd.title }}</option>
                            </select>
                        </td>
                        <td style="width: 250px;">
                            <button type="submit" class="btn btn-success" style="font-size:13px;width:150px;"><?php echo lang('dnd_start'); ?></button>
                        </td>
                        <td  v-if="agent_dnd_status.dnd_status == 'on'" style="width: 250px;font-size:13px;font-weight: bold">
                            <?php echo lang('dnd_status'); ?>: {{ agent_dnd_status.dnd_subject_title}}
                        </td>
                        <td v-if="agent_dnd_status.dnd_status == 'on'"  style="width: 250px;">
                            <button type="button" @click="end_dnd();" class="btn btn-danger" style="font-size:13px;width:150px;"><?php echo lang('dnd_end'); ?></button>
                        </td>
                        <td v-if="agent_dnd_status.dnd_status == 'on'">
                            <span style="font-size:13px;font-weight: bold;"><?php echo lang('dnd_duration'); ?></span>
                            <span class="text-danger"> <small>(hh:mm)</small> {{ agent_dnd_status.dnd_duration}}</span>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<br><br>
<script>
    $(document).ready(function(){
        $(document).on('click', '.parent_subject', function() {
            //Set Button Colors
            var parent_btn_id=this.id;

            $('.parent_subject').css("background-color","#ecf0f1");
            $('#'+parent_btn_id).css("background-color","#3498db");

        });

        $('#save_calls').click(function(){

            live_uniqueid = $('#live_unicueid').val();

            family_array_to_send='';

            for(var i=0; i<family_array.length; i++){
                family_array_to_send+=family_array[i]+'|';
            }

            var comment=$('#comment').val();
            console.log()

            $.ajax({
                url: "<?php echo base_url() ?>index.php/api/Recording/create_future_event",
                type: "POST",
                data: {
                    uniqueid: live_uniqueid,
                    subject_family: family_array_to_send,
                    comment: comment,
                },
                cache: false,
                success: function(dataResult){
                    alert('Data Was Added');
                    window.location.reload();
                }
            });
        });

    });
    var family_array=[];

     //Reset Function
     function reset_subjects(){
            $('.parent_subject').css("background-color","#ecf0f1");
            $('#comment').val('');

            $('#child_1_items_list').html('');
            $('#subject_child_1').html('');

            $('#child_2_items_list').html('');
            $('#subject_child_2').html('');

            $('#child_3_items_list').html('');
            $('#subject_child_3').html('');



            family_array=[];

         //window.location.reload();
            //agent_get_current_call
        }
    </script>
