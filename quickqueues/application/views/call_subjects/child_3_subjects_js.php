<script>
$(document).ready(function() {
//  Display Child 3 Subjects Items
$('#child_2_items_list').on('click', '.child_2_subject', display_child_3_items);

    
    function display_child_3_items(){
        //Set Button Colors
        /* var child_2_btn_id=this.id;
        $('.child_2_subject').css("background-color","#ecf0f1");
        $('#'+child_2_btn_id+'.child_2_subject').css("background-color","#3498db"); */

        if(typeof(this.id) != "undefined" && this.id !== null) {
           
            parent_subject_id=this.id;
        }
        else{
            parent_subject_id=$('#subject_child_3_parent_id').val();
        }
        
        //var parent_subject_title=$('#'+parent_subject_id).text();
        var parent_subject_title=$('#'+parent_subject_id+'.child_2_subject').text();
        $('#subject_child_3, #child_3_modal_title').text(parent_subject_title);
        $('#add_child_3_button').removeAttr('disabled');
        $('#subject_child_3_parent_id').val(parent_subject_id);
        
        $.post('<?php echo base_url() ?>index.php/Call_subjects/get_child_3_subject',
            {
                type: 'display_child_3_subject',
				parent_id: parent_subject_id,
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    var child_3_arr=dataResult.child_3_result;
                    var child_3_items_list_html='';
                    for(var i=0; i<child_3_arr.length; i++){
                        var child_3_id=child_3_arr[i]['id'];
                        var child_3_title=child_3_arr[i]['title'];
                        var child_3_visible=child_3_arr[i]['visible'];

                        //Child 3 Items List Dinamyc HTML
                        child_3_items_list_html+="<div class='w100' id='child_3_list_div'>";
                            child_3_items_list_html+="<a href='#'>";
                                child_3_items_list_html+="<div class='div_inline btn btn-light mt-1 child_3_subject'";
                                child_3_items_list_html+="id="+child_3_id+">";
                                child_3_items_list_html+=child_3_title;
                                child_3_items_list_html+="</div>";
                            child_3_items_list_html+="</a>";
                            if(child_3_visible==1){
                                child_3_items_list_html+="<div class='div_inline div_right div_w_30 trash_child_3 visible1' id="+child_3_id+">";
                                child_3_items_list_html+="<a href='#'>Hide</a>";
                                child_3_items_list_html+="</div>";
                            }
                            else if(child_3_visible==0){
                                child_3_items_list_html+="<div class='div_inline div_right div_w_30 trash_child_3 visible0' id="+child_3_id+">";
                                child_3_items_list_html+="<a href='#' style='color:red'>Show</a>";
                                child_3_items_list_html+="</div>";
                            }
                            child_3_items_list_html+="<div class='div_inline div_right div_w_30 edit_child_3' id="+child_3_id; 
                            child_3_items_list_html+=" data-toggle='modal' data-target='#edit_child_3_modal'>";
                                child_3_items_list_html+="<a href='#'>Edit</a>";
                            child_3_items_list_html+="</div>";
                        child_3_items_list_html+="</div>";
                    }
                    $('#child_3_items_list').html(child_3_items_list_html);
                }
                else{
                    alert('Request Status Error!');
				}
        });
    }
//  Add Child 3 Subject
    $('#add_subject_child_3').on('click', function() {
        var title = $('#child_3_title').val();
        var comment = $('textarea#child_3_comment').val();
        var parent_id=$('#subject_child_3_parent_id').val();

        if(title.length<1){
            $('#child_3_title').css('borderColor','red');
            return false;
        }
        $('#add_subject').attr('disabled', 'disabled');
        
        $.post('<?php echo base_url() ?>index.php/Call_subjects/add_child_3_subject',
            {
                type: 'add_child_3_subject',
                parent_id: parent_id,
				title: title,
				comment: comment, 
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    close_modal('#add_child_3_modal', '.modal-backdrop', '#child_3_title', '#child_3_comment');
                    display_child_3_items();
                    //window.location.reload();
                }
                else{
                    alert('Request Status Error!');
				}
        });
    });

/* --- Edit  CHild 3 --- */
    //Collect  Child 2 Data
    $(document).on('click', '.edit_child_3', function() {
        var id=this.id;
        $("#child_3_edit_id").val(id);
       
        $.ajax({
            url: "<?php echo base_url() ?>index.php/Call_subjects/get_child_3_details",
            type: "POST",
            data: {
                type: "get_child_3_details",
                id: id,
            },
            cache: false,
            success: function(dataResult){
                var dataResult = JSON.parse(dataResult);
                console.log(dataResult[0].title);
                var modal_title=dataResult[0].title;
                $('#edit_child_3_modal_title').text(modal_title);
                $("#edit_child_3_title").val(dataResult[0].title);
                $("#edit_child_3_comment").val(dataResult[0].comment);
            }
        });
    });

    //Update And Save Child 3 Data
    $('#edit_child_3_button').on('click', function() {
        var name = $('#edit_child_3_title').val();
        var comment = $('textarea#edit_child_3_comment').val();
        var id=$("#child_3_edit_id").val();
        
        if(name.length<1){
            $('#edit_child_3_title').css('borderColor','red');
            return false;
        }

        $.ajax({
            url: "<?php echo base_url() ?>index.php/Call_subjects/save_child_3_subject",
            type: "POST",
            data: {
                type: "save_child_3_subject",
                id: id,
                name: name,
                comment: comment,
            },
            cache: false,
            success: function(dataResult){
                close_modal('#add_child_3_modal', '.modal-backdrop', '#child_3_title', '#child_3_comment');
                display_child_3_items();
            }
        });
    });
 /* --- End Of Edit Child 3 Data--- */ 
 
 /* Hide & Show Child 3 */
    $(document).on('click', '.trash_child_3', function() {
        var id=this.id;
        if($(this).hasClass( "visible0")){
            var visible='1';
        }
        if($(this).hasClass( "visible1")){
            var visible='0';
        }
        
        $.ajax({
            url: '<?php echo base_url() ?>index.php/Call_subjects/hide_show_child_3_subject',
            type: "POST",
            data: {
                type: "hide_show_child_3",
                id: id,
                visible:visible,
            },
            cache: false,
            success: function(dataResult){
                display_child_3_items();
            }
        });
    });
/* End Of Hide Child 3 */

    $(document).on('click', '.child_3_subject', function() {
        var this_id=this.id;

        //Set Button Colors
        /* var child_3_btn_id=this.id;
        $('.child_3_subject').css("background-color","#ecf0f1");
        $('#'+child_3_btn_id+'.child_3_subject').css("background-color","#3498db"); */

        if(!this_id){
            return false;
        }
    });
}); 
  
</script>