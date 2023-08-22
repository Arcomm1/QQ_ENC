<script>
$(document).ready(function() {
//  Display Child 2 Subjects Items
    $('#child_1_items_list').on('click', '.child_1_subject', display_child_2_items);
    
    function display_child_2_items(){
        $('#child_3_items_list').html('');
        $('#subject_child_3').html('');
        $('#add_child_3_button').attr('disabled', 'disabled');

        //Set Button Colors
        /* var child_1_btn_id=this.id;
        $('.child_1_subject').css("background-color","#ecf0f1");
        $('#'+child_1_btn_id+'.child_1_subject').css("background-color","#3498db"); */
        
        if(typeof(this.id) != "undefined" && this.id !== null) {
            parent_subject_id=this.id;
        }
        else{
            parent_subject_id=$('#subject_child_2_parent_id').val();
        }
        
        //var parent_subject_title=$('#'+parent_subject_id).text();
        var parent_subject_title=$('#'+parent_subject_id+'.child_1_subject').text();
        $('#subject_child_2, #child_2_modal_title').text(parent_subject_title);
        $('#add_child_2_button').removeAttr('disabled');
        $('#subject_child_2_parent_id').val(parent_subject_id);
        
        $.post('<?php echo base_url() ?>index.php/Call_subjects/get_child_2_subject',
            {
                type: 'display_child_2_subject',
				parent_id: parent_subject_id,
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    var child_2_arr=dataResult.child_2_result;
                    var child_2_items_list_html='';
                    for(var i=0; i<child_2_arr.length; i++){
                        var child_2_id=child_2_arr[i]['id'];
                        var child_2_title=child_2_arr[i]['title'];
                        var child_2_visible=child_2_arr[i]['visible'];

                        //Child 2 Items List Dinamyc HTML
                         child_2_items_list_html+="<div class='w100' id='child_2_list_div'>";
                            child_2_items_list_html+="<a href='#'>";
                                child_2_items_list_html+="<div class='div_inline btn btn-light mt-1 child_2_subject'";
                                child_2_items_list_html+="id="+child_2_id+">";
                                child_2_items_list_html+=child_2_title;
                                child_2_items_list_html+="</div>";
                            child_2_items_list_html+="</a>";
                            if(child_2_visible==1){
                                child_2_items_list_html+="<div class='div_inline div_right div_w_30 trash_child_2 visible1' id="+child_2_id+">";
                                child_2_items_list_html+="<a href='#'>Hide</a>";
                                child_2_items_list_html+="</div>";
                            }
                            else if(child_2_visible==0){
                                child_2_items_list_html+="<div class='div_inline div_right div_w_30 trash_child_2 visible0' id="+child_2_id+">";
                                child_2_items_list_html+="<a href='#' style='color:red'>Show</a>";
                                child_2_items_list_html+="</div>";
                            }
                            child_2_items_list_html+="<div class='div_inline div_right div_w_30 edit_child_2' id="+child_2_id; 
                            child_2_items_list_html+=" data-toggle='modal' data-target='#edit_child_2_modal'>";
                                child_2_items_list_html+="<a href='#'>Edit</a>";
                            child_2_items_list_html+="</div>";
                        child_2_items_list_html+="</div>";
                    }
                    $('#child_2_items_list').html(child_2_items_list_html);
                }
                else{
                    alert('Request Status Error!');
				}
        });
    }
//  Add Child 2 Subject
    $('#add_subject_child_2').on('click', function() {
        var title = $('#child_2_title').val();
        var comment = $('textarea#child_2_comment').val();
        var parent_id=$('#subject_child_2_parent_id').val();
        
        if(title.length<1){
            $('#child_2_title').css('borderColor','red');
            return false;
        }
        $('#add_subject').attr('disabled', 'disabled');
        
        $.post('<?php echo base_url() ?>index.php/Call_subjects/add_child_2_subject',
            {
                type: 'add_child_2_subject',
                parent_id: parent_id,
				title: title,
				comment: comment, 
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    close_modal('#add_child_2_modal', '.modal-backdrop', '#child_2_title', '#child_2_comment');
                    display_child_2_items();
                    //window.location.reload();
                }
                else{
                    alert('Request Status Error!');
				}
        });
    });

/* --- Edit  CHild 2 --- */
    //Collect  Child 2 Data
    $(document).on('click', '.edit_child_2', function() {
        var id=this.id;
        $("#child_2_edit_id").val(id);
       
        $.ajax({
            url: "<?php echo base_url() ?>index.php/Call_subjects/get_child_2_details",
            type: "POST",
            data: {
                type: "get_child_2_details",
                id: id,
            },
            cache: false,
            success: function(dataResult){
                var dataResult = JSON.parse(dataResult);
                console.log(dataResult[0].title);
                var modal_title=dataResult[0].title;
                $('#edit_child_2_modal_title').text(modal_title);
                $("#edit_child_2_title").val(dataResult[0].title);
                $("#edit_child_2_comment").val(dataResult[0].comment);
            }
        });
    });

    //Update And Save Child 2 Data
    $('#edit_child_2_button').on('click', function() {
        var name = $('#edit_child_2_title').val();
        var comment = $('textarea#edit_child_2_comment').val();
        var id=$("#child_2_edit_id").val();
        
        if(name.length<1){
            $('#edit_child_2_title').css('borderColor','red');
            return false;
        }

        $.ajax({
            url: "<?php echo base_url() ?>index.php/Call_subjects/save_child_2_subject",
            type: "POST",
            data: {
                type: "save_child_2_subject",
                id: id,
                name: name,
                comment: comment,
            },
            cache: false,
            success: function(dataResult){
                close_modal('#add_child_2_modal', '.modal-backdrop', '#child_2_title', '#child_2_comment');
                display_child_2_items();
            }
        });
    });
 /* --- End Of Edit Child 2 Data--- */ 
 
 /* Hide & Show Child 2 */
 $(document).on('click', '.trash_child_2', function() {
            var id=this.id;
            if($(this).hasClass( "visible0")){
                var visible='1';
            }
            if($(this).hasClass( "visible1")){
                var visible='0';
            }
          
            $.ajax({
                url: '<?php echo base_url() ?>index.php/Call_subjects/hide_show_child_2_subject',
                type: "POST",
                data: {
                    type: "hide_show_child_2",
                    id: id,
                    visible:visible,
                },
                cache: false,
                success: function(dataResult){
                    display_child_2_items();
                }
            });
        });
/* End Of Hide Child 2 */
}); 
  
</script>