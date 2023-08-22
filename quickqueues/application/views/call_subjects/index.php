<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
<!-- Updated At 19.02.2022-->
<?php
    echo '<h2>'.lang('manage_subjects').'</h2>';
    echo "<hr>";
    echo '<h5>'.lang('current_subjects').'</h5>';
    echo "<div class='card p-4'>";
    echo "<table  style='width:100%'>";
    echo "<tr style='vertical-align:top;'>";
        echo "<td>";
            echo "<div class='p-2' style='border:1px solid black; width:260px;min-height:300px;border-radius:5px;'>";
                echo "<div style='display:inline-block'>".lang('main_subjects')."</div>";
                echo "<div style='display:inline-block;float:right;'>";
                    echo "<button type='button' id='add_categories_modal_button' class='btn btn-primary mb-2' data-toggle='modal'
                    data-target='#add_subjects_modal'> + </button>";
                echo "</div>";
                echo "<hr>";

                $all_parent_subjects=$this->Call_subjects_model->get_main_subjects();
                    foreach($all_parent_subjects as $parent_subject){
                        $subject_id=$parent_subject['id'];
                        echo "<div class='w100'>";
                            echo "<a href='#'>";
                                echo "<div class='btn btn-light mt-1 parent_subject div_inline'
                                id=".$subject_id.">".$parent_subject['title']."</div>";
                            echo "</a>";

                            if($parent_subject['visible']=='1'){
                                echo "<div class='div_inline div_right div_w_30 trash_main_subject visible1' id=".$subject_id.">";
                                    echo "<a href='#' >Hide</a>";
                                echo "</div>";
                            }
                            elseif($parent_subject['visible']=='0'){
                                echo "<div class='div_inline div_right div_w_30 trash_main_subject visible0' id=".$subject_id.">";
                                    echo "<a href='#' style='color:red;'>Show</a>";
                                echo "</div>";
                            }
                            echo "<div class='div_inline div_right div_w_30 edit_parent_subject' id=".$subject_id." 
                                    data-toggle='modal' data-target='#edit_subjects_modal'>";
                                echo "<a href='#'>Edit</a>";
                            echo "</div>";
                        echo "</div>";
                    }
             echo "</div>";
        echo "</td>";
        echo "<td>";
        /* Child 1 */
            echo "<div class='p-2' style='border:1px solid black; width:260px;min-height:300px;border-radius:5px;'>";
                echo "<div style='display:inline-block' id='subject_child_1'>&nbsp;</div>";
                echo "<div style='display:inline-block;float:right;'>";
                    echo "<button type='button' id='add_child_1_button' class='btn btn-primary mb-2' data-toggle='modal'
                    data-target='#add_child_1_modal' disabled> + </button>";
                echo "</div>";
                echo "<hr>";
                echo "<input type='hidden' name='subject_child_1_parent_id' id='subject_child_1_parent_id'>";
                /* Child 1 div Dinamyc Content */
                echo "<div id='child_1_items_list'></div>";
            echo "</div>";
    echo "</td>";
    echo "<td>";
    /* Child 2 */
        echo "<div class='p-2' style='border:1px solid black; width:260px;min-height:300px;border-radius:5px;'>";
            echo "<div style='display:inline-block' id='subject_child_2'>&nbsp;</div>";
            echo "<div style='display:inline-block;float:right;'>";
                echo "<button type='button' id='add_child_2_button' class='btn btn-primary mb-2' data-toggle='modal'
                data-target='#add_child_2_modal' disabled> + </button>";
            echo "</div>";
            echo "<hr>";
            echo "<input type='hidden' name='subject_child_2_parent_id' id='subject_child_2_parent_id'>";
            /* Child 2 div Dinamyc Content */
            echo "<div id='child_2_items_list'></div>";
        echo "</div>";
    echo "</td>";
    echo "<td>";
    /* Child 3 */
            echo "<div class='p-2' style='border:1px solid black; width:260px;min-height:300px;border-radius:5px;'>";
            echo "<div style='display:inline-block' id='subject_child_3'>&nbsp;</div>";
            echo "<div style='display:inline-block;float:right;'>";
                echo "<button type='button' id='add_child_3_button' class='btn btn-primary mb-2' data-toggle='modal'
                data-target='#add_child_3_modal' disabled> + </button>";
            echo "</div>";
            echo "<hr>";
            echo "<input type='hidden' name='subject_child_3_parent_id' id='subject_child_3_parent_id'>";
            /* Child 3 div Dinamyc Content */
            echo "<div id='child_3_items_list'></div>";
        echo "</div>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";

//Include Device Modals And JS
$this->load->view('call_subjects/call_subjects_modals');
$this->load->view('call_subjects/parent_subjects_js');
$this->load->view('call_subjects/child_1_subjects_js');
$this->load->view('call_subjects/child_2_subjects_js');
$this->load->view('call_subjects/child_3_subjects_js');
$this->load->view('call_subjects/common_js')

?>
<script>
$(document).ready(function(){
    $(document).on('click', '.parent_subject', function() {
        //Set Button Colors
        /* var parent_btn_id=this.id;
        $('.parent_subject').css("background-color","#ecf0f1");
        $('#'+parent_btn_id).css("background-color","#3498db"); */
    
    });
});
</script>

