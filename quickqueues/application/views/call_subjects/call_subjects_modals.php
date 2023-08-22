<?php
/* ---- Modal Add Subject---*/
    echo "<div class='modal fade' id='add_subjects_modal' tabindex='-1' role='dialog' aria-labelledby='add_subjects_modal_Title' aria-hidden='true'>";
    echo "<div class='modal-dialog modal-dialog-centered' role='document'>";
            echo "<div class='modal-content'>";
                echo "<div class='modal-header'>";
                    echo "<h5 class='modal-title' id='add_subjects_modal_LongTitle'>".lang('add_new_subject')."</h5>";
                    echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                        echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button>";
                echo "</div>";
                echo "<div class='modal-body'>";
                    echo "<div>";
                        echo "<div><label for='title'>".lang('subject_title')."</label></div>";
                        echo "<div><input type='text'  class='form-control' name='title' id='title' placeholder='".lang('subject_title')."'></div>";
                    echo "</div>";
                    echo "<div class='mt-3'><label for='comment'>".lang('comment')."</label></div>";
                    echo "<div><textarea name='comment' id='comment'  class='form-control' placeholder='".lang('comment')."'></textarea></div>";
                echo "</div>";
                echo "<div class='modal-footer'>";
                    echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>".lang('close')."</button>";
                    echo "<button type='button' name='add_subject' id='add_subject' class='btn btn-success'>".lang('assign')."</button>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
/* --- End Of Modal Add Subject--- */

/* ---- Modal Edit Subject---*/
echo "<div class='modal fade' id='edit_subjects_modal' tabindex='-1' role='dialog' aria-labelledby='add_subjects_modal_Title' aria-hidden='true'>";
echo "<div class='modal-dialog modal-dialog-centered' role='document'>";
        echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
                echo "<h5 class='modal-title' id='edit_main_subjects_modal_title'></h5>";
                echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                echo "</button>";
            echo "</div>";
            echo "<div class='modal-body'>";
                echo "<input type='hidden' name='parent_edit_id' id='parent_edit_id'>";
                echo "<div>";
                    echo "<div><label for='edit_subject_title'>".lang('subject_title')."</label></div>";
                    echo "<div><input type='text'  class='form-control' name='edit_subject_title' id='edit_subject_title' placeholder='".lang('subject_title')."'></div>";
                echo "</div>";
                echo "<div class='mt-3'><label for='edit_subject_comment'>".lang('comment')."</label></div>";
                echo "<div><textarea name='edit_subject_comment' id='edit_subject_comment'  class='form-control' placeholder='".lang('comment')."'></textarea></div>";
            echo "</div>";
            echo "<div class='modal-footer'>";
                echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>".lang('close')."</button>";
                echo "<button type='button' name='edit_main_subject_button' id='edit_main_subject_button' class='btn btn-success'>".lang('save')."</button>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
echo "</div>";
/* --- End Of Modal Edit Subject--- */

/* ---- Modal Add Child 1 ---*/
    echo "<div class='modal fade' id='add_child_1_modal' tabindex='-1' role='dialog' aria-labelledby='add_subjects_modal_Title' aria-hidden='true'>";
    
    echo "<div class='modal-dialog modal-dialog-centered' role='document'>";
            echo "<div class='modal-content'>";
                echo "<div class='modal-header'>";
                    echo "<h5 class='modal-title' id='add_subjects_modal_LongTitle'>თემა: <div id='child_1_modal_title' style='display:inline'></div></h5>";
                    echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                        echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button>";
                echo "</div>";
                echo "<div class='modal-body'>";
                    echo "<div>";
                        echo "<div><label for='title'>".lang('subject_title')."</label></div>";
                        echo "<div><input type='text'  class='form-control' name='child_1_title' id='child_1_title' placeholder='".lang('subject_title')."'></div>";
                    echo "</div>";
                    echo "<div class='mt-3'><label for='comment'>".lang('comment')."</label></div>";
                    echo "<div><textarea name='child_1_comment' id='child_1_comment'  class='form-control' placeholder='".lang('comment')."'></textarea></div>";
                echo "</div>";
                echo "<div class='modal-footer'>";
                    echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>".lang('close')."</button>";
                    echo "<button type='button' name='add_subject_child_1' id='add_subject_child_1' class='btn btn-success' data-dismiss='modal'>".lang('assign')."</button>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
/* --- End Of Modal Add Child 1--- */

/* ---- Modal Edit Child 1 ---*/
echo "<div class='modal fade' id='edit_child_1_modal' tabindex='-1' role='dialog' aria-labelledby='add_subjects_modal_Title' aria-hidden='true'>";
echo "<div class='modal-dialog modal-dialog-centered' role='document'>";
        echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
                echo "<h5 class='modal-title' id='edit_child_1_modal_title'></h5>";
                echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                echo "</button>";
            echo "</div>";
            echo "<div class='modal-body'>";
                echo "<input type='hidden' name='child_1_edit_id' id='child_1_edit_id'>";
                echo "<div>";
                    echo "<div><label for='edit_subject_title'>".lang('subject_title')."</label></div>";
                    echo "<div><input type='text'  class='form-control' name='edit_child_1_title' id='edit_child_1_title' placeholder='".lang('subject_title')."'></div>";
                echo "</div>";
                echo "<div class='mt-3'><label for='edit_subject_comment'>".lang('comment')."</label></div>";
                echo "<div><textarea name='edit_child_1_comment' id='edit_child_1_comment'  class='form-control' placeholder='".lang('comment')."'></textarea></div>";
            echo "</div>";
            echo "<div class='modal-footer'>";
                echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>".lang('close')."</button>";
                echo "<button type='button' name='edit_child_1_button' data-dismiss='modal' id='edit_child_1_button' class='btn btn-success'>".lang('save')."</button>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
echo "</div>";
/* --- End Of Modal Edit Child 1--- */

/* ---- Modal Add Child 2 ---*/
    echo "<div class='modal fade' id='add_child_2_modal' tabindex='-1' role='dialog' aria-labelledby='add_subjects_modal_Title' aria-hidden='true'>";
    echo "<div class='modal-dialog modal-dialog-centered' role='document'>";
            echo "<div class='modal-content'>";
                echo "<div class='modal-header'>";
                    echo "<h5 class='modal-title' id='add_subjects_modal_LongTitle'>თემა: <div id='child_2_modal_title' style='display:inline'></div></h5>";
                    echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                        echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button>";
                echo "</div>";
                echo "<div class='modal-body'>";
                    echo "<div>";
                        echo "<div><label for='title'>".lang('subject_title')."</label></div>";
                        echo "<div><input type='text'  class='form-control' name='child_2_title' id='child_2_title' placeholder='".lang('subject_title')."'></div>";
                    echo "</div>";
                    echo "<div class='mt-3'><label for='comment'>".lang('comment')."</label></div>";
                    echo "<div><textarea name='child_2_comment' id='child_2_comment'  class='form-control' placeholder='".lang('comment')."'></textarea></div>";
                echo "</div>";
                echo "<div class='modal-footer'>";
                    echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>".lang('close')."</button>";
                    echo "<button type='button' name='add_subject_child_2' id='add_subject_child_2' class='btn btn-success'  data-dismiss='modal'>".lang('assign')."</button>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
/* --- End Of Modal Add SChild 2--- */

/* ---- Modal Edit Child 2 ---*/
echo "<div class='modal fade' id='edit_child_2_modal' tabindex='-1' role='dialog' aria-labelledby='add_subjects_modal_Title' aria-hidden='true'>";
echo "<div class='modal-dialog modal-dialog-centered' role='document'>";
        echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
                echo "<h5 class='modal-title' id='edit_child_2_modal_title'></h5>";
                echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                echo "</button>";
            echo "</div>";
            echo "<div class='modal-body'>";
                echo "<input type='hidden' name='child_2_edit_id' id='child_2_edit_id'>";
                echo "<div>";
                    echo "<div><label for='edit_subject_title'>".lang('subject_title')."</label></div>";
                    echo "<div><input type='text'  class='form-control' name='edit_child_2_title' id='edit_child_2_title' placeholder='".lang('subject_title')."'></div>";
                echo "</div>";
                echo "<div class='mt-3'><label for='edit_subject_comment'>".lang('comment')."</label></div>";
                echo "<div><textarea name='edit_child_2_comment' id='edit_child_2_comment'  class='form-control' placeholder='".lang('comment')."'></textarea></div>";
            echo "</div>";
            echo "<div class='modal-footer'>";
                echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>".lang('close')."</button>";
                echo "<button type='button' name='edit_child_2_button' data-dismiss='modal' id='edit_child_2_button' class='btn btn-success'>".lang('save')."</button>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
echo "</div>";
/* --- End Of Modal Edit Child 2--- */

/* ---- Modal Add Child 3 ---*/
echo "<div class='modal fade' id='add_child_3_modal' tabindex='-1' role='dialog' aria-labelledby='add_subjects_modal_Title' aria-hidden='true'>";
echo "<div class='modal-dialog modal-dialog-centered' role='document'>";
        echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
                echo "<h5 class='modal-title' id='add_subjects_modal_LongTitle'>თემა: <div id='child_3_modal_title' style='display:inline'></div></h5>";
                echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                echo "</button>";
            echo "</div>";
            echo "<div class='modal-body'>";
                echo "<div>";
                    echo "<div><label for='title'>".lang('subject_title')."</label></div>";
                    echo "<div><input type='text'  class='form-control' name='child_3_title' id='child_3_title' placeholder='".lang('subject_title')."'></div>";
                echo "</div>";
                echo "<div class='mt-3'><label for='comment'>".lang('comment')."</label></div>";
                echo "<div><textarea name='child_3_comment' id='child_3_comment'  class='form-control' placeholder='".lang('comment')."'></textarea></div>";
            echo "</div>";
            echo "<div class='modal-footer'>";
                echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>".lang('close')."</button>";
                echo "<button type='button' name='add_subject_child_3' id='add_subject_child_3' class='btn btn-success'  data-dismiss='modal'>".lang('assign')."</button>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
echo "</div>";
/* --- End Of Modal Add SChild 3--- */

/* ---- Modal Edit Child 3 ---*/
echo "<div class='modal fade' id='edit_child_3_modal' tabindex='-1' role='dialog' aria-labelledby='add_subjects_modal_Title' aria-hidden='true'>";
echo "<div class='modal-dialog modal-dialog-centered' role='document'>";
        echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
                echo "<h5 class='modal-title' id='edit_child_3_modal_title'></h5>";
                echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                echo "</button>";
            echo "</div>";
            echo "<div class='modal-body'>";
                echo "<input type='hidden' name='child_3_edit_id' id='child_3_edit_id'>";
                echo "<div>";
                    echo "<div><label for='edit_subject_title'>".lang('subject_title')."</label></div>";
                    echo "<div><input type='text'  class='form-control' name='edit_child_3_title' id='edit_child_3_title' placeholder='".lang('subject_title')."'></div>";
                echo "</div>";
                echo "<div class='mt-3'><label for='edit_subject_comment'>".lang('comment')."</label></div>";
                echo "<div><textarea name='edit_child_3_comment' id='edit_child_3_comment'  class='form-control' placeholder='".lang('comment')."'></textarea></div>";
            echo "</div>";
            echo "<div class='modal-footer'>";
                echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>".lang('close')."</button>";
                echo "<button type='button' name='edit_child_3_button' data-dismiss='modal' id='edit_child_3_button' class='btn btn-success'>".lang('save')."</button>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
echo "</div>";
/* --- End Of Modal Edit Child 3--- */