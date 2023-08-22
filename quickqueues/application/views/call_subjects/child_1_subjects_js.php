<script>
$(document).ready(function() {
//  Display Child 1 Subjects Items
    $('.parent_subject').click(display_child_1_items);
    
    function display_child_1_items(){
        $('#child_3_items_list').html('');
        $('#subject_child_3').html('');

        $('#child_2_items_list').html('');
        $('#subject_child_2').html('');

        $('#add_child_2_button').attr('disabled', 'disabled');
        $('#add_child_3_button').attr('disabled', 'disabled');

        if(typeof(this.id) != "undefined" && this.id !== null) {
            parent_subject_id=this.id;
        }
        else{
            parent_subject_id=$('#subject_child_1_parent_id').val();
        }
        
        var parent_subject_title=$('#'+parent_subject_id).text();
        $('#subject_child_1, #child_1_modal_title').text(parent_subject_title);
        $('#add_child_1_button').removeAttr('disabled');
        $('#subject_child_1_parent_id').val(parent_subject_id);
        
        $.post('<?php echo base_url() ?>index.php/Call_subjects/get_child_1_subject_all',
            {
                type: 'display_child_1_subject',
				parent_id: parent_subject_id,
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    var child_1_arr=dataResult.child_1_result;
                    var child_1_items_list_html='';
                    for(var i=0; i<child_1_arr.length; i++){
                        var child_1_id=child_1_arr[i]['id'];
                        var child_1_title=child_1_arr[i]['title'];
                        var child_1_visible=child_1_arr[i]['visible'];
                        var child_1_comment=child_1_arr[i]['comment'];

                        //Child 1 Items List Dinamyc HTML
                        child_1_items_list_html+="<div class='w100' id='child_1_list_div'>";
                            child_1_items_list_html+="<a href='#'>";
                                child_1_items_list_html+="<div class='div_inline btn btn-light mt-1 child_1_subject'";
                                child_1_items_list_html+=" id="+child_1_id+">";
                                child_1_items_list_html+=child_1_title;
                                child_1_items_list_html+="</div>";
                            child_1_items_list_html+="</a>";
                            if(child_1_visible==1){
                                child_1_items_list_html+="<div class='div_inline div_right div_w_30 trash_child_1 visible1' id="+child_1_id+">";
                                child_1_items_list_html+="<a href='#'>Hide</a>";
                                child_1_items_list_html+="</div>";
                            }
                            else if(child_1_visible==0){
                                child_1_items_list_html+="<div class='div_inline div_right div_w_30 trash_child_1 visible0' id="+child_1_id+">";
                                child_1_items_list_html+="<a href='#' style='color:red'>Show</a>";
                                child_1_items_list_html+="</div>";
                            }
                            child_1_items_list_html+="<div class='div_inline div_right div_w_30 edit_child_1' id="+child_1_id; 
                            child_1_items_list_html+=" data-toggle='modal' data-target='#edit_child_1_modal'>";
                                child_1_items_list_html+="<a href='#'>Edit</a>";
                            child_1_items_list_html+="</div>";
                        child_1_items_list_html+="</div>";
                    }
                    $('#child_1_items_list').html(child_1_items_list_html);
                }
                else{
                    alert('Request Status Error!');
				}
        });
    }
//  Add Child 1 Subject
    $('#add_subject_child_1').on('click', function() {
        var title = $('#child_1_title').val();
        var comment = $('textarea#child_1_comment').val();
        var parent_id=$('#subject_child_1_parent_id').val();
        
        if(title.length<1){
            $('#child_1_title').css('borderColor','red');
            return false;
        }
        
        $('#add_subject').attr('disabled', 'disabled');
        

        $.post('<?php echo base_url() ?>index.php/Call_subjects/add_child_1_subject',
            {
                type: 'add_child_1_subject',
                parent_id: parent_id,
				title: title,
				comment: comment, 
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    close_modal('#add_child_1_modal', '.modal-backdrop', '#child_1_title', '#child_1_comment');
                    display_child_1_items();
                }
                else{
                    alert('Request Status Error!');
				}
        });
    });

    /* --- Edit  Child 1 --- */
    //Collect  Child 1 Data
    $(document).on('click', '.edit_child_1', function() {
        var id=this.id;
        $("#child_1_edit_id").val(id);
       
        $.ajax({
            url: "<?php echo base_url() ?>index.php/Call_subjects/get_child_1_details",
            type: "POST",
            data: {
                type: "get_child_1_details",
                id: id,
            },
            cache: false,
            success: function(dataResult){
                var dataResult = JSON.parse(dataResult);
                console.log(dataResult[0].title);
                var modal_title=dataResult[0].title;
                $('#edit_child_1_modal_title').text(modal_title);
                $("#edit_child_1_title").val(dataResult[0].title);
                $("#edit_child_1_comment").val(dataResult[0].comment);
            }
        });
    });

    //Update And Save Child 1 Data
    $('#edit_child_1_button').on('click', function() {
        var name = $('#edit_child_1_title').val();
        var comment = $('textarea#edit_child_1_comment').val();
        var id=$("#child_1_edit_id").val();
        
        if(name.length<1){
            $('#edit_child_1_title').css('borderColor','red');
            return false;
        }

        $.ajax({
            url: "<?php echo base_url() ?>index.php/Call_subjects/save_child_1_subject",
            type: "POST",
            data: {
                type: "save_child_1_subject",
                id: id,
                name: name,
                comment: comment,
            },
            cache: false,
            success: function(dataResult){
                close_modal('#add_child_1_modal', '.modal-backdrop', '#child_1_title', '#child_1_comment');
                display_child_1_items();
            }
        });
    });
 /* --- End Of Edit Child 1 Data--- */ 

 /* Hide & Show Child 1 */
    $(document).on('click', '.trash_child_1', function() {
        var id=this.id;
        if($(this).hasClass( "visible0")){
            var visible='1';
        }
        if($(this).hasClass( "visible1")){
            var visible='0';
        }
        
        $.ajax({
            url: '<?php echo base_url() ?>index.php/Call_subjects/hide_show_child_1_subject',
            type: "POST",
            data: {
                type: "hide_show_child_1",
                id: id,
                visible:visible,
            },
            cache: false,
            success: function(dataResult){
                display_child_1_items();
            }
        });
    });
/* End Of Hide Child 1 */
}); 
  
</script>
